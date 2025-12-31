<?php
/**
 * Header Actions: Login/Logout, Profile, Wishlist, Cart
 * Variables passed: $template_uri
 */

$template_uri = $args['template_uri'] ?? '';
?>

<div class="Navbar-actions">

    <?php if (!is_user_logged_in()) : ?>
        <a href="<?= esc_url(add_query_arg('action','register', get_permalink(wc_get_page_id('myaccount')))); ?>" 
           class="Button-root Button-primary" data-variant="contained">Sign Up</a>
    <?php else : ?>
        <a href="<?= esc_url(wp_logout_url(get_permalink())); ?>" 
           class="Button-root Button-primary Button-icon-start" data-variant="contained">
            <iconify-icon icon="line-md:logout"></iconify-icon> Logout
        </a>
    <?php endif; ?>

    <a data-tooltip="My Account" <?php if (!is_account_page()): ?> href="<?= wc_get_page_permalink('myaccount'); ?>" <?php endif; ?> 
       class="Button-root Button-icon Button-user">
        <img src="<?= $template_uri ?>/assets/media/profile.svg" alt="">
    </a>

    <div class="Badge-root" data-value="0">
        <a href="<?= esc_url(home_url('/wishlist/')); ?>" class="Button-root Button-icon Button-wishlist">
            <img src="<?= $template_uri ?>/assets/media/wishlist.svg" alt="">
        </a>
    </div>

    <div class="cart-badge-wrapper header-cart-badge">
        <?php $count = WC()->cart->get_cart_contents_count(); ?>
        <div class="Badge-root" data-value="<?= esc_attr($count); ?>">
            <a href="<?= esc_url(wc_get_cart_url()); ?>" 
               class="Button-root Button-icon Button-shop cart-button">
                <img src="<?= esc_url($template_uri); ?>/assets/media/shopping-cart.svg" alt="Cart">
            </a>
        </div>
    </div>

    <button class="Button-root Button-icon Button-shop Button-menu" data-drawer="#Drawer-menu">
        <iconify-icon icon="material-symbols:menu-rounded"></iconify-icon>
    </button>

</div>

<?php get_template_part('template-parts/header/header', 'wishlist-script'); ?>