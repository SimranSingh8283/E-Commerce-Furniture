<?php
/**
 * Header Search Form
 * Variables passed: $template_uri
 */
$product_categories = get_terms([
    'taxonomy' => 'product_cat',
    'hide_empty' => true,
]);

$template_uri = $args['template_uri'] ?? '';
?>

<div class="Header-search">
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

        <button data-tooltip="Search" type="submit" class="HeaderSearch-button Button-root Button-icon">
            <img src="<?= $template_uri ?>/assets/media/search.svg" alt="Search">
        </button>
    </form>
</div>