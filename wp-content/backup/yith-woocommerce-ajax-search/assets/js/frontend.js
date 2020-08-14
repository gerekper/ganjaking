/**
 * frontend.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Ajax Search
 * @version 1.1.1
 */

jQuery(document).ready(function ($) {
    "use strict";

    var el = $('.yith-s'),
        def_loader = (typeof woocommerce_params != 'undefined' && typeof woocommerce_params.ajax_loader_url != 'undefined') ? woocommerce_params.ajax_loader_url : yith_wcas_params.loading,
        loader_icon = el.data('loader-icon') == '' ? def_loader : el.data('loader-icon'),
        search_button = $('#yith-searchsubmit'),
        min_chars = el.data('min-chars'),
        ajaxurl = yith_wcas_params.ajax_url;

    if (ajaxurl.indexOf('?') == -1) {
        ajaxurl += '?';
    }

    search_button.on('click', function () {
        var form = $(this).closest('form');
        if (form.find('.yith-s').val() == '') {
            return false;
        }
        return true;
    });

    if (el.length == 0) el = $('#yith-s');

    el.each(function () {
        var $t = $(this),
            append_to = (typeof  $t.data('append-to') == 'undefined') ? $t.closest('.yith-ajaxsearchform-container') : $t.closest($t.data('append-to'));

        $t.yithautocomplete({
            minChars: min_chars,
            appendTo: append_to,
            triggerSelectOnValidInput: false,
            serviceUrl: ajaxurl + 'action=yith_ajax_search_products',
            onSearchStart: function () {
                $t.css({
                    'background-image': 'url(' + loader_icon + ')',
                    'background-repeat': 'no-repeat',
                    'background-position': 'center right'
                });
            },
            onSelect: function (suggestion) {
                if (suggestion.id != -1) {
                    window.location.href = suggestion.url;
                }
            },
            onSearchComplete: function () {
                $t.css('background-image', 'none');
            }
        });
    });
});


