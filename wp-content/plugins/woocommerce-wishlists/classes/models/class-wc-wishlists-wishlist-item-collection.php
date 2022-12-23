<?php

class WC_Wishlists_Wishlist_Item_Collection {
	private $id;

	public function __construct( $id ) {
		$this->id = $id;
	}


	public static function get_items( $id, $update_cached_price = false ) {

		if ( version_compare( WC()->version, '2.7.0', 'ge' ) ) {
			//If there is a list created with the beta versions of WC 3.0 it will cause a deserilization error.
			//Nothing we can do about it except ignore the error.
			try {
				$items = get_post_meta( $id, '_wishlist_items', true );
				if ( !empty( $items ) ) {
					$wishlist_items = maybe_unserialize( $items );
				} else {
					$wishlist_items = array();
				}
			} catch ( Exception $ex ) {
				$items = array();
			}
		} else {
			$items          = get_post_meta( $id, '_wishlist_items', true );
			$wishlist_items = !empty( $items ) ? maybe_unserialize( $items ) : array();
		}

		$contents = array();

		if ( $update_cached_price ) {
			self::update_cached_prices( $id );
		}


		foreach ( $wishlist_items as $key => $values ) {
			$id       = empty( $values['variation_id'] ) ? $values['product_id'] : $values['variation_id'];
			$_product = wc_get_product( $id );

			if ( $_product && $_product->exists() && $values['quantity'] > 0 ) {
				// Put session data into array. Run through filter so other plugins can load their own session data
				$cart_item_data = array(
					'product_id'    => $values['product_id'],
					'variation_id'  => $values['variation_id'],
					'variation'     => $values['variation'],
					'quantity'      => $values['quantity'],
					'data'          => $_product,
					'wl_price'      => isset( $values['wl_price'] ) ? $values['wl_price'] : false,
					'date'          => isset( $values['date'] ) ? $values['date'] : strtotime( 'now' ),
					'wl_date'       => isset( $values['wl_date'] ) ? $values['wl_date'] : strtotime( 'now' ),
					'ordered_total' => isset( $values['ordered_total'] ) ? $values['ordered_total'] : 0,
					'orders'        => isset( $values['orders'] ) ? $values['orders'] : array(),
				);


				$cart_item = apply_filters( 'woocommerce_get_cart_item_from_session', $cart_item_data, $values, $key );

				if ( class_exists( 'WC_Deposits_Cart_Manager' ) ) {
					$deposits  = WC_Deposits_Cart_Manager::get_instance();
					$cart_item = $deposits->get_cart_item_from_session( $cart_item, $values, $key );
				}

				$contents[ $key ] = $cart_item;

			}
		}

		return apply_filters( 'wc_wishlists_item_collection_get_items', $contents, $id, $update_cached_price );
	}

	public static function get_first_image( $id, $size = 'full' ) {
		$wishlist_items = self::get_items( $id );

		$result = false;
		foreach ( $wishlist_items as $item ) {
			$_product = wc_get_product( empty( $item['variation_id'] ) ? $item['product_id'] : $item['variation_id'] );
			if ( $_product->exists() ) {

				if ( has_post_thumbnail( $_product->get_id() ) ) {
					$image = wp_get_attachment_image_src( get_post_thumbnail_id( $_product->get_id() ), $size );
				} elseif ( ( $parent_id = wp_get_post_parent_id( $_product->get_id() ) ) && has_post_thumbnail( $parent_id ) ) {
					$image = wp_get_attachment_image_src( get_post_thumbnail_id( $parent_id ), $size );
				} else {
					$image = false;
				}

				if ( $image ) {
					$result = $image[0];
					break;
				}
			}
		}

		return $result;
	}

