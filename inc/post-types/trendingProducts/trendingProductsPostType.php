<?php
/**
 * Entry point for TrendingProducts Post Type
 */

if (!class_exists('ThemePostTypeTrendingProducts')) {
    class ThemePostTypeTrendingProducts {
        public static function init() {
            $base = get_template_directory() . '/inc/post-types/trendingProducts/';
            require_once $base . 'register.php';
            require_once $base . 'module/loader.php';
        }
    }
}