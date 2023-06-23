/* global WP_Smush */

/**
 * Abstract Media Library Scanner.
 *
 */
import Fetcher from '../utils/fetcher';
import { scanProgressBar } from './progressbar';
import { GlobalStats } from './globalStats';
const { __ } = wp.i18n;
export default class MediaLibraryScanner {
	constructor() {
		this.autoSyncDuration = 1500;
		this.progressTimeoutId = 0;
		this.scanProgress = scanProgressBar( this.autoSyncDuration );
	}

	startScan( optimizeOnScanCompleted = false ) {
		this.onStart();
		return Fetcher.scanMediaLibrary.start( optimizeOnScanCompleted ).then( ( response ) => {
			if ( ! response?.success ) {
				this.onStartFailure( response );
				return;
			}
			this.showProgressBar().autoSyncStatus();
		} );
	}

	onStart() {
		// Do nothing at the moment.
	}

	onStartFailure( response ) {
		WP_Smush.helpers.showNotice( response, {
			showdismiss: true,
			autoclose: false,
		} );
	}

	showProgressBar() {
		this.onShowProgressBar();
		this.scanProgress.reset().setOnCancelCallback( this.showStopScanningModal.bind( this ) ).open();
		return this;
	}

	onShowProgressBar() {
		// Do nothing at the moment.
	}

	showStopScanningModal() {
		if ( ! window.SUI ) {
			return;
		}

		this.onShowStopScanningModal();

		window.SUI.openModal(
			'smush-stop-scanning-dialog',
			'wpbody-content',
			undefined,
			false
		);
	}

	onShowStopScanningModal() {
		this.registerCancelProcessEvent();
	}

	registerCancelProcessEvent() {
		const stopScanButton = document.querySelector( '.smush-stop-scanning-dialog-button' );
		if ( ! stopScanButton ) {
			return;
		}

		stopScanButton.addEventListener( 'click', this.cancelProgress.bind( this ) );
	}

	closeStopScanningModal() {
		if ( ! window.SUI ) {
			return;
		}
		const stopScanningElement = document.querySelector( '#smush-stop-scanning-dialog' );
		const isModalClosed = ( ! stopScanningElement ) || ! stopScanningElement.classList.contains( 'sui-content-fade-in' );
		if ( isModalClosed ) {
			return;
		}
		window.SUI.closeModal( 'smush-stop-scanning-dialog' );
	}

	closeProgressBar() {
		this.onCloseProgressBar();
		this.scanProgress.close();
	}

	onCloseProgressBar() {
		// Do nothing at the moment.
	}

	updateProgress( stats ) {
		const totalItems = this.getTotalItems( stats );
		const processedItems = this.getProcessedItems( stats );

		return this.scanProgress.update( processedItems, totalItems );
	}

	getProcessedItems( stats ) {
		return stats?.processed_items || 0;
	}

	getTotalItems( stats ) {
		return stats?.total_items || 0;
	}

	cancelProgress() {
		this.scanProgress.setCancelButtonOnCancelling();
		return Fetcher.scanMediaLibrary.cancel().then( ( response ) => {
			if ( ! response?.success ) {
				this.onCancelFailure( response );
				return;
			}
			this.onCancelled( response.data );
		} );
	}

	onCancelFailure( response ) {
		WP_Smush.helpers.showNotice( response, {
			showdismiss: true,
			autoclose: false,
		} );
		this.scanProgress.resetCancelButtonOnFailure();
	}

	getErrorProgressMessage() {
		return __( 'Unfortunately the scan hit an error due to limited resources on your site, we have adjusted the scan to use fewer resources the next time.', 'wp-smushit' );
	}

	onDead( stats ) {
		this.clearProgressTimeout();
		this.closeProgressBar();
		this.closeStopScanningModal();
		this.showRetryScanModal();
	}

	showRetryScanModal() {
		const retryScanModalElement = document.getElementById( 'smush-retry-scan-notice' );
		if ( ! window.SUI || ! retryScanModalElement ) {
			return;
		}

		retryScanModalElement.querySelector('.smush-retry-scan-notice-button').onclick = (e) => {
			window.SUI.closeModal( 'smush-retry-scan-notice' );
			const recheckImagesBtn = document.querySelector( '.wp-smush-scan' );
			if ( ! recheckImagesBtn ) {
				return;
			}

			e.preventDefault();
			recheckImagesBtn.click();
		}

		window.SUI.openModal(
			'smush-retry-scan-notice',
			'wpbody-content',
			undefined,
			false
		);
	}

	onCompleted( stats ) {
		this.onFinish( stats );
	}

	onCancelled( stats ) {
		this.onFinish( stats );
	}

	onFinish( stats ) {
		this.clearProgressTimeout();
		const globalStats = stats?.global_stats;
		this.updateGlobalStatsAndBulkContent( globalStats );
		this.closeProgressBar();
		this.closeStopScanningModal();
	}

	clearProgressTimeout() {
		if ( this.progressTimeoutId ) {
			clearTimeout( this.progressTimeoutId );
		}
	}

	updateGlobalStatsAndBulkContent( globalStats ) {
		if ( ! globalStats ) {
			return;
		}
		GlobalStats.updateGlobalStatsFromSmushScriptData( globalStats );
		GlobalStats.renderStats();
	}

	getStatus() {
		return Fetcher.scanMediaLibrary.getScanStatus();
	}

	autoSyncStatus() {
		const startTime = new Date().getTime();
		this.getStatus().then( ( response ) => {
			if ( ! response?.success ) {
				return;
			}
			const stats = response.data;

			if ( stats.is_dead ) {
				this.onDead( response.data );
				return;
			}

			this.beforeUpdateStatus( stats );

			this.updateProgress( stats ).then( () => {
				this.scanProgress.increaseDurationToHaveChangeOnProgress( new Date().getTime() - startTime );

				const isCompleted = stats?.is_completed;
				if ( isCompleted ) {
					this.onCompleted( stats );
					return;
				}
				const isCancelled = stats?.is_cancelled;
				if ( isCancelled ) {
					this.onCancelled( stats );
					return;
				}

				this.progressTimeoutId = setTimeout( () => this.autoSyncStatus(), this.autoSyncDuration );
			} );
		} );
	}

	beforeUpdateStatus() {
		// Do nothing at the moment.
	}

	setInnerText( element, newText ) {
		if ( ! element ) {
			return;
		}
		element.dataset.originalText = element.dataset.originalText || element.innerText.trim();
		element.innerText = newText;
	}

	revertInnerText( element ) {
		if ( ! element || ! element.dataset.originalText ) {
			return;
		}
		element.innerText = element.dataset.originalText.trim();
	}

	hideAnElement( element ) {
		if ( element ) {
			element.classList.add( 'sui-hidden' );
		}
	}

	showAnElement( element ) {
		if ( element ) {
			element.classList.remove( 'sui-hidden' );
		}
	}
}
