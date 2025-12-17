<?php

/**
 * The header.
 *
 * This is the template that displays all of the <head> section and everything up until main.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_four
 * @since Twenty Twenty-four 1.0
 */
$custom_logo_id = get_theme_mod('custom_logo');
$logo = wp_get_attachment_image_src($custom_logo_id, 'full')[0];
$logo_alt = get_post_meta($custom_logo_id, '_wp_attachment_image_alt', true);

$insta = get_theme_mod('instagram_link');
$fb = get_theme_mod('facebook_link');
$li = get_theme_mod('linkedin_link');
$yt = get_theme_mod('youtube_link');

$site_url = site_url();
$template_uri = get_template_directory_uri();


$menu_arg = array('menu' => 'Header Menu', 'menu_class' => 'Navbar-nav', 'menu_id' => 'Navbar-nav', 'container' => 'ul', 'container_class' => '', 'container_id' => '', 'add_li_class' => 'Navbar-item', 'add_a_class' => 'Navbar-link');
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php wp_get_document_title(); ?></title>

    <?php wp_head(); ?>

    <script src="https://unpkg.com/lenis@1.0.45/dist/lenis.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.0/iconify-icon.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <?php if (!is_404()): ?>
        <header id="Header-root" class="Header-root" data-aos="slide-down"
            aria-label="Main Navigation with contact information">
            <div class="Container-root">

                <nav class="Navbar-root">
                    <a href="<?= $site_url; ?>" class="Navbar-brand">
                        <img src="<?= $logo ?>" alt="">
                    </a>

                    <?php wp_nav_menu($menu_arg); ?>

                    <div class="Navbar-actions">
                        <a href="#" class="Button-root Button-primary" data-variant="contained">Sign Up</a>

                        <a <?php if (!is_account_page()): ?> href="<?php echo wc_get_page_permalink('myaccount'); ?>" <?php endif; ?> class="Button-root Button-icon Button-user">
                            <img src="<?= $template_uri ?>/assets/media/profile.svg" alt="">
                        </a>

                        <div class="Badge-root" data-value="0">
                            <a href="<?php echo esc_url(home_url('/wishlist/')); ?>"
                                data-tooltip="<?php echo esc_attr($wishlist_tooltip); ?>"
                                class="Button-root Button-icon Button-wishlist">
                                <img src="<?= $template_uri ?>/assets/media/wishlist.svg" alt="">
                            </a>
                        </div>

                        <script>
                            document.addEventListener("DOMContentLoaded", function () {

                                function syncWishlistBadge() {
                                    const menuSpan = document.querySelector('.woosw-menu-item-inner');
                                    const badge = document.querySelector('.Button-wishlist');
                                    if (!menuSpan || !badge) return;

                                    const count = parseInt(menuSpan.dataset.count || 0, 10);

                                    const badgeWrapper = badge.closest('.Badge-root');
                                    if (badgeWrapper && badgeWrapper.getAttribute('data-value') != count) {
                                        badgeWrapper.setAttribute('data-value', count);
                                    }

                                    let tooltip = "Your wishlist is empty";
                                    if (count === 1) tooltip = "1 item in wishlist";
                                    else if (count > 1) tooltip = `${count} items in wishlist`;

                                    if (badge.getAttribute('data-tooltip') !== tooltip) {
                                        badge.setAttribute('data-tooltip', tooltip);
                                    }
                                }

                                const observer = new MutationObserver((mutations) => {
                                    for (const mutation of mutations) {
                                        if (mutation.type === 'childList') {
                                            if (
                                                document.querySelector('.woosw-menu-item-inner')
                                            ) {
                                                syncWishlistBadge();
                                                break;
                                            }
                                        }
                                    }
                                });

                                observer.observe(document.body, {
                                    childList: true,
                                    subtree: true
                                });

                                syncWishlistBadge();

                                document.addEventListener('woosw_change_count', syncWishlistBadge);
                                document.addEventListener('woosw_update_fragments', syncWishlistBadge);
                                document.addEventListener('woosw_loaded', syncWishlistBadge);
                            });
                        </script>
                        
                        <div class="cart-badge-wrapper">
                            <?php $count = WC()->cart->get_cart_contents_count(); ?>
                            <?php
                            $tooltip_text = $count === 0 ? 'Your cart is empty' : ($count === 1 ? '1 item in your cart' : "$count items in your cart");
                            ?>
                            <div class="Badge-root" data-value="<?php echo $count; ?>">
                                <a data-tooltip="<?php echo esc_attr($tooltip_text); ?>" href="<?php echo wc_get_cart_url(); ?>"
                                class="Button-root Button-icon Button-shop cart-button">
                                    <img src="<?= $template_uri ?>/assets/media/shopping-cart.svg" alt="">
                                </a>
                            </div>
                        </div>

                        <Button class="Button-root Button-icon Button-shop Button-menu" data-drawer="#Drawer-menu">
                            <iconify-icon icon="material-symbols:menu-rounded"></iconify-icon>
                        </Button>

                    </div>
                </nav>

                <div class="Header-search">
                    <?php
                    $product_categories = get_terms([
                        'taxonomy' => 'product_cat',
                        'hide_empty' => true,
                    ]);
                    ?>

                    <form role="search" method="get" class="HeaderSearch-form" action="<?= esc_url(home_url('/')); ?>">
                        <select class="HeaderSearch-select" name="product_cat">
                            <option value="">All Categories</option>
                            <?php foreach ($product_categories as $cat): ?>
                                <option value="<?= esc_attr($cat->slug); ?>" <?= selected($_GET['product_cat'] ?? '', $cat->slug) ?>>
                                    <?= esc_html($cat->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <input type="search" name="s" class="HeaderSearch-input" placeholder="Search..."
                            value="<?= esc_attr(get_search_query()); ?>">

                        <input type="hidden" name="post_type" value="product">

                        <button type="submit" class="HeaderSearch-button Button-root Button-icon">
                            <img src="<?= $template_uri ?>/assets/media/search.svg" alt="Search">
                        </button>
                    </form>

                </div>

            </div>
        </header>

        <div id="Drawer-menu" data-direction="right" data-timeout="1000" data-lenis-prevent class="Drawer-root Drawer-menu"
            style="display: none;">
            <div class="Drawer-container">
                <div class="Drawer-header">
                    <nav class="Navbar-root">
                        <a href="<?= $site_url; ?>" class="Navbar-brand">
                            <img src="<?= $site_url ?>/wp-content/uploads/2025/12/Group-1.png" alt="">
                        </a>

                        <button id="Button-close-menu" data-drawer-close class="Button-root Button-icon Button-light">
                            <iconify-icon icon="mdi:close"></iconify-icon>
                        </button>
                    </nav>
                </div>

                <div class="Drawer-body">
                    <?php wp_nav_menu($menu_arg); ?>
                </div>
            </div>
        </div>

        <main id="Main-root" class="Main-root" aria-label="Main part of the Website">
        <?php endif; ?>