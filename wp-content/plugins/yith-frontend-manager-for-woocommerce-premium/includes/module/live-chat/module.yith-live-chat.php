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
 * @class      YITH_Frontend_Manager_Live_Chat
 * @package    Yithemes
 * @since      Version 1.2.2
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Frontend_Manager_Live_Chat' ) ) {

	/**
	 * YITH_Frontend_Manager_Live_Chat Class
	 */
	class YITH_Frontend_Manager_Live_Chat {

		/**
		 * Main instance
		 */
		private static $_instance = null;

		/**
		 * Main plugin Instance
		 *
		 * @static
		 * @return YITH_Frontend_Manager_Live_Chat Main instance
		 *
		 * @since  1.2.2
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

			add_filter( 'yith_wcfm_access_capability', array( $this, 'allow_chat_operator_on_front' ) );
			add_filter( 'yith_wcfm_is_section_enabled', array( $this, 'remove_sections' ), 10, 3 );

		}

		/**
		 * Allow chat operator on front
		 *
		 * @static
		 * @return string Chat Operator role
		 *
		 * @since  1.0
		 * @author Alberto Ruggiero
		 */
		public function allow_chat_operator_on_front( $cap ) {

			$user = wp_get_current_user();

			if ( ! current_user_can( 'manage_woocommerce' ) && ( in_array( 'ylc_chat_op', $user->roles ) ) ) {
				$cap = 'answer_chat';
			}

			return $cap;
		}

		public function remove_sections( $is_enabled, $obj, $obj_slug ) {

			$user = wp_get_current_user();

			if ( ! current_user_can( 'manage_woocommerce' ) && ( in_array( 'ylc_chat_op', $user->roles ) ) ) {

				if ( $obj->id != 'live-chat' && $obj->id != 'user-logout' ) {
					$is_enabled = false;
				}


			}

			return $is_enabled;

		}

	}
}

/**
 * Main instance of plugin
 *
 * @return /YITH_Frontend_Manager_Live_Chat
 * @since  1.2.2
 * @author Alberto Ruggiero
 */
if ( ! function_exists( 'YITH_Frontend_Manager_Live_Chat' ) ) {
	function YITH_Frontend_Manager_Live_Chat() {
		return YITH_Frontend_Manager_Live_Chat::instance();
	}
}