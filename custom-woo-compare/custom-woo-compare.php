<?php
/*
Plugin Name: Custom WooCommerce Compare
Description: Custom product comparison plugin for WooCommerce
Version: 1.0
Author: Gsptechnologies
Author URI: https://gsptechnologies.com/
*/


if (!defined('ABSPATH'))
    exit;

/* -----------------------------
  ENQUEUE SCRIPTS
----------------------------- */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'custom-compare-js',
        plugin_dir_url(__FILE__) . 'assets/compare.js',
        ['jquery'],
        null,
        true
    );

    wp_localize_script('custom-compare-js', 'compareData', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);

    wp_enqueue_style(
        'custom-compare-css',
        plugin_dir_url(__FILE__) . 'assets/compare.css'
    );
});

/* -----------------------------
  ADD BUTTON ON PRODUCT PAGE
----------------------------- */
add_action('woocommerce_single_product_summary', function () {
    global $product;
    echo '<button class="custom-compare-btn" data-id="' . esc_attr($product->get_id()) . '">
            🔁 Add to Compare
          </button>';
}, 35);

/* -----------------------------
  AJAX: ADD TO COMPARE
----------------------------- */
add_action('wp_ajax_add_to_compare', 'add_to_compare');
add_action('wp_ajax_nopriv_add_to_compare', 'add_to_compare');

function add_to_compare()
{

    $product_id = intval($_POST['product_id']);
    $meta = $_POST['selected_meta'] ?? [];

    if ($product_id <= 0) {
        wp_send_json_error('Invalid product');
    }

    // Ensure meta is array
    if (!is_array($meta)) {
        $meta = [];
    }

    // Clean meta values
    $clean_meta = [
        'finish_color' => sanitize_text_field($meta['finish_color'] ?? ''),
        'base_material' => sanitize_text_field($meta['base_material'] ?? ''),
        'wood_color' => sanitize_text_field($meta['wood_color'] ?? ''),
    ];

    // Load existing compare cookie
    $compare = [];

    if (!empty($_COOKIE['compare_products'])) {
        $compare = json_decode(stripslashes($_COOKIE['compare_products']), true);
        if (!is_array($compare)) {
            $compare = [];
        }
    }

    // Save / overwrite product with selected meta
    $compare[$product_id] = $clean_meta;

    // Save cookie
    setcookie(
        'compare_products',
        wp_json_encode($compare),
        time() + DAY_IN_SECONDS,
        '/',
        $_SERVER['HTTP_HOST'],
        false
    );

    wp_send_json_success($compare);
}
/* -----------------------------
  COMPARE PAGE SHORTCODE
----------------------------- */

