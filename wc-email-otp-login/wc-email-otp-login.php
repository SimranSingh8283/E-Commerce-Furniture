<?php
/**
 * Plugin Name: WooCommerce Email OTP Login (Custom UI)
 * Description: Email-only login with OTP verification + custom UI
 * Version: 2.1.0
 * Author: Gsptechnologies
 * Author URI: https://gsptechnologies.com/
 */

if (!defined('ABSPATH'))
    exit;

/* -------------------------------------------------
   ENQUEUE CSS & JS
------------------------------------------------- */
add_action('wp_enqueue_scripts', function () {

    wp_enqueue_style(
        'cw-email-otp-style',
        plugin_dir_url(__FILE__) . 'assets/css/login.css',
        [],
        '2.1'
    );

    wp_enqueue_script(
        'cw-email-otp-script',
        plugin_dir_url(__FILE__) . 'assets/js/otp.js',
        ['jquery'],
        '2.1',
        true
    );
});

/* -------------------------------------------------
   SHORTCODE
------------------------------------------------- */

add_action('template_redirect', function () {

    if (!is_user_logged_in()) {
        return;
    }

    if (!is_page()) {
        return;
    }

    global $post;

    if ($post && has_shortcode($post->post_content, 'wc_email_otp_login')) {
        wp_safe_redirect(wc_get_page_permalink('myaccount'));
        exit;
    }

});


add_shortcode('wc_email_otp_login', function () {

    if (is_user_logged_in()) {
        wp_redirect(wc_get_page_permalink('myaccount'));
        exit;
    }

    ob_start();
    wc_print_notices();
    ?>

    <div class="cw-login-wrapper">

        <div class="cw-login-left">
            <div class="cw-login-box">

                <?php if (isset($_GET['otp']) && $_GET['otp'] === 'verify' && isset($_GET['uid'])): ?>

                    <div class="Block-heading">
                        <h1 aria-level="1" data-level="1"><?php esc_html_e('Verify OTP Code', 'woocommerce'); ?></h1>
                        <p>Enter the code we sent to your email to confirm your identity and continue securely.</p>
                    </div>

                    <form method="post" class="cw-otp-form">
                        <div class="cw-otp-inputs">
                            <?php for ($i = 0; $i < 6; $i++): ?>
                                <input type="text" maxlength="1" required>
                            <?php endfor; ?>
                        </div>

                        <input type="hidden" name="cw_otp">
                        <input type="hidden" name="cw_user_id" value="<?php echo esc_attr($_GET['uid']); ?>">

                        <button type="submit" name="cw_verify_otp">Verify</button>
                    </form>

                    <p class="cw-resend" style="margin-top: 1rem;">
                        Didn’t receive code?
                        <a href="<?php echo esc_url(remove_query_arg(['otp', 'uid'])); ?>">Resend Code</a>
                    </p>

                <?php else: ?>
                    <div class="Block-heading">
                        <h1 aria-level="1" data-level="1"><?php esc_html_e('Login', 'woocommerce'); ?></h1>
                        <p>Welcome back to your furniture haven, where timeless design and everyday comfort come
                            together to create the home you’ve always imagined.</p>
                    </div>

                    <form method="post">
                        <label>Email *</label>
                        <input type="email" name="cw_email" placeholder="Enter email address" required>
                        <button type="submit" class="Button-root" name="cw_email_login">Continue</button>
                    </form>

                <?php endif; ?>

            </div>
        </div>

    </div>

    <?php
    return ob_get_clean();
});

/* -------------------------------------------------
   EMAIL → SEND OTP
------------------------------------------------- */
add_action('init', function () {

    if (!isset($_POST['cw_email_login']))
        return;

    $email = sanitize_email($_POST['cw_email']);

    if (!is_email($email) || !email_exists($email)) {
        wc_add_notice('Invalid email address', 'error');
        return;
    }

    $user = get_user_by('email', $email);
    $otp = rand(100000, 999999);

    update_user_meta($user->ID, '_cw_otp', $otp);
    update_user_meta($user->ID, '_cw_otp_exp', time() + 300);

    wp_mail($email, 'Your Login OTP', "Your OTP is: $otp");

    wp_redirect(add_query_arg([
        'otp' => 'verify',
        'uid' => $user->ID
    ], wp_get_referer()));

    exit;
});

/* -------------------------------------------------
   VERIFY OTP & LOGIN
------------------------------------------------- */
add_action('init', function () {

    if (!isset($_POST['cw_verify_otp']))
        return;

    $uid = intval($_POST['cw_user_id']);
    $otp = sanitize_text_field($_POST['cw_otp']);

    if ($otp !== get_user_meta($uid, '_cw_otp', true)) {
        wc_add_notice('Invalid OTP', 'error');
        return;
    }

    wp_set_current_user($uid);
    wp_set_auth_cookie($uid);

    delete_user_meta($uid, '_cw_otp');
    delete_user_meta($uid, '_cw_otp_exp');

    wp_redirect(wc_get_page_permalink('myaccount'));
    exit;
});
