<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php if(url()->current() == route('front.index')): ?>
        <title><?php echo $__env->yieldContent('hometitle'); ?></title>
    <?php else: ?>
        <title><?php echo e($setting->title); ?> -<?php echo $__env->yieldContent('title'); ?></title>
    <?php endif; ?>

    <!-- SEO Meta Tags-->
    <?php if(url()->current() == route('front.index')): ?>
        <meta name="author" content="GeniusDevs">
        <meta name="distribution" content="web">
        <meta name="description" content="<?php echo e($setting->meta_description); ?>">
        <meta name="keywords" content="<?php echo e($setting->meta_keywords); ?>">
        <meta name="image" content="<?php echo e(url('/core/public/storage/images/' . $setting->meta_image)); ?>">
        <meta property="og:title" content="<?php echo e($setting->title); ?>">
        <meta property="og:description" content="<?php echo e($setting->meta_description); ?>">
        <meta property="og:image" content="<?php echo e(url('/core/public/storage/images/' . $setting->meta_image)); ?>">
        <meta property="og:image:secure_url"
            content="<?php echo e(url('/core/public/storage/images/' . $setting->meta_image)); ?>" />
        <meta property="og:image:type" content="image/jpeg" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="627" />
        <meta property="og:url" content="<?php echo e(url()->current()); ?>">
        <meta property="og:site_name" content="<?php echo e($setting->title); ?>">
        <meta property="og:type" content="website">
    <?php else: ?>
        <?php echo $__env->yieldContent('meta'); ?>
    <?php endif; ?>

    <!-- Mobile Specific Meta Tag-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <!-- Favicon Icons-->
    <link rel="icon" type="image/png" href="<?php echo e(url('/core/public/storage/images/' . $setting->favicon)); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(url('/core/public/storage/images/' . $setting->favicon)); ?>">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo e(url('/core/public/storage/images/' . $setting->favicon)); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(url('/core/public/storage/images/' . $setting->favicon)); ?>">
    <link rel="apple-touch-icon" sizes="167x167" href="<?php echo e(url('/core/public/storage/images/' . $setting->favicon)); ?>">

    <!-- Vendor Styles including: Bootstrap, Font Icons, Plugins, etc.-->
    <link rel="stylesheet" media="screen" href="<?php echo e(asset('assets/front/css/plugins.min.css')); ?>">

    <?php echo $__env->yieldContent('styleplugins'); ?>

    <link id="mainStyles" rel="stylesheet" media="screen" href="<?php echo e(asset('assets/front/css/styles.min.css')); ?>">

    <link id="mainStyles" rel="stylesheet" media="screen" href="<?php echo e(asset('assets/front/css/responsive.css')); ?>">
    <!-- Color css -->
    <link
        href="<?php echo e(asset('assets/front/css/color.php?primary_color=') . str_replace('#', '', $setting->primary_color)); ?>"
        rel="stylesheet">

    <!-- Modernizr-->
    <script src="<?php echo e(asset('assets/front/js/modernizr.min.js')); ?>"></script>

    <?php if(DB::table('languages')->where('is_default', 1)->first()->rtl == 1): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/front/css/rtl.css')); ?>">
    <?php endif; ?>
    <style>
        <?php echo e($setting->custom_css); ?>

    </style>
    
    <?php if($setting->is_google_adsense == '1'): ?>
        <?php echo html_entity_decode($setting->google_adsense); ?>

    <?php endif; ?>
    

    
    <?php if($setting->is_google_analytics == '1'): ?>
        <?php echo html_entity_decode($setting->google_analytics); ?>

    <?php endif; ?>
    

    
    <?php if($setting->is_facebook_pixel == '1'): ?>
        <?php echo html_entity_decode($setting->facebook_pixel); ?>

    <?php endif; ?>
    

</head>

<!-- Body-->

<body
    class="
