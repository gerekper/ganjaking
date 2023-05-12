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
 * @copyright   Copyright (c) 2012-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_0 as Framework;

/**
 * WooCommerce Product Bundles integration class.
 *
 * @since 2.2.0
 */
class WC_Local_Pickup_Plus_Integration_Product_Bundles {


	/* @var array memoized flag whether a bundled product should be shipped individually */
	private $bundled_item_shipped_individually = array();


	/**
	 * Loads integration hooks.
	 *
	 * Ensures that child products of a product bundle do not follow their own handling, unless a child is meant to be shipped individually.
	 * If this setting is disabled, they will inherit the parent bundle container handling and pickup location data, if set.
	 *
	 * @since 2.2.0
	 */
	public function __construct() {

		// perhaps disable the cart item field for child bundled products that do not ship individually
		add_filter( 'wc_local_pickup_plus_get_pickup_location_cart_item_field_html', array( $this, 'remove_cart_item_pickup_field' ), 100, 2 );

		// ensure that items in a container bundle follow the parent product handling
		add_filter( 'wc_local_pickup_plus_cart_shipping_packages', array( $this, 'adjust_cart_items_handling' ), 100 );

		// make sure product bundles containing only products that can be picked up can also be picked up
		add_filter( 'wc_local_pickup_plus_product_can_be_picked_up', [ $this, 'product_bundle_can_be_picked_up' ], 10, 3 );
	}


