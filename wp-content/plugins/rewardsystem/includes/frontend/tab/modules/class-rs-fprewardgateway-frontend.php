<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSRewardGatewayFrontend' ) ) {

	class RSRewardGatewayFrontend {

		public static function init() {

			add_action( 'woocommerce_checkout_update_order_meta' , array( __CLASS__ , 'update_cart_subtotal' ) , 10 , 2 ) ;

			if ( '1' == get_option( 'rs_show_hide_reward_points_gateway' ) ) {
				// Validate add to cart.
				add_filter( 'woocommerce_add_to_cart_validation' , array( __CLASS__ , 'validate_add_to_cart' ) , 10 , 2 ) ;
				// Check cart items.
				add_action( 'woocommerce_check_cart_items' , array( __CLASS__ , 'check_cart_items' ) ) ;
			}
		}

		public static function update_cart_subtotal( $order_id, $data ) {
			$cart_subtotal = srp_cart_subtotal( true ) ;
			update_post_meta( $order_id , 'rs_cart_subtotal' , $cart_subtotal ) ;
		}

		/**
		 * Validate add to cart.
		 *
		 * @return bool
		 * */
		public static function validate_add_to_cart( $bool, $product_id ) {

			$product = wc_get_product( $product_id ) ;
			if ( ! is_object( $product ) ) {
				return $bool ;
			}

			$cart_items = WC()->cart->cart_contents ;
			if ( ! srp_check_is_array( $cart_items ) ) {
				return $bool ;
			}

			if ( self::validate_visible_selected_products_or_categories_type( $product ) ) {
				// Validate if other products added in cart.
				foreach ( $cart_items as $key => $value ) {
					$cart_product_id = isset( $value[ 'product_id' ] ) ? $value[ 'product_id' ] : '' ;
					$cart_product    = wc_get_product( $cart_product_id ) ;
					if ( ! is_object( $cart_product ) ) {
						continue ;
					}

					if ( ! self::validate_visible_selected_products_or_categories_type( $cart_product ) ) {
						wc_add_notice( get_option( 'rs_restrict_errmsg_add_to_cart_selected_products' , 'You cannot add this product to cart because it can be purchased only using Reward Points Gateway.' ) , 'error' ) ;
						return false ;
					}
				}
			}

			return $bool ;
		}

		/**
		 * Check cart items.
		 *
		 * @return bool
		 * */
		public static function check_cart_items() {

			$cart_items = WC()->cart->cart_contents ;
			if ( ! srp_check_is_array( $cart_items ) ) {
				return ;
			}

			$unset_product = false ;
			$cart_data     = array() ;
			foreach ( $cart_items as $cart_item_key => $cart_item_value ) {

				$cart_product_id = isset( $cart_item_value[ 'product_id' ] ) ? $cart_item_value[ 'product_id' ] : '' ;
				$cart_product    = wc_get_product( $cart_product_id ) ;
				if ( ! is_object( $cart_product ) ) {
					continue ;
				}

				if ( ! self::validate_visible_selected_products_or_categories_type( $cart_product ) ) {
					$unset_product = true ;
				}

				$cart_data[ $cart_product_id ] = $cart_item_key ;
			}

			$return = true ;
			if ( $unset_product ) {
				// Unset cart items when selected products and other products are added in cart.
				$return = self::unset_cart_items( $cart_data ) ;
			}

			return $return ;
		}

		/**
		 * Unset cart items.
		 *
		 * @return string/bool
		 * */
		public static function unset_cart_items( $cart_data ) {

			$return = true ;

			foreach ( $cart_data as $product_id => $item_key ) {

				$_product = wc_get_product( $product_id ) ;
				if ( ! is_object( $_product ) ) {
					continue ;
				}

				if ( self::validate_visible_selected_products_or_categories_type( $_product ) ) {
					// Remove the product from cart. 
					WC()->cart->set_quantity( $item_key , 0 ) ;

					wc_add_notice( str_replace( '[productname]' , $_product->get_name() , esc_html( get_option( 'rs_errmsg_when_other_products_added_to_cart_page' ) ) ) , 'error' ) ;

					$return = false ;
				}
			}

			return $return ;
		}

		/**
		 * Validate visible selected products/categories type.
		 *
		 * @return bool
		 * */
		public static function validate_visible_selected_products_or_categories_type( $product ) {

			$include_products = self::get_included_products() ;
			if ( in_array( $product->get_id() , $include_products ) ) {
				return true ;
			}

			$include_categories = self::get_included_categories() ;
			if ( srp_check_is_array( array_intersect( ( array ) $product->get_category_ids() , $include_categories ) ) ) {
				return true ;
			}

			return false ;
		}

		/**
		 * Get included products.
		 *
		 * @return array
		 * */
		public static function get_included_products() {

			$include_products = get_option( 'rs_select_product_for_purchase_using_points' ) ;
			if ( 'yes' != get_option( 'rs_enable_selected_product_for_purchase_using_points' ) || ! srp_check_is_array( $include_products ) ) {
				return array() ;
			}

			return $include_products ;
		}

		/**
		 * Get included categories.
		 *
		 * @return array
		 * */
		public static function get_included_categories() {

			$include_categories = get_option( 'rs_select_category_for_purchase_using_points' ) ;
			if ( 'yes' != get_option( 'rs_enable_selected_category_for_purchase_using_points' ) || ! srp_check_is_array( $include_categories ) ) {
				return array() ;
			}

			return $include_categories ;
		}

	}

	RSRewardGatewayFrontend::init() ;
}
