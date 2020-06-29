<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists ( 'YWGC_Multi_Vendor_Module' ) ) {
	
	/**
	 *
	 * @class   YWGC_Multi_Vendor_Module
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YWGC_Multi_Vendor_Module {
		
		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;
		
		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null ( self::$instance ) ) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		
		public function __construct() {
			/**
			 * Prevent the multiple gift card generation when an order is set as completed
			 */
			add_filter ( 'yith_ywgc_create_gift_card_for_order_item', array(
				$this,
				'manage_vendor_gift_cards_generation'
			), 10, 4 );
			
			/**
			 * Retrieve the list of gift cards of an item in the suborder, to be shown on main order
			 */
			add_filter ( 'yith_ywgc_get_order_item_gift_cards', array(
				$this,
				'retrieve_suborder_gift_cards'
			), 10, 2 );
			
			/**
			 * Manage a link between the order items from main order and sub order
			 */
			add_filter ( 'yith_get_order_item_gift_cards', array(
				$this,
				'get_parent_order_item'
			) );
			
			add_filter ( 'yith_ywgc_enter_pre_printed_gift_card_code', array(
				$this,
				'can_enter_pre_printed_code'
			), 10, 3 );
			
			/**
			 * Add plugin compatibility with YITH WooCommerce Multi Vendor
			 */
			add_filter ( 'ywgc_can_create_gift_card', array(
				$this,
				'user_can_create_gift_cards'
			) );
		}
		
		/**
		 * Deny all vendors from creating gift cards
		 *
		 * @param $enable_user bool current enable status
		 *
		 * @return bool
		 */
		public function user_can_create_gift_cards( $enable_user ) {
			//  if YITH Multivendor is active, check if the user can
			if ( defined ( 'YITH_WPV_PREMIUM' ) ) {
				$vendor = yith_get_vendor ( 'current', 'user' );
				
				return $vendor->is_super_user ();
			}
			
			return $enable_user;
		}
		
		/**
		 * Manage a link between the order items from main order and sub order
		 *
		 * @param int $order_item_id
		 *
		 * @return int
		 */
		public function get_parent_order_item( $order_item_id ) {
			
			$parent_id = wc_get_order_item_meta ( $order_item_id, '_parent_line_item_id', true );
			
			if ( $parent_id ) {
				return $parent_id;
			}
			
			return $order_item_id;
		}
		
		/**
		 * Prevent the main order from generating gift cards code automatically if the product owner is a (different) vendor
		 *
		 * @param bool     $can_do
		 * @param WC_Order $order
		 * @param int      $order_item_id
		 * @param array    $order_item_data
		 *
		 * @return bool
		 */
		public function manage_vendor_gift_cards_generation( $can_do, $order, $order_item_id, $order_item_data ) {
			
			$product_id = $order_item_data["product_id"];
			
			return $this->is_product_owner ( $order, $product_id );
		}
		
		/**
		 * Check if the seller of the order is also the owner of the product
		 *
		 * @param WC_Order $order
		 * @param int      $product_id
		 *
		 * @return bool
		 */
		public function is_product_owner( $order, $product_id ) {
			$order_seller_id = get_post_field ( 'post_author', $order->get_id() );
			
			//  check if the product owner is the same of the order
			$product_vendor = yith_get_vendor ( $product_id, 'product' );
			$order_vendor   = yith_get_vendor ( $order_seller_id, 'user' );
			
			return $product_vendor->id == $order_vendor->id;
		}
		
		/**
		 * Check if the current user can enter the code for pre-printed gift card
		 *
		 * @param bool       $can_do
		 * @param WC_Order   $order
		 * @param WC_Product $_product
		 *
		 * @return bool
		 */
		public function can_enter_pre_printed_code( $can_do, $order, $_product ) {
			
			return $this->is_product_owner ( $order, $_product->get_id() );
		}
	}
}

YWGC_Multi_Vendor_Module::get_instance ();