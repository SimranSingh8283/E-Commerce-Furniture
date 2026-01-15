<?php
/**
 * Wishlist JS Script
 */
?>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        function updateWishlistBadge(count) {
            const badgeWrapper = document.querySelector('.Badge-root');
            if (!badgeWrapper) return;

            const badge = badgeWrapper.querySelector('.Button-wishlist');
            if (!badge) return;

            count = Number(count);

            if (badgeWrapper.getAttribute('data-value') != count) {
                badgeWrapper.setAttribute('data-value', count);
            }

            let tooltip = "Your wishlist is empty";
            if (count === 1) tooltip = "1 item in wishlist";
            else if (count > 1) tooltip = count + " items in wishlist";

            if (badge.getAttribute('data-tooltip') !== tooltip) {
                badge.setAttribute('data-tooltip', tooltip);
                badge.setAttribute('aria-label', tooltip);
            }
        }

        updateWishlistBadge(cwWishlist.count);

        document.addEventListener('cw:wishlist-updated', function (e) {
            updateWishlistBadge(e.detail.count);
        });

    });
</script>