add_shortcode('custom_compare_table', function () {

    $compare = !empty($_COOKIE['compare_products'])
        ? json_decode(stripslashes($_COOKIE['compare_products']), true)
        : [];

    if (!is_array($compare)) {
        $compare = [];
    }

    $items = [];
    foreach ($compare as $pid => $meta) {
        $product = wc_get_product((int) $pid);
        if ($product instanceof WC_Product) {
            $items[$pid] = [
                'product' => $product
            ];
        }
    }

    ob_start();

    $min_cells = 4;
    $product_items = array_values($items);

    while (count($product_items) < $min_cells - 1) {
        $product_items[] = null;
    }

    $product_items[] = 'add_cell';

    /**
     * ----------------------------------------
     * BUILD ROWS DYNAMICALLY (SHIPPING IGNORED)
     * ----------------------------------------
     */
    $rows = [];

    foreach ($items as $item) {
        $product_data = ThemeFunctions::get_product_attribute_data(
            $item['product']->get_id()
        );

        foreach ($product_data as $tab_slug => $tab) {

            if ($tab_slug === 'shipping') {
                continue;
            }

            foreach ($tab['attributes'] as $attr) {
                $rows[$attr['label']] = true;
            }
        }
    }

    $rows = array_keys($rows);

    /**
     * ----------------------------------------
     * Helper: attribute label → full options
     * ----------------------------------------
     */
    function compare_get_attr_values($product_id)
    {
        $data = ThemeFunctions::get_product_attribute_data($product_id);
        $map = [];

        foreach ($data as $tab_slug => $tab) {

            if ($tab_slug === 'shipping') {
                continue;
            }

            foreach ($tab['attributes'] as $attr) {
                $map[$attr['label']] = $attr['options'];
            }
        }

        return $map;
    }
    ?>

    <div class="Block-root Block-compare compare-wrapper" data-lenis-prevent>
        <div class="Container-root">
            <div class="Compare-grid" style="--_total-cells: <?php echo count($product_items) + 1; ?>;">
                <div class="Compare-row">

                    <!-- LEFT LABEL COLUMN -->
                    <div class="Compare-cell">
                        <div class="Compare-head" style="justify-content: space-between;">
                            <div class="count">
                                <strong class="Compare-count"><?php echo count($items); ?></strong>
                                <span>Products To Compare</span>
                            </div>

                            <div class="Compare-navigation">
                                <button class="Compare-scroll-btn Button-root Button-icon left">
                                    <iconify-icon icon="line-md:chevron-left"></iconify-icon>
                                </button>
                                <button class="Compare-scroll-btn Button-root Button-icon right">
                                    <iconify-icon icon="line-md:chevron-right"></iconify-icon>
                                </button>
                            </div>
                        </div>

                        <?php foreach ($rows as $row_label): ?>
                            <div class="Compare-item">
                                <strong><?php echo esc_html($row_label); ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- PRODUCT COLUMNS -->
                    <?php foreach ($product_items as $item): ?>
                        <div class="Compare-cell">
                            <div class="Compare-head">

                                <?php if ($item === 'add_cell' || $item === null): ?>
                                    <div class="Compare-search">
                                        <button class="compare-search" data-current="0">
                                            Search to replace...
                                        </button>
                                    </div>
                                    <div class="Compare-product-info">
                                        <div>-</div>
                                        <div>-</div>
                                    </div>
                                    <button class="Compare-thumb add-more" id="compare-add">+</button>
                                    <div class="Compare-ratings">
                                        <div>-</div>
                                        <div>-</div>
                                        <div>-</div>
                                    </div>
                                <?php else: ?>
                                    <div class="Compare-search">
                                        <button class="compare-search"
                                            data-current="<?php echo esc_attr($item['product']->get_id()); ?>">
                                            Search to replace...
                                        </button>
                                    </div>
                                    <div class="Compare-product-info">
                                        <a href="<?php echo esc_url($item['product']->get_permalink()); ?>">
                                            <h4><?php echo esc_html($item['product']->get_name()); ?></h4>
                                        </a>
                                        <p><?php echo wc_price($item['product']->get_price()); ?></p>
                                    </div>
                                    <div class="Compare-thumb">
                                        <a href="<?php echo esc_url($item['product']->get_permalink()); ?>">
                                            <?php echo $item['product']->get_image('medium'); ?>
                                        </a>
                                        <span class="compare-remove" data-id="<?php echo esc_attr($item['product']->get_id()); ?>">
                                            <iconify-icon icon="tabler:trash"></iconify-icon>
                                        </span>
                                    </div>
                                    <div class="Compare-ratings">
                                        <?php echo wc_get_rating_html($item['product']->get_average_rating()); ?>
                                        <div><?php echo $item['product']->get_review_count(); ?> reviews</div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php
                            $attr_map = is_array($item)
                                ? compare_get_attr_values($item['product']->get_id())
                                : [];
                            ?>

                            <?php foreach ($rows as $row_label): ?>
                                <div class="Compare-item">
                                    <?php if (!empty($attr_map[$row_label])): ?>
                                        <?php foreach ($attr_map[$row_label] as $opt): ?>
                                            <?php
                                            $type = is_array($opt['type']) ? ($opt['type']['value'] ?? '') : $opt['type'];
                                            ?>

                                            <?php if ($type === 'color' && !empty($opt['color'])): ?>

                                                <span data-tooltip="<?php echo esc_attr($opt['name']); ?>"
                                                    class="Compare-swatch Compare-swatch--color" title="<?php echo esc_attr($opt['name']); ?>"
                                                    style="display: inline-block; width: 1.5rem; height: 1.5rem; border-radius: 50%; background-color: <?php echo esc_attr($opt['color']); ?>">
                                                </span>

                                            <?php elseif ($type === 'image' && !empty($opt['image'])): ?>

                                                <?php
                                                $img_url = is_numeric($opt['image'])
                                                    ? wp_get_attachment_image_url($opt['image'], 'thumbnail')
                                                    : $opt['image'];
                                                ?>

                                                <span data-tooltip="<?php echo esc_attr($opt['name']); ?>"
                                                    class="Compare-swatch Compare-swatch--image" title="<?php echo esc_attr($opt['name']); ?>">
                                                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($opt['name']); ?>">
                                                </span>

                                            <?php else: ?>

                                                <span class="Compare-swatch Compare-swatch--text" title="<?php echo esc_attr($opt['name']); ?>">
                                                    <?php echo esc_html($opt['name']); ?>
                                                </span>

                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    </div>


    <div id="compare-modal">
        <div class="compare-modal-content">
            <div class="compare-modal-header"> <input type="text" id="compare-modal-search"
                    placeholder="Search product..." /> <button id="close-modal" class="Button-root Button-icon">
                    <iconify-icon icon="line-md:close"></iconify-icon> </button> </div>
            <div id="compare-modal-results"></div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const compareWrapper = document.querySelector('.compare-wrapper');
            const grid = compareWrapper.querySelectorAll('.Compare-row');
            const btnLeft = compareWrapper.querySelectorAll('.Compare-scroll-btn.left');
            const btnRight = compareWrapper.querySelectorAll('.Compare-scroll-btn.right');

            btnLeft.forEach(btn => {
                btn.addEventListener('click', () => {
                    const firstCell = btn.closest(".Compare-row").querySelector('.Compare-cell');
                    if (!firstCell) return;

                    btn.closest(".Compare-row").scrollBy({
                        left: -firstCell.offsetWidth + 24,
                        behavior: 'smooth'
                    });
                });
            })

            btnRight.forEach(btn => {
                btn.addEventListener('click', () => {
                    const firstCell = btn.closest(".Compare-row").querySelector('.Compare-cell');
                    if (!firstCell) return;
                    btn.closest(".Compare-row").scrollBy({
                        left: firstCell.offsetWidth + 24,
                        behavior: 'smooth'
                    });
                });
            })
        });
    </script>


    <?php
    return ob_get_clean();
});




