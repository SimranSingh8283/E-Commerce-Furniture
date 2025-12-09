<?php
/**
 * Template Name: Front Page
 * This template is used for the homepage of the site.
 *
 * @package WordPress
 */


if (!defined('ABSPATH')) {
    exit;
}
get_header();

$site_url = site_url();
$template_uri = get_template_directory_uri();
// $insta = get_theme_mod('instagram_link');
// $fb = get_theme_mod('facebook_link');
// $li = get_theme_mod('linkedin_link');
// $yt = get_theme_mod('youtube_link');

// remove_filter('the_content', 'wpautop');
// remove_filter('the_excerpt', 'wpautop');

?>

<?php the_content() ?>

<?php get_footer(); ?>