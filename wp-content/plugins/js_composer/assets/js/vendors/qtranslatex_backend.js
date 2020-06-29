(function ( $ ) {
	'use strict';

	function hookLanguageSwitch( activeLang ) {
		var $inline_href = $( '.wpb_switch-to-front-composer' );
		if ( !$inline_href.data( 'raw-url' ) ) {
			$inline_href.data( 'raw-url', $inline_href.attr( 'href' ) );
		}
		var new_url = $inline_href.data( 'raw-url' ) + '&lang=' + activeLang;
		$inline_href.attr( 'href', new_url );

		vc.shortcodes.fetch( { reset: true } );
	}

	$( function () {
		var qtranslateManager = qTranslateConfig.js.get_qtx();

		qtranslateManager.addLanguageSwitchListener( hookLanguageSwitch );
	} );
})( window.jQuery );
