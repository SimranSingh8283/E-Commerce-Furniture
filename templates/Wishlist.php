<?php
/*
Template Name: Wishlist
*/
defined('ABSPATH') || exit;

get_header();
?>

<section class="Block-root Block-wishlist Wishlist-root">
    <div class="Container-root">

        <?php echo do_shortcode('[woosw_list]'); ?>

    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const table = document.querySelector('.woosw-list table');
        if (!table) return;

        if (!table.querySelector('thead')) {

            const thead = document.createElement('thead');
            const row = document.createElement('tr');

            const headers = ['Remove', 'Product', 'Price', 'Action'];

            headers.forEach(text => {
                const th = document.createElement('th');
                th.textContent = text;
                row.appendChild(th);
            });

            thead.appendChild(row);
            table.prepend(thead);
        }

        const styleButton = (btn) => {
            btn.classList.remove('button', 'product_type_simple', 'added_to_cart');
            btn.classList.add('Button-root', 'Button-primary');
            btn.setAttribute('data-variant', 'contained');
        };

        table.querySelectorAll('.add_to_cart_button').forEach(styleButton);
        table.querySelectorAll('.added_to_cart').forEach(styleButton);

        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {

                    if (!(node instanceof HTMLElement)) return;

                    if (node.matches('.added_to_cart, .add_to_cart_button')) {
                        styleButton(node);
                    }

                    node.querySelectorAll?.('.added_to_cart, .add_to_cart_button')
                        .forEach(styleButton);
                });
            });
        });

        observer.observe(table, {
            childList: true,
            subtree: true
        });

    });
</script>


<?php get_footer(); ?>