<?php
    $grandSubtotal = 0;
    $normalizeMetaText = function ($value) {
        $value = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $value))));
        return $value === '' ? null : $value;
    };
    $displayMetaText = function ($value, $limit = 60) use ($normalizeMetaText) {
        $value = $normalizeMetaText($value);
        return $value ? Str::limit($value, $limit) : null;
    };
?>

<?php if(Session::has('cart')): ?>
    <?php $__currentLoopData = Session::get('cart'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $quantity = $cart['qty'] ?? $cart['quantity'] ?? 0;
            $productName = trim((string) ($cart['name'] ?? ''));
            if ($productName === '' && \App\Models\Item::where('id', \App\Helpers\PriceHelper::GetItemId($key))->exists()) {
                $productName = \App\Models\Item::find(\App\Helpers\PriceHelper::GetItemId($key))->name ?? '';
            }
            $itemPrice = \App\Helpers\PriceHelper::cartEntryTotalPrice($cart);
            $itemSubtotal = $itemPrice * $quantity;
            $grandSubtotal += $itemSubtotal;
        ?>
        <div class="entry">
            <div class="entry-thumb">
                <a href="<?php echo e(route('front.product', $cart['slug'] ?? '')); ?>">
                    <img src="<?php echo e(url('/core/public/storage/images/' . ($cart['photo'] ?? 'placeholder.png'))); ?>" alt="Product">
                </a>
            </div>
            <div class="entry-content">
                <h4 class="entry-title">
                    <a href="<?php echo e(route('front.product', $cart['slug'] ?? '')); ?>">
                        <?php echo e(Str::limit($productName, 29)); ?>

                    </a>
                </h4>
                <span class="entry-meta"><?php echo e($quantity); ?> x <?php echo e(PriceHelper::setCurrencyPrice($itemPrice)); ?></span>

                <?php if(!empty($cart['attribute']['option_name']) && is_array($cart['attribute']['option_name'])): ?>
                    <?php $__currentLoopData = $cart['attribute']['option_name']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionkey => $option_name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="att"><em><?php echo e($cart['attribute']['names'][$optionkey] ?? ''); ?>:</em> <?php echo e($option_name); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

            </div>
            <div class="entry-delete">
                <a href="<?php echo e(route('front.cart.destroy', $key)); ?>"><i class="icon-x"></i></a>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="text-right">
        <p class="text-gray-dark py-2 mb-0">
            <span class="text-muted"><?php echo e(__('Subtotal')); ?>:</span>
            <?php echo e(PriceHelper::setCurrencyPrice($grandSubtotal)); ?>

        </p>
    </div>

    <div class="d-flex justify-content-between">
        <div class="w-50 d-block">
            <a class="btn btn-primary btn-sm mb-0" href="<?php echo e(route('front.cart')); ?>"><span><?php echo e(__('Cart')); ?></span></a>
        </div>
        <div class="w-50 d-block text-end">
            <a class="btn btn-primary btn-sm mb-0" href="<?php echo e(route('front.checkout.billing')); ?>"><span><?php echo e(__('Checkout')); ?></span></a>
        </div>
    </div>
<?php else: ?>
    <?php echo e(__('Cart empty')); ?>

<?php endif; ?>
<?php /**PATH I:\xampp8212\htdocs\sscrackers\core\resources\views/includes/header_cart.blade.php ENDPATH**/ ?>