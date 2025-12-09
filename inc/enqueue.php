<?php
/**
 * Theme Enqueue Class
 */

if (!class_exists('ThemeEnqueue')) {
    class ThemeEnqueue
    {
        public static function init()
        {
            add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
            add_filter('script_loader_tag', [__CLASS__, 'add_module_type_to_index_js'], 10, 3);
        }

        public static function enqueue_assets()
        {
            wp_enqueue_script('jquery');
            wp_enqueue_style('styles', get_template_directory_uri() . '/assets/css/styles.css', [], '0.3');
            wp_enqueue_script('index-js', get_template_directory_uri() . '/assets/js/index.js', [], '0.1', true);
            wp_enqueue_script('swiper-js', get_template_directory_uri() . '/assets/js/swiper-elements.min.js', [], '0.1', true);
            wp_enqueue_script('lenis-js', 'https://unpkg.com/lenis@1.0.45/dist/lenis.min.js', [], '0.1', true);
        }

        public static function add_module_type_to_index_js($tag, $handle, $src)
        {
            if ($handle === 'index-js') {
                return '<script type="module" src="' . esc_url($src) . '"></script>';
            }
            return $tag;
        }
    }
}

ThemeEnqueue::init();