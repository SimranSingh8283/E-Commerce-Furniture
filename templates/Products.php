<?php
/*
Template Name: Products
Template Post Type: page
*/

if (isset($_GET['prod_search']) && trim($_GET['prod_search']) === '') {
    wp_safe_redirect(remove_query_arg('prod_search'));
    exit;
}

get_header('shop');

$collection = isset($_GET['collection'])
    ? sanitize_text_field($_GET['collection'])
    : 'all';
?>

<section class="Block-root Block-products Products-root">
    <div class="Container-root">

        <div class="Products-wrapper">
            <?php get_template_part('template-parts/products/categories'); ?>
            <?php get_template_part('template-parts/products/sale'); ?>

            <div class="Products-collection">
                <?php get_template_part(
                    'template-parts/products/collection',
                    null,
                    array('collection' => $collection)
                ); ?>
            </div>
        </div>

    </div>
</section>

<?php get_footer('shop'); ?>