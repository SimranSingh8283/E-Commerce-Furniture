<?php
/**
 * Class ThemePostTypeTestimonialsRegister
 * Handles registration of the Testimonials custom post type
 */

add_action('init', ['ThemePostTypeTestimonialsRegister', 'register_post_type']);

if (!class_exists('ThemePostTypeTestimonialsRegister')) {
    class ThemePostTypeTestimonialsRegister
    {
        public static function register_post_type()
        {
            $labels = array(
                'name' => _x('Testimonials', 'Post type general name', 'textdomain'),
                'singular_name' => _x('Testimonial', 'Post type singular name', 'textdomain'),
                'menu_name' => _x('Testimonials', 'Admin Menu text', 'textdomain'),
                'name_admin_bar' => _x('Testimonial', 'Add New on Toolbar', 'textdomain'),
                'add_new' => __('Add New', 'textdomain'),
                'add_new_item' => __('Add New Testimonial', 'textdomain'),
                'new_item' => __('New Testimonial', 'textdomain'),
                'edit_item' => __('Edit Testimonial', 'textdomain'),
                'view_item' => __('View Testimonial', 'textdomain'),
                'all_items' => __('All Testimonials', 'textdomain'),
                'search_items' => __('Search Testimonials', 'textdomain'),
                'not_found' => __('No testimonials found.', 'textdomain'),
                'not_found_in_trash' => __('No testimonials found in Trash.', 'textdomain'),
                'featured_image' => _x('Testimonial Image', 'Overrides the “Featured Image” phrase', 'textdomain'),
                'set_featured_image' => _x('Set testimonial image', 'textdomain'),
                'remove_featured_image' => _x('Remove testimonial image', 'textdomain'),
                'use_featured_image' => _x('Use as testimonial image', 'textdomain'),
                'archives' => _x('Testimonial archives', 'The post type archive label', 'textdomain'),
                'insert_into_item' => _x('Insert into testimonial', 'textdomain'),
                'uploaded_to_this_item' => _x('Uploaded to this testimonial', 'textdomain'),
                'filter_items_list' => _x('Filter testimonials list', 'textdomain'),
                'items_list_navigation' => _x('Testimonials list navigation', 'textdomain'),
                'items_list' => _x('Testimonials list', 'textdomain'),
            );

            $args = array(
                'labels' => $labels,
                'description' => 'Customer testimonials',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => false,
                'capability_type' => 'post',
                'has_archive' => false,
                'hierarchical' => false,
                'menu_position' => 6,
                'menu_icon' => 'dashicons-testimonial',
                'show_in_rest' => true,
                'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions'],
            );

            register_post_type('testimonials', $args);
        }
    }
}