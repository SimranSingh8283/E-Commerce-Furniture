<?php
add_action('init', function () {
    add_shortcode('trending_products_slider', function ($atts) {

        $atts = shortcode_atts([
            'speed'          => 800,
            'autoplay_delay' => 3000,
            'loop'           => true,
        ], $atts, 'trending_products_slider');

        ob_start();

        TrendingProductsModule::render([
            'args' => $atts
        ]);

        return ob_get_clean();
    });
});