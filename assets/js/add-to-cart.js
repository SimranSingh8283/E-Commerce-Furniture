document.addEventListener('DOMContentLoaded', function () {

    jQuery(document.body).on('added_to_cart', function (event, fragments, cart_hash, button) {

        if (!button) return;

        const $btn = jQuery(button);
        const cartUrl = wc_add_to_cart_params.cart_url;

        $btn.find('.Button-text').text('View Cart');

        const $icon = $btn.find('iconify-icon');
        if ($icon.length) {
            $icon.attr('icon', 'mdi:cart');
        }

        $btn.removeClass('ajax_add_to_cart add_to_cart_button')
            .addClass('Button-cart-added');

        $btn.attr('href', cartUrl);

    });

});


document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".Attribute-group").forEach(function (group) {

        group.querySelectorAll(".Attribute-root").forEach(function (attrRoot) {
            const options = attrRoot.querySelectorAll(".Swatch-root input");

            const checkedInput = attrRoot.querySelector(".Swatch-root input:checked");
            if (checkedInput) {
                const parentLabel = checkedInput.closest(".Swatch-root");
                parentLabel.classList.add("Swatch-selected");

                const titleDiv = attrRoot.querySelector(".Attribute-title");
                let labelDiv = titleDiv.querySelector(".Attribute-label");
                if (!labelDiv) {
                    labelDiv = document.createElement("div");
                    labelDiv.className = "Attribute-label";
                    titleDiv.appendChild(labelDiv);
                }
                labelDiv.textContent = parentLabel.querySelector(".Swatch-name").textContent;
            }

            options.forEach(function (input) {
                input.addEventListener("change", function () {
                    attrRoot.querySelectorAll(".Swatch-root").forEach(function (swatch) {
                        swatch.classList.remove("Swatch-selected");
                    });

                    const newSelected = input.closest(".Swatch-root");
                    newSelected.classList.add("Swatch-selected");

                    const titleDiv = attrRoot.querySelector(".Attribute-title");
                    let labelDiv = titleDiv.querySelector(".Attribute-label");
                    if (!labelDiv) {
                        labelDiv = document.createElement("div");
                        labelDiv.className = "Attribute-label";
                        titleDiv.appendChild(labelDiv);
                    }
                    labelDiv.textContent = newSelected.querySelector(".Swatch-name").textContent;
                });
            });
        });
    });
});

jQuery(function ($) {
    // WooCommerce triggers 'adding_to_cart' before AJAX add-to-cart
    $(document.body).on('click', 'a.ajax_add_to_cart', function (e) {
        const $btn = $(this);
        const $product = $btn.closest('.Product-root');

        let extraData = {};

        $product.find('input[type="radio"]:checked').each(function () {
            const $input = $(this);
            const name = $input.attr('name');
            const value = $input.val();

            if (name && value) {
                extraData[name] = value;
            }
        });

        $btn.data('extra_params', extraData);
    });

    $(document.body).on('adding_to_cart', function (e, $btn, data) {
        const extraParams = $btn.data('extra_params') || {};
        Object.keys(extraParams).forEach(key => {
            data[key] = extraParams[key];
        });
    });
});

