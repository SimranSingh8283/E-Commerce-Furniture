<?php
if (!defined('ABSPATH')) exit;

$uncat = get_term_by('slug', 'uncategorized', 'product_cat');

$product_categories = get_terms(array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => false,
    'exclude'    => $uncat ? array($uncat->term_id) : array(),
));

if (empty($product_categories) || is_wp_error($product_categories)) {
    return;
}

$main_category   = $product_categories[0];
$side_categories = array_slice($product_categories, 1, 4);
?>

<div class="Products-categories">

    <div class="Block-heading">
        <span aria-level="1" data-level="1">
            Find the Pieces That Shape Your Home’s <span class="clr">Story</span>
        </span>
    </div>

    <div class="Flex-root Flex-wrap Flex-products">

        <div class="Col-root Col-lg-6 Products-categoryBig">
            <?php
            $thumb_id = get_term_meta($main_category->term_id, 'thumbnail_id', true);
            $img = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'large') : wc_placeholder_img_src();
            ?>
            <a href="<?php echo esc_url(get_term_link($main_category)); ?>"
               class="CategoryCard CategoryCard--big">
                <img src="<?php echo esc_url($img); ?>"
                     alt="<?php echo esc_attr($main_category->name); ?>">
                <span><?php echo esc_html($main_category->name); ?></span>
            </a>
        </div>

        <div class="Col-root Col-lg-6">
            <div class="Flex-root Flex-wrap">

                <?php foreach ($side_categories as $category) :

                    $thumb_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                    $img = $thumb_id
                        ? wp_get_attachment_image_url($thumb_id, 'medium')
                        : wc_placeholder_img_src();
                ?>

                    <div class="Col-root Col-6 Products-categorySmall">
                        <a href="<?php echo esc_url(get_term_link($category)); ?>"
                           class="CategoryCard CategoryCard--small">
                            <img src="<?php echo esc_url($img); ?>"
                                 alt="<?php echo esc_attr($category->name); ?>">
                            <span><?php echo esc_html($category->name); ?></span>
                        </a>
                    </div>

                <?php endforeach; ?>

            </div>
        </div>

    </div>

</div>