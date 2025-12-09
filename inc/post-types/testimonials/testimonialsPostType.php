<?php
/**
 * Entry point for Testimonials Post Type
 */

if (!class_exists('ThemePostTypeTestimonials')) {
    class ThemePostTypeTestimonials {
        public static function init() {
            $base = get_template_directory() . '/inc/post-types/testimonials/';
            require_once $base . 'register.php';
            require_once $base . 'module/loader.php';
        }
    }
}