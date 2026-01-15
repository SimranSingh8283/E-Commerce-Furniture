<?php
if (!defined('ABSPATH'))
    exit;

if (isset($_GET['prod_search']) && trim($_GET['prod_search']) === '') {
    wp_safe_redirect(remove_query_arg('prod_search'));
    exit;
}

get_header('shop');

$collection = isset($_GET['collection'])
    ? sanitize_text_field($_GET['collection'])
    : 'all';

$collections = array(
    'all' => array(
        'label' => 'All Items',
        'title' => 'All Items',
        'description' => 'Explore our complete furniture collection.',
    ),
    'best_selling' => array(
        'label' => 'Best Selling Items',
        'title' => 'Best Selling Items',
        'description' => 'Explore our collection of thoughtfully crafted pieces, designed to blend comfort, style, and timeless charm into your everyday living.',
    ),
    'on_sale' => array(
        'label' => 'On Sale',
        'title' => 'On Sale',
        'description' => 'Limited-time deals you don’t want to miss.',
    ),
);

if (!isset($collections[$collection])) {
    $collection = 'all';
}
?>

<section class="Block-root Block-productsCategory">
    <div class="Container-root">

        <div class="Products-collection">
            <?php get_template_part(
                'template-parts/products/collection',
                null,
                array('collection' => $collection)
            ); ?>
        </div>

    </div>
</section>