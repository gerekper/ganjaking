/* global _SEARCHWP */

( function($) {

    'use strict';

    const app = {

        /**
         * Init.
         *
         * @since 4.3.0
         */
        init: () => {

            $( app.ready );
        },

        /**
         * Document ready
         *
         * @since 4.3.0
         */
        ready: () => {

            app.events();
        },

        /**
         * Page events.
         *
         * @since 4.3.0
         */
        events: () => {

			$( '#swp-wake-up-indexer-continue-btn' ).on('click', app.wakeUpIndexer );
        },

		wakeUpIndexer: function(e) {

			e.preventDefault();

            $(e.target).closest('.swp-modal').find('.swp-modal--close').click();

			$('.swp-content-container button').attr('disabled','disabled');
			$('#swp-wake-up-indexer-btn').addClass('swp-button--processing');

			jQuery.post(ajaxurl, {
				_ajax_nonce: _SEARCHWP.nonce,
				action: _SEARCHWP.prefix + 'wake_indexer'
			}, function(response) {
				$('.swp-content-container button').removeAttr('disabled');
				$('#swp-wake-up-indexer-btn').removeClass('swp-button--processing');
				if (response.success) {
                    $('#swp-wake-up-indexer-btn').addClass('swp-button--completed');
                    setTimeout(
                        () => {
                            $('#swp-wake-up-indexer-btn').removeClass('swp-button--completed');
                        },
                        1500
                    );
				} else {
                    console.log(response);
                    $('#swp-wake-up-indexer-btn').after('<span class="swp-error-msg swp-text-red swp-b ">Waking indexer FAILED. View console for more information.</span>');
                    setTimeout(
                        function () {
                            $('#swp-wake-up-indexer-btn').siblings('.swp-error-msg').remove();
                        },
                        3000
                    );
                }
			});
		},
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.AdminMiscSettingsPage = app;

}( jQuery ) );
