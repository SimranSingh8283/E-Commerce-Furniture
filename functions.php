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


add_filter('query_vars', function ($vars) {
    $vars[] = 'collection';
    return $vars;
});

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


add_action('wp_ajax_load_product_collection', 'load_product_collection');
add_action('wp_ajax_nopriv_load_product_collection', 'load_product_collection');

function load_product_collection() {
    $collection = sanitize_text_field($_POST['collection'] ?? 'best_selling');

    ob_start();
    get_template_part(
        'template-parts/products/collection',
        null,
        array('collection' => $collection)
    );
    wp_send_json_success(ob_get_clean());
}


add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {

    ob_start();

    $count = WC()->cart->get_cart_contents_count();
    $tooltip = $count === 0
        ? 'Your cart is empty'
        : ($count === 1 ? '1 item in your cart' : "$count items in your cart");
    ?>

    <div class="header-cart-badge">
        <div class="Badge-root" data-value="<?php echo esc_attr($count); ?>">
            <a href="<?php echo esc_url(wc_get_cart_url()); ?>"
               class="Button-root Button-icon Button-shop cart-button"
               data-tooltip="<?php echo esc_attr($tooltip); ?>">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/media/shopping-cart.svg" alt="">
            </a>
        </div>
    </div>

    <?php
    $fragments['.header-cart-badge'] = ob_get_clean();

    return $fragments;
});


add_action('wp_enqueue_scripts', function() {
    if (function_exists('is_woocommerce')) {
        wp_enqueue_script('wc-cart-fragments');
    }
});