/* global wp_mail_smtp_logs, ajaxurl, flatpickr */
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

			if ( app.pageHolder.hasClass( 'wp-mail-smtp-page-logs-archive' ) ) {
				app.archive.ready();
			}

			app.pageHolder.trigger( 'WPMailSMTP.Admin.Logs.ready' );
		},

		/**
		 * Process all generic actions/events, mostly custom that were fired by our API.
		 *
		 * @since 1.5.0
		 */
		bindActions: function() {
			$( '.wp-mail-smtp-page-logs-archive' )
				.on( 'click', '#wp-mail-smtp-reset-filter .reset', app.archive.resetFilter );

			$( '.wp-mail-smtp-page-logs-single' )
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
				var $details = $( this ).closest( '.postbox' );

				if ( ! $details.hasClass( 'closed' ) ) {
					$details.find( '.inside' ).slideUp( 'fast', function() {
						$details.addClass( 'closed' );
						$details.find( '.handle-actions .dashicons' ).removeClass( 'dashicons-arrow-up' ).addClass( 'dashicons-arrow-down' );
					} );
				} else {
					$details.find( '.inside' ).slideDown( 'fast', function() {
						$details.removeClass( 'closed' );
						$details.find( '.handle-actions .dashicons' ).removeClass( 'dashicons-arrow-down' ).addClass( 'dashicons-arrow-up' );
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
				$( event.target ).closest( '.postbox' ).find( 'h2.hndle:not(.closed)' ).click();
			}
		},

		/**
		 * All the methods associated with the Archive view (list of email log entries).
		 *
		 * @since 1.5.0
		 */
		archive: {

			/**
			 * DOM is fully loaded.
			 *
			 * @since 2.8.0
			 */
			ready: function() {

				app.archive.initFlatpickr();
			},

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
			},

			/**
			 * Init date picker.
			 *
			 * @since 2.8.0
			 */
			initFlatpickr: function() {

				var flatpickrLocale = {
						rangeSeparator: ' - ',
					},
					args = {
						altInput: true,
						altFormat: 'M j, Y',
						dateFormat: 'Y-m-d',
						mode: 'range'
					};

				if (
					flatpickr !== 'undefined' &&
					Object.prototype.hasOwnProperty.call( flatpickr, 'l10ns' ) &&
					Object.prototype.hasOwnProperty.call( flatpickr.l10ns, wp_mail_smtp_logs.lang_code )
				) {
					flatpickrLocale = flatpickr.l10ns[ wp_mail_smtp_logs.lang_code ];

					// Rewrite separator for all locales to make filtering work.
					flatpickrLocale.rangeSeparator = ' - ';
				}

				args.locale = flatpickrLocale;

				$( '.wp-mail-smtp-filter-date-selector' ).flatpickr( args );
			},

			/**
			 * Reset filter handler.
			 *
			 * @since 2.8.0
			 */
			resetFilter: function() {

				var $form = $( this ).parents( 'form' );
				$form.find( $( this ).data( 'scope' ) ).find( 'input,select' ).each( function() {

					var $this = $( this );
					if ( app.isIgnoredForResetInput( $this ) ) {
						return;
					}
					app.resetInput( $this );
				} );

				// Submit the form.
				$form.submit();
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
		},

		/**
		 * Reset input.
		 *
		 * @since 2.8.0
		 *
		 * @param {object} $input Input element.
		 */
		resetInput: function( $input ) {

			switch ( $input.prop( 'tagName' ).toLowerCase() ) {
				case 'input':
					$input.val( '' );
					break;
				case 'select':
					$input.val( $input.find( 'option' ).first().val() );
					break;
			}
		},

		/**
		 * Input is ignored for reset.
		 *
		 * @since 2.8.0
		 *
		 * @param {object} $input Input element.
		 *
		 * @returns {boolean} Is ignored.
		 */
		isIgnoredForResetInput: function( $input ) {

			return [ 'submit', 'hidden' ].indexOf( ( $input.attr( 'type' ) || '' ).toLowerCase() ) !== -1 &&
				! $input.hasClass( 'flatpickr-input' );
		},
	};

	// Provide access to public functions/properties.
	return app;
}( document, window, jQuery ) );

// Initialize.
WPMailSMTP.Admin.Logs.init();
