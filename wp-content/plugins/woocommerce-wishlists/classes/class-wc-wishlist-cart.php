<?php

/**
 * WC_Wishlists_Cart class.
 */
class WC_Wishlists_Cart {
	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Wishlists_Cart();
		}
	}

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {


		// Load cart data per page load
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 20, 2 );

		// Get item data to display
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );


		// Add meta to order 2.0
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'order_item_meta' ), 10, 3 );

		// Register hidden meta on the orders screen
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'set_hidden_order_item_meta' ) );

		//add_action( 'woocommerce_order_status_changed', array( $this, 'on_order_status_changed' ), 10, 3 );
		add_action( 'woocommerce_order_status_changed', array( $this, 'on_order_completed' ), 11, 3 );
	}

	/**
	 * get_cart_item_from_session function.
	 *
	 * @access public
	 *
	 * @param mixed $cart_item
	 * @param mixed $values
	 *
	 * @return void
	 */
	function get_cart_item_from_session( $cart_item, $values ) {

		if ( ! empty( $values['wishlist-data'] ) ) {
			$cart_item['wishlist-data'] = $values['wishlist-data'];
		}

		return $cart_item;
	}

	/**
	 * get_item_data function.
	 *
	 * @access public
	 *
	 * @param mixed $other_data
	 * @param mixed $cart_item
	 *
	 * @return void
	 */
	function get_item_data( $other_data, $cart_item ) {

		if ( ! empty( $cart_item['wishlist-data'] ) && isset( $cart_item['wishlist-data']['list'] ) ) {
			$wishlist_id = $cart_item['wishlist-data']['list']['value'];

			$other_data[] = array(
				'name'    => $cart_item['wishlist-data']['list']['name'],
				'value'   => $wishlist_id,
				'display' => get_the_title( $wishlist_id )
			);
		}

		return $other_data;
	}

	/**
	 * order_item_meta function.
	 *
	 * @access public
	 *
	 * @param mixed $item_id
	 * @param mixed $values
	 *
	 * @return void
	 */
	public function order_item_meta( $item, $cart_item_key, $values ) {

		if ( function_exists( 'wc_add_order_item_meta' ) ) {

			if ( ! empty( $values['wishlist-data'] ) && isset( $values['wishlist-data']['list'] ) && isset( $values['wishlist-data']['item'] ) && isset( $values['wishlist-data']['customer'] ) ) {

				$list_data     = $values['wishlist-data']['list'];
				$item_data     = $values['wishlist-data']['item'];
				$customer_data = $values['wishlist-data']['customer'];

				$wishlist_id           = $list_data['value'];
				$wishlist_item_id      = $item_data['value'];
				$wishlist_customer_key = $customer_data['value'];

				$name = get_the_title( $wishlist_id );

				$item->add_meta_data( $list_data['name'], $name );
				$item->add_meta_data( '_wishlist_id', $wishlist_id );
				$item->add_meta_data( '_wishlist_item_id', $wishlist_item_id );
				$item->add_meta_data( '_wishlist_customer_key', $wishlist_customer_key );
			}
		}
	}

	public function set_hidden_order_item_meta( $hidden ) {
		return array_merge( $hidden, array( '_wishlist_id', '_wishlist_item_id', '_wishlist_customer_key' ) );
	}



	public function on_order_completed( $order_id, $current_status, $new_status ) {

		$auto_remove_items     = WC_Wishlists_Settings::get_setting( 'wc_wishlist_processing_autoremove', 'no' ) == 'yes';
		$auto_remove_on_status = WC_Wishlists_Settings::get_setting( 'wc_wishlist_processing_autoremove_status', WC_Wishlist_Compatibility::is_wc_version_gte_2_2() ? 'completed' : 'wc-completed' );
		$auto_remove_type      = WC_Wishlists_Settings::get_setting( 'wc_wishlist_processing_autoremove_type', 'owner' );

		if ( ($current_status == 'pending' && $auto_remove_on_status == 'wc-pending') || ( ( $new_status == $auto_remove_on_status ) || ( $new_status == str_replace( 'wc-', '', $auto_remove_on_status ) ) ) ) {
			$order = wc_get_order( $order_id );
			$items = $order->get_items();

			if ( $items ) {
				foreach ( $items as $item_id => $item ) {

					$wishlist_id           = wc_get_order_item_meta( $item_id, '_wishlist_id', true );
					$wishlist_item_id      = wc_get_order_item_meta( $item_id, '_wishlist_item_id', true );
					$wishlist_customer_key = wc_get_order_item_meta( $item, '_wishlist_customer_key', true );


					if ( $wishlist_id && $wishlist_item_id ) {
						$wl_owner = WC_Wishlists_Wishlist::get_the_wishlist_owner( $wishlist_id );

						$process = false;
						if ( $auto_remove_type == 'owner' ) {
							$process = $wl_owner == $wishlist_customer_key || $wl_owner == $order->get_customer_id();
						} elseif ( $auto_remove_type == 'all' ) {
							$process = true;
						}


						if ( $process ) {

							$ordered_quantity = $item['qty'];
							$wishlist_items   = WC_Wishlists_Wishlist_Item_Collection::get_items( $wishlist_id );

							if ( isset( $wishlist_items[ $item['wishlist_item_id'] ] ) ) {

								$wishlist_item = $wishlist_items[ $item['wishlist_item_id'] ];
								if ( $wishlist_item['quantity'] <= $ordered_quantity ) {
									if ( $auto_remove_items ) {
										WC_Wishlists_Wishlist_Item_Collection::remove_item( $wishlist_id, $wishlist_item_id );
									} else {
										WC_Wishlists_Wishlist_Item_Collection::update_item_ordered( $wishlist_id, $wishlist_item_id, $order, $item );
									}

								} else {
									if ( $auto_remove_items ) {
										WC_Wishlists_Wishlist_Item_Collection::update_item_quantity( $wishlist_id, $wishlist_item_id, $wishlist_item['quantity'] - $ordered_quantity );
									}

									WC_Wishlists_Wishlist_Item_Collection::update_item_ordered( $wishlist_id, $wishlist_item_id, $order, $item );
								}
							}
						}
					}
				}
			}
		}
	}

}