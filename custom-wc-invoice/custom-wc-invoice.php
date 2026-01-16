<?php
/**
 * Plugin Name: Custom WooCommerce Invoice PDF
 * Description: Generate & download invoice PDF on Thank You page (Block + Classic)
 * Version: 1.1
 * Author: Gsptechnologies
 * Author URI: https://gsptechnologies.com/
 */

if (!defined('ABSPATH')) exit;

/* ----------------------------------------
   LOAD DOMPDF SAFELY
---------------------------------------- */
$dompdf_autoload = __DIR__ . '/lib/dompdf/autoload.inc.php';
if (!file_exists($dompdf_autoload)) {
    return; // Dompdf not installed
}

require_once $dompdf_autoload;
use Dompdf\Dompdf;


/* ----------------------------------------
   ADMIN SETTINGS (WooCommerce → Invoice Settings)
---------------------------------------- */
add_action('admin_menu', function () {
    add_submenu_page(
        'woocommerce',
        'Invoice Settings',
        'Invoice Settings',
        'manage_options',
        'cw-invoice-settings',
        'cw_invoice_settings_page'
    );
});

add_action('admin_init', function () {
    register_setting('cw_invoice_settings', 'cw_invoice_company_name');
    register_setting('cw_invoice_settings', 'cw_invoice_company_address');
    register_setting('cw_invoice_settings', 'cw_invoice_company_phone');
    register_setting('cw_invoice_settings', 'cw_invoice_company_email');
    register_setting('cw_invoice_settings', 'cw_invoice_footer_note');
});

function cw_invoice_settings_page() {
?>
<div class="wrap">
    <h1>Invoice Settings</h1>
    <form method="post" action="options.php">
        <?php settings_fields('cw_invoice_settings'); ?>
        <table class="form-table">
            <tr>
                <th>Company Name</th>
                <td><input type="text" class="regular-text"
                    name="cw_invoice_company_name"
                    value="<?php echo esc_attr(get_option('cw_invoice_company_name')); ?>"></td>
            </tr>
            <tr>
                <th>Company Address</th>
                <td><textarea rows="4" class="large-text"
                    name="cw_invoice_company_address"><?php
                    echo esc_textarea(get_option('cw_invoice_company_address'));
                ?></textarea></td>
            </tr>
            <tr>
                <th>Phone</th>
                <td><input type="text" class="regular-text"
                    name="cw_invoice_company_phone"
                    value="<?php echo esc_attr(get_option('cw_invoice_company_phone')); ?>"></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><input type="email" class="regular-text"
                    name="cw_invoice_company_email"
                    value="<?php echo esc_attr(get_option('cw_invoice_company_email')); ?>"></td>
            </tr>
            <tr>
                <th>Footer Note</th>
                <td><textarea rows="3" class="large-text"
                    name="cw_invoice_footer_note"><?php
                    echo esc_textarea(get_option('cw_invoice_footer_note'));
                ?></textarea></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
<?php
}


/* ----------------------------------------
   SHOW DOWNLOAD BUTTON (THANK YOU PAGE)
---------------------------------------- */
function cw_add_invoice_button($order_id) {

    if (!$order_id) {
        return;
    }

    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }

    $url = add_query_arg([
        'cw_download_invoice' => 1,
        'order_id'            => $order_id,
        'order_key'           => $order->get_order_key(),
    ], site_url('/'));

    echo '<p style="margin:30px 0;">';
    echo '<a class="button" href="' . esc_url($url) . '">
            Download Invoice (PDF)
          </a>';
    echo '</p>';
}

/* Classic checkout */
// add_action('woocommerce_thankyou', 'cw_add_invoice_button', 3);

/* Block checkout */
// add_action('woocommerce_blocks_order_confirmation', 'cw_add_invoice_button', 3);

/* ----------------------------------------
   GENERATE PDF
---------------------------------------- */
add_action('init', 'cw_generate_invoice_pdf');
function cw_generate_invoice_pdf() {

    if (!isset($_GET['cw_download_invoice'])) {
        return;
    }

    $order_id  = intval($_GET['order_id'] ?? 0);
    $order_key = sanitize_text_field($_GET['order_key'] ?? '');

    if (!$order_id || !$order_key) {
        wp_die('Invalid request');
    }

    $order = wc_get_order($order_id);
    if (!$order || $order->get_order_key() !== $order_key) {
        wp_die('Invalid order');
    }


 /* SETTINGS */
    $company_name    = get_option('cw_invoice_company_name', 'Your Company Name');
    $company_address = nl2br(get_option('cw_invoice_company_address', 'Company Address'));
    $company_phone   = get_option('cw_invoice_company_phone');
    $company_email   = get_option('cw_invoice_company_email');
    $footer_note     = get_option(
        'cw_invoice_footer_note',
        'This is a computer-generated invoice and does not require a signature.'
    );
    /* ---------- INVOICE HTML ---------- */
   ob_start();
?>
<!DOCTYPE html>
<html>
<head>
<style>
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    color: #333;
}

