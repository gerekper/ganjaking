<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 * @class      YITH_Frontend_Manager_Order_Tracking
 * @package    Yithemes
 * @since      Version 1.4.12
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Frontend_Manager_Order_Tracking' ) ) {

	/**
	 * YITH_Frontend_Manager_Order_Tracking Class
	 */
	class YITH_Frontend_Manager_Order_Tracking {

		/**
		 * Main instance
		 */
		private static $_instance = null;

		/**
		 * Main plugin Instance
		 *
		 * @static
		 * @return YITH_Frontend_Manager_Order_Tracking Main instance
		 *
		 * @since  1.4.12
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Construct
		 */
		public function __construct() {
			if ( function_exists( 'YITH_YWOT' ) ) {
				add_action( 'yith_wcfm_after_order_details', array( YITH_YWOT(), 'show_order_tracking_metabox' ) );
				add_action( 'yith_wcfm_shop_order_save', array( $this, 'save' ) );
			}
		}

		/**
		 * Save the tracking data on current order
		 *
		 * @param $order WC_Order order to save tracking data
		 * @return void
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function save( $order ) {
			if ( class_exists( 'YITH_Tracking_Data' ) ) {
				$YITH_Tracking_Data = new YITH_Tracking_Data( $order );
				$tracking_data      = array(
					'ywot_tracking_code'     => isset( $_POST['ywot_tracking_code'] ) ? $_POST['ywot_tracking_code'] : '',
					'ywot_tracking_postcode' => isset( $_POST['ywot_tracking_postcode'] ) ? $_POST['ywot_tracking_postcode'] : '',
					'ywot_carrier_id'        => isset( $_POST['ywot_carrier_id'] ) ? $_POST['ywot_carrier_id'] : - 1,
					'ywot_pick_up_date'      => isset( $_POST['ywot_pick_up_date'] ) ? $_POST['ywot_pick_up_date'] : '',
					'ywot_picked_up'         => isset( $_POST['ywot_picked_up'] ) && ( ( 'on' == $_POST['ywot_picked_up'] ) || ( true == $_POST['ywot_picked_up'] ) || ( 1 == $_POST['ywot_picked_up'] ) ),
				);

				$YITH_Tracking_Data->set( $tracking_data );
				$YITH_Tracking_Data->save();
			}
		}
	}
}

/**
 * Main instance of plugin
 *
 * @return /YITH_Frontend_Manager_Order_Tracking
 * @since  1.4.12
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if ( ! function_exists( 'YITH_Frontend_Manager_Order_Tracking' ) ) {
	function YITH_Frontend_Manager_Order_Tracking() {
		return YITH_Frontend_Manager_Order_Tracking::instance();
	}
}