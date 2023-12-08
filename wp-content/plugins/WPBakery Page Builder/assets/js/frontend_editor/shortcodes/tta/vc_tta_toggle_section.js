(function ( $ ) {
	'use strict';

	window.vc.ttaSectionActivateOnClone = false;
	window.InlineShortcodeView_vc_tta_toggle_section = window.InlineShortcodeView_vc_tta_section.extend( {
		controls_selector: '#vc_controls-template-vc_tta_toggle_section',

		allowAddControl: function () {
			return vc_user_access().shortcodeAll( 'vc_tta_toggle_section' );
		}
	} );
})( window.jQuery );
