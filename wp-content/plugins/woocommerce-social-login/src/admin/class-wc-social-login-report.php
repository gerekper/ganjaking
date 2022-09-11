<?php
/**
 * WooCommerce Social Login
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Social Login to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-social-login/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Social Login report handler.
 *
 * @since 1.0.0
 */
class WC_Report_Social_Login extends \WC_Admin_Report {


	/**
	 * Output the report
	 *
	 * @since 1.0.0
	 */
	public function output_report() {

		$social_registrations = $this->get_social_registrations();

		include( wc_social_login()->get_plugin_path() . '/src/admin/views/html-report-social-login.php' );
	}


	/**
	 * Return an array of social registrations
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_social_registrations() {
		global $wpdb;

		$social_registration = array();

		foreach ( wc_social_login()->get_providers() as $provider ) {

			$linked_accounts = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE meta_key = %s", '_wc_social_login_' . $provider->get_id() . '_identifier' ) );

			if ( $linked_accounts ) {

				$social_registration[ $provider->get_id() ] = array(
					'provider_title'  => $provider->get_title(),
					'chart_color'     => $provider->get_color(),
					'linked_accounts' => $linked_accounts,
				);
			}
		}

		return $social_registration;
	}


}