/* =============================
   REMOVE PRODUCT
============================= */
add_action('wp_ajax_remove_from_compare', 'remove_from_compare');
add_action('wp_ajax_nopriv_remove_from_compare', 'remove_from_compare');

function remove_from_compare()
{
    $pid = intval($_POST['product_id']);
    $compare = json_decode(stripslashes($_COOKIE['compare_products']), true);
    unset($compare[$pid]);

    setcookie('compare_products', wp_json_encode($compare), time() + DAY_IN_SECONDS, '/', $_SERVER['HTTP_HOST']);
    wp_send_json_success();
}

/* =============================
   SEARCH PRODUCTS
============================= */
add_action('wp_ajax_compare_search', 'compare_search');
add_action('wp_ajax_nopriv_compare_search', 'compare_search');

function compare_search()
{

    $q = sanitize_text_field($_POST['q'] ?? '');

    $query = new WP_Query([
        'post_type' => 'product',
        'posts_per_page' => 5,
        's' => $q,
    ]);

    $html = '';

    while ($query->have_posts()) {
        $query->the_post();
        $pid = get_the_ID();

        $html .= '<div class="compare-result" data-lenis-prevent data-id="' . esc_attr($pid) . '">';
        $html .= get_the_post_thumbnail($pid, 'thumbnail');
        $html .= '<div class="info">';
        $html .= '<h4>' . get_the_title() . '</h4>';

        // 🔥 META UI HERE
        $html .= render_product_attributes($pid);

        $html .= '<button class="Button-root Button-primary compare-select-btn" data-variant="contained" data-id="' . esc_attr($pid) . '">
                    Add / Replace
                  </button>';

        $html .= '</div>';
        $html .= '</div>';
    }

    wp_reset_postdata();

    wp_send_json_success($html);
}

