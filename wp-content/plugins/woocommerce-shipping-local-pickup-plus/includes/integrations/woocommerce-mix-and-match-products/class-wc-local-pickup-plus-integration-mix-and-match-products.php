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
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * WooCommerce Mix and Match Products integration class.
 *
 * @since 2.2.0
 */
class WC_Local_Pickup_Plus_Integration_Mix_And_Match_Products {


	/* @var array associative array of cached cart item keys of container items with their children */
	private $matched_items = array();


	/**
	 * Loads integration hooks.
	 *
	 * Ensures that child products of a Mix and Match container do not follow their own handling, unless per-item shipping is set on the container.
	 * If this setting is disabled, they will inherit the parent container handling and pickup location data, if set.
	 *
	 * @since 2.2.0
	 */
	public function __construct() {

		// perhaps disable the cart item field for child products that do not ship individually
		add_filter( 'wc_local_pickup_plus_get_pickup_location_cart_item_field_html', array( $this, 'remove_cart_item_pickup_field' ), 100, 2 );

		// ensure that items in a container follow the parent product handling
		add_filter( 'wc_local_pickup_plus_cart_shipping_packages', array( $this, 'adjust_cart_items_handling' ), 100 );
	}


	/**
	 * Ensures that a cart item pickup field for products inside a mix-and-match container is output conditionally.
	 *
	 * Displays the cart item pickup field only if a child product is shipped individually.
	 *
	 * @internal
	 *
	 * @since 2.2.0
	 *
	 * @param string $field_html HTML
	 * @param string $cart_item_id the current cart item ID
	 * @return string HTML or empty string
	 */
	public function remove_cart_item_pickup_field( $field_html, $cart_item_id ) {

		if ( '' !== $field_html ) {

			$cart_item = WC()->cart->get_cart_item( $cart_item_id );

			if ( $this->is_shipping_handled_by_parent_container( $cart_item ) ) {
				$field_html = '';
			}
		}

		return $field_html;
	}


