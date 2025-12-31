<?php
/**
 * Wishlist JS Script
 */
?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    function syncWishlistBadge() {
        const menuSpan = document.querySelector('.woosw-menu-item-inner');
        const badge = document.querySelector('.Button-wishlist');
        if (!menuSpan || !badge) return;

        const count = parseInt(menuSpan.dataset.count || 0, 10);
        const badgeWrapper = badge.closest('.Badge-root');
        if (badgeWrapper && badgeWrapper.getAttribute('data-value') != count) {
            badgeWrapper.setAttribute('data-value', count);
        }

        let tooltip = "Your wishlist is empty";
        if (count === 1) tooltip = "1 item in wishlist";
        else if (count > 1) tooltip = `${count} items in wishlist`;

        if (badge.getAttribute('data-tooltip') !== tooltip) {
            badge.setAttribute('data-tooltip', tooltip);
        }
    }

    const observer = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            if (mutation.type === 'childList' && document.querySelector('.woosw-menu-item-inner')) {
                syncWishlistBadge();
                break;
            }
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });
    syncWishlistBadge();

    document.addEventListener('woosw_change_count', syncWishlistBadge);
    document.addEventListener('woosw_update_fragments', syncWishlistBadge);
    document.addEventListener('woosw_loaded', syncWishlistBadge);
});
</script>