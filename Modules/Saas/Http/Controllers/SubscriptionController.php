<?php

namespace Modules\Saas\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Saas\Entities\SaasSettings;
use Modules\Saas\Entities\SmPackagePlan;
use Modules\Saas\Entities\SmSubscriptionPayment;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\Webhook;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['mySubscription', 'cancelSubscription']);
    }

    /**
     * Show available plans (public route).
     */
    public function packageList()
    {
        try {
            $plans = SmPackagePlan::where('active_status', true)->get();

            return view('saas::subscription.packageList', compact('plans'));
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    /**
     * Show checkout page with plan details.
     */
    public function checkout(string $planId)
    {
        try {
            $plan = SmPackagePlan::findOrFail($planId);

            if (! $plan->active_status) {
                Toastr::error('This plan is no longer available.', 'Failed');

                return redirect()->route('subscription.package-list');
            }

            $stripeKey = $this->getStripePublishableKey();

            return view('saas::subscription.checkout', compact('plan', 'stripeKey'));
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    /**
     * Process payment via Stripe.
     */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:sm_package_plans,id',
            'payment_type' => 'required|in:trial,buy_now,instantly',
            'payment_method' => 'required|in:stripe,paypal,manual',
            'stripe_token' => 'required_if:payment_method,stripe',
            'cardholder_name' => 'required_if:payment_method,stripe',
            'cardholder_email' => 'required_if:payment_method,stripe',
        ]);

        try {
            $plan = SmPackagePlan::findOrFail($request->plan_id);
            $school = Auth::user()->school;

            if (! $school) {
                Toastr::error('No school associated with your account.', 'Failed');

                return redirect()->back();
            }

            $startDate = now()->toDateString();
            $endDate = now()->addDays($plan->duration_days)->toDateString();

            $payment = SmSubscriptionPayment::create([
                'school_id' => $school->id,
                'package_id' => $plan->id,
                'payment_type' => $request->payment_type,
                'payment_method' => $request->payment_method,
                'amount' => $request->payment_type === 'trial' ? 0 : $plan->price,
                'approve_status' => 'pending',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'buy_type' => $request->payment_type,
            ]);

            if ($request->payment_type === 'trial') {
                $payment->update(['approve_status' => 'approved']);
                $this->updateSchoolSubscription($school, $plan, $payment);

                Toastr::success('Trial activated successfully!', 'Success');

                return redirect()->route('subscription.thank-you');
            }

            if ($request->payment_method === 'stripe') {
                return $this->processStripePayment($request, $plan, $school, $payment);
            }

            Toastr::error('Payment method not yet supported.', 'Failed');

            return redirect()->back();
        } catch (\Exception $exception) {
            Log::error('Checkout error: '.$exception->getMessage());
            Toastr::error('Payment processing failed. Please try again.', 'Failed');

            return redirect()->back();
        }
    }

    /**
     * Handle Stripe webhook.
     */
    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret', '')
            );
        } catch (\Exception $e) {
            return response('Invalid signature', 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($event->data->object);
                break;
            case 'invoice.payment_succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;
            case 'invoice.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;
            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;
        }

        return response('Webhook handled', 200);
    }

    /**
     * Thank you page after payment.
     */
    public function thankYou()
    {
        return view('saas::subscription.thankYou');
    }

    /**
     * Show current subscription (authenticated).
     */
    public function mySubscription()
    {
        try {
            $school = Auth::user()->school;
            $subscription = SmSubscriptionPayment::with('package')
                ->where('school_id', $school->id)
                ->orderBy('id', 'desc')
                ->first();

            $activePlan = null;
            if ($subscription && $subscription->package) {
                $activePlan = $subscription->package;
            }

            return view('saas::subscription.mySubscription', compact('subscription', 'activePlan'));
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    /**
     * Cancel subscription.
     */
    public function cancelSubscription(Request $request)
    {
        try {
            $school = Auth::user()->school;
            $subscription = SmSubscriptionPayment::where('school_id', $school->id)
                ->where('approve_status', 'approved')
                ->first();

            if (! $subscription) {
                Toastr::error('No active subscription found.', 'Failed');

                return redirect()->back();
            }

            $subscription->update(['approve_status' => 'cancelled']);

            $school->update([
                'package_id' => null,
                'plan_type' => null,
                'starting_date' => null,
                'ending_date' => null,
            ]);

            Toastr::success('Subscription cancelled successfully.', 'Success');

            return redirect()->route('subscription.my');
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    /**
     * Renew expired subscription.
     */
    public function renewSubscription(Request $request)
    {
        try {
            $school = Auth::user()->school;
            $lastPayment = SmSubscriptionPayment::where('school_id', $school->id)
                ->orderBy('id', 'desc')
                ->first();

            if (! $lastPayment || ! $lastPayment->package) {
                Toastr::error('No previous subscription found.', 'Failed');

                return redirect()->route('subscription.package-list');
            }

            return redirect()->route('subscription.checkout', ['plan' => $lastPayment->package_id]);
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    /**
     * Process Stripe payment.
     */
    private function processStripePayment(Request $request, SmPackagePlan $plan, $school, SmSubscriptionPayment $payment)
    {
        Stripe::setApiKey($this->getStripeSecretKey());

        try {
            $customer = Customer::create([
                'email' => Auth::user()->email,
                'name' => Auth::user()->name,
                'metadata' => [
                    'school_id' => $school->id,
                    'user_id' => Auth::id(),
                ],
            ]);

            $stripeSubscription = Subscription::create([
                'customer' => $customer->id,
                'items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'unit_amount' => (int) ($plan->price * 100),
                            'recurring' => [
                                'interval' => 'month',
                            ],
                            'product_data' => [
                                'name' => $plan->name.' Plan',
                            ],
                        ],
                    ],
                ],
                'payment_behavior' => 'default_incomplete',
                'expand' => ['latest_invoice.payment_intent'],
                'metadata' => [
                    'payment_id' => $payment->id,
                    'school_id' => $school->id,
                    'plan_id' => $plan->id,
                ],
            ]);

            $clientSecret = $stripeSubscription->latest_invoice->payment_intent->client_secret;

            return view('saas::subscription.stripeConfirm', compact('payment', 'clientSecret', 'plan', 'stripeKey'));
        } catch (ApiErrorException $e) {
            Log::error('Stripe error: '.$e->getMessage());
            $payment->update(['approve_status' => 'failed']);

            Toastr::error('Payment processing failed: '.$e->getMessage(), 'Failed');

            return redirect()->back();
        }
    }

    /**
     * Update school subscription details.
     */
    private function updateSchoolSubscription($school, SmPackagePlan $plan, SmSubscriptionPayment $payment): void
    {
        $school->update([
            'package_id' => $plan->id,
            'plan_type' => $payment->payment_type,
            'starting_date' => $payment->start_date,
            'ending_date' => $payment->end_date,
        ]);
    }

    /**
     * Get Stripe publishable key.
     */
    private function getStripePublishableKey(): string
    {
        $setting = \App\Models\SmPaymentGatewaySetting::where('gateway_name', 'Stripe')
            ->where('school_id', Auth::user()->school_id ?? 1)
            ->first();

        return $setting->gateway_publishable_key ?? config('services.stripe.key', '');
    }

    /**
     * Get Stripe secret key.
     */
    private function getStripeSecretKey(): string
    {
        $setting = \App\Models\SmPaymentGatewaySetting::where('gateway_name', 'Stripe')
            ->where('school_id', Auth::user()->school_id ?? 1)
            ->first();

        return $setting->gateway_secret_key ?? config('services.stripe.secret', '');
    }

    /**
     * Handle successful checkout session.
     */
    private function handleCheckoutCompleted($session): void
    {
        $paymentId = $session->metadata->payment_id ?? null;
        if (! $paymentId) {
            return;
        }

        $payment = SmSubscriptionPayment::find($paymentId);
        if (! $payment) {
            return;
        }

        $payment->update(['approve_status' => 'approved']);
        $this->updateSchoolSubscription($payment->school, $payment->package, $payment);
    }

    /**
     * Handle successful payment.
     */
    private function handlePaymentSucceeded($invoice): void
    {
        Log::info('Stripe payment succeeded', ['invoice_id' => $invoice->id]);
    }

    /**
     * Handle failed payment.
     */
    private function handlePaymentFailed($invoice): void
    {
        Log::warning('Stripe payment failed', ['invoice_id' => $invoice->id]);
    }

    /**
     * Handle subscription deletion.
     */
    private function handleSubscriptionDeleted($subscription): void
    {
        Log::info('Stripe subscription deleted', ['subscription_id' => $subscription->id]);
    }
}
