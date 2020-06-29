(function ($) {

    function getEnhancedSelectFormatString() {

        return {
            formatMatches        : function (matches) {
                if (1 === matches) {
                    return ylc.i18n_matches_1;
                }

                return ylc.i18n_matches_n.replace('%qty%', matches);
            },
            formatNoMatches      : function () {
                return ylc.i18n_no_matches;
            },
            formatAjaxError      : function () {
                return ylc.i18n_ajax_error;
            },
            formatInputTooShort  : function (input, min) {
                var number = min - input.length;

                if (1 === number) {
                    return ylc.i18n_input_too_short_1;
                }

                return ylc.i18n_input_too_short_n.replace('%qty%', number);
            },
            formatInputTooLong   : function (input, max) {
                var number = input.length - max;

                if (1 === number) {
                    return ylc.i18n_input_too_long_1;
                }

                return ylc.i18n_input_too_long_n.replace('%qty%', number);
            },
            formatSelectionTooBig: function (limit) {
                if (1 === limit) {
                    return ylc.i18n_selection_too_long_1;
                }

                return ylc.i18n_selection_too_long_n.replace('%qty%', limit);
            },
            formatLoadMore       : function () {
                return ylc.i18n_load_more;
            },
            formatSearching      : function () {
                return ylc.i18n_searching;
            }
        };

    }

    $(':input.ylc-select').filter(':not(.enhanced)').each(function () {
        var select2_args = $.extend({
            minimumResultsForSearch: 10,
            allowClear             : $(this).data('allow_clear'),
            placeholder            : $(this).data('placeholder')
        }, getEnhancedSelectFormatString());

        $(this).select2(select2_args).addClass('enhanced');
    });

    $('.select_all').click(function () {
        $(this).closest('td').find('select option').attr('selected', 'selected');
        $(this).closest('td').find('select').trigger('change');
        return false;
    });

    $('.select_none').click(function () {
        $(this).closest('td').find('select option').removeAttr('selected');
        $(this).closest('td').find('select').trigger('change');
        return false;
    });

  /*  $('#yit_live_chat_options_showing-pages-all').change(function () {

        var pages = $('#yit_live_chat_options_showing-pages-container').parent().parent();

        if ($(this).is(':checked')) {
            pages.hide();
        } else {

            if (!$('#yit_live_chat_options_only-vendor-chat').is(':checked')) {
                pages.show();
            }

        }

    }).change();

    $('#yit_live_chat_options_only-vendor-chat').change(function () {

        var pages_all = $('#yit_live_chat_options_showing-pages-all-container').parent().parent(),
            pages = $('#yit_live_chat_options_showing-pages-container').parent().parent();

        if ($(this).is(':checked')) {
            pages_all.hide();
            pages.hide();
        } else {
            pages_all.show();

            if (!$('#yit_live_chat_options_showing-pages-all').is(':checked')) {
                pages.show();
            }

        }

    }).change();*/

})(jQuery);
