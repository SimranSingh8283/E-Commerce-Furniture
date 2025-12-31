<?php
/**
 * Header Brand / Logo
 * Variables passed:
 * $logo, $logo_alt, $site_url
 */

$logo = $args['logo'] ?? '';
$logo_alt = $args['logo_alt'] ?? '';
$site_url = $args['site_url'] ?? '';
?>

<a href="<?= esc_url($site_url); ?>" class="Navbar-brand">
    <img src="<?= esc_url($logo); ?>" alt="<?= esc_attr($logo_alt); ?>">
</a>