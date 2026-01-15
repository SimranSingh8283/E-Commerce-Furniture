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




/* ==============================
 * ICONIFY ICON FOR TERMS
 * ============================== */

function theme_term_iconify_add_field() {
?>
<div class="form-field term-iconify-wrap">
    <label>Icon</label>

    <p class="description">
        Search icons on
        <a href="https://icon-sets.iconify.design/" target="_blank" rel="noopener">
            Iconify
        </a>
        and copy the icon name (example:
        <code>mdi:home</code>)
    </p>

    <input type="text"
        name="term_icon"
        class="regular-text term-iconify-input"
        placeholder="mdi:home">

    <div class="term-iconify-preview" style="margin-top:6px;"></div>
</div>
<?php
}

function theme_term_iconify_edit_field( $term ) {
    $icon = get_term_meta( $term->term_id, 'term_icon', true );
?>
<tr class="form-field term-iconify-wrap">
<th><label>Icon</label></th>
<td>

<p class="description">
    Search icons on
    <a href="https://icon-sets.iconify.design/" target="_blank" rel="noopener">
        Iconify
    </a>
    and copy the icon name (example:
    <code>mdi:home</code>)
</p>

<input type="text"
    name="term_icon"
    class="regular-text term-iconify-input"
    value="<?php echo esc_attr($icon); ?>"
    placeholder="mdi:home">

<div class="term-iconify-preview" style="margin-top:6px;">
    <?php if ($icon): ?>
        <iconify-icon icon="<?php echo esc_attr($icon); ?>"></iconify-icon>
    <?php endif; ?>
</div>

</td>
</tr>
<?php
}

function theme_save_term_iconify( $term_id ) {
    if ( isset($_POST['term_icon']) ) {
        update_term_meta(
            $term_id,
            'term_icon',
            sanitize_text_field($_POST['term_icon'])
        );
    }
}

foreach ( ['category','post_tag'] as $tax ) {
    add_action("{$tax}_add_form_fields", 'theme_term_iconify_add_field');
    add_action("{$tax}_edit_form_fields", 'theme_term_iconify_edit_field');
    add_action("created_{$tax}", 'theme_save_term_iconify');
    add_action("edited_{$tax}", 'theme_save_term_iconify');
}


add_action('admin_footer', function () {
?>
<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

<script>
document.addEventListener('input', e => {
    if (!e.target.classList.contains('term-iconify-input')) return;

    const preview = e.target
        .closest('.term-iconify-wrap')
        .querySelector('.term-iconify-preview');

    const value = e.target.value.trim();

    preview.innerHTML = value
        ? `<iconify-icon icon="${value}"></iconify-icon>`
        : '';
});
</script>
<?php
});


function theme_term_iconify( $term_id ) {
    $icon = get_term_meta( $term_id, 'term_icon', true );

    if ( ! $icon ) return '';

    return '<iconify-icon icon="' . esc_attr($icon) . '"></iconify-icon>';
}




/* -------------------------------------------------
 * PRODUCT FAQs META BOX
 * ------------------------------------------------- */
add_action('add_meta_boxes', function () {
    add_meta_box(
        'product_faqs',
        'Product FAQs',
        'render_product_faqs_metabox',
        'product',
        'normal',
        'high'
    );
});


function render_product_faqs_metabox($post)
{
    $faqs = get_post_meta($post->ID, '_product_faqs', true);
    if (!is_array($faqs)) {
        $faqs = [];
    }

    wp_nonce_field('save_product_faqs', 'product_faqs_nonce');
    ?>

    <div id="product-faqs-wrapper">
        <?php foreach ($faqs as $index => $faq): ?>
            <div class="product-faq-card">
                <div class="faq-header">
                    <strong>FAQ</strong>
                    <button type="button" class="button-link delete-faq">✕</button>
                </div>

                <p>
                    <label>Question</label>
                    <input type="text"
                        name="product_faqs[<?php echo $index; ?>][question]"
                        value="<?php echo esc_attr($faq['question'] ?? ''); ?>"
                        class="widefat">
                </p>

                <p>
                    <label>Answer</label>
                    <textarea
                        name="product_faqs[<?php echo $index; ?>][answer]"
                        rows="3"
                        class="widefat"><?php echo esc_textarea($faq['answer'] ?? ''); ?></textarea>
                </p>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" style="width: 100%;" class="button button-primary" id="add-faq">
        + Add FAQ
    </button>

    <?php
}

add_action('save_post_product', function ($post_id) {

    if (!isset($_POST['product_faqs_nonce']) ||
        !wp_verify_nonce($_POST['product_faqs_nonce'], 'save_product_faqs')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $faqs = $_POST['product_faqs'] ?? [];

    $clean = [];

    foreach ($faqs as $faq) {
        if (empty($faq['question']) && empty($faq['answer'])) {
            continue;
        }

        $clean[] = [
            'question' => sanitize_text_field($faq['question']),
            'answer'   => sanitize_textarea_field($faq['answer']),
        ];
    }

    if (!empty($clean)) {
        update_post_meta($post_id, '_product_faqs', $clean);
    } else {
        delete_post_meta($post_id, '_product_faqs');
    }
});


add_action('admin_footer', function () {
    $screen = get_current_screen();
    if ($screen->post_type !== 'product') return;
    ?>

    <script>
        jQuery(function ($) {
            let index = $('#product-faqs-wrapper .product-faq-card').length;

            $('#add-faq').on('click', function () {
                $('#product-faqs-wrapper').append(`
                    <div class="product-faq-card">
                        <div class="faq-header">
                            <strong>FAQ</strong>
                            <button type="button" class="button-link delete-faq">✕</button>
                        </div>

                        <p>
                            <label>Question</label>
                            <input type="text"
                                name="product_faqs[${index}][question]"
                                class="widefat">
                        </p>

                        <p>
                            <label>Answer</label>
                            <textarea
                                name="product_faqs[${index}][answer]"
                                rows="3"
                                class="widefat"></textarea>
                        </p>
                    </div>
                `);
                index++;
            });

            $(document).on('click', '.delete-faq', function () {
                $(this).closest('.product-faq-card').remove();
            });
        });
    </script>

    <?php
});

add_action('admin_head', function () {
    ?>
    <style>
        #product-faqs-wrapper {
            margin-bottom: 15px;
        }

        .product-faq-card {
            background: #fff;
            border: 1px solid #dcdcde;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 12px;
        }

        .faq-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .faq-header strong {
            font-size: 14px;
        }

        .delete-faq {
            color: #b32d2e;
            font-size: 16px;
            text-decoration: none;
            cursor: pointer;
        }

        .delete-faq:hover {
            color: #dc3232;
        }

        .product-faq-card label {
            font-weight: 600;
            display: block;
            margin-bottom: 4px;
        }
    </style>
    <?php
});

/*
add_action('woocommerce_after_single_product_summary', function () {
    global $product;

    $faqs = get_post_meta($product->get_id(), '_product_faqs', true);

    if (empty($faqs) || !is_array($faqs)) {
        return;
    }
    ?>

    <section class="product-faqs">
        <h2>Frequently Asked Questions</h2>

        <?php foreach ($faqs as $faq): ?>
            <div class="faq-item">
                <h4><?php echo esc_html($faq['question']); ?></h4>
                <p><?php echo esc_html($faq['answer']); ?></p>
            </div>
        <?php endforeach; ?>
    </section>

    <?php
}, 25);
*/