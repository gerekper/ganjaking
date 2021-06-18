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
 * @copyright   Copyright (c) 2012-2021, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * WooCommerce Composite Products integration class.
 *
 * @since 2.2.0
 */
class WC_Local_Pickup_Plus_Integration_Composite_Products {


	/** @var array memoized flag whether composite product components can be handled individually for shipping */
	private $composite_product_component_shipped_individually = array();


	/**
	 * Loads integration hooks.
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
	 * Ensures that a cart item pickup field for components of a composite product is output conditionally.
	 *
	 * Displays the cart item pickup field only if a component product can be shipped individually.
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

		if ( '' !== $field_html && ! $this->is_component_product_shipped_individually( $cart_item_id ) ) {
			$field_html = '';
		}

		return $field_html;
	}


	/**
	 * Adjusts the product handling before items are sorted into packages.
	 *
	 * Ensures that products that are components of a composite product follow the parent handling unless individual shipping is allowed
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
					if ( ! $this->is_component_product_shipped_individually( $cart_item ) ) {

						// gather composite parent & its children components
						$cart_item_parent_key = $this->get_composite_parent_cart_item_key( $cart_item );
						$cart_item_parent     = $this->get_composite_parent_cart_item( $cart_item_parent_key, $current_items );
						$cart_composite_items = $this->get_composite_cart_items( $cart_item_parent, $current_items );

						// the composite parent is set for shipping
						if ( ! empty( $cart_composite_items ) && isset( $current_items['ship_items'][ $cart_item_parent_key ] ) ) {

							/* @type array $cart_composite_items */
							foreach ( $cart_composite_items as $composite_child_cart_item_key => $composite_cart_item ) {

								// remove any pickup data from teh child item to ensure it is not transported in the items to ship array
								if ( isset( $composite_cart_item['pickup_location_id'] ) ) {
									unset( $composite_cart_item['pickup_location_id'], $composite_cart_item['pickup_data'], $composite_cart_item['ship_via'] );
								}

								$ship_items[ $composite_child_cart_item_key ] = $composite_cart_item;

								// remove the child item from the cart items since it has been already assigned to items to ship array
								unset( $cart_items[ $composite_child_cart_item_key ] );
							}

						} else {

							$container_pickup_location_id    = isset( $current_items['pickup_items'][ $cart_item_parent_key ]['pickup_location_id'] ) ? $current_items['pickup_items'][ $cart_item_parent_key ]['pickup_location_id'] : 0;
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

						// either the item is not a composite component or it can be individually handled within a composite product
						$pickup_items[ $cart_item_key ] = $cart_item;
					}
				}

			// cycle items that were assigned to shipping
			} elseif ( 'ship_items' === $handling ) {
				foreach ( $cart_items as $cart_item_key => $cart_item ) {

					// ignore if the handling is per-item
					if ( ! $this->is_component_product_shipped_individually( $cart_item ) ) {

						// gather composite parent & its children components
						$cart_item_parent_key = $this->get_composite_parent_cart_item_key( $cart_item );
						$cart_item_parent     = $this->get_composite_parent_cart_item( $cart_item_parent_key, $current_items );
						$cart_composite_items = $this->get_composite_cart_items( $cart_item_parent, $current_items );

						// the parent container is set for pickup
						if ( ! empty( $cart_composite_items ) && isset( $current_items['pickup_items'][ $cart_item_parent_key ] ) ) {

							$parent_composite_pickup_location_id = $current_items['pickup_items'][ $cart_item_parent_key ]['pickup_location_id'];
							$parent_composite_pickup_date        = $current_items['pickup_items'][ $cart_item_parent_key ]['pickup_date'];

							/* @type array $cart_composite_items */
							foreach ( $cart_composite_items as $composite_child_cart_item_key => $composite_cart_item ) {

								$composite_cart_item['ship_via']                = $pickup;
								$composite_cart_item['pickup_location_id']      = $parent_composite_pickup_location_id;
								$composite_cart_item['pickup_date']             = $parent_composite_pickup_date;
								$pickup_items[ $composite_child_cart_item_key ] = $composite_cart_item;

								unset( $cart_items[ $composite_child_cart_item_key ] );
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

						// either the item is not a composite component or it can be individually handled within a composite product
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
	 * Returns a composite parent item cart key from a child component cart item.
	 *
	 * @since 2.2.0
	 *
	 * @param array $cart_item a cart item that is supposed to be a composite product component
	 * @return string|null
	 */
	private function get_composite_parent_cart_item_key( $cart_item ) {
		return isset( $cart_item['composite_parent'] ) ? $cart_item['composite_parent'] : null;
	}


	/**
	 * Returns a composite cart item from its cart item key.
	 *
	 * @since 2.2.0
	 *
	 * @param string $composite_cart_item_key the cart key of a composite item
	 * @param array $cart_item_groups associative array of cart item divided by handling groups
	 * @return array|null
	 */
	private function get_composite_parent_cart_item( $composite_cart_item_key, array $cart_item_groups ) {

		$composite_cart_item = null;

		foreach ( $cart_item_groups as $cart_items ) {

			if ( ! empty( $cart_items ) ) {

				foreach ( $cart_items as $cart_item_key => $cart_item ) {

					if ( $cart_item_key === $composite_cart_item_key ) {

						$composite_cart_item = $cart_item;
						break;
					}
				}
			}
		}

		return $composite_cart_item;
	}


	/**
	 * Returns the composite product's children component items.
	 *
	 * We cannot use directly `wc_cp_get_composited_cart_items()` because it calls the cart object contents and that may return a result already filtered by Local Pickup Plus.
	 * @see wc_cp_get_composited_cart_items()
	 * @see \WC_Local_Pickup_Plus_Integration_Composite_Products::adjust_cart_items_handling()
	 *
	 * @since 2.2.0
	 *
	 * @param array $composite_cart_item the parent composite product item
	 * @param array $cart_item_groups cart items from the cart subdivided per handling types
	 * @return array associative array of cart item keys and contents
	 */
	private function get_composite_cart_items( $composite_cart_item, array $cart_item_groups ) {

		$component_items     = array();
		$component_item_keys = isset( $composite_cart_item['composite_children'] ) ? $composite_cart_item['composite_children'] : array();

		foreach ( $component_item_keys as $component_item_cart_key ) {

			foreach ( $cart_item_groups as $cart_items ) {

				if ( ! empty( $cart_items ) ) {

					foreach ( $cart_items as $cart_item_key => $cart_item ) {

						// It is important to check if the current cart item is not shipped individually:
						// Composite Products can flag an individual product components within a container to ship independently from the others or the parent.
						// If that's so, we can skip the inclusion of the cart item in this context, so it won't be automatically pushed in the parent's handling group.
						if ( $component_item_cart_key === $cart_item_key && ! $this->is_component_product_shipped_individually( $cart_item ) ) {
							$component_items[ $cart_item_key ] = $cart_item;
						}
					}
				}
			}
		}

		return $component_items;
	}


	/**
	 * Returns a composite component item ID.
	 *
	 * @since 2.2.0
	 *
	 * @param array $cart_item a cart item that could be a composite component
	 * @return int
	 */
	private function get_composite_item_component_id( $cart_item ) {
		return isset( $cart_item['composite_item'] ) ? (int) $cart_item['composite_item'] : 0;
	}


	/**
	 * Checks whether a cart item is a component of a composite product and the shipping should be handled by its parent container.
	 *
	 * @since 2.2.0
	 *
	 * @param string|array $cart_item_component a cart item array or key which could be part of a composite
	 * @return bool
	 */
	private function is_component_product_shipped_individually( $cart_item_component ) {

		$shipped_individually    = true;
		$cart_item_component_key = $this->get_cart_item_key( $cart_item_component );

		if ( is_string( $cart_item_component_key ) ) {

			if ( array_key_exists( $cart_item_component_key, $this->composite_product_component_shipped_individually ) ) {

				$shipped_individually = $this->composite_product_component_shipped_individually[ $cart_item_component_key ];

			} else {

				$cart_item_component = is_string( $cart_item_component ) ? WC()->cart->get_cart_item( $cart_item_component ) : $cart_item_component;
				$cart_component_id   = $this->get_composite_item_component_id( $cart_item_component );
				$cart_item_container = wc_cp_get_composited_cart_item_container( $cart_item_component );
				$composite_product   = isset( $cart_item_container['data'] ) ? $cart_item_container['data'] : null;

				if ( $composite_product instanceof \WC_Product_Composite && $composite_product->is_type( 'composite' ) && ( $components = $composite_product->get_components() ) ) {

					/* @type \WC_CP_Component $component composite product component object */
					foreach ( $components as $component_id => $component ) {

						if ( $component_id === $cart_component_id ) {

							$this->composite_product_component_shipped_individually[ $cart_item_component_key ] = $shipped_individually = $component->is_shipped_individually();
							break;
						}
					}
				}
			}
		}

		return $shipped_individually;
	}


}
