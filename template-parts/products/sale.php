<?php
if (!defined('ABSPATH'))
    exit;

$sale_ids = wc_get_product_ids_on_sale();

if (empty($sale_ids)) {
    return;
}

$args = array(
    'post_type' => 'product',
    'post__in' => $sale_ids,
    'posts_per_page' => 12,
    'orderby' => 'date',
);

$sale_query = new WP_Query($args);

if (!$sale_query->have_posts()) {
    return;
}
?>

<div class="Products-sale">

    <div class="Block-heading">
        <span aria-level="1" data-level="1">
            Find Items That Would Perfectly <span class="clr">Fit Together</span> With Special Offers For <span
                class="clr">Limited</span> Time!
        </span>
    </div>

    <swiper-container id="Products-saleSwiper" class="Products-saleSwiper" init="false">
        <?php while ($sale_query->have_posts()):
            $sale_query->the_post();
            global $product;
            ?>

            <swiper-slide class="Product-slide">
                <div class="ProductCard">
                    <div class="ProductCard-image">
                        <?php
                        if (has_post_thumbnail()) {
                            the_post_thumbnail();
                        }
                        ?>
                    </div>

                    <div class="ProductCard-actions">
                        <a href="<?php echo esc_url($product->add_to_cart_url()); ?>"
                            class="Button-root Button-primary add_to_cart_button ajax_add_to_cart" data-variant="contained"
                            data-product_id="<?php echo esc_attr($product->get_id()); ?>"
                            data-product_sku="<?php echo esc_attr($product->get_sku()); ?>">
                            <?php echo esc_html($product->add_to_cart_text()); ?>
                        </a>
                    </div>
                </div>

            </swiper-slide>

        <?php endwhile;
        wp_reset_postdata(); ?>

        <div slot="container-end">
            <div class="Products-swiper-controls">
                <button data-tooltip="Previous" class="Button-root Button-prev">
                    <iconify-icon icon="system-uicons:chevron-left"></iconify-icon>
                </button>

                <button data-tooltip="Next" class="Button-root Button-next">
                    <iconify-icon icon="system-uicons:chevron-right"></iconify-icon>
                </button>
            </div>

        </div>
    </swiper-container>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const swiperEl = document.querySelector('#Products-saleSwiper');
            if (!swiperEl) return;

            const params = {
                slidesPerView: 1,
                loop: true,
                speed: 1000,
                spaceBetween: 24,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true
                },
                navigation: {
                    nextEl: ".Products-swiper-controls .Button-next",
                    prevEl: ".Products-swiper-controls .Button-prev"
                }
            };

            Object.assign(swiperEl, params);

            swiperEl.initialize();
        });
    </script>
</div>