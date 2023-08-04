(function () {
	'use strict';

	window.InlineShortcodeView_vc_tta_toggle = window.InlineShortcodeView_vc_tta_tour.extend( {
		render: function () {
			var model_id = this.model.get( 'id' );
			window.InlineShortcodeView_vc_tta_toggle.__super__.render.call( this );
			vc.frame_window.vc_iframe.addActivity( function () {
				this.vc_iframe.vc_tta_toggle( model_id );
			} );

			return this;
		}
	} );
})();