	/**
	 * Adjusts the product handling before items are sorted into packages.
	 *
	 * Ensures that products in a mix-n-match container follow the parent handling unless individual shipping is used.
	 *
	 * @internal
	 *
	 * @since 2.2.0
	 *
	 * @param array $cart_item_groups associative array
	 * @return array
	 */
	public function adjust_cart_items_handling( array $cart_item_groups ) {

		$pickup        = wc_local_pickup_plus_shipping_method_id();
		$pickup_items  = array();
		$ship_items    = array();
		$current_items = wp_parse_args( $cart_item_groups, array(
			'pickup_items' => isset( $cart_item_groups['pickup_items'] ) ? $cart_item_groups['pickup_items'] : $pickup_items,
			'ship_items'   => isset( $cart_item_groups['ship_items'] )   ? $cart_item_groups['ship_items']   : $ship_items,
		) );

		/* @type array $cart_items */
		foreach ( $cart_item_groups as $handling => $cart_items ) {

			// cycle items that were sorted for pickup
			if ( 'pickup_items' === $handling ) {

				foreach ( $cart_items as $cart_item_key => $cart_item ) {

					// ignore if the handling is per-item
					if ( $this->is_shipping_handled_by_parent_container( $cart_item ) ) {

						// gather container & children
						$container_cart_item     = $this->get_container_cart_item( $cart_item_key );
						$container_cart_item_key = $this->get_container_cart_item_key( $container_cart_item );
						$children_cart_items     = $container_cart_item_key ? $this->get_container_cart_item_children( $container_cart_item, $current_items ) : array();

						// the parent container is set for shipping
						if ( ! empty( $children_cart_items ) && isset( $current_items['ship_items'][ $container_cart_item_key ] ) ) {

							foreach ( $children_cart_items as $child_cart_item_key => $child_cart_item ) {

								// remove any pickup data from the child item to ensure it is not transported in the items to ship array
								if ( isset( $child_cart_item['pickup_location_id'] ) ) {
									unset( $child_cart_item['pickup_location_id'], $child_cart_item['pickup_data'], $child_cart_item['ship_via'] );
								}

								$ship_items[ $child_cart_item_key ] = $child_cart_item;

								// remove the child item from the cart items since it has been already assigned to items to ship array
								unset( $cart_items[ $child_cart_item_key ] );
							}

						} else {

							$container_pickup_location_id    = isset( $current_items['pickup_items'][ $container_cart_item_key ]['pickup_location_id'] ) ? $current_items['pickup_items'][ $container_cart_item_key ]['pickup_location_id'] : 0;
							$cart_item['pickup_location_id'] = isset( $cart_item['pickup_location_id'] ) && $cart_item['pickup_location_id'] > 0 ? (int) $cart_item['pickup_location_id'] : $container_pickup_location_id;

							if ( 0 === $cart_item['pickup_location_id'] ) {

								if ( isset( $cart_item['ship_via'] ) && $pickup === $cart_item['ship_via'] ) {
									unset( $cart_item['ship_via'] );
								}

								$ship_items[ $cart_item_key ]   = $cart_item;

							} else {

								$cart_item['ship_via']          = $pickup;
								$pickup_items[ $cart_item_key ] = $cart_item;
							}
						}

					} else {

						// either the item is not a mix and match managed item or it can be individually handled
						$pickup_items[ $cart_item_key ] = $cart_item;
					}
				}

			// cycle items that were assigned to shipping
			} elseif ( 'ship_items' === $handling ) {

				foreach ( $cart_items as $cart_item_key => $cart_item ) {

					// ignore if the handling is per-item
					if ( $this->is_shipping_handled_by_parent_container( $cart_item ) ) {

						// gather container & children
						$container_cart_item     = $this->get_container_cart_item( $cart_item_key );
						$container_cart_item_key = $this->get_container_cart_item_key( $container_cart_item );
						$children_cart_items     = $container_cart_item_key ? $this->get_container_cart_item_children( $container_cart_item, $current_items ) : array();

						// the parent container is set for pickup
						if ( ! empty( $children_cart_items ) && isset( $current_items['pickup_items'][ $container_cart_item_key ], $current_items['pickup_items'][ $container_cart_item_key ]['pickup_location_id'], $current_items['pickup_items'][ $container_cart_item_key ]['pickup_date'] ) ) {

							$container_pickup_location_id = $current_items['pickup_items'][ $container_cart_item_key ]['pickup_location_id'];
							$container_pickup_date        = $current_items['pickup_items'][ $container_cart_item_key ]['pickup_date'];

							foreach ( $children_cart_items as $child_cart_item_key => $child_cart_item ) {

								$child_cart_item['ship_via']           = $pickup;
								$child_cart_item['pickup_location_id'] = $container_pickup_location_id;
								$child_cart_item['pickup_date']        = $container_pickup_date;
								$pickup_items[ $child_cart_item_key ]  = $child_cart_item;

								unset( $cart_items[ $child_cart_item_key ] );
							}

						} else {

							if ( isset( $cart_item['ship_via'] ) && $pickup === $cart_item['ship_via'] ) {
								unset( $cart_item['ship_via'] );
							}

							// fallback for sanity to default behaviour if children cannot be determined
							$ship_items[ $cart_item_key ] = $cart_item;
						}

					} else {

						if ( isset( $cart_item['ship_via'] ) && $pickup === $cart_item['ship_via'] ) {
							unset( $cart_item['ship_via'] );
						}

						// either the item is not a mix and match managed item or it can be individually handled
						$ship_items[ $cart_item_key ] = $cart_item;
					}
				}
			}
		}

		// return the two groups of items that will be arranged later into packages
		return array(
			'pickup_items' => $pickup_items,
			'ship_items'   => $ship_items,
		);
	}


