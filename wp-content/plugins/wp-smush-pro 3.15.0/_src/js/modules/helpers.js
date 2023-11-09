/* global WP_Smush */
/* global ajaxurl */
/* global wp_smush_msgs */

/**
 * Helpers functions.
 *
 * @since 2.9.0  Moved from admin.js
 */
( function() {
	'use strict';

	WP_Smush.helpers = {
		init: () => {},
		cacheUpsellErrorCodes: [],

		/**
		 * Convert bytes to human-readable form.
		 *
		 * @param {number} a Bytes
		 * @param {number} b Number of digits
		 * @return {*} Formatted Bytes
		 */
		formatBytes: ( a, b ) => {
			const thresh = 1024,
				units = [ 'KB', 'MB', 'GB', 'TB', 'PB' ];

			if ( Math.abs( a ) < thresh ) {
				return a + ' B';
			}

			let u = -1;

			do {
				a /= thresh;
				++u;
			} while ( Math.abs( a ) >= thresh && u < units.length - 1 );

			return a.toFixed( b ) + ' ' + units[ u ];
		},

		/**
		 * Get size from a string.
		 *
		 * @param {string} formattedSize Formatter string
		 * @return {*} Formatted Bytes
		 */
		getSizeFromString: ( formattedSize ) => {
			return formattedSize.replace( /[a-zA-Z]/g, '' ).trim();
		},

		/**
		 * Get type from formatted string.
		 *
		 * @param {string} formattedSize Formatted string
		 * @return {*} Formatted Bytes
		 */
		getFormatFromString: ( formattedSize ) => {
			return formattedSize.replace( /[0-9.]/g, '' ).trim();
		},

		/**
		 * Stackoverflow: http://stackoverflow.com/questions/1726630/formatting-a-number-with-exactly-two-decimals-in-javascript
		 *
		 * @param {number} num
		 * @param {number} decimals
		 * @return {number}  Number
		 */
		precise_round: ( num, decimals ) => {
			const sign = num >= 0 ? 1 : -1;
			// Keep the percentage below 100.
			num = num > 100 ? 100 : num;
			return (
				Math.round( num * Math.pow( 10, decimals ) + sign * 0.001 ) /
				Math.pow( 10, decimals )
			);
		},

		/**
		 * Displays a floating error message using the #wp-smush-ajax-notice container.
		 *
		 * @since 3.8.0
		 *
		 * @param {string} message
		 */
		showErrorNotice: ( message ) => {
			if ( 'undefined' === typeof message ) {
				return;
			}

			const noticeMessage = `<p>${ message }</p>`,
				noticeOptions = {
					type: 'error',
					icon: 'info',
				};

			SUI.openNotice( 'wp-smush-ajax-notice', noticeMessage, noticeOptions );

			const loadingButton = document.querySelector( '.sui-button-onload' );
			if ( loadingButton ) {
				loadingButton.classList.remove( 'sui-button-onload' );
			}
		},

		/**
		 * Reset settings.
		 *
		 * @since 3.2.0
		 */
		resetSettings: () => {
			const _nonce = document.getElementById( 'wp_smush_reset' );
			const xhr = new XMLHttpRequest();
			xhr.open( 'POST', ajaxurl + '?action=reset_settings', true );
			xhr.setRequestHeader(
				'Content-type',
				'application/x-www-form-urlencoded'
			);
			xhr.onload = () => {
				if ( 200 === xhr.status ) {
					const res = JSON.parse( xhr.response );
					if ( 'undefined' !== typeof res.success && res.success ) {
						window.location.href = wp_smush_msgs.smush_url;
					}
				} else {
					window.console.log(
						'Request failed.  Returned status of ' + xhr.status
					);
				}
			};
			xhr.send( '_ajax_nonce=' + _nonce.value );
		},

		/**
		 * Prepare error row. Will only allow to hide errors for WP media attachments (not nextgen).
		 *
		 * @since 1.9.0
		 * @since 3.12.0 Moved from Smush.
		 *
		 * @param {string} errorMsg   Error message.
		 * @param {string} fileName   File name.
		 * @param {string} thumbnail  Thumbnail for image (if available).
		 * @param {number} id         Image ID.
		 * @param {string} type       Smush type: media or netxgen.
		 * @param {string} errorCode  Error code.
		 *
		 * @return {string}  Row with error.
		 */
		 prepareBulkSmushErrorRow: (errorMsg, fileName, thumbnail, id, type, errorCode) => {
			const thumbDiv =
			thumbnail && 'undefined' !== typeof thumbnail ?
				`<img class="attachment-thumbnail" src="${thumbnail}" />` :
				'<i class="sui-icon-photo-picture" aria-hidden="true"></i>';
			const editLink = window.wp_smush_msgs.edit_link.replace('{{id}}', id);
			fileName =
				'undefined' === fileName || 'undefined' === typeof fileName ?
				'undefined' :
				fileName;

			let tableDiv =
				`<div class="smush-bulk-error-row" data-error-code="${errorCode}">
					<div class="smush-bulk-image-data">
						<div class="smush-bulk-image-title">
							${ thumbDiv }
							<span class="smush-image-name">
								<a href="${editLink}">${fileName}</a>
							</span>
						</div>
					<div class="smush-image-error">
						${errorMsg}
					</div>
				</div>`;

			if ('media' === type) {
				tableDiv +=
					`<div class="smush-bulk-image-actions">
						<a href="javascript:void(0)" class="sui-tooltip sui-tooltip-constrained sui-tooltip-left smush-ignore-image" data-tooltip="${window.wp_smush_msgs.error_ignore}" data-id="${id}">
							${window.wp_smush_msgs.btn_ignore}
						</a>
						<a class="smush-link-detail" href="${editLink}">
							${window.wp_smush_msgs.view_detail}
						</a>
					</div>`;
			}

			tableDiv += '</div>';

			tableDiv += WP_Smush.helpers.upsellWithError( errorCode );

			return tableDiv;
		},
		cacheUpsellErrorCode( errorCode ) {
			this.cacheUpsellErrorCodes.push( errorCode );
		},
		/**
		 * Get upsell base on error code.
		 * @param {string} errorCode Error code.
		 * @returns {string}
		 */
		upsellWithError(errorCode) {
			if (
				!errorCode
				|| !window.wp_smush_msgs['error_' + errorCode]
				|| this.isUpsellRendered( errorCode )
			) {
				return '';
			}
			this.cacheRenderedUpsell( errorCode );
			
			return '<div class="smush-bulk-error-row smush-error-upsell">' +
				'<div class="smush-bulk-image-title">' +
				'<span class="smush-image-error">' +
				window.wp_smush_msgs['error_' + errorCode] +
				'</span>' +
				'</div></div>';
		},
		// Do not use arrow function to use `this`.
		isUpsellRendered( errorCode ) {
			return this.cacheUpsellErrorCodes.includes( errorCode );
		},
		// Do not use arrow function to use `this`.
		cacheRenderedUpsell( errorCode ) {
			this.cacheUpsellErrorCodes.push( errorCode );
		},
		/**
		 * Get error message from Ajax response or Error. 
		 * @param {Object} resp
		 */
		getErrorMessage: ( resp ) => {
			return resp.message || resp.data && resp.data.message ||
				resp.responseJSON && resp.responseJSON.data && resp.responseJSON.data.message ||
				window.wp_smush_msgs.generic_ajax_error ||
				resp.status && 'Request failed. Returned status of ' + resp.status
		},

		/**
		 * Displays a floating message from response,
		 * using the #wp-smush-ajax-notice container.
		 *
		 * @param {Object|string} notice
		 * @param {Object} 		  noticeOptions
		 */
		showNotice: function( notice, noticeOptions ) {
			let message;
			if ( 'object' === typeof notice ) {
				message = this.getErrorMessage( notice );
			} else {
				message = notice;
			}

			if ( ! message ) {
				return;
			}

			noticeOptions = noticeOptions || {};
			noticeOptions = Object.assign({
				showdismiss: false,
				autoclose: true,
			},noticeOptions);
			noticeOptions = {
				type: noticeOptions.type || 'error',
				icon: noticeOptions.icon || ( 'success' === noticeOptions.type ? 'check-tick' : 'info' ),
				dismiss: {
					show: noticeOptions.showdismiss,
					label: window.wp_smush_msgs.noticeDismiss,
					tooltip: window.wp_smush_msgs.noticeDismissTooltip,
				},
				autoclose: {
					show: noticeOptions.autoclose
				}
			};

			const noticeMessage = `<p>${ message }</p>`;

			SUI.openNotice( 'wp-smush-ajax-notice', noticeMessage, noticeOptions );
			return Promise.resolve( '#wp-smush-ajax-notice' );
		},
		closeNotice() {
			window.SUI.closeNotice( 'wp-smush-ajax-notice' );
		},
		renderActivationCDNNotice: function( noticeMessage ) {
			const animatedNotice = document.getElementById('wp-smush-animated-upsell-notice');
			if ( animatedNotice ) {
				return;
			}
			const upsellHtml = `<div class="sui-notice sui-notice-info sui-margin-top" id="wp-smush-animated-upsell-notice">
									<div class="sui-notice-content">
										<div class="sui-notice-message">
											<i class="sui-notice-icon sui-icon-info" aria-hidden="true"></i>
											<p>${noticeMessage}</p>
										</div>
									</div>
								</div>`;
			document.querySelector( '#smush-box-bulk .wp-smush-bulk-wrapper' ).outerHTML += upsellHtml;
		}
	};

	WP_Smush.helpers.init();
}() );
