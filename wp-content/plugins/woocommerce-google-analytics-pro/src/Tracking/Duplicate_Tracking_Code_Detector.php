<?php
/**
 * WooCommerce Google Analytics Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Google Analytics Pro to newer
 * versions in the future. If you wish to customize WooCommerce Google Analytics Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-google-analytics-pro/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Admin\AJAX;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integration;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking;

defined( 'ABSPATH' ) or exit;

/**
 * Duplicate frontend tracking code detector.
 *
 * @since 2.0.0
 */
class Duplicate_Tracking_Code_Detector {


	/** @var string the transient name */
	protected string $transient_name;


	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->transient_name = 'wc_' . wc_google_analytics_pro()->get_id() . '_site_has_duplicate_tracking_codes';

		add_action( 'wc_google_analytics_pro_after_tracking_code_setup', [ $this, 'print_js' ] );

		add_action( 'admin_notices', [ $this, 'add_admin_notice' ] );

		add_action( 'deactivated_plugin', [ $this, 'clear_duplicate_tracking_code_results' ] );
	}


	/**
	 * Prints the JS for detecting duplicate tracking codes.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @see AJAX::report_duplicate_tracking_code_results()
	 *
	 * @param string $ga_function_name
	 * @return void
	 */
	public function print_js( string $ga_function_name ): void {

		if ( ! $this->should_check_for_duplicate_tracking_codes() ) {
			return;
		}

		$tracking_id = esc_js( Tracking::get_tracking_id() );
		$plugin_id   = esc_js( wc_google_analytics_pro()->get_id() );
		$nonce       = esc_js( wp_create_nonce( 'report-duplicate-tracking-code-results' ) );

		echo <<<JS
		/**
		 * Integrate with Google Analytics trackers to find out whether the configured web
		 * property is being tracked multiple times.
		 *
		 * @since 1.8.6
		 */
		window.wc_ga_pro.findDuplicateTrackingCodes = function() {

			var originalSendHitTasks = {},
				pageviewHitCount     = 0,
				reportResultsTimeout = null;

			// return early if jQuery is not available
			if ( 'undefined' === typeof jQuery ) {
				return;
			}

			/**
			 * Update all modified trackers to use their original sendHitTask functions.
			 *
			 * @since 1.8.6
			 */
			function restoreOriginalSendHitTasks() {

				var tracker, trackerName;

				for ( trackerName in originalSendHitTasks ) {

					tracker = {$ga_function_name}.getByName( trackerName );

					if ( tracker ) {
						tracker.set( 'sendHitTask', originalSendHitTasks[ trackerName ] );
					}
				}
			}


			/**
			 * Send an AJAX request to indicate whether we found duplicate tracking codes or not.
			 *
			 * @since 1.8.6
			 */
			function reportResults( hasDuplicateTrackingCodes ) {

				clearTimeout( reportResultsTimeout );

				jQuery.post(
					window.wc_ga_pro.ajax_url,
					{
						action: 'wc_{$plugin_id}_report_duplicate_tracking_code_results',
						nonce: '{$nonce}',
						has_duplicate_tracking_codes: hasDuplicateTrackingCodes ? 1 : 0,
					}
				);
			}

			// update all trackers created so far to sniff every hit looking for duplicates
			jQuery.each( {$ga_function_name}.getAll(), function( i, tracker ) {

				// ignore trackers for other web properties
				if ( tracker.get( 'trackingId' ) !== '{$tracking_id}' ) {
					return;
				}

				originalSendHitTasks[ tracker.get( 'name' ) ] = tracker.get( 'sendHitTask' );

				tracker.set( 'sendHitTask', function( model ) {

					// call the original sendHitTask function to send information to Google Analytics servers
					originalSendHitTasks[ tracker.get( 'name' ) ]( model );

					// is this a pageview hit?
					if ( /&t=pageview&/.test( model.get( 'hitPayload' ) ) ) {
						pageviewHitCount += 1;
					}

					// multiple pageview requests suggest a property is being tracked more than once
					if ( pageviewHitCount >= 2 ) {
						restoreOriginalSendHitTasks();
						reportResults( true );
					}
				} );
			} );

			// if not duplicates are detected during the first seconds, try checking if other
			// trackers (for example named trackers from GTM) were created for the same tracking ID
			reportResultsTimeout = setTimeout( function() {

				{$ga_function_name}( function() {

					var trackers = jQuery.map( {$ga_function_name}.getAll(), function( tracker ) {
						if ( '{$tracking_id}' === tracker.get( 'trackingId' ) ) {
							return tracker;
						}
					} );

					reportResults( trackers.length > 1 );
				} );

			}, 3000 );
		}

		{$ga_function_name}( wc_ga_pro.findDuplicateTrackingCodes );
		JS;
	}


	/**
	 * Determines whether we should try to detect duplicate tracking codes.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	private function should_check_for_duplicate_tracking_codes(): bool {

		$should_check = false === get_transient( $this->transient_name );

		// tracking Administrators or Shop Managers may not be enabled on other plugins
		// so is better to wait and check for duplicates when a different user is
		// exploring the website
		if ( current_user_can( 'manage_woocommerce' ) ) {
			$should_check = false;
		}

		/**
		 * Filters whether we should try to detect duplicate tracking codes.
		 *
		 * @since 1.8.11
		 *
		 * @param bool $should_check whether we should check for duplicate tracking codes
		 * @param Integration $integration the integration instance
		 */
		return (bool) apply_filters( 'wc_google_analytics_pro_should_check_for_duplicate_tracking_codes', $should_check, $this );
	}


	/**
	 * Adds the admin notice.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function add_admin_notice(): void {

		// only show the notice if we detect that the web property is being tracked more than once
		if ( 'yes' !== get_transient( $this->transient_name ) ) {
			return;
		}

		wc_google_analytics_pro()->get_admin_notice_handler()->add_admin_notice(
			'<strong>' . __( 'Google Analytics Pro:', 'woocommerce-google-analytics-pro' ) . '</strong>' . ' ' .
			__( "Heads up! We've detected that another plugin is sending duplicated events to Google Analytics, which can result in duplicated tracking data. Please disable any other plugins tracking events in Google Analytics while using Google Analytics Pro.", 'woocommerce-google-analytics-pro' ),
			'duplicate-google-analytics-tracking-code',
			[
					'notice_class' => 'error',
			]
		);
	}


	/**
	 * Deletes the results from the duplicate tracking code verification when a
	 * plugin is deactivated.
	 *
	 * If the deactivated plugin was the one sending duplicate events, checking
	 * again should show that there are no problems anymore and the notice will
	 * remain hidden.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function clear_duplicate_tracking_code_results(): void {

		// delete the transient if we previously detected a conflict only
		if ( 'yes' === get_transient( $this->transient_name ) ) {
			delete_transient( $this->transient_name );
		}
	}


}
