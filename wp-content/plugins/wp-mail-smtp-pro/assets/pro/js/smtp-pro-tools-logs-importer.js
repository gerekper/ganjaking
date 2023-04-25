/* global wp_mail_smtp, wp_mail_smtp_tools_importer */

'use strict';

var WPMailSMTP = window.WPMailSMTP || {};
WPMailSMTP.Admin = WPMailSMTP.Admin || {};
WPMailSMTP.Admin.Tools = WPMailSMTP.Admin.Tools || {};

/**
 * WPMailSMTP Importer functionality.
 *
 * @since 3.8.0
 */
WPMailSMTP.Admin.Tools.LogsImporter = WPMailSMTP.Admin.Tools.LogsImporter || ( function( document, window, $ ) {

	/**
	 * Elements.
	 *
	 * @since 3.8.0
	 *
	 * @type {object}
	 */
	var el = {
		$resultNoticeSection: $( '#wp-mail-smtp-log-importer-result-notice' ),
		$resultNoticeTextSection: $( '#wp-mail-smtp-log-importer-result-notice > p' ),
		$importButton: $( '#wp-mail-smtp-tools-log-importer-button' ),
		$importButtonTxt: $( '#wp-mail-smtp-tools-log-importer-button > .wp-mail-smtp-btn-text' ),
		$importButtonSpinner: $( '#wp-mail-smtp-tools-log-importer-button > .wp-mail-smtp-btn-spinner' ),
		$importLoader: $( '#wp-mail-smtp-tools-log-importer-loader' ),
		$descriptionSection: $( '#wp-mail-smtp-pro-import-description-section' ),
		$importedLogsCount: $( '.wp-mail-smtp-pro-importer-imported-logs-count' ),
		$logsToImportCount: $( '.wp-mail-smtp-pro-importer-logs-to-import-count' ),
		$importProgressSection: $( '#wp-mail-smtp-pro-import-progress-section' ),
		$summarySection: $( '#wp-mail-smtp-pro-import-summary' ),
		$summarySuccessCount: $( '#wp-mail-smtp-pro-import-summary-success-count' ),
		$summaryFailedCount: $( '#wp-mail-smtp-pro-import-summary-failed-count' ),
		$summaryAttachmentFailedCount: $( '#wp-mail-smtp-pro-import-summary-attachment-failed-count' ),
	};

	/**
	 * Runtime variables.
	 *
	 * @since 3.8.0
	 *
	 * @type {object}
	 */
	var runtime = {
		successfulImportCount: 0,
		failedImportCount: 0,
		failedAttachmentCount: 0,
		errorMessages: [],
		failedImportIds: [],
	};

	/**
	 * Notice severity.
	 *
	 * @since 3.8.0
	 *
	 * @type {object}
	 */
	var NOTICE_SEVERITY = {
		SUCCESS: 1,
		WARNING: 2,
		FAIL: 3,
	};

	/**
	 * Public functions and properties.
	 *
	 * @since 3.8.0
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Start the engine.
		 *
		 * @since 3.8.0
		 */
		init: function() {

			$( app.ready );
		},

		/**
		 * Document ready.
		 *
		 * @since 3.8.0
		 */
		ready: function() {

			app.setupUI();
			app.bindActions();
		},

		/**
		 * Setup UI.
		 *
		 * @since 3.8.0
		 */
		setupUI: function() {

			el.$logsToImportCount.text( wp_mail_smtp_tools_importer.logs_to_import_count );
		},

		/**
		 * Bind UI interactions.
		 *
		 * @since 3.8.0
		 */
		bindActions: function() {

			el.$importButton.on( 'click', app.initializeImportProcess );
		},

		/**
		 * Import button click event handler.
		 *
		 * @since 3.8.0
		 *
		 * @param {Event} e Event object.
		 */
		initializeImportProcess: function( e ) {

			e.preventDefault();

			el.$importButton.prop( 'disabled', true );
			el.$importButton.hide();
			el.$importLoader.show();

			app.sendAjaxRequest();
		},

		/**
		 * Send AJAX request to the importer.
		 *
		 * @since 3.8.0
		 */
		sendAjaxRequest: function() {

			var data = {
				action: 'wp_mail_smtp_importer_ajax_wp_mail_logging_importer',
				nonce: wp_mail_smtp_tools_importer.nonce,
			};

			$.post(
				wp_mail_smtp.ajax_url,
				data,
				function( response ) {

					app.handleResponse( response.data );

					if ( ! response.success ) {
						app.handleErrors( response.data );
						return;
					}

					if ( response.data.continue === true ) {
						app.updateProgressSection();
						app.sendAjaxRequest();
						return;
					}

					app.importDone();
					app.showSummary();
				}
			);
		},

		/**
		 * Handle the AJAX response.
		 *
		 * @since {VERISON}
		 *
		 * @param {object} data The AJAX response.
		 */
		handleResponse: function( data ) {

			if ( data.successful_import_count ) {
				runtime.successfulImportCount += parseInt( data.successful_import_count, 10 );
			}

			if ( data.failed_import_count ) {
				runtime.failedImportCount += parseInt( data.failed_import_count, 10 );
			}

			if ( data.failed_attachment_count ) {
				runtime.failedAttachmentCount += parseInt( data.failed_attachment_count, 10 );
			}
		},

		/**
		 * Handle import errors.
		 *
		 * @since 3.8.0
		 *
		 * @param {object} data Failed import AJAX response data.
		 */
		handleErrors: function( data ) {

			if ( ! data.error_message ) {
				return;
			}

			runtime.errorMessages.push( data.error_message );

			app.importDone();
			app.showResultNotice( NOTICE_SEVERITY.FAIL, runtime.errorMessages.join( '.' ) );
		},

		/**
		 * Update the response section with progress.
		 *
		 * @since 3.8.0
		 */
		updateProgressSection: function() {
			el.$importedLogsCount.text( runtime.successfulImportCount );
			el.$importProgressSection.show();
		},

		/**
		 * Import done action.
		 *
		 * @since 3.8.0
		 */
		importDone: function() {

			el.$descriptionSection.hide();
			el.$importProgressSection.hide();
			el.$importLoader.hide();
		},

		/**
		 * Show the import summary.
		 *
		 * @since 3.8.0
		 */
		showSummary: function() {

			if ( ( runtime.failedImportCount + runtime.failedAttachmentCount ) === parseInt( wp_mail_smtp_tools_importer.logs_to_import_count, 10 ) ) {
				app.setupSummary();
				app.showResultNotice( NOTICE_SEVERITY.FAIL );
				el.$summarySection.show();
			} else if ( runtime.failedImportCount > 0 || runtime.failedAttachmentCount > 0 ) {
				app.setupSummary();
				app.showResultNotice( NOTICE_SEVERITY.WARNING );
				el.$summarySection.show();
			} else {
				app.showResultNotice( NOTICE_SEVERITY.SUCCESS );
			}

		},

		/**
		 * Setup the summary.
		 *
		 * @since 3.8.0
		 */
		setupSummary: function() {

			// Show summary if there's any error.
			el.$summarySuccessCount.text( runtime.successfulImportCount );
			el.$summaryFailedCount.text( runtime.failedImportCount );
			el.$summaryAttachmentFailedCount.text( runtime.failedAttachmentCount );
		},

		/**
		 * Show result notice.
		 *
		 * @since 3.8.0
		 *
		 * @param {number} noticeSeverity Severity of the notice.
		 * @param {string} appendMessage  Message to be included in the notice.
		 */
		showResultNotice: function( noticeSeverity, appendMessage = '' ) {

			var severityMessage = '';

			switch ( noticeSeverity ) {
				case NOTICE_SEVERITY.SUCCESS:
					el.$resultNoticeSection.addClass( 'notice-success' );
					severityMessage = wp_mail_smtp_tools_importer.notice_success;
					break;
				case NOTICE_SEVERITY.WARNING:
					el.$resultNoticeSection.addClass( 'notice-warning' );
					severityMessage = wp_mail_smtp_tools_importer.notice_warning;
					break;
				case NOTICE_SEVERITY.FAIL:
					el.$resultNoticeSection.addClass( 'notice-error' );
					severityMessage = wp_mail_smtp_tools_importer.notice_fail;
					break;
				default:
					break;
			}

			var noticeMessage = severityMessage;

			if ( appendMessage !== '' ) {
				noticeMessage += ' ' + appendMessage;
			}

			el.$resultNoticeTextSection.text( noticeMessage );
			el.$resultNoticeSection.show();
		}
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

// Initialize.
WPMailSMTP.Admin.Tools.LogsImporter.init();
