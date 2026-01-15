jQuery(function ($) {

    /* =====================================
       ADD FROM PRODUCT PAGE (ADD ONLY)
    ===================================== */
    $(document).on('click', '.custom-compare-btn', function (e) {
        e.preventDefault();

        let productId = parseInt($(this).data('id'), 10);
        if (!productId) return;

        let selectedMeta = {};

        $('.fc-group').each(function () {
            let $checked = $(this).find('input[type="radio"]:checked');
            if (!$checked.length) return;

            selectedMeta[$checked.attr('name')] = $checked.val();
        });

        $.post(compareData.ajax_url, {
            action: 'add_to_compare',
            product_id: productId,
            selected_meta: selectedMeta
        }, function (res) {
            if (res.success) {
                alert('Product added to compare');
                window.location.assign('/furniture/compare');
            }
        });
    });


    /* =====================================
       ADD / REPLACE FROM MODAL
    ===================================== */
    $(document).on('click', '.compare-select-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let newProductId = parseInt($(this).data('id'), 10);
        let oldProductId = parseInt($('#compare-modal').data('replace'), 10) || 0;

        if (!newProductId) return;

        let selectedMeta = {};

        // ✅ READ META ONLY FROM THIS RESULT
        $(this).closest('.compare-result')
            .find('.fc-group')
            .each(function () {
                let checked = $(this).find('input[type="radio"]:checked');
                if (!checked.length) return;

                selectedMeta[checked.attr('name')] = checked.val();
            });

        let action = oldProductId > 0 ? 'replace_compare' : 'add_to_compare';

        let postData = {
            action: action,
            new_product_id: newProductId,
            product_id: newProductId,
            selected_meta: selectedMeta
        };

        if (oldProductId > 0) {
            postData.old_product_id = oldProductId;
        }

        $.post(compareData.ajax_url, postData, function (res) {
            if (res.success) {
                location.reload();
            }
        });
    });


    /* =====================================
       REMOVE FROM COMPARE
    ===================================== */
    $(document).on('click', '.compare-remove', function (e) {
        e.preventDefault();
        e.stopPropagation();

        $.post(compareData.ajax_url, {
            action: 'remove_from_compare',
            product_id: $(this).data('id')
        }, function () {
            location.reload();
        });
    });


    /* =====================================
       OPEN SEARCH MODAL
    ===================================== */
    $(document).on('click focus', '#compare-add, .compare-search', function () {
        $('#compare-modal').fadeIn();
        $('#compare-modal-search').focus();
        $('#compare-modal').data('replace', $(this).data('current') || 0);
    });
    $(document).on('click', '#close-modal', function () {
        $('#compare-modal').fadeOut();
    });
    $(document).on('click', '#compare-modal', function (e) {
        if (!$(e.target).closest('.compare-modal-content').length) {
            $('#compare-modal').fadeOut();
        }
    });


    /* =====================================
       SEARCH PRODUCTS
    ===================================== */
    $('#compare-modal-search').on('keyup', function () {

        let q = $(this).val().trim();

        if (q.length < 1) {
            $('#compare-modal-results').empty();
            return;
        }

        $.post(compareData.ajax_url, {
            action: 'compare_search',
            q: q
        }, function (res) {

            if (!res.success) {
                $('#compare-modal-results').html('<p>No products found</p>');
                return;
            }

            // PHP returns HTML
            $('#compare-modal-results').html(res.data);
        });
    });

});