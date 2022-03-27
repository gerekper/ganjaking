<?php
/**
 * WooCommerce Sequential Order Numbers Pro
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
 * @copyright   Copyright (c) 2012-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Sequential_Order_Numbers_Pro;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * REST API handler.
 *
 * @since 1.13.0
 *
 * @method \WC_Seq_Order_Number_Pro get_plugin()
 */
class REST_API extends Framework\REST_API {


	/**
	 * Initializes the REST API handler.
	 *
	 * @since 1.13.0
	 *
	 * @param \WC_Seq_Order_Number_Pro $plugin main class
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		// Legacy WC REST API support
		// TODO drop this action when legacy REST API v1-v3 are no longer supported {BR 2017-04-17}
		add_action( 'woocommerce_api_create_order',              array( $this, 'handle_rest_api_orders' ) );
		// WC REST API v2 support (WC 3.0+)
		add_action( 'woocommerce_rest_insert_shop_order_object', array( $this, 'handle_rest_api_orders' ) );
		// WC REST API v1 support (WC 2.6+)
		add_action( 'woocommerce_rest_insert_shop_order',        array( $this, 'handle_rest_api_orders' ) );
	}


	/**
	 * Handles orders created with the WC REST API.
	 *
	 * For orders created via the /wp-json/wc/v1/ (WC 2.6+) endpoint we need an additional check because a post object is passed instead of a WC order.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 *
	 * @param \WP_Post|\WC_Order $order post or order object, depending on current action
	 */
	public function handle_rest_api_orders( $order ) {

		if ( $order instanceof \WC_Order ) {
			$this->get_plugin()->set_sequential_order_number( $order );
		} elseif ( $order instanceof \WP_Post && ( $order = wc_get_order( $order->ID ) ) ) {
			$this->get_plugin()->set_sequential_order_number( $order );
		}
	}


}
