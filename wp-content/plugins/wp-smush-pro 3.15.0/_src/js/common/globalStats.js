/* global WP_Smush */
export const UpsellManger = ( () => {
	return {
		maybeShowCDNActivationNotice() {
			if ( ! wp_smush_msgs.smush_cdn_activation_notice ) {
				return;
			}
			WP_Smush.helpers.renderActivationCDNNotice( wp_smush_msgs.smush_cdn_activation_notice );
		},
		maybeShowCDNUpsellForPreSiteOnStart() {
			const upsellCdn = document.querySelector( '.wp-smush-upsell-cdn' );
			if ( upsellCdn ) {
				upsellCdn.querySelector( 'p' ).innerHTML = wp_smush_msgs.processing_cdn_for_free;
				upsellCdn.classList.remove( 'sui-hidden' );
			}
		},
		maybeShowCDNUpsellForPreSiteOnCompleted() {
			const upsellCdn = document.querySelector( '.wp-smush-upsell-cdn' );
			if ( upsellCdn ) {
				upsellCdn.querySelector( 'p' ).innerHTML = wp_smush_msgs.processed_cdn_for_free;
				upsellCdn.classList.remove( 'sui-hidden' );
			}
		}
	};
} )();
export const GlobalStats = ( () => {
	const $ = document.querySelector.bind( document );
	const summarySmush = $( '.sui-summary-smush-metabox' );
	if ( ! summarySmush ) {
		return {};
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
		human_bytes: '',
		savings_conversion_human: '',
		savings_conversion: 0,
	};

	const imageScore = $( '#smush-image-score' );
	const logBulk = $( '.smush-final-log .smush-bulk-errors' );
	const bulkSmushCountContent = $( '#wp-smush-bulk-content' );
	let allErrors = {};

	const generateGlobalStatsFromSmushData = ( smushScriptData ) => {
		window.wp_smushit_data = Object.assign( window.wp_smushit_data, smushScriptData || {} );
		globalStats = Object.keys( globalStats ).reduce( function( newStats, key ) {
			if ( key in window.wp_smushit_data ) {
				newStats[ key ] = window.wp_smushit_data[ key ];
			}
			return newStats;
		}, {} );
	}

	generateGlobalStatsFromSmushData( window.wp_smushit_data );

	return {
		isChangedStats( newBoStats ) {
			const primaryKeys = [ 'total_items', 'processed_items', 'failed_items', 'is_cancelled', 'is_completed' ];
			return primaryKeys.some( ( key ) => {
				return newBoStats[ key ] !== boStats[ key ];
			} );
		},
		setBoStats( newBoStats ) {
			boStats = Object.assign( boStats, newBoStats || {} );
			return this;
		},
		getBoStats() {
			return boStats;
		},
		setGlobalStats( newGlobalStats ) {
			globalStats = Object.assign( globalStats, newGlobalStats || {} );
			return this;
		},
		getGlobalStats() {
			return globalStats;
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
			this.renderTotalStats();
			this.renderResizedStats();
			this.renderConversionSavings();
		},
		renderTotalStats() {
			// Total savings.
			summarySmush.querySelector( '.sui-summary-large.wp-smush-stats-human' ).innerHTML = globalStats.human_bytes;
			// Update the savings percent.
			summarySmush.querySelector( '.wp-smush-savings .wp-smush-stats-percent' ).innerHTML = globalStats.savings_percent;
			// To total smushed images files.
			summarySmush.querySelector( '.wp-smush-count-total .wp-smush-total-optimised' ).innerHTML = globalStats.count_images;
		},
		renderResizedStats() {
			const resizeCountElement = summarySmush.querySelector( '.wp-smush-count-resize-total' );
			if ( ! resizeCountElement ) {
				return;
			}
			if ( globalStats.count_resize > 0 ) {
				resizeCountElement.classList.remove( 'sui-hidden' );
			} else {
				resizeCountElement.classList.add( 'sui-hidden' );
			}
			resizeCountElement.querySelector( '.wp-smush-total-optimised' ).innerHTML = globalStats.count_resize;
		},
		renderConversionSavings() {
			// PNG2JPG Savings.
			const conversionSavingsElement = summarySmush.querySelector( '.smush-conversion-savings .wp-smush-stats' );
			if ( ! conversionSavingsElement ) {
				return;
			}
			conversionSavingsElement.innerHTML = globalStats.savings_conversion_human;
			if ( globalStats.savings_conversion > 0 ) {
				conversionSavingsElement.parentElement.classList.remove( 'sui-hidden' );
			} else {
				conversionSavingsElement.parentElement.classList.add( 'sui-hidden' );
			}
		},
		renderBoxSummary() {
			// Circle core progress.
			this.renderScoreProgress();
			// Summary detail.
			this.renderSummaryDetail();
		},
		setErrors( newErrors ) {
			allErrors = newErrors || {};
		},
		getErrors() {
			return allErrors;
		},
		renderErrors() {
			if ( ! Object.keys( allErrors ).length || ! boStats.is_completed ) {
				return;
			}
			const errors = [];
			const errorKeys = Object.keys( allErrors );
			// Cache error code to avoid double upsell notice.
			let showAnimatedUpsell = false;
			errorKeys.map( ( image_id, index ) => {
				const upsellErrorCode = allErrors[ image_id ].error_code;
				if ( index < 5 && 'animated' === upsellErrorCode ) {
					showAnimatedUpsell = true;
				}
				errors.push( WP_Smush.helpers.prepareBulkSmushErrorRow(
					allErrors[ image_id ].error_message,
					allErrors[ image_id ].file_name,
					allErrors[ image_id ].thumbnail,
					image_id,
					'media',
					allErrors[ image_id ].error_code,
				) );
			}
			);
			logBulk.innerHTML = errors.join( '' );
			logBulk.parentElement.classList.remove( 'sui-hidden' );
			logBulk.parentElement.style.display = null;
			// Show view all.
			if ( errorKeys.length > 1 ) {
				$( '.smush-bulk-errors-actions' ).classList.remove( 'sui-hidden' );
			}

			// Show animated upsell CDN if user disabled CDN and found an animated error in first 5 errors.
			if ( showAnimatedUpsell ) {
				UpsellManger.maybeShowCDNActivationNotice();
			}
		},
		resetAndHideBulkErrors() {
			if ( ! logBulk ) {
				return;
			}
			this.resetErrors();
			logBulk.parentElement.classList.add( 'sui-hidden' );
			logBulk.innerHTML = '';
		},
		resetErrors() {
			allErrors = {};
		},
		renderStats() {
			// Render Smush box summary.
			this.renderBoxSummary();
			// Render Errors.
			this.renderErrors();
		},
		maybeUpdateBulkSmushCountContent( newContent ) {
			if ( newContent && bulkSmushCountContent ) {
				bulkSmushCountContent.innerHTML = newContent;
			}
		},
		updateGlobalStatsFromSmushScriptData( smushScriptData ) {
			this.maybeUpdateBulkSmushCountContent( smushScriptData?.content );
			generateGlobalStatsFromSmushData( smushScriptData );
			return this;
		},
	};
} )();
