function woo_gpf_sync_media_status(json_data) {
    var data               = JSON.parse(json_data);
    var excluded_media_ids = data.excluded_media_ids;
    var primary_media_id   = data.primary_media_id;
    jQuery('.woo-gpf-image-source-list-item').each(function (idx, elem) {
        var wrapper       = jQuery(elem);
        var actionWrapper = wrapper.children('.woo-gpf-img-actions-wrapper');
        var media_id      = actionWrapper.data('media-id');
        if (excluded_media_ids.includes(media_id)) {
            wrapper
                .removeClass('woo-gpf-image-source-list-item-included')
                .addClass('woo-gpf-image-source-list-item-excluded');
        } else {
            wrapper
                .removeClass('woo-gpf-image-source-list-item-excluded')
                .addClass('woo-gpf-image-source-list-item-included');

        }
        if (media_id === primary_media_id) {
            wrapper.addClass('woo-gpf-image-source-list-item-primary');
        } else {
            wrapper.removeClass('woo-gpf-image-source-list-item-primary');
        }
    });
}

function woo_gpf_handle_media_actions(elem, action, callback) {

    var nonce = elem.data('nonce');

    var wrapper    = elem.parents('.woo-gpf-img-actions-wrapper');
    var media_id   = wrapper.data('media-id');
    var product_id = wrapper.data('product-id');

    jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: action,
                        media_id: media_id,
                        product_id: product_id,
                        nonce: nonce
                    },
                    success: callback,
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
        woo_gpf_handle_media_actions(jQuery(this), 'woo_gpf_exclude_media', woo_gpf_sync_media_status);
    });
    jQuery(document).on('click', '.woo-gpf-image-source-include-item', function (e) {
        woo_gpf_handle_media_actions(jQuery(this), 'woo_gpf_include_media', woo_gpf_sync_media_status);
    });
    jQuery(document).on('click', '.woo-gpf-image-source-set-primary-item', function (e) {
        woo_gpf_handle_media_actions(jQuery(this), 'woo_gpf_set_primary_media', woo_gpf_sync_media_status);
    });
});
