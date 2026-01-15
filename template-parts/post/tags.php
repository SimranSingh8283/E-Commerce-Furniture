<?php
if (!empty($_GET['blog_tag'])) {
    return;
}

$tags = get_the_tags();
if (!$tags) {
    return;
}
?>

<div class="Post-tags">
    <?php foreach ($tags as $tag): ?>
        <a class="Post-tag-link" href="<?php echo esc_url(add_query_arg('blog_tag', $tag->slug)); ?>">

            <?php
            $icon = get_term_meta($tag->term_id, 'term_icon', true);
            if ($icon):
                ?>
                <iconify-icon icon="<?php echo esc_attr($icon); ?>">
                </iconify-icon>
            <?php endif; ?>

            <span class="Post-label">
                <?php echo esc_html($tag->name); ?>
            </span>
        </a>
    <?php endforeach; ?>
</div>