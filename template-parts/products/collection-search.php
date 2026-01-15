<?php
if (!defined('ABSPATH'))
    exit;

$template_uri = get_template_directory_uri();

$collections = [
    'all' => [
        'label' => 'All Items',
        'title' => 'All Items',
        'description' => 'Explore our complete furniture collection.',
    ],
    'best_selling' => [
        'label' => 'Best Selling Items',
        'title' => 'Best Selling Items',
        'description' => 'Explore our collection of thoughtfully crafted pieces.',
    ],
    'on_sale' => [
        'label' => 'On Sale',
        'title' => 'On Sale',
        'description' => 'Limited-time deals you don’t want to miss.',
    ],
];

$args = wp_parse_args($args ?? [], [
    'collection' => 'all',
    'show_chips' => true,
    'collections' => $collections ?? [],
]);

$collection = $args['collection'];
$show_chips = $args['show_chips'];

if (!$collections || !isset($collections[$collection])) {
    return;
}
?>

<div class="ProductsCollection-form">
    <form method="get" action="<?php echo esc_url(get_permalink()); ?>" class="Form-root Form-ProductsCollection">

        <button type="submit" data-tooltip="Search" class="Button-root Button-icon">
            <img src="<?php echo esc_url($template_uri); ?>/assets/media/search.svg" alt="" />
        </button>

        <input type="search" name="prod_search" placeholder="Search..."
            value="<?php echo esc_attr($_GET['prod_search'] ?? ''); ?>">
    </form>

    <?php /* if ($show_chips): ?>
   <div class="ProductsCollection-chips">
       <?php foreach ($collections as $key => $data): ?>
           <button type="button" class="Chip <?php echo $collection === $key ? 'active' : ''; ?>"
               data-collection="<?php echo esc_attr($key); ?>">
               <?php echo esc_html($data['label']); ?>
           </button>
       <?php endforeach; ?>
   </div>
<?php endif; */ ?>

    <?php if ($show_chips): ?>
        <div class="ProductsCollection-chips">
            <?php foreach ($collections as $key => $data): ?>

                <?php
                $url = add_query_arg(
                    'collection',
                    $key,
                    remove_query_arg('paged')
                );
                ?>

                <a href="<?php echo esc_url($url); ?>" class="Chip <?php echo $collection === $key ? 'active' : ''; ?>">
                    <?php echo esc_html($data['label']); ?>
                </a>

            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>