function render_product_attributes($product, $show_only = [])
{
    // Make sure WooCommerce exists (important in plugins)
    if (!function_exists('wc_get_product')) {
        return '';
    }

    if (is_int($product)) {
        $product = wc_get_product($product);
    }

    if (!$product instanceof WC_Product) {
        return '';
    }

    $attributes = $product->get_attributes();
    if (empty($attributes)) {
        return '';
    }

    ob_start();

    echo '<div class="Attribute-root Attribute-product">';
    echo '<div class="Attribute-options">';

    foreach ($attributes as $attribute) {

        // Only global attributes
        if (!$attribute->is_taxonomy()) {
            continue;
        }

        $taxonomy = $attribute->get_name(); // pa_finish_color
        $slug     = str_replace('pa_', '', $taxonomy);

        if (!empty($show_only) && !in_array($slug, $show_only, true)) {
            continue;
        }

        $label = wc_attribute_label($taxonomy);

        $terms = wc_get_product_terms(
            $product->get_id(),
            $taxonomy,
            ['fields' => 'all']
        );

        if (empty($terms)) {
            continue;
        }

        echo '<div class="Attribute-group" data-attribute="' . esc_attr($slug) . '">';
        echo '<strong>' . esc_html($label) . '</strong>';
        echo '<div class="Swatch-list">';

        foreach ($terms as $term) {

            $color    = get_term_meta($term->term_id, 'attr_color', true);
            $image_id = get_term_meta($term->term_id, 'attr_image', true);
            $image    = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : null;

            echo '<div class="Swatch-root"
                data-tooltip="' . esc_attr($label . ': ' . $term->name) . '">';

            if ($image) {
                echo '<img class="Swatch-image" src="' . esc_url($image) . '" alt="">';
            } elseif ($color) {
                echo '<span class="Swatch-color" style="background:' . esc_attr($color) . '"></span>';
            } else {
                echo '<span class="Swatch-text">' . esc_html($term->name) . '</span>';
            }

            echo '</div>';
        }

        echo '</div></div>';
    }

    echo '</div></div>';

    return ob_get_clean();
}

function get_default_compare_meta($product_id)
{

    $data = get_post_meta($product_id, '_custom_finish_options', true);
    if (!is_array($data))
        return [];

    $meta = [];

    foreach (['finish_color', 'base_material', 'wood_color'] as $key) {
        if (!empty($data[$key][0]['label'])) {
            $meta[$key] = $data[$key][0]['label']; // FIRST option = default
        }
    }

    return $meta;
}


/* =============================
   REPLACE PRODUCT
============================= */
add_action('wp_ajax_replace_compare', 'replace_compare');
add_action('wp_ajax_nopriv_replace_compare', 'replace_compare');



function replace_compare()
{

    $old = intval($_POST['old_product_id'] ?? 0);
    $new = intval($_POST['new_product_id'] ?? 0);
    $meta = $_POST['selected_meta'] ?? [];

    if ($new <= 0) {
        wp_send_json_error('Invalid product');
    }

    // Load existing compare cookie
    $compare = [];

    if (!empty($_COOKIE['compare_products'])) {
        $compare = json_decode(stripslashes($_COOKIE['compare_products']), true);
        if (!is_array($compare)) {
            $compare = [];
        }
    }

    // 🔥 REMOVE OLD PRODUCT
    if ($old > 0 && isset($compare[$old])) {
        unset($compare[$old]);
    }

    // 🔥 ADD NEW PRODUCT WITH META
    $clean_meta = [];

    if (is_array($meta)) {
        foreach ($meta as $key => $value) {
            $clean_meta[sanitize_key($key)] = sanitize_text_field($value);
        }
    }

    $compare[$new] = $clean_meta;

    // Save cookie
    setcookie(
        'compare_products',
        wp_json_encode($compare),
        time() + DAY_IN_SECONDS,
        '/',
        $_SERVER['HTTP_HOST'],
        false
    );

    wp_send_json_success($compare);
}