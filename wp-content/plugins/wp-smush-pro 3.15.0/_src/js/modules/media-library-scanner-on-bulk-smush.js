/* global WP_Smush */

/**
 * Scan Media Library.
 *
 */
import SmushProgress from '../common/progressbar';
import MediaLibraryScanner from '../common/media-library-scanner';
import { GlobalStats } from '../common/globalStats';

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
	if ( ! recheckImagesBtn ) {
		return;
	}
	const bulkSmushButton = $( '.wp-smush-bo-start' ) || $( '.wp-smush-bulk-wrapper .wp-smush-all' );
	const { __ } = wp.i18n;

	class MediaLibraryScannerOnBulkSmush extends MediaLibraryScanner {
		constructor() {
			super();
			this.runBulkSmushOnComplete = false;
			this.restoreButton = $( '.wp-smush-restore' );
			this.autoBulkSmushNotification = $( '.wp-smush-auto-bulk-smush-notification' );
		}

		startScanThenBulkSmushOnComplete() {
			this.runBulkSmushOnComplete = true;
			return this.startScan( true );
		}

		onStart() {
			this.hideRecheckNotice();
			this.disableRelatedButtons();
			this.setRecheckImagesButtonOnLoad();
			this.toggleBulkSmushBoxContent();
			return this;
		}

		onStartFailure( response ) {
			super.onStartFailure( response );
			this.revertRelatedButtons();
		}

		onCloseProgressBar() {
			this.maybeHideAutoBulkSmushNotification();
		}

		disableRelatedButtons() {
			this.restoreButton.setAttribute( 'disabled', true );
			if ( bulkSmushButton ) {
				bulkSmushButton.setAttribute( 'disabled', true );
				this.setInnerText( bulkSmushButton, __( 'Waiting for Re-check to finish', 'wp-smushit' ) );
			}
		}

		revertRelatedButtons() {
			if ( bulkSmushButton ) {
				bulkSmushButton.removeAttribute( 'disabled' );
				this.revertInnerText( bulkSmushButton );
			}
			this.restoreButton.removeAttribute( 'disabled' );
			this.revertRecheckImagesButton();
			return this;
		}

		setRecheckImagesButtonOnLoad() {
			// recheckImagesBtn.classList.add( 'sui-button-onload' );
			this.disableRecheckImagesButton();
			this.setInnerText( recheckImagesBtn.querySelector( '.wp-smush-inner-text' ), __( 'Checking Images', 'wp-smushit' ) );
		}

		disableRecheckImagesButton() {
			recheckImagesBtn.setAttribute( 'disabled', true );
		}

		revertRecheckImagesButton() {
			// recheckImagesBtn.classList.remove( 'sui-button-onload' );
			recheckImagesBtn.removeAttribute( 'disabled' );
			this.revertInnerText( recheckImagesBtn.querySelector( '.wp-smush-inner-text' ) );
		}

		beforeUpdateStatus( stats ) {
			this.runBulkSmushOnComplete = stats?.optimize_on_scan_completed;
			this.maybeShowAutoBulkSmushNotification();
		}

		onDead( stats ) {
			super.onDead( stats );
			this.revertRelatedButtons();
			this.setRequiredScanForBulkSmushButton();
		}

		onFinish( stats ) {
			const globalStats = stats.global_stats;
			super.onFinish( stats );
			this.revertRelatedButtons();
			this.toggleBulkSmushDescription( globalStats );
			if ( globalStats.is_outdated ) {
				this.setRequiredScanForBulkSmushButton();
			} else {
				this.removeScanEventFromBulkSmushButton();
			}

			this.revertRecheckWarning();
		}

		onCompleted( stats ) {
			const requiredReloadPage = ! bulkSmushButton;
			if ( requiredReloadPage ) {
				window.location.reload();
				return;
			}
			this.onFinish( stats );
			const globalStats = stats.global_stats;
			const allImagesSmushed = globalStats.remaining_count < 1;
			if ( allImagesSmushed ) {
				return;
			}

			if ( ! this.runBulkSmushOnComplete ) {
				this.showRecheckNoticeSuccess();
				return;
			}
			this.runBulkSmushOnComplete = false;

			this.triggerBulkSmushEvent( stats );
		}

		showNotice( stats ) {
			if ( ! stats.notice ) {
				return;
			}
			let type = 'success';
			if ( 'undefined' !== typeof stats.noticeType ) {
				type = stats.noticeType;
			}
			window.SUI.openNotice(
				'wp-smush-ajax-notice',
				'<p>' + stats.notice + '</p>',
				{ type, icon: 'check-tick' }
			);
		}

		showRecheckNoticeSuccess() {
			const recheckNotice = $( '.wp-smush-recheck-images-notice-box' );
			if ( ! recheckNotice ) {
				return;
			}
			this.showAnElement( recheckNotice );
			this.hideAnElement( recheckNotice.querySelector( '.wp-smush-recheck-images-notice-warning' ) );
			this.showAnElement( recheckNotice.querySelector( '.wp-smush-recheck-images-notice-success' ) );
		}

		showRecheckNoticeWarning() {
			const recheckNotice = $( '.wp-smush-recheck-images-notice-box' );
			if ( ! recheckNotice ) {
				return;
			}
			this.showAnElement( recheckNotice );
			this.hideAnElement( recheckNotice.querySelector( '.wp-smush-recheck-images-notice-success' ) );
			this.showAnElement( recheckNotice.querySelector( '.wp-smush-recheck-images-notice-warning' ) );
		}

		hideRecheckNotice() {
			this.hideAnElement( $( '.wp-smush-recheck-images-notice-box' ) );
		}

		showProgressErrorNoticeOnRecheckNotice() {
			const recheckWarningElement = $( '.wp-smush-recheck-images-notice-box .wp-smush-recheck-images-notice-warning' );
			if ( ! recheckWarningElement ) {
				return;
			}
			recheckWarningElement.classList.add( 'sui-notice-error' );
			recheckWarningElement.classList.remove( 'sui-notice-warning' );
			this.setInnerText( recheckWarningElement.querySelector( 'span' ), this.getErrorProgressMessage() );
			this.showRecheckNoticeWarning();
		}

		revertRecheckWarning() {
			const recheckWarningElement = $( '.wp-smush-recheck-images-notice-box .wp-smush-recheck-images-notice-warning' );
			if ( ! recheckWarningElement ) {
				return;
			}
			recheckWarningElement.classList.add( 'sui-notice-warning' );
			recheckWarningElement.classList.remove( 'sui-notice-error' );
			this.revertInnerText( recheckWarningElement.querySelector( 'span' ) );
		}

		triggerBulkSmushEvent( stats ) {
			this.disableRecheckImagesButton();

			if ( stats.enabled_background_process ) {
				this.triggerBackgroundBulkSmushEvent( stats.global_stats );
			} else {
				this.triggerAjaxBulkSmushEvent( stats.global_stats );
			}
		}

		toggleBulkSmushDescription( globalStats ) {
			if ( SmushProgress.isEmptyObject ) {
				return;
			}

			if ( globalStats.remaining_count < 1 ) {
				SmushProgress.hideBulkSmushDescription();
				SmushProgress.showBulkSmushAllDone();
			} else {
				SmushProgress.showBulkSmushDescription();
				SmushProgress.hideBulkSmushAllDone();
			}
		}

		setRequiredScanForBulkSmushButton() {
			bulkSmushButton && bulkSmushButton.classList.add( 'wp-smush-scan-and-bulk-smush' );
		}

		removeScanEventFromBulkSmushButton() {
			bulkSmushButton && bulkSmushButton.classList.remove( 'wp-smush-scan-and-bulk-smush' );
		}

		triggerBackgroundBulkSmushEvent( globalStats ) {
			document.dispatchEvent(
				new CustomEvent( 'backgroundBulkSmushOnScanCompleted', {
					detail: globalStats
				} )
			);
		}

		triggerAjaxBulkSmushEvent( globalStats ) {
			document.dispatchEvent(
				new CustomEvent( 'ajaxBulkSmushOnScanCompleted', {
					detail: globalStats
				} )
			);
		}

		onCancelled( stats ) {
			this.onFinish( stats );
			this.runBulkSmushOnComplete = false;
			this.setRequiredScanForBulkSmushButton();
		}

		maybeShowAutoBulkSmushNotification() {
			if (
				! this.runBulkSmushOnComplete
			) {
				return;
			}
			this.showAnElement( this.autoBulkSmushNotification );
		}

		maybeHideAutoBulkSmushNotification() {
			if (
				! this.runBulkSmushOnComplete
			) {
				return;
			}
			this.hideAnElement( this.autoBulkSmushNotification );
		}

		toggleBulkSmushBoxContent() {
			GlobalStats.resetAndHideBulkErrors();
			this.toggleBulkSmushDescription( GlobalStats.getGlobalStats() );
		}
	}
	const mediaLibScanner = new MediaLibraryScannerOnBulkSmush();

	/**
	 * Event Listeners.
	 */

	// Background Scan Media Library.
	const registerScanMediaLibraryEvent = () => {
		if ( ! recheckImagesBtn ) {
			return;
		}

		const canScanInBackground = recheckImagesBtn.classList.contains( 'wp-smush-background-scan' );
		if ( ! canScanInBackground ) {
			return;
		}

		recheckImagesBtn.addEventListener( 'click', () => mediaLibScanner.startScan() );

		//Check scan is running.
		if ( window.wp_smushit_data.media_library_scan?.in_processing ) {
			mediaLibScanner.onStart().showProgressBar().autoSyncStatus();
			return;
		}

		if ( window.location.search.includes( 'smush-action=start-scan-media' ) ) {
			recheckImagesBtn.click();
			const removeScanActionFromURLAddress = () => {
				const cleanedURL = window.location.href.replace( '&smush-action=start-scan-media', '' );
				window.history.pushState( null, null, cleanedURL );
			};
			removeScanActionFromURLAddress();
		}
	};
	registerScanMediaLibraryEvent();

	/**
	 * Recheck Images Notice events.
	 */
	const registerEventsRelatedRecheckImagesNotice = () => {
		const recheckImagesNotice = $( '.wp-smush-recheck-images-notice-box' );
		if ( ! recheckImagesNotice || ! recheckImagesBtn ) {
			return;
		}
		const triggerBackgroundScanImagesLink = recheckImagesNotice.querySelector( '.wp-smush-trigger-background-scan' );
		if ( triggerBackgroundScanImagesLink ) {
			triggerBackgroundScanImagesLink.onclick = ( e ) => {
				e.preventDefault();
				recheckImagesBtn.click();
			};

			if ( window.wp_smushit_data.media_library_scan?.is_dead ) {
				mediaLibScanner.showProgressErrorNoticeOnRecheckNotice();
			} else if( window.wp_smushit_data.is_outdated ) {
				mediaLibScanner.showRecheckNoticeWarning();
			}
		}
		const triggerBulkSmush = recheckImagesNotice.querySelector( '.wp-smush-trigger-bulk-smush' );
		if ( triggerBulkSmush && bulkSmushButton ) {
			triggerBulkSmush.onclick = ( e ) => {
				e.preventDefault();
				recheckImagesNotice.classList.add( 'sui-hidden' );
				bulkSmushButton.click();
			};
		}
		const dismissNotices = recheckImagesNotice.querySelectorAll( 'button.sui-button-icon' );
		if ( dismissNotices ) {
			dismissNotices.forEach( ( dismissNotice ) => {
				dismissNotice.onclick = ( e ) => {
					dismissNotice.closest( '.sui-recheck-images-notice' ).classList.add( 'sui-hidden' );
				};
			} );
		}

		document.addEventListener( 'onSavedSmushSettings', function( e ) {
			if ( ! e?.detail?.is_outdated_stats ) {
				return;
			}

			mediaLibScanner.setRequiredScanForBulkSmushButton();

			recheckImagesNotice.classList.remove( 'sui-hidden' );
			recheckImagesNotice.querySelector( '.wp-smush-recheck-images-notice-success' ).classList.add( 'sui-hidden' );
			recheckImagesNotice.querySelector( '.wp-smush-recheck-images-notice-warning' ).classList.remove( 'sui-hidden' );
		} );
	};

	registerEventsRelatedRecheckImagesNotice();

	// Scan and Bulk Smush.
	const registerScanAndBulkSmushEvent = () => {
		if ( ! bulkSmushButton ) {
			return;
		}

		const handleScanAndBulkSmush = ( e ) => {
			const shouldRunScan = bulkSmushButton.classList.contains( 'wp-smush-scan-and-bulk-smush' );
			if ( ! shouldRunScan ) {
				return;
			}

			e.preventDefault();
			mediaLibScanner.startScanThenBulkSmushOnComplete();
		};

		bulkSmushButton.addEventListener( 'click', handleScanAndBulkSmush );
	};
	registerScanAndBulkSmushEvent();
}() );
