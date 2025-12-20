<?php
/*
Template Name: Products
Template Post Type: page
*/
get_header();

$collection = isset($_GET['collection'])
    ? sanitize_text_field($_GET['collection'])
    : 'best_selling';
?>

<section class="Block-root Block-products Products-root">
    <div class="Container-root">

        <div class="Products-wrapper">
            <?php get_template_part('template-parts/products/categories'); ?>
            <?php get_template_part('template-parts/products/sale'); ?>

            <div class="Products-collection">
                <?php get_template_part(
                    'template-parts/products/collection',
                    null,
                    array('collection' => $collection)
                ); ?>
            </div>
        </div>

    </div>
</section>

<script>
document.addEventListener('click', function (e) {
    const chip = e.target.closest('.ProductsCollection-chips .Chip');
    if (!chip) return;

    const wrapper = document.querySelector('.Products-collection');
    const collection = chip.dataset.collection;

    const url = new URL(window.location);
    url.searchParams.set('collection', collection);
    history.pushState({}, '', url);

    wrapper.classList.add('is-loading');

    fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'load_product_collection',
            collection
        })
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            wrapper.innerHTML = res.data;
        }
        wrapper.classList.remove('is-loading');
    });
});

window.addEventListener('popstate', function () {
    const collection = new URLSearchParams(window.location.search).get('collection') || 'best_selling';
    const wrapper = document.querySelector('.Products-collection');

    wrapper.classList.add('is-loading');

    fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'load_product_collection',
            collection
        })
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            wrapper.innerHTML = res.data;
        }
        wrapper.classList.remove('is-loading');
    });
});
</script>

<?php get_footer(); ?>