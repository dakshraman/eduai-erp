<!DOCTYPE html>
<html lang="en">

<head>
    <?php
        $basePath = config('spondonit.branding.asset_path', 'vendor/spondonit');
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo e(isset($title) ? $title . ' | ' . config('app.name') : config('app.name')); ?></title>
    <link rel="shortcut icon" href="<?php echo e(asset($basePath . '/img/favicon.png')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset($basePath . '/css/installer.css')); ?>">
    <?php echo $__env->yieldPushContent('css'); ?>
</head>

<body>
    
    <div class="preloader" id="preloader">
        <div class="spinner"></div>
        <p class="preloader-text"><?php echo e(__('service::install.installation_processing')); ?></p>
    </div>

    <div class="installer-wrapper">
        <div class="installer-container">
            
            <?php
                $steps = [
                    ['route' => 'service.install', 'label' => __('service::install.welcome')],
                    ['route' => 'service.preRequisite', 'label' => __('service::install.environment')],
                    ['route' => 'service.license', 'label' => __('service::install.license')],
                    ['route' => 'service.database', 'label' => __('service::install.database')],
                    ['route' => 'service.user', 'label' => __('service::install.admin_setup')],
                    ['route' => 'service.done', 'label' => __('service::install.done')],
                ];

                $currentRoute = request()->route()?->getName();
                $routeNames = array_column($steps, 'route');
                $currentIndex = array_search($currentRoute, $routeNames);
            ?>

            <ol class="stepper">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <li class="<?php echo e($index < $currentIndex ? 'completed' : ''); ?><?php echo e($index === $currentIndex ? 'active' : ''); ?>">
                        <?php echo e($step['label']); ?>

                    </li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ol>

            <div class="card">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('message')): ?>
        <div id="session-flash"
             data-message="<?php echo e(e(session('message'))); ?>"
             data-type="<?php echo e(e(session('status', 'error'))); ?>"
             class="hidden"></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <script src="<?php echo e(asset($basePath . '/js/installer.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('js'); ?>

</body>

</html>
<?php /**PATH /home/dell/erp/resources/views/vendors/service/layouts/app_install.blade.php ENDPATH**/ ?>