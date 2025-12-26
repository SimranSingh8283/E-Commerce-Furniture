jQuery(function ($) {
    function debounce(func, wait) {
        let timeout;
        return function () {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                func.apply(context, args);
            }, wait);
        };
    }

    const triggerUpdateCart = debounce(function ($form) {
        $form.find('button[name="update_cart"]').trigger('click');
    }, 300);

    $(document).on('change', 'form.woocommerce-cart-form input.qty', function () {
        var $oldInput = $(this);
        var cartItemKey = $oldInput.attr('name').match(/\[(.*?)\]/)[1];
        var $form = $oldInput.closest('form');

        triggerUpdateCart($form);
    });

    $(document).on('click', '.qty-minus, .qty-plus', function () {
        var $sibling = $(this).siblings('.quantity');
        var $input = $sibling.find('input.qty');

        var current = parseInt($input.val()) || 0;
        var min = parseInt($input.attr('min')) || 1;
        var max = parseInt($input.attr('max')) || 999;

        if ($(this).hasClass('qty-minus')) {
            if (current > min) $input.val(current - 1).trigger('change');
        } else {
            if (current < max) $input.val(current + 1).trigger('change');
        }
    });
});