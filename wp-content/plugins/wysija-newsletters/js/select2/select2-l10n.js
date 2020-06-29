/**
 * Select2 WordPress localize
 */
(function ($, window, _) {
    "use strict";

    $.extend($.fn.select2.defaults, {
        formatNoMatches: function () {
            return window.mailpoet_l10n_select2.noMatches;
        },
        formatInputTooShort: function (input, min) {
            var n = min - input.length;
            return _.template( window.mailpoet_l10n_select2.inputTooShort )( { chars: parseInt( n, 10 ), plural: (n == 1? "" : "s") } );
        },
        formatInputTooLong: function (input, max) {
            var n = input.length - max;
            return _.template( window.mailpoet_l10n_select2.inputTooLong )( { chars: parseInt( n, 10 ), plural: (n == 1? "" : "s") } );
        },
        formatSelectionTooBig: function (limit) {
            return _.template( window.mailpoet_l10n_select2.selectionTooBig )( { chars: parseInt( n, 10 ), plural: (n == 1? "" : "s") } );
        },
        formatLoadMore: function (pageNumber) {
            return window.mailpoet_l10n_select2.loadMore;
        },
        formatSearching: function () {
            return window.mailpoet_l10n_select2.searching;
        }
    });
}(jQuery.noConflict(), window, _));
