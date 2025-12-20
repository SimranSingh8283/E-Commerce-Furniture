<article id="Block-PostSingle" class="Block-root Block-PostSingle">
    <div class="Container-root">

        <?php get_template_part('template-parts/post/header'); ?>
        <?php get_template_part('template-parts/post/excerpt'); ?>
        <?php get_template_part('template-parts/post/thumbnail'); ?>
        <?php get_template_part('template-parts/post/content'); ?>
        <?php get_template_part('template-parts/post/meta'); ?>

        <?php
        if (comments_open() || get_comments_number()) {
            comments_template('/template-parts/post/comments.php');
        }
        ?>

        <?php get_template_part('template-parts/post/related'); ?>

        <div class="PostSingle-newsletter">
            <?php echo do_shortcode('[elementor-template id="516"]'); ?>
        </div>

    </div>
</article>