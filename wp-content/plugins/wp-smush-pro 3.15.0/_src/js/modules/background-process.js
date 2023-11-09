/* global WP_Smush */

/**
 * Bulk Smush Background Optimization.
 *
 * @since 3.12.0
 */
import Fetcher from '../utils/fetcher';
import MixPanel from "../mixpanel";
import SmushProgress from '../common/progressbar';
import {GlobalStats, UpsellManger} from "../common/globalStats";

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

        const mixPanel = MixPanel.getInstance();
        const BO = new BackgroundProcess();
        const bulkWrapper = $('.bulk-smush-wrapper');
        const reScanImagesButton = $('.wp-smush-scan');
        let intervalHandle  = 0;
        let cancellationInProgress = false;
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
                GlobalStats.setBoStats( {
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
                            GlobalStats.renderStats();
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
                if (!GlobalStats.getBoStats().in_processing) {
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
                        if ( res.data.errors && ! Object.keys( GlobalStats.getErrors() ).length ) {
                            GlobalStats.setErrors( res.data.errors );
                        }
                        // Render stats.
                        GlobalStats.renderStats();
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
            hideProgressBar() {
                // Hide progress bar.
                SmushProgress.close().update(0, GlobalStats.getBoStats().total_items);
            },
            showProgressBar() {
                // Reset progress bar.
                SmushProgress.update(GlobalStats.getBoStats().processed_items, GlobalStats.getBoStats().total_items);
                // Show progress bar.
                SmushProgress.show();
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
                if ( ! GlobalStats.isChangedStats( newBoStats ) ) {
                    return false;
                }
                // Update BO stats.
                GlobalStats.setBoStats(newBoStats);
                if (updateGlobal) {
                    // Update global stats.
                    GlobalStats.setGlobalStats(global_stats);
                }
                // Update Errors.
                GlobalStats.setErrors( errors );
                return true;
            },
            cancelBulk() {
                // Sync Stats.
                this.syncStats(() => {
                    if (100 === GlobalStats.getGlobalStats().percent_optimized) {
                        // If the last item was getting processed when the user cancelled then the process might have completed
                        GlobalStats.setBoStats( {
                            is_completed: true
                        } );
                        this.onCompletedBulk();
                    } else {
                        // Update status of boStats.
                        GlobalStats.setBoStats( {
                            is_cancelled: true
                        } );
                        // Reset and hide progress bar.
                        this.onFinish();
                        // Bulk is cancelled, show bulk desc.
                        SmushProgress.showBulkSmushDescription();
                    }

                    mixPanel.trackBulkSmushCancel();

                    cancellationInProgress = false;
                });
            },
            showCompletedMessage() {
                // Render completed message.
                // Show completed message.
                const processedWrapper = bulkWrapper.querySelector('.wp-smush-all-done');
                if ( GlobalStats.getBoStats().failed_items ) {
                    let completeMessage = wp_smush_msgs.all_failed;
                    if ( ! this.isFailedAllItems() ) {
                        completeMessage = wp_smush_msgs.error_in_bulk.replace( '{{smushed}}', GlobalStats.getBoStats().total_items - GlobalStats.getBoStats().failed_items )
                            .replace('{{total}}', GlobalStats.getBoStats().total_items )
                            .replace('{{errors}}', GlobalStats.getBoStats().failed_items );
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
                return GlobalStats.getBoStats().failed_items === GlobalStats.getBoStats().total_items;
            },
            getNoticeType() {
                return this.isFailedAllItems() ? 'warning' : 'success';
            },
            onCompletedBulk() {
                // Reset and hide progress bar.
                this.onFinish();
                // Bulk is completed, hide bulk desc.
                SmushProgress.hideBulkSmushDescription();
                // Show completed message.
                this.showCompletedMessage();

                // Reset the progress when we finish so the next smushing starts from zero.
                SmushProgress.update(0, GlobalStats.getBoStats().total_items);
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
                        GlobalStats.renderStats();
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
                        SmushProgress.setNotice( res.data.in_process_notice );
                    }

                    if (res.success) {
                        // Update stats.
                        if ( this.updateStats(res.data, false) ) {
                            // Update progress bar.
                            SmushProgress.update(GlobalStats.getBoStats().processed_items, GlobalStats.getBoStats().total_items);

                            if (! GlobalStats.getBoStats().is_cancelled && ! GlobalStats.getBoStats().is_completed) {
                                // Render stats.
                                GlobalStats.renderStats();
                            }
                        }

                        if (GlobalStats.getBoStats().is_cancelled && !cancellationInProgress) {
                            // Cancelled on server side
                            this.cancelBulk();
                        } else if (GlobalStats.getBoStats().is_completed) {
                            this.completeBulk();
                        }
                    } else {
                        WP_Smush.helpers.showNotice( res );
                    }
                });
            },
            onStart() {
                // Disable btn.
                startBtn.setAttribute('disabled', '');
                // Disable re-check images.
                reScanImagesButton && reScanImagesButton.setAttribute('disabled', '');
                $('.wp-smush-restore').setAttribute('disabled', '');
                // Show upsell cdn.
                UpsellManger.maybeShowCDNUpsellForPreSiteOnStart();
                
				this.setCancelButtonStateToInitial();
            },
            onFinish() {
                // Clear interval.
                if (intervalHandle) {
                    clearInterval(intervalHandle);
                    intervalHandle = 0;
                }

                // Disable btn.
                startBtn.removeAttribute('disabled');
                // Reset and hide Progress Bar.
                this.hideProgressBar();
                // Disable re-check images.
                reScanImagesButton && reScanImagesButton.removeAttribute('disabled', '');
                $('.wp-smush-restore').removeAttribute('disabled', '');

                // Show upsell cdn.
                UpsellManger.maybeShowCDNUpsellForPreSiteOnCompleted();
            },
            init() {
                if (!startBtn) {
                    return;
                }

                // Start BO.
                startBtn.onclick = () => {
                    const requiredScanMedia = startBtn.classList.contains('wp-smush-scan-and-bulk-smush');
                    if ( requiredScanMedia ) {
                        return;
                    }
                    this.start();
                }

                // If BO is running, initial new state.
                this.initState();
            },
			setCancelButtonStateToInitial() {
                SmushProgress.setCancelButtonLabel( wp_smush_msgs.cancel );
                SmushProgress.setOnCancelCallback( this.cancel.bind(this) );
			},
			setCancelButtonStateToStarted() {
                SmushProgress.setCancelButtonLabel( wp_smush_msgs.cancelling );
                SmushProgress.setOnCancelCallback( () => false );
            },
        }
    })();
    // Run.
    BackgroundSmush && BackgroundSmush.init();
    /**
     * For globalStats, we will need to update it on reload and after re-checking images,
     * and on finish the BO.
     * 1. On finish, we handled via BackgroundSmush.syncStats -> BackgroundSmush.updateStats
     * 2. On reload or after re-checking images, we need to update globalStats from global variable:
     * window.wp_smushit_data
     */
    // Update global stats after re-checking images.
    document.addEventListener('wpSmushAfterRecheckImages', function(){
        GlobalStats.updateGlobalStatsFromSmushScriptData();
    });
    
    document.addEventListener('backgroundBulkSmushOnScanCompleted', function(){
		if ( ! BackgroundSmush ) {
			return;
		}
        GlobalStats.setBoStats({
            in_processing: true,
        });
        BackgroundSmush.initState();
    });
})();