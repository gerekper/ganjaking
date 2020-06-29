<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_Tracking_Data' ) ) {
	
	/**
	 * Implements features of Yith WooCommerce Order Tracking
	 *
	 * @class   YITH_Tracking_Data
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_Tracking_Data {
		
		private $order = null;
		
		private $tracking_code = '';

		private $tracking_postcode = '';

		private $carrier_id = - 1;
		
		private $pickup_date = '';
		
		private $pickedup = false;
		
		/**
		 * YITH_Tracking_Data constructor.
		 *
		 * @param int|WC_Order $order
		 */
		public function __construct( $order = 0 ) {
			if ( is_numeric( $order ) ) {
				$_order = $order > 0 ? wc_get_order( $order ) : null;
				
				$this->order = $_order ? $_order : null;
			} elseif ( $order instanceof WC_Order ) {
				$this->order = $order;
			}
			if ( $this->order ) {
				$this->load();
			}
		}
		
		/**
		 * @param int|WC_Order $order
		 *
		 * @return YITH_Tracking_Data
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public static function get( $order ) {
			return new YITH_Tracking_Data( $order );
			
		}
		
		private function load() {
			$this->tracking_code = yit_get_prop( $this->order, 'ywot_tracking_code' );
			$this->tracking_postcode = yit_get_prop( $this->order, 'ywot_tracking_postcode' );
			$this->carrier_id    = yit_get_prop( $this->order, 'ywot_carrier_id' );
			$this->pickup_date   = yit_get_prop( $this->order, 'ywot_pick_up_date' );
			$this->pickedup      = yit_get_prop( $this->order, 'ywot_picked_up' );
		}
		
		public function is_pickedup() {
			return ( 'on' == $this->pickedup ) || ( true == $this->pickedup ) || ( 1 == $this->pickedup );
		}
		
		public function get_tracking_code() {
			return $this->tracking_code;
		}

        /**
         * Return the postcode from the admin panel
         *
         * @return $this->tracking_postcode
         */
        public function get_tracking_postcode() {
			return apply_filters( 'yith_ywot_tracking_postcode', $this->tracking_postcode , $this->order );
		}
	
		public function get_carrier_id() {
			return $this->carrier_id;
		}
		
		public function get_pickup_date() {
			return $this->pickup_date;
		}
		
		public function set( $args = array() ) {
			if ( ! is_array( $args ) ) {
				
				return;
			}
			
			if ( isset( $args['ywot_tracking_code'] ) ) {
				$this->tracking_code = $args['ywot_tracking_code'];
			}

            if ( isset( $args['ywot_tracking_postcode'] ) ) {
                $this->tracking_postcode = $args['ywot_tracking_postcode'];
            }

			if ( isset( $args['ywot_carrier_id'] ) ) {
				$this->carrier_id = $args['ywot_carrier_id'];
			}
			if ( isset( $args['ywot_pick_up_date'] ) ) {
				$this->pickup_date = $args['ywot_pick_up_date'];
			}
			
			if ( isset( $args['ywot_picked_up'] ) ) {
				$picked_up_status = ( 'on' == $args['ywot_picked_up'] ) || ( true == $args['ywot_picked_up'] ) || ( 1 == $args['ywot_picked_up'] );
				$this->pickedup   = $picked_up_status;
			}
		}
		
		public function save() {
			
			if ( $this->order ) {
				
				yit_save_prop( $this->order,
					array(
						'ywot_tracking_code' => $this->tracking_code,
						'ywot_tracking_postcode' => $this->tracking_postcode,
						'ywot_carrier_id'    => $this->carrier_id,
						'ywot_pick_up_date'  => $this->pickup_date,
						'ywot_picked_up'     => $this->pickedup
					) );
			}
		}
	}
}