<?php if($setting->theme == 'theme1'): ?> body_theme1
<?php elseif($setting->theme == 'theme2'): ?>
body_theme2
<?php elseif($setting->theme == 'theme3'): ?>
body_theme3
<?php elseif($setting->theme == 'theme4'): ?>
body_theme4 <?php endif; ?>
">
    <?php if($setting->is_loader == 1): ?>
        <!-- Preloader Start -->
        <?php if($setting->is_loader == 1): ?>
            <div id="preloader">
                <img src="<?php echo e(url('/core/public/storage/images/' . $setting->loader)); ?>" alt="<?php echo e(__('Loading...')); ?>">
            </div>
        <?php endif; ?>

        <!-- Preloader endif -->
    <?php endif; ?>

    <!-- Header-->

    <header class="site-header navbar-sticky">
        <div class="menu-top-area">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="t-m-s-a">
                            <a class="track-order-link" href="<?php echo e(route('front.order.track')); ?>"><i
                                    class="icon-map-pin"></i><?php echo e(__('Track Order')); ?></a>
                            <a class="track-order-link compare-mobile d-lg-none"
                                href="<?php echo e(route('fornt.compare.index')); ?>"><?php echo e(__('Compare')); ?></a>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="right-area">

                            <a class="track-order-link wishlist-mobile d-inline-block d-lg-none"
                                href="<?php echo e(route('user.wishlist.index')); ?>"><i
                                    class="icon-heart"></i><?php echo e(__('Wishlist')); ?></a>

                            


                            <?php if($setting->enable_header_currency_dropdown == 1): ?>
                            <div class="t-h-dropdown ">
                                <a class="main-link" href="#"><?php echo e(__('Currency')); ?><i
                                        class="icon-chevron-down"></i></a>
                                <div class="t-h-dropdown-menu">
                                    <?php $__currentLoopData = DB::table('currencies')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a class="<?php echo e(Session::get('currency') == $currency->id ? 'active' : ($currency->is_default == 1 && !Session::has('currency') ? 'active' : '')); ?>"
                                            href="<?php echo e(route('front.currency.setup', $currency->id)); ?>"><i
                                                class="icon-chevron-right pr-2"></i><?php echo e($currency->name); ?></a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="login-register ">
                                <?php if(!Auth::user()): ?>
                                    <a class="track-order-link mr-2" href="<?php echo e(route('user.login')); ?>">
                                        <?php echo e(__('Login')); ?>

                                    </a>
                                    <a class="track-order-link mr-0" href="<?php echo e(route('user.register')); ?>">
                                      / <?php echo e(__('Register')); ?>

                                    </a>
                                <?php else: ?>
                                    <div class="t-h-dropdown">
                                        <div class="main-link">
                                            <i class="icon-user pr-2"></i> <span
                                                class="text-label"><?php echo e(Auth::user()->first_name); ?></span>
                                        </div>
                                        <div class="t-h-dropdown-menu">
                                            <a href="<?php echo e(route('user.dashboard')); ?>"><i
                                                    class="icon-chevron-right pr-2"></i><?php echo e(__('Dashboard')); ?></a>
                                            <a href="<?php echo e(route('user.logout')); ?>"><i
                                                    class="icon-chevron-right pr-2"></i><?php echo e(__('Logout')); ?></a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
</div>
        </div>
    </div>
