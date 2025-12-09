<?php
/**
 * Class ThemeSetup
 * Handles theme setup and utilities
 */

if ( ! class_exists( 'ThemeSetup' ) ) {
    class ThemeSetup {

        public static function init() {
            add_action('after_setup_theme', [__CLASS__, 'theme_setup']);
            add_shortcode('elementor_template', [__CLASS__, 'elementor_template_shortcode']);
            add_filter('wpcf7_autop_or_not', '__return_false');
        }

        public static function theme_setup() {
            global $theme_slug;

            add_theme_support('automatic-feed-links');
            add_theme_support('title-tag');
            add_theme_support('post-thumbnails');

            add_theme_support('custom-logo', [
                'height'      => 100,
                'width'       => 400,
                'flex-height' => true,
                'flex-width'  => true,
            ]);

            add_theme_support('custom-header', [
                'default-image' => get_template_directory_uri() . '/images/header.jpg',
                'width'         => 1000,
                'height'        => 250,
                'flex-height'   => true,
                'flex-width'    => true,
            ]);

            add_theme_support('custom-background', [
                'default-color' => 'ffffff',
                'default-image' => '',
            ]);

            add_theme_support('html5', [
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption'
            ]);

            add_theme_support('post-formats', [
                'aside', 'image', 'video', 'quote', 'link', 'gallery', 'audio'
            ]);

            add_theme_support('editor-styles');
            add_editor_style('editor-style.css');

            add_theme_support('responsive-embeds');
            add_theme_support('customize-selective-refresh-widgets');

            register_nav_menus([
                'primary' => __('Primary Menu', $theme_slug),
                'footer'  => __('Footer Menu', $theme_slug),
            ]);

            add_image_size('hero-large', 1920, 800, true);
            add_image_size('hero-medium', 1366, 500, true);
            add_image_size('hero-small', 750, 500, true);
        }

        public static function elementor_template_shortcode($atts) {
            $atts = shortcode_atts([
                'id' => '',
            ], $atts, 'elementor_template');

            if (!empty($atts['id'])) {
                return \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($atts['id']);
            }

            return '';
        }

    }
}

ThemeSetup::init();