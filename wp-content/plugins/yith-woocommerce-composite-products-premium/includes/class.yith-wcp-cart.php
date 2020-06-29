<?php
/**
 * Frontend class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCP_Cart' ) ) {
	/**
	 * Frontend class.
	 * The class manage the add to cart actions.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCP_Cart {
		
		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct( ) {

			add_action( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );

			// Add to cart
			add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 20, 1 );
			add_filter( 'woocommerce_add_to_cart_validation' , array( $this, 'add_to_cart_validation' ) , 50 , 6 ) ;

			// Load cart data per page load
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 20, 2 );

			// Add child items
			add_action( 'woocommerce_add_to_cart', array( $this, 'add_child_items' ), 10, 6 );

			// Cart Display

			// tr class
			add_filter( 'woocommerce_cart_item_class' , array( $this , 'cart_item_class' ) , 10 , 3 );
			add_filter( 'woocommerce_mini_cart_item_class' , array( $this , 'cart_item_class' ) , 10 , 3 );
			add_filter( 'woocommerce_order_item_class' , array( $this , 'cart_item_class' ) , 10 , 3 );

			// Remove link
			add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'cart_item_remove_link' ), 10, 2 );

			// Remove items from cart
			add_action( 'woocommerce_cart_item_removed', array( $this, 'remove_items_from_cart' ), 10 , 2 );
			add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'remove_items_from_cart' ), 10 , 2 );

			// Restored item
			add_action( 'woocommerce_cart_item_restored', array( $this, 'cart_item_restored' ), 10, 2 );

			// Product Name / Url
			add_filter( 'woocommerce_cart_item_permalink', array( $this, 'cart_item_permalink' ), 10, 3 );
			add_filter( 'woocommerce_cart_item_name', array( $this, 'cart_item_name' ) , 10 , 3 );
			add_filter( 'woocommerce_order_item_name', array( $this, 'order_item_name' ) , 10 , 2 );

			// Price
			add_filter( 'woocommerce_cart_item_price', array( $this, 'cart_item_price' ), 10 ,2 ) ;

			// Quantity
			add_filter( 'woocommerce_cart_item_quantity', array( $this, 'cart_item_quantity' ), 10 ,3 );
			add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'update_quantity_in_cart' ), 20, 4 );
			add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'update_quantity_in_cart' ) );

			// Total
			add_filter( 'woocommerce_cart_item_subtotal' , array( $this, 'cart_item_subtotal' ) ,10 , 3 ) ;
			add_filter( 'woocommerce_order_formatted_line_subtotal' , array( $this, 'order_item_subtotal' ) ,10 , 3 ) ;

			// Order 
			
			// Add meta to order
			// add_action( 'woocommerce_new_order_item', array( $this, 'order_item_meta' ), 10, 3 );
			@ add_action( 'woocommerce_add_order_item_meta', array( $this, 'order_item_meta' ), 10, 3 );
			
			// Order again
			add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 'order_again_cart_item_data' ), 10, 3 );

			// Shipping
			add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'cart_shipping_packages' ), 11 );

			add_filter( 'woocommerce_product_get_price', array( $this, 'check_dynamic_price'), 99, 2 );
		}

		/**
		 * @param $product
		 *
		 * @return mixed
		 */
		private function getCompositeItemMetaData( $product, $post_data ) {

			$component_data['product_base_price'] = yit_get_display_price( $product );
			$component_data['selection_data'] = $post_data['ywcp_selected_product_value'];
			$component_data['selection_variation_data'] = isset( $post_data['ywcp_variation_id'] ) ? $post_data['ywcp_variation_id'] : array();
			$component_data['selection_quantity'] = isset( $post_data['ywcp_quantity'] ) ? $post_data['ywcp_quantity'] : array();

			// will be set after add to cart
			$component_data['cart_item_key'] = '';

			return $component_data;

		}

		/**
		 * @param $product
		 * @param $key
		 * @param $cart_item_key
		 *
		 * @return mixed
		 */
		private function getCompositeChildItemMetaData( $product, $key, $cart_item_key ) {

			$child_meta_data_value['yith_wcp_component_parent_object'] = $product;
			$child_meta_data_value['yith_wcp_component_key']           = $key;
			$child_meta_data_value['yith_wcp_cart_parent_key']         = $cart_item_key;
			
			return $child_meta_data_value;
			
		}

		/**
		 * @param $cart_item_meta
		 * @param $product_id
		 *
		 * @return mixed
		 */
		public function add_cart_item_data( $cart_item_meta, $product_id, $post_data = null  ) {
			if ( is_null( $post_data ) ) { $post_data = $_REQUEST; }
			if ( isset( $post_data['add-to-cart'] ) && $post_data['add-to-cart'] > 0 && $product_id > 0 ) {
				$product = wc_get_product( $product_id );
				if ( $product->get_type() == 'yith-composite' && isset( $post_data['ywcp_selected_product_value'] ) ) {
					$cart_item_meta['yith_wcp_component_data'] = $this->getCompositeItemMetaData( $product, $post_data );
				}
			}
			return $cart_item_meta;
		}

		/**
		 * @param $cart_item
		 *
		 * @return mixed
		 */
		public function add_cart_item( $cart_item ) {

			$product = $cart_item['data'];

			if ( isset( $cart_item['yith_wcp_child_component_data'] ) ) {

				$child_item_meta = $cart_item['yith_wcp_child_component_data'];

				// price.
				if ( $child_item_meta['yith_wcp_component_parent_object']->isPerItemPricing() ) {

					YITH_WCP_Frontend::markProductAsCompositeProcessed(
						$product,
						yit_get_base_product_id( $child_item_meta['yith_wcp_component_parent_object'] ),
						$child_item_meta['yith_wcp_component_key']
					);

				}  else { yit_set_prop( $product, 'price', 0 ); }

				// Shipping.
				if ( $product->needs_shipping() ) {

					// $product->set_virtual(true);

					if ( ! $child_item_meta['yith_wcp_component_parent_object']->isPerItemShipping() ) {

						if ( apply_filters( 'ywcp_woocommerce_composited_product_has_bundled_weight', false, $cart_item[ 'data' ], $child_item_meta['yith_wcp_component_key'], $child_item_meta['yith_wcp_component_parent_object'] ) ) {
							$cart_item[ 'data' ]->ywcp_bundled_weight = $cart_item[ 'data' ]->get_weight();
						}

						$cart_item[ 'data' ]->ywcp_bundled_price = $cart_item[ 'data' ]->get_price();
						$cart_item[ 'data' ]->virtual = 'yes';

					}
				}

			}

			return $cart_item;

		}

		public function add_to_cart_validation( $continue , $product_id, $quantity, $variation_id = 0, $variations = array(), $cart_item_data = array() ) {

			// doesn' t re-add child items(after order again)
			if ( isset( $cart_item_data[ 'yith_wcp_child_component_data_no_reorder' ] ) && $cart_item_data[ 'yith_wcp_child_component_data_no_reorder' ] == 1 ) {
				$continue = false;
			}

			return $continue;
		}

		/**
		 * @param $cart_item
		 * @param $values
		 *
		 * @return mixed
		 */
		public function get_cart_item_from_session( $cart_item, $values ) {

			if ( ! empty( $values['yith_wcp_component_data'] ) ) {

				$cart_item['yith_wcp_component_data'] = $values['yith_wcp_component_data'];
				
			} else if( isset( $values['yith_wcp_child_component_data'] ) ) {

				$cart_item['yith_wcp_child_component_data'] = $values['yith_wcp_child_component_data'];

				$cart_item = $this->add_cart_item( $cart_item );

			}

			return $cart_item;
		}

		/**
		 * Add child Composite Product child items to the cart
		 * 
		 * @param $cart_item_key
		 * @param $product_id
		 * @param $quantity
		 * @param $variation_id
		 * @param $variation
		 * @param $cart_item_data
		 */
		public function add_child_items( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

			$product = wc_get_product( $product_id );

			$add_chid_items = $product->get_type() == 'yith-composite' && isset( $cart_item_data['yith_wcp_component_data']['selection_variation_data'] );

			if ( apply_filters( 'yith_wcp_composite_add_child_items', $add_chid_items, $cart_item_data ) ) {

				if ( isset( $cart_item_data ) && isset( $cart_item_data['yith_wcp_component_data'] ) && ! empty( $cart_item_data['yith_wcp_component_data'] ) ) {

					WC()->cart->cart_contents[ $cart_item_key ][ 'yith_wcp_component_data' ]['cart_item_key'] = $cart_item_key;

					remove_action( 'woocommerce_add_to_cart', array( $this, 'add_child_items' ), 10 ) ;
					remove_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10 );

					$component_data = $cart_item_data['yith_wcp_component_data'];
					$composite_stored_data = $product->getComponentsData();

					foreach ( $composite_stored_data as $key => $component_item ) {

						if ( isset( $component_data['selection_data'][$key] ) ) {

							if ( $component_data['selection_data'][$key] > 0 ) {

								// Check if a variation is selected
								if ( isset( $component_data['selection_variation_data'][$key] ) && $component_data['selection_variation_data'][$key] > 0 ) {
									$child_product = wc_get_product( $component_data['selection_variation_data'][$key] );
								} else {
									$child_product = wc_get_product( $component_data['selection_data'][$key] );
								}

								$parent_id = yit_get_base_product_id( $child_product );
								YITH_WCP_Frontend::markProductAsCompositeProcessed( $child_product, yit_get_base_product_id( $product ), $key );
								$child_variation_id = 0;
								$child_variation_data = array();

								if ( isset( $component_data['selection_variation_data'][$key] ) ) {

									// Variation selected by the user or forced by dependence
									if ( $component_data['selection_variation_data'][$key] > 0 ) {
										$child_variation_id = $component_data['selection_variation_data'][$key];
										$child_variation_data = wc_get_product_variation_attributes( $child_variation_id );
									} else {
										$child_variation_id = $component_data['selection_data'][$key];
										$child_variation_object = wc_get_product( $component_data['selection_data'][$key] );
										$child_variation_data = wc_get_product_variation_attributes( $child_variation_object->get_id() );
										$parent_id = yit_get_base_product_id( $child_product );
									}

								}

								$child_meta_data['yith_wcp_child_component_data'] = $this->getCompositeChildItemMetaData( $product, $key, $cart_item_key );

								if ( isset( $component_item['sold_individually'] ) && $component_item['sold_individually'] ) {
									$row_quantity = $component_data['selection_quantity'][$key];
								} else {
									$row_quantity = $component_data['selection_quantity'][$key] * $quantity;
								}

								$cart_children_key = WC()->cart->add_to_cart( absint( $parent_id ), absint( $row_quantity ), absint( $child_variation_id ), $child_variation_data, $child_meta_data );

							}

						}

					}

					add_action( 'woocommerce_add_to_cart', array( $this, 'add_child_items' ), 10, 6 ) ;
					add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );

				}

			}

		}

		/**
		 * @param $class
		 * @param $cart_item
		 * @param $cart_item_key
		 *
		 * @return string
		 */
		public function cart_item_class( $class , $cart_item, $cart_item_key ) {
			
			 if( isset( $cart_item['yith_wcp_component_data'] ) ) {

				 $class.= ' ywcp_component_item';

			 } else if( isset( $cart_item['yith_wcp_child_component_data'] ) ) {

				 $class.= ' ywcp_component_child_item';

			 }

			 return $class;
			
		}

		/**
		 * @param $link
		 * @param $cart_item_key
		 *
		 * @return string
		 */
		public function cart_item_remove_link( $link, $cart_item_key ) {

			if ( isset( WC()->cart->cart_contents[ $cart_item_key ][ 'yith_wcp_child_component_data' ] ) ) {

				$child_item_meta = WC()->cart->cart_contents[ $cart_item_key ][ 'yith_wcp_child_component_data' ];

				if( isset(  WC()->cart->cart_contents[ $child_item_meta['yith_wcp_cart_parent_key' ] ] ) ) {

					return '';

				}

			}

			return $link;

		}

		/**
		 * @param $cart_item_key
		 * @param $cart
		 */
		public function remove_items_from_cart( $cart_item_key, $cart )  {

			remove_action( 'woocommerce_cart_item_removed', array( $this, 'remove_items_from_cart' ), 10 );

			// Remove all components
			foreach( $cart->get_cart() as $cart_item_key_ass => $value ) {
				if( isset( $value['yith_wcp_child_component_data'] ) ) {
					if( isset( $value['yith_wcp_child_component_data']['yith_wcp_cart_parent_key'] ) && $value['yith_wcp_child_component_data']['yith_wcp_cart_parent_key'] == $cart_item_key  ) {
						WC()->cart->remove_cart_item( $cart_item_key_ass );
					}
				}
			}

			add_action( 'woocommerce_cart_item_removed', array( $this, 'remove_items_from_cart' ), 10 , 2 );

		}

		/**
		 * @param $cart_item_key
		 * @param $cart
		 */
		public function cart_item_restored( $cart_item_key, $cart ) {

			if ( isset( $cart->cart_contents[ $cart_item_key ][ 'yith_wcp_component_data' ] ) ) {

				$removed_cart_contents = $cart->removed_cart_contents;

				if( isset( $_REQUEST['undo_item'] ) ) {

					remove_action( 'woocommerce_cart_item_restored' , array( $this, 'cart_item_restored' ), 10 );

					foreach ( $removed_cart_contents as $cart_item_key_removed => $values ) {

						if( isset( $values['yith_wcp_child_component_data'] ) ) {

							if( isset( $values['yith_wcp_child_component_data']['yith_wcp_cart_parent_key'] ) && $values['yith_wcp_child_component_data']['yith_wcp_cart_parent_key'] == $cart_item_key  ) {

								$cart->restore_cart_item( $cart_item_key_removed );

							}

						}

					}

					add_action( 'woocommerce_cart_item_restored' , array( $this, 'cart_item_restored' ), 10, 2 );

				}
			}
		}

		/**
		 * @param $url
		 * @param $cart_item
		 * @param $cart_item_key
		 *
		 * @return mixed
		 */
		public function cart_item_permalink( $url , $cart_item, $cart_item_key ) {

			if ( isset( $cart_item[ 'yith_wcp_component_data' ] ) ) {

				/** @var WC_Product $product */
				$product = $cart_item['data'];
				$url = $product->get_permalink() . '?ywcp_cart_item_key=' . $cart_item_key;

			}

			return $url;

		}

		/**
		 * @param $title
		 * @param $cart_item
		 * @param $cart_item_key
		 *
		 * @return string
		 */
		public function cart_item_name( $title, $cart_item, $cart_item_key ) {
			if ( isset( $cart_item[ 'yith_wcp_child_component_data' ] ) ) {
				$child_data = $cart_item[ 'yith_wcp_child_component_data' ];
				$component_data = $child_data[ 'yith_wcp_component_parent_object' ]->getComponentItemByKey( $child_data[ 'yith_wcp_component_key' ] );
				$component_name = apply_filters( 'ywcp_item_component_name', $component_data[ 'name' ], yit_get_base_product_id( $child_data[ 'yith_wcp_component_parent_object' ] ), $child_data[ 'yith_wcp_component_key' ] );
				$title = '<div class="ywcp_cart_component_name"><label>' . apply_filters( 'ywcp_cart_component_name_label', esc_html( $component_name ) ) . ': </label>' . $title . '</div>';
			}
			return $title;
		}

		/**
		 * @param $title
		 * @param $item
		 *
		 * @return string
		 */
		public function order_item_name( $title, $item ) {
			if ( isset( $item['item_meta']['_yith_wcp_child_component_data'][0] ) ) {
				$child_data = maybe_unserialize( $item['item_meta']['_yith_wcp_child_component_data'][0] );
				$component_data = $child_data[ 'yith_wcp_component_parent_object' ]->getComponentItemByKey( $child_data[ 'yith_wcp_component_key' ] );
				$component_name = apply_filters( 'ywcp_item_component_name', $component_data[ 'name' ], yit_get_base_product_id( $child_data[ 'yith_wcp_component_parent_object' ] ), $child_data[ 'yith_wcp_component_key' ] ) ;
				$title = '<div class="ywcp_cart_component_name"><label>' . apply_filters( 'ywcp_cart_component_name_label', esc_html( $component_name ) ) . ':</label>' . $title . '</div>';
			}
			return $title;
		}

		/**
		 * @param $product_price
		 * @param $cart_item
		 *
		 * @return string
		 */
		public function cart_item_price( $product_price, $cart_item ) {
			$product = $cart_item['data'];
			if ( $product->get_type() == 'yith-composite' && ! empty( $cart_item['yith_wcp_component_data'] ) && $cart_item['line_total'] == 0 ) { $product_price = ''; }
			elseif ( ! empty( $cart_item['yith_wcp_child_component_data'] ) && $cart_item['line_total'] == 0 ) { $product_price = ''; }
			return $product_price;
		}

		/**
		 * @param $product_quantity
		 * @param $cart_item_key
		 * @param $cart_item
		 *
		 * @return mixed
		 */
		public function cart_item_quantity( $product_quantity, $cart_item_key, $cart_item = null ) {

            if ( ! $cart_item ) {
                $cart_item = WC()->cart->get_cart_item( $cart_item_key );
            }

			if ( isset( $cart_item['yith_wcp_child_component_data'] ) ) {

				$cart_item_child_meta = $cart_item['yith_wcp_child_component_data'];

				if ( isset( WC()->cart->cart_contents[ $cart_item_child_meta['yith_wcp_cart_parent_key']] ) ) {

					$wcp_component_item = $cart_item_child_meta['yith_wcp_component_parent_object']->getComponentItemByKey( $cart_item_child_meta['yith_wcp_component_key'] );
					$sold_individually = isset( $wcp_component_item['sold_individually'] ) && $wcp_component_item['sold_individually'] ? $wcp_component_item['sold_individually'] : false;
					$cart_item_parent_meta = WC()->cart->cart_contents[$cart_item_child_meta['yith_wcp_cart_parent_key']];
					$product = $cart_item['data'];

					if ( $sold_individually ) {
						$min_value = max( $wcp_component_item['min_quantity'], 1 );
						$max_value = $wcp_component_item['max_quantity'];
						$step = 1;
					} else {
						$wcp_component_item['min_quantity'] = empty( $wcp_component_item['min_quantity'] ) ? 1 : $wcp_component_item['min_quantity'];
						$wcp_component_item['max_quantity'] = empty( $wcp_component_item['max_quantity'] ) ? 0 : $wcp_component_item['max_quantity'];
						$min_value = max( $wcp_component_item['min_quantity'] * $cart_item_parent_meta['quantity'], 1 );
						$max_value = $wcp_component_item['max_quantity'] * $cart_item_parent_meta['quantity'];
						$step = $cart_item_parent_meta['quantity'];
					}

					if ( $cart_item_parent_meta['quantity'] == 1 ) {
						$min_value = apply_filters( 'ywcp_woocommerce_quantity_input_min', max( $wcp_component_item['min_quantity'], 1 ), $product );
						$max_value = apply_filters( 'ywcp_woocommerce_quantity_input_max', $wcp_component_item['max_quantity'], $product );
					}

					$product_quantity = woocommerce_quantity_input( array(
						'min_value'   => $min_value,
						'max_value'   => $max_value,
						'input_value' => $cart_item['quantity'],
						'input_name'  => "cart[{$cart_item_key}][qty]",
						'step'        => $step,
					), $product );

				}

			}

			return $product_quantity;
		}

		/**
		 * @param $cart_item_key
		 * @param int $quantity
		 */
		public function update_quantity_in_cart( $cart_item_key, $quantity = false, $old_quantity = false, $cart = false ) {

			if ( $quantity != false && $old_quantity != false && $cart != false ) {

				if ( ! empty( WC()->cart->cart_contents[ $cart_item_key ] ) ) {

					$new_quantity = $quantity;
					if ( $quantity == 0 || $quantity < 0 ) { $quantity = 0; }
					else { $quantity = WC()->cart->cart_contents[ $cart_item_key ][ 'quantity' ]; }

					$cart_item = WC()->cart->cart_contents[ $cart_item_key ];

					// parent quantity modified
					if ( isset( $cart_item['yith_wcp_component_data'] ) ) {

						$cart = WC()->cart->get_cart();

						// read child items of composite products

						foreach( $cart as $cart_item_key_ass => $value ) {

							if ( isset( $value['yith_wcp_child_component_data'] ) ) {

								$cart_item_child_meta_data = $value['yith_wcp_child_component_data'];

								if ( isset( $cart_item_child_meta_data['yith_wcp_cart_parent_key'] ) && $cart_item_child_meta_data['yith_wcp_cart_parent_key'] == $cart_item_key  ) {

									$wcp_child_key = $cart_item_child_meta_data['yith_wcp_component_key'];

									$wcp_component_item = $cart_item_child_meta_data['yith_wcp_component_parent_object']->getComponentItemByKey( $wcp_child_key );

									$sold_individually = isset( $wcp_component_item['sold_individually'] ) && $wcp_component_item['sold_individually'] ? $wcp_component_item['sold_individually'] : false;

									if ( ! $sold_individually ) {
										WC()->cart->set_quantity( $cart_item_key_ass, $cart_item['yith_wcp_component_data']['selection_quantity'][$wcp_child_key] * $quantity, false );
									}

								}

							}

						}

					}

					// child quantity modified

					else if ( isset( $cart_item['yith_wcp_child_component_data'] ) ) {

						// Fix cart quantities
						$parent_cart_item_key = $cart_item['yith_wcp_child_component_data']['yith_wcp_cart_parent_key'];
						$parent_cart_item = WC()->cart->get_cart_item( $parent_cart_item_key );
						$quantity = $parent_cart_item['quantity'];
						// Fix cart quantities

						WC()->cart->cart_contents[ $cart_item_key ][ 'quantity' ] = $new_quantity;

						$cart_item_child_meta_data = $cart_item['yith_wcp_child_component_data'];

						if ( isset( $cart_item_child_meta_data['yith_wcp_cart_parent_key'] ) ) {

							$parent_key = $cart_item_child_meta_data['yith_wcp_cart_parent_key'];

							if ( isset( WC()->cart->cart_contents[ $parent_key ] ) ) {

								if ( isset( WC()->cart->cart_contents[ $parent_key ]['yith_wcp_component_data'] ) ) {

									$wcp_child_key = $cart_item_child_meta_data['yith_wcp_component_key'];

									$wcp_component_item = $cart_item_child_meta_data['yith_wcp_component_parent_object']->getComponentItemByKey( $wcp_child_key );

									$sold_individually = isset( $wcp_component_item['sold_individually'] ) && $wcp_component_item['sold_individually'] ? $wcp_component_item['sold_individually'] : false;

									$parent_quanity = WC()->cart->cart_contents[ $parent_key ]['quantity'];

									if ( $parent_quanity == 1 || $sold_individually ) {

										WC()->cart->cart_contents[ $parent_key ]['yith_wcp_component_data']['selection_quantity'][$wcp_child_key] = $new_quantity;

									} else if( $parent_quanity > 1 ) {

									   WC()->cart->cart_contents[ $parent_key ]['yith_wcp_component_data']['selection_quantity'][$wcp_child_key] = $quantity / $parent_quanity;

									}

								}

							}
						}
					}
				}
			}
		}

		/**
		 * @param $product_sub_total
		 * @param $cart_item
		 *
		 * @return string
		 */
		public function cart_item_subtotal( $product_sub_total, $cart_item, $cart_item_key ) {
			$product = $cart_item['data'];
			$calculate_subtotal = apply_filters( 'yith_wcp_calculate_subtotals', true, $cart_item );
			if ( $calculate_subtotal ) {
				if ( $product->get_type() == 'yith-composite' && ! empty( $cart_item['yith_wcp_component_data'] ) ) {
					if ( $product->isPerItemPricing() ) {
						$component_data = $cart_item['yith_wcp_component_data'];
						$composite_base_price = isset( $cart_item['data'] ) ? yit_get_display_price( $cart_item['data'] ) : $component_data['product_base_price'];
						$composite_total = $this->getCompoSiteChildsSubTotal( $product, $component_data, $cart_item_key, $cart_item['quantity'] );
						$new_subtotal = ( $composite_base_price * $cart_item['quantity'] ) + $composite_total;
						$price_html = wc_price( $new_subtotal );
						if ( $composite_base_price > 0 ) {
							$price_html .= '<div>(' . __( 'Base Price:', 'yith-composite-products-for-woocommerce' ) . ' ' . wc_price( $composite_base_price * $cart_item['quantity'] ) . ' + ' .
											__( 'Components:', 'yith-composite-products-for-woocommerce' ) . ' ' . wc_price( $composite_total ).')</div>';
						}
						return $price_html;
					}
				} elseif ( isset( $cart_item['yith_wcp_child_component_data'] ) ) {

					$child_item_meta = $cart_item['yith_wcp_child_component_data'];
					if ( $child_item_meta['yith_wcp_component_parent_object']->isPerItemPricing() ) {
						return __( 'Options subtotal', 'yith-composite-products-for-woocommerce' ) . ': ' . $product_sub_total;
					} else {
						return '';
					}
				}
			}
			return $product_sub_total;
		}

		/**
		 * @param $product_sub_total
		 * @param $item
		 * @param $order
		 *
		 * @return string
		 */
		public function order_item_subtotal( $product_sub_total , $item , $order ) {

			if ( isset( $item[ 'ywcp_updated_subtotal' ] ) ) {
				return $product_sub_total;
			}

			if( isset( $item['item_meta'] ) ) {

				$cart_item = $item['item_meta'];

				$product_id = isset( $cart_item['_product_id'][0] ) ? $cart_item['_product_id'][0] : $item->get_product_id();

				$product = wc_get_product( $product_id );

				if ( is_object( $product ) && $product->get_type() == 'yith-composite' && ! empty( $cart_item['_yith_wcp_component_data'][0] ) ) {

					$component_parent_data = maybe_unserialize( $cart_item['_yith_wcp_component_data'][0] );

					$cart_parent_key = $component_parent_data['cart_item_key'];

					foreach ( $order->get_items( 'line_item' ) as $order_item_id => $order_child_item ) {

						if( $item != $order_child_item ) {

							if(  !empty( $order_child_item['item_meta']['_yith_wcp_child_component_data'][0] ) )  {

								$component_child_data = maybe_unserialize( $order_child_item['item_meta']['_yith_wcp_child_component_data'][0] );

								if ( $cart_parent_key == $component_child_data['yith_wcp_cart_parent_key'] ) {

									$item[ 'line_subtotal' ]     += $order_child_item[ 'line_subtotal' ];
									$item[ 'line_subtotal_tax' ] += $order_child_item[ 'line_subtotal_tax' ];

								}

							}

						}

					}

					$item[ 'ywcp_updated_subtotal' ] = 1;

					return $order->get_formatted_line_subtotal( $item );

				} else if ( isset( $cart_item['_yith_wcp_child_component_data'][0] ) ) {

					$child_item_meta = maybe_unserialize( $cart_item['_yith_wcp_child_component_data'][0] );

					if ( $child_item_meta['yith_wcp_component_parent_object']->isPerItemPricing() ) {

						return __( 'Options subtotal', 'yith-composite-products-for-woocommerce' ) . ': ' . $product_sub_total;

					} else {

						return '';

					}

				}

			}

			return $product_sub_total;

		}

		/**
		 * @param $product
		 * @param $component_data
		 * @param $cart_item_key
		 *
		 * @return int|string
		 */
		public function getCompoSiteChildsSubTotal( $product, $component_data, $cart_item_key, $global_quantity ) {
			$new_subtotal = 0;
			$composite_stored_data = $product->getComponentsData();
			$cart = WC()->cart->get_cart();
			foreach ( $composite_stored_data as $key => $component_item ) {
				if( isset( $component_data['selection_data'][$key] ) ) {
					if ( $component_data['selection_data'][$key] > 0 ) {
						// Variation selected
						if ( isset( $component_data['selection_variation_data'][$key] ) && $component_data['selection_variation_data'][$key] > 0 ) {
							$child_product = wc_get_product( $component_data['selection_variation_data'][$key] );
						} else {
							$child_product = wc_get_product( $component_data['selection_data'][$key] );
						}
						YITH_WCP_Frontend::markProductAsCompositeProcessed( $child_product, yit_get_base_product_id( $product ), $key );
						// Read child items of composite products
						foreach ( $cart as $cart_item_key_ass => $value ) {
							if ( isset( $value['yith_wcp_child_component_data'] ) ) {
								$cart_child_meta_data = $value['yith_wcp_child_component_data'];
								if ( isset( $cart_child_meta_data['yith_wcp_cart_parent_key'] ) && $cart_child_meta_data['yith_wcp_cart_parent_key'] == $cart_item_key ) {
									if ( $cart_child_meta_data['yith_wcp_component_key'] == $key ) {
										$child_quantity = $component_data['selection_quantity'][$key];
										$wcp_component_item = $cart_child_meta_data['yith_wcp_component_parent_object']->getComponentItemByKey( $cart_child_meta_data['yith_wcp_component_key'] );
										$sold_individually = isset( $wcp_component_item['sold_individually'] ) && $wcp_component_item['sold_individually'] ? $wcp_component_item['sold_individually'] : false;
										if ( $sold_individually ) {
											$single_total = yit_get_display_price( $child_product ) * $child_quantity;
										} else {
											$single_total = yit_get_display_price( $child_product ) * $global_quantity;
										}
										$new_subtotal += $single_total;
										break;
									}
								}
							}
						}
					}
				}
			}
			return apply_filters( 'yith_wcp_composite_children_subtotal', $new_subtotal, $product , $component_data , $cart_item_key , $global_quantity );
		}


		/**
		 * @param $item_id
		 * @param $values
		 * @param $cart_item_key
		 */
		public function order_item_meta( $item_id, $values, $cart_item_key ) {

			if ( ! empty( $values['yith_wcp_component_data'] ) ) {

				wc_add_order_item_meta( $item_id, '_yith_wcp_component_data', $values['yith_wcp_component_data'] );

			} else if ( ! empty( $values['yith_wcp_child_component_data'] ) ) {

				wc_add_order_item_meta( $item_id, '_yith_wcp_child_component_data', $values['yith_wcp_child_component_data'] );
				// wc_add_order_item_meta( $item_id, '_yith_wcp_child_component_data_no_reorder', 1 );

			}

		}

		/**
		 * @param $cart_item_data
		 * @param $item
		 * @param $order
		 *
		 * @return mixed
		 */
		public function order_again_cart_item_data( $cart_item_data, $item, $order ) {

			if ( isset( $item['item_meta']['_yith_wcp_component_data'] ) && $item['item_meta']['_yith_wcp_component_data'] ) {

				$cart_item_data['yith_wcp_component_data'] = maybe_unserialize( $item['item_meta']['_yith_wcp_component_data'] );

			} else if ( isset( $item['item_meta']['_yith_wcp_child_component_data'] ) && $item['item_meta']['_yith_wcp_child_component_data'] ) {

				$cart_item_data['yith_wcp_child_component_data'] = maybe_unserialize( $item['item_meta']['_yith_wcp_child_component_data'] );
				// $cart_item_data['yith_wcp_child_component_data_no_reorder'] = $item['item_meta']['_yith_wcp_child_component_data_no_reorder'];

			}

			return $cart_item_data;

		}

		/**
		 * @param $packages
		 *
		 * @return mixed
		 */
		public function cart_shipping_packages( $packages ) {

			if ( ! empty( $packages ) ) {

				foreach ( $packages as $package_key => $package ) {

					if ( ! empty( $package[ 'contents' ] ) ) {

						foreach ( $package[ 'contents' ] as $cart_item_key => $cart_item_data ) {

							if ( isset( $cart_item_data[ 'yith_wcp_component_data' ] ) ) {

								$composite     = clone $cart_item_data[ 'data' ];
								$composite_qty = $cart_item_data[ 'quantity' ];

								if ( ! $composite->isPerItemShipping() ) {

									// Aggregate weights.

									$bundled_weight = 0;

									// Aggregate prices.

									$bundled_price = 0;

									$bundle_totals = array(
										'line_subtotal'     => $cart_item_data[ 'line_subtotal' ],
										'line_total'        => $cart_item_data[ 'line_total' ],
										'line_subtotal_tax' => $cart_item_data[ 'line_subtotal_tax' ],
										'line_tax'          => $cart_item_data[ 'line_tax' ],
										'line_tax_data'     => $cart_item_data[ 'line_tax_data' ]
									);

									foreach( WC()->cart->get_cart() as $cart_item_key_ass => $value ) {

										if ( isset( $value['yith_wcp_child_component_data'] ) ) {

											if ( isset( $value['yith_wcp_child_component_data']['yith_wcp_cart_parent_key'] ) && $value['yith_wcp_child_component_data']['yith_wcp_cart_parent_key'] == $cart_item_key  ) {

												if ( isset( $package[ 'contents' ][ $cart_item_key_ass ] ) ) {

													$child_cart_item_data   = $package[ 'contents' ][ $cart_item_key_ass ];
													$composited_product     = clone $child_cart_item_data[ 'data' ];
													$composited_product_qty = $child_cart_item_data[ 'quantity' ];

													// Aggregate price.

													if ( isset( $composited_product->ywcp_bundled_price ) ) {

														$bundled_price += $composited_product->ywcp_bundled_price * $composited_product_qty;
														yit_set_prop( $composited_product , 'price' , 0 );
														$packages[ $package_key ][ 'contents' ][ $cart_item_key_ass ][ 'data' ] = $composited_product;

														$bundle_totals[ 'line_subtotal' ]     += $child_cart_item_data[ 'line_subtotal' ];
														$bundle_totals[ 'line_total' ]        += $child_cart_item_data[ 'line_total' ];
														$bundle_totals[ 'line_subtotal_tax' ] += $child_cart_item_data[ 'line_subtotal_tax' ];
														$bundle_totals[ 'line_tax' ]          += $child_cart_item_data[ 'line_tax' ];

														$packages[ $package_key ][ 'contents_cost' ] += $child_cart_item_data[ 'line_total' ];

														$child_item_line_tax_data = $child_cart_item_data[ 'line_tax_data' ];

														$bundle_totals[ 'line_tax_data' ][ 'total' ]    = array_merge( $bundle_totals[ 'line_tax_data' ][ 'total' ], $child_item_line_tax_data[ 'total' ] );
														$bundle_totals[ 'line_tax_data' ][ 'subtotal' ] = array_merge( $bundle_totals[ 'line_tax_data' ][ 'subtotal' ], $child_item_line_tax_data[ 'subtotal' ] );

														$packages[ $package_key ][ 'contents' ][ $cart_item_key_ass ][ 'line_subtotal' ]               = 0;
														$packages[ $package_key ][ 'contents' ][ $cart_item_key_ass ][ 'line_total' ]                  = 0;
														$packages[ $package_key ][ 'contents' ][ $cart_item_key_ass ][ 'line_subtotal_tax' ]           = 0;
														$packages[ $package_key ][ 'contents' ][ $cart_item_key_ass ][ 'line_tax' ]                    = 0;
														$packages[ $package_key ][ 'contents' ][ $cart_item_key_ass ][ 'line_tax_data' ][ 'total' ]    = array();
														$packages[ $package_key ][ 'contents' ][ $cart_item_key_ass ][ 'line_tax_data' ][ 'subtotal' ] = array();
													}

													// Aggregate weight.

													if ( isset( $composited_product->ywcp_bundled_weight ) ) {
														$bundled_weight += $composited_product->ywcp_bundled_weight * $composited_product_qty;
													}

												}

											}

										}

									}

									$composite->set_price( $bundled_price / $composite_qty );
									$packages[ $package_key ][ 'contents' ][ $cart_item_key ] = array_merge( $cart_item_data, $bundle_totals );

									$composite_weight  = (float) yit_get_prop( $composite, 'weight' );
									$additional_weight = (float) ( ( $bundled_weight <= 0 ) ? 0 : $bundled_weight / $composite_qty );

									yit_set_prop( $composite, 'weight', ( $composite_weight + $additional_weight ) );

									$packages[ $package_key ][ 'contents' ][ $cart_item_key ][ 'data' ] = $composite;

								}
							}
						}
					}
				}
			}

			return $packages;
		}

		/**
		 *
		 * check if a composite product has a dynamic price
		 * @param float $price
		 * @param WC_Product $product
		 *
		 * @return float
		 */
		public function check_dynamic_price( $price , $product ){

			if( $product->get_type() == 'yith-composite' && !( is_product() || is_archive() ) ){

				if( function_exists( 'YITH_WC_Dynamic_Pricing_Frontend' ) ){
					$product_id = yit_get_product_id( $product);
					if( isset( YITH_WC_Dynamic_Pricing_Frontend()->has_get_price_filter[ $product_id ] ) ){
						$price = YITH_WC_Dynamic_Pricing_Frontend()->has_get_price_filter[ $product_id ];
					}
				}
			}

			return $price;

		}
		
	}

}
