<?php
add_action('init', function() {
    add_shortcode('testimonials_slider', function($atts) {
        ob_start();
        TestimonialsModule::render([
            'args' => shortcode_atts([
                'effect'         => 'fade',
                'speed'          => 1000,
                'autoplay_delay' => 4000,
                'loop'           => true,
            ], $atts)
        ]);
        return ob_get_clean();
    });
});