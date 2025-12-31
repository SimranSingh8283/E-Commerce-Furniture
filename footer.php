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

$quick_links_arg = array('menu' => 'Quick Links', 'menu_class' => 'Navbar-nav-quickLinks Widget-list', 'menu_id' => 'Navbar-nav-quickLinks', 'container' => 'ul', 'container_class' => '', 'container_id' => '', 'add_li_class' => 'Navbar-item', 'add_a_class' => 'Navbar-link');
$company_links_arg = array('menu' => 'Company Links', 'menu_class' => 'Navbar-nav-companyLinks Widget-list', 'menu_id' => 'Navbar-nav-companyLinks', 'container' => 'ul', 'container_class' => '', 'container_id' => '', 'add_li_class' => 'Navbar-item', 'add_a_class' => 'Navbar-link');


$socials = [
    'facebook' => [
        'url' => $fb ?? '',
        'icon' => 'line-md:facebook'
    ],
    'x' => [
        'url' => $x ?? '',
        'icon' => 'line-md:twitter-x',
    ],
    'linkedin' => [
        'url' => $li ?? '',
        'icon' => 'line-md:linkedin',
    ],
    'instagram' => [
        'url' => $insta ?? '',
        'icon' => 'line-md:instagram',
    ],
];

$has_socials = array_filter(array_column($socials, 'url'));
?>

<?php if (!ThemeFunctions::hide_layout_elements()): ?>
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

                        <?php wp_nav_menu($quick_links_arg); ?>
                    </div>
                </div>

                <div class="Col-root Col-lg-2">
                    <div class="Widget-root">
                        <div class="Widget-heading">
                            <span>Company</span>
                        </div>

                        <?php wp_nav_menu($company_links_arg); ?>
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
                        <?php if (!empty($has_socials)): ?>
                            <ul class="Social-root">
                                <?php foreach ($socials as $network => $data): ?>
                                    <?php if (!empty($data['url'])): ?>
                                        <li class="Social-item" data-aos="zoom-in">
                                            <a class="Social-link" href="<?= esc_url($data['url']); ?>" target="_blank"
                                                rel="noopener noreferrer" aria-label="<?= esc_attr(ucfirst($network)); ?>">
                                                <iconify-icon icon="<?= esc_attr($data['icon']); ?>"></iconify-icon>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
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

<script>
    jQuery(function ($) {
        if (typeof wc_cart_params === 'undefined') return;
        function refreshCartBadge() {
            $.ajax({
                url: wc_cart_params.ajax_url,
                type: 'POST',
                data: { action: 'woocommerce_get_refreshed_fragments' },
                success: function (data) {
                    if (data && data.fragments) {
                        $.each(data.fragments, function (key, value) {
                            $(key).replaceWith(value);
                        });
                    }
                }
            });
        }

        $(document.body).on('added_to_cart removed_from_cart updated_wc_div', refreshCartBadge);
    });

    jQuery(function ($) {

        $(document.body).on('adding_to_cart', function (e, button) {
            $(button).addClass('Button-loading');
        });

        $(document.body).on('added_to_cart', function (e, fragments, cart_hash, button) {
            $(button)
                .removeClass('Button-loading loading')
                .addClass('Button-cart-added');
        });

        $(document.body).on('wc_fragments_refreshed', function () {
            $('.Product-addToCart')
                .removeClass('Button-loading loading');
        });

        $(document).ajaxComplete(function () {
            $('.Product-addToCart')
                .removeClass('Button-loading loading');
        });

    });
</script>

</body>

</html>