<?php
/**
 * Template for displaying a single post.
 * 
 * @package WordPress
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_the_ID();

// Get custom fields for the banner image
$attachment_id = get_field('banner_image');

// Check if a banner image is set
if ($attachment_id) {
    $banner_full = wp_get_attachment_image_src($attachment_id, 'full')[0];
    $banner_medium = wp_get_attachment_image_src($attachment_id, 'hero-medium')[0];
    $banner_small = wp_get_attachment_image_src($attachment_id, 'hero-small')[0];

    // Get alt and title attributes for the image
    $alt_text = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
    $title_text = get_the_title($attachment_id);
} else {
    // Fallback values if no banner image is set
    $banner_full = '';
    $banner_medium = '';
    $banner_small = '';
    $alt_text = 'Default alt text';
    $title_text = 'Default Title';
}

// Get subtitle field (fallback if not set)
$subtitle = get_field('subtitle');
?>

<section class="Hero-root Hero-inner Hero-post Block-root BlockParallax-root" data-aos>
    <div class="Block-object Parallax-object">
        <picture>
            <source media="(max-width: 600px)" srcset="<?= esc_url($banner_small); ?>">
            <source media="(max-width: 1024px)" srcset="<?= esc_url($banner_medium); ?>">
            <img src="<?= esc_url($banner_full); ?>" alt="<?= esc_attr($alt_text); ?>"
                title="<?= esc_attr($title_text); ?>" class="Hero-banner-image">
        </picture>
    </div>

    <div class="container">
        <div class="Hero-content">
            <div class="Block-heading">
                <?php if (!empty($subtitle)) { ?>
                    <span><?= esc_html($subtitle); ?></span>
                <?php } ?>
                <h1><?= esc_html(get_the_title()); ?></h1>
            </div>
        </div>
    </div>
</section>

<section class="Block-root Block-article">
    <div class="container">

        <article class="Post-body">
            <?php
            the_content();

            wp_link_pages();
            ?>
        </article>

    </div>
</section>