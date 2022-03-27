jQuery(document).ready(function (  ) {

	(function($){

		// # UI EVENTS

		$( '#_gform_setting_gwreloadform_enable' ).click(function(){
			toggleSettings( $(this).is(':checked') );
		});

		// # HELPERS

		function toggleSettings( isChecked ) {

			var enableCheckbox = jQuery('#_gform_setting_gwreloadform_enable');
			var settingsContainer = jQuery('#gwreloadform_settings');

			if( isChecked ) {
				enableCheckbox.prop( 'checked', true );
				settingsContainer.slideDown();
			} else {
				enableCheckbox.prop( 'checked', false );
				settingsContainer.slideUp();
			}

		}
		toggleSettings( $( '#_gform_setting_gwreloadform_enable' ).is(':checked') );
	})(jQuery);

});
