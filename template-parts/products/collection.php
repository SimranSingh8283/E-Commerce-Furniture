<?php
if (!defined('ABSPATH')) exit;

$collection = $args['collection'] ?? 'best_selling';

$collections = array(
    'best_selling' => array(
        'label' => 'Best Selling Items',
        'title' => 'Best Selling Items',
        'description' => 'Our most loved products, chosen by customers.',
    ),
    'on_sale' => array(
        'label' => 'On Sale',
        'title' => 'On Sale',
        'description' => 'Limited-time deals you don’t want to miss.',
    ),
    'all' => array(
        'label' => 'All Items',
        'title' => 'All Items',
        'description' => 'Explore our complete furniture collection.',
    ),
);

if (!isset($collections[$collection])) {
    $collection = 'best_selling';
}
?>

<div class="ProductsCollection-chips">
    <?php foreach ($collections as $key => $data): ?>
        <button
            type="button"
            class="Chip <?php echo $collection === $key ? 'active' : ''; ?>"
            data-collection="<?php echo esc_attr($key); ?>">
            <?php echo esc_html($data['label']); ?>
        </button>
    <?php endforeach; ?>
</div>

<h2 class="ProductsCollection-title">
    <?php echo esc_html($collections[$collection]['title']); ?>
</h2>

<p class="ProductsCollection-desc">
    <?php echo esc_html($collections[$collection]['description']); ?>
</p>