jQuery(function ($) {

    /* ----------------------------
     * ALERT
     * ---------------------------- */
    function showAlert(message, actionUrl = null, actionText = '') {

        $('.cw-alert').remove();

        const actionBtn = actionUrl
            ? `<a href="${actionUrl}" class="cw-alert-btn">${actionText}</a>`
            : '';

        const html = `
            <div class="cw-alert">
                <span>${message}</span>
                ${actionBtn}
            </div>
        `;

        $('body').append(html);

        setTimeout(() => $('.cw-alert').addClass('show'), 50);

        setTimeout(() => {
            $('.cw-alert').removeClass('show');
            setTimeout(() => $('.cw-alert').remove(), 300);
        }, 4500);
    }

    /* ----------------------------
     * EMPTY WISHLIST UI
     * ---------------------------- */
    function handleEmptyWishlist(count) {
        if (count > 0) return;

        $('.cw-wishlist-table, .add-all').remove();

        if ($('.cw-empty').length) return;

        const shopUrl = cwWishlist.shop_url || '/shop';

        const emptyHtml = `
            <div class="cw-empty text-center">
                <div class="Block-heading">
                    <span aria-level="1" data-level="1">
                        No products in your wishlist.
                    </span>
                </div>

                <div class="Action-root">
                    <a href="${shopUrl}"
                       class="Button-root Button-primary"
                       data-variant="contained">
                        Go to shop
                    </a>
                </div>
            </div>
        `;

        $('.page-template-Wishlist .Wishlist-root .Container-root')
            .append(emptyHtml);
    }

    /* ----------------------------
     * WISHLIST ACTION
     * ---------------------------- */
    $(document).on('click', '.Button-wishlist, .cw-remove', function (e) {
        e.preventDefault();

        if (!cwWishlist.is_logged_in) {
            showAlert(
                'Please login to use the wishlist ❤️',
                cwWishlist.login_url,
                'Login'
            );
            return;
        }

        const $btn = $(this);

        // prevent double click
        if ($btn.hasClass('Button-loading')) return;

        const productId = $btn.data('id');

        $btn.addClass('Button-loading');

        $.post(cwWishlist.ajax_url, {
            action: 'cw_toggle_wishlist',
            product_id: productId
        }, function (res) {

            $btn.removeClass('Button-loading');

            if (!res || !res.success) return;

            showAlert(
                'Wishlist updated successfully ❤️',
                cwWishlist.wishlist_url,
                'View Wishlist'
            );

            if ($btn.hasClass('cw-remove')) {
                $btn.closest('tr').fadeOut(300, function () {
                    $(this).remove();
                });
            }

            document.dispatchEvent(new CustomEvent('cw:wishlist-updated', {
                detail: {
                    product_id: productId,
                    count: res.data.count,
                    action: res.data.action
                }
            }));

            handleEmptyWishlist(res.data.count);
        });
    });

});


document.addEventListener('cw:wishlist-updated', function (e) {
    const { product_id, action } = e.detail;

    const btn = document.querySelector(`.Button-wishlist[data-id="${product_id}"]`);
    if (!btn) return;

    const icon = btn.querySelector('iconify-icon');
    if (icon) {
        icon.setAttribute('icon', action === 'added' ? 'mdi:heart' : 'mdi:heart-outline');
    }

    const tooltipText = action === 'added' ? 'Remove From Wishlist' : 'Add to Wishlist';
    btn.setAttribute('data-tooltip', tooltipText);
    btn.setAttribute('aria-label', tooltipText);
});


jQuery(function ($) {
    $(document).on('change', '.quantity .qty', function () {
        const $btn = $(this).closest('.cw-td').find('.add_to_cart_button');;
        $btn.attr('data-quantity', $(this).val());
    });
});


(function ($) {
    function toggleWishlistTables() {
        var width = $(window).width();

        var desktopTable = $('.Block-wishlist .Table-root.cw-wishlist-table').not('.Table-cw-mobile');
        var mobileTable = $('.Block-wishlist .Table-root.Table-cw-mobile.cw-wishlist-table');

        if (width > 992) {
            // Show desktop, remove mobile
            desktopTable.show();
            mobileTable.remove();
        } else {
            // Show mobile, remove desktop
            mobileTable.show();
            desktopTable.remove();
        }
    }

    // Initial run
    $(document).ready(function () {
        toggleWishlistTables();
    });

    // On resize
    $(window).on('resize', function () {
        toggleWishlistTables();
    });

})(jQuery);
