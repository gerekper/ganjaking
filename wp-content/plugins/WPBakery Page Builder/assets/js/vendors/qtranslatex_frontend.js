(function ( $ ) {
	'use strict';

	$( '#vc_vendor_qtranslatex_langs_front' ).on( 'change', function () {
		vc.closeActivePanel();
		$( '#vc_logo' ).addClass( 'vc_ui-wp-spinner' );
		window.location.href = $( this ).val();
	} );

	var native_getContent = vc.ShortcodesBuilder.prototype.getContent;
	vc.ShortcodesBuilder.prototype.getContent = function () {
		var content = native_getContent();
		$( '#content' ).val( content );

		return content;
	};

})( window.jQuery );