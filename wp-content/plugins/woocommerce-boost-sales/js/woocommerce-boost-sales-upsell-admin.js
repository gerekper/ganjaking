'use strict';
jQuery(document).ready(function () {
    jQuery(".wbs-category-search").select2({
        closeOnSelect: false,
        placeholder: "Please fill in your category title",
        ajax: {
            url: "admin-ajax.php?action=wbs_search_category_excl",
            dataType: 'json',
            type: "GET",
            quietMillis: 50,
            delay: 250,
            data: function (params) {
                return {
                    keyword: params.term
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

    jQuery(".product-search").select2({
        closeOnSelect: false,
        placeholder: "Please fill your title product",
        ajax: {
            url: "admin-ajax.php?action=wbs_search_product",
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
    let upsells_products = {};
    jQuery('.product-search').next(".select2-container").find('ul.select2-selection__rendered').sortable({
        containment: 'parent',
        stop: function (event, ui) {
            var product_id = jQuery(this).closest('tr').find('.column-action').data('id');
            // event target would be the <ul> which also contains a list item for searching (which has to be excluded)
            var arr = Array.from(jQuery(event.target).find('li:not(.select2-search)').map(function () {
                return jQuery(this).data('data').id;
            }));
            upsells_products[product_id] = arr;
        }
    });
    let upsells_categories = {};
    jQuery('.wbs-category-search').next(".select2-container").find('ul.select2-selection__rendered').sortable({
        containment: 'parent',
        stop: function (event, ui) {
            var product_id = jQuery(this).closest('tr').find('.column-action').data('id');
            // event target would be the <ul> which also contains a list item for searching (which has to be excluded)
            var arr = Array.from(jQuery(event.target).find('li:not(.select2-search)').map(function () {
                return jQuery(this).data('data').id;
            }));
            upsells_categories[product_id] = arr;
        }
    });
    /*Save Up sell*/
    jQuery('.button-save').on('click', function () {
        var product_id = jQuery(this).closest('td').data('id');
        var btn = jQuery(this);
        if (product_id) {
            var u_id;
            var u_cate_ids;
            if (upsells_products.hasOwnProperty(product_id)) {
                u_id = upsells_products[product_id];
            } else {
                u_id = jQuery('select.u-product-' + product_id).val();
            }
            if (upsells_categories.hasOwnProperty(product_id)) {
                u_cate_ids = upsells_categories[product_id];
            } else {
                u_cate_ids = jQuery('select.u-categories-' + product_id).val();
            }
            btn.text('Saving');
            jQuery.ajax({
                type: 'POST',
                data: {
                    action:'wbs_u_save_product',
                    id:product_id,
                    u_id:u_id,
                    u_cate_ids:u_cate_ids,
                },
                url: wboostsales_ajax_url,
                success: function (obj) {
                    if (obj.check == 'done') {
                        btn.text('Save');
                        btn.removeClass('button-primary');
                    } else {

                    }
                },
                error: function (html) {
                }
            })
        } else {
            return false;
        }
    });
    /*Remove all*/
    jQuery('.button-remove').on('click', function () {
        var r = confirm("Your products in up-sells of selected product will be removed all. Are you sure ?");
        if (r == true) {
            var product_id = jQuery(this).closest('td').data('id');

            var btn = jQuery(this);
            if (product_id) {
                btn.text('Removing');
                jQuery.ajax({
                    type: 'POST',
                    data: 'action=wbs_u_remove_product' + '&id=' + product_id,
                    url: wboostsales_ajax_url,
                    success: function (html) {
                        var obj = jQuery.parseJSON(html);
                        if (obj.check == 'done') {
                            btn.text('Remove all');
                            jQuery('select.u-product-' + product_id).val('null').trigger("change");
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
    jQuery('.product-search').on("select2:selecting", function (e) {
        // what you would like to happen
        var p_id = jQuery(this).closest('td').data('id');
        jQuery('.product-action-' + p_id).find('.button-save').addClass('button-primary');
    });
    /*Action after remove product*/
    jQuery('.product-search').on("select2:unselecting", function (e) {
        var p_id = jQuery(this).closest('td').data('id');
        jQuery('.product-action-' + p_id).find('.button-save').addClass('button-primary');
    });
    /*Click Bulk Adds*/
    jQuery('.btn-bulk-adds').on('click', function () {
        jQuery('.bulk-adds').slideToggle('400');
        jQuery('.list-products').fadeToggle('400');
    });
    /*Bulk Add products Upsell*/
    jQuery('.ba-button-save').on('click', function () {

        var p_id = jQuery('select.ba-product').val();
        var u_id = jQuery('select.ba-u-product').val();
        var btn = jQuery(this);
        if (p_id && u_id) {
            u_id = u_id.toString();
            p_id = p_id.toString();
            btn.text('Adding');
            jQuery.ajax({
                type: 'POST',
                data: 'action=wbs_ba_save_product' + '&p_id=' + p_id + '&u_id=' + u_id,
                url: wboostsales_ajax_url,
                success: function (html) {
                    var obj = jQuery.parseJSON(html);
                    if (obj.check == 'done') {
                        reload_cache();
                    } else {

                    }
                },
                error: function (html) {
                }
            })
        } else if (u_id && jQuery('input#vi_chk_selectall').is(':checked')) {
            u_id = u_id.toString();
            btn.text('Adding');
            jQuery.ajax({
                type: 'POST',
                data: 'action=wbs_ba_save_all_product' + '&u_id=' + u_id,
                url: wboostsales_ajax_url,
                success: function (html) {
                    var obj = jQuery.parseJSON(html);
                    if (obj.check == 'done') {
                        reload_cache();
                    } else {

                    }
                },
                error: function (html) {
                }
            })
        } else {
            return false
        }
    });

    /*checkbox select all*/
    jQuery('input#vi_chk_selectall').on('change', function () {
        if (jQuery('input#vi_chk_selectall').is(':checked')) {
            jQuery(this).closest('td').find('span.select2-container').css('display', 'none');
        } else {
            jQuery(this).closest('td').find('span.select2-container').css('display', 'block');
        }
    });


    /*Remove all*/
    jQuery('.btn-sync-upsell').on('click', function () {
        if (confirm('Create Up-sells to use with WooCommerce Boost Sales plugin from Up-sells data in WooCommerce single product settings. Continue?')) {
            var btn = jQuery(this);
            btn.text('Syncing');
            jQuery.ajax({
                type: 'POST',
                data: 'action=wbs_u_sync_product',
                url: wboostsales_ajax_url,
                success: function (html) {
                    var obj = jQuery.parseJSON(html);
                    if (obj.check == 'done') {
                        btn.text('Get Product Up-Sells');
                        reload_cache();
                    } else {

                    }
                },
                error: function (html) {
                }
            })
        }
    });
    jQuery('.btn-sync-upsell-revert').on('click', function () {
        if (confirm('Up-sells data in single product settings will be OVERRIDDEN by Up-sells data managed by WooCommerce Boost Sales plugin. Continue?')) {
            var btn = jQuery(this);
            let btnText = btn.html();
            btn.text('Syncing...');
            jQuery.ajax({
                type: 'POST',
                data: 'action=wbs_u_sync_product_revert',
                url: wboostsales_ajax_url,
                success: function (response) {
                    alert('Sync completed.');
                },
                error: function (html) {
                },
                complete: function () {
                    btn.text(btnText);
                }
            })
        }
    });

    /*Reload*/
    function reload_cache() {
        jQuery('.product-search').trigger('change');
        location.reload();
    }

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
    jQuery('.wbs-upsells-ajax-enable').on('click', function () {
        jQuery.ajax({
            type: 'POST',
            url: wboostsales_ajax_url,
            data: {
                action: 'wbs_ajax_enable_upsell',
                nonce: jQuery('#_wsm_nonce').val(),
            },
            success: function (response) {
                jQuery('.wbs-upsells-ajax-enable').parent().fadeOut(300);
            },
            error: function (err) {
                jQuery('.wbs-upsells-ajax-enable').parent().fadeOut(300);
            }
        });
    })
});