/**
 * Start qrcode widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetQRCode = function( $scope, $ ) {

		var $qrcode = $scope.find( '.bdt-qrcode' ),
            image   = $scope.find( '.bdt-qrcode-image' );

        if ( ! $qrcode.length ) {
            return;
        }
        var settings = $qrcode.data('settings');
            settings.image = image[0];

        $($qrcode).qrcode(settings);

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-qrcode.default', widgetQRCode );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End qrcode widget script
 */

