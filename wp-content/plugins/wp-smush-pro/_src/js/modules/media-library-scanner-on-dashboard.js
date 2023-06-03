/* global WP_Smush */

/**
 * Scan Media Library.
 *
 */
import MediaLibraryScanner from '../common/media-library-scanner';

( function() {
	'use strict';
	if ( ! window.wp_smush_msgs ) {
		return;
	}
	const $ = document.querySelector.bind( document );
	const existScanProgressBar = $( '.wp-smush-scan-progress-bar-wrapper' );
	if ( ! existScanProgressBar ) {
		return;
	}

	const recheckImagesBtn = $( '.wp-smush-scan' );
	if ( recheckImagesBtn ) {
		return;
	}
	//Check scan is running.
	const is_scan_running = window.wp_smushit_data.media_library_scan?.in_processing;
	if ( ! is_scan_running ) {
		return;
	}

	const { __ } = wp.i18n;

	class mediaLibraryScannerOnDashboard extends MediaLibraryScanner {
		constructor() {
			super();
			this.bulkSmushLink = $( '.wp-smush-bulk-smush-link' );
		}
		onShowProgressBar() {
			this.disableBulkSmushLink();
		}

		onCloseProgressBar() {
			this.revertBulkSmushLink();
		}

		disableBulkSmushLink() {
			if ( ! this.bulkSmushLink ) {
				return;
			}
			this.bulkSmushLink.setAttribute( 'disabled', true );
			this.setInnerText( this.bulkSmushLink, __( 'Waiting for Re-check to finish', 'wp-smushit' ) );
		}

		revertBulkSmushLink() {
			if ( ! this.bulkSmushLink ) {
				return;
			}
			this.bulkSmushLink.removeAttribute( 'disabled' );
			this.revertInnerText( this.bulkSmushLink );
		}
	}

	( new mediaLibraryScannerOnDashboard() ).showProgressBar().autoSyncStatus();
}() );
