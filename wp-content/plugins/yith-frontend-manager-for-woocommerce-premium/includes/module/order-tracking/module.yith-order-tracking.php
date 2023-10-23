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
 * @author     YITH <plugins@yithemes.com>
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
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

				add_action( 'yith_wcfm_after_order_details', array( YITH_YWOT(), 'show_order_tracking_metabox' ) );
				add_action( 'yith_wcfm_shop_order_save', array( $this, 'save' ) );
			}
		}

		/**
		 * Enqueue scripts
		 */
		public function enqueue_scripts() {
			$sections       = YITH_Frontend_Manager()->get_section();
			$section_orders = ! empty( $sections['product_orders'] ) ? $sections['product_orders'] : false;

			if ( $section_orders instanceof YITH_Frontend_Manager_Section && $section_orders->is_current() && class_exists( 'YIT_Assets' ) ) {
				if ( ! wp_script_is( 'yith-plugin-fw-fields', 'registered' ) || ! wp_style_is( 'yith-plugin-fw-fields', 'registered' ) ) {
					YIT_Assets::instance()->register_styles_and_scripts();
				}

				wp_enqueue_script( 'yith-plugin-fw-fields' );
				wp_enqueue_style( 'yith-plugin-fw-fields' );

				wp_localize_script(
					'ywot_script',
					'ywot',
					array(
						'is_license_active' => ywot_has_active_license(),
						'is_account_page'   => is_account_page(),
					)
				);

				wp_enqueue_script( 'ywot_script' );
				wp_enqueue_style( 'ywot_style' );
			}
		}

		/**
		 * Save the tracking data on current order
		 *
		 * @param $order WC_Order order to save tracking data
		 * @return void
		 *
		 */
		public function save( $order ) {
			if ( defined( 'YITH_YWOT_PREMIUM' ) ) {
				global $YWOT_Instance;
				$YWOT_Instance->update_order_status( $order );
			}
		}
	}
}

/**
 * Main instance of plugin
 *
 * @return /YITH_Frontend_Manager_Order_Tracking
 * @since  1.4.12
 */
if ( ! function_exists( 'YITH_Frontend_Manager_Order_Tracking' ) ) {
	function YITH_Frontend_Manager_Order_Tracking() {
		return YITH_Frontend_Manager_Order_Tracking::instance();
	}
}
