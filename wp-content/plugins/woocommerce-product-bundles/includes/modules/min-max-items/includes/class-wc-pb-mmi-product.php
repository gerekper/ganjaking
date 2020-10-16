<?php
/**
 * WC_PB_MMI_Product class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Product Bundles
 * @since    5.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product-related functions and filters.
 *
 * @class    WC_PB_MMI_Product
 * @version  6.4.0
 */
class WC_PB_MMI_Product {

	/**
	 * Setup hooks.
	 */
	public static function init() {

		// Change bundled item quantities.
		add_filter( 'woocommerce_bundled_item_quantity', array( __CLASS__, 'bundled_item_quantity' ), 10, 3 );
		add_filter( 'woocommerce_bundled_item_quantity_max', array( __CLASS__, 'bundled_item_quantity_max' ), 10, 3 );

		// When min/max qty constraints are present, require input.
		add_filter( 'woocommerce_bundle_requires_input', array( __CLASS__, 'min_max_bundle_requires_input' ), 10, 2 );
	}

	/*
	|--------------------------------------------------------------------------
	| Application layer functions.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Find the price-optimized AND availability-constrained set of bundled item quantities that meet the min item count constraint while honoring the initial min/max item quantity constraints.
	 *
	 * @param  WC_Product  $product
	 * @return array
	 */
	public static function get_min_required_quantities( $bundle ) {

		$result = WC_PB_Helpers::cache_get( 'min_required_quantities_' . $bundle->get_id() );

		if ( is_null( $result ) ) {

			$quantities = array(
				'min' => array(),
				'max' => array()
			);

			$pricing_data  = array();
			$bundled_items = $bundle->get_bundled_items();

			if ( ! empty( $bundled_items ) ) {

				$min_qty = $bundle->get_meta( '_wcpb_min_qty_limit', true );

				foreach ( $bundled_items as $bundled_item ) {
					$pricing_data[ $bundled_item->get_id() ][ 'price' ]         = $bundled_item->get_price();
					$pricing_data[ $bundled_item->get_id() ][ 'regular_price' ] = $bundled_item->get_regular_price();
					$quantities[ 'min' ][ $bundled_item->get_id() ]             = $bundled_item->get_quantity( 'min', array( 'check_optional' => true ) );
					$quantities[ 'max' ][ $bundled_item->get_id() ]             = $bundled_item->get_quantity( 'max' );
				}

				// Slots filled so far.
				$filled_slots = 0;

				foreach ( $quantities[ 'min' ] as $item_min_qty ) {
					$filled_slots += $item_min_qty;
				}

				// Fill in the box with items that are in stock, giving preference to cheapest available.
				if ( $filled_slots < $min_qty ) {

					// Sort by cheapest.
					uasort( $pricing_data, array( __CLASS__, 'sort_by_price' ) );

					// Fill additional slots.
					foreach ( $pricing_data as $bundled_item_id => $data ) {

						$slots_to_fill = $min_qty - $filled_slots;

						if ( $filled_slots >= $min_qty ) {
							break;
						}

						$bundled_item = $bundled_items[ $bundled_item_id ];

						if ( false === $bundled_item->is_purchasable() ) {
							continue;
						}

						if ( false === $bundled_item->is_in_stock() ) {
							continue;
						}

						$max_stock    = $bundled_item->get_max_stock();
						$max_item_qty = $quantities[ 'max' ][ $bundled_item_id ];

						if ( '' === $max_item_qty ) {
							$max_items_to_use = $max_stock;
						} elseif ( '' === $max_stock ) {
							$max_items_to_use = $max_item_qty;
						} else {
							$max_items_to_use = min( $max_item_qty, $max_stock );
						}

						$min_items_to_use = $quantities[ 'min' ][ $bundled_item_id ];

						$items_to_use = '' !== $max_items_to_use ? min( $max_items_to_use - $min_items_to_use, $slots_to_fill ) : $slots_to_fill;

						$filled_slots += $items_to_use;

						$quantities[ 'min' ][ $bundled_item_id ] += $items_to_use;
					}
				}

				// If there are empty slots, then bundled items do not have sufficient stock to fill the minimum box size.
				// In this case, ignore stock constraints and return the optimal price quantities, forcing the bundle to show up as out of stock.

				if ( $min_qty > $filled_slots ) {
					$quantities[ 'min' ] = self::get_min_price_quantities( $bundle );
				}
			}

			$result = $quantities[ 'min' ];
			WC_PB_Helpers::cache_set( 'min_required_quantities_' . $bundle->get_id(), $result );
		}

		return $result;
	}

