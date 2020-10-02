function woo_gpf_sync_media_excluded_status(data) {
    jQuery('.woo-gpf-image-item-status-excluded').each(function (idx, elem) {
        var jElem = jQuery(elem);
        var media_id = jElem.data('media-id');
        if (data.includes(media_id)) {
            jElem.show();
        } else {
            jElem.hide();
        }
    });
    jQuery('.woo-gpf-image-item-status-included').each(function (idx, elem) {
        var jElem = jQuery(elem);
        var media_id = jElem.data('media-id');
        if (data.includes(media_id)) {
            jElem.hide();
        } else {
            jElem.show();
        }
    });
}

function woo_gpf_handle_media_inclusion(elem, action) {
    var media_id = elem.data('media-id');
    var product_id = elem.data('product-id');
    var nonce = elem.data('nonce');
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            action: action,
            media_id: media_id,
            product_id: product_id,
            nonce: nonce
        },
        success: function (data) {
            woo_gpf_sync_media_excluded_status(JSON.parse(data));
        },
        error: function (data) {
            console.log('FAILURE');
        }
    });
}

jQuery(function () {
    jQuery(document).on('click', '.wc_gpf_metabox h2', function (e) {
        var metabox = jQuery(this).parent('.wc_gpf_metabox');
        if (metabox.hasClass('closed')) {
            metabox.addClass('open').removeClass('closed');
            metabox.find('.wc_gpf_metabox_content').show()
        } else {
            metabox.addClass('closed').removeClass('open');
            metabox.find('.wc_gpf_metabox_content').hide();
        }
    });
    jQuery(document).on('click', '.woocommerce-gpf-manage-images', function (e) {
        var container = jQuery(this).parents('.woo-gpf-image-source-list-container');
        container.removeClass('woo-gpf-image-source-list-container-collapsed');
        jQuery('.woocommerce-gpf-collapse-images').show();
        jQuery('.woocommerce-gpf-manage-images').hide();
        e.preventDefault();
    });
    jQuery(document).on('click', '.woocommerce-gpf-collapse-images', function (e) {
        var container = jQuery(this).parents('.woo-gpf-image-source-list-container');
        container.addClass('woo-gpf-image-source-list-container-collapsed');
        jQuery('.woocommerce-gpf-collapse-images').hide();
        jQuery('.woocommerce-gpf-manage-images').show();
        e.preventDefault();
    });
    jQuery(document).on('click', '.woo-gpf-image-source-exclude-item', function (e) {
        woo_gpf_handle_media_inclusion(jQuery(this), 'woo_gpf_exclude_media');
    });
    jQuery(document).on('click', '.woo-gpf-image-source-include-item', function (e) {
        woo_gpf_handle_media_inclusion(jQuery(this), 'woo_gpf_include_media');
    });
});
