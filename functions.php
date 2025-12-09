<?php
/**
 * Furniture Theme Functions
 *
 * @package Furniture
 */

define('THEME_SLUG', 'Furniture');
// modular function files
require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/widgets.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/post-types.php';
require_once get_template_directory() . '/inc/theme-functions.php';
require_once get_template_directory() . '/inc/elementor-widgets/register-widgets.php';