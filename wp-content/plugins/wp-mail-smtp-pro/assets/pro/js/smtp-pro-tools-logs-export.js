/* global wp_mail_smtp_tools_export_email_logs, ajaxurl, flatpickr */
/**
 * WPMailSmtp Email Logs Export function.
 *
 * @since 2.8.0
 */

'use strict';

var WPMailSmtpEmailLogsExport = window.WPMailSmtpEmailLogsExport || ( function( document, window, $ ) {

	/**
	 * Elements.
	 *
	 * @since 2.8.0
	 *
	 * @type {object}
	 */
	var el = {
		$form                : $( '#wp-mail-smtp-tools-export-email-logs' ),
		$dateFlatpickr       : $( '#wp-mail-smtp-tools-export-email-logs-date-flatpickr' ),
		$submitButton        : $( '#wp-mail-smtp-tools-export-email-logs-submit' ),
		$cancelButton        : $( '#wp-mail-smtp-tools-export-email-logs-cancel' ),
		$processMsg          : $( '#wp-mail-smtp-tools-export-email-logs-process-msg' ),
	};

	/**
	 * Shorthand to translated strings.
	 *
	 * @since 2.8.0
	 *
	 * @type {object}
	 */
	var i18n = wp_mail_smtp_tools_export_email_logs.i18n;

	/**
	 * Runtime variables.
	 *
	 * @since 2.8.0
	 *
	 * @type {object}
	 */
	var vars = {};

	/**
	 * Public functions and properties.
	 *
	 * @since 2.8.0
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Start the engine.
		 *
		 * @since 2.8.0
		 */
		init: function() {

			$( app.ready );
		},

		/**
		 * Document ready.
		 *
		 * @since 2.8.0
		 */
		ready: function() {

			vars.processing = false;

			app.initDateRange();
			app.initSubmit();
			app.events();
		},

		/**
		 * Register JS events.
		 *
		 * @since 2.8.0
		 */
		events: function() {

			// Display file download error.
			$( document ).on( 'csv_file_error', function( e, msg ) {
				app.displaySubmitMsg( msg, 'error' );
			} );
		},

		/**
		 * Export step ajax request.
		 *
		 * @since 2.8.0
		 *
		 * @param {integer} step Current step.
		 * @param {string} requestId Request Identifier.
		 */
		exportAjaxStep: function( step, requestId ) {

			if ( ! step ) {
				step = 1;
			}

			var ajaxData;

			if ( ! vars.processing ) {
				return;
			}

			ajaxData = app.getAjaxPostData( step, requestId );
			$.post( ajaxurl, ajaxData )
				.done( function( res ) {

					if ( res.success && res.data.total_steps > step ) {
						app.exportAjaxStep( ++step, res.data.request_id );
						return;
					}

					var msg = '';
					clearTimeout( vars.timerId );
					if ( ! res.success ) {
						app.displaySubmitMsg( res.data.error, 'error' );
						app.displaySubmitSpinner( false );
						return;
					}
					if ( res.data.count === 0 ) {
						app.displaySubmitMsg( i18n.prc_2_no_email_logs );
						app.displaySubmitSpinner( false );
						return;
					}
					msg = i18n.prc_3_done;
					msg += '<br>' + i18n.prc_3_download + ', <a href="#" class="wp-mail-smtp-download-link">' + i18n.prc_3_click_here + '</a>.';
					app.displaySubmitMsg( msg, 'info' );
					app.displaySubmitSpinner( false );
					app.triggerDownload( res.data.request_id );
				} )
				.fail( function( jqXHR, textStatus, errorThrown ) {
					clearTimeout( vars.timerId );
					app.displaySubmitMsg( i18n.error_prefix + ':<br>' + errorThrown, 'error' );
					app.displaySubmitSpinner( false );
				} );
		},

		/**
		 * Get export step ajax POST data.
		 *
		 * @since 2.8.0
		 *
		 * @param {integer} step Current step.
		 * @param {string} requestId Request Identifier.
		 *
		 * @returns {object} Ajax POST data.
		 */
		getAjaxPostData: function( step, requestId ) {

			var ajaxData;

			if ( step === 1 ) {
				ajaxData = el.$form.serialize();
			} else {
				ajaxData = {
					'action'    : 'wp_mail_smtp_tools_export_email_logs',
					'nonce'     : wp_mail_smtp_tools_export_email_logs.nonce,
					'request_id': requestId,
					'step'      : step,
				};
			}

			return ajaxData;
		},

		/**
		 * Submit button click.
		 *
		 * @since 2.8.0
		 */
		initSubmit: function() {

			el.$submitButton.on( 'click', function( e ) {

				e.preventDefault();

				if ( $( this ).hasClass( 'wp-mail-smtp-btn-spinner-on' ) ) {
					return;
				}

				el.$submitButton.blur();
				app.displaySubmitSpinner( true );
				app.displaySubmitMsg( '' );

				vars.timerId = setTimeout(
					function() {
						app.displaySubmitMsg( i18n.prc_1_filtering + '<br>' + i18n.prc_1_please_wait, 'info' );
					},
					3000
				);

				app.exportAjaxStep( 1 );

			} );

			el.$cancelButton.on( 'click', function( e ) {

				e.preventDefault();
				el.$cancelButton.blur();
				app.displaySubmitMsg( '' );
				app.displaySubmitSpinner( false );
			} );
		},

		/**
		 * Init Flatpickr at Date Range field.
		 *
		 * @since 2.8.0
		 */
		initDateRange: function() {

			var langCode = wp_mail_smtp_tools_export_email_logs.lang_code,
				flatpickrLocale = {
					rangeSeparator: ' - ',
				};

			if (
				flatpickr !== 'undefined' &&
				Object.prototype.hasOwnProperty.call( flatpickr, 'l10ns' ) &&
				Object.prototype.hasOwnProperty.call( flatpickr.l10ns, langCode )
			) {
				flatpickrLocale = flatpickr.l10ns[ langCode ];
				flatpickrLocale.rangeSeparator = ' - ';
			}

			el.$dateFlatpickr.flatpickr( {
				altInput: true,
				altFormat: 'M j, Y',
				dateFormat: 'Y-m-d',
				locale: flatpickrLocale,
				mode: 'range'
			} );
		},

		/**
		 * Show/hide submit button spinner.
		 *
		 * @since 2.8.0
		 *
		 * @param {boolean} show Show or hide the submit button spinner.
		 */
		displaySubmitSpinner: function( show ) {

			if ( show ) {
				el.$submitButton.addClass( 'wp-mail-smtp-btn-spinner-on' );
				el.$cancelButton.removeClass( 'hidden' );
				vars.processing = true;
			} else {
				el.$submitButton.removeClass( 'wp-mail-smtp-btn-spinner-on' );
				el.$cancelButton.addClass( 'hidden' );
				vars.processing = false;
			}
		},

		/**
		 * Display message under submit button.
		 *
		 * @since 2.8.0
		 *
		 * @param {string} msg  Message.
		 * @param {string} type Use 'error' for errors messages.
		 */
		displaySubmitMsg: function( msg, type ) {

			if ( ! vars.processing ) {
				return;
			}

			if ( type && 'error' === type ) {
				el.$processMsg.addClass( 'wp-mail-smtp-error' );
			} else {
				el.$processMsg.removeClass( 'wp-mail-smtp-error' );
			}

			el.$processMsg.html( msg );

			if ( msg.length > 0 ) {
				el.$processMsg.removeClass( 'hidden' );
			} else {
				el.$processMsg.addClass( 'hidden' );
			}
		},

		/**
		 * Initiating file downloading.
		 *
		 * @since 2.8.0
		 *
		 * @param {string} requestId Request ID.
		 */
		triggerDownload: function( requestId ) {

			var url = wp_mail_smtp_tools_export_email_logs.export_page;

			url += '&action=wp_mail_smtp_tools_export_download_result';
			url += '&nonce=' + wp_mail_smtp_tools_export_email_logs.nonce;
			url += '&request_id=' + requestId;

			el.$form.find( 'iframe' ).remove();
			el.$form.append( '<iframe src="' + url + '"></iframe>' );
			el.$processMsg.find( '.wp-mail-smtp-download-link' ).attr( 'href', url );
		}
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

// Initialize.
WPMailSmtpEmailLogsExport.init();
