
jQuery(function ($) {
    $('.Product-wishlist').each(function () {
        syncWishlistUI($(this));
    });

    function syncWishlistUI($wrap) {
        const $nativeBtn = $wrap.find('.woosw-btn');
        const $btn = $wrap.find('.Button-wishlist');

        if ($nativeBtn.hasClass('woosw-added')) {
            $btn
                .addClass('is-added')
                .attr({
                    'data-tooltip': 'View Wishlist',
                    'aria-label': 'View Wishlist'
                })
                .html('<iconify-icon icon="mdi:heart"></iconify-icon>');
        }
    }

    $('.Product-wishlist').on('click', '.Button-wishlist', function (e) {
        e.preventDefault();

        const $btn = $(this);
        const $wrap = $btn.closest('.Product-wishlist');
        const $nativeBtn = $wrap.find('.woosw-btn');

        $nativeBtn.trigger('click');

        if (!$btn.hasClass('is-added')) {
            $btn.addClass('Button-loading');
        }
    });

    const observer = new MutationObserver(mutations => {
        mutations.forEach(m => {
            if (
                m.target.classList &&
                m.target.classList.contains('woosw-btn') &&
                m.target.classList.contains('woosw-added')
            ) {
                const $nativeBtn = $(m.target);
                const $wrap = $nativeBtn.closest('.Product-wishlist');
                const $btn = $wrap.find('.Button-wishlist');

                $btn
                    .removeClass('Button-loading')
                    .addClass('is-added')
                    .attr({
                        'data-tooltip': 'View Wishlist',
                        'aria-label': 'View Wishlist'
                    })
                    .html('<iconify-icon icon="mdi:heart"></iconify-icon>');
            }
        });
    });

    $('.woosw-btn').each(function () {
        observer.observe(this, { attributes: true, attributeFilter: ['class'] });
    });

});