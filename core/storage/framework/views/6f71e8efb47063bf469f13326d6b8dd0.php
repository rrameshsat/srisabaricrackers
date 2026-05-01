<?php $__env->startSection('meta'); ?>
    <meta name="keywords" content="<?php echo e($setting->meta_keywords); ?>">
    <meta name="description" content="<?php echo e($setting->meta_description); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>


    <?php if($extra_settings->is_t2_slider == 1): ?>
        <div class="slider-area-wrapper mt-0">
            <!-- Main Slider-->
            <div class="hero-slider">
                <div class="hero-slider-main owl-carousel dots-inside">
                    <?php $__currentLoopData = $sliders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="item
                            <?php if(DB::table('languages')->where('is_default', 1)->first()->rtl == 1): ?> d-flex justify-content-end <?php endif; ?>
                            "
                            style="background: url('<?php echo e(url('/core/public/storage/images/' . $slider->photo)); ?>')">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="item-inner">
                                            <div class="from-bottom" style="text-align: <?php echo e($slider->text_position ?? 'left'); ?>">
                                                <?php if($slider->logo): ?>
                                                    <img class="d-inline-block brand-logo"
                                                        src="<?php echo e(url('/core/public/storage/images/' . $slider->logo)); ?>" alt="logo">
                                                <?php endif; ?>

                                                <div class="title" style="color: <?php echo e($slider->title_color ?? '#ffffff'); ?>"><?php echo e($slider->title); ?></div>
                                                <div class="subtitle" style="color: <?php echo e($slider->text_color ?? '#ffffff'); ?>"><?php echo e($slider->details); ?></div>
                                            </div>
                                            <?php if($slider->link != '#'): ?>
                                            <a class="btn btn-primary scale-up delay-1" href="<?php echo e($slider->link); ?>">
                                                <span><?php echo e(__('Buy Now')); ?></span>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if($extra_settings->is_t2_service_section == 1): ?>
        <section class="service-section mt-30 pt-0">
            <div class="container">
                <div class="row">
                    <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-lg-3 col-sm-6 text-center mb-30">
                            <div class="single-service single-service2">
                                <img src="<?php echo e(url('/core/public/storage/images/' . $service->photo)); ?>" alt="Shipping">
                                <div class="content">
                                    <h6 class="mb-2"><?php echo e($service->title); ?></h6>
                                    <p class="text-sm text-muted mb-0"><?php echo e($service->details); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if($extra_settings->is_t2_3_column_banner_first == 1): ?>
        <div class="bannner-section mt-30">
            <div class="container ">
                <div class="row gx-3">
                    <div class="col-md-4">
                        <a href="<?php echo e($banner_first['firsturl1']); ?>" class="genius-banner">
                            <img src="<?php echo e(url('/core/public/storage/images/' . $banner_first['img1'])); ?>" alt="">
                            <div class="inner-content">
                                <?php if(isset($banner_first['subtitle1'])): ?>
                                    <p style="<?php echo e(isset($banner_first['subtitle_color_1']) ? 'color:'.$banner_first['subtitle_color_1'] : ''); ?>"><?php echo e($banner_first['subtitle1']); ?></p>
                                <?php endif; ?>
                                <?php if(isset($banner_first['title1'])): ?>
                                    <h4 style="<?php echo e(isset($banner_first['title_color_1']) ? 'color:'.$banner_first['title_color_1'] : ''); ?>"><?php echo e($banner_first['title1']); ?></h4>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="<?php echo e($banner_first['firsturl2']); ?>" class="genius-banner">
                            <img src="<?php echo e(url('/core/public/storage/images/' . $banner_first['img2'])); ?>" alt="">
                            <div class="inner-content">
                                <?php if(isset($banner_first['subtitle2'])): ?>
                                    <p style="<?php echo e(isset($banner_first['subtitle_color_2']) ? 'color:'.$banner_first['subtitle_color_2'] : ''); ?>"><?php echo e($banner_first['subtitle2']); ?></p>
                                <?php endif; ?>
                                <?php if(isset($banner_first['title2'])): ?>
                                    <h4 style="<?php echo e(isset($banner_first['title_color_2']) ? 'color:'.$banner_first['title_color_2'] : ''); ?>"><?php echo e($banner_first['title2']); ?></h4>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="<?php echo e($banner_first['firsturl3']); ?>" class="genius-banner">
                            <img src="<?php echo e(url('/core/public/storage/images/' . $banner_first['img3'])); ?>" alt="">
                            <div class="inner-content">
                                <?php if(isset($banner_first['subtitle3'])): ?>
                                    <p style="<?php echo e(isset($banner_first['subtitle_color_3']) ? 'color:'.$banner_first['subtitle_color_3'] : ''); ?>"> <?php echo e($banner_first['subtitle3']); ?></p>
                                <?php endif; ?>
                                <?php if(isset($banner_first['title3'])): ?>
                                    <h4 style="<?php echo e(isset($banner_first['title_color_3']) ? 'color:'.$banner_first['title_color_3'] : ''); ?>"><?php echo e($banner_first['title3']); ?></h4>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <?php if($extra_settings->is_t2_falsh == 1): ?>
        <div class="flash-sell-new-section mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="section-title section-title2 section-title3section-title section-title2 section-title3">
                            <h2 class="h3"><?php echo e(__('Flash Deal')); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-content">
                            <div class="flash-deal-slider owl-carousel">
                                <?php $__currentLoopData = $products->orderBy('id', 'DESC')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($item->is_type == 'flash_deal' && $item->date != null): ?>
                                        <div class="slider-item">
                                            <div class="product-card ">
                                                <div class="product-thumb">
                                                    <?php if(!$item->is_stock()): ?>
                                                        <div
                                                            class="product-badge bg-secondary border-default text-body
                                                ">
                                                            <?php echo e(__('out of stock')); ?></div>
                                                    <?php endif; ?>
                                                    <?php if($item->previous_price && $item->previous_price != 0): ?>
                                                        <div class="product-badge product-badge2 bg-info">
                                                            -<?php echo e(PriceHelper::DiscountPercentage($item)); ?></div>
                                                    <?php endif; ?>
                                                    <img class="lazy"
                                                        data-src="<?php echo e(url('/core/public/storage/images/' . $item->thumbnail)); ?>"
                                                        alt="Product">
                                                    <div class="product-button-group"><a
                                                            class="product-button wishlist_store"
                                                            href="<?php echo e(route('user.wishlist.store', $item->id)); ?>"
                                                            title="<?php echo e(__('Wishlist')); ?>"><i class="icon-heart"></i></a>
                                                        <a data-target="<?php echo e(route('fornt.compare.product', $item->id)); ?>"
                                                            class="product-button product_compare" href="javascript:;"
                                                            title="<?php echo e(__('Compare')); ?>"><i class="icon-repeat"></i></a>
                                                        <?php echo $__env->make('includes.item_footer', [
                                                            'sitem' => $item,
                                                        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                                    </div>
                                                </div>
                                                <div class="product-card-inner">
                                                    <div class="product-card-body">

                                                        <div class="product-category"><a
                                                                href="<?php echo e(route('front.catalog') . '?category=' . $item->category->slug); ?>"><?php echo e($item->category->name); ?></a>
                                                        </div>
                                                        <h3 class="product-title"><a
                                                                href="<?php echo e(route('front.product', $item->slug)); ?>">
                                                                <?php echo e(Str::limit($item->name, 50)); ?>

                                                            </a></h3>
                                                        <div class="rating-stars">
                                                            <?php echo Helper::renderStarRating($item->reviews->avg('rating')); ?>

                                                        </div>
                                                        <h4 class="product-price">
                                                            <?php if($item->previous_price != 0): ?>
                                                                <del><?php echo e(PriceHelper::setPreviousPrice($item->previous_price)); ?></del>
                                                            <?php endif; ?>

                                                            <?php echo e(PriceHelper::grandCurrencyPrice($item)); ?>

                                                        </h4>
                                                        <?php if(date('d-m-y') != \Carbon\Carbon::parse($item->date)->format('d-m-y')): ?>
                                                            <div class="countdown countdown-alt mb-3"
                                                                data-date-time="<?php echo e($item->date); ?>">
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <?php if($extra_settings->is_t2_new_product == 1): ?>
        <section class="selected-product-section mt-50 theme2">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title section-title2 section-title3">
                            <h2 class="h3"><?php echo e(__('New  Products')); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-lg-12">

                        <div class="features-slider  owl-carousel">
                            <?php $__currentLoopData = $products->orderBy('id', 'DESC')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($key <= 9): ?>
                                    <div class="slider-item">
                                        <div class="product-card ">
                                            <div class="product-thumb">
                                                <?php if(!$item->is_stock()): ?>
                                                    <div
                                                        class="product-badge bg-secondary border-default text-body
                                                ">
                                                        <?php echo e(__('out of stock')); ?></div>
                                                <?php endif; ?>
                                                <?php if($item->previous_price && $item->previous_price != 0): ?>
                                                    <div class="product-badge product-badge2 bg-info">
                                                        -<?php echo e(PriceHelper::DiscountPercentage($item)); ?></div>
                                                <?php endif; ?>
                                                <img class="lazy"
                                                    data-src="<?php echo e(url('/core/public/storage/images/' . $item->thumbnail)); ?>"
                                                    alt="Product">
                                                <div class="product-button-group"><a class="product-button wishlist_store"
                                                        href="<?php echo e(route('user.wishlist.store', $item->id)); ?>"
                                                        title="<?php echo e(__('Wishlist')); ?>"><i class="icon-heart"></i></a>
                                                    <a data-target="<?php echo e(route('fornt.compare.product', $item->id)); ?>"
                                                        class="product-button product_compare" href="javascript:;"
                                                        title="<?php echo e(__('Compare')); ?>"><i class="icon-repeat"></i></a>
                                                    <?php echo $__env->make('includes.item_footer', ['sitem' => $item], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                                </div>
                                            </div>
                                            <div class="product-card-inner">
                                                <div class="product-card-body">
                                                    <div class="product-category"><a
                                                            href="<?php echo e(route('front.catalog') . '?category=' . $item->category->slug); ?>"><?php echo e($item->category->name); ?></a>
                                                    </div>
                                                    <h3 class="product-title"><a
                                                            href="<?php echo e(route('front.product', $item->slug)); ?>">
                                                            <?php echo e(Str::limit($item->name, 35)); ?>

                                                        </a></h3>
                                                    <div class="rating-stars">
                                                        <?php echo Helper::renderStarRating($item->reviews->avg('rating')); ?>

                                                    </div>
                                                    <h4 class="product-price">
                                                        <?php if($item->previous_price != 0): ?>
                                                            <del><?php echo e(PriceHelper::setPreviousPrice($item->previous_price)); ?></del>
                                                        <?php endif; ?>
                                                        <?php echo e(PriceHelper::grandCurrencyPrice($item)); ?>

                                                    </h4>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                <?php break; ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if($extra_settings->is_t2_3_column_banner_second == 1): ?>
    <div class="bannner-section mt-60">
        <div class="container ">
            <div class="row gx-3">
                <div class="col-md-4">
                    <a href="<?php echo e($banner_secend['url1']); ?>" class="genius-banner">
                        <img class="lazy" data-src="<?php echo e(url('/core/public/storage/images/' . $banner_secend['img1'])); ?>"
                            alt="">
                        <div class="inner-content">
                            <?php if(isset($banner_secend['subtitle1'])): ?>
                                <p style="<?php echo e(isset($banner_secend['subtitle_color_1']) ? 'color:'.$banner_secend['subtitle_color_1'] : ''); ?>"><?php echo e($banner_secend['subtitle1']); ?></p>
                            <?php endif; ?>

                            <?php if(isset($banner_secend['title1'])): ?>
                                <h4 style="<?php echo e(isset($banner_secend['title_color_1']) ? 'color:'.$banner_secend['title_color_1'] : ''); ?>"><?php echo e($banner_secend['title1']); ?></h4>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="<?php echo e($banner_secend['url2']); ?>" class="genius-banner">
                        <img class="lazy" data-src="<?php echo e(url('/core/public/storage/images/' . $banner_secend['img2'])); ?>"
                            alt="">
                        <div class="inner-content">
                            <?php if(isset($banner_secend['subtitle2'])): ?>
                                <p style="<?php echo e(isset($banner_secend['subtitle_color_2']) ? 'color:'.$banner_secend['subtitle_color_2'] : ''); ?>"><?php echo e($banner_secend['subtitle2']); ?></p>
                            <?php endif; ?>

                            <?php if(isset($banner_secend['title2'])): ?>
                                <h4 style="<?php echo e(isset($banner_secend['title_color_2']) ? 'color:'.$banner_secend['title_color_2'] : ''); ?>"> <?php echo e($banner_secend['title2']); ?></h4>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="<?php echo e($banner_secend['url3']); ?>" class="genius-banner">
                        <img class="lazy" data-src="<?php echo e(url('/core/public/storage/images/' . $banner_secend['img3'])); ?>"
                            alt="">
                        <div class="inner-content">
                            <?php if(isset($banner_secend['subtitle3'])): ?>
                                <p style="<?php echo e(isset($banner_secend['subtitle_color_3']) ? 'color:'.$banner_secend['subtitle_color_3'] : ''); ?>"> <?php echo e($banner_secend['subtitle3']); ?></p>
                            <?php endif; ?>

                            <?php if(isset($banner_secend['title3'])): ?>
                                <h4 style="<?php echo e(isset($banner_secend['title_color_3']) ? 'color:'.$banner_secend['title_color_3'] : ''); ?>"><?php echo e($banner_secend['title3']); ?></h4>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if($extra_settings->is_t2_featured_product == 1): ?>
    <section class="selected-product-section mt-50 theme2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title section-title2 section-title3">
                        <h2 class="h3"><?php echo e(__('Featured Products')); ?></h2>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-lg-12">

                    <div class="features-slider  owl-carousel">
                        <?php $__currentLoopData = $products->orderBy('id', 'DESC')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($item->is_type == 'feature'): ?>
                                <div class="slider-item">
                                    <div class="product-card ">
                                        <div class="product-thumb">
                                            <?php if(!$item->is_stock()): ?>
                                                <div
                                                    class="product-badge bg-secondary border-default text-body
                                                    ">
                                                    <?php echo e(__('out of stock')); ?></div>
                                            <?php endif; ?>
                                            <?php if($item->previous_price && $item->previous_price != 0): ?>
                                                <div class="product-badge product-badge2 bg-info">
                                                    -<?php echo e(PriceHelper::DiscountPercentage($item)); ?></div>
                                            <?php endif; ?>
                                            <img class="lazy"
                                                data-src="<?php echo e(url('/core/public/storage/images/' . $item->thumbnail)); ?>"
                                                alt="Product">
                                            <div class="product-button-group"><a class="product-button wishlist_store"
                                                    href="<?php echo e(route('user.wishlist.store', $item->id)); ?>"
                                                    title="<?php echo e(__('Wishlist')); ?>"><i class="icon-heart"></i></a>
                                                <a data-target="<?php echo e(route('fornt.compare.product', $item->id)); ?>"
                                                    class="product-button product_compare" href="javascript:;"
                                                    title="<?php echo e(__('Compare')); ?>"><i class="icon-repeat"></i></a>
                                                <?php echo $__env->make('includes.item_footer', ['sitem' => $item], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                            </div>
                                        </div>
                                        <div class="product-card-inner">
                                            <div class="product-card-body">
                                                <div class="product-category"><a
                                                        href="<?php echo e(route('front.catalog') . '?category=' . $item->category->slug); ?>"><?php echo e($item->category->name); ?></a>
                                                </div>
                                                <h3 class="product-title"><a
                                                        href="<?php echo e(route('front.product', $item->slug)); ?>">
                                                        <?php echo e(Str::limit($item->name, 35)); ?>

                                                    </a></h3>
                                                <div class="rating-stars">
                                                    <?php echo Helper::renderStarRating($item->reviews->avg('rating')); ?>

                                                </div>
                                                <h4 class="product-price">
                                                    <?php if($item->previous_price != 0): ?>
                                                        <del><?php echo e(PriceHelper::setPreviousPrice($item->previous_price)); ?></del>
                                                    <?php endif; ?>
                                                    <?php echo e(PriceHelper::grandCurrencyPrice($item)); ?>

                                                </h4>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

            </div>
        </div>
    </section>
<?php endif; ?>

<?php if($extra_settings->is_t2_bestseller_product == 1): ?>
    <section class="selected-product-section mt-50  theme2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title section-title2  section-title3">
                        <h2 class="h3"><?php echo e(__('Best Seller')); ?></h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="features-slider  owl-carousel">
                        <?php $__currentLoopData = $products->orderBy('id', 'DESC')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($item->is_type == 'best'): ?>
                                <div class="slider-item">
                                    <div class="product-card ">
                                        <div class="product-thumb">
                                            <?php if(!$item->is_stock()): ?>
                                                <div
                                                    class="product-badge bg-secondary border-default text-body
                                                ">
                                                    <?php echo e(__('out of stock')); ?></div>
                                            <?php endif; ?>
                                            <?php if($item->previous_price && $item->previous_price != 0): ?>
                                                <div class="product-badge product-badge2 bg-info">
                                                    -<?php echo e(PriceHelper::DiscountPercentage($item)); ?></div>
                                            <?php endif; ?>
                                            <img class="lazy"
                                                data-src="<?php echo e(url('/core/public/storage/images/' . $item->thumbnail)); ?>"
                                                alt="Product">
                                            <div class="product-button-group"><a class="product-button wishlist_store"
                                                    href="<?php echo e(route('user.wishlist.store', $item->id)); ?>"
                                                    title="<?php echo e(__('Wishlist')); ?>"><i class="icon-heart"></i></a>
                                                <a data-target="<?php echo e(route('fornt.compare.product', $item->id)); ?>"
                                                    class="product-button product_compare" href="javascript:;"
                                                    title="<?php echo e(__('Compare')); ?>"><i class="icon-repeat"></i></a>
                                                <?php echo $__env->make('includes.item_footer', ['sitem' => $item], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                            </div>

                                        </div>
                                        <div class="product-card-inner">
                                            <div class="product-card-body">
                                                <div class="product-category"><a
                                                        href="<?php echo e(route('front.catalog') . '?category=' . $item->category->slug); ?>"><?php echo e($item->category->name); ?></a>
                                                </div>
                                                <h3 class="product-title"><a
                                                        href="<?php echo e(route('front.product', $item->slug)); ?>">
                                                        <?php echo e(Str::limit($item->name, 35)); ?>

                                                    </a></h3>
                                                <div class="rating-stars">
                                                    <?php echo Helper::renderStarRating($item->reviews->avg('rating')); ?>

                                                </div>
                                                <h4 class="product-price">
                                                    <?php if($item->previous_price != 0): ?>
                                                        <del><?php echo e(PriceHelper::setPreviousPrice($item->previous_price)); ?></del>
                                                    <?php endif; ?>
                                                    <?php echo e(PriceHelper::grandCurrencyPrice($item)); ?>

                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

            </div>
        </div>
    </section>
<?php endif; ?>

<?php if($extra_settings->is_t2_toprated_product == 1): ?>
    <section class="selected-product-section mt-50  theme2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title section-title2  section-title3">
                        <h2 class="h3"><?php echo e(__('Top Rated')); ?></h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">

                    <div class="features-slider  owl-carousel">
                        <?php $__currentLoopData = $products->orderBy('id', 'DESC')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($item->is_type == 'top'): ?>
                                <div class="slider-item">
                                    <div class="product-card ">
                                        <div class="product-thumb">
                                            <?php if(!$item->is_stock()): ?>
                                                <div
                                                    class="product-badge bg-secondary border-default text-body
                                                ">
                                                    <?php echo e(__('out of stock')); ?></div>
                                            <?php endif; ?>
                                            <?php if($item->previous_price && $item->previous_price != 0): ?>
                                                <div class="product-badge product-badge2 bg-info">
                                                    -<?php echo e(PriceHelper::DiscountPercentage($item)); ?></div>
                                            <?php endif; ?>
                                            <img class="lazy"
                                                data-src="<?php echo e(url('/core/public/storage/images/' . $item->thumbnail)); ?>"
                                                alt="Product">
                                            <div class="product-button-group"><a class="product-button wishlist_store"
                                                    href="<?php echo e(route('user.wishlist.store', $item->id)); ?>"
                                                    title="<?php echo e(__('Wishlist')); ?>"><i class="icon-heart"></i></a>
                                                <a data-target="<?php echo e(route('fornt.compare.product', $item->id)); ?>"
                                                    class="product-button product_compare" href="javascript:;"
                                                    title="<?php echo e(__('Compare')); ?>"><i class="icon-repeat"></i></a>
                                                <?php echo $__env->make('includes.item_footer', ['sitem' => $item], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                            </div>
                                        </div>
                                        <div class="product-card-inner">
                                            <div class="product-card-body">
                                                <div class="product-category"><a
                                                        href="<?php echo e(route('front.catalog') . '?category=' . $item->category->slug); ?>"><?php echo e($item->category->name); ?></a>
                                                </div>
                                                <h3 class="product-title"><a
                                                        href="<?php echo e(route('front.product', $item->slug)); ?>">
                                                        <?php echo e(Str::limit($item->name, 35)); ?>

                                                    </a></h3>
                                                <div class="rating-stars">
                                                    <?php echo Helper::renderStarRating($item->reviews->avg('rating')); ?>

                                                </div>
                                                <h4 class="product-price">
                                                    <?php if($item->previous_price != 0): ?>
                                                        <del><?php echo e(PriceHelper::setPreviousPrice($item->previous_price)); ?></del>
                                                    <?php endif; ?>
                                                    <?php echo e(PriceHelper::grandCurrencyPrice($item)); ?>

                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

            </div>
        </div>
    </section>
<?php endif; ?>

<?php if($extra_settings->is_t2_2_column_banner == 1): ?>
    <div class="bannner-section mt-50">
        <div class="container ">
            <div class="row gx-3">
                <div class="col-md-6">
                    <a href="<?php echo e($banner_third['url1']); ?>" class="genius-banner">
                        <img class="lazy" data-src="<?php echo e(url('/core/public/storage/images/' . $banner_third['img1'])); ?>"
                            alt="">
                        <div class="inner-content">
                            <?php if(isset($banner_third['subtitle1'])): ?>
                                <p style="<?php echo e(isset($banner_third['subtitle_color_1']) ? 'color:'.$banner_third['subtitle_color_1'] : ''); ?>"><?php echo e($banner_third['subtitle1']); ?></p>
                            <?php endif; ?>
                            <?php if(isset($banner_third['title1'])): ?>
                                <h4 style="<?php echo e(isset($banner_third['title_color_1']) ? 'color:'.$banner_third['title_color_1'] : ''); ?>"><?php echo e($banner_third['title1']); ?></h4>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="<?php echo e($banner_third['url2']); ?>" class="genius-banner">
                        <img class="lazy" data-src="<?php echo e(url('/core/public/storage/images/' . $banner_third['img2'])); ?>"
                            alt="">
                        <div class="inner-content">
                            <?php if(isset($banner_third['subtitle2'])): ?>
                                <p style="<?php echo e(isset($banner_third['subtitle_color_2']) ? 'color:'.$banner_third['subtitle_color_2'] : ''); ?>"> <?php echo e($banner_third['subtitle2']); ?></p>
                            <?php endif; ?>
                            <?php if(isset($banner_third['title2'])): ?>
                                <h4 style="<?php echo e(isset($banner_third['title_color_2']) ? 'color:'.$banner_third['title_color_2'] : ''); ?>"><?php echo e($banner_third['title2']); ?></h4>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if($extra_settings->is_t2_three_column_category == 1): ?>
    <div class="flash-sell-area three_column_product mt-50">
        <div class="container">
            <div class="row gx-3 justify-content-center">
                <?php $__currentLoopData = $two_column_categoriess; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $two_column_key => $two_column_category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-xl-4 col-lg-6">
                        <div class="section-title">
                            <h2 class="h3"><?php echo e($two_column_category['name']->name); ?></h2>
                        </div>
                        <div class="main-content">
                            <div class="newproduct-slider owl-carousel">
                                <?php $__currentLoopData = $two_column_categoriess[$two_column_key]['items']->chunk(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $two_column_category_itemt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="slider-item">
                                        <?php $__currentLoopData = $two_column_category_itemt; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $two_column_category_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="product-card p-col">
                                                <a class="product-thumb"
                                                    href="<?php echo e(route('front.product', $two_column_category_item->slug)); ?>">
                                                    <?php if(!$two_column_category_item->is_stock()): ?>
                                                        <div
                                                            class="product-badge bg-secondary border-default text-body
                                                ">
                                                            <?php echo e(__('out of stock')); ?></div>
                                                    <?php endif; ?>

                                                    <img class="lazy"
                                                        data-src="<?php echo e(url('/core/public/storage/images/' . $two_column_category_item->thumbnail)); ?>"
                                                        alt="Product">
                                                </a>
                                                <div class="product-card-body">
                                                    <h3 class="product-title"><a
                                                            href="<?php echo e(route('front.product', $two_column_category_item->slug)); ?>">
                                                            <?php echo e(Str::limit($two_column_category_item->name, 40)); ?>

                                                        </a></h3>
                                                    <div class="rating-stars">
                                                        <?php echo Helper::renderStarRating($two_column_category_item->reviews->avg('rating')); ?>

                                                    </div>
                                                    <h4 class="product-price">
                                                        <?php if($two_column_category_item->previous_price != 0): ?>
                                                            <del><?php echo e(PriceHelper::setPreviousPrice($two_column_category_item->previous_price)); ?></del>
                                                        <?php endif; ?>
                                                        <?php echo e(PriceHelper::grandCurrencyPrice($two_column_category_item)); ?>

                                                    </h4>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>
        </div>
    </div>
<?php endif; ?>

<?php if($extra_settings->is_t2_blog_section == 1): ?>
    <div class="blog-section-h page_section mt-50 mb-30">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title section-title2 section-title3">
                        <h2 class="h3"><?php echo e(__('Our Blog')); ?></h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="home-blog-slider owl-carousel">
                        <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="slider-item">
                                <a href="<?php echo e(route('front.blog.details', $post->slug)); ?>" class="blog-post">
                                    <div class="post-thumb">
                                        <img class="lazy"
                                            data-src="<?php echo e(url('/core/public/storage/images/' . json_decode($post->photo, true)[array_key_first(json_decode($post->photo, true))])); ?>"
                                            alt="Blog Post">
                                    </div>
                                    <div class="post-body">

                                        <h3 class="post-title"> <?php echo e(Str::limit($post->title, 55)); ?>

                                        </h3>
                                        <ul class="post-meta">

                                            <li><i class="icon-user"></i><?php echo e(__('Admin')); ?></li>
                                            <li><i
                                                    class="icon-clock"></i><?php echo e(date('jS F, Y', strtotime($post->created_at))); ?>

                                            </li>
                                        </ul>
                                        <p><?php echo e(Str::limit(strip_tags($post->details), 120, '...')); ?>

                                        </p>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if($extra_settings->is_t2_brand_section == 1): ?>
    <section class="brand-section  mt-30 mb-60">
        <div class="container ">
            <div class="row">
                <div class="col-lg-12 ">
                    <div class="section-title section-title2  section-title3">
                        <h2 class="h3"><?php echo e(__('Popular Brands')); ?></h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="brand-slider owl-carousel">
                        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="slider-item">
                                <a class="text-center"
                                    href="<?php echo e(route('front.catalog') . '?brand=' . $brand->slug); ?>">
                                    <img class="d-block hi-50 lazy"
                                        data-src="<?php echo e(url('/core/public/storage/images/' . $brand->photo)); ?>"
                                        alt="<?php echo e($brand->name); ?>" title="<?php echo e($brand->name); ?>">
                                </a>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('master.front', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH I:\xampp8212\htdocs\sscrackers\core\resources\views/front/themes/theme2.blade.php ENDPATH**/ ?>