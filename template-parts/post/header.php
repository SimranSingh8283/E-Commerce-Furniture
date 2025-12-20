<div class="PostSingle-header">
    <div class="Post-meta">
        <?php get_template_part('template-parts/post/tags'); ?>
        
        <span class="Post-date">
            <?php echo get_the_date('M d, Y'); ?>
        </span>
    </div>

    <h1 class="Post-title"><?php the_title(); ?></h1>
</div>