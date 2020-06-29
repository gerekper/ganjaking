/* global jQuery, window, wc_stamps_admin, Clipboard */
( function( $ ) {

	var self = {
		clipboardTriggers: ['.copy-tracking-number'],

		init: function() {
			self.initClipboard();
		},

		initClipboard: function() {
			$.each( self.clipboardTriggers, $.proxy( self.initClipboardHandler, self ) );
		},

		initClipboardHandler: function( _, selector ) {
			var clipboard = new Clipboard( selector );

			clipboard.on( 'success', $.proxy( self.onCopySuccess, self ) );
			clipboard.on( 'error', $.proxy( self.onCopyError, self ) );

			$( selector ).on( 'click', $.proxy( self.clipboardClickHandler, self ) );
		},

		clipboardClickHandler: function( e ) {
			e.preventDefault();
		},

		onCopySuccess: function( e ) {
			$( e.trigger ).tipTip( {
				'attribute': 'data-clipboard-success',
				'activation': 'focus',
				'fadeIn': 50,
				'fadeOut': 50,
				'delay': 0
			} ).focus();
		},

		onCopyError: function( e ) {
			self.clipboardFallback( e.text );
		},

		clipboardFallback: function( textToCopy ) {
			window.prompt( wc_stamps_admin.copy_to_clipboard_fallback_i18n, textToCopy );
		}
	}

	$( self.init );

} )( jQuery );
