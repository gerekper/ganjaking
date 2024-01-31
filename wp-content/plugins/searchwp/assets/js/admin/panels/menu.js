/* global _SEARCHWP */

( function($) {

    'use strict';

    const app = {

        /**
         * Init.
         *
         * @since 4.3.10
         */
        init: () => {

            $( app.ready );
        },

        /**
         * Document ready
         *
         * @since 4.3.10
         */
        ready: () => {

            app.addParamsToUpgradeLink();
        },

		/**
		 * Add 'target="_blank"' and 'rel="noopener noreferrer"' to the "Upgrade to Pro" menu link.
		 *
		 * @since 4.3.10
		 */
		addParamsToUpgradeLink: function() {

			$( 'a.searchwp-sidebar-upgrade-to-pro' )
				.attr( 'target', '_blank' )
				.attr( 'rel', 'noopener noreferrer' );
		},
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.AdminMenu = app;

}( jQuery ) );
