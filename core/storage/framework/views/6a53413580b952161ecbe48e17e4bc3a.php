
    <?php
        $categories = App\Models\Category::with('subcategory')->whereStatus(1)->orderby('serial','asc')->take(8)->get();
    ?>


    <div class="left-category-area">
        <div class="category-header">
            <h4><i class="icon-align-justify"></i> <?php echo e(__('Categories')); ?></h4>
        </div>
        <div class="category-list">
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $pcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="c-item">
                    <a class="d-block navi-link" href="<?php echo e(route('front.catalog').'?category='.$pcategory->slug); ?>">
                        <img class="lazy" data-src="<?php echo e(url('/core/public/storage/images/'.$pcategory->photo)); ?>">
                        <span class="text-gray-dark"><?php echo e($pcategory->name); ?></span>
                        <?php if($pcategory->subcategory->count() > 0): ?>
                        <i class="icon-chevron-right"></i>
                        <?php endif; ?>
                    </a>
                    <?php if($pcategory->subcategory->count() > 0): ?>
                    <div class="sub-c-box">
                            <?php $__currentLoopData = $pcategory->subcategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="child-c-box">
                              <a class="title" href="<?php echo e(route('front.catalog').'?subcategory='.$scategory->slug); ?>">
                                <?php echo e($scategory->name); ?>

                                <?php if($scategory->childcategory->count() > 0): ?>
                                <i class="icon-chevron-right"></i>
                                <?php endif; ?>
                                </a>
                                <?php if($scategory->childcategory->count() > 0): ?>
                              <div class="child-category">

                                <?php $__currentLoopData = $scategory->childcategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $childcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('front.catalog').'?childcategory='.$childcategory->slug); ?>"><?php echo e($childcategory->name); ?></a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              </div>
                              <?php endif; ?>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('front.catalog')); ?>" class="d-block navi-link view-all-category">
            <img class="lazy" data-src="<?php echo e(url('/core/public/storage/images/category.jpg')); ?>" alt="">
            <span class="text-gray-dark"><?php echo e(__('All Categories')); ?></span>
        </a>
    </div>


    </div>


<?php /**PATH I:\xampp8212\htdocs\sscrackers\core\resources\views/includes/categories.blade.php ENDPATH**/ ?>