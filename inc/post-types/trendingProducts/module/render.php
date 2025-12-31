<?php
if (!class_exists('TrendingProductsModule')) {
    class TrendingProductsModule
    {
        public static function render($args = [])
        {
            $default_query_args = [
                'post_type'      => 'trending_products',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'ASC',
            ];

            $incoming_args = $args['args'] ?? [];
            $query_args    = wp_parse_args($incoming_args, $default_query_args);

            $settings = [
                'speed'          => $incoming_args['speed'] ?? 800,
                'autoplay_delay' => $incoming_args['autoplay_delay'] ?? 3000,
                'loop'           => $incoming_args['loop'] ?? true,
            ];

            $query = new WP_Query($query_args);
            ?>
            <swiper-container
                class="TrendingProducts-swiper"
                navigation="false"
                speed="<?= esc_attr($settings['speed']); ?>"
                slides-per-view="4"
                autoplay-delay="<?= esc_attr($settings['autoplay_delay']); ?>"
                loop="<?= $settings['loop'] ? 'true' : 'false'; ?>"
                breakpoints='{
                    "0": {
                        "slidesPerView": 1
                    },
                    "768": {
                        "slidesPerView": 2,
                        "spaceBetween": 30
                    },
                    "1024": {
                        "slidesPerView": 4,
                        "spaceBetween": 30
                    }
                }'
            >
                <?php
                if ($query->have_posts()) :
                    while ($query->have_posts()) :
                        $query->the_post();
                        self::render_card(get_post());
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p>No Trending Products found.</p>';
                endif;
                ?>
            </swiper-container>
            <?php
        }

        protected static function render_card($post)
        {
            TrendingProductsModuleCard::render($post);
        }
    }
}