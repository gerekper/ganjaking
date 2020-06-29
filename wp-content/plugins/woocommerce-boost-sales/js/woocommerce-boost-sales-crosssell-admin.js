'use strict';
jQuery(document).ready(function () {

    jQuery(".wbs-use-other-bundle").on('click',function () {
        let td=jQuery(this).parent().parent();
        if(jQuery(this).prop('checked')){
            td.find('.wbs-product-search-bundle-container').show();
            td.find('.product-search-crs-container').hide();
        }else{
            td.find('.wbs-product-search-bundle-container').hide();
            td.find('.product-search-crs-container').show();
        }
    })
    jQuery(".product-search-bundle").select2({
        closeOnSelect: false,
        placeholder: "Please enter product bundle title",
        ajax: {
            url: "admin-ajax.php?action=wbs_search_product_bundle",
            dataType: 'json',
            type: "GET",
            quietMillis: 50,
            delay: 250,
            data: function (params) {
                return {
                    keyword: params.term,
                    p_bundle_id: jQuery(this).closest('td').find('[name="_wbs_cross_sell_of"]').val()
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 1
    });

    jQuery(".product-search-crs").select2({
        closeOnSelect: false,
        placeholder: "Please fill in your product title",
        ajax: {
            url: "admin-ajax.php?action=wbs_search_product_crs",
            dataType: 'json',
            type: "GET",
            quietMillis: 50,
            delay: 250,
            data: function (params) {
                return {
                    keyword: params.term,
                    p_id: jQuery(this).closest('td').data('id')
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 1
    });

    /*Save Cross sell*/
    jQuery('.button-save').on('click', function () {
        var product_id = jQuery(this).closest('td').data('id'),
            product_bundle_id = jQuery(this).closest('tr').find('input[name="_wbs_cross_sell_of"]').val(),
                other_bundle_id = '';
        if(jQuery(this).closest('tr').find('select[name="_wbs_cross_sell_bundle"]').val()!==null&&jQuery(this).closest('tr').find('.wbs-use-other-bundle').prop('checked')){
            other_bundle_id=jQuery(this).closest('tr').find('select[name="_wbs_cross_sell_bundle"]').val();
        }
        var btn = jQuery(this);
        if (product_id) {
            var c_id = jQuery('select.u-product-' + product_id).val();
            if (c_id == null&&!other_bundle_id) {
                jQuery(this).closest('td').find('.button-remove').triggerHandler('click');

            } else {
                //c_id = c_id.toString();
                btn.text('Saving');
                jQuery.ajax({
                    type: 'POST',
                    data: 'action=wbs_c_save_product' + '&id=' + product_id + '&c_id=' + c_id + '&product_bundle_id=' + product_bundle_id+'&other_bundle_id='+other_bundle_id,
                    url: wboostsales_ajax_url,
                    success: function (html) {
                        var obj = jQuery.parseJSON(html);
                        if (obj.check == 'done') {
                            btn.text('Save');
                            btn.removeClass('button-primary');
                        } else if (obj.check == 'wrong') {
                            btn.text('Save');
                            btn.removeClass('button-primary');
                            alert('Please click Clear All replace to use save data without anything !');
                        } else {
                            btn.text('Save');
                            btn.removeClass('button-primary');
                        }
                        location.reload();
                    },
                    error: function (html) {
                    }
                })
            }
        } else {
            return false;
        }
    });
    /*Remove all*/
    jQuery('.button-remove').on('click', function () {
        var r = confirm("Your products in cross-sells of selected product will be removed all and Delete bundle product. Are you sure ?");
        if (r == true) {
            var product_id = jQuery(this).closest('td').data('id'),
                product_bundle_id = jQuery(this).closest('tr').find('input[name="_wbs_cross_sell_of"]').val();
            var btn = jQuery(this);
            if (product_id) {
                btn.text('Removing');
                jQuery.ajax({
                    type: 'POST',
                    data: 'action=wbs_c_remove_product' + '&id=' + product_id + '&product_bundle_id=' + product_bundle_id,
                    url: wboostsales_ajax_url,
                    success: function (html) {
                        var obj = jQuery.parseJSON(html);
                        if (obj.check == 'done') {
                            btn.text('Remove all');
                            jQuery('select.u-product-' + product_id).val('').trigger("change");
                            location.reload();
                        } else {

                        }
                    },
                    error: function (html) {
                    }
                })
            } else {
                return false;
            }
        }
    });

    /*Action after selected product*/
    jQuery('.product-search-crs').on("select2:selecting", function (e) {
        // what you would like to happen
        var p_id = jQuery(this).closest('td').data('id');
        jQuery('.product-action-' + p_id).find('.button-save').addClass('button-primary');
    });
    /*Action after remove product*/
    jQuery('.product-search-crs').on("select2:unselecting", function (e) {
        var p_id = jQuery(this).closest('td').data('id');
        jQuery('.product-action-' + p_id).find('.button-save').addClass('button-primary');
    });

    /*Reload*/
    function reload_cache() {
        jQuery('.product-search-crs').trigger('change');
        location.reload();
    }

    jQuery('input[name="product_bundle_regular_price"]').keypress(function (event) {
        if (event.which < 44 || event.which == 45 || event.which == 47
            || event.which > 57) {
            event.preventDefault();
        } // prevent if not number/dot

        if (event.which == 46
            && jQuery(this).val().indexOf('.') != -1) {
            event.preventDefault();
        }
        if (event.which == 44
            && jQuery(this).val().indexOf(',') != -1) {
            event.preventDefault();
        }
    });

    jQuery('.button-quick-edit').on('click', function () {
        jQuery(this).next().slideToggle();
    });

    jQuery('.button-cancel').on('click', function () {
        jQuery(this).closest('.inline-edit-row').slideUp('400');
    });

    jQuery('.button-update').on('click', function () {
        var product_bundle_id = jQuery(this).closest('.inline-edit-row').data('product_bundle_id'),
            post_bundle_title = jQuery(this).closest('.inline-edit-row').find('input[name="post_bundle_title"]').val(),
            product_bundle_regular_price = jQuery(this).closest('.inline-edit-row').find('input[name="product_bundle_regular_price"]').val();
        jQuery(this).next().addClass('is-active');
        if (product_bundle_id) {
            jQuery.ajax({
                type: 'POST',
                data: 'action=wbs_update_product' + '&id=' + product_bundle_id + '&title=' + post_bundle_title + '&price=' + product_bundle_regular_price,
                url: wboostsales_ajax_url,
                success: function (html) {
                    var obj = jQuery.parseJSON(html);
                    if (obj.check == 'done') {
                        jQuery('.inline-edit-row').attr('data-product_bundle_id', product_bundle_id).find('span.spinner').removeClass('is-active');
                        jQuery('.inline-edit-row').attr('data-product_bundle_id', product_bundle_id).slideUp('300');
                        location.reload();
                    } else {
                        alert(obj.detail_err);
                    }
                },
                error: function (html) {
                }
            });
        }
    });

    var wbs_different_up = jQuery('#wbs_different_up-cross-sell').data('wbs_up_crosssell');
    jQuery(document).tooltip({
        items: "#wbs_different_up-cross-sell",
        position: {
            my: "right top+10"
        },
        track: true,
        content: '<img class="wbs_img_tooltip_dfc" src="' + wbs_different_up + '" width="700px" style="float: left; margin-left: 180px;" />',
        show: {
            effect: "slideDown",
            delay: 150
        }
    });
    jQuery('.wbs-crosssells-ajax-enable').on('click', function () {
        jQuery.ajax({
            type: 'POST',
            url: wboostsales_ajax_url,
            data: {
                action: 'wbs_ajax_enable_crosssell',
                nonce: jQuery('#_wsm_nonce').val(),
            },
            success: function (response) {
                jQuery('.wbs-crosssells-ajax-enable').parent().fadeOut(300);
            },
            error: function (err) {
                jQuery('.wbs-crosssells-ajax-enable').parent().fadeOut(300);
            }
        });
    })
    jQuery('.btn-sync-crosssell').on('click', function () {
        if (confirm("This will create a bundle for each product from it's WooCommerce Cross-sells. Products whose bundles were already created will be skipped. Do you want to continue?")) {
            let btn = jQuery(this);
            let oldtext=btn.html();
            btn.text('Syncing...');
            jQuery.ajax({
                type: 'POST',
                data: {
                    action:'wbs_u_create_bundle_from_crosssells',
                    nonce:jQuery('#_wsm_nonce').val(),
                },
                url: wboostsales_ajax_url,
                success: function (response) {
                    let obj = jQuery.parseJSON(response);
                    if (obj.check == 'done') {
                        btn.text(oldtext);
                        reload_cache();
                    } else {

                    }
                },
                error: function (html) {
                }
            })
        }
    });
});