<?php
/**
 * WC_MNM_Order_Again class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Mix and Match
 * @since    1.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Order-again functions and filters.
 *
 * @class    WC_MNM_Order_Again
 * @version  1.7.0
 */
class WC_MNM_Order_Again {

	/*
	 * Initilize.
	 */
	public static function init() {

		// Put back cart item data to allow re-ordering of container.
		add_filter( 'woocommerce_order_again_cart_item_data', array( __CLASS__, 'order_again_cart_item_data' ), 10, 3 );

		if ( WC_MNM_Core_Compatibility::is_wc_version_gte( '3.5' ) ) {

			// Initialize parent-child associations from order-again keys.
			add_filter( 'woocommerce_get_cart_item_from_session', array( __CLASS__, 'get_cart_item_from_session' ), -100, 3 );

			// Finalize parent-child associations from order-again keys.
			add_action( 'woocommerce_cart_loaded_from_session', array( __CLASS__, 'cart_loaded_from_session' ), -100 );

		}
	
	}

	/*
	|--------------------------------------------------------------------------
	| Filter hooks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Reinitialize cart item data for re-ordering purchased orders.
	 *
	 * @param  mixed     $cart_item
	 * @param  mixed     $order_item
	 * @param  WC_Order  $order
	 * @return mixed
	 */
	public static function order_again_cart_item_data( $cart_item, $order_item, $order ) {

		// Add data to container.
		if ( wc_mnm_is_container_order_item( $order_item ) ) {

			if ( ! $order_item->meta_exists( '_mnm_config' ) ) {
				return $cart_item;
			}

			$cart_item[ 'mnm_config' ]   = $order_item->get_meta( '_mnm_config', true );
			$cart_item[ 'mnm_contents' ] = array();

			if ( ! WC_Mix_and_Match()->cart->is_cart_session_loaded() ) {

				$cart_id = $order_item->get_meta( '_mnm_cart_key', true );

				if ( ! empty( $cart_id ) ) {
					$cart_item[ 'order_again_mnm_cart_key' ] = $cart_id;
				}

			}

			// Will be added by parent.
		} elseif ( wc_mnm_maybe_is_child_order_item( $order_item ) ) {

			if ( WC_Mix_and_Match()->cart->is_cart_session_loaded() ) {

				$mnm_item_id   = $order_item[ 'variation_id' ] > 0 ? $order_item[ 'variation_id' ] : $order_item[ 'product_id' ];
				$modified_cart = false;

				// Copy all cart data of the "orphaned" child cart item into the one already added by the container on 'woocommerce_add_to_cart'.
				foreach ( WC()->cart->cart_contents as $check_cart_item_key => $check_cart_item_data ) {

					if ( ! wc_mnm_maybe_is_child_cart_item( $check_cart_item_data ) ) {
						continue;
					}

					$check_mnm_item_id = $check_cart_item_data[ 'variation_id' ] > 0 ? $check_cart_item_data[ 'variation_id' ] : $check_cart_item_data[ 'product_id' ];

					if ( absint( $mnm_item_id ) !== absint( $check_mnm_item_id ) ) {
						continue;
					}

					$existing_child_cart_item     = $check_cart_item_data;
					$existing_child_cart_item_key = $check_cart_item_key;

					foreach ( $cart_item as $key => $value ) {
						if ( ! isset( $existing_child_cart_item[ $key ] ) ) {
							WC()->cart->cart_contents[ $existing_child_cart_item_key ][ $key ] = $value;
							$modified_cart = true;
						}
					}
				}

				// Cart data changed? Recalculate totals and set session.
				if ( $modified_cart ) {
					WC()->cart->calculate_totals();
				}

				// Identify this as a cart item that is originally part of a container. Will be removed since it has already been added to the cart by its container.
				$cart_item[ 'is_order_again_mnm_item' ] = 'yes';

			} else {

				$container_item = $order_item->get_meta( '_mnm_container', true );

				if ( ! empty( $container_item ) ) {
					$cart_item[ 'order_again_mnm_container' ] = $container_item;
				}
			}
		}

		return $cart_item;

	}


	/**
	 * Initialize parent-child associations from order-again keys.
	 *
	 * @since  1.4.0
	 *
	 * @param  array   $cart_item
	 * @param  array   $cart_session_item
	 * @param  string  $key
	 */
	public static function get_cart_item_from_session( $cart_item, $cart_session_item, $key ) {

		if ( ! did_action( 'woocommerce_ordered_again' ) ) {
			return $cart_item;
		}

		// Add reference to parent key in child.
		if ( ! empty( $cart_item[ 'order_again_mnm_container' ] ) ) {
			
			// Always add this key so that WC_Mix_and_Match_Cart::cart_loaded_from_session() will clean up orphaned child items when parent cannot be ordered again.
			$cart_item[ 'mnm_container' ] = '';

			foreach ( WC()->cart->cart_contents as $search_container_item_key => $search_container_item ) {

				if ( empty( $search_container_item[ 'order_again_mnm_cart_key' ] ) ) {
					continue;
				}

				if ( $cart_session_item[ 'order_again_mnm_container' ] === $search_container_item[ 'order_again_mnm_cart_key' ] ) {
					// Add reference to parent key in child.
					$cart_item[ 'mnm_container' ] = $search_container_item_key;
					// Break the search.
					break;
				}
			}

			// Clean up.
			unset( $cart_item[ 'order_again_mnm_container' ] );
		}

		return $cart_item;
	}


	/**
	 * Finalize parent-child associations from order-again keys.
	 *
	 * @since  1.4.0
	 *
	 * @param  WC_Cart  $cart
	 * @return void
	 */
	public static function cart_loaded_from_session( $cart ) {

		if ( ! did_action( 'woocommerce_ordered_again' ) ) {
			return;
		}

		if ( empty( $cart->cart_contents ) ) {
			return;
		}

		foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {

			if ( wc_mnm_is_container_cart_item( $cart_item ) ) {

				foreach ( $cart->cart_contents as $search_child_key => $search_child_item ) {

					if ( ! wc_mnm_maybe_is_child_cart_item( $search_child_item ) ) {
						continue;
					}

					if ( $search_child_item[ 'mnm_container' ] === $cart_item_key ) {

						// Add reference to child key in parent item.
						WC()->cart->cart_contents[ $cart_item_key ][ 'mnm_contents' ][] = $search_child_key;
						// Invalidate session data.
						WC()->session->set( 'cart_totals', null );
					}
				}

				// Clean up.
				unset( WC()->cart->cart_contents[ $cart_item_key ][ 'order_again_mnm_cart_key' ] );
			}
		}
	}

}
WC_MNM_Order_Again::init();
