<?php
$args = get_query_var('card_args', []);
$show_title = $args['title'] ?? true;
$show_meta = $args['meta'] ?? true;
$show_desc = $args['desc'] ?? true;
$show_excerpt = $args['excerpt'] ?? false;
?>

<article class="Post-root">
    <div class="Post-thumb">
        <a href="<?php the_permalink(); ?>">
            <?php if (has_post_thumbnail()): ?>
                <?php the_post_thumbnail('medium_large'); ?>
            <?php endif; ?>
        </a>
    </div>

    <div class="Post-content">
        <?php if ($show_meta): ?>
            <div class="Post-meta">
                <?php
                if (empty($_GET['blog_tag'])):
                    $post_tags = get_the_tags();
                    if ($post_tags):
                        ?>
                        <span class="Post-tags">
                            <?php foreach ($post_tags as $tag): ?>
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
                        </span>
                        <?php
                    endif;
                endif;
                ?>
                <span class="Post-date"><?php echo get_the_date('M d, Y'); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($show_title): ?>
            <h3 class="Post-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
        <?php endif; ?>

        <?php if ($show_desc): ?>
            <div class="Post-desc">
                <?php echo wp_trim_words(get_the_excerpt(), 22); ?>
            </div>
        <?php endif; ?>

        <?php if ($show_excerpt): ?>
            <div class="Post-excerpt">
                <?php the_excerpt(); ?>
            </div>
        <?php endif; ?>

        <a href="<?php the_permalink(); ?>" class="Post-link">
            Learn more <iconify-icon icon="line-md:arrow-right"></iconify-icon>
        </a>
    </div>
</article>