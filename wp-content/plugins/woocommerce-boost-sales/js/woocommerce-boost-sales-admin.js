'use strict';
jQuery(document).ready(function () {

    jQuery('.wbs_exclude_product').dependsOn({
        'input[name="_woocommerce_boost_sales[show_with_category]"]': {
            checked: true
        }
    });
    jQuery('.select_product_bundle').dependsOn({
        'input[name="_woocommerce_boost_sales[enable_cart_page]"]': {
            checked: true
        }
    });
    jQuery('.select_product_bundle_checkout').dependsOn({
        'input[name="_woocommerce_boost_sales[enable_checkout_page]"]': {
            checked: true
        }
    });
    jQuery('.crosssell_display_on').dependsOn({
        'select[name="_woocommerce_boost_sales[crosssell_display_on]"]': {
            values: ['1']
        }
    });
    jQuery('.wbs-products-in-category').dependsOn({
        'input[name="_woocommerce_boost_sales[show_with_category]"]': {
            checked: false
        }
    });
    /*Save Submit button*/
    let submit_button;
    jQuery('.wbs-submit').on('click', function (e) {
        submit_button = jQuery(this);
    })
    jQuery('.woocommerce-boost-sales').on('submit', 'form', function (e) {
        submit_button.addClass('loading');
    });
    /*Add row*/
    jQuery('.wbs-crosssell-price-rule-add').on('click', function () {
        let rows = jQuery('.wbs-crosssell-price-rule-row'),
            lastRow = rows.last(),
            min_price = 0;
        if (lastRow.length > 0) {
            min_price = parseInt(lastRow.find('.wbs-crosssell-bundle-price-from').val()) + 1;
            lastRow.find('.wbs-crosssell-bundle-price-from').prop('max', min_price - 1);
        }
        let newRow = lastRow.clone(),
            newRowPlusVal = parseInt(newRow.find('.wbs-crosssell-bundle-price-discount-value').val());
        newRow.find('.wbs-crosssell-bundle-price-from').val(min_price).prop('min', min_price).prop('max', '');
        jQuery('.wbs-crosssell-price-rule-container').append(newRow);
        recalculatePriceRange();
    });

    /*remove last row*/
    jQuery('.wbs-crosssell-price-rule-remove').on('click', function () {
        let rows = jQuery('.wbs-crosssell-price-rule-row'),
            lastRow = rows.last();
        if (rows.length > 1) {
            let prev = jQuery('.wbs-crosssell-price-rule-row').eq(rows.length - 2);
            lastRow.remove();
            if (rows.length > 2) {
                prev.find('.wbs-crosssell-bundle-price-from').prop('max', '');
            }

        } else {
            alert('Cannot remove more.')
        }
        recalculatePriceRange();
    });
    recalculatePriceRange();

    function recalculatePriceRange() {
        jQuery('.wbs-crosssell-bundle-price-from').unbind().on('change', function () {
            let rows = jQuery('.wbs-crosssell-price-rule-row'),
                current = jQuery(this).parent().parent(),
                value = parseInt(jQuery(this).val());
            let currentPos = rows.index(current),
                nextRow = rows.eq(currentPos + 1),
                prevRow = rows.eq(currentPos - 1);
            let max = parseInt(jQuery(this).prop('max')),
                min = parseInt(jQuery(this).prop('min'));
            if (value < min) {
                value = min;
                jQuery(this).val(value);
            } else if (value > max) {
                value = max;
                jQuery(this).val(value);
            }
            if (currentPos > 1) {
                prevRow.find('.wbs-crosssell-bundle-price-from').prop('max', value - 1);
            }
            if (nextRow.length > 0) {
                nextRow.find('.wbs-crosssell-bundle-price-from').prop('min', value + 1);
            }
        });
    }

    /**
     * Start Get download key
     */
    jQuery('.villatheme-get-key-button').one('click', function (e) {
        let v_button = jQuery(this);
        v_button.addClass('loading');
        let data = v_button.data();
        let item_id = data.id;
        let app_url = data.href;
        let main_domain = window.location.hostname;
        main_domain = main_domain.toLowerCase();
        let popup_frame;
        e.preventDefault();
        let download_url = v_button.attr('data-download');
        popup_frame = window.open(app_url, "myWindow", "width=380,height=600");
        window.addEventListener('message', function (event) {
            /*Callback when data send from child popup*/
            let obj = jQuery.parseJSON(event.data);
            let update_key = '';
            let message = obj.message;
            let support_until = '';
            let check_key = '';
            if (obj['data'].length > 0) {
                for (let i = 0; i < obj['data'].length; i++) {
                    if (obj['data'][i].id == item_id && (obj['data'][i].domain == main_domain || obj['data'][i].domain == '' || obj['data'][i].domain == null)) {
                        if (update_key == '') {
                            update_key = obj['data'][i].download_key;
                            support_until = obj['data'][i].support_until;
                        } else if (support_until < obj['data'][i].support_until) {
                            update_key = obj['data'][i].download_key;
                            support_until = obj['data'][i].support_until;
                        }
                        if (obj['data'][i].domain == main_domain) {
                            update_key = obj['data'][i].download_key;
                            break;
                        }
                    }
                }
                if (update_key) {
                    check_key = 1;
                    jQuery('.villatheme-autoupdate-key-field').val(update_key);
                }
            }
            v_button.removeClass('loading');
            if (check_key) {
                jQuery('<p><strong>' + message + '</strong></p>').insertAfter(".villatheme-autoupdate-key-field");
                jQuery(v_button).closest('form').submit();
            } else {
                jQuery('<p><strong> Your key is not found. Please contact support@villatheme.com </strong></p>').insertAfter(".villatheme-autoupdate-key-field");
            }
        });
    });
    /**
     * End get download key
     */
});