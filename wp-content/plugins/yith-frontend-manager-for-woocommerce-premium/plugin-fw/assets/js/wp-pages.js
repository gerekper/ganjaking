/* global yith_plugin_fw_wp_pages, yith */
jQuery( function ( $ ) {
	// prevents the WC message for changes when leaving the panel page
	$( '.yith-plugin-fw-wp-page-wrapper .woo-nav-tab-wrapper' ).removeClass( 'woo-nav-tab-wrapper' ).addClass( 'yith-nav-tab-wrapper' );

	var isTaxEdit = 'term-php' === window.adminpage;

	// Update message animation.
	( function () {
		var message;
		if ( isTaxEdit ) {
			message                 = $( 'div#message' );
			var ajaxResponseElement = $( '#ajax-response' );
			if ( message.length ) {
				if ( ajaxResponseElement.length ) {
					message.insertAfter( ajaxResponseElement );
					message.addClass( 'inline' );
					message.addClass( 'yith-plugin-fw-animate__appear-from-top' ).show();
				}
			}
		} else {
			message = $( 'div#message.updated' );
			if ( message.length ) {
				message.addClass( 'inline' );
				message.addClass( 'yith-plugin-fw-animate__appear-from-top' ).show();
				message.on( 'click', '.notice-dismiss', function ( e ) {
					e.stopPropagation();
					message.removeClass( 'yith-plugin-fw-animate__appear-from-top' ).slideUp( 200 );
				} );
			}
		}
	} )();

	// Confirmation window when deleting custom post types and custom terms through Bulk Actions.
	if ( yith_plugin_fw_wp_pages.bulk_delete_confirmation_enabled ) {
		var bulkDeleteConfirmed = false;

		$( document ).on( 'click', '#doaction, #doaction2', function ( e ) {
			var doActionButton     = $( this ),
				bulkActionSelector = doActionButton.siblings( '#bulk-action-selector-top, #bulk-action-selector-bottom' );

			if ( 'yith' in window && 'ui' in yith ) {
				if ( bulkDeleteConfirmed ) {
					bulkDeleteConfirmed = false;
				} else {
					var confirmOptions = {},
						selectedItems  = $( '#the-list .check-column input[type=checkbox]:checked' );

					if ( selectedItems.length ) {
						switch ( bulkActionSelector.val() ) {
							case 'trash':
								confirmOptions.title             = yith_plugin_fw_wp_pages.i18n.bulk_trash_confirm_title;
								confirmOptions.message           = yith_plugin_fw_wp_pages.i18n.bulk_trash_confirm_message;
								confirmOptions.cancelButton      = yith_plugin_fw_wp_pages.i18n.bulk_trash_cancel_button;
								confirmOptions.confirmButton     = yith_plugin_fw_wp_pages.i18n.bulk_trash_confirm_button;
								confirmOptions.confirmButtonType = 'delete';
								break;
							case 'delete':
								confirmOptions.title             = yith_plugin_fw_wp_pages.i18n.bulk_delete_confirm_title;
								confirmOptions.message           = yith_plugin_fw_wp_pages.i18n.bulk_delete_confirm_message;
								confirmOptions.cancelButton      = yith_plugin_fw_wp_pages.i18n.bulk_delete_cancel_button;
								confirmOptions.confirmButton     = yith_plugin_fw_wp_pages.i18n.bulk_delete_confirm_button;
								confirmOptions.confirmButtonType = 'delete';
								break;
						}

						if ( !$.isEmptyObject( confirmOptions ) ) {
							e.preventDefault();

							confirmOptions.closeAfterConfirm = false;
							confirmOptions.onConfirm         = function () {
								bulkDeleteConfirmed = true;
								doActionButton.trigger( 'click' );
							};

							yith.ui.confirm( confirmOptions );
						}
					}
				}

			}
		} );
	}

	// Fix the WP footer
	( function () {
		var wrongWpFooter = $( '#wpbody #wpfooter' ),
			wpContent     = $( '#wpcontent' );

		if ( wrongWpFooter.length && wpContent.length ) {
			wpContent.append( wrongWpFooter );
		}
	} )();
} );