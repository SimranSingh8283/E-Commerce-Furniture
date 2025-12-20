<?php
$post_id = get_the_ID();

/**
 * Exit if no featured image
 */
if (!has_post_thumbnail($post_id)) {
    return;
}

$thumb_id = get_post_thumbnail_id($post_id);

$banner_full = wp_get_attachment_image_src($thumb_id, 'full')[0] ?? '';
$banner_medium = wp_get_attachment_image_src($thumb_id, 'hero-medium')[0] ?? '';
$banner_small = wp_get_attachment_image_src($thumb_id, 'hero-small')[0] ?? '';

$alt_text = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
$title_text = get_the_title($thumb_id);
?>

<div class="PostSingle-thumbnail">
    <picture>
        <?php if ($banner_small): ?>
            <source media="(max-width: 600px)" srcset="<?php echo esc_url($banner_small); ?>">
        <?php endif; ?>

        <?php if ($banner_medium): ?>
            <source media="(max-width: 1024px)" srcset="<?php echo esc_url($banner_medium); ?>">
        <?php endif; ?>

        <img src="<?php echo esc_url($banner_full); ?>" alt="<?php echo esc_attr($alt_text ?: get_the_title()); ?>"
            title="<?php echo esc_attr($title_text); ?>" loading="eager" decoding="async">
    </picture>
</div>