

<?php $__env->startSection('content'); ?>
    <div class="card-header">
        <h2><?php echo e(config('spondonit.branding.welcome_title', __('service::install.welcome_title'))); ?></h2>
    </div>

    <div class="card-body text-center">
        <div class="hero-icon success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
            </svg>
        </div>

        <p class="text-secondary">
            <?php echo config('spondonit.branding.welcome_description', __('service::install.welcome_description')); ?>

        </p>

        <div class="form-actions form-actions-center mt-3">
            <a href="<?php echo e(route('service.preRequisite')); ?>" class="btn btn-primary">
                <?php echo e(__('service::install.get_started')); ?>

                <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('service::layouts.app_install', ['title' => config('spondonit.branding.welcome_title', __('service::install.welcome'))], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dell/erp/resources/views/vendors/service/install/welcome.blade.php ENDPATH**/ ?>