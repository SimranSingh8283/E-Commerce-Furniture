<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function register_elementor_widgets( $widgets_manager ) {

    require_once get_template_directory() . '/inc/elementor-widgets/Button.php';
    require_once get_template_directory() . '/inc/elementor-widgets/Heading.php';

    $widgets_manager->register( new \Button_Widget() );
    $widgets_manager->register( new \Heading_Widget() );
}
add_action( 'elementor/widgets/register', 'register_elementor_widgets' );