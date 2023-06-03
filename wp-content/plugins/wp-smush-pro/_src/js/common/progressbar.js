/* global WP_Smush */

/**
 * SmushProgressBar
 * TODO: Update progressbar for free version.
 *
 * @param  autoSyncDuration
 */
export const scanProgressBar = ( autoSyncDuration ) => {
	const { __, _n } = wp.i18n;
	const scanProgressBar = document.querySelector( '.wp-smush-scan-progress-bar-wrapper' );
	const percentElement = scanProgressBar.querySelector( '.wp-smush-progress-percent' );
	const progressElement = scanProgressBar.querySelector( '.wp-smush-progress-inner' );
	const remainingTimeElement = scanProgressBar.querySelector( '.wp-smush-remaining-time' );
	const cancelBtn = scanProgressBar.querySelector( '.wp-smush-cancel-scan-progress-btn' );
	const holdOnNoticeElement = scanProgressBar.querySelector( '.wp-smush-scan-hold-on-notice' );
	let onCancelCallback = () => {};
	let intervalProgressAnimation = 0;
	// It should be smaller than autoSyncDuration.
	const progressTransitionDuration = autoSyncDuration - 300;//1200
	scanProgressBar.style.setProperty( '--progress-transition-duration', progressTransitionDuration / 1000 + 's' );

	let prevProcessedItems = window.wp_smushit_data?.media_library_scan?.processed_items || 0;
	const cacheProcessTimePerItem = [];
	let durationToHaveChangeOnProgress = autoSyncDuration;
	let timeLimitToShowNotice = autoSyncDuration * 10;// 15s.
	return {
		update( processedItems, totalItems ) {
			this.updateRemainingTime( processedItems, totalItems );

			let width = ( totalItems && Math.floor( processedItems / totalItems * 100 ) ) || 0;
			width = Math.min( width, 100 );

			let currentWidth = progressElement.style.width;
			currentWidth = ( currentWidth && currentWidth.replace( '%', '' ) ) || 0;
			progressElement.style.width = width + '%';

			return this.animateProgressBar( currentWidth, width );
		},
		animateProgressBar( currentWidth, width ) {
			if ( intervalProgressAnimation ) {
				clearInterval( intervalProgressAnimation );
			}
			return new Promise( ( resolve ) => {
				const delayTime = progressTransitionDuration / ( width - currentWidth );
				intervalProgressAnimation = setInterval( () => {
					// Progress bar label.
					percentElement.innerHTML = currentWidth + '%';
					currentWidth++;
					if ( currentWidth > width ) {
						resolve();
						clearInterval( intervalProgressAnimation );
					}
				}, delayTime );
			} );
		},
		updateRemainingTime( processedItems, totalItems ) {
			if ( ! remainingTimeElement ) {
				return;
			}
			const processTimePerItem = this.calcProcessTimePerItem( processedItems ) || 500;
			const remainingTime = processTimePerItem * ( totalItems - processedItems );
			remainingTimeElement.innerText = this.formatTime( remainingTime );
		},
		calcProcessTimePerItem( processedItems ) {
			if ( ! processedItems ) {
				return;
			}
			prevProcessedItems = prevProcessedItems <= processedItems ? prevProcessedItems : 0;
			if ( prevProcessedItems != processedItems ) {
				const processTimePerItem = Math.floor( durationToHaveChangeOnProgress / ( processedItems - prevProcessedItems ) );

				prevProcessedItems = processedItems;
				cacheProcessTimePerItem.push( processTimePerItem );
				this.resetDurationToHaveChangeOnProgress();
			} else {
				this.increaseDurationToHaveChangeOnProgress( autoSyncDuration );
			}

			if ( ! cacheProcessTimePerItem.length ) {
				return;
			}

			return cacheProcessTimePerItem.reduce(
				( accumulator, processTime ) => accumulator + processTime, 0
			) / cacheProcessTimePerItem.length;
		},
		increaseDurationToHaveChangeOnProgress( increaseTime ) {
			durationToHaveChangeOnProgress += increaseTime;
			if ( durationToHaveChangeOnProgress > timeLimitToShowNotice ) {
				this.showHoldOnNotice();
			}
		},
		showHoldOnNotice() {
			holdOnNoticeElement.classList.remove( 'sui-hidden' );
			timeLimitToShowNotice = 100000000;
		},
		resetHoldOnNoticeVisibility() {
			holdOnNoticeElement.classList.add( 'sui-hidden' );
		},
		resetDurationToHaveChangeOnProgress() {
			durationToHaveChangeOnProgress = autoSyncDuration;
		},
		formatTime( totalMilliSeconds ) {
			const totalSeconds = Math.floor( ( totalMilliSeconds + progressTransitionDuration ) / 1000 );
			const seconds = totalSeconds % 60;
			const minutes = Math.floor( totalSeconds / 60 );

			let timeString = '';
			if ( minutes ) {
				timeString += minutes + ' ' + _n( 'minute', 'minutes', minutes, 'wp-smushit' );
			}

			timeString += ' ' + seconds + ' ' + _n( 'second', 'seconds', seconds, 'wp-smushit' );

			return timeString.trim();
		},
		reset() {
			progressElement.style.width = '0%';
			percentElement.innerHTML = '0%';

			this.resetCancelButton();
			this.resetHoldOnNoticeVisibility();
			return this;
		},
		open() {
			cancelBtn.onclick = onCancelCallback;
			scanProgressBar.classList.remove( 'sui-hidden' );
		},
		close() {
			scanProgressBar.classList.add( 'sui-hidden' );
			this.reset();
		},
		setOnCancelCallback( callBack ) {
			if ( 'function' !== typeof callBack ) {
				return;
			}
			onCancelCallback = callBack;
			return this;
		},
		setCancelButtonLabel( textContent ) {
			cancelBtn.textContent = textContent;
			return this;
		},
		setCancelButtonOnCancelling() {
			this.setCancelButtonLabel( wp_smush_msgs.cancelling );
			this.setOnCancelCallback( () => false );
			cancelBtn.setAttribute( 'disabled', true );
		},
		resetCancelButton() {
			this.setOnCancelCallback( () => {} );
			this.resetCancelButtonLabel();
			cancelBtn.removeAttribute( 'disabled' );
		},
		resetCancelButtonLabel() {
			this.setCancelButtonLabel( __( 'Cancel Scan', 'wp-smushit' ) );
		},
		resetCancelButtonOnFailure() {
			this.resetCancelButtonLabel();
			cancelBtn.removeAttribute( 'disabled' );
		}
	};
};

