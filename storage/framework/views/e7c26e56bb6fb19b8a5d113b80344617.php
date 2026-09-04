<?php $__env->startSection('title', 'Checkout'); ?>

<?php $__env->startSection('mainContent'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="mb-0">Checkout - <?php echo e($plan->name); ?> Plan</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Order Summary</h5>
                                    <hr>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Plan:</span>
                                        <strong><?php echo e($plan->name); ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Price:</span>
                                        <strong>$<?php echo e(number_format($plan->price, 2)); ?>/month</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Teachers:</span>
                                        <strong><?php echo e($plan->staff_quantity ?? '-'); ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Students:</span>
                                        <strong><?php echo e($plan->student_quantity ?? '-'); ?></strong>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->trial_days > 0): ?>
                                    <div class="mt-3 p-2 bg-success bg-opacity-10 rounded">
                                        <small class="text-success"><i class="fas fa-gift"></i> <?php echo e($plan->trial_days); ?>-day free trial included</small>
                                    </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <form action="<?php echo e(route('subscription.process')); ?>" method="POST" id="checkoutForm">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="plan_id" value="<?php echo e($plan->id); ?>">

                                <div class="mb-3">
                                    <label class="form-label">Payment Type</label>
                                    <select name="payment_type" class="form-select" id="paymentType">
                                        <option value="buy_now">Buy Now</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->trial_days > 0): ?>
                                        <option value="trial">Start Free Trial (<?php echo e($plan->trial_days); ?> days)</option>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                </div>

                                <div id="paymentMethodSection">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Method</label>
                                        <select name="payment_method" class="form-select" id="paymentMethod">
                                            <option value="stripe">Credit/Debit Card (Stripe)</option>
                                        </select>
                                    </div>

                                    <div id="stripeFields">
                                        <div class="mb-3">
                                            <label class="form-label">Cardholder Name</label>
                                            <input type="text" name="cardholder_name" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Cardholder Email</label>
                                            <input type="email" name="cardholder_email" class="form-control" value="<?php echo e(Auth::user()->email ?? ''); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Card Details</label>
                                            <div id="card-element" class="form-control"></div>
                                            <div id="card-errors" class="text-danger small mt-1" role="alert"></div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 mt-3" id="submitBtn">
                                    <span id="buttonText">Pay $<?php echo e(number_format($plan->price, 2)); ?></span>
                                    <span id="buttonSpinner" class="d-none">
                                        <i class="fas fa-spinner fa-spin"></i> Processing...
                                    </span>
                                </button>
                            </form>
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
        const paymentType = document.getElementById('paymentType');
        const paymentMethodSection = document.getElementById('paymentMethodSection');
        const stripeFields = document.getElementById('stripeFields');
        const form = document.getElementById('checkoutForm');
        const submitBtn = document.getElementById('submitBtn');

        paymentType.addEventListener('change', function() {
            if (this.value === 'trial') {
                paymentMethodSection.style.display = 'none';
            } else {
                paymentMethodSection.style.display = 'block';
            }
        });

        if (paymentType.value === 'trial') {
            paymentMethodSection.style.display = 'none';
        }

        const stripeKey = '<?php echo e($stripeKey); ?>';
        if (stripeKey) {
            const stripe = Stripe(stripeKey);
            const elements = stripe.elements();
            const cardElement = elements.create('card');
            cardElement.mount('#card-element');

            cardElement.on('change', function(event) {
                const displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            form.addEventListener('submit', function(e) {
                if (paymentType.value === 'trial') {
                    return true;
                }

                e.preventDefault();
                submitBtn.disabled = true;
                document.getElementById('buttonText').classList.add('d-none');
                document.getElementById('buttonSpinner').classList.remove('d-none');

                stripe.createToken(cardElement).then(function(result) {
                    if (result.error) {
                        document.getElementById('card-errors').textContent = result.error.message;
                        submitBtn.disabled = false;
                        document.getElementById('buttonText').classList.remove('d-none');
                        document.getElementById('buttonSpinner').classList.add('d-none');
                    } else {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.setAttribute('type', 'hidden');
                        hiddenInput.setAttribute('name', 'stripe_token');
                        hiddenInput.setAttribute('value', result.token.id);
                        form.appendChild(hiddenInput);
                        form.submit();
                    }
                });
            });
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dell/erp/Modules/Saas/Resources/views/subscription/checkout.blade.php ENDPATH**/ ?>