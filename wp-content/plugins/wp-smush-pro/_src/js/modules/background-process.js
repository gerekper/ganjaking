/* global WP_Smush */

/**
 * Bulk Smush Background Optimization.
 *
 * @since 3.12.0
 */
import Fetcher from '../utils/fetcher';
import MixPanel from "../mixpanel";

(function () {
    'use strict';
    if (!window.wp_smush_msgs) {
        return;
    }
    const $ = document.querySelector.bind(document);
    const NO_FREE_LIMITED = 50;

    /**
     * Handle Background Process.
     * @returns 
     */
    const BackgroundProcess = () => {
        return {
            handle(action) {
                return Fetcher.background[action]();
            },
            initState() {
                return Fetcher.background.initState();
            },
        }
    }

    /**
     * Background Optimization.
     */
    const BackgroundSmush = (() => {

        const startBtn = window.wp_smushit_data && window.wp_smushit_data.bo_stats && $('.wp-smush-bo-start');
        if (!startBtn) {
            return;
        }
        // Cache initial stats.
        let boStats = window.wp_smushit_data.bo_stats;
        let globalStats = {
            count_images: 0,
            count_total: 0,
            count_resize: 0,
            count_skipped: 0,
            count_smushed: 0,
            savings_bytes: 0,
            savings_resize: 0,
            size_after: 0,
            size_before: 0,
            savings_percent: 0,
            percent_grade: 'sui-grade-dismissed',
            percent_metric: 0,
            percent_optimized: 0,
            remaining_count: 0,
        };

        const mixPanel = new MixPanel();
        const BO = new BackgroundProcess();
        const bulkWrapper = $('.bulk-smush-wrapper');
        const bulkRunning = bulkWrapper.querySelector('#wp-smush-running-notice');
        const progressBar = bulkWrapper.querySelector('.wp-smush-bulk-progress-bar-wrapper');
        const imageScore = $( '#smush-image-score' );
        const summarySmush = $('.sui-summary-smush');
        const logBulk = $('.smush-final-log .smush-bulk-errors');
        const btnCancel = $('.wp-smush-bo-cancel-bulk');
        let intervalHandle  = 0;
        let allErrors = [];
        let cancellationInProgress = false;

        /**
         * For globalStats, we will need to update it on reload and after re-checking images,
         * and on finish the BO.
         * 1. On finish, we handled via BackgroundSmush.syncStats -> BackgroundSmush.updateStats
         * 2. On reload or after re-checking images, we need to update globalStats from global variable:
         * window.wp_smushit_data
         */
        const updateGlobalStats = () => {
            globalStats = Object.keys(globalStats).reduce(function (newStats, key) {
                if (key in window.wp_smushit_data) {
                    if ( 'percent_grade' === key ) {
                        newStats[key] = window.wp_smushit_data[key];
                    } else {
                        newStats[key] = parseInt( window.wp_smushit_data[key] ) || 0;
                    }
                }
                return newStats;
            }, {});
        };
        // Update global stats on reload.
        updateGlobalStats();
        // Update global stats after re-checking images.
        document.addEventListener('wpSmushAfterRecheckImages', function(){
            updateGlobalStats();
        });

        return {
            hookStatusChecks() {
                if (intervalHandle) {
                    // Already done
                    return;
                }

                let count = 0;
				let statusSyncInProgress = false;
                let statSyncInProgress = false;
                intervalHandle = setInterval(() => {
                    if (statusSyncInProgress) {
                        return;
                    }
                    statusSyncInProgress = true;

                    count++;
                    const statusSynced = this.syncBackgroundStatus();
                    if (count % 3 === 0) {
                        // Do a full update every nth time
                        statusSynced.then(() => {
                            if (!statSyncInProgress) {
                                this.syncStats().then(() => {
                                    statSyncInProgress = false;
                                });
                                statSyncInProgress = true;
                            }
                        });
                    }

                    statusSynced.finally(() => {
                        statusSyncInProgress = false;
                    });
                }, 3 * 1000);
            },
            resetBOStatsOnStart() {
                boStats = Object.assign( boStats, {
                    is_cancelled: false,
                    is_completed: false,
                    processed_items: 0,
                    failed_items: 0,
                } );
            },
            start() {
                // Reset boStats.
                this.resetBOStatsOnStart();

                // Disable UI.
                this.onStart();

                // Start BO.
                BO.handle('start').then((res) => {
                    if (res.success) {
                        // Update stats.
                        const updatedStats = this.updateStats(res.data, false);
                        // Show progress bar.
                        this.showProgressBar();
                        this.hookStatusChecks();
                        if ( updatedStats ) {
                            // Render stats.
                            this.renderStats();
                        }
                    } else {
                        WP_Smush.helpers.showNotice( res, {
                            'showdismiss': true,
                            'autoclose' : false,
                        } );
                        this.cancelBulk();
                    }
                });
            },
            /**
             * Initial state when load the Bulk Smush page while BO is running.
             */
             initState() {
                if (!boStats.in_processing) {
                    return;
                }
                // Disable UI.
                this.onStart();
                // Start BO.
                BO.initState().then((res) => {
                    if (res.success) {
                        // Update stats.
                        this.updateStats(res.data, false);
                        // Show progress bar.
                        this.showProgressBar();
                        this.hookStatusChecks();
                        // Maybe update errors.
                        if ( res.data.errors && ! Object.keys( allErrors ).length ) {
                            allErrors = Object.assign( {}, res.data.errors );
                        }
                        // Render stats.
                        this.renderStats();
                    } else {
                        WP_Smush.helpers.showNotice( res );
                    }
                });
            },
            /**
             * Cancel.
             */
            cancel() {
                cancellationInProgress = true;
				this.setCancelButtonStateToStarted();
                BO.handle('cancel').then((res) => {
                    if (res.success) {
                        this.cancelBulk();
                    } else {
                        WP_Smush.helpers.showNotice( res );
                    }
                });
            },
            /**
             * Update progress bar.
             * @param {number} processedItems 
             * @param {number} totalItems 
             */
            updateProgressBar(processedItems, totalItems) {
                const width = Math.floor(processedItems / totalItems * 100) || 0;
                // Progress bar label.
                progressBar.querySelector('.wp-smush-images-percent').innerHTML = width + '%';
                // Progress bar.
                progressBar.querySelector('.wp-smush-progress-inner').style.width = width + '%';

                // Update processed/total.
                const processStateStats = progressBar.querySelector('.sui-progress-state-text');
                processStateStats.firstElementChild.innerHTML = processedItems;
                processStateStats.lastElementChild.innerHTML = totalItems;
            },
            hideProgressBar() {
                // Hide progress bar.
                progressBar.classList.add('sui-hidden');
                // Reset progress bar.
                this.updateProgressBar(0, boStats.total_items);
            },
            showProgressBar() {
                // Reset progress bar.
                this.updateProgressBar(boStats.processed_items, boStats.total_items);
                // Show progress bar.
                progressBar.classList.remove('sui-hidden');
                bulkWrapper.querySelector('.wp-smush-bulk-wrapper').classList.add('sui-hidden');
            },
            /**
             * Update stats.
             * @param {Object} newStats Included increment stats and new BO stats.
             * @param updateGlobal
             */
            updateStats(newStats, updateGlobal) {
                // Make sure we have processed_stats/errors property (not added by default when start).
                newStats.global_stats = newStats.global_stats || {};
                newStats.errors = newStats.errors || {};
                const {
                    global_stats,
                    errors,
                    ...newBoStats
                } = newStats;
                if ( ! this.isChangedStats( newBoStats ) ) {
                    return false;
                }
                // Update BO stats.
                boStats = Object.assign(boStats, newBoStats);
                if (updateGlobal) {
                    // Update global stats.
                    globalStats = Object.assign(globalStats, global_stats);
                }
                // Update Errors.
                allErrors = errors;
                return true;
            },
            isChangedStats( newBoStats ) {
                const primaryKeys = ['total_items', 'processed_items', 'failed_items', 'is_cancelled', 'is_completed'];
                return primaryKeys.some( (key) => {
                    return newBoStats[key] !== boStats[key];
                });
            },
            cancelBulk() {
                // Sync Stats.
                this.syncStats(() => {
                    if (100 === globalStats.percent_optimized) {
                        // If the last item was getting processed when the user cancelled then the process might have completed
                        boStats.is_completed = true;
                        this.onCompletedBulk();
                    } else {
                        // Update status of boStats.
                        boStats.is_cancelled = true;
                        // Reset and hide progress bar.
                        this.onFinish();
                        // Bulk is cancelled, show bulk desc.
                        bulkWrapper.querySelector('.wp-smush-bulk-wrapper').classList.remove('sui-hidden');
                    }

                    mixPanel.trackBulkSmushCancel();

                    cancellationInProgress = false;
                });
            },
            showCompletedMessage() {
                // Render completed message.
                // Show completed message.
                const processedWrapper = bulkWrapper.querySelector('.wp-smush-all-done');
                if ( boStats.failed_items ) {
                    let completeMessage = wp_smush_msgs.all_failed;
                    if ( ! this.isFailedAllItems() ) {
                        completeMessage = wp_smush_msgs.error_in_bulk.replace( '{{smushed}}', boStats.total_items - boStats.failed_items )
                            .replace('{{total}}', boStats.total_items )
                            .replace('{{errors}}', boStats.failed_items );
                    }
                    processedWrapper.querySelector('p').innerHTML = completeMessage;
                    processedWrapper.classList.remove('sui-notice-success', 'sui-notice-warning');
                    const noticeType = this.getNoticeType();
                    const noticeIcon = 'warning' === noticeType ? 'info' : 'check-tick';
                    const iconElement = processedWrapper.querySelector('.sui-notice-icon');
                    processedWrapper.classList.add( 'sui-notice-' + noticeType );
                    iconElement.classList.remove('sui-icon-check-tick', 'sui-icon-info');
                    iconElement.classList.add( 'sui-icon-' + noticeIcon );
                } else {
                    processedWrapper.querySelector('p').innerHTML = wp_smush_msgs.all_smushed;
                }
                processedWrapper.classList.remove('sui-hidden');
            },
            isFailedAllItems() {
                return boStats.failed_items === boStats.total_items;
            },
            getNoticeType() {
                return this.isFailedAllItems() ? 'warning' : 'success';
            },
            onCompletedBulk() {
                // Reset and hide progress bar.
                this.onFinish();
                // Bulk is completed, hide bulk desc.
                bulkWrapper.querySelector('.wp-smush-bulk-wrapper').classList.add('sui-hidden');
                // Show completed message.
                this.showCompletedMessage();
                this.trackBulkSmushCompleted();

                // Reset the progress when we finish so the next smushing starts from zero.
                this.updateProgressBar(0, boStats.total_items);
            },
            getFormattedSavingsBytes: function () {
                return WP_Smush.helpers.formatBytes(globalStats.savings_bytes, 1);
            },
            trackBulkSmushCompleted() {
                mixPanel.trackBulkSmushCompleted(
                    this.getFormattedSavingsBytes(),
                    globalStats.count_images,
                    globalStats.percent_optimized,
                    globalStats.savings_percent
                );
            },
            completeBulk() {
                // Sync Stats.
                this.syncStats(() => this.onCompletedBulk());
            },
            syncStats(onComplete = () => false) {
                return BO.handle('getStats').then((res) => {
                    if ( res.success ) {
                        const responseErrors = res.data.errors || {};
                        this.updateStats( { global_stats: res.data, errors: responseErrors }, true );
                        this.renderStats();
                        if ( res.data.content ) {
                            $('#wp-smush-bulk-content').innerHTML = res.data.content;
                        }
                        onComplete();
                    } else {
                        WP_Smush.helpers.showNotice( res );
                    }
                }).catch( (error) => console.log('error', error));
            },
            syncBackgroundStatus() {
                return BO.handle('getStatus').then((res) => {
                    if ((res.data || {}).in_process_notice) {
                        const progressMessage = progressBar.querySelector('#wp-smush-running-notice .sui-notice-message p');
                        progressMessage.innerHTML = res.data.in_process_notice;
                    }

                    if (res.success) {
                        // Update stats.
                        if ( this.updateStats(res.data, false) ) {
                            // Update progress bar.
                            this.updateProgressBar(boStats.processed_items, boStats.total_items);

                            if (! boStats.is_cancelled && ! boStats.is_completed) {
                                // Render stats.
                                this.renderStats();
                            }
                        }

                        if (boStats.is_cancelled && !cancellationInProgress) {
                            // Cancelled on server side
                            this.cancelBulk();
                        } else if (boStats.is_completed) {
                            this.completeBulk();
                        }
                    } else {
                        WP_Smush.helpers.showNotice( res );
                    }
                });
            },
            onStart() {
                bulkRunning.classList.remove('sui-hidden');
                // Disable btn.
                startBtn.setAttribute('disabled', '');
                // Disable re-check images.
                $('.wp-smush-scan').setAttribute('disabled', '');
                $('.wp-smush-restore').setAttribute('disabled', '');
                // Show upsell cdn.
                this.maybeShowCDNUpsellForPreSiteOnStart();

				this.setCancelButtonStateToInitial();
            },
            onFinish() {
                // Clear interval.
                if (intervalHandle) {
                    clearInterval(intervalHandle);
                    intervalHandle = 0;
                }

                bulkRunning.classList.add('sui-hidden');
                // Disable btn.
                startBtn.removeAttribute('disabled');
                // Reset and hide Progress Bar.
                this.hideProgressBar();
                // Disable re-check images.
                $('.wp-smush-scan').removeAttribute('disabled');
                $('.wp-smush-restore').removeAttribute('disabled', '');

                // Show upsell cdn.
                this.maybeShowCDNUpsellForPreSiteOnCompleted();
            },
            /**
             * Circle progress bar.
             */
            renderScoreProgress() {
                imageScore.className = imageScore.className.replace( /(^|\s)sui-grade-\S+/g, '' );
                imageScore.classList.add( globalStats.percent_grade );
                imageScore.dataset.score = globalStats.percent_optimized;
                imageScore.querySelector( '.sui-circle-score-label' ).innerHTML = globalStats.percent_optimized;

                imageScore
                    .querySelector( 'circle:last-child' )
                    .setAttribute( 'style', '--metric-array:' + ( 2.63893782902 * globalStats.percent_metric ) + ' ' + ( 263.893782902 - globalStats.percent_metric ) );
            },
            /**
             * Summary detail - center meta box.
             */
            renderSummaryDetail() {
                // Total Savings.
                // Use decimal is 1 to the same on php.
		        const formattedSize = this.getFormattedSavingsBytes();
                summarySmush.querySelector('.sui-summary-large.wp-smush-stats-human').innerHTML = Math.round( WP_Smush.helpers.getSizeFromString( formattedSize ) );
                summarySmush.querySelector('.wp-smush-savings .wp-smush-stats-human').innerHTML = WP_Smush.helpers.getFormatFromString( formattedSize );
                // Update the savings percent.
				if (globalStats.size_before) {
					summarySmush.querySelector('.wp-smush-savings .wp-smush-stats-percent').innerHTML = globalStats.savings_percent;
				}
               
                // To total smushed images files.
                summarySmush.querySelector('.wp-smush-count-total .wp-smush-total-optimised').innerHTML = globalStats.count_images;
                // Images Resized.
                if ( globalStats.count_resize ) {
                    summarySmush.querySelector('.wp-smush-count-resize-total').classList.remove('sui-hidden');
                    summarySmush.querySelector('.wp-smush-count-resize-total .wp-smush-total-optimised').innerHTML = globalStats.count_resize;
                }
                // Image Resize Savings.
                summarySmush.querySelector('.smush-resize-savings .wp-smush-stats').innerHTML = WP_Smush.helpers.formatBytes( globalStats.savings_resize, 1 );
            },
            renderBoxSummary() {
                // Circle core progress.
                this.renderScoreProgress();
                // Summary detail.
                this.renderSummaryDetail();
            },
            renderErrors() {
                if ( ! Object.keys( allErrors ).length || ! boStats.is_completed ) {
                    return;
                }
                const errors = [];
                const errorKeys = Object.keys( allErrors );
                // Cache error code to avoid double upsell notice.
                const cacheUpsellErrorCodes = {};
                let showAnimatedUpsell = false;
                errorKeys.map( ( image_id, index ) => {
                        let upsellErrorCode = allErrors[ image_id ].error_code;
                        if ( index < 5 && 'animated' === upsellErrorCode ) {
                            showAnimatedUpsell = true;
                        }
                        if ( ! ( upsellErrorCode in cacheUpsellErrorCodes ) ) {
                            cacheUpsellErrorCodes[ upsellErrorCode ] = upsellErrorCode;
                        } else {
                            upsellErrorCode = false;
                        }
                        errors.push( WP_Smush.helpers.prepareBulkSmushErrorRow(
                                allErrors[ image_id ].error_msg,
                                allErrors[ image_id ].file_link,
                                allErrors[ image_id ].thumbnail ? '<img class="attachment-thumbnail" src='+ allErrors[ image_id ].thumbnail +' />' : 'undefined',
                                image_id,
                                'media',
                                upsellErrorCode,
                        ) );
                    }
                );
                logBulk.innerHTML = errors.join('');
                logBulk.parentElement.classList.remove('sui-hidden');
                logBulk.parentElement.style.display = null;
                // Show view all.
                if ( errorKeys.length > 1 ) {
                    $('.smush-bulk-errors-actions').classList.remove('sui-hidden');
                }
                
                // Show animated upsell CDN if user disabled CDN and found an animated error in first 5 errors.
                if ( showAnimatedUpsell ) {
                    this.maybeShowCDNActivationNotice();
                }
            },
            renderStats() {
                // Render Smush box summary.
                this.renderBoxSummary();
                // Render Errors.
                this.renderErrors();
            },
            init() {
                if (!startBtn) {
                    return;
                }

                // Start BO.
                startBtn.onclick = this.start.bind(this);

                // If BO is running, initial new state.
                this.initState();

				this.setCancelButtonStateToInitial();
            },
			setCancelButtonStateToInitial() {
				btnCancel.textContent = wp_smush_msgs.cancel;
				btnCancel.onclick = this.cancel.bind(this);				
			},
			setCancelButtonStateToStarted() {
				btnCancel.textContent = wp_smush_msgs.cancelling;
				btnCancel.onclick = () => false;
			},
            maybeShowCDNActivationNotice () {
                if ( ! wp_smush_msgs.smush_cdn_activation_notice ) {
                    return;
                }
                WP_Smush.helpers.renderActivationCDNNotice( wp_smush_msgs.smush_cdn_activation_notice );
            },
            maybeShowCDNUpsellForPreSiteOnStart() {
                const upsellCdn = document.querySelector('.wp-smush-upsell-cdn');
                if ( upsellCdn ) {
                    upsellCdn.querySelector('p').innerHTML = wp_smush_msgs.processing_cdn_for_free;
                    upsellCdn.classList.remove('sui-hidden');
                }
            },
            maybeShowCDNUpsellForPreSiteOnCompleted() {
                const upsellCdn = document.querySelector('.wp-smush-upsell-cdn');
                if ( upsellCdn ) {
                    upsellCdn.querySelector('p').innerHTML = wp_smush_msgs.processed_cdn_for_free;
                    upsellCdn.classList.remove('sui-hidden');
                }
            }
        }
    })();
    // Run.
    BackgroundSmush && BackgroundSmush.init();
})();