.header {
    width: 100%;
    border-bottom: 2px solid #000;
    margin-bottom: 20px;
    padding-bottom: 10px;
}

.company {
    float: left;
    width: 60%;
}

.invoice-details {
    float: right;
    width: 40%;
    text-align: right;
}

.clearfix { clear: both; }

h1 {
    margin: 0;
    font-size: 24px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th {
    background: #f2f2f2;
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

td {
    border: 1px solid #ddd;
    padding: 8px;
}

.text-right {
    text-align: right;
}

.footer { margin-top:30px; font-size:11px; text-align:center; color:#666; }

.summary-table td {
    border: none;
    padding: 5px;
}

.footer {
    margin-top: 30px;
    font-size: 11px;
    color: #666;
    text-align: center;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div class="company">
         <h2><?php echo esc_html($company_name); ?></h2>
        <p>
            <?php echo wp_kses_post($company_address); ?><br>
            <?php if ($company_phone): ?>Phone: <?php echo esc_html($company_phone); ?><br><?php endif; ?>
            <?php if ($company_email): ?>Email: <?php echo esc_html($company_email); ?><?php endif; ?>
        </p>
    </div>

    <div class="invoice-details">
        <strong>Invoice #:</strong> <?php echo esc_html($order->get_id()); ?><br>
        <strong>Invoice Date:</strong> <?php echo esc_html(wc_format_datetime($order->get_date_created())); ?><br>
        <strong>Order ID:</strong> <?php echo esc_html($order->get_order_number()); ?>
    </div>

    <div class="clearfix"></div>
</div>

<!-- BILL TO -->
<p>
<strong>Bill To:</strong><br>
<?php echo esc_html($order->get_formatted_billing_full_name()); ?><br>
<?php echo nl2br(esc_html($order->get_billing_address_1())); ?><br>
<?php echo esc_html($order->get_billing_city()); ?>,
<?php echo esc_html($order->get_billing_postcode()); ?><br>
<?php echo esc_html($order->get_billing_email()); ?>
</p>

<!-- ITEMS TABLE -->
<table>
<thead>
<tr>
    <th>Product</th>
    <th class="text-right">Qty</th>
    <th class="text-right">Price</th>
    <th class="text-right">Total</th>
</tr>
</thead>
<tbody>
<?php foreach ($order->get_items() as $item): ?>
<tr>
    <td><?php echo esc_html($item->get_name()); ?></td>
    <td class="text-right"><?php echo esc_html($item->get_quantity()); ?></td>
    <td class="text-right"><?php echo html_entity_decode(wc_price($item->get_total() / $item->get_quantity())); ?></td>
    <td class="text-right"><?php echo html_entity_decode(wc_price($item->get_total())); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<!-- TOTALS -->
<table class="summary-table" style="width: 40%; float: right; margin-top: 15px;">
<tr>
    <td>Subtotal:</td>
    <td class="text-right"><?php echo html_entity_decode(wc_price($order->get_subtotal())); ?></td>
</tr>
<tr>
    <td>Tax:</td>
    <td class="text-right"><?php echo html_entity_decode(wc_price($order->get_total_tax())); ?></td>
</tr>
<tr>
    <td>Shipping:</td>
    <td class="text-right"><?php echo html_entity_decode(wc_price($order->get_shipping_total())); ?></td>
</tr>
<tr>
    <td><strong>Grand Total:</strong></td>
    <td class="text-right"><strong><?php echo html_entity_decode(wc_price($order->get_total())); ?></strong></td>
</tr>
</table>

<div class="clearfix"></div>

<!-- PAYMENT INFO -->
<p style="margin-top: 20px;">
<strong>Payment Method:</strong>
<?php echo esc_html($order->get_payment_method_title()); ?>
</p>

<!-- FOOTER -->

<div class="footer"><?php echo esc_html($footer_note); ?></div>


</body>
</html>
<?php
$html = ob_get_clean();


    /* ---------- CREATE PDF ---------- */
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $dompdf->stream(
        'invoice-' . $order_id . '.pdf',
        ['Attachment' => true]
    );

    exit;
}
