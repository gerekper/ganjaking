<?php
/**
 * Porto WooCommerce class to show Pre-Order buttons on shop and product pages
 *
 * @author     Porto Themes
 * @category   Library
 * @since      5.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Porto_Pre_Order_View' ) ) :
	class Porto_Pre_Order_View {
		public function __construct() {
			global $porto_settings;
			if ( empty( $porto_settings['woo-pre-order'] ) ) {
				return;
			}

			// add pre-order label to the "add to cart" buttons and cart item
			add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'pre_order_label' ), 10, 2 );
			add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'pre_order_label' ), 20, 2 );
			add_action( 'woocommerce_after_cart_item_name', array( $this, 'pre_order_product_cart_label' ), 75 );
			add_action( 'porto_woocommerce_minicart_after_product_name', array( $this, 'pre_order_product_cart_label' ) );

			// add porto-pre-order css class to the "add to cart" link
			add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'add_pre_order_class' ), 10, 2 );

			// display pre order available date
			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'display_pre_order_date' ) );
			add_filter( 'woocommerce_available_variation', array( $this, 'add_variable_pre_order_data' ), 10, 3 );

			// add pre_order status to the order item
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'add_pre_order_status' ), 10, 2 );
			add_action( 'woocommerce_new_order_item', array( $this, 'add_order_item_meta' ), 10, 2 );

			add_action( 'woocommerce_order_item_meta_start', array( $this, 'add_pre_order_label_on_single_order_page' ), 10, 3 );
		}

		public function pre_order_label( $text, $product ) {
			if ( 'yes' == get_post_meta( $product->get_id(), '_porto_pre_order', true ) ) {
				global $porto_settings;
				return empty( $porto_settings['woo-pre-order-label'] ) ? esc_html__( 'Pre-Order Now', 'porto' ) : esc_html( $porto_settings['woo-pre-order-label'] );
			}

			return $text;
		}

		public function pre_order_product_cart_label( $cart_item ) {
			if ( is_a( $cart_item, 'WC_Product' ) ) {
				$product_id = $cart_item->get_id();
			} else {
				$product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
			}
			if ( 'yes' != get_post_meta( $product_id, '_porto_pre_order', true ) ) {
				return;
			}
			echo '<div class="label-pre-order">' . esc_html__( 'Pre-Ordered', 'porto' ) . '</div>';
		}

		public function display_pre_order_date() {
			global $product;
			if ( $product->is_type( 'simple' ) && 'yes' == get_post_meta( $product->get_id(), '_porto_pre_order', true ) ) {
				$this->get_available_date_html_escaped( $product->get_id(), true );
			}
		}

		public function add_variable_pre_order_data( $vars, $self, $variation ) {
			if ( 'yes' == get_post_meta( $variation->get_id(), '_porto_pre_order', true ) ) {
				global $porto_settings;
				$vars['porto_pre_order']       = true;
				$vars['porto_pre_order_label'] = empty( $porto_settings['woo-pre-order-label'] ) ? esc_js( __( 'Pre-Order Now', 'porto' ) ) : esc_js( $porto_settings['woo-pre-order-label'] );

				$available_date_escaped = $this->get_available_date_html_escaped( $variation->get_id() );
				if ( $available_date_escaped ) {
					$vars['porto_pre_order_date'] = $available_date_escaped;
				}
			}
			return $vars;
		}

		public function add_pre_order_class( $link, $product ) {
			if ( $product->is_purchasable() && $product->is_in_stock() && 'yes' == get_post_meta( $product->get_id(), '_porto_pre_order', true ) ) {
				return str_replace( 'add_to_cart_button', 'add_to_cart_button porto-pre-order', $link );
			}
			return $link;
		}

		public function add_pre_order_status( $order_id, $post ) {
			$order = wc_get_order( $order_id );
			$items = $order->get_items();
			foreach ( $items as $key => $item ) {
				if ( ! empty( $item['variation_id'] ) ) {
					$product_id = $item['variation_id'];
				} else {
					$product_id = $item['product_id'];
				}

				if ( 'yes' == get_post_meta( $product_id, '_porto_pre_order', true ) ) {
					update_post_meta( $order_id, '_porto_pre_order', 'yes' );
					$product = wc_get_product( $product_id );

					/* translators: Order Item name */
					$order->add_order_note( sprintf( esc_html__( 'Item %s is Pre-Ordered', 'porto' ), esc_html( $product->get_formatted_name() ) ) );

					break;
				}
			}
		}

		public function add_order_item_meta( $item_id, $item ) {
			if ( 'line_item' != $item->get_type() ) {
				return;
			}
			$product = $item->get_product();
			if ( ! $product ) {
				return;
			}
			if ( 'yes' == get_post_meta( $product->get_id(), '_porto_pre_order', true ) ) {
				wc_add_order_item_meta( $item_id, '_porto_pre_order_item', 'yes' );
				$date = get_post_meta( $product->get_id(), '_porto_pre_order_date', true );
				if ( $date ) {
					wc_add_order_item_meta( $item_id, '_porto_pre_order_item_date', $date );
				}
			}
		}

		public function add_pre_order_label_on_single_order_page( $item_id, $item, $order ) {
			if ( isset( $item['porto_pre_order_item'] ) && 'yes' == $item['porto_pre_order_item'] ) {
				echo '<div class="label-pre-order">' . esc_html__( 'Pre-Ordered', 'porto' ) . '</div>';
			}
		}

		private function get_available_date_html_escaped( $product_id, $echo = false ) {
			global $porto_settings;
			$available_date = get_post_meta( $product_id, '_porto_pre_order_date', true );
			if ( $available_date && ( strtotime( $available_date ) + 24 * HOUR_IN_SECONDS - current_time( 'timestamp' ) ) > 0 ) {
				$available_date = date_i18n( wc_date_format(), strtotime( $available_date ) );
				/* translators: available date */
				$available_date_escaped = sprintf( esc_js( empty( $porto_settings['woo-pre-order-msg-date'] ) ? __( 'Available Date: %s', 'porto' ) : $porto_settings['woo-pre-order-msg-date'] ), '<span>' . esc_js( apply_filters( 'porto_pre_order_available_date', $available_date, $product_id ) ) . '</span>' );
			} else {
				$available_date_escaped = empty( $porto_settings['woo-pre-order-msg-nodate'] ) ? esc_html__( 'Available soon', 'porto' ) : esc_js( $porto_settings['woo-pre-order-msg-nodate'] );
			}
			if ( $available_date_escaped ) {
				if ( $echo ) {
					echo '<p class="porto-pre-order-date">' . $available_date_escaped . '</p>';
				} else {
					return '<p class="mb-0 porto-pre-order-date">' . $available_date_escaped . '</p>';
				}
			}
			return false;
		}
	}
endif;
