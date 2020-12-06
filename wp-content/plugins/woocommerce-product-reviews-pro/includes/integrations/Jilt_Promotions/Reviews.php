<?php
/**
 * WooCommerce Product Reviews Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Product_Reviews_Pro\Integrations\Jilt_Promotions;

defined( 'ABSPATH' ) or exit;

/**
 * WooCommerce Product Vendors integration.
 *
 * TODO: remove this class by version 2.0.0 or by 2021-11-16 {DM 2020-11-16}
 *
 * @since 1.16.0
 * @deprecated 1.17.0
 */
final class Reviews {


	/**
	 * Reviews constructor.
	 *
	 * @since 1.17.0
	 * @deprecated 1.17.0
	 */
	public function __construct() {

		wc_deprecated_function( __METHOD__, '1.17.0' );
	}


	/**
	 * Enables the Reviews message when the merchant visits the WooCommerce > Reviews page.
	 *
	 * @internal
	 *
	 * @since 1.16.0
	 * @deprecated 1.17.0
	 */
	public function maybe_enable_reviews_message() {

		wc_deprecated_function( __METHOD__, '1.17.0' );
	}


	/**
	 * Enqueues the assets.
	 *
	 * @internal
	 *
	 * @since 1.16.0
	 * @deprecated 1.17.0
	 */
	public function enqueue_assets() {

		wc_deprecated_function( __METHOD__, '1.17.0' );
	}


	/**
	 * Outputs the admin notice for the Reviews message.
	 *
	 * @internal
	 *
	 * @since 1.16.0
	 * @deprecated 1.17.0
	 */
	public function add_admin_notices() {

		wc_deprecated_function( __METHOD__, '1.17.0' );
	}


}
