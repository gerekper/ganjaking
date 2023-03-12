/* global WP_Smush */
/* global ajaxurl */

/**
 * Bulk Smush functionality.
 *
 * @since 2.9.0  Moved from admin.js
 */

import Smush from '../smush/smush';
import Fetcher from '../utils/fetcher';

( function( $ ) {
	'use strict';

	WP_Smush.bulk = {
		init: () => {
			/**
			 * Handle the Bulk Smush/Bulk re-Smush button click.
			 */
			$( '.wp-smush-all' ).on( 'click', function( e ) {
				e.preventDefault();

				const bulkRunning = document.getElementById(
					'wp-smush-running-notice'
				);
				bulkRunning.classList.add( 'sui-hidden' );

				// Remove limit exceeded styles.
				const progress = $( '.wp-smush-bulk-progress-bar-wrapper' );
				// TODO: we don't have wp-smush-exceed-limit remove the following line and test
				progress.removeClass( 'wp-smush-exceed-limit' );
				progress
					.find( '.sui-progress-block .wp-smush-all' )
					.addClass( 'sui-hidden' );
				progress
					.find( '.sui-progress-block .wp-smush-cancel-bulk' )
					.removeClass( 'sui-hidden' );
				if ( bulkRunning ) {
					document
						.getElementById( 'bulk-smush-resume-button' )
						.classList.add( 'sui-hidden' );
				}

				// remove smush-limit-reached-notice.
				const limitReachedNotice = document.getElementById( 'smush-limit-reached-notice' );
				if ( limitReachedNotice ) {
					limitReachedNotice.classList.add( 'sui-hidden' );
				}

				// Disable re-Smush and scan button.
				// TODO: refine what is disabled.
				$(
					'.wp-resmush.wp-smush-action, .wp-smush-scan, .wp-smush-all:not(.sui-progress-close), a.wp-smush-lossy-enable, button.wp-smush-resize-enable, button#save-settings-button'
				).prop( 'disabled', true );

				// Check for IDs, if there is none (unsmushed or lossless), don't call Smush function.
				/** @param {Array} wp_smushit_data.unsmushed */
				if (
					'undefined' === typeof window.wp_smushit_data ||
					( 0 === window.wp_smushit_data.unsmushed.length &&
						0 === window.wp_smushit_data.resmush.length )
				) {
					return false;
				}

				$( '.wp-smush-remaining' ).addClass( 'sui-hidden' );

				WP_Smush.bulk.maybeShowCDNUpsellForPreSiteOnStart();

				// Show loader.
				progress
					.find( '.sui-progress-block i.sui-icon-info' )
					.removeClass( 'sui-icon-info' )
					.addClass( 'sui-loading' )
					.addClass( 'sui-icon-loader' );

				new Smush( $( this ), true );
			} );

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
					if ( self.is( 'a' ) && response.success && 'undefined' !== typeof response.data.links ) {
						if ( e.target.closest( '.smush-status-links' ) ) {
							const smushStatus = self.parent().parent().find( '.smush-status' );
							smushStatus.text( wp_smush_msgs.ignored );
							smushStatus.addClass('smush-ignored');
							e.target.closest( '.smush-status-links' ).innerHTML = response.data.links;
						} else if (e.target.closest( '.smush-bulk-error-row' ) ){
							self.addClass('disabled');
							e.target.closest( '.smush-bulk-error-row' ).style.opacity = 0.5;
						}
					}
				} );
			} );

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