const SmushProgressBar = () => {
	'use strict';
	const progressBar = document.querySelector( '.wp-smush-bulk-progress-bar-wrapper' );
	if ( ! progressBar ) {
		return {
			isEmptyObject: true,
		};
	}
	const cancelBtn = progressBar.querySelector( '.wp-smush-cancel-btn' );
	const bulkSmushDescription = document.querySelector( '.wp-smush-bulk-wrapper' );
	const bulkRunningNotice = progressBar.querySelector( '#wp-smush-running-notice' );
	const bulkSmushAllDone = document.querySelector( '.wp-smush-all-done' );
	let isStateHidden = false;
	let onCancelCallback = () => {};

	return {
		/**
		 * Update progress bar.
		 *
		 * @param {number} processedItems
		 * @param {number} totalItems
		 */
		update( processedItems, totalItems ) {
			let width = totalItems && Math.floor( processedItems / totalItems * 100 ) || 0;
			width = Math.min( width, 100 );

			// Progress bar label.
			progressBar.querySelector( '.wp-smush-images-percent' ).innerHTML = width + '%';
			// Progress bar.
			progressBar.querySelector( '.wp-smush-progress-inner' ).style.width = width + '%';

			// Update processed/total.
			const processStateStats = progressBar.querySelector( '.sui-progress-state-text' );
			processStateStats.firstElementChild.innerHTML = processedItems;
			processStateStats.lastElementChild.innerHTML = totalItems;

			return this;
		},
		close() {
			progressBar.classList.add( 'sui-hidden' );
			this.setCancelButtonLabel( window.wp_smush_msgs.cancel )
				.setOnCancelCallback( () => {} )
				.update( 0, 0 );
			this.resetOriginalNotice();
			return this;
		},
		show() {
			// Show progress bar.
			cancelBtn.onclick = onCancelCallback;
			progressBar.classList.remove( 'sui-hidden' );
			this.hideBulkSmushDescription();
			this.hideBulkSmushAllDone();
			this.hideRecheckImagesNotice();
		},
		setCancelButtonLabel( textContent ) {
			cancelBtn.textContent = textContent;
			return this;
		},
		showBulkSmushDescription() {
			bulkSmushDescription.classList.remove( 'sui-hidden' );
		},
		hideBulkSmushDescription() {
			bulkSmushDescription.classList.add( 'sui-hidden' );
		},
		showBulkSmushAllDone() {
			bulkSmushAllDone.classList.remove( 'sui-hidden' );
		},
		hideBulkSmushAllDone() {
			bulkSmushAllDone.classList.add( 'sui-hidden' );
		},
		hideState() {
			if ( isStateHidden ) {
				return this;
			}
			isStateHidden = true;
			progressBar.querySelector( '.sui-progress-state' ).classList.add( 'sui-hidden' );
			return this;
		},
		showState() {
			if ( ! isStateHidden ) {
				return this;
			}
			isStateHidden = false;
			progressBar.querySelector( '.sui-progress-state' ).classList.remove( 'sui-hidden' );
			return this;
		},
		setNotice( inProcessNotice ) {
			const progressMessage = bulkRunningNotice.querySelector( '.sui-notice-message p' );
			this.cacheOriginalNotice( progressMessage );
			progressMessage.innerHTML = inProcessNotice;
			return this;
		},
		cacheOriginalNotice( progressMessage ) {
			if ( bulkRunningNotice.dataset.progressMessage ) {
				return;
			}
			bulkRunningNotice.dataset.progressMessage = progressMessage.innerHTML;
		},
		resetOriginalNotice() {
			if ( ! bulkRunningNotice.dataset.progressMessage ) {
				return;
			}
			const progressMessage = bulkRunningNotice.querySelector( '.sui-notice-message p' );
			progressMessage.innerHTML = bulkRunningNotice.dataset.progressMessage;
		},
		hideBulkProcessingNotice() {
			bulkRunningNotice.classList.add( 'sui-hidden' );
			return this;
		},
		showBulkProcessingNotice() {
			bulkRunningNotice.classList.remove( 'sui-hidden' );
			return this;
		},
		setCountUnitText( unitText ) {
			const progressUnit = progressBar.querySelector( '.sui-progress-state-unit' );
			progressUnit.innerHTML = unitText;
		},
		setOnCancelCallback( callBack ) {
			if ( 'function' !== typeof callBack ) {
				return;
			}
			onCancelCallback = callBack;
			return this;
		},
		disableExceedLimitMode() {
			progressBar.classList.remove( 'wp-smush-exceed-limit' );
			progressBar.querySelector( '#bulk-smush-resume-button' ).classList.add( 'sui-hidden' );
		},
		hideRecheckImagesNotice() {
			const recheckImagesNoticeElement = document.querySelector( '.wp-smush-recheck-images-notice-box' );
			if ( recheckImagesNoticeElement ) {
				recheckImagesNoticeElement.classList.add( 'sui-hidden' );
			}
		}

	};
};
export default new SmushProgressBar();
