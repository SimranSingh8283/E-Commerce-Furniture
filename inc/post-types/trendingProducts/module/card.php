<?php
if (!class_exists('TrendingProductsModuleCard')) {
    class TrendingProductsModuleCard
    {
        public static function render($post)
        {
            $post_id = $post->ID;
            $title = get_the_title($post_id);

            $attachment_id = get_post_thumbnail_id($post_id);

            $overlay_id = get_field('overlay');

            if ($overlay_id) {
                $overlay_full = wp_get_attachment_image_src($overlay_id, 'full')[0] ?? '';
                $overlay_medium = wp_get_attachment_image_src($overlay_id, 'hero-medium')[0] ?? $overlay_full;
                $overlay_small = wp_get_attachment_image_src($overlay_id, 'hero-small')[0] ?? $overlay_medium;

                $overlay_alt = get_post_meta($overlay_id, '_wp_attachment_image_alt', true);
                $overlay_title = get_the_title($overlay_id);
            }

            if ($attachment_id) {
                $banner_full = wp_get_attachment_image_src($attachment_id, 'full')[0] ?? '';
                $banner_medium = wp_get_attachment_image_src($attachment_id, 'hero-medium')[0] ?? $banner_full;
                $banner_small = wp_get_attachment_image_src($attachment_id, 'hero-small')[0] ?? $banner_medium;

                $alt_text = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
                $title_text = get_the_title($attachment_id);
            }
            ?>
            <swiper-slide class="ProductCard-slide">
                <div class="ProductCard-root">
                    <?php if (!empty($attachment_id)): ?>
                        <div class="ProductCard-thumb">
                            <picture>
                                <?php if (!empty($banner_small)): ?>
                                    <source media="(max-width: 600px)" srcset="<?= esc_url($banner_small); ?>">
                                <?php endif; ?>

                                <?php if (!empty($banner_medium)): ?>
                                    <source media="(max-width: 1024px)" srcset="<?= esc_url($banner_medium); ?>">
                                <?php endif; ?>

                                <img src="<?= esc_url($banner_full); ?>" alt="<?= esc_attr($alt_text); ?>"
                                    title="<?= esc_attr($title_text); ?>" loading="lazy">
                            </picture>
                            
                            <?php if (!empty($overlay_id)): ?>
                                <div class="ProductCard-overlay">
                                    <picture>
                                        <?php if (!empty($overlay_small)): ?>
                                            <source media="(max-width: 600px)" srcset="<?= esc_url($overlay_small); ?>">
                                        <?php endif; ?>

                                        <?php if (!empty($overlay_medium)): ?>
                                            <source media="(max-width: 1024px)" srcset="<?= esc_url($overlay_medium); ?>">
                                        <?php endif; ?>

                                        <img src="<?= esc_url($overlay_full); ?>" alt="<?= esc_attr($overlay_alt); ?>"
                                            title="<?= esc_attr($overlay_title); ?>" loading="lazy">
                                    </picture>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="ProductCard-title">
                        <h3><?= esc_html($title); ?></h3>
                    </div>

                </div>
            </swiper-slide>
            <?php
        }
    }
}