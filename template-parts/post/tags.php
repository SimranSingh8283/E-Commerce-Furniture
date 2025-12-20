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
    <?php foreach ($tags as $tag) : ?>
        <span class="Post-tag">
            <?php echo esc_html($tag->name); ?>
        </span>
    <?php endforeach; ?>
</div>