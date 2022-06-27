<?php
/**
 * WC_PB_MMI_Product class
 *
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
 * @version  6.15.4
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

		// Make sure the bundled items stock status takes the min bundle size into account.
		add_filter( 'woocommerce_synced_bundled_items_stock_status', array( __CLASS__, 'synced_bundled_items_stock_status' ), 10, 2 );

		// Make sure the bundle stock quantity the min bundle size into account.
		add_filter( 'woocommerce_synced_bundle_stock_quantity', array( __CLASS__, 'synced_bundle_stock_quantity' ), 10, 2 );

		// Make sure the bundle thinks it has 'mandatory' contents when the min bundle size is > 0.
		add_filter( 'woocommerce_bundles_synced_contents_data', array( __CLASS__, 'synced_contents_data' ), 10, 2 );
	}

	/*
	|--------------------------------------------------------------------------
	| Application layer functions.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Indicates if a bundle has min/max size rules in effect.
	 *
	 * @since  6.5.0
	 *
	 * @param  WC_Product_Bundle  $bundle
	 * @return boolean
	 */
	public static function has_limited_bundle_size( $bundle ) {

		$has_limited_bundle_size = false;

		$min_qty = $bundle->get_min_bundle_size();
		$max_qty = $bundle->get_max_bundle_size();

		if ( $min_qty || $max_qty ) {

			if ( $min_qty === $max_qty ) {

				$bundle_size = $min_qty;
				$total_items = 0;

				foreach ( $bundle->get_bundled_items() as $bundled_item ) {

					$item_qty_min = $bundled_item->get_quantity( 'min', array( 'check_optional' => true ) );
					$item_qty_max = $bundled_item->get_quantity( 'max' );

					// If the bundle has configurable quantities, then we have to assume that the bundle size rule is in effect.
					if ( $item_qty_min !== $item_qty_max ) {
						$total_items = 0;
						break;
					}

					$total_items += $item_qty_min;
				}

				// If the bundle doesn't have configurable quantities and its bundle size rule can't be satisfied, activate it to make sure the store owner sees their error.
				if ( absint( $total_items ) !== absint( $bundle_size ) ) {
					$has_limited_bundle_size = true;
				}

			} else {
				$has_limited_bundle_size = true;
			}
		}

		return $has_limited_bundle_size;
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

				$min_qty = $bundle->get_min_bundle_size();;

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

			$max_qty = $bundle->get_max_bundle_size();

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

		if ( isset( $args[ 'context' ] ) && in_array( $args[ 'context' ], array( 'price' ) ) ) {

			$bundle  = $bundled_item->get_bundle();
			$min_qty = $bundle ? WC_PB_Helpers::cache_get( 'min_qty_' . $bundle->get_id() ) : '';

			if ( is_null( $min_qty ) ) {
				$min_qty = $bundle->get_min_bundle_size();
				WC_PB_Helpers::cache_set( 'min_qty_' . $bundle->get_id(), $min_qty );
			}

			if ( $min_qty ) {

				$quantities = self::get_min_price_quantities( $bundle );

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

		if ( isset( $args[ 'context' ] ) && in_array( $args[ 'context' ], array( 'price' ) ) ) {

			$bundle  = $bundled_item->get_bundle();
			$min_qty = $bundle ? WC_PB_Helpers::cache_get( 'min_qty_' . $bundle->get_id() ) : '';

			if ( is_null( $min_qty ) ) {
				$min_qty = $bundle->get_min_bundle_size();
				WC_PB_Helpers::cache_set( 'min_qty_' . $bundle->get_id(), $min_qty );
			}

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
	 * When min/max qty constraints are present and the quantity of items in the bundle can be adjusted, require input.
	 *
	 * @param  bool               $requires_input
	 * @param  WC_Product_Bundle  $bundle
	 */
	public static function min_max_bundle_requires_input( $requires_input, $bundle ) {

		if ( false === $requires_input ) {
			if ( self::has_limited_bundle_size( $bundle ) ) {
				$requires_input = true;
			}
		}

		return $requires_input;
	}

	/**
	 * Makes sure the bundled items stock status takes the min bundle size into account.
	 *
	 * @since  6.5.0
	 *
	 * @param  string             $bundled_items_stock_status
	 * @param  WC_Product_Bundle  $bundle
	 * @return string
	 */
	public static function synced_bundled_items_stock_status( $bundled_items_stock_status, $bundle ) {

		// If already out of stock, exit early.
		if ( 'outofstock' === $bundled_items_stock_status ) {
			return $bundled_items_stock_status;
		}

		$min_bundle_size = $bundle->get_min_bundle_size();

		if ( $min_bundle_size ) {

			// Is it possible to buy this?
 			$stock_available = 0;
 			foreach ( $bundle->get_bundled_data_items( 'edit' ) as $bundled_data_item ) {

 				$item_stock_available = $bundled_data_item->get_meta( 'max_stock' );
 				$item_max_quantity    = $bundled_data_item->get_meta( 'quantity_max' );

 				$item_stock_available = '' === $item_stock_available ? $item_max_quantity : min( $item_stock_available, $item_max_quantity );

				if ( '' === $item_stock_available || is_null( $item_stock_available ) ) {
					$stock_available = '';
					break;
				}

				$stock_available += $item_stock_available;
			}

			if ( '' !== $stock_available && $stock_available < $min_bundle_size ) {
				$bundled_items_stock_status = 'outofstock';
			}
		}

		return $bundled_items_stock_status;
	}

	/**
	 * Makes sure the bundle stock quantity takes the min bundle size into account.
	 *
	 * @since  6.5.0
	 *
	 * @param  string             $bundle_stock_quantity
	 * @param  WC_Product_Bundle  $bundle
	 * @return string
	 */
	public static function synced_bundle_stock_quantity( $bundle_stock_quantity, $bundle ) {

		// If already out of stock, exit early.
		if ( 0 === $bundle_stock_quantity ) {
			return $bundle_stock_quantity;
		}

		$min_bundle_size = $bundle->get_min_bundle_size();

		if ( $min_bundle_size ) {

			$stock_available = 0;
			foreach ( $bundle->get_bundled_data_items( 'edit' ) as $bundled_data_item ) {

				$item_stock_available = $bundled_data_item->get_meta( 'max_stock' );

				if ( '' === $item_stock_available || is_null( $item_stock_available ) ) {
					$stock_available = '';
					break;
				}

				$stock_available += $item_stock_available;
			}

			if ( '' === $stock_available ) {
				return $bundle_stock_quantity;
			}

			$times_purchasable = intval( floor( $stock_available / $min_bundle_size ) );

			if ( '' === $bundle_stock_quantity || $times_purchasable < $bundle_stock_quantity ) {
				$bundle_stock_quantity = $times_purchasable;
			}
		}

		return $bundle_stock_quantity;
	}

	/**
	 * Make sure the bundle thinks it has 'mandatory' contents when the min bundle size is > 0.
	 *
	 * @since  6.5.2
	 *
	 * @param  array              $data
	 * @param  WC_Product_Bundle  $bundle
	 * @return string
	 */
	public static function synced_contents_data( $data, $bundle ) {

		$min_bundle_size = $bundle->get_min_bundle_size();

		if ( $min_bundle_size ) {
			$data[ 'mandatory' ] = true;
		}

		return $data;
	}
}

WC_PB_MMI_Product::init();
