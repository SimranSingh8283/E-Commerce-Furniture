<?php
/**
 * Drawer / Mobile Menu
 * Variables passed: $site_url
 */


$logo = $args['logo'] ?? '';
$logo_alt = $args['logo_alt'] ?? '';
$site_url = $args['site_url'] ?? '';

$menu_arg = [
    'menu' => 'Header Menu',
    'menu_class' => 'Navbar-nav',
    'menu_id' => 'Navbar-nav',
    'container' => 'ul',
    'add_li_class' => 'Navbar-item',
    'add_a_class' => 'Navbar-link'
];
?>

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

            <div class="Drawer-action" style="padding: 1.5rem;">
                <?php if (!is_user_logged_in()): ?>
                    <a style="width: 100%;" href="<?= esc_url(add_query_arg('action', 'register', get_permalink(wc_get_page_id('myaccount')))); ?>"
                        class="Button-root Button-primary" data-variant="contained">Sign Up</a>
                <?php else: ?>
                    <a style="width: 100%;" href="<?= esc_url(wp_logout_url(get_permalink())); ?>"
                        class="Button-root Button-primary Button-icon-start" data-variant="contained">
                        <iconify-icon icon="line-md:logout"></iconify-icon> Logout
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>