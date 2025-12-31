<?php
/**
 * Class ThemePostTypeTrendingProductsRegister
 * Handles registration of the Trending Products custom post type
 */

add_action('init', ['ThemePostTypeTrendingProductsRegister', 'register_post_type']);

if (!class_exists('ThemePostTypeTrendingProductsRegister')) {
    class ThemePostTypeTrendingProductsRegister
    {
        public static function register_post_type()
        {
            $labels = [
                'name'                  => _x('Trending Products', 'Post type general name', 'textdomain'),
                'singular_name'         => _x('Trending Product', 'Post type singular name', 'textdomain'),
                'menu_name'             => _x('Trending Products', 'Admin Menu text', 'textdomain'),
                'name_admin_bar'        => _x('Trending Product', 'Add New on Toolbar', 'textdomain'),
                'add_new'               => __('Add New', 'textdomain'),
                'add_new_item'          => __('Add New Trending Product', 'textdomain'),
                'new_item'              => __('New Trending Product', 'textdomain'),
                'edit_item'             => __('Edit Trending Product', 'textdomain'),
                'view_item'             => __('View Trending Product', 'textdomain'),
                'all_items'             => __('All Trending Products', 'textdomain'),
                'search_items'          => __('Search Trending Products', 'textdomain'),
                'not_found'             => __('No trending products found.', 'textdomain'),
                'not_found_in_trash'    => __('No trending products found in Trash.', 'textdomain'),
                'featured_image'        => _x('Product Image', 'Overrides Featured Image label', 'textdomain'),
                'set_featured_image'    => __('Set product image', 'textdomain'),
                'remove_featured_image' => __('Remove product image', 'textdomain'),
                'use_featured_image'    => __('Use as product image', 'textdomain'),
                'archives'              => __('Trending Products Archives', 'textdomain'),
                'items_list'            => __('Trending products list', 'textdomain'),
                'items_list_navigation' => __('Trending products list navigation', 'textdomain'),
            ];

            $args = [
                'labels'             => $labels,
                'description'        => __('Products marked as trending', 'textdomain'),
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => false, // change to array('slug' => 'trending-products') if needed
                'capability_type'    => 'post',
                'has_archive'        => false,
                'hierarchical'       => false,
                'menu_position'      => 7,
                'menu_icon'          => 'dashicons-chart-line',
                'show_in_rest'       => true,
                'supports'           => [
                    'title',
                    'editor',
                    'thumbnail',
                    'excerpt',
                    'custom-fields',
                    'revisions'
                ],
            ];

            register_post_type('trending_products', $args);
        }
    }
}