<?php
/**
 * Furniture Theme Functions
 *
 * @package Furniture
 */

define('THEME_SLUG', 'Furniture');
// modular function files
require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/widgets.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/post-types.php';
require_once get_template_directory() . '/inc/theme-functions.php';
require_once get_template_directory() . '/inc/shortcodes.php';
require_once get_template_directory() . '/inc/elementor-widgets/register-widgets.php';



add_filter('woocommerce_add_to_cart_fragments', function($fragments) {
    ob_start();
    $count = WC()->cart->get_cart_contents_count();
    $tooltip_text = $count === 0 ? 'Your cart is empty' : ($count === 1 ? '1 item in your cart' : "$count items in your cart");
    ?>
    <div class="cart-badge-wrapper">
        <div class="Badge-root" data-value="<?php echo $count; ?>">
            <a data-tooltip="<?php echo esc_attr($tooltip_text); ?>" href="<?php echo wc_get_cart_url(); ?>"
               class="Button-root Button-icon Button-shop cart-button">
                <img src="<?= get_template_directory_uri(); ?>/assets/media/shopping-cart.svg" alt="">
            </a>
        </div>
    </div>
    <?php
    $fragments['.cart-badge-wrapper'] = ob_get_clean();
    return $fragments;
});
