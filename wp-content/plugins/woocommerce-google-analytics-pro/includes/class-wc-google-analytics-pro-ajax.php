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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * The AJAX class.
 *
 * @since 1.0.0
 */
class WC_Google_Analytics_Pro_AJAX {


	/**
	 * Constructs the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'wp_ajax_wc_google_analytics_pro_revoke_access', array( $this, 'revoke_access' ) );

		add_action( 'wp_ajax_wc_google_analytics_pro_report_duplicate_tracking_code_results', [ $this, 'report_duplicate_tracking_code_results' ] );
		add_action( 'wp_ajax_nopriv_wc_google_analytics_pro_report_duplicate_tracking_code_results', [ $this, 'report_duplicate_tracking_code_results' ] );
	}


	/**
	 * Revokes access to a Google Account.
	 *
	 * @since 1.0.0
	 */
	public function revoke_access() {

		check_ajax_referer( 'revoke-access', 'security' );

		$url = wc_google_analytics_pro()->get_integration()->get_access_token_revoke_url();

		$response = wp_safe_remote_get( $url );

		// log errors
		if ( is_wp_error( $response ) ) {

			wc_google_analytics_pro()->log( sprintf( 'Could not revoke access token: %s', json_encode( $response->errors ) ) );
		}

		delete_option( 'wc_google_analytics_pro_access_token' );
		delete_option( 'wc_google_analytics_pro_refresh_token' );
		delete_option( 'wc_google_analytics_pro_account_id' );
		delete_transient( 'wc_google_analytics_pro_properties' );

		wp_die();
	}


	/**
	 * Stores the results of the search for duplicate tracking codes.
	 *
	 * @internal
	 *
	 * @since 1.8.6
	 */
	public function report_duplicate_tracking_code_results() {

		check_ajax_referer( 'report-duplicate-tracking-code-results', 'nonce' );

		set_transient(
			'wc_' . wc_google_analytics_pro()->get_id() . '_site_has_duplicate_tracking_codes',
			! empty( $_POST['has_duplicate_tracking_codes'] ) ? 'yes' : 'no',
			WEEK_IN_SECONDS
		);

		wp_die();
	}


}
