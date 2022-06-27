<?php
/**
 * WC_PB_MMI_Cart class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cart-related functions and filters.
 *
 * @class    WC_PB_MMI_Cart
 * @version  6.4.0
 */
class WC_PB_MMI_Cart {

	/**
	 * Setup hooks.
	 */
	public static function init() {

		// Add-to-Cart validation.
		add_action( 'woocommerce_add_to_cart_bundle_validation', array( __CLASS__, 'add_to_cart_validation' ), 10, 4 );

		// Cart validation.
		add_action( 'woocommerce_cart_bundle_validation', array( __CLASS__, 'cart_validation' ), 10, 4 );
	}

	/*
	|--------------------------------------------------------------------------
	| Filter hooks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Add-to-Cart validation.
	 *
	 * @param  bool                 $is_valid
	 * @param  int                  $bundle_id
	 * @param  WC_PB_Stock_Manager  $stock_data
	 * @param  array                $configuration
	 * @return boolean
	 */
	public static function add_to_cart_validation( $is_valid, $bundle_id, $stock_data, $configuration = array() ) {

		if ( $is_valid ) {

			$bundle         = $stock_data->product;
			$items_min      = $bundle->get_min_bundle_size();
			$items_max      = $bundle->get_max_bundle_size();
			$items          = $stock_data->get_items();
			$items_selected = 0;

			foreach ( $items as $item ) {
				$item_id         = isset( $item->bundled_item ) && $item->bundled_item ? $item->bundled_item->item_id : false;
				$item_qty        = $item_id && isset( $configuration[ $item_id ] ) && isset( $configuration[ $item_id ][ 'quantity' ] ) ? $configuration[ $item_id ][ 'quantity' ] : $item->quantity;
				$items_selected += $item_qty;
			}

			$items_invalid = false;

			if ( $items_min !== '' && $items_selected < $items_min ) {
				$items_invalid = true;
			} else if ( $items_max !== '' && $items_selected > $items_max ) {
				$items_invalid = true;
			}

			if ( $items_invalid ) {

				$bundle_title = $bundle->get_title();
				/* translators: Product name */
				$action       = sprintf( __( '&quot;%s&quot; cannot be added to the cart', 'woocommerce-product-bundles' ), $bundle_title );
				$status       = '';

				if ( $items_min === $items_max ) {
					/* translators: Item count */
					$resolution = sprintf( _n( 'please choose %s item', 'please choose %s items', $items_min, 'woocommerce-product-bundles' ), $items_min );
				} elseif ( $items_selected < $items_min ) {
					/* translators: Item count */
					$resolution = sprintf( _n( 'please choose at least %s item', 'please choose at least %s items', $items_min, 'woocommerce-product-bundles' ), $items_min );
				} else {
					/* translators: Item count */
					$resolution = sprintf( _n( 'please limit your selection to %s item', 'please choose up to %s items', $items_max, 'woocommerce-product-bundles' ), $items_max );
				}

				if ( $items_selected === 1 ) {
					$status = __( ' (you have chosen 1)', 'woocommerce-product-bundles' );
				} elseif ( $items_selected > 1 ) {
					/* translators: Item count */
					$status = sprintf( __( ' (you have chosen %s)', 'woocommerce-product-bundles' ), $items_selected );
				}

				/* translators: %1$s: Action, %2$s: Resolution, %3$s: Status */
				$message = sprintf( _x( '%1$s &ndash; %2$s%3$s.', 'add-to-cart validation error: action, resolution, status', 'woocommerce-product-bundles' ), $action, $resolution, $status );

				wc_add_notice( $message, 'error' );

				$is_valid = false;
			}
		}

		return $is_valid;
	}

	/**
	 * Cart validation.
	 *
	 * @throws Exception
	 *
	 * @param  bool                 $is_valid
	 * @param  int                  $bundle_id
	 * @param  WC_PB_Stock_Manager  $stock_data
	 * @param  array                $configuration
	 * @return boolean
	 */
	public static function cart_validation( $is_valid, $bundle_id, $stock_data, $configuration = array() ) {

		if ( $is_valid ) {

			$bundle         = $stock_data->product;
			$items_min      = $bundle->get_min_bundle_size();
			$items_max      = $bundle->get_max_bundle_size();
			$items_selected = 0;

			if ( $configuration ) {
				foreach ( $configuration as $item_id => $item_configuration ) {
					$item_qty   = isset( $item_configuration[ 'quantity' ] ) ? $item_configuration[ 'quantity' ] : 0;
					$items_selected += $item_qty;
				}
			}

			$items_invalid = false;

			if ( $items_min !== '' && $items_selected < $items_min ) {
				$items_invalid = true;
			} else if ( $items_max !== '' && $items_selected > $items_max ) {
				$items_invalid = true;
			}

			if ( $items_invalid ) {

				$bundle_title = $bundle->get_title();
				/* translators: Product title */
				$action       = sprintf( __( '&quot;%s&quot; cannot be purchased', 'woocommerce-product-bundles' ), $bundle_title );

				if ( $items_min === $items_max ) {
					/* translators: Item count */
					$resolution = sprintf( _n( 'please choose %s item', 'please choose %s items', $items_min, 'woocommerce-product-bundles' ), $items_min );
				} elseif ( $items_selected < $items_min ) {
					/* translators: Item count */
					$resolution = sprintf( _n( 'please choose at least %s item', 'please choose at least %s items', $items_min, 'woocommerce-product-bundles' ), $items_min );
				} else {
					/* translators: Item count */
					$resolution = sprintf( _n( 'please limit your selection to %s item', 'please choose up to %s items', $items_max, 'woocommerce-product-bundles' ), $items_max );
				}

				/* translators: %1$s: Action, %2$s: Resolution */
				$message = sprintf( _x( '%1$s &ndash; %2$s.', 'cart validation error: action, resolution', 'woocommerce-product-bundles' ), $action, $resolution );

				throw new Exception( $message );
			}
		}

		return $is_valid;
	}
}

WC_PB_MMI_Cart::init();
