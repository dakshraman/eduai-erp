<?php

namespace App\Http\Controllers\Admin\Subscription;

use App\Http\Controllers\Controller;
use App\Models\SchoolSubscription;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Subscription;
use Stripe\Webhook;

class SubscriptionController extends Controller
{
    public function plans()
    {
        $plans = SubscriptionPlan::active()->ordered()->get();

        return view('backEnd.subscription.plans', compact('plans'));
    }

    public function dashboard()
    {
        $schoolId = Auth::user()->school_id ?? Auth::id();
        $subscription = SchoolSubscription::with('plan')
            ->where('school_id', $schoolId)
            ->latest()
            ->first();

        return view('backEnd.subscription.dashboard', compact('subscription'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        $schoolId = Auth::user()->school_id ?? Auth::id();

        Stripe::setApiKey(config('stripe.secret_key'));

        $session = StripeCheckoutSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $plan->stripe_price_id,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('subscription.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('subscription.plans'),
            'metadata' => [
                'school_id' => $schoolId,
                'plan_id' => $plan->id,
            ],
        ]);

        SchoolSubscription::updateOrCreate(
            [
                'school_id' => $schoolId,
                'status' => 'pending',
            ],
            [
                'subscription_plan_id' => $plan->id,
                'stripe_subscription_id' => $session->subscription,
            ]
        );

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $schoolId = Auth::user()->school_id ?? Auth::id();

        $subscription = SchoolSubscription::where('school_id', $schoolId)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($subscription) {
            $subscription->update(['status' => 'active']);
        }

        return redirect()->route('subscription.dashboard')
            ->with('success', 'Subscription activated successfully!');
    }

    public function cancel(Request $request)
    {
        $schoolId = Auth::user()->school_id ?? Auth::id();

        Stripe::setApiKey(config('stripe.secret_key'));

        $subscription = SchoolSubscription::where('school_id', $schoolId)
            ->active()
            ->latest()
            ->first();

        if (! $subscription) {
            return back()->with('error', 'No active subscription found.');
        }

        if ($subscription->stripe_subscription_id) {
            try {
                Subscription::update($subscription->stripe_subscription_id, [
                    'cancel_at_period_end' => true,
                ]);
            } catch (\Exception $e) {
                Log::error('Stripe cancel error: '.$e->getMessage());

                return back()->with('error', 'Failed to cancel subscription. Please try again.');
            }
        }

        $subscription->update(['canceled_at' => now()]);

        return redirect()->route('subscription.dashboard')
            ->with('success', 'Subscription will be canceled at the end of the billing period.');
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('stripe.webhook_secret')
            );
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($event->data->object);
                break;
            case 'invoice.paid':
                $this->handleInvoicePaid($event->data->object);
                break;
            case 'invoice.payment_failed':
                $this->handleInvoicePaymentFailed($event->data->object);
                break;
            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;
        }

        return response('Webhook handled', 200);
    }

    private function handleCheckoutCompleted(object $session): void
    {
        $schoolId = $session->metadata->school_id ?? null;

        if (! $schoolId) {
            return;
        }

        $subscription = SchoolSubscription::where('school_id', $schoolId)
            ->where('stripe_subscription_id', $session->subscription)
            ->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'active',
                'stripe_customer_id' => $session->customer,
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);
        }
    }

    private function handleInvoicePaid(object $invoice): void
    {
        $subscriptionId = $invoice->subscription;

        if (! $subscriptionId) {
            return;
        }

        $subscription = SchoolSubscription::where('stripe_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'active',
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);
        }
    }

    private function handleInvoicePaymentFailed(object $invoice): void
    {
        $subscriptionId = $invoice->subscription;

        if (! $subscriptionId) {
            return;
        }

        $subscription = SchoolSubscription::where('stripe_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            $subscription->update(['status' => 'past_due']);
        }
    }

    private function handleSubscriptionDeleted(object $stripeSubscription): void
    {
        $subscription = SchoolSubscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);
        }
    }

    private function handleSubscriptionUpdated(object $stripeSubscription): void
    {
        $subscription = SchoolSubscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if (! $subscription) {
            return;
        }

        $statusMap = [
            'active' => 'active',
            'past_due' => 'past_due',
            'canceled' => 'canceled',
            'unpaid' => 'unpaid',
            'trialing' => 'trial',
        ];

        $newStatus = $statusMap[$stripeSubscription->status] ?? $stripeSubscription->status;

        $subscription->update([
            'status' => $newStatus,
            'current_period_start' => $stripeSubscription->current_period_start
                ? Carbon::createFromTimestamp($stripeSubscription->current_period_start)
                : $subscription->current_period_start,
            'current_period_end' => $stripeSubscription->current_period_end
                ? Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                : $subscription->current_period_end,
        ]);
    }
}
