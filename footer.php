<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

$site_url = site_url();
$template_uri = get_template_directory_uri();

$custom_logo_id = get_theme_mod('custom_logo');
$logo = wp_get_attachment_image_src($custom_logo_id, 'full')[0];
$logo_alt = get_post_meta($custom_logo_id, '_wp_attachment_image_alt', true);

$insta = get_theme_mod('instagram_link');
$fb = get_theme_mod('facebook_link');
$li = get_theme_mod('linkedin_link');
$x = get_theme_mod('x_link');
$phone = get_theme_mod('phone_number');
$email = get_theme_mod('email_address');
$address = get_theme_mod('physical_address');
?>

<?php if (!is_404()): ?>
    </main>

    <footer id="Footer-root" class="Footer-root" aria-label="Site footer with quick-links and contact information">
        <div class="Container-root">

            <div class="Flex-root Flex-wrap">
                <div class="Col-root Col-lg-4">
                    <div class="Widget-root">
                        <a class="branding">
                            <img src="<?= $site_url ?>/wp-content/uploads/2025/12/Group-1.png" alt="">
                        </a>

                        <div class="text">
                            <p>
                                Created with passion by dreamers, crafting experiences and stories that reflect our journey,
                                our values, and our love for what we do.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="Col-root Col-lg-2">
                    <div class="Widget-root">
                        <div class="Widget-heading">
                            <span>Quick links</span>
                        </div>

                        <ul class="Widget-list">
                            <li class="Widget-item">
                                <a href="#" class="Widget-link">Home</a>
                            </li>
                            <li class="Widget-item">
                                <a href="#" class="Widget-link">Blog</a>
                            </li>
                            <li class="Widget-item">
                                <a href="#" class="Widget-link">About</a>
                            </li>
                            <li class="Widget-item">
                                <a href="#" class="Widget-link">Compare</a>
                            </li>
                            <li class="Widget-item">
                                <a href="#" class="Widget-link">Furniture</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="Col-root Col-lg-2">
                    <div class="Widget-root">
                        <div class="Widget-heading">
                            <span>Company</span>
                        </div>

                        <ul class="Widget-list">
                            <li class="Widget-item">
                                <a href="#" class="Widget-link">Terms & Conditions</a>
                            </li>
                            <li class="Widget-item">
                                <a href="#" class="Widget-link">Privacy Policy</a>
                            </li>
                            <li class="Widget-item">
                                <a href="#" class="Widget-link">FAQs</a>
                            </li>
                            <li class="Widget-item">
                                <a href="#" class="Widget-link">Contact us</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="Col-root Col-lg-4">
                    <div class="Widget-root">
                        <div class="Widget-heading">
                            <span>Sign up to our newsletter</span>
                        </div>

                        <div class="Widget-body">
                            <form class="Form-newsletter">
                                <input type="email" placeholder="E-mail" />
                                <button class="Button-root Button-primary" data-variant="contained">Subscribe</button>
                            </form>
                        </div>
                    </div>

                    <div class="Widget-root Widget-socials">
                        <?php if (!empty($insta) || !empty($fb)) { ?>
                            <ul class="Social-root">
                                <?php if (!empty($fb)) { ?>
                                    <li class="Social-item" data-aos="zoom-in">
                                        <a class="Social-link" target="_blank" href="<?= $fb; ?>">
                                            <iconify-icon icon=""></iconify-icon>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if (!empty($li)) { ?>
                                    <li class="Social-item" data-aos="zoom-in">
                                        <a class="Social-link" target="_blank" href="<?= $li; ?>">
                                            <iconify-icon icon=""></iconify-icon>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if (!empty($x)) { ?>
                                    <li class="Social-item">
                                        <a class="Social-link" target="_blank" href="<?= $x; ?>">
                                            <iconify-icon icon="line-md:twitter-x"></iconify-icon>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if (!empty($insta)) { ?>
                                    <li class="Social-item" data-aos="zoom-in">
                                        <a class="Social-link" target="_blank" href="<?= $insta; ?>">
                                            <iconify-icon icon=""></iconify-icon>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>

        <div class="Footer-copyrights text-center">
            <div class="copy">
                <span>Copyright@<?= date("Y") ?> Scandura, All right reserved </span>
            </div>
        </div>
    </footer>

<?php endif; ?>

<?php wp_footer(); ?>

</body>

</html>