	/**
	 * Find the price-optimized set of bundled item quantities that meet the min item count constraint while honoring the initial min/max item quantity constraints.
	 *
	 * @param  WC_Product  $product
	 * @return array
	 */
	public static function get_min_price_quantities( $bundle ) {

		$result = WC_PB_Helpers::cache_get( 'min_price_quantities_' . $bundle->get_id() );

		if ( is_null( $result ) ) {

			$quantities = array(
				'min' => array(),
				'max' => array()
			);

			$pricing_data  = array();
			$bundled_items = $bundle->get_bundled_items();

			if ( ! empty( $bundled_items ) ) {
				foreach ( $bundled_items as $bundled_item ) {
					$pricing_data[ $bundled_item->get_id() ][ 'price' ] = $bundled_item->get_price();
					$quantities[ 'min' ][ $bundled_item->get_id() ] = $bundled_item->get_quantity( 'min', array( 'check_optional' => true ) );
					$quantities[ 'max' ][ $bundled_item->get_id() ] = $bundled_item->get_quantity( 'max' );
				}
			}

			if ( ! empty( $pricing_data ) ) {

				$min_qty = $bundle->get_meta( '_wcpb_min_qty_limit', true );

				// Slots filled due to item min quantities.
				$filled_slots = 0;

				foreach ( $quantities[ 'min' ] as $item_min_qty ) {
					$filled_slots += $item_min_qty;
				}

				// Fill in the remaining box slots with cheapest combination of items.
				if ( $filled_slots < $min_qty ) {

					// Sort by cheapest.
					uasort( $pricing_data, array( __CLASS__, 'sort_by_price' ) );

					// Fill additional slots.
					foreach ( $pricing_data as $bundled_item_id => $data ) {

						$slots_to_fill = $min_qty - $filled_slots;

						if ( $filled_slots >= $min_qty ) {
							break;
						}

						$bundled_item = $bundled_items[ $bundled_item_id ];

						if ( false === $bundled_item->is_purchasable() ) {
							continue;
						}

						$max_items_to_use = $quantities[ 'max' ][ $bundled_item_id ];
						$min_items_to_use = $quantities[ 'min' ][ $bundled_item_id ];

						$items_to_use = '' !== $max_items_to_use ? min( $max_items_to_use - $min_items_to_use, $slots_to_fill ) : $slots_to_fill;

						$filled_slots += $items_to_use;

						$quantities[ 'min' ][ $bundled_item_id ] += $items_to_use;
					}
				}
			}

			$result = $quantities[ 'min' ];
			WC_PB_Helpers::cache_set( 'min_price_quantities_' . $bundle->get_id(), $result );
		}

		return $result;
	}

	/**
	 * Find the worst-price set of bundled item quantities that meet the max item count constraint while honoring the initial min/max item quantity constraints.
	 *
	 * @param  WC_Product  $product
	 * @return array
	 */
	public static function get_max_price_quantities( $bundle ) {

		$result = WC_PB_Helpers::cache_get( 'max_price_quantities_' . $bundle->get_id() );

		/*
		 * Max items count defined: Put the min quantities in the box, then keep adding items giving preference to the most expensive ones, while honoring their max quantity constraints.
		 */
		if ( is_null( $result ) ) {

			$quantities = array(
				'min' => array(),
				'max' => array()
			);

			$pricing_data  = array();
			$bundled_items = $bundle->get_bundled_items();

			if ( ! empty( $bundled_items ) ) {
				foreach ( $bundled_items as $bundled_item ) {
					$pricing_data[ $bundled_item->get_id() ][ 'price' ] = $bundled_item->get_price();
					$quantities[ 'min' ][ $bundled_item->get_id() ]     = $bundled_item->get_quantity( 'min', array( 'check_optional' => true ) );
					$quantities[ 'max' ][ $bundled_item->get_id() ]     = $bundled_item->get_quantity( 'max' );
				}
			}

			$max_qty = $bundle->get_meta( '_wcpb_max_qty_limit', true );

			if ( ! empty( $pricing_data ) ) {

				// Sort by most expensive.
				uasort( $pricing_data, array( __CLASS__, 'sort_by_price' ) );
				$reverse_pricing_data = array_reverse( $pricing_data, true );

				// Slots filled due to item min quantities.
				$filled_slots = 0;

				foreach ( $quantities[ 'min' ] as $item_min_qty ) {
					$filled_slots += $item_min_qty;
				}
			}

			// Fill in the remaining box slots with most expensive combination of items.
			if ( $filled_slots < $max_qty ) {

				// Fill additional slots.
				foreach ( $reverse_pricing_data as $bundled_item_id => $data ) {

					$slots_to_fill = $max_qty - $filled_slots;


					if ( $filled_slots >= $max_qty ) {
						$quantities[ 'max' ][ $bundled_item_id ] = $quantities[ 'min' ][ $bundled_item_id ];
						continue;
					}

					$bundled_item = $bundled_items[ $bundled_item_id ];

					if ( false === $bundled_item->is_purchasable() ) {
						continue;
					}

					$max_items_to_use = $quantities[ 'max' ][ $bundled_item_id ];
					$min_items_to_use = $quantities[ 'min' ][ $bundled_item_id ];

					$items_to_use = '' !== $max_items_to_use ? min( $max_items_to_use - $min_items_to_use, $slots_to_fill ) : $slots_to_fill;

					$filled_slots += $items_to_use;

					$quantities[ 'max' ][ $bundled_item_id ] = $quantities[ 'min' ][ $bundled_item_id ] + $items_to_use;
				}
			}

			$result = $quantities[ 'max' ];
			WC_PB_Helpers::cache_set( 'max_price_quantities_' . $bundle->get_id(), $result );
		}

		return $result;
	}

