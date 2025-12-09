<?php
get_header();

$base_url = site_url();
$template_uri = get_template_directory_uri();
?>

<style>
    .page-404 {
        padding-block: var(--section-padding);
    }

    .page-404 .banner-404 img {
        width: 100%;
    }

    .page-404 .content-404 {
        max-width: 85ch;
        margin-block-start: 3rem;
        text-align: center;
        margin-inline: auto;
    }

    .page-404 .content-404 .title>* {
        font-size: clamp(2rem, 3.5vw, 3.5rem);
        margin-block-end: 0.5em;
    }

    .page-404 .content-404 .text {
        font-size: clamp(1rem, 1.5vw, 1.5rem);
    }
</style>

<section id="page-404" class="page-404">
    <div class="banner-404">
        <img src="<?= $template_uri ?>/404-plug.png" alt="">
    </div>

    <div class="container">

        <div class="content-404">
            <div class="title">
                <h1>Page Not Found!</h1>
            </div>

            <div class="text">
                <p>Sorry, but the page you are looking for does not exist.</p>
            </div>

            <div class="action">
                <a href="<?php echo site_url() ?>" class="btn btn-ripple" data-variant="contained">Back To Home</a>
            </div>
        </div>

    </div>
</section>
<?php get_footer(); ?>