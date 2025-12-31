<?php
/**
 * Header Menu
 * Uses $menu_arg defined here
 */

$menu_arg = [
    'menu' => 'Header Menu',
    'menu_class' => 'Navbar-nav',
    'menu_id' => 'Navbar-nav',
    'container' => 'ul',
    'add_li_class' => 'Navbar-item',
    'add_a_class' => 'Navbar-link'
];
?>

<?php wp_nav_menu($menu_arg); ?>