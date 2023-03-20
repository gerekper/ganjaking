<?php
/**
 * WooCommerce Customer/Order/Coupon Export
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon Export to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon Export for your
 * needs please refer to http://docs.woocommerce.com/document/ordercustomer-csv-exporter/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\CSV_Export\Integrations\Jilt_Promotions;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\CSV_Export\Automations\Automation;

/**
 * Handler for Jilt Promotion prompts on the Automations screen.
 *
 * TODO: remove this class by version 6.0.0 or by 2021-11-16 {DM 2020-11-16}
 *
 * @since 5.1.0
 * @deprecated 5.2.0
 */
final class Automations {


	/**
	 * Automations constructor.
	 *
	 * @since 5.2.0
	 * @deprecated 5.2.0
	 */
	public function __construct() {

		wc_deprecated_function( __METHOD__, '5.2.0' );
	}


	/**
	 * Enqueues the assets.
	 *
	 * @internal
	 *
	 * @since 5.1.0
	 * @deprecated 5.2.0
	 */
	public function enqueue_assets() {

		wc_deprecated_function( __METHOD__, '5.2.0' );
	}


	/**
	 * Enables the automated exports message when a customers automated export is saved.
	 *
	 * @internal
	 *
	 * @since 5.1.0
	 * @deprecated 5.2.0
	 *
	 * @param Automation $automation
	 */
	public function maybe_enable_automated_exports_message( Automation $automation ) {

		wc_deprecated_function( __METHOD__, '5.2.0' );
	}


	/**
	 * Outputs the admin notices for the Automated exports message.
	 *
	 * @internal
	 *
	 * @since 5.1.0
	 * @deprecated 5.2.0
	 */
	public function add_admin_notices() {

		wc_deprecated_function( __METHOD__, '5.2.0' );
	}


}