	/**
	 * Ensures that a cart item pickup field for bundled products inside a product bundle is output conditionally.
	 *
	 * Displays the cart item pickup field only if a bundled item is shipped individually, hides the field otherwise.
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

		if ( '' !== $field_html && ! $this->is_bundled_item_shipped_individually( $cart_item_id ) ) {
			$field_html = '';
		}

		return $field_html;
	}


	/**
	 * Adjusts the product handling before items are sorted into packages.
	 *
	 * Ensures that products that are part of a bundle follow the parent handling unless individual shipping is enabled.
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
					if ( ! $this->is_bundled_item_shipped_individually( $cart_item ) ) {

						// gather parent bundle & children
						$cart_item_bundle_key = $this->get_parent_bundle_cart_item_key( $cart_item );
						$cart_item_bundle     = $this->get_parent_bundle_cart_item( $cart_item_bundle_key, $current_items );
						$cart_bundled_items   = $this->get_bundled_cart_items( $cart_item_bundle, $current_items );

						// the parent bundle is set for shipping
						if ( ! empty( $cart_bundled_items ) && isset( $current_items['ship_items'][ $cart_item_bundle_key ] ) ) {

							/* @type array $cart_bundled_items */
							foreach ( $cart_bundled_items as $bundled_cart_item_key => $bundled_cart_item ) {

								// remove any pickup data from teh child item to ensure it is not transported in the items to ship array
								if ( isset( $bundled_cart_item['pickup_location_id'] ) ) {
									unset( $bundled_cart_item['pickup_location_id'], $bundled_cart_item['pickup_data'], $bundled_cart_item['ship_via'] );
								}

								$ship_items[ $bundled_cart_item_key ] = $bundled_cart_item;

								// remove the child item from the cart items since it has been already assigned to items to ship array
								unset( $cart_items[ $bundled_cart_item_key ] );
							}

						} else {

							$container_pickup_location_id    = isset( $current_items['pickup_items'][ $cart_item_bundle_key ]['pickup_location_id'] ) ? $current_items['pickup_items'][ $cart_item_bundle_key ]['pickup_location_id'] : 0;
							$cart_item['pickup_location_id'] = isset( $cart_item['pickup_location_id'] ) && $cart_item['pickup_location_id'] > 0 ? (int) $cart_item['pickup_location_id'] : $container_pickup_location_id;

							if ( 0 === (int) $cart_item['pickup_location_id'] ) {

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

						// either the item is not a bundled item or it can be individually handled within a bundle
						$pickup_items[ $cart_item_key ] = $cart_item;
					}
				}

			// cycle items that were assigned to shipping
			} elseif ( 'ship_items' === $handling ) {
				foreach ( $cart_items as $cart_item_key => $cart_item ) {

					// ignore if the handling is per-item
					if ( ! $this->is_bundled_item_shipped_individually( $cart_item ) ) {

						// gather parent bundle & children
						$cart_item_bundle_key = $this->get_parent_bundle_cart_item_key( $cart_item );
						$cart_item_bundle     = $this->get_parent_bundle_cart_item( $cart_item_bundle_key, $current_items );
						$cart_bundled_items   = $this->get_bundled_cart_items( $cart_item_bundle, $current_items );

						// the parent container is set for pickup
						if ( ! empty( $cart_bundled_items ) && isset( $current_items['pickup_items'][ $cart_item_bundle_key ] ) ) {

							$bundle_pickup_location_id = $current_items['pickup_items'][ $cart_item_bundle_key ]['pickup_location_id'];
							$bundle_pickup_date        = $current_items['pickup_items'][ $cart_item_bundle_key ]['pickup_date'];

							/* @type array $cart_bundled_items */
							foreach ( $cart_bundled_items as $bundled_cart_item_key => $bundled_cart_item ) {

								$bundled_cart_item['ship_via']           = $pickup;
								$bundled_cart_item['pickup_location_id'] = $bundle_pickup_location_id;
								$bundled_cart_item['pickup_date']        = $bundle_pickup_date;
								$pickup_items[ $bundled_cart_item_key ]  = $bundled_cart_item;

								unset( $cart_items[ $bundled_cart_item_key ] );
							}

						} else {

							if ( isset( $cart_item['ship_via'] ) && $pickup === $cart_item['ship_via'] ) {
								unset( $cart_item['ship_via'] );
							}

							// fallback for sanity to default behaviour if one of the necessary variables cannot be determined
							$ship_items[ $cart_item_key ] = $cart_item;
						}

					} else {

						if ( isset( $cart_item['ship_via'] ) && $pickup === $cart_item['ship_via'] ) {
							unset( $cart_item['ship_via'] );
						}

						// either the item is not a bundled item or it can be individually handled within a bundle
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
	 * Checks each bundled item when checking if a product bundle can be picked up.
	 *
	 * @internal
	 *
	 * @since 2.8.3
	 *
	 * @param bool $can_be_picked_up whether the product can be picked up
	 * @param int|\WC_Product $product product ID or object
	 * @param null|int|\WC_Local_Pickup_Plus_Pickup_Location $pickup_location optional: a pickup location object or ID
	 * @return bool
	 */
	public function product_bundle_can_be_picked_up( $can_be_picked_up, $product, $pickup_location ) {

		if ( $product instanceof \WC_Product_Bundle ) {

			$can_be_picked_up = true;

			foreach ( $product->get_bundled_items() as $bundled_item ) {

				if ( ! wc_local_pickup_plus()->get_products_instance()->product_can_be_picked_up( $bundled_item->get_product(), $pickup_location ) ) {
					$can_be_picked_up = false;
					break;
				}
			}
		}

		return $can_be_picked_up;
	}


	/**
	 * Returns a cart item key from a cart item array.
	 *
	 * @since 2.2.0
	 *
	 * @param array|string $cart_item cart item array data or string
	 * @return string|null
	 */
	private function get_cart_item_key( $cart_item ) {

		$cart_item_key = null;

		if ( is_string( $cart_item ) ) {
			$cart_item_key = $cart_item;
		} elseif ( is_array( $cart_item ) ) {
			$cart_item_key = isset( $cart_item['cart_item_key'] ) ? $cart_item['cart_item_key'] : null;
		}

		return $cart_item_key;
	}


	/**
	 * Returns a cart item key from a cart item bundle array.
	 *
	 * @since 2.2.0
	 *
	 * @param array $cart_item a cart item that could be a bundled product or a product bundle container
	 * @return null|string
	 */
	private function get_parent_bundle_cart_item_key( $cart_item ) {
		return isset( $cart_item['bundled_by'] ) ? $cart_item['bundled_by'] : null;
	}


	/**
	 * Returns a cart item bundle.
	 *
	 * @since 2.2.0
	 *
	 * @param string $bundle_cart_item_key the product bundle cart item key
	 * @param array $cart_item_groups associative array of cart item divided by handling groups
	 * @return array cart item
	 */
	private function get_parent_bundle_cart_item( $bundle_cart_item_key, array $cart_item_groups ) {

		$bundle_cart_item = null;

		foreach ( $cart_item_groups as $cart_items ) {
			foreach ( $cart_items as $cart_item_key => $cart_item ) {

				if ( $bundle_cart_item_key === $cart_item_key ) {

					$bundle_cart_item = $cart_item;
					break;
				}
			}
		}

		return $bundle_cart_item;
	}


	/**
	 * Returns the product bundle's children items.
	 *
	 * We cannot use directly `wc_pb_get_bundled_cart_items()` because it calls the cart object contents and that may return a result already filtered by Local Pickup Plus.
	 * @see wc_pb_get_bundled_cart_items()
	 * @see \WC_Local_Pickup_Plus_Integration_Product_Bundles::adjust_cart_items_handling()
	 *
	 * @since 2.2.0
	 *
	 * @param array $cart_item_bundle the parent product bundle item
	 * @param array $cart_item_groups cart items from the cart subdivided per handling types
	 * @return array associative array of cart item keys and contents
	 */
	private function get_bundled_cart_items( $cart_item_bundle, $cart_item_groups ) {

		$bundled_items     = array();
		$bundled_item_keys = isset( $cart_item_bundle['bundled_items'] ) ? $cart_item_bundle['bundled_items'] : array();

		foreach ( $bundled_item_keys as $bundled_item_cart_key ) {

			foreach ( $cart_item_groups as $cart_items ) {

				if ( ! empty( $cart_items ) ) {

					foreach ( $cart_items as $cart_item_key => $cart_item ) {

						// It is important to check if the current cart item is not shipped individually:
						// Product Bundles can flag an individual product in a bundle to ship independently from the others or the whole bundle.
						// If that's so, we can skip the inclusion of the cart item in this context, so it won't be automatically pushed in the parent's handling group.
						if ( $bundled_item_cart_key === $cart_item_key && ! $this->is_bundled_item_shipped_individually( $cart_item ) ) {
							$bundled_items[ $cart_item_key ] = $cart_item;
						}
					}
				}
			}
		}

		return $bundled_items;
	}


	/**
	 * Returns a bundled item ID.
	 *
	 * @since 2.2.0
	 *
	 * @param array $cart_item a cart item as array data that might be part of a bundle
	 * @return int
	 */
	private function get_bundled_item_id( $cart_item ) {
		return is_array( $cart_item ) && isset( $cart_item['bundled_item_id'] ) ? (int) $cart_item['bundled_item_id'] : 0;
	}


	/**
	 * Checks whether a cart item is part of a bundle and the shipping should be handled by its parent.
	 *
	 * @since 2.2.0
	 *
	 * @param string|array $cart_item_bundled a cart item array or key which could be part of a bundle
	 *
	 * @return bool
	 */
	private function is_bundled_item_shipped_individually( $cart_item_bundled ) {

		$shipped_individually  = true;
		$cart_item_bundled_key = $this->get_cart_item_key( $cart_item_bundled );

		if ( is_string( $cart_item_bundled_key ) ) {

			if ( array_key_exists( $cart_item_bundled_key, $this->bundled_item_shipped_individually ) ) {

				$shipped_individually = $this->bundled_item_shipped_individually[ $cart_item_bundled_key ];

			} else {

				$cart_item_bundled    = is_string( $cart_item_bundled ) ? WC()->cart->get_cart_item( $cart_item_bundled ) : $cart_item_bundled;
				$cart_item_bundled_id = $this->get_bundled_item_id( $cart_item_bundled );
				$cart_item_bundle     = $cart_item_bundled_id > 0 ? wc_pb_get_bundled_cart_item_container( $cart_item_bundled ) : null;

				if ( $cart_item_bundle && isset( $cart_item_bundle['data'] ) && $cart_item_bundle['data'] instanceof \WC_Product ) {

					/* @type \WC_Product_Bundle $product_bundle */
					$product_bundle = $cart_item_bundle['data'];

					if ( $product_bundle->is_type( 'bundle' ) && ( $bundled_products = $product_bundle->get_bundled_items() ) ) {

						/* @type \WC_Bundled_Item $bundled_item */
						foreach ( $bundled_products as $bundled_product_id => $bundled_item ) {

							if ( $cart_item_bundled_id === (int) $bundled_product_id ) {

								$this->bundled_item_shipped_individually[ $cart_item_bundled_key ] = $shipped_individually = $bundled_item->is_shipped_individually();
								break;
							}
						}
					}
				}
			}
		}

		return $shipped_individually;
	}


}
