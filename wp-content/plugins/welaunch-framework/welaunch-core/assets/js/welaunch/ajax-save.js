/* global welaunch, tinyMCE, ajaxurl */

(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$.welaunch.ajax_save = function( button ) {
		var $data;
		var $nonce;

		var overlay           = $( document.getElementById( 'welaunch_ajax_overlay' ) );
		var $notification_bar = $( document.getElementById( 'welaunch_notification_bar' ) );
		var $parent           = $( button ).parents( '.welaunch-wrap-div' ).find( 'form' ).first();

		overlay.fadeIn();

		// Add the loading mechanism.
		$( '.welaunch-action_bar .spinner' ).addClass( 'is-active' );
		$( '.welaunch-action_bar input' ).attr( 'disabled', 'disabled' );

		$notification_bar.slideUp();

		$( '.welaunch-save-warn' ).slideUp();
		$( '.welaunch_ajax_save_error' ).slideUp(
			'medium',
			function() {
				$( this ).remove();
			}
		);

		// Editor field doesn't auto save. Have to call it. Boo.
		if ( welaunch.optName.hasOwnProperty( 'editor' ) ) {
			$.each(
				welaunch.optName.editor,
				function( $key ) {
					var editor;

					if ( 'undefined' !== typeof ( tinyMCE ) ) {
						editor = tinyMCE.get( $key );

						if ( editor ) {
							editor.save();
						}
					}
				}
			);
		}

		$data = $parent.serialize();

		// Add values for checked and unchecked checkboxes fields.
		$parent.find( 'input[type=checkbox]' ).each(
			function() {
				var chkVal;

				if ( 'undefined' !== typeof $( this ).attr( 'name' ) ) {
					chkVal = $( this ).is( ':checked' ) ? $( this ).val() : '0';

					$data += '&' + $( this ).attr( 'name' ) + '=' + chkVal;
				}
			}
		);

		if ( 'welaunch_save' !== button.attr( 'name' ) ) {
			$data += '&' + button.attr( 'name' ) + '=' + button.val();
		}

		$nonce = $parent.attr( 'data-nonce' );

		$.ajax(
			{ type: 'post',
				dataType: 'json',
				url: ajaxurl,
				data: {
					action:     welaunch.optName.args.opt_name + '_ajax_save',
					nonce:      $nonce,
					'opt_name': welaunch.optName.args.opt_name,
					data:       $data
				},
				error: function( response ) {
					$( '.welaunch-action_bar input' ).removeAttr( 'disabled' );

					if ( true === welaunch.optName.args.dev_mode ) {
						console.log( response.responseText );

						overlay.fadeOut( 'fast' );
						$( '.welaunch-action_bar .spinner' ).removeClass( 'is-active' );
						alert( welaunch.optName.ajax.alert );
					} else {
						welaunch.optName.args.ajax_save = false;

						$( button ).click();
						$( '.welaunch-action_bar input' ).attr( 'disabled', 'disabled' );
					}
				},
				success: function( response ) {
					var $save_notice;

					if ( response.action && 'reload' === response.action ) {
						location.reload( true );
					} else if ( 'success' === response.status ) {
						$( '.welaunch-action_bar input' ).removeAttr( 'disabled' );
						overlay.fadeOut( 'fast' );
						$( '.welaunch-action_bar .spinner' ).removeClass( 'is-active' );
						welaunch.optName.options  = response.options;
						welaunch.optName.errors   = response.errors;
						welaunch.optName.warnings = response.warnings;
						welaunch.optName.sanitize = response.sanitize;

						$notification_bar.html( response.notification_bar ).slideDown( 'fast' );
						if ( null !== response.errors || null !== response.warnings ) {
							$.welaunch.notices();
						}

						if ( null !== response.sanitize ) {
							$.welaunch.sanitize();
						}

						$save_notice = $( document.getElementById( 'welaunch_notification_bar' ) ).find( '.saved_notice' );

						$save_notice.slideDown();
						$save_notice.delay( 4000 ).slideUp();
					} else {
						$( '.welaunch-action_bar input' ).removeAttr( 'disabled' );
						$( '.welaunch-action_bar .spinner' ).removeClass( 'is-active' );
						overlay.fadeOut( 'fast' );
						$( '.wrap h2:first' ).parent().append( '<div class="error welaunch_ajax_save_error" style="display:none;"><p>' + response.status + '</p></div>' );
						$( '.welaunch_ajax_save_error' ).slideDown();
						$( 'html, body' ).animate(
							{ scrollTop: 0 },
							'slow'
						);
					}
				}
			}
		);

		return false;
	};
})( jQuery );
