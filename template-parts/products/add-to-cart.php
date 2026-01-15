<?php
/**
 * Product Add to Cart / View Cart Button
 */

$product = $args['product'] ?? null;

if (!$product || !is_a($product, 'WC_Product')) {
    return;
}

$product_id = $product->get_id();
$cart_url = wc_get_cart_url();

$in_cart = false;
if (WC()->cart) {
    foreach (WC()->cart->get_cart() as $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            $in_cart = true;
            break;
        }
    }
}

if ($in_cart) {
    $button_text = 'View Cart';
    $button_icon = 'mdi:cart';
    $button_url = $cart_url;
    $classes = 'Button-root Button-primary Product-addToCart Button-cart-added';
} else {
    $button_text = $product->add_to_cart_text();
    $button_icon = 'mdi:cart-plus';
    $button_url = $product->add_to_cart_url();
    $classes = implode(' ', array_filter([
        'Button-root',
        'Button-primary',
        'Product-addToCart',
        'ajax_add_to_cart',
        'add_to_cart_button',
        $product->is_in_stock() ? '' : 'disabled'
    ]));
}
?>

<a href="<?php echo esc_url($button_url); ?>" data-quantity="1" data-product_id="<?php echo esc_attr($product_id); ?>"
    data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" data-variant="contained"
    class="Button-icon-start <?php echo esc_attr($classes); ?>" rel="nofollow">
    <span class="Button-icon">
        <iconify-icon icon="<?php echo esc_attr($button_icon); ?>"></iconify-icon>
    </span>
    <span class="Button-text"><?php echo esc_html($button_text); ?></span>
</a>