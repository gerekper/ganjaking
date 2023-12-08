(function ( $ ) {
	'use strict';

	var minHeight = '340px';

	function vc_jwplayer_resize( target ) {
		window.jwplayer( target ).onReady( function () {
			$( this.container ).css( 'min-height', minHeight );
		} );
		$( window.jwplayer( target ).container ).css( 'min-height', minHeight );
	}

	$( document ).on( 'ready', function () {
		$( "div" ).filter( function () {
			return this.id.match( /^jwplayer\-\d+$/ );
		} ).each( function () {
			vc_jwplayer_resize( this );
		} );
	} );
	$( window ).on( 'vc_reload', function () {
		$( "div" ).filter( function () {
			return this.id.match( /^jwplayer\-\d+$/ );
		} ).each( function () {
			vc_jwplayer_resize( this );
		} );
	} );

})( window.jQuery );
