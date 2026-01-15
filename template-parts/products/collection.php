<?php
if (!defined('ABSPATH'))
    exit;

$template_uri = get_template_directory_uri();

$collection = $args['collection'] ?? 'all';

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


$category = get_queried_object();
$category_name = $category && isset($category->name) ? $category->name : '';
?>


<div class="Flex-root Flex-wrap">
    <div class="Col-root Col-lg-6">
        <div class="Block-heading">
            <span aria-level="1" data-level="1">
                <?php echo esc_html($collections[$collection]['title']); ?>
                <?php if ($category_name): ?>
                     / <span class="clr"><?php echo esc_html($category_name); ?></span>
                <?php endif; ?>
            </span>
            <p>
                <?php echo esc_html($collections[$collection]['description']); ?>
            </p>
        </div>

    </div>
</div>

<?php
get_template_part(
    'template-parts/products/collection-search',
    null,
    [
        'collection' => $collection,
    ]
);
?>


<?php
$paged = max(1, get_query_var('paged'));

$queried = get_queried_object();

$category_id = (
    $queried &&
    isset($queried->taxonomy) &&
    $queried->taxonomy === 'product_cat'
) ? (int) $queried->term_id : 0;

$args = [
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => 12,
    'paged' => $paged,
];

if ($category_id) {
    $args['tax_query'] = [[
        'taxonomy' => 'product_cat',
        'field'    => 'term_id',
        'terms'    => $category_id,
    ]];
}

if (!empty($_GET['prod_search'])) {
    $args['s'] = sanitize_text_field($_GET['prod_search']);
}

if ($collection === 'best_selling') {
    $args['meta_key'] = 'total_sales';
    $args['orderby'] = 'meta_value_num';
    $args['order'] = 'DESC';
}

if ($collection === 'on_sale') {
    $args['post__in'] = wc_get_product_ids_on_sale();
}

$query = new WP_Query($args);
?>

<?php if ($query->have_posts()): ?>

    <ul class="Flex-root Flex-wrap Products-root Products--grid">

        <?php while ($query->have_posts()):
            $query->the_post(); ?>
            <?php
            global $product;
            if (!$product || !$product->is_visible())
                continue;

            $product_id = $product->get_id();
            $permalink = $product->get_permalink();
            $title = $product->get_name();
            $image_id = $product->get_image_id();
            $image_url = wp_get_attachment_image_url($image_id, 'medium');
            ?>

            <li class="Col-root Col-lg-4 Col-md-6 Product-root" data-product-id="<?php echo esc_attr($product_id); ?>">

                <div class="Product-thumbnail">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>">
                </div>

                <div class="Product-category">
                    <?php
                    $categories = wp_get_post_terms($product_id, 'product_cat');
                    if (!empty($categories) && !is_wp_error($categories)) {
                        $category_links = [];

                        foreach ($categories as $category) {
                            $link = get_term_link($category);
                            if (!is_wp_error($link)) {
                                $category_links[] = '<a href="' . esc_url($link) . '" class="Product-category-link">' . esc_html($category->name) . '</a>';
                            }
                        }

                        echo implode(', ', $category_links);
                    }
                    ?>
                </div>

                <div class="Product-wishlist" data-product-id="<?php echo esc_attr($product_id); ?>">

                    <div class="Product-wishlist-native" style="display:none;">
                        <?php echo do_shortcode('[woosw id="' . $product_id . '"]'); ?>
                    </div>

                    <button class="Button-root Button-icon Button-wishlist" data-tooltip="Add to Wishlist"
                        aria-label="Add to Wishlist">
                        <iconify-icon icon="mdi:heart-outline"></iconify-icon>
                    </button>
                </div>

                <div class="Product-overlay">
                    <a href="<?php echo esc_url($permalink); ?>" class="Product-title">
                        <span><?php echo esc_html($title); ?></span>
                    </a>

                    <div class="Product-action">
                        <?php
                        get_template_part('template-parts/products/add-to-cart', null, ['product' => $product]);
                        ?>
                    </div>

                </div>
            </li>

        <?php endwhile; ?>

    </ul>

    <?php wp_reset_postdata(); ?>

<?php else: ?>
    <?php do_action('woocommerce_no_products_found'); ?>
<?php endif; ?>


<script>
    // document.addEventListener('click', function (e) {
    //     const chip = e.target.closest('.ProductsCollection-chips .Chip');
    //     if (!chip) return;

    //     const collection = chip.dataset.collection;
    //     const url = new URL(window.location);

    //     url.searchParams.set('collection', collection);

    //     window.location.href = url.toString();
    // });

// document.addEventListener('click', function (e) {
//     const chip = e.target.closest('.ProductsCollection-chips .Chip');
//     if (!chip) return;

//     const wrapper = document.querySelector('.Products-collection');
//     const collection = chip.dataset.collection;

//     const url = new URL(window.location);
//     url.searchParams.set('collection', collection);
//     history.pushState({}, '', url);

//     wrapper.classList.add('is-loading');

//     fetch('<?php // echo esc_url(admin_url('admin-ajax.php')); ?>', {
//         method: 'POST',
//         headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
//         body: new URLSearchParams({
//             action: 'load_product_collection',
//             collection
//         })
//     })
//     .then(res => res.json())
//     .then(res => {
//         if (res.success) {
//             wrapper.innerHTML = res.data;
//         }
//         wrapper.classList.remove('is-loading');
//     });
// });

// window.addEventListener('popstate', function () {
//     const collection = new URLSearchParams(window.location.search).get('collection') || 'all';
//     const wrapper = document.querySelector('.Products-collection');

//     wrapper.classList.add('is-loading');

//     fetch('<?php // echo esc_url(admin_url('admin-ajax.php')); ?>', {
//         method: 'POST',
//         headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
//         body: new URLSearchParams({
//             action: 'load_product_collection',
//             collection
//         })
//     })
//     .then(res => res.json())
//     .then(res => {
//         if (res.success) {
//             wrapper.innerHTML = res.data;
//         }
//         wrapper.classList.remove('is-loading');
//     });
// });
</script>