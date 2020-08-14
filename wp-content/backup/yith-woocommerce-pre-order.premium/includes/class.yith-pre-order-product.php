<?php

if ( ! defined( 'YITH_WCPO_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}
if ( ! class_exists( 'YITH_Pre_Order_Product' ) ) {


	class YITH_Pre_Order_Product {

		public $product;
		public $id;

		/**
		 * Constructor
		 *
		 * @param int|WC_Product|object $product Product ID, post object, or product object
		 *
		 * @since  1.0.0
		 */
		public function __construct( $product ) {
			if ( $product ) {
				$this->product = wc_get_product( $product ) instanceof WC_Product ? wc_get_product( $product ) : false;
				if ( $this->product ) {
					$this->id = $this->product->get_id();
				}
			}
		}

		public function set_pre_order_status( $status ) {
			$old_status = $this->get_pre_order_status();
			yit_save_prop( $this->product, '_ywpo_preorder', $status );
			do_action( 'yith_ywpo_pre_order_status_changed', $this->product, $status, $old_status );
		}

		public function get_pre_order_status() {
			$pre_order_status = $this->product instanceof WC_Product ? yit_get_prop( $this->product, '_ywpo_preorder', true ) : false;
			return apply_filters( 'yith_ywpo_pre_order_get_status', $pre_order_status, $this->id, $this->product );
		}

		public function set_for_sale_date( $date ) {
			if ( ! empty( $date ) ) {
				$format_date = str_replace( '/', '-', $date );
				$format_date = $format_date . ':00';
				$format_date = get_gmt_from_date( $format_date );
				yit_save_prop( $this->product, '_ywpo_for_sale_date', $format_date ? strtotime( $format_date ) : '' );
				do_action( 'yith_ywpo_pre_order_date_changed', $this->id, $date );
			} else {
				yit_save_prop( $this->product, '_ywpo_for_sale_date', '' );
			}
		}

		public function get_for_sale_date() {
			$timestamp     = yit_get_prop( $this->product, '_ywpo_for_sale_date', true );
			$for_sale_date = ! empty( $timestamp )
				? get_date_from_gmt( date( 'Y-m-d H:i:s', $timestamp ), 'Y/m/d H:i' )
				: '';

			return apply_filters( 'yith_ywpo_pre_order_get_for_sale_date', $for_sale_date, $this->id, $this->product );
		}

		public function get_for_sale_date_timestamp() {
			$timestamp = yit_get_prop( $this->product, '_ywpo_for_sale_date', true );
			return apply_filters( 'yith_ywpo_pre_order_get_for_sale_date_timestamp', $timestamp, $this->id, $this->product );
		}

		public function set_pre_order_label( $pre_order_label ) {
			if ( isset( $pre_order_label ) ) {
				yit_save_prop( $this->product, '_ywpo_preorder_label', $pre_order_label );
				do_action( 'yith_ywpo_pre_order_label_changed', $this->id, $pre_order_label );
			}
		}

		public function get_pre_order_label() {
			$pre_order_label = yit_get_prop( $this->product, '_ywpo_preorder_label', true );
			return apply_filters( 'yith_ywpo_pre_order_get_label', $pre_order_label, $this->id, $this->product );
		}

		public function set_pre_order_availability_date_label( $pre_order_availability_date_label ) {
			if ( isset( $pre_order_availability_date_label ) ) {
				yit_save_prop( $this->product, '_ywpo_preorder_availability_date_label', $pre_order_availability_date_label );
				do_action( 'yith_ywpo_pre_order_availability_date_label_changed', $this->id, $pre_order_availability_date_label );
			}
		}

		public function get_pre_order_availability_date_label() {
			$pre_order_availability_date_label = yit_get_prop( $this->product, '_ywpo_preorder_availability_date_label', true );
			return apply_filters( 'yith_ywpo_pre_order_get_availability_date_label', $pre_order_availability_date_label, $this->id, $this->product );
		}

		public function set_pre_order_price( $pre_order_price ) {
			if ( isset( $pre_order_price ) ) {
				yit_save_prop( $this->product, '_ywpo_preorder_price', $pre_order_price );
				do_action( 'yith_ywpo_pre_order_price_changed', $this->id, $pre_order_price );
			}
		}

		public function get_pre_order_price() {
			$pre_order_price = yit_get_prop( $this->product, '_ywpo_preorder_price', true );
			return apply_filters( 'yith_ywpo_pre_order_get_price', $pre_order_price, $this->id, $this->product );
		}

		public function set_pre_order_adjustment_amount( $adjustment_amount ) {
			if ( isset( $adjustment_amount ) ) {
				yit_save_prop( $this->product, '_ywpo_price_adjustment_amount', $adjustment_amount );
				do_action( 'yith_ywpo_pre_order_adjustment_amount_changed', $this->id, $adjustment_amount );
			}
		}

		public function get_pre_order_adjustment_amount() {
			$price_adjustment_amount = yit_get_prop( $this->product, '_ywpo_price_adjustment_amount', true );
			return apply_filters( 'yith_ywpo_pre_order_get_adjustment_amount', $price_adjustment_amount, $this->id, $this );
		}

		public function set_pre_order_price_adjustment( $price_adjustment ) {
			if ( isset( $price_adjustment ) ) {
				yit_save_prop( $this->product, '_ywpo_price_adjustment', $price_adjustment );
				do_action( 'yith_ywpo_pre_order_price_adjustment_changed', $this->id, $price_adjustment );
			}
		}

		public function get_pre_order_price_adjustment() {
			$price_adjustment = yit_get_prop( $this->product, '_ywpo_price_adjustment', true );
			return apply_filters( 'yith_ywpo_pre_order_get_price_adjustment', $price_adjustment, $this->id, $this->product );
		}

		public function set_pre_order_adjustment_type( $adjustment_type ) {
			if ( isset( $adjustment_type ) ) {
				yit_save_prop( $this->product, '_ywpo_adjustment_type', $adjustment_type );
				do_action( 'yith_ywpo_pre_order_adjustment_type_changed', $this->id, $adjustment_type );
			}
		}

		public function get_pre_order_adjustment_type() {
			$adjustment_type = yit_get_prop( $this->product, '_ywpo_adjustment_type', true );
			return apply_filters( 'yith_ywpo_pre_order_get_adjustment_type', $adjustment_type, $this->id, $this->product );
		}

		public function clear_pre_order_product() {
			delete_post_meta( $this->id, '_ywpo_preorder' );
			delete_post_meta( $this->id, '_ywpo_preorder_notified' );
			delete_post_meta( $this->id, '_ywpo_for_sale_date' );
			delete_post_meta( $this->id, '_ywpo_preorder_label' );
			delete_post_meta( $this->id, '_ywpo_price_adjustment' );
			delete_post_meta( $this->id, '_ywpo_preorder_price' );
			delete_post_meta( $this->id, '_ywpo_adjustment_type' );
			delete_post_meta( $this->id, '_ywpo_price_adjustment_amount' );
		}


	}


}