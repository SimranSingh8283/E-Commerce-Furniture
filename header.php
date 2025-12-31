<?php
/**
 * The header file
 */

$custom_logo_id = get_theme_mod('custom_logo');
$logo = wp_get_attachment_image_src($custom_logo_id, 'full')[0] ?? '';
$logo_alt = get_post_meta($custom_logo_id, '_wp_attachment_image_alt', true) ?? '';
$site_url = site_url();
$template_uri = get_template_directory_uri();
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php wp_get_document_title(); ?></title>
    <?php wp_head(); ?>

    <script src="https://unpkg.com/lenis@1.0.45/dist/lenis.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.0/iconify-icon.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <?php if (!ThemeFunctions::hide_layout_elements()): ?>

        <header id="Header-root" class="Header-root" data-aos="slide-down"
            aria-label="Main Navigation with contact information">
            <div class="Container-root">

                <nav class="Navbar-root">
                    <?php
                    get_template_part('template-parts/header/header', 'brand', [
                        'logo' => $logo,
                        'logo_alt' => $logo_alt,
                        'site_url' => $site_url
                    ]);

                    get_template_part('template-parts/header/header', 'menu');

                    get_template_part('template-parts/header/header', 'actions', [
                        'template_uri' => $template_uri
                    ]);
                    ?>
                </nav>

                <?php
                get_template_part('template-parts/header/header', 'search', [
                    'template_uri' => $template_uri
                ]);
                ?>

            </div>
        </header>

        <?php
        get_template_part('template-parts/header/header', 'drawer', [
            'logo' => $logo,
            'logo_alt' => $logo_alt,
            'site_url' => $site_url
        ]);
        ?>

        <main id="Main-root" class="Main-root" aria-label="Main part of the Website">
        <?php endif; ?>