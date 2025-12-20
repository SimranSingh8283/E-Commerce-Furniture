document.addEventListener('DOMContentLoaded', function () {

    jQuery(document.body).on('added_to_cart', function (event, fragments, cart_hash, button) {

        if (!button) return;

        const $btn = jQuery(button);
        const cartUrl = wc_add_to_cart_params.cart_url;

        $btn.find('.Button-text').text('View Cart');

        $btn.removeClass('ajax_add_to_cart add_to_cart_button')
            .addClass('Button-cart-added');

        $btn.attr('href', cartUrl);

    });

});