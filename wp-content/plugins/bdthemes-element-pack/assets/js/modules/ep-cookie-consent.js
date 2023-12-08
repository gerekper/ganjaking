/**
 * Start cookie consent widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetCookieConsent = function( $scope, $ ) {

		var $cookieConsent = $scope.find('.bdt-cookie-consent'),
            $settings      = $cookieConsent.data('settings'),
            editMode       = Boolean( elementorFrontend.isEditMode() );
        
        if ( ! $cookieConsent.length || editMode ) {
            return;
        }

        window.cookieconsent.initialise($settings);

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-cookie-consent.default', widgetCookieConsent );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End cookie consent widget script
 */

