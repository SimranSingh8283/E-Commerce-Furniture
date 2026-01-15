<?php
/**
 * Plugin Name: Custom WooCommerce Wishlist
 * Description: Simple WooCommerce Wishlist like Amazon-style table layout
 * Version: 1.0
 * Author: Gsptechnologies
Author URI: https://gsptechnologies.com/
 */

if (!defined('ABSPATH'))
    exit;

/* ===========================
  ADD WISHLIST BUTTON
=========================== */

function cw_get_wishlist_url()
{
    $page = get_page_by_path('wishlist');
    return $page ? get_permalink($page->ID) : home_url('/');
}


add_action('woocommerce_after_add_to_cart_button', function () {
    if (!is_user_logged_in())
        return;

    global $product;
    echo '<button class="cw-wishlist-btn" data-id="' . esc_attr($product->get_id()) . '">❤ Add to Wishlist</button>';
});

/* ===========================
  AJAX: ADD / REMOVE
=========================== */
add_action('wp_ajax_cw_toggle_wishlist', 'cw_toggle_wishlist');
function cw_toggle_wishlist()
{
    if (!is_user_logged_in())
        wp_die();

    $product_id = intval($_POST['product_id']);
    $user_id = get_current_user_id();

    $wishlist = get_user_meta($user_id, '_cw_wishlist', true);
    if (!is_array($wishlist))
        $wishlist = [];

    $action = 'added';

    if (in_array($product_id, $wishlist)) {
        $wishlist = array_diff($wishlist, [$product_id]);
        $action = 'removed';
    } else {
        $wishlist[] = $product_id;
    }

    update_user_meta($user_id, '_cw_wishlist', $wishlist);

    do_action(
        'cw_wishlist_updated',
        $user_id,
        $wishlist,
        $product_id,
        $action
    );

    wp_send_json_success([
        'count' => count($wishlist),
        'action' => $action
    ]);
}

