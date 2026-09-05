@extends('backEnd.master')

@section('title', 'Complete Payment')

@section('mainContent')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card mt-5">
                <div class="card-body text-center p-5">
                    <div id="stripe-element-loading">
                        <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                        <h4>Processing your payment...</h4>
                        <p class="text-muted">Please wait while we confirm your payment.</p>
                    </div>
                    <div id="stripe-element-result" style="display: none;">
                        <div id="stripe-element-success" style="display: none;">
                            <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                            <h4>Payment Successful!</h4>
                            <p class="text-muted">Your subscription is now active.</p>
                            <a href="{{ url('/') }}" class="btn btn-primary mt-3">Go to Dashboard</a>
                        </div>
                        <div id="stripe-element-error" style="display: none;">
                            <i class="fas fa-times-circle text-danger fa-3x mb-3"></i>
                            <h4>Payment Failed</h4>
                            <p class="text-muted" id="errorMessage">Something went wrong.</p>
                            <a href="{{ route('subscription.checkout', ['plan' => $plan->id]) }}" class="btn btn-primary mt-3">Try Again</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stripe = Stripe('{{ $stripeKey }}');
        const clientSecret = '{{ $clientSecret }}';

        stripe.handleCardAction(clientSecret).then(function(result) {
            if (result.error) {
                document.getElementById('stripe-element-loading').style.display = 'none';
                document.getElementById('stripe-element-result').style.display = 'block';
                document.getElementById('stripe-element-error').style.display = 'block';
                document.getElementById('errorMessage').textContent = result.error.message;
            } else {
                fetch('/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: 'payment_intent_id=' + result.paymentIntent.id + '&payment_method=stripe&payment_type=buy_now&plan_id={{ $plan->id }}&stripe_token=none&cardholder_name=&cardholder_email='
                }).then(function(response) {
                    document.getElementById('stripe-element-loading').style.display = 'none';
                    document.getElementById('stripe-element-result').style.display = 'block';
                    document.getElementById('stripe-element-success').style.display = 'block';
                }).catch(function(error) {
                    document.getElementById('stripe-element-loading').style.display = 'none';
                    document.getElementById('stripe-element-result').style.display = 'block';
                    document.getElementById('stripe-element-error').style.display = 'block';
                    document.getElementById('errorMessage').textContent = 'Payment confirmation failed.';
                });
            }
        });
    });
</script>
@endsection
