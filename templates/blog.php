<?php
/*
Template Name: Blog
Template Post Type: page
*/
/*
get_header();

$base_url = site_url();
$template_uri = get_template_directory_uri();


<section class="Block-root Blog-root">
    <div class="Container-root">

        <div class="Blog-header">
            <div class="Block-heading">
                <h1 data-level="1"><?= the_title() ?></h1>
            </div>

            <div class="Blog-content">
                <div class="Flex-root">
                    <div class="Col-root Col-lg-6">
                        <?= the_content() ?>
                    </div>
                </div>
            </div>

            <form method="get" action="<?php echo esc_url(get_permalink()); ?>" class="Form-root Form-blog">

                <button type="submit" data-tooltip="Search Article" class="Button-root Button-icon">
                    <img src="<?= esc_url($template_uri); ?>/assets/media/search.svg" alt="" />
                </button>

                <input type="search" name="blog_search" placeholder="Search Article..."
                    value="<?php echo esc_attr($_GET['blog_search'] ?? ''); ?>">

                <?php if (!empty($_GET['blog_cat'])): ?>
                    <input type="hidden" name="blog_cat" value="<?php echo esc_attr($_GET['blog_cat']); ?>">
                <?php endif; ?>

            </form>

            <?php
            $active_tag = $_GET['blog_tag'] ?? '';
            $tags = get_tags([
                'hide_empty' => false,
            ]);
            ?>

            <div class="Blog-tags">
                <a href="<?php echo esc_url(remove_query_arg('blog_tag')); ?>"
                    class="Blog-chip <?php echo empty($active_tag) ? 'is-active' : ''; ?>">
                    All Articles
                </a>

                <?php foreach ($tags as $tag): ?>
                    <a href="<?php echo esc_url(add_query_arg('blog_tag', $tag->slug)); ?>"
                        class="Blog-chip <?php echo ($active_tag === $tag->slug) ? 'is-active' : ''; ?>">
                        <?php echo esc_html($tag->name); ?>
                    </a>
                <?php endforeach; ?>

            </div>
        </div>


        <?php
        $blog_search = isset($_GET['blog_search']) ? sanitize_text_field($_GET['blog_search']) : '';
        $blog_tag = isset($_GET['blog_tag']) ? sanitize_text_field($_GET['blog_tag']) : '';

        $args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 9,
            'category_name' => 'articles',
        ];

        if ($blog_search) {
            $args['s'] = $blog_search;
        }

        if ($blog_tag) {
            $args['tag'] = $blog_tag;
        }

        $blog_query = new WP_Query($args);
        ?>

        <?php if ($blog_query->have_posts()): ?>
            <div class="Flex-root">

                <?php while ($blog_query->have_posts()):
                    $blog_query->the_post(); ?>
                    <div class="Col-root Col-lg-4">
                        <article class="Post-root">

                            <div class="Post-thumb">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()): ?>
                                        <?php the_post_thumbnail('medium_large'); ?>
                                    <?php endif; ?>
                                </a>
                            </div>

                            <div class="Post-content">

                                <div class="Post-meta">

                                    <?php
                                    if (empty($_GET['blog_tag'])):

                                        $post_tags = get_the_tags();

                                        if ($post_tags):
                                            ?>
                                            <span class="Post-tags">
                                                <?php foreach ($post_tags as $tag): ?>
                                                    <span class="Post-tag">
                                                        <?php echo esc_html($tag->name); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </span>
                                            <?php
                                        endif;
                                    endif;
                                    ?>

                                    <span class="Post-date">
                                        <?php echo get_the_date('M d, Y'); ?>
                                    </span>

                                </div>

                                <h3 class="Post-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>

                                <div class="Post-desc">
                                    <?php echo wp_trim_words(get_the_excerpt(), 22); ?>
                                </div>

                                <a href="<?php the_permalink(); ?>" class="Post-link">Learn more <iconify-icon
                                        icon="line-md:arrow-right"></iconify-icon> </a>

                            </div>

                        </article>
                    </div>

                <?php endwhile; ?>

            </div>
        <?php else: ?>
            <p class="Blog-empty">No articles found.</p>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

    </div>
</section>

<?php get_footer(); */ ?>