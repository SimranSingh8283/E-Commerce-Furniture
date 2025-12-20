<?php
$author_id = get_the_author_meta('ID');
$author_name = get_the_author();
$author_url = get_author_posts_url($author_id);
$avatar = get_avatar($author_id, 100);
?>

<div class="PostSingle-meta PostMeta-root">
    <a href="<?php echo esc_url($author_url); ?>" class="PostMeta-author">
        <div class="PostMeta-avatar"><?php echo $avatar; ?></div>

        <div class="PostMeta-info">
            <span class="PostMeta-name"><?php echo esc_html($author_name); ?></span>
            <span class="PostMeta-author">Author</span>
        </div>
    </a>

    <div class="PostMeta-date">
        <?php echo esc_html(get_the_date('M d, Y')); ?>
    </div>
</div>