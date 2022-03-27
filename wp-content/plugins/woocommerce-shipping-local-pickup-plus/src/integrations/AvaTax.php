<?php
/**
 * WooCommerce Local Pickup Plus
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2022, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Local_Pickup_Plus\Integrations;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * WooCommerce AvaTax integration class.
 *
 * @since 2.7.5
 */
class AvaTax {


	/**
	 * Sets up the AvaTax integration with Local Pickup Plus.
	 *
	 * @since 2.7.5
	 */
	public function __construct() {

		add_action( 'init', function() { $this->remove_legacy_integration(); } );

		add_filter( 'wc_avatax_product_destination', [ $this, 'set_package_destination' ], 10, 3 );

		add_filter( 'wc_avatax_checkout_ready_for_calculation', [ $this, 'set_cart_ready_for_tax_calculation' ] );
	}


	/**
	 * Removes hooks set on Avatax to move the integration on Local Pickup Plus.
	 *
	 * @since 2.7.5
	 */
	private function remove_legacy_integration() {

		if ( function_exists( 'wc_avatax' ) ) {

			$integrations = wc_avatax()->get_integrations();

			if ( $integrations ) {

				$callables = [
					'wc_avatax_product_destination'            => [ $integrations, 'set_lpp_destination' ],
					'wc_avatax_checkout_ready_for_calculation' => [ $integrations, 'set_lpp_ready_for_calculation' ],
				];

				foreach ( $callables as $hook => $callable ) {

					if ( is_callable( $callable ) && has_filter( $hook, $callable ) ) {

						remove_filter( $hook, $callable, 10 );
					}
				}
			}
		}
	}


	/**
	 * Sets the package destination to the pickup address, if package is for pickup.
	 *
	 * @internal
	 *
	 * @since 2.7.5
	 *
	 * @param array $address address data
	 * @param \WC_Product $product product in package
	 * @param array $package package data
	 * @param null|int|string $package_key package index
	 * @return array
	 */
	public function set_package_destination( $address, $product, $package, $package_key = null ) {

		if ( $pickup_location = wc_local_pickup_plus()->get_packages_instance()->get_package_pickup_location( $package ) ) {

			$address = $pickup_location->get_address()->get_array();
		}

		return $address;
	}


	/**
	 * Sets the cart as "ready for calculation" when a pickup is being used.
	 *
	 * @internal
	 *
	 * @since 2.7.5
	 *
	 * @param bool $ready true if ready for calculation
	 * @return bool
	 */
	public function set_cart_ready_for_tax_calculation( $ready ) {

		if ( WC()->customer->get_shipping_country() ) {

			foreach ( WC()->shipping()->get_packages() as $package_key => $package ) {

				if ( wc_local_pickup_plus_shipping_method_id() === wc_get_chosen_shipping_method_for_package( $package_key, $package ) ) {

					$ready = true;
				}
			}
		}

		return $ready;
	}


}
