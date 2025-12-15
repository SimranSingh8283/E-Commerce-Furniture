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

                    <?php
                    $menu_arg = array('menu' => 'Header Menu', 'menu_class' => 'Navbar-nav', 'menu_id' => 'Navbar-nav', 'container' => 'ul', 'container_class' => '', 'container_id' => '', 'add_li_class' => 'Navbar-item', 'add_a_class' => 'Navbar-link');

                    wp_nav_menu($menu_arg);
                    ?>

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

                        <?php $count = WC()->cart->get_cart_contents_count(); ?>
                        <?php
                        $count = WC()->cart->get_cart_contents_count();

                        if ($count == 0) {
                            $tooltip_text = "Your cart is empty";
                        } elseif ($count == 1) {
                            $tooltip_text = "1 item in your cart";
                        } else {
                            $tooltip_text = "$count items in your cart";
                        }
                        ?>

                        <div class="Badge-root" data-value="<?php echo $count; ?>">
                            <a data-tooltip="<?php echo esc_attr($tooltip_text); ?>" href="<?php echo wc_get_cart_url(); ?>"
                                class="Button-root Button-icon Button-shop cart-button">
                                <img src="<?= $template_uri ?>/assets/media/shopping-cart.svg" alt="">
                            </a>
                        </div>

                        <Button class="Button-root Button-icon Button-shop Button-menu">
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

                    <form role="search" method="get" class="HeaderSearch-form"
                        action="<?php echo esc_url(home_url('/')); ?>">

                        <select class="HeaderSearch-select" id="productCatSelect">
                            <option value="">All Categories</option>

                            <?php foreach ($product_categories as $cat): ?>
                                <option value="<?php echo esc_attr($cat->slug); ?>" <?php selected($_GET['product_cat'] ?? '', $cat->slug); ?>>
                                    <?php echo esc_html($cat->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <input type="search" class="HeaderSearch-input" placeholder="Search..." name="s"
                            value="<?php echo esc_attr(get_search_query()); ?>" />

                        <input type="hidden" name="post_type" value="product">

                        <button data-tooltip="Search" data-position="bottom" type="submit"
                            class="HeaderSearch-button Button-root Button-icon">
                            <img src="<?= $template_uri ?>/assets/media/search.svg" alt="Search">
                        </button>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const form = document.querySelector('.HeaderSearch-form');
                            const select = document.getElementById('productCatSelect');

                            form.addEventListener('submit', function () {
                                if (select.value) {
                                    select.setAttribute('name', 'product_cat');
                                } else {
                                    select.removeAttribute('name');
                                }
                            });
                        });
                    </script>

                </div>

            </div>
        </header>

        <div id="Drawer-sidebar" data-direction="right" data-timeout="1000" data-lenis-prevent
            class="Drawer-root Drawer-sidebar" style="display: none;">
            <div class="Drawer-container">
                <div class="Drawer-header">
                    <nav class="Navbar-root">
                        <a href="<?= $site_url; ?>" class="Navbar-brand">
                            <img src="<?= $logo ?>" alt="">
                        </a>

                        <button id="Button-close-sidebar" data-drawer-close class="Button-root Button-icon Button-light">
                            <i class="fa fa-close"></i>
                        </button>
                    </nav>
                </div>

                <div class="Drawer-body">
                    <?php
                    $menu_arg = array('menu' => 'Sidebar Menu', 'menu_class' => 'Sidebar-nav', 'menu_id' => 'Sidebar-nav', 'container' => 'ul', 'container_class' => '', 'container_id' => '', 'add_li_class' => 'Sidebar-item', 'add_a_class' => 'Sidebar-link');

                    wp_nav_menu($menu_arg);
                    ?>
                </div>

                <div class="Drawer-footer">
                    <div class="border"></div>
                    <span>Follow Us:</span>

                    <?php if (!empty($insta) || !empty($fb)) { ?>
                        <ul class="Social-root">
                            <?php if (!empty($insta)) { ?>
                                <li class="Social-item" data-aos="zoom-in">
                                    <a class="Social-link Button-root Button-icon" target="_blank" href="<?= $insta; ?>">
                                        <i class="fa-brands fa-instagram"></i>
                                    </a>
                                </li>
                            <?php } ?>

                            <?php if (!empty($fb)) { ?>
                                <li class="Social-item" data-aos="zoom-in">
                                    <a class="Social-link Button-root Button-icon" target="_blank" href="<?= $fb; ?>">
                                        <i class="fa-brands fa-facebook-f"></i>
                                    </a>
                                </li>
                            <?php } ?>

                            <?php if (!empty($li)) { ?>
                                <li class="Social-item" data-aos="zoom-in">
                                    <a class="Social-link Button-root Button-icon" target="_blank" href="<?= $li; ?>">
                                        <i class="fa-brands fa-linkedin"></i>
                                    </a>
                                </li>
                            <?php } ?>

                            <?php if (!empty($yt)) { ?>
                                <li class="Social-item">
                                    <a class="Social-link Button-root Button-icon" target="_blank" href="<?= $yt; ?>">
                                        <i class="fa-brands fa-youtube"></i>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>

            </div>
        </div>

        <main id="Main-root" class="Main-root" aria-label="Main part of the Website">
        <?php endif; ?>