<?php get_header(); ?>

<main class="Search-results">
    <h1>Search Results</h1>

    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article>
                <h2><?php the_title(); ?></h2>
                <?php the_excerpt(); ?>
            </article>
        <?php endwhile; ?>
    <?php else : ?>
        <p>No results found.</p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