	public static function get_items_from_session() {
		$contents = array();
		if ( WC_Wishlist_Compatibility::WC()->session ) {
			$items          = WC_Wishlist_Compatibility::WC()->session->get( '_wishlist_items' );
			$wishlist_items = !empty( $items ) ? maybe_unserialize( $items ) : array();

			foreach ( $wishlist_items as $key => $values ) {
				if ( !isset( $values['variation_id'] ) && !isset( $values['product_id'] ) ) {
					continue;
				}

				$_product = wc_get_product( $values['variation_id'] ? $values['variation_id'] : $values['product_id'] );

				if ($_product && $_product->exists() && $values['quantity'] > 0 ) {

					// Put session data into array. Run through filter so other plugins can load their own session data
					$contents[ $key ] = apply_filters( 'woocommerce_get_cart_item_from_session', array(
						'product_id'    => $values['product_id'],
						'variation_id'  => $values['variation_id'],
						'variation'     => $values['variation'],
						'quantity'      => $values['quantity'],
						'data'          => $_product,
						'date'          => isset( $values['date'] ) ? $values['date'] : strtotime( 'now' ),
						'ordered_total' => isset( $values['ordered_total'] ) ? $values['ordered_total'] : 0,
						'orders'        => isset( $values['orders'] ) ? $values['orders'] : array(),
					), $values, $key );
				}
			}
		}

		return $contents;
	}

	public static function get_items_categories( $id ) {
		$items = self::get_items( $id );
		$cats  = array();
		$ids   = array();
		foreach ( $items as $item ) {
			$item_cats = wp_get_object_terms( $item['product_id'], 'product_cat' );
			if ( $item_cats && !is_wp_error( $item_cats ) ) {
				foreach ( $item_cats as $item_cat ) {
					if ( !array_key_exists( $item_cat->term_id, $cats ) ) {
						$cats[ $item_cat->term_id ] = $item_cat;
					}
				}
			}
		}

		return $cats;
	}

	public static function move_item( $source, $destination, $wishlist_item_key ) {

		$source_items      = self::get_items( $source );
		$destination_items = self::get_items( $destination );

		$the_item = isset( $source_items[ $wishlist_item_key ] ) ? $source_items[ $wishlist_item_key ] : false;
		if ( !$the_item ) {
			return false;
		}

		if ( isset( $destination_items[ $wishlist_item_key ] ) ) {
			$destination_items[ $wishlist_item_key ]['quantity'] = (int) $destination_items[ $wishlist_item_key ]['quantity'] + (int) $source_items[ $wishlist_item_key ]['quantity'];
		} else {
			$destination_items[ $wishlist_item_key ] = $source_items[ $wishlist_item_key ];
		}

		self::update_list_items( $destination, $destination_items );
		do_action( 'wc_wishlists_wishlist_items_updated,', $source );

		self::remove_item( $source, $wishlist_item_key );

		return true;
	}

	public static function move_item_to_session( $source, $wishlist_item_key ) {
		$source_items      = self::get_items( $source );
		$destination_items = WC_Wishlist_Compatibility::WC()->session->get( '_wishlist_items', array() );

		$the_item = isset( $source_items[ $wishlist_item_key ] ) ? $source_items[ $wishlist_item_key ] : false;
		if ( !$the_item ) {
			return false;
		}

		if ( isset( $destination_items[ $wishlist_item_key ] ) ) {
			// $destination_items[$wishlist_item_key]['quantity'] = (int) $destination_items[$wishlist_item_key]['quantity'] + (int) $source_items[$wishlist_item_key]['quantity'];
		} else {
			$destination_items[ $wishlist_item_key ] = $source_items[ $wishlist_item_key ];
		}

		WC_Wishlist_Compatibility::WC()->session->set( '_wishlist_items', $destination_items );

		return true;
	}

	public static function move_item_to_list_from_session( $destination, $wishlist_item_key ) {
		$source_items      = WC_Wishlist_Compatibility::WC()->session->get( '_wishlist_items', array() );
		$destination_items = self::get_items( $destination );

		$the_item = isset( $source_items[ $wishlist_item_key ] ) ? $source_items[ $wishlist_item_key ] : false;
		if ( !$the_item ) {
			return 0;
		}

		if ( isset( $destination_items[ $wishlist_item_key ] ) ) {
			$destination_items[ $wishlist_item_key ]['quantity'] = (int) $destination_items[ $wishlist_item_key ]['quantity'] + (int) $source_items[ $wishlist_item_key ]['quantity'];
		} else {
			$destination_items[ $wishlist_item_key ] = $source_items[ $wishlist_item_key ];
		}

		self::update_list_items( $destination, $destination_items );
		do_action( 'wc_wishlists_wishlist_items_updated,', $destination );
		self::remove_item_from_session( $wishlist_item_key );

		return 1;
	}

