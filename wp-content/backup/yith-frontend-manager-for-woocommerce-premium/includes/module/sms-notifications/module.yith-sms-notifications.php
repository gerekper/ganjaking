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
 * @class      YITH_Frontend_Manager_SMS_Notifications
 * @package    Yithemes
 * @since      Version 1.4.12
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Frontend_Manager_SMS_Notifications' ) ) {

	/**
	 * YITH_Frontend_Manager_SMS_Notifications Class
	 */
	class YITH_Frontend_Manager_SMS_Notifications {

		/**
		 * Main instance
		 */
		private static $_instance = null;

		/**
		 * Main plugin Instance
		 *
		 * @static
		 * @return YITH_Frontend_Manager_SMS_Notifications Main instance
		 *
		 * @since  1.4.12
		 * @author Alberto Ruggiero
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

			add_filter( 'yith_wcfm_section_option_type', 'YITH_Frontend_Manager_SMS_Notifications::section_option_type', 11, 2 );
			add_filter( 'yith_wcfm_section_option_title', 'YITH_Frontend_Manager_SMS_Notifications::section_option_title', 11, 2 );

			if(function_exists('YITH_WSN')){
				add_action( 'yith_wcfm_orders_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
			}


		}

		/**
		 * @param $type
		 *
		 * @return string
		 */
		public static function section_option_type( $type, $section_obj ) {

			if ( ! empty( $section_obj ) && 'sms-vendor-panel' == $section_obj->id ) {

				$type = 'checkbox';
			}


			return $type;
		}

		/**
		 * @param $type
		 *
		 * @return string
		 */
		public static function section_option_title( $title, $section_obj ) {
			if ( ! empty( $section_obj ) && 'sms-vendor-panel' == $section_obj->id ) {
				$title = sprintf( '%s (%s)', $title, __( 'only available for vendors', 'yith-frontend-manager-for-woocommerce' ) );
			}

			return $title;
		}

		public function enqueue_scripts() {
			YITH_WSN()->admin_scripts();
		}


	}
}

/**
 * Main instance of plugin
 *
 * @return /YITH_Frontend_Manager_SMS_Notifications
 * @since  1.4.12
 * @author Alberto Ruggiero
 */
if ( ! function_exists( 'YITH_Frontend_Manager_SMS_Notifications' ) ) {
	function YITH_Frontend_Manager_SMS_Notifications() {
		return YITH_Frontend_Manager_SMS_Notifications::instance();
	}
}