	/**
	 * Sort array data by price.
	 *
	 * @param  array $a
	 * @param  array $b
	 * @return -1|0|1
	 */
	public static function sort_by_price( $a, $b ) {

		if ( $a[ 'price' ] == $b[ 'price' ] ) {
			return 0;
		}

		return ( $a[ 'price' ] < $b[ 'price' ] ) ? -1 : 1;
	}

	/*
	|--------------------------------------------------------------------------
	| Filter hooks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Filter bundled item min quantities used in sync/price context.
	 *
	 * @param  int              $qty
	 * @param  WC_Bundled_Item  $bundled_item
	 * @param  array            $args
	 * @return int
	 */
	public static function bundled_item_quantity( $qty, $bundled_item, $args = array() ) {

		if ( isset( $args[ 'context' ] ) && in_array( $args[ 'context' ], array( 'sync', 'price' ) ) ) {

			$bundle  = $bundled_item->get_bundle();
			$min_qty = $bundle ? $bundle->get_meta( '_wcpb_min_qty_limit', true ) : '';

			if ( $min_qty ) {

				if ( 'sync' === $args[ 'context' ] ) {
					$quantities = self::get_min_required_quantities( $bundle );
				} elseif ( 'price' === $args[ 'context' ] ) {
					$quantities = self::get_min_price_quantities( $bundle );
				}

				if ( isset( $quantities[ $bundled_item->get_id() ] ) ) {
					$qty = $quantities[ $bundled_item->get_id() ];
				}
			}
		}

		return $qty;
	}

	/**
	 * Filter bundled item max quantities used in sync/price context.
	 *
	 * @param  int              $qty
	 * @param  WC_Bundled_Item  $bundled_item
	 * @param  array            $args
	 * @return int
	 */
	public static function bundled_item_quantity_max( $qty, $bundled_item, $args = array() ) {

		if ( isset( $args[ 'context' ] ) && in_array( $args[ 'context' ], array( 'sync', 'price' ) ) ) {

			$bundle  = $bundled_item->get_bundle();
			$min_qty = $bundle ? $bundle->get_meta( '_wcpb_min_qty_limit', true ) : '';

			if ( $min_qty ) {

				if ( 'price' === $args[ 'context' ] ) {
					$quantities = self::get_max_price_quantities( $bundle );
				}

				if ( isset( $quantities[ $bundled_item->get_id() ] ) ) {
					$qty = $quantities[ $bundled_item->get_id() ];
				}
			}
		}

		return $qty;
	}

	/**
	 * When min/max qty constraints are present, require input.
	 *
	 * @param  bool               $requires_input
	 * @param  WC_Product_Bundle  $bundle
	 */
	public static function min_max_bundle_requires_input( $requires_input, $bundle ) {

		$min_qty = $bundle->get_meta( '_wcpb_min_qty_limit', true );
		$max_qty = $bundle->get_meta( '_wcpb_max_qty_limit', true );

		if ( $min_qty || $max_qty ) {
			$requires_input = true;
		}

		return $requires_input;
	}
}

WC_PB_MMI_Product::init();