	public static function remove_item( $wishlist_id, $wishlist_item_key ) {
		$wishlist_items = self::get_items( $wishlist_id );
		unset( $wishlist_items[ $wishlist_item_key ] );
		self::update_list_items( $wishlist_id, $wishlist_items );
		do_action( 'wc_wishlists_wishlist_items_updated,', $wishlist_id );

		return true;
	}

	public static function remove_item_from_session( $wishlist_item_key ) {
		$items = WC_Wishlist_Compatibility::WC()->session->get( '_wishlist_items', array() );

		if ( isset( $items[ $wishlist_item_key ] ) ) {
			unset( $items[ $wishlist_item_key ] );
		}

		$valid = 0;
		foreach ( $items as $item_key => $item ) {
			if ( $item_key ) {
				$valid ++;
			}
		}

		if ( $valid == 0 ) {
			WC_Wishlist_Compatibility::WC()->session->set( '_wishlist_items', null );
		} else {
			WC_Wishlist_Compatibility::WC()->session->set( '_wishlist_items', $items );
		}

		return true;
	}

	public static function add_item( $wishlist_id, $product_id, $quantity = 1, $variation_id = '', $variation = '', $cart_item_data = array() ) {
		global $wpdb;

		if ( $wishlist_id != 'session' ) {
			$wishlist = new WC_Wishlists_Wishlist( $wishlist_id );
			if ( !$wishlist->post ) {
				WC_Wishlist_Compatibility::wc_add_notice( __( 'List could not be located', 'wc_wishlist' ), 'error' );

				return false;
			}
			$wishlist_items = self::get_items( $wishlist_id );
		} elseif ( $wishlist_id == 'session' ) {
			$wishlist_items = self::get_items_from_session();
		}

		//Let's make sure we have a valid product first.
		$product_data = wc_get_product( $variation_id > 0 ? $variation_id : $product_id );
		if ( empty( $product_data ) ) {
			return false;
		}


		// Load cart item data - may be added by other plugins

		$cart_item_data = (array) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id, $quantity );
		$cart_item_data = array_merge( $cart_item_data, apply_filters( 'woocommerce_add_wishlist_item_data', $cart_item_data, $product_id, $wishlist_id ) );