</div>
<!-- Topbar-->
<div class="topbar">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex justify-content-between">
                    <!-- Logo-->
                    <div class="site-branding"><a class="site-logo align-self-center"
                            href="<?php echo e(route('front.index')); ?>"><img
                                src="<?php echo e(url('/core/public/storage/' . $setting->logo)); ?>"
                                alt="<?php echo e($setting->title); ?>"></a></div>
                    <!-- Search / Categories-->
                    <div class="search-box-wrap d-none d-lg-block d-flex align-items-center">
                        <div class="search-box-inner align-self-center">
                            <div class="search-box d-flex">
                                <select name="category" id="category_select" class="categoris">
                                    <option value=""><?php echo e(__('All')); ?></option>
                                    <?php $__currentLoopData = DB::table('categories')->whereStatus(1)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category->slug); ?>"><?php echo e($category->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <form class="input-group" id="header_search_form"
                                    action="<?php echo e(route('front.catalog')); ?>" method="get">
                                    <input type="hidden" name="category" value=""
                                        id="search__category">
                                    <span class="input-group-btn">
                                        <button type="submit"><i class="icon-search"></i></button>
                                    </span>
                                    <input class="form-control" type="text"
                                        data-target="<?php echo e(route('front.search.suggest')); ?>"
                                        id="__product__search" name="search"
                                        placeholder="<?php echo e(__('Search by product name')); ?>">
                                    <div class="serch-result d-none">
                                        
                                    </div>
                                </form>
                            </div>
                        </div>
                        <span class="d-block d-lg-none close-m-serch"><i class="icon-x"></i></span>
                    </div>
                    <!-- Toolbar-->
                    <div class="toolbar d-flex">

                        <div class="toolbar-item close-m-serch visible-on-mobile"><a href="#">
                                <div>
                                    <i class="icon-search"></i>
                                </div>
                            </a>
                        </div>
                        <div class="toolbar-item visible-on-mobile mobile-menu-toggle"><a href="#">
                                <div><i class="icon-menu"></i><span
                                        class="text-label"><?php echo e(__('Menu')); ?></span></div>
                            </a>
                        </div>

                        <div class="toolbar-item hidden-on-mobile"><a
                                href="<?php echo e(route('fornt.compare.index')); ?>">
                                <div><span class="compare-icon"><i class="icon-repeat"></i><span
                                            class="count-label compare_count"><?php echo e(Session::has('compare') ? count(Session::get('compare')) : '0'); ?></span></span><span
                                            class="text-label"><?php echo e(__('Compare')); ?></span></div>
                            </a>
                        </div>
                        <?php if(Auth::check()): ?>
                            <div class="toolbar-item hidden-on-mobile"><a
                                    href="<?php echo e(route('user.wishlist.index')); ?>">
                                    <div><span class="compare-icon"><i class="icon-heart"></i><span
                                                class="count-label wishlist_count"><?php echo e(Auth::user()->wishlists->count()); ?></span></span><span
                                                class="text-label"><?php echo e(__('Wishlist')); ?></span></div>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="toolbar-item hidden-on-mobile"><a
                                    href="<?php echo e(route('user.wishlist.index')); ?>">
                                    <div><span class="compare-icon"><i class="icon-heart"></i></span><span
                                            class="text-label"><?php echo e(__('Wishlist')); ?></span></div>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="toolbar-item"><a href="<?php echo e(route('front.cart')); ?>">
                                <div><span class="cart-icon"><i class="icon-shopping-cart"></i><span
                                            class="count-label cart_count"><?php echo e(Session::has('cart') ? count(Session::get('cart')) : '0'); ?>

                                        </span></span><span class="text-label"><?php echo e(__('Cart')); ?></span>
                                </div>
                            </a>
                            <div class="toolbar-dropdown cart-dropdown widget-cart  cart_view_header"
                                id="header_cart_load" data-target="<?php echo e(route('front.header.cart')); ?>">
                                <?php echo $__env->make('includes.header_cart', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Menu-->
                    <div class="mobile-menu">
                                <!-- Slideable (Mobile) Menu-->
                                <div class="mm-heading-area">
                                    <h4><?php echo e(__('Navigation')); ?></h4>
                                    <div class="toolbar-item visible-on-mobile mobile-menu-toggle mm-t-two">
                                        <a href="#">
                                            <div> <i class="icon-x"></i></div>
                                        </a>
                                    </div>
                                </div>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item" role="presentation99">
                                        <span class="active" id="mmenu-tab" data-bs-toggle="tab"
                                            data-bs-target="#mmenu" role="tab" aria-controls="mmenu"
                                            aria-selected="true"><?php echo e(__('Menu')); ?></span>
                                    </li>
                                    <li class="nav-item" role="presentation99">
                                        <span class="" id="mcat-tab" data-bs-toggle="tab"
                                            data-bs-target="#mcat" role="tab" aria-controls="mcat"
                                            aria-selected="false"><?php echo e(__('Category')); ?></span>
                                    </li>

                                </ul>
                                <div class="tab-content p-0">
                                    <div class="tab-pane fade show active" id="mmenu" role="tabpanel"
                                        aria-labelledby="mmenu-tab">
                                        <nav class="slideable-menu">
                                            <ul>
                                                <li class="<?php echo e(request()->routeIs('front.index') ? 'active' : ''); ?>"><a
                                                        href="<?php echo e(route('front.index')); ?>"><i
                                                            class="icon-chevron-right"></i><?php echo e(__('Home')); ?></a>
                                                </li>
                                                <?php if($setting->is_shop == 1): ?>
                                                    <li
                                                        class="<?php echo e(request()->routeIs('front.catalog*') ? 'active' : ''); ?>">
                                                        <a href="<?php echo e(route('front.catalog')); ?>"><i
                                                                class="icon-chevron-right"></i><?php echo e(__('Shop')); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if($setting->is_campaign == 1): ?>
                                                    <li
                                                        class="<?php echo e(request()->routeIs('front.campaign') ? 'active' : ''); ?>">
                                                        <a href="<?php echo e(route('front.campaign')); ?>"><i
                                                                class="icon-chevron-right"></i><?php echo e(__('Campaign')); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if($setting->is_brands == 1): ?>
                                                    <li
                                                        class="<?php echo e(request()->routeIs('front.brand') ? 'active' : ''); ?>">
                                                        <a href="<?php echo e(route('front.brand')); ?>"><i
                                                                class="icon-chevron-right"></i><?php echo e(__('Brand')); ?></a>
                                                    </li>
                                                <?php endif; ?>

                                                <?php if($setting->is_blog == 1): ?>
                                                    <li
                                                        class="<?php echo e(request()->routeIs('front.blog*') ? 'active' : ''); ?>">
                                                        <a href="<?php echo e(route('front.blog')); ?>"><i
                                                                class="icon-chevron-right"></i><?php echo e(__('Blog')); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                                <li class="t-h-dropdown">
                                                    <a class="" href="#"><i
                                                            class="icon-chevron-right"></i><?php echo e(__('Pages')); ?> <i
                                                            class="icon-chevron-down"></i></a>
                                                    <div class="t-h-dropdown-menu">
                                                        <?php if($setting->is_faq == 1): ?>
                                                            <a class="<?php echo e(request()->routeIs('front.faq*') ? 'active' : ''); ?>"
                                                                href="<?php echo e(route('front.faq')); ?>"><i
                                                                    class="icon-chevron-right pr-2"></i><?php echo e(__('Faq')); ?></a>
                                                        <?php endif; ?>
                                                        <?php
                                                            $headerPagesQuery = DB::table('pages')->where(function ($query) {
                                                                $query->wherePos(0)->orWhere('pos', 2);
                                                            });

                                                            if (\Illuminate\Support\Facades\Schema::hasColumn('pages', 'show_in_menu')) {
                                                                $headerPagesQuery->where('show_in_menu', 1);
                                                            }
                                                        ?>
                                                        <?php $__currentLoopData = $headerPagesQuery->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <a class="<?php echo e(request()->url() == route('front.page', $page->slug) ? 'active' : ''); ?> "
                                                                href="<?php echo e(route('front.page', $page->slug)); ?>"><i
                                                                    class="icon-chevron-right pr-2"></i><?php echo e($page->title); ?></a>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                </li>

                                                <?php if($setting->is_contact == 1): ?>
                                                    <li
                                                        class="<?php echo e(request()->routeIs('front.contact') ? 'active' : ''); ?>">
                                                        <a href="<?php echo e(route('front.contact')); ?>"><i
                                                                class="icon-chevron-right"></i><?php echo e(__('Contact')); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </nav>
                                    </div>
                                    <div class="tab-pane fade" id="mcat" role="tabpanel"
                                        aria-labelledby="mcat-tab">
                                        <nav class="slideable-menu">
                                            <?php echo $__env->make('includes.mobile-category', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Navbar-->
        <div class="navbar">
            <div class="container">
                <div class="row g-3 w-100">
                    <?php if($setting->is_show_category == 1): ?>
                        <div class="col-lg-3">
                            <?php echo $__env->make('includes.categories', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                    <?php endif; ?>
                    <div class="col-lg-9 d-flex justify-content-between">
                        <div class="nav-inner">
                            <?php echo $__env->make('master.inc.site-menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                        <?php
                            $free_shipping = DB::table('shipping_services')
                                ->whereStatus(1)
                                ->whereIsCondition(1)
                                ->first();
                        ?>

                    </div>
                </div>
            </div>
        </div>

    </header>
    <!-- Page Content-->
    <?php echo $__env->yieldContent('content'); ?>

    <!--    announcement banner section start   -->
    <a class="announcement-banner" href="#announcement-modal"></a>
    <div id="announcement-modal" class="mfp-hide white-popup">
        <?php if($setting->announcement_type == 'newletter'): ?>
            <div class="announcement-with-content">
                <div class="left-area">
                    <img src="<?php echo e(url('/core/public/storage/images/' . $setting->announcement)); ?>" alt="">
                </div>
                <div class="right-area">
                    <h3 class=""><?php echo e($setting->announcement_title); ?></h3>
                    <p><?php echo e($setting->announcement_details); ?></p>
                    <form class="subscriber-form" action="<?php echo e(route('front.subscriber.submit')); ?>" method="post">
                        <?php echo csrf_field(); ?>
                        <div class="input-group">
                            <input class="form-control" type="email" name="email"
                                placeholder="<?php echo e(__('Your e-mail')); ?>">
                            <span class="input-group-addon"><i class="icon-mail"></i></span>
                        </div>
                        <div aria-hidden="true">
                            <input type="hidden" name="b_c7103e2c981361a6639545bd5_1194bb7544" tabindex="-1">
                        </div>

                        <button class="btn btn-primary btn-block mt-2" type="submit">
                            <span><?php echo e(__('Subscribe')); ?></span>
                        </button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <a href="<?php echo e($setting->announcement_link); ?>">
                <img src="<?php echo e(url('/core/public/storage/images/' . $setting->announcement)); ?>" alt="">
            </a>
        <?php endif; ?>


    </div>
    <!--    announcement banner section end   -->

    <!-- Site Footer-->
    <footer class="site-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <!-- Contact Info-->
                    <section class="widget widget-light-skin">
                        <h3 class="widget-title"><?php echo e(__('Get In Touch')); ?></h3>
                        <p class="mb-1"><strong><?php echo e(__('Address')); ?>: </strong> <?php echo e($setting->footer_address); ?></p>
                        <p class="mb-1"><strong><?php echo e(__('Phone')); ?>: </strong> <?php echo e($setting->footer_phone); ?></p>
                        <p class="mb-1"><strong><?php echo e(__('Email')); ?>: </strong> <?php echo e($setting->footer_email); ?></p>
                        <ul class="list-unstyled text-sm">
                            <li><span class=""><strong><?php echo e($setting->working_days_from_to); ?>:
                                    </strong></span><?php echo e($setting->friday_start); ?> - <?php echo e($setting->friday_end); ?></li>
                        </ul>
                        <?php
                            $links = json_decode($setting->social_link, true)['links'];
                            $icons = json_decode($setting->social_link, true)['icons'];

                        ?>
                        <div class="footer-social-links">
                            <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link_key => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e($link); ?>"><span><i
                                            class="<?php echo e($icons[$link_key]); ?>"></i></span></a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </section>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <!-- Customer Info-->
                    <div class="widget widget-links widget-light-skin">
                        <h3 class="widget-title"><?php echo e(__('Usefull Links')); ?></h3>
                        <?php
                            $footerUsefulLinks = collect([
                                [
                                    'visible' => (bool) ($setting->show_footer_faq ?? config('footer.show_faq')) && $setting->is_faq == 1 && Route::has(config('footer.faq_route')),
                                    'label' => __('Faq'),
                                    'url' => Route::has(config('footer.faq_route')) ? route(config('footer.faq_route')) : null,
                                ],
                                [
                                    'visible' => (bool) ($setting->show_footer_how_it_works ?? config('footer.show_how_it_works')) && Route::has(config('footer.how_it_works_route')),
                                    'label' => __('How It Works'),
                                    'url' => Route::has(config('footer.how_it_works_route')) ? route(config('footer.how_it_works_route')) : null,
                                ],
                            ])->filter(fn ($link) => $link['visible']);
                        ?>
                        <ul>
                            <?php $__currentLoopData = $footerUsefulLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $footerUsefulLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <a class="" href="<?php echo e($footerUsefulLink['url']); ?>"><?php echo e($footerUsefulLink['label']); ?></a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $footerPagesQuery = DB::table('pages')->where(function ($query) {
                                    $query->wherePos(2)->orWhere('pos', 1);
                                });

                                if (\Illuminate\Support\Facades\Schema::hasColumn('pages', 'show_in_menu')) {
                                    $footerPagesQuery->where('show_in_menu', 1);
                                }
                            ?>
                            <?php $__currentLoopData = $footerPagesQuery->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><a href="<?php echo e(route('front.page', $page->slug)); ?>"><?php echo e($page->title); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <!-- Subscription-->
                    <section class="widget">
                        <h3 class="widget-title"><?php echo e(__('Newsletter')); ?></h3>
                        <form class="row subscriber-form" action="<?php echo e(route('front.subscriber.submit')); ?>"
                            method="post">
                            <?php echo csrf_field(); ?>
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <input class="form-control" type="email" name="email"
                                        placeholder="<?php echo e(__('Your e-mail')); ?>">
                                    <span class="input-group-addon"><i class="icon-mail"></i></span>
                                </div>
                                <div aria-hidden="true">
                                    <input type="hidden" name="b_c7103e2c981361a6639545bd5_1194bb7544"
                                        tabindex="-1">
                                </div>

                            </div>
                            <div class="col-sm-12">
                                <button class="btn btn-primary btn-block mt-2" type="submit">
                                    <span><?php echo e(__('Subscribe')); ?></span>
                                </button>
                            </div>
                            <div class="col-lg-12">
                                <p class="text-sm opacity-80 pt-2">
                                    <?php echo e(__('Subscribe to our Newsletter to receive early discount offers, latest news, sales and promo information.')); ?>

                                </p>
                            </div>
                        </form>
                        <div class="pt-3"><img class="d-block gateway_image"
                                src="<?php echo e($setting->footer_gateway_img ? url('/core/public/storage/images/' . $setting->footer_gateway_img) : asset('system/resources/assets/images/placeholder.png')); ?>">
                        </div>
                    </section>
                </div>
            </div>
            <!-- Copyright-->
            <p class="footer-copyright"> <?php echo e($setting->copy_right); ?></p>
        </div>
    </footer>

    <!-- Back To Top Button-->
    <a class="scroll-to-top-btn" href="#">
        <i class="icon-chevron-up"></i>
    </a>
    <!-- Backdrop-->
    <div class="site-backdrop"></div>

    <!-- Cookie alert dialog  -->
    <?php if($setting->is_cookie == 1): ?>
        <?php echo $__env->make('cookie-consent::index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
    <!-- Cookie alert dialog  -->


    <?php
        $mainbs = [];
        $mainbs['is_announcement'] = $setting->is_announcement;
        $mainbs['announcement_delay'] = $setting->announcement_delay;
        $mainbs['overlay'] = $setting->overlay;
        $mainbs = json_encode($mainbs);
    ?>

    <script>
        var mainbs = <?php echo $mainbs; ?>;
        var decimal_separator = '<?php echo $setting->decimal_separator; ?>';
        var thousand_separator = '<?php echo $setting->thousand_separator; ?>';
    </script>

    <script>
        let language = {
            Days: '<?php echo e(__('Days')); ?>',
            Hrs: '<?php echo e(__('Hrs')); ?>',
            Min: '<?php echo e(__('Min')); ?>',
            Sec: '<?php echo e(__('Sec')); ?>',
        }
    </script>



    <!-- JavaScript (jQuery) libraries, plugins and custom scripts-->
    <script type="text/javascript" src="<?php echo e(asset('assets/front/js/plugins.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/back/js/plugin/bootstrap-notify/bootstrap-notify.min.js')); ?>">
    </script>
    <script type="text/javascript" src="<?php echo e(asset('assets/front/js/scripts.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/front/js/lazy.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/front/js/lazy.plugin.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/front/js/myscript.js')); ?>"></script>

    <?php echo $__env->yieldContent('script'); ?>
    <?php echo $__env->yieldContent('scripts'); ?>
    
    <?php if($setting->is_facebook_messenger == '1'): ?>
        <!-- Messenger Chat Plugin Code -->
        <div id="fb-root"></div>

        <!-- Your Chat Plugin code -->
        <div id="fb-customer-chat" class="fb-customerchat">
        </div>

        <script>
            var chatbox = document.getElementById('fb-customer-chat');
            chatbox.setAttribute("page_id", "<?php echo e($setting->facebook_messenger); ?>");
            chatbox.setAttribute("attribution", "biz_inbox");
            window.fbAsyncInit = function() {
                FB.init({
                    xfbml: true,
                    version: 'v11.0'
                });
            };

            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s);
                js.id = id;
                js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
    <?php endif; ?>



    <script type="text/javascript">
        let mainurl = '<?php echo e(route('front.index')); ?>';

        let view_extra_index = 0;
        // Notifications
        function SuccessNotification(title) {
            $.notify({
                title: ` <strong>${title}</strong>`,
                message: '',
                icon: 'fas fa-check-circle'
            }, {
                element: 'body',
                position: null,
                type: "success",
                allow_dismiss: true,
                newest_on_top: false,
                showProgressbar: false,
                placement: {
                    from: "top",
                    align: "right"
                },
                offset: 20,
                spacing: 10,
                z_index: 1031,
                delay: 5000,
                timer: 1000,
                url_target: '_blank',
                mouse_over: null,
                animate: {
                    enter: 'animated fadeInDown',
                    exit: 'animated fadeOutUp'
                },
                onShow: null,
                onShown: null,
                onClose: null,
                onClosed: null,
                icon_type: 'class'
            });
        }

        function DangerNotification(title) {
            $.notify({
                // options
                title: ` <strong>${title}</strong>`,
                message: '',
                icon: 'fas fa-exclamation-triangle'
            }, {
                // settings
                element: 'body',
                position: null,
                type: "danger",
                allow_dismiss: true,
                newest_on_top: false,
                showProgressbar: false,
                placement: {
                    from: "top",
                    align: "right"
                },
                offset: 20,
                spacing: 10,
                z_index: 1031,
                delay: 5000,
                timer: 1000,
                url_target: '_blank',
                mouse_over: null,
                animate: {
                    enter: 'animated fadeInDown',
                    exit: 'animated fadeOutUp'
                },
                onShow: null,
                onShown: null,
                onClose: null,
                onClosed: null,
                icon_type: 'class'
            });
        }
        // Notifications Ends
    </script>

    <?php if(Session::has('error')): ?>
        <script>
            $(document).ready(function() {
                DangerNotification('<?php echo e(Session::get('error')); ?>')
            })
        </script>
    <?php endif; ?>
    <?php if(Session::has('success')): ?>
        <script>
            $(document).ready(function() {
                SuccessNotification('<?php echo e(Session::get('success')); ?>');
            })
        </script>
    <?php endif; ?>

</body>

</html>
<?php /**PATH I:\xampp8212\htdocs\sscrackers\core\resources\views/master/front.blade.php ENDPATH**/ ?>