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
$yt = get_theme_mod('youtube_link');
$phone = get_theme_mod('phone_number');
$email = get_theme_mod('email_address');
$address = get_theme_mod('physical_address');
?>

<?php if (!is_404()): ?>
    </main>

    <footer id="Footer-root" class="Footer-root" aria-label="Site footer with quick-links and contact information">
        <div class="container">
            <div class="Footer-copyrights text-center">
                <div class="payments">
                    <img src="<?= $site_url?>/wp-content/uploads/2025/12/visa-1.png" alt="">
                    <img src="<?= $site_url?>/wp-content/uploads/2025/12/gpay.png" alt="">
                    <img src="<?= $site_url?>/wp-content/uploads/2025/12/apple-pay.png" alt="">
                    <img src="<?= $site_url?>/wp-content/uploads/2025/12/discover_payment_method1.png" alt="">
                    <img src="<?= $site_url?>/wp-content/uploads/2025/12/paypal.png" alt="">
                    <img src="<?= $site_url?>/wp-content/uploads/2025/12/paypal.png" alt="">
                </div>

                <div class="copy">
                    <span>© <?= date("Y") ?>, Copyright, All rights reserved. </span>
                    <a href="#">Refund policy</a>
                    <a href="#">Privacy policy</a>
                    <a href="#">Terms of service</a>
                </div>
            </div>
        </div>
    </footer>

<?php endif; ?>

<?php wp_footer(); ?>

</body>

</html>