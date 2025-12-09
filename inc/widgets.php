<?php
/**
 * Theme Widgets Class
 */

if (!class_exists('ThemeWidgets')) {
    class ThemeWidgets
    {
        public static function init()
        {
            add_action('widgets_init', [__CLASS__, 'register_sidebars']);
        }

        public static function register_sidebars()
        {
            register_sidebar([
                'name' => __('Sidebar', THEME_SLUG),
                'id' => 'sidebar-1',
                'description' => __('Add widgets here.', THEME_SLUG),
                'before_widget' => '<section class="widget">',
                'after_widget' => '</section>',
                'before_title' => '<h2 class="widget-title">',
                'after_title' => '</h2>',
            ]);
        }
    }
}


ThemeWidgets::init();