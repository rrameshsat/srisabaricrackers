<?php $__env->startSection('hometitle'); ?>
    <?php echo e($setting->home_page_title); ?>

<?php $__env->stopSection(); ?>
<?php if($setting->theme == 'theme1'): ?>
    <?php if ($__env->exists('front.themes.theme1')) echo $__env->make('front.themes.theme1', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php elseif($setting->theme == 'theme2'): ?>
    <?php if ($__env->exists('front.themes.theme2')) echo $__env->make('front.themes.theme2', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php elseif($setting->theme == 'theme3'): ?>
    <?php if ($__env->exists('front.themes.theme3')) echo $__env->make('front.themes.theme3', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php elseif($setting->theme == 'theme4'): ?>
    <?php if ($__env->exists('front.themes.theme4')) echo $__env->make('front.themes.theme4', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php endif; ?><?php /**PATH I:\xampp8212\htdocs\sscrackers\core\resources\views/front/index.blade.php ENDPATH**/ ?>