		// Generate a ID based on product ID, variation ID, variation data, and other cart item data
		$cart_id = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );

		if ( $quantity < 1 ) {
			if ( $wishlist_items && is_array( $wishlist_items ) && isset( $wishlist_items[ $cart_id ] ) ) {
				unset( $wishlist_items[ $cart_id ] );
				WC_Wishlist_Compatibility::wc_add_notice( __( 'Item has been removed from the list.', 'wc_wishlist' ) );

				return true;
			}
		}


		// See if this product and its options is already in the wishlist
		$cart_item_key = false;
		if ( $wishlist_items && is_array( $wishlist_items ) ) {
			foreach ( $wishlist_items as $wishlist_item_key => $item ) {
				if ( $wishlist_item_key == $cart_id ) {
					$cart_item_key = $wishlist_item_key;
					break;
				}
			}
		}


		// If cart_item_key is set, the item is already in the list
		if ( $cart_item_key ) {
			$new_quantity                                 = $quantity + $wishlist_items[ $cart_item_key ]['quantity'];
			$wishlist_items[ $cart_item_key ]['quantity'] = $new_quantity;
			$wishlist_items[ $cart_item_key ]['wl_price'] = wc_get_price_excluding_tax( $product_data );
			$wishlist_items[ $cart_item_key ]['wl_stock_status'] = $product_data->is_in_stock();
		} else {
			$cart_item_key = $cart_id;

			// Add item after merging with $cart_item_data - hook to allow plugins to modify cart item
			$wishlist_item = apply_filters( 'woocommerce_add_cart_item', array_merge( $cart_item_data, array(
				'product_id'    => $product_id,
				'variation_id'  => $variation_id,
				'variation'     => $variation,
				'quantity'      => $quantity,
				'data'          => $product_data,
				'wl_date'       => strtotime( 'now' ),
				'ordered_total' => 0,
				'orders'        => array(),
			) ), $cart_item_key );

			$wishlist_item['wl_price'] = wc_get_price_excluding_tax( $product_data );
			$wishlist_item['wl_stock_status'] = $product_data->is_in_stock();
			$wishlist_item = apply_filters( 'woocommerce_add_wishlist_item', $wishlist_item, $cart_item_key );

			//Unset serialized product data from wishlist item.
			$wishlist_item['data'] = null;
			unset( $wishlist_item['data'] );

			$wishlist_items[ $cart_item_key ] = $wishlist_item;
		}

		do_action( 'woocommerce_wishlist_add_item', $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data, $wishlist_id );


		if ( $wishlist_id == 'session' ) {
			WC_Wishlist_Compatibility::WC()->session->set( '_wishlist_items', $wishlist_items );
		} else {

			self::update_list_items( $wishlist_id, $wishlist_items );
			do_action( 'wc_wishlists_wishlist_items_updated', $wishlist_id );
		}


		return true;
	}

	public static function update_item_quantity( $wishlist_id, $wishlist_item_key, $quantity ) {
		$wishlist_items = self::get_items( $wishlist_id );
		if ( isset( $wishlist_items[ $wishlist_item_key ] ) ) {
			$wishlist_items[ $wishlist_item_key ]['quantity'] = $quantity;
		}

		self::update_list_items( $wishlist_id, $wishlist_items );
		do_action( 'wc_wishlists_wishlist_items_updated', $wishlist_id );

		return true;
	}

	public static function update_item_ordered_quantity( $wishlist_id, $wishlist_item_key, $quantity ) {
		$wishlist_items = self::get_items( $wishlist_id );
		if ( isset( $wishlist_items[ $wishlist_item_key ] ) ) {
			$wishlist_items[ $wishlist_item_key ]['ordered_total'] = $quantity;
		}

		self::update_list_items( $wishlist_id, $wishlist_items );
		do_action( 'wc_wishlists_wishlist_items_updated', $wishlist_id );

		return true;
	}

	/**
	 * Tracks the number of times an item has been ordered.
	 *
	 * @param $wishlist_id
	 * @param $wishlist_item_key
	 * @param $order WC_Order
	 * @param $order_item WC_Order_Item
	 */
	public static function update_item_ordered( $wishlist_id, $wishlist_item_key, $order, $order_item ) {
		$wishlist_items = self::get_items( $wishlist_id );
		if ( isset( $wishlist_items[ $wishlist_item_key ] ) ) {
			if ( !isset( $wishlist_items[ $wishlist_item_key ]['orders'] ) ) {
				$wishlist_items[ $wishlist_item_key ]['orders'] = array();
			}

			$current_ordered_quantity = isset( $wishlist_items[ $wishlist_item_key ]['orders'][ $order->get_id() ] ) ? $wishlist_items[ $wishlist_item_key ]['orders'][ $order->get_id() ]['quantity'] : 0;

			$wishlist_items[ $wishlist_item_key ]['orders'][ $order->get_id() ] = array(
				'quantity' => $order_item->get_quantity() + $current_ordered_quantity,
			);

			$wishlist_items[ $wishlist_item_key ]['ordered_total'] = isset( $wishlist_items[ $wishlist_item_key ]['ordered_total'] ) ? $wishlist_items[ $wishlist_item_key ]['ordered_total'] + $order_item->get_quantity() : $order_item->get_quantity();

			do_action( 'wc_wishlists_wishlist_item_ordered', $order_item, $order, $wishlist_item_key, $wishlist_id );

		}

		self::update_list_items( $wishlist_id, $wishlist_items );
		do_action( 'wc_wishlists_wishlist_items_updated', $wishlist_id );

		return true;
	}

	public static function update_cached_prices( $wishlist_id ) {

		$wishlist_items = self::get_items( $wishlist_id );
		if ( $wishlist_items ) {
			foreach ( $wishlist_items as &$values ) {
				$_product = wc_get_product( $values['variation_id'] ? $values['variation_id'] : $values['product_id'] );

				if ( $_product->exists() ) {
					$values['wl_price'] = wc_get_price_excluding_tax( $_product );
				}

				//Unset serialized product data from wishlist item.
				$values['data'] = null;
				unset( $values['data'] );
			}


			//Unset serialized product data from wishlist item.

			self::update_list_items( $wishlist_id, $wishlist_items );
			do_action( 'wc_wishlists_wishlist_items_updated', $wishlist_id );

			return true;
		}
	}


	public static function update_list_items( $wishlist_id, $wishlist_items ) {

		foreach ( $wishlist_items as &$item ) {
			$item['data'] = null;
			unset( $item['data'] );
		}

		update_post_meta( $wishlist_id, '_wishlist_items', $wishlist_items );
	}

}
