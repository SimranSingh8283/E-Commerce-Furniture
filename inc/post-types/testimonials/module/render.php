<?php
if (!class_exists('TestimonialsModule')) {
    class TestimonialsModule {
        public static function render($args = []) {
            // Default query
            $default_query_args = [
                'post_type'      => 'testimonials',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
            ];

            $incoming_args = $args['args'] ?? [];
            $query_args = wp_parse_args($incoming_args, $default_query_args);

            // UI settings
            $settings = [
                'effect'         => $incoming_args['effect'] ?? 'fade',
                'speed'          => $incoming_args['speed'] ?? 1000,
                'autoplay_delay' => $incoming_args['autoplay_delay'] ?? 4000,
                'loop'           => $incoming_args['loop'] ?? true,
            ];

            // Query
            $query = new WP_Query($query_args);

            ?>
            <swiper-container 
                class="Review-wrapper Review-swiper"
                navigation="true"
                effect="<?= esc_attr($settings['effect']) ?>"
                speed="<?= esc_attr($settings['speed']) ?>"
                autoplay-delay="<?= esc_attr($settings['autoplay_delay']) ?>"
                loop="<?= $settings['loop'] ? 'true' : 'false' ?>"
                data-swiper='{"fadeEffect": {"crossFade": true}}'
            >
                <?php
                if ($query->have_posts()) :
                    while ($query->have_posts()) :
                        $query->the_post();
                        self::render_card(get_post());
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p>No Testimonials found.</p>';
                endif;
                ?>
            </swiper-container>
            <?php
        }

        public static function render_card($post) {
            TestimonialsModuleCard::render($post);
        }
    }
}