<?php

/*
 * Template Name: Template Inner
 */

get_header();

$base_url = site_url();
$template_uri = get_template_directory_uri();
/*
$post_id = get_the_ID();

// Get the post thumbnail ID (featured image)
$attachment_id = get_post_thumbnail_id($post_id);

if ($attachment_id) {
    $banner_full = wp_get_attachment_image_src($attachment_id, 'full')[0];
    $banner_medium = wp_get_attachment_image_src($attachment_id, 'hero-medium')[0] ?? $banner_full;
    $banner_small = wp_get_attachment_image_src($attachment_id, 'hero-small')[0] ?? $banner_medium;

    $alt_text = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
    $title_text = get_the_title($attachment_id);
}
*/
?>

<? /*<section class="Block-root BlockParallax-root Hero-root Hero-inner" data-aos>
    <div class="Block-object Parallax-object">
        <picture>
            <source media="(max-width: 600px)" srcset="<?= esc_url($banner_small); ?>">
            <source media="(max-width: 1024px)" srcset="<?= esc_url($banner_medium); ?>">
            <img src="<?= esc_url($banner_full); ?>" alt="<?= esc_attr($alt_text); ?>"
                title="<?= esc_attr($title_text); ?>">
        </picture>
    </div>

    <div class="container">
        <div class="Hero-content">
            <div class="Hero-title">
                <h1><?= esc_html(get_the_title()); ?></h1>
            </div>
        </div>
    </div>
</section> */?>

<?php the_content(); ?>

<?php get_footer(); ?>