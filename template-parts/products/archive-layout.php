<?php
defined('ABSPATH') || exit;

$search_query = get_search_query();
?>

<div class="Block-root Block-searchProducts SearchProducts-root">
    <div class="Container-root">

        <div class="SearchProducts-header">
            <div class="Container-root">

                <div class="Block-heading <?php echo $search_query ? '' : 'text-center'; ?>" style="margin: 0;">
                    <span aria-level="1" data-level="1">
                        <?php if ($search_query): ?>
                            Search results for :
                        <?php else: ?>
                            Search Results
                        <?php endif; ?>
                    </span>
                </div>

                <?php if ($search_query): ?>
                    <div class="Block-heading result" style="margin: 0;">
                        <span aria-level="2" data-level="2">
                            “<?php echo esc_html($search_query); ?>”
                        </span>
                    </div>
                <?php endif; ?>

            </div>

        </div>

        <ul class="Products-root Products--grid" style="margin-top: 1.5rem;">
            <?php if (woocommerce_product_loop()): ?>

                <?php do_action('woocommerce_before_shop_loop'); ?>

                <ul class="Flex-root Flex-wrap Products-root Products--grid">

                    <?php while (have_posts()):
                        the_post(); ?>
                        <?php
                        global $product;

                        if (!$product || !$product->is_visible()) {
                            continue;
                        }

                        $product_id = $product->get_id();
                        $permalink = $product->get_permalink();
                        $title = $product->get_name();
                        $image_id = $product->get_image_id();
                        $image_url = wp_get_attachment_image_url($image_id, 'medium');
                        ?>

                        <li class="Col-root Col-lg-4 Col-md-6 Product-root"
                            data-product-id="<?php echo esc_attr($product_id); ?>">

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

                <?php do_action('woocommerce_after_shop_loop'); ?>

            <?php else: ?>
                <?php do_action('woocommerce_no_products_found'); ?>
            <?php endif; ?>
        </ul>

    </div>
</div>


<script>
    jQuery(function ($) {

        $('.Product-wishlist').each(function () {
            syncWishlistUI($(this));
        });

        function syncWishlistUI($wrap) {
            const $nativeBtn = $wrap.find('.woosw-btn');
            const $btn = $wrap.find('.Button-wishlist');

            if ($nativeBtn.hasClass('woosw-added')) {
                $btn
                    .addClass('is-added')
                    .attr({
                        'data-tooltip': 'View Wishlist',
                        'aria-label': 'View Wishlist'
                    })
                    .html('<iconify-icon icon="mdi:heart"></iconify-icon>');
            }
        }

        $('.Product-wishlist').on('click', '.Button-wishlist', function (e) {
            e.preventDefault();

            const $btn = $(this);
            const $wrap = $btn.closest('.Product-wishlist');
            const $nativeBtn = $wrap.find('.woosw-btn');

            $nativeBtn.trigger('click');

            if (!$btn.hasClass('is-added')) {
                $btn.addClass('Button-loading');
            }
        });

        const observer = new MutationObserver(mutations => {
            mutations.forEach(m => {
                if (
                    m.target.classList &&
                    m.target.classList.contains('woosw-btn') &&
                    m.target.classList.contains('woosw-added')
                ) {
                    const $nativeBtn = $(m.target);
                    const $wrap = $nativeBtn.closest('.Product-wishlist');
                    const $btn = $wrap.find('.Button-wishlist');

                    $btn
                        .removeClass('Button-loading')
                        .addClass('is-added')
                        .attr({
                            'data-tooltip': 'View Wishlist',
                            'aria-label': 'View Wishlist'
                        })
                        .html('<iconify-icon icon="mdi:heart"></iconify-icon>');
                }
            });
        });

        $('.woosw-btn').each(function () {
            observer.observe(this, { attributes: true, attributeFilter: ['class'] });
        });

    });
</script>