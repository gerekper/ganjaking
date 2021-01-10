/* global wp_mail_smtp_logs, ajaxurl */
'use strict';

var WPMailSMTP = window.WPMailSMTP || {};
WPMailSMTP.Admin = WPMailSMTP.Admin || {};

/**
 * WP Mail SMTP Admin area Logs module.
 *
 * @since 1.5.0
 */
WPMailSMTP.Admin.Logs = WPMailSMTP.Admin.Logs || ( function( document, window, $ ) {

	/**
	 * Public functions and properties.
	 *
	 * @since 1.5.0
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Start the engine. DOM is not ready yet, use only to init something.
		 *
		 * @since 1.5.0
		 */
		init: function() {

			// Do that when DOM is ready.
			$( document ).ready( app.ready );
		},

		/**
		 * DOM is fully loaded.
		 *
		 * @since 1.5.0
		 */
		ready: function() {

			app.pageHolder = $( '.wp-mail-smtp-page-logs' );

			app.bindActions();

			app.pageHolder.trigger( 'WPMailSMTP.Admin.Logs.ready' );
		},

		/**
		 * Process all generic actions/events, mostly custom that were fired by our API.
		 *
		 * @since 1.5.0
		 */
		bindActions: function() {
			jQuery( '.wp-mail-smtp-page-logs-single' )
				.on( 'click', '.js-wp-mail-smtp-pro-logs-email-delete', app.single.processDelete )
				.on( 'click', '.js-wp-mail-smtp-pro-logs-toggle-extra-details', app.single.processExtraDetailsToggle )
				.on( 'click', '.js-wp-mail-smtp-pro-logs-close-extra-details', app.single.processExtraDetailsClose );

			app.pageHolder.on( 'click', '#wp-mail-smtp-delete-all-logs-button', app.archive.deleteAllEmailLogs );
		},

		/**
		 * All the methods associated with the Single Email view.
		 *
		 * @since 1.5.0
		 */
		single: {

			/**
			 * Process single email deletion.
			 *
			 * @since 1.5.0
			 *
			 * @returns {boolean} Whether to remove or not.
			 */
			processDelete: function() {
				return confirm( wp_mail_smtp_logs.text_email_delete_sure );
			},

			/**
			 * Process the click on extra details header to open/close.
			 *
			 * @since 1.5.0
			 */
			processExtraDetailsToggle: function() {
				var $details = $( this );

				if ( $details.hasClass( 'open' ) ) {
					$details.siblings( '.email-header-details' ).slideUp( 'fast', function() {
						$details.removeClass( 'open' );
						$details.find( '.dashicons' ).removeClass( 'dashicons-arrow-up' ).addClass( 'dashicons-arrow-down' );
					} );
				} else {
					$details.siblings( '.email-header-details' ).slideDown( 'fast', function() {
						$details.addClass( 'open' );
						$details.find( '.dashicons' ).removeClass( 'dashicons-arrow-down' ).addClass( 'dashicons-arrow-up' );
					} );
				}
			},

			/**
			 * Process the click on close details button.
			 *
			 * @since 1.5.0
			 *
			 * @param {object} event jQuery event.
			 */
			processExtraDetailsClose: function( event ) {
				jQuery( event.target ).parents( '.email-extra-details' ).find( 'h2.open' ).click();
			}
		},

		/**
		 * All the methods associated with the Archive view (list of email log entries).
		 *
		 * @since 1.5.0
		 */
		archive: {

			/**
			 * Process the click on the delete all email logs button.
			 *
			 * @since 2.5.0
			 *
			 * @param {object} event jQuery event.
			 */
			deleteAllEmailLogs: function( event ) {

				event.preventDefault();

				var $btn = $( event.target );

				$.confirm( {
					backgroundDismiss: false,
					escapeKey: true,
					animationBounce: 1,
					theme: 'modern',
					animateFromElement: false,
					draggable: false,
					closeIcon: true,
					useBootstrap: false,
					type: 'orange',
					boxWidth: '450px',
					icon: '"></i><img src="' + wp_mail_smtp_logs.plugin_url + '/assets/images/font-awesome/exclamation-circle-solid-orange.svg" style="width: 40px; height: 40px;" alt="' + wp_mail_smtp_logs.icon + '"><i class="',
					title: wp_mail_smtp_logs.heads_up_title,
					content: wp_mail_smtp_logs.delete_all_email_logs_confirmation_text,
					buttons: {
						confirm: {
							text: wp_mail_smtp_logs.yes_text,
							btnClass: 'wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-orange',
							keys: [ 'enter' ],
							action: function() {
								app.archive.executeAllEmailLogEntriesDeletion( $btn );
							}
						},
						cancel: {
							text: wp_mail_smtp_logs.cancel_text
						}
					}
				} );
			},

			/**
			 * AJAX call for deleting all email logs.
			 *
			 * @since 2.5.0
			 *
			 * @param {object} $btn jQuery object of the clicked button.
			 */
			executeAllEmailLogEntriesDeletion: function( $btn ) {

				$btn.prop( 'disabled', true );

				var data = {
					action: 'wp_mail_smtp_delete_all_log_entries',
					nonce: $( '#wp-mail-smtp-delete-log-entries-nonce', app.pageHolder ).val()
				};

				$.post( ajaxurl, data, function( response ) {
					var message = response.data,
						icon,
						type,
						callback;

					if ( response.success ) {
						icon     = 'check-circle-solid-green';
						type     = 'green';
						callback = function() {
							location.reload();
							return false;
						};
					} else {
						icon     = 'exclamation-circle-regular-red';
						type     = 'red';
						callback = function() {};
					}

					app.displayModal( message, icon, type, callback );
					$btn.prop( 'disabled', false );
				} ).fail( function() {
					app.displayModal( wp_mail_smtp_logs.error_occurred, 'exclamation-circle-regular-red', 'red', function() {} );
					$btn.prop( 'disabled', false );
				} );
			}
		},

		/**
		 * Display the modal with provided text and icon.
		 *
		 * @since 2.5.0
		 *
		 * @param {string}   message        The message to be displayed in the modal.
		 * @param {string}   icon           The icon name from /assets/images/font-awesome/ to be used in modal.
		 * @param {string}   type           The type of the message (red, green, orange, blue, purple, dark).
		 * @param {Function} actionCallback The action callback function.
		 */
		displayModal: function( message, icon, type, actionCallback ) {

			$.alert( {
				backgroundDismiss: true,
				escapeKey: true,
				animationBounce: 1,
				theme: 'modern',
				type: type,
				animateFromElement: false,
				draggable: false,
				closeIcon: true,
				useBootstrap: false,
				title: false,
				icon: '"></i><img src="' + wp_mail_smtp_logs.plugin_url + '/assets/images/font-awesome/' + icon + '.svg" style="width: 40px; height: 40px;" alt="' + wp_mail_smtp_logs.icon + '"><i class="',
				content: message,
				boxWidth: '350px',
				buttons: {
					confirm: {
						text: wp_mail_smtp_logs.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
						action: actionCallback
					}
				}
			} );
		}
	};

	// Provide access to public functions/properties.
	return app;
}( document, window, jQuery ) );

// Initialize.
WPMailSMTP.Admin.Logs.init();
