/**
 * Settings
 *
 * @package WC_Instagram/Assets/JS/Admin
 * @since   3.0.0
 */

/* global wc_instagram_settings_params */
( function( $ ) {

	'use strict';

	if ( typeof wc_instagram_settings_params === 'undefined' ) {
		return false;
	}

	var wcInstagramSettings = {
		hasChanges: false,

		init: function() {
			this.initSortableTables();

			// Reset changes on click the save button.
			$( '[name="save"]' ).on( 'click', function() {
				wcInstagramSettings.hasChanges = false;
			});

			$( window ).on( 'beforeunload', this.unloadConfirmation );
		},

		/**
		 * Make sortable the table fields.
		 */
		initSortableTables: function() {
			$( 'table.wc-instagram-field-table.sortable tbody' ).sortable({
				items: 'tr',
				cursor: 'move',
				axis: 'y',
				handle: 'td.sort',
				scrollSensitivity: 40,
				helper: function( event, ui ) {
					ui.children().each( function() {
						$( this ).width( $( this ).width() );
					});
					ui.css( 'left', '0' );
					return ui;
				},
				start: function( event, ui ) {
					ui.item.css( 'background-color', '#f6f6f6' );
				},
				stop: function( event, ui ) {
					ui.item.removeAttr( 'style' );
				},
				update: function() {
					wcInstagramSettings.hasChanges = true;
				}
			});
		},

		/**
		 * Loads a confirmation dialog if there are unsaved changes.
		 */
		unloadConfirmation: function( event ) {
			if ( wcInstagramSettings.hasChanges ) {
				event.returnValue        = wc_instagram_settings_params.unload_confirmation_msg;
				window.event.returnValue = wc_instagram_settings_params.unload_confirmation_msg;

				return wc_instagram_settings_params.unload_confirmation_msg;
			}
		}
	};

	wcInstagramSettings.init();
})( jQuery );
