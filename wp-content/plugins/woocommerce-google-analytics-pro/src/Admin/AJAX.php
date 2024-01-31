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
 * @copyright Copyright (c) 2015-2024, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Admin;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Duplicate_Tracking_Code_Detector;

defined( 'ABSPATH' ) or exit;

/**
 * The AJAX class.
 *
 * @since 1.0.0
 */
class AJAX {


	/**
	 * Constructs the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'wp_ajax_wc_google_analytics_pro_revoke_access', [ $this, 'revoke_access' ] );

		add_action( 'wp_ajax_wc_google_analytics_pro_report_duplicate_tracking_code_results', [ $this, 'report_duplicate_tracking_code_results' ] );
		add_action( 'wp_ajax_nopriv_wc_google_analytics_pro_report_duplicate_tracking_code_results', [ $this, 'report_duplicate_tracking_code_results' ] );
	}


	/**
	 * Revokes access to a Google Account.
	 *
	 * @since 1.0.0
	 */
	public function revoke_access(): void {

		check_ajax_referer( 'revoke-access', 'security' );

		wc_google_analytics_pro()->get_api_client_instance()->get_auth_instance()->revoke_access();

		wp_die();
	}


	/**
	 * Stores the results of the search for duplicate tracking codes.
	 *
	 * @internal
	 *
	 * @see Duplicate_Tracking_Code_Detector::print_js()
	 *
	 * @since 1.8.6
	 */
	public function report_duplicate_tracking_code_results(): void {

		check_ajax_referer( 'report-duplicate-tracking-code-results', 'nonce' );

		set_transient(
			'wc_' . wc_google_analytics_pro()->get_id() . '_site_has_duplicate_tracking_codes',
			! empty( $_POST['has_duplicate_tracking_codes'] ) ? 'yes' : 'no',
			WEEK_IN_SECONDS
		);

		wp_die();
	}


}

class_alias( AJAX::class, 'WC_Google_Analytics_Pro_AJAX' );
