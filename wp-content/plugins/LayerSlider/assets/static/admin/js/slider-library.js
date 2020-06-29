// In some rare cases the globally loaded LS_SLibrary_l10n
// variable might not be available due to plugins making
// changes in the WP script queue. The below makes sure
// that we can at least avoid undef JS errors.
if( typeof LS_SLibrary_l10n === 'undefined' ) {
	LS_SLibrary_l10n = {};
}


var LS_SliderLibrary = {

	initialized: false,

	settings: {},

	defaults: {

		onChange: function() {},

		modalSettings: {
			uid: 'ls-guttenberg-slider-list',
			id: 'ls-slider-library-modal',
			title: LS_SLibrary_l10n.WindowTitle,
			content: LS_SLibrary_l10n.WindowLoading,
			modalClasses: 'ls-slider-group-modal-window',
			minWidth: 500,
			maxWidth: 1500,
			maxHeight: '100%',
			animationIn: 'scale',
			overlaySettings: {
				animationIn: 'fade'
			}
		},

		groupModalSettings: {
			into: '.ls-sliders-grid',
			maxWidth: 1380,
			minWidth: 600,
			modalClasses: 'ls-slider-group-modal-window',
			animationIn: 'scale',
			overlaySettings: {
				animationIn: 'fade'
			}
		}
	},

	init: function( userSettings ) {

		LS_SliderLibrary.parseData( userSettings );

		if( ! LS_SliderLibrary.initialized ) {
			LS_SliderLibrary.initialized = true;

			// Open group
			jQuery( document ).on('click', '.slider-item.group-item', function( e ) {
				e.preventDefault();
				LS_SliderLibrary.openGroup( jQuery( this ) );

			// Select slider
			}).on('click', '#ls-slider-library-modal .slider-item:not(.group-item)', function( event ) {
				LS_SliderLibrary.selectSlider( event, jQuery( event.currentTarget ) );
			});

		}
	},

	parseData: function( userSettings ) {

		userSettings = userSettings || {};
		LS_SliderLibrary.settings = jQuery.extend( true, {}, LS_SliderLibrary.defaults, userSettings );
	},

	open: function( userSettings ) {

		LS_SliderLibrary.init( userSettings );

		kmw.modal.open( LS_SliderLibrary.settings.modalSettings );

		jQuery.get( LS_SLibrary_l10n.ajaxurl, { action : 'ls_slider_library_contents' }, function( html ) {
			jQuery('#ls-slider-library-modal .kmw-modal-content').html( html );
		});
	},

	openGroup: function( $item ) {

		var groupModalSettings = LS_SliderLibrary.settings.groupModalSettings;

		groupModalSettings.title = $item.find('.name').html();
		groupModalSettings.content = $item.next().children();

		kmw.modal.open( groupModalSettings );
	},

	selectSlider : function( event, $item ) {

		// Make a copy of $item data before it becomes unavailable
		// due to changes in the DOM.
		var sliderData = jQuery.extend( true, {}, $item.data() );

		// Close the Slider Library
		LS_SliderLibrary.close();

		// Trigger onChange event and send back slider data
		if( LS_SliderLibrary.settings.onChange ) {
			LS_SliderLibrary.settings.onChange( sliderData );
		}
	},


	close : function() {
		kmw.modal.close();
	}
};