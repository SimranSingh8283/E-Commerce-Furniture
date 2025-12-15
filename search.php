<?php get_header(); ?>

<main class="Search-results">
    <?php if (have_posts()) : ?>
        <h1>Search Results</h1>

        <?php while (have_posts()) : the_post(); ?>
            <?php wc_get_template_part('content', 'product'); ?>
        <?php endwhile; ?>

    <?php else : ?>
        <p>No products found.</p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>