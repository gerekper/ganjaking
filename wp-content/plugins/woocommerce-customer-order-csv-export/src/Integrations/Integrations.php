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

namespace SkyVerge\WooCommerce\CSV_Export\Integrations;

defined( 'ABSPATH' ) or exit;

/**
 * Customer/Order CSV Export Integrations handler
 *
 * @since 5.1.0
 */
class Integrations {


	/**
	 * Initializes Jilt Promotions handlers.
	 *
	 * We need to instantiate Jilt Promotion handlers after plugins_loaded to ensure all necessary classes are loaded first.
	 *
	 * TODO: remove this method by version 6.0.0 or by 2021-11-16 {DM 2020-11-16}
	 *
	 * @internal
	 *
	 * @since 5.1.0
	 * @deprecated 5.2.0
	 */
	public function load_jilt_promotions_handlers() {

		wc_deprecated_function( __METHOD__, '5.2.0' );
	}


}

