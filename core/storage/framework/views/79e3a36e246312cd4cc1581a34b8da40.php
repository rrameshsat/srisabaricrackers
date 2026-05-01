
<?php
  
    $links = json_decode($menus->menus, true);
 
?>

<nav class="site-menu">
    <ul>
      
        <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
             $href = Helper::getHref($link); 
            
            ?>

            <?php if(!array_key_exists("children",$link)): ?>
                <li class="<?php if($href == URL::current() ): ?> active  <?php endif; ?>">
                    <a href="<?php echo e($link["href"] == null ? $href : $link["href"]); ?>" target="<?php echo e($link["target"]); ?>"><?php echo e($link["text"]); ?></a>
                </li>
            <?php else: ?>
                <li class="t-h-dropdown">
                    <a class="main-link" href="<?php echo e($href); ?>" <?php echo e($link["target"]); ?>><?php echo e($link["text"]); ?><i class="icon-chevron-down"></i></a>

                    <div class="t-h-dropdown-menu">
                        <?php $__currentLoopData = $link["children"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <?php
                            $l2Href = Helper::getHref($level2);
                            
                        ?>
                        
                        <a class="<?php if($l2Href == URL::current() ): ?> active  <?php endif; ?>" href="<?php echo e($l2Href); ?>" target="<?php echo e($level2["target"]); ?>">
                            <i class="icon-chevron-right pr-2"></i>
                            <?php echo e($level2["text"]); ?>

                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                </li>
            <?php endif; ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</nav><?php /**PATH I:\xampp8212\htdocs\sscrackers\core\resources\views/master/inc/site-menu.blade.php ENDPATH**/ ?>