	/**
	 * Check whether the shipping should be handled by a parent container.
	 *
	 * @since 2.2.0
	 *
	 * @param string|array $cart_item a cart item
	 * @return bool
	 */
	private function is_shipping_handled_by_parent_container( $cart_item ) {

		$product_container = $this->get_container_product( $cart_item );

		return $product_container && $product_container->is_type( 'mix-and-match' ) && 'yes' !== $product_container->get_meta( '_mnm_per_product_shipping' );
	}


	/**
	 * Returns a container cart item key.
	 *
	 * @since 2.2.0
	 *
	 * @param string|array $cart_item a container cart item or cart key
	 * @return null|string
	 */
	private function get_container_cart_item_key( $cart_item ) {

		$cart_item_key = null;

		if ( is_string( $cart_item ) ) {
			$cart_item_key = $cart_item;
		} elseif ( is_array( $cart_item ) ) {
			$cart_item_key = isset( $cart_item['cart_item_key'] ) ? $cart_item['cart_item_key'] : null;
		}

		return $cart_item_key;
	}


	/**
	 * Returns a container cart item from another cart item.
	 *
	 * @since 2.2.0
	 *
	 * @param array|string $cart_item a child cart item or cart item key
	 * @return false|array
	 */
	private function get_container_cart_item( $cart_item ) {

		$container_cart_item = null;

		if ( is_string( $cart_item ) ) {
			$cart_item = WC()->cart->get_cart_item( $cart_item );
		}

		return wc_mnm_get_mnm_cart_item_container( $cart_item );
	}


	/**
	 * Returns the container item's children.
	 *
	 * We cannot use directly `wc_mnm_get_mnm_cart_items()` because it calls the cart object contents and that may return a result already filtered by Local Pickup Plus.
	 * @see wc_mnm_get_mnm_cart_items()
	 * @see \WC_Local_Pickup_Plus_Integration_Mix_And_Match_Products::adjust_cart_items_handling()
	 *
	 * @since 2.2.0
	 *
	 * @param array $container_cart_item the parent container item
	 * @param array $cart_item_groups cart items from the cart subdivided per handling types
	 * @return array associative array of cart item keys and contents
	 */
	private function get_container_cart_item_children( $container_cart_item, $cart_item_groups ) {

		$children                = array();
		$container_cart_item_key = isset( $container_cart_item['cart_item_key'] ) ? $container_cart_item['cart_item_key'] : null;

		if ( $container_cart_item_key && array_key_exists( $container_cart_item_key, $this->matched_items ) ) {

			$children = $this->matched_items[ $container_cart_item_key ];

		} else {

			if ( $matched_items = isset( $container_cart_item[ 'mnm_contents' ] ) ? $container_cart_item[ 'mnm_contents' ] : null ) {

				foreach ( $matched_items as $matched_item_key ) {

					/* @type array $cart_item_groups */
					foreach ( $cart_item_groups as $cart_items ) {

						if ( ! empty( $cart_items ) ) {

							/* @type array $cart_items */
							foreach ( $cart_items as $cart_item_key => $cart_item ) {

								if ( $matched_item_key === $cart_item_key ) {
									$children[ $matched_item_key ] = $cart_item;
								}
							}
						}
					}
				}
			}

			if ( $container_cart_item_key ) {
				$this->matched_items[ $container_cart_item_key ] = $children;
			}
		}


		return $children;
	}


	/**
	 * Returns a product container from a child cart item.
	 *
	 * @since 2.2.0
	 *
	 * @param array|string $cart_item a child cart item or cart item key
	 * @return null|\WC_Product_Mix_and_Match the mix-n-match product container
	 */
	private function get_container_product( $cart_item ) {

		$container_cart_item = $this->get_container_cart_item( $cart_item );

		return $container_cart_item && isset( $container_cart_item['data'] ) && $container_cart_item['data'] instanceof \WC_Product_Mix_and_Match ? $container_cart_item['data'] : null;
	}


}
