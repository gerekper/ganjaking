/* global wp_mail_smtp, wp_mail_smtp_logs, flatpickr */
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
	 * Private functions and properties.
	 *
	 * @since 2.9.0
	 *
	 * @type {object}
	 */
	var __private = {

		/**
		 * Whether the email is valid.
		 *
		 * @since 2.9.0
		 *
		 * @param {string} email Email address.
		 *
		 * @returns {boolean} Whether email is valid or not.
		 */
		isEmailValid: function( email ) {

			var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			return re.test( String( email ).toLowerCase() );
		},

		/**
		 * Whether the emails are valid.
		 *
		 * @since 2.9.0
		 *
		 * @param {string} emails Email addresses.
		 *
		 * @returns {boolean} Whether emails are valid or not.
		 */
		areEmailsValid: function( emails ) {

			if ( ! Array.isArray( emails ) ) {
				emails = emails.split( ',' );
			}

			for ( var i = 0; i < emails.length; i++ ) {
				var email = emails[ i ].trim();
				if ( ! __private.isEmailValid( email ) ) {
					return false;
				}
			}

			return true;
		}
	};

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
			$( app.ready );
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
				.on( 'click', '#wp-mail-smtp-reset-filter .reset', app.archive.resetFilter )
				.on( 'click', '#doaction, #doaction2', app.archive.onBulkSubmit );

			$( '.wp-mail-smtp-page-logs-single' )
				.on( 'click', '.js-wp-mail-smtp-pro-logs-email-delete', app.single.processDelete )
				.on( 'click', '.js-wp-mail-smtp-pro-logs-toggle-extra-details', app.single.processExtraDetailsToggle )
				.on( 'click', '.js-wp-mail-smtp-pro-logs-close-extra-details', app.single.processExtraDetailsClose )
				.on( 'click', '#wp-mail-smtp-email-actions .wp-mail-smtp-email-log-resend > a', app.single.processResendEmail );

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
				$( event.target ).closest( '.postbox' ).find( 'h2.hndle:not(.closed)' ).trigger( 'click' );
			},

			/**
			 * Process the click on resend email button.
			 *
			 * @since 2.9.0
			 *
			 * @param {object} event jQuery event.
			 */
			processResendEmail: function( event ) {

				event.preventDefault();

				app.displayConfirmModal( wp_mail_smtp_logs.resend_email_confirmation_text, function() {

					var emailId = wp_mail_smtp_logs.email_id,
						emailRecipients = this.$content.find( 'input[name="email"]' ).val();

					if ( ! __private.areEmailsValid( emailRecipients ) ) {
						app.displayModal(
							wp_mail_smtp_logs.resend_email_invalid_recipients_addresses,
							'exclamation-circle-regular-red',
							'red'
						);
						return false;
					}

					app.displayModal( function() {
						return app.single.resendEmail( emailId, emailRecipients, this );
					} );
				} );
			},

			/**
			 * AJAX call for resend email.
			 *
			 * @since 2.9.0
			 *
			 * @param {int} emailId Email id.
			 * @param {string} recipients Email recipients.
			 * @param {object} modal jquery-confirm object.
			 *
			 * @returns {jqXHR} xhr object.
			 */
			resendEmail: function( emailId, recipients, modal ) {

				var data = {
					'action': 'wp_mail_smtp_resend_email',
					'nonce': wp_mail_smtp.nonce,
					'email_id': emailId,
					'recipients': recipients
				};

				modal.setTitle( wp_mail_smtp_logs.resend_email_processing_text );

				return $.post( wp_mail_smtp.ajax_url, data, function( response ) {

					var message = response.data;

					modal.setTitle( '' );

					if ( response.success ) {
						modal.setType( 'green' );
						modal.setIcon( app.getModalIcon( 'check-circle-solid-green' ) );
					} else {
						modal.setType( 'red' );
						modal.setIcon( app.getModalIcon( 'exclamation-circle-regular-red' ) );
					}

					modal.setContent( message );
				} ).fail( function() {
					modal.setTitle( '' );
					modal.setType( 'red' );
					modal.setIcon( app.getModalIcon( 'exclamation-circle-regular-red' ) );
					modal.setContent( wp_mail_smtp_logs.error_occurred );
				} ).always( function() {

					// If modal was closed by click to background, open it after getting response.
					if ( ! modal.isOpen() ) {
						modal.open();
					}
				} );
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

				app.displayConfirmModal( wp_mail_smtp_logs.delete_all_email_logs_confirmation_text, function() {
					app.archive.executeAllEmailLogEntriesDeletion( $btn );
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

				$.post( wp_mail_smtp.ajax_url, data, function( response ) {
					var message = response.data,
						icon,
						type,
						callback;

					if ( response.success ) {
						icon = 'check-circle-solid-green';
						type = 'green';
						callback = function() {
							location.reload();
							return false;
						};
					} else {
						icon = 'exclamation-circle-regular-red';
						type = 'red';
					}

					app.displayModal( message, icon, type, callback );
					$btn.prop( 'disabled', false );
				} ).fail( function() {
					app.displayModal( wp_mail_smtp_logs.error_occurred, 'exclamation-circle-regular-red', 'red' );
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
				$form.trigger( 'submit' );
			},

			/**
			 * Process the bulk action.
			 *
			 * @since 2.9.0
			 *
			 * @param {object} event jQuery event.
			 */
			onBulkSubmit: function( event ) {

				var action = $( this ).parents( '.bulkactions' ).find( 'select[name^=action]' ).val(),
					ids = [];

				$( '.wp-list-table.emails input[name="email_id[]"]:checked' ).each( function() {
					ids.push( $( this ).val() );
				} );

				if ( ids.length === 0 ) {
					return;
				}

				if ( action === 'resend' ) {
					event.preventDefault();
					app.archive.processResendEmails( ids );
				}
			},

			/**
			 * Reset bulk actions UI.
			 *
			 * @since 2.9.0
			 */
			resetBulkActionUI: function() {

				// Reset bulk action select.
				$( '.bulkactions select[name^=action]' ).val( '-1' );

				// Reset items checkboxes.
				$( '.wp-list-table.emails input[name="email_id[]"]' ).prop( 'checked', false );

				// Reset select all checkbox.
				$( '.wp-list-table.emails input[id^="cb-select-all"]' ).prop( 'checked', false );
			},

			/**
			 * Process resend emails bulk action.
			 *
			 * @since 2.9.0
			 *
			 * @param {Array<int>} ids Email ids.
			 */
			processResendEmails: function( ids ) {

				app.displayConfirmModal( wp_mail_smtp_logs.bulk_resend_email_confirmation_text, function() {
					app.displayModal( function() {
						return app.archive.resendEmails( ids, this );
					} );
				} );
			},

			/**
			 * AJAX call for resend emails.
			 *
			 * @since 2.9.0
			 *
			 * @param {Array<int>} ids Email ids.
			 * @param {object} modal jquery-confirm object.
			 *
			 * @returns {jqXHR} xhr object for this request.
			 */
			resendEmails: function( ids, modal ) {

				var data = {
					'action': 'wp_mail_smtp_bulk_resend_emails',
					'nonce': wp_mail_smtp.nonce,
					'email_ids': ids
				};

				modal.setTitle( wp_mail_smtp_logs.bulk_resend_email_processing_text );

				return $.post( wp_mail_smtp.ajax_url, data, function( response ) {

					var message = response.data;

					modal.setTitle( '' );

					if ( response.success ) {
						modal.setType( 'green' );
						modal.setIcon( app.getModalIcon( 'check-circle-solid-green' ) );
						app.archive.resetBulkActionUI();
					} else {
						modal.setType( 'red' );
						modal.setIcon( app.getModalIcon( 'exclamation-circle-regular-red' ) );
					}

					modal.setContent( message );
				} ).fail( function() {
					modal.setTitle( '' );
					modal.setType( 'red' );
					modal.setIcon( app.getModalIcon( 'exclamation-circle-regular-red' ) );
					modal.setContent( wp_mail_smtp_logs.error_occurred );
				} ).always( function() {

					// If modal was closed by click to background, open it after getting response.
					if ( ! modal.isOpen() ) {
						modal.open();
					}
				} );
			}
		},

		/**
		 * Display confirmation modal with provided text.
		 *
		 * @since 2.9.0
		 *
		 * @param {string}   message        The message to be displayed in the modal.
		 * @param {Function} actionCallback The action callback function.
		 */
		displayConfirmModal: function( message, actionCallback ) {

			$.confirm( {
				backgroundDismiss: false,
				escapeKey: true,
				animationBounce: 1,
				type: 'orange',
				icon: app.getModalIcon( 'exclamation-circle-solid-orange' ),
				title: wp_mail_smtp_logs.heads_up_title,
				content: message,
				buttons: {
					confirm: {
						text: wp_mail_smtp_logs.yes_text,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
						action: actionCallback
					},
					cancel: {
						text: wp_mail_smtp_logs.cancel_text,
						btnClass: 'btn-cancel',
					}
				}
			} );
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

			type = type || 'default';
			actionCallback = actionCallback || function() {
			};

			$.alert( {
				backgroundDismiss: true,
				escapeKey: true,
				animationBounce: 1,
				type: type,
				title: false,
				icon: icon ? app.getModalIcon( icon ) : '',
				content: message,
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
		 * Returns prepared modal icon.
		 *
		 * @since 2.9.0
		 *
		 * @param {string} icon The icon name from /assets/images/font-awesome/ to be used in modal.
		 *
		 * @returns {string} Modal icon HTML.
		 */
		getModalIcon: function( icon ) {

			return '"></i><img src="' + wp_mail_smtp_logs.plugin_url + '/assets/images/font-awesome/' + icon + '.svg" style="width: 40px; height: 40px;" alt="' + wp_mail_smtp_logs.icon + '"><i class="';
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

		/**
		 * Open link in new tab.
		 *
		 * @since 2.9.0
		 *
		 * @param {object} event jQuery event.
		 */
		openLinkInNewTab: function( event ) {

			event.preventDefault();
			window.open( $( this ).attr( 'href' ) );
		}
	};

	// Provide access to public functions/properties.
	return app;
}( document, window, jQuery ) );

// Initialize.
WPMailSMTP.Admin.Logs.init();
