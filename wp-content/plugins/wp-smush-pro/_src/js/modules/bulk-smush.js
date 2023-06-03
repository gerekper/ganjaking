/* global WP_Smush */
/* global ajaxurl */

/**
 * Bulk Smush functionality.
 *
 * @since 2.9.0  Moved from admin.js
 */

import Smush from '../smush/smush';
import Fetcher from '../utils/fetcher';
import SmushProcess from '../common/progressbar';

( function( $ ) {
	'use strict';

	WP_Smush.bulk = {
		init() {
			this.onClickBulkSmushNow();
			this.onClickIgnoreImage();
			this.onClickIgnoreAllImages();
			this.onScanCompleted();
		},
		onClickBulkSmushNow() {
			/**
			 * Handle the Bulk Smush/Bulk re-Smush button click.
			 */
			$( '.wp-smush-all' ).on( 'click', function( e ) {
				const bulkSmushButton = $(this);
				if ( bulkSmushButton.hasClass('wp-smush-scan-and-bulk-smush') ) {
					return;
				}
				e.preventDefault();

				WP_Smush.bulk.ajaxBulkSmushStart( bulkSmushButton );
			} );
		},
		ajaxBulkSmushStart( bulkSmushButton ) {
			bulkSmushButton = bulkSmushButton || $( '#wp-smush-bulk-content .wp-smush-all' );
			// Check for IDs, if there is none (unsmushed or lossless), don't call Smush function.
			/** @param {Array} wp_smushit_data.unsmushed */
			if (
				'undefined' === typeof window.wp_smushit_data ||
				( 0 === window.wp_smushit_data.unsmushed.length &&
					0 === window.wp_smushit_data.resmush.length )
			) {
				return false;
			}
			// Disable re-Smush and scan button.
			// TODO: refine what is disabled.
			$(
				'.wp-resmush.wp-smush-action, .wp-smush-scan, .wp-smush-all:not(.sui-progress-close), a.wp-smush-lossy-enable, button.wp-smush-resize-enable, button#save-settings-button'
			).prop( 'disabled', true );

			if ( bulkSmushButton.hasClass('wp-smush-resume-bulk-smush') && this.bulkSmush ) {
				this.resumeBulkSmush();
				return;
			}
			
			this.bulkSmush = new Smush( bulkSmushButton, true );
			SmushProcess.setOnCancelCallback( () => {
				this.bulkSmush.cancelAjax()
			}).update( 0, this.bulkSmush.ids.length ).show();

			// Show upsell cdn.
			this.maybeShowCDNUpsellForPreSiteOnStart();

			// Run bulk Smush.
			this.bulkSmush.run();
		},
		resumeBulkSmush() {
			SmushProcess.disableExceedLimitMode();
			SmushProcess.hideBulkSmushDescription();
			this.bulkSmush.onStart();
			this.bulkSmush.callAjax();
		},
		onClickIgnoreImage() {
			/**
			 * Ignore file from bulk Smush.
			 *
			 * @since 2.9.0
			 */
			$( 'body' ).on( 'click', '.smush-ignore-image', function( e ) {
				e.preventDefault();

				const self = $( this );

				self.prop( 'disabled', true );
				self.attr( 'data-tooltip' );
				self.removeClass( 'sui-tooltip' );
				$.post( ajaxurl, {
					action: 'ignore_bulk_image',
					id: self.attr( 'data-id' ),
					_ajax_nonce: wp_smush_msgs.nonce,
				} ).done( ( response ) => {
					if ( self.is( 'a' ) && response.success && 'undefined' !== typeof response.data.html ) {
						if( self.closest('.smush-status-links') ) {
							self.closest('.smush-status-links').parent().html( response.data.html );
						} else if (e.target.closest( '.smush-bulk-error-row' ) ){
							self.addClass('disabled');
							e.target.closest( '.smush-bulk-error-row' ).style.opacity = 0.5;
						}
					}
				} );
			} );
		},
		onClickIgnoreAllImages() {
			/**
			 * Ignore file from bulk Smush.
			 *
			 * @since 3.12.0
			 */
			const ignoreAll = document.querySelector('.wp_smush_ignore_all_failed_items');
			if ( ignoreAll ) {
				ignoreAll.onclick = (e) => {
					e.preventDefault();
					e.target.setAttribute('disabled','');
					e.target.style.cursor = 'progress';
					const type = e.target.dataset.type || null;
					e.target.classList.remove('sui-tooltip');
					Fetcher.smush.ignoreAll(type).then((res) => {
						if ( res.success ) {
							window.location.reload();
						} else {
							e.target.style.cursor = 'pointer';
							e.target.removeAttribute('disabled');
							WP_Smush.helpers.showNotice( res );
						}
					});
				}
			}
		},
		onScanCompleted() {
			document.addEventListener('ajaxBulkSmushOnScanCompleted', (e) => {
				this.ajaxBulkSmushStart();
			});
		},
		maybeShowCDNUpsellForPreSiteOnStart: () => {
			// Show upsell cdn.
			const upsell_cdn = document.querySelector('.wp-smush-upsell-cdn');
			if ( upsell_cdn ) {
				upsell_cdn.classList.remove('sui-hidden');
			}
		}
		
	};

	WP_Smush.bulk.init();
} )( jQuery );