/* ===========================
  WISHLIST SHORTCODE
=========================== */
add_shortcode('cw_wishlist', function () {
    if (!is_user_logged_in())
        return '<p>Please login to view wishlist.</p>';

    $wishlist = get_user_meta(get_current_user_id(), '_cw_wishlist', true);
    if (empty($wishlist)) {
        $shop_url = wc_get_page_permalink('shop');

        return '
        <div class="Block-root">
            <div class="Container-root text-center">
                <div class="Block-heading">
                    <span aria-level="1" data-level="1">
                        No products in your wishlist.
                    </span>
                </div>

                <div class="Action-root">
                    <a href="' . esc_url($shop_url) . '"
                       class="Button-root Button-primary"
                       data-variant="contained">
                        Go to shop
                    </a>
                </div>
            </div>
        </div>';
    }


    ob_start(); ?>
    <table class="Table-root cw-wishlist-table">
        <thead>
            <tr>
                <th>Products</th>
                <th>Stock Status</th>
                <th>Price</th>
                <th style="text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($wishlist as $product_id):
                $product = wc_get_product($product_id);
                if (!$product)
                    continue;

                $in_cart = false;
                foreach (WC()->cart->get_cart() as $cart_item) {
                    if ($cart_item['product_id'] == $product_id) {
                        $in_cart = true;
                        break;
                    }
                }
                ?>
                <tr>
                    <td class="cw-product">
                        <?php echo $product->get_image('thumbnail'); ?>
                        <div>
                            <strong><?php echo esc_html($product->get_name()); ?></strong>
                        </div>
                    </td>
                    <td>
                        <?php echo $product->is_in_stock()
                            ? '<span class="in-stock">In Stock</span>'
                            : '<span class="out-stock">Out of Stock</span>'; ?>
                    </td>
                    <td><?php echo wp_kses_post(wc_price($product->get_price())) ?></td>
                    <td align="center" style="width: 20%;">
                        <div class="Action-group"
                            style="display: flex; gap: 0.5rem; align-items: center; max-width: max-content; justify-content: space-between;">
                            <?php if ($product->is_in_stock()): ?>
                                <a href="?add-to-cart=<?php echo $product_id; ?>" class="Button-root Button-primary"
                                    data-variant="contained" <?php echo $in_cart ? 'aria-disabled="true" disabled onclick="return false;"' : ''; ?>>
                                    Add to Cart
                                </a>
                            <?php else: ?>
                                <button class="button disabled">Add to Cart</button>
                            <?php endif; ?>
                            <button data-tooltip="Remove" class="Button-root Button-icon cw-remove"
                                data-id="<?php echo $product_id; ?>">
                                <iconify-icon style="font-size: 1.5rem;" icon="mynaui:trash"></iconify-icon>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="Table-root Table-cw-mobile cw-wishlist-table" style="display: none;">
        <thead>
            <tr>
                <th>Products</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($wishlist as $product_id):
                $product = wc_get_product($product_id);
                if (!$product)
                    continue;

                $in_cart = false;
                foreach (WC()->cart->get_cart() as $cart_item) {
                    if ($cart_item['product_id'] == $product_id) {
                        $in_cart = true;
                        break;
                    }
                }
                ?>
                <tr>
                    <td class="cw-td">
                        <div class="cw-product">
                            <div class="cw-thumb">
                                <?php echo $product->get_image('thumbnail'); ?>
                            </div>

                            <div class="cw-info">
                                <div class="title">
                                    <strong><?php echo esc_html($product->get_name()); ?></strong>
                                </div>
                                <div class="price">
                                    <?php echo $product->get_price_html(); ?>
                                </div>

                                <div class="Table-quantityWrapper quantityWrapper <?php echo $in_cart ? "disabled" : "" ?>">
                                    <button type="button" class="Button-root Button-icon qty-minus">
                                        <iconify-icon icon="akar-icons:minus"></iconify-icon>
                                    </button>
                                    <?php
                                    $min_quantity = $product->is_sold_individually() ? 1 : 1;
                                    $max_quantity = $product->is_sold_individually()
                                        ? 1
                                        : $product->get_max_purchase_quantity();

                                    woocommerce_quantity_input(
                                        array(
                                            'input_value' => 1,
                                            'min_value' => $min_quantity,
                                            'max_value' => $max_quantity,
                                            'product_name' => $product->get_name(),
                                        ),
                                        $product
                                    );
                                    ?>
                                    <button type="button" class="Button-root Button-icon qty-plus">
                                        <iconify-icon icon="akar-icons:plus"></iconify-icon>
                                    </button>
                                </div>
                            </div>
                            <button data-tooltip="Remove" class="Button-root Button-icon cw-remove"
                                data-id="<?php echo $product_id; ?>">
                                <iconify-icon style="font-size: 1.5rem;" icon="mynaui:trash"></iconify-icon>
                            </button>
                        </div>

                        <div class="Action-root">
                            <?php if ($product->is_in_stock()): ?>
                                <a href="?add-to-cart=<?php echo $product_id; ?>"
                                    class="Button-root Button-primary add_to_cart_button" data-variant="contained" <?php echo $in_cart ? 'aria-disabled="true" disabled onclick="return false;"' : ''; ?>>
                                    Add to Cart
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="?cw_add_all=1" class="Button-root Button-primary add-all" data-variant="contained">Add All to Cart</a>
    <?php
    return ob_get_clean();
});

/* ===========================
  ADD ALL TO CART
=========================== */
add_action('init', function () {
    if (!isset($_GET['cw_add_all']) || !is_user_logged_in())
        return;

    $wishlist = get_user_meta(get_current_user_id(), '_cw_wishlist', true);
    if (!$wishlist)
        return;

    foreach ($wishlist as $product_id) {
        WC()->cart->add_to_cart($product_id);
    }
});

/* ===========================
  SCRIPTS + CSS (SEPARATE FILES)
=========================== */
add_action('wp_enqueue_scripts', function () {

    /* JS */
    wp_enqueue_script(
        'cw-wishlist',
        plugin_dir_url(__FILE__) . 'assets/js/wishlist.js',
        ['jquery'],
        '1.0',
        true // footer
    );

    wp_localize_script('cw-wishlist', 'cwWishlist', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'wishlist_url' => cw_get_wishlist_url(),
        'shop_url' => wc_get_page_permalink('shop'),
        'login_url' => wp_login_url(get_permalink()),
        'is_logged_in' => is_user_logged_in(),
        'count' => is_user_logged_in()
            ? count((array) get_user_meta(get_current_user_id(), '_cw_wishlist', true))
            : 0
    ]);


    /* CSS */
    wp_enqueue_style(
        'cw-wishlist',
        plugin_dir_url(__FILE__) . 'assets/css/wishlist.css',
        [],
        '1.0'
    );

});
