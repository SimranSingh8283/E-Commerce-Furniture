<?php
$tags = wp_get_post_tags(get_the_ID());
if (!$tags)
    return;

$tag_ids = wp_list_pluck($tags, 'term_id');

$query = new WP_Query([
    'post_type' => 'post',
    'posts_per_page' => 3,
    'tag__in' => $tag_ids,
    'post__not_in' => [get_the_ID()],
    'post_status' => 'publish',
    'category_name' => 'articles',
]);

if ($query->have_posts()):
    ?>
    <div class="PostSingle-related">
        <div class="Block-heading">
            <span aria-level="1" data-level="1">More Related Topics</span>
        </div>

        <div class="Related-grid">
            <div class="Flex-root">
                <?php while ($query->have_posts()):
                    $query->the_post(); ?>
                    <div class="Col-root Col-lg-4">
                        <?php
                        set_query_var('card_args', [
                            'desc' => false,
                        ]);

                        get_template_part('template-parts/post/card');
                        ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <?php
endif;
wp_reset_postdata();