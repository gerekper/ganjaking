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

if ( ! class_exists( 'YITH_Frontend_Manager_Section_Live_Chat' ) && defined( 'YLC_PREMIUM' ) ) {

	class YITH_Frontend_Manager_Section_Live_Chat extends YITH_WCFM_Section {

		/**
		 * $this @var YITH_Frontend_Manager_Section
		 */

		/**
		 * Constructor method
		 *
		 * @return \YITH_Frontend_Manager_Section
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->id                    = 'live-chat';
			$this->_default_section_name = _x( 'Live Chat', '[Frontend]: Live Chat menu item', 'yith-frontend-manager-for-woocommerce' );

			$this->_subsections = array(

				'chat-console' => array(
					'slug' => $this->get_option( 'slug', $this->id . '_chat-console', 'chat-console' ),
					'name' => __( 'Chat Console', 'yith-frontend-manager-for-woocommerce' )
				),

				'offline-messages' => array(
					'slug' => $this->get_option( 'slug', $this->id . '_offline-messages', 'offline-messages' ),
					'name' => __( 'Offline Messages', 'yith-frontend-manager-for-woocommerce' )
				),

				'chat-logs' => array(
					'slug' => $this->get_option( 'slug', $this->id . '_chat-logs', 'chat-logs' ),
					'name' => __( 'Chat Logs', 'yith-frontend-manager-for-woocommerce' )
				),

				'ylc-macros' => array(
					'slug' => $this->get_option( 'slug', $this->id . '_ylc-macros', 'ylc-macros' ),
					'name' => __( 'Chat Macros', 'yith-frontend-manager-for-woocommerce' )
				),

				'ylc-macro' => array(
					'slug' => $this->get_option( 'slug', $this->id . '_ylc-macro', 'ylc-macro' ),
					'name' => __( 'Add Macro', 'yith-frontend-manager-for-woocommerce' )
				),

			);

			include_once( YLC_DIR . 'includes/admin/class-yith-custom-table.php' );
			include_once( YLC_TEMPLATE_PATH . '/admin/ylc-offline-table.php' );
			include_once( YLC_TEMPLATE_PATH . '/admin/ylc-chat-log-table.php' );

			if ( ! class_exists( 'WP_Posts_List_Table' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/class-wp-posts-list-table.php' );
			}

			require_once( YITH_WCFM_LIB_PATH . 'class.yith-frontend-manager-chat-macro-list-table.php' );

			/*
			 *  Construct
			 */
			parent::__construct();
		}

		/* === SECTION METHODS === */

		/**
		 * Print shortcode function
		 *
		 * @author Alberto Ruggiero
		 * @return void
		 * @since  1.0.0
		 */
		public function print_shortcode( $atts = array(), $content = '', $tag ) {
			$section           = $this->id;
			$subsection_prefix = $this->get_shortcodes_prefix() . $section;
			$subsection        = $tag != $subsection_prefix ? str_replace( $subsection_prefix . '_', '', $tag ) : $section;
			$atts              = array( 'section_obj' => $this, 'section' => $section, 'subsection' => $subsection );

			if ( apply_filters( 'yith_wcfm_print_live_chat_section', true, $subsection, $section, $atts ) ) {
				$this->print_section( $subsection, $section, $atts );
			} else {
				do_action( 'yith_wcfm_print_section_unauthorized', $this->id );
			}
		}

		public function print_section( $subsection = '', $section = '', $atts = array() ) {
			if ( ! is_user_logged_in() ) {
				return;
			}


			if ( $this->is_enabled() ) {

				switch ( $subsection ) {
					case 'offline-messages':
						$GLOBALS['hook_suffix'] = 'offline-messages';
						add_filter( 'yith_wcfm_offline_messages_url', array( $this, 'get_subsection_url' ), 10, 2 );
						add_filter( 'yith_wcfm_offline_messages_hide', '__return_true' );
						YLC_Offline_Messages()->output();
						break;

					case 'chat-logs':
						$GLOBALS['hook_suffix'] = 'chat-logs';
						add_filter( 'yith_wcfm_chat_log_url', array( $this, 'get_subsection_url' ), 10, 2 );
						add_filter( 'yith_wcfm_chat_log_hide', '__return_true' );
						YLC_Chat_Logs()->output();
						break;

					case 'ylc-macro':
					case 'ylc-macros':
						yith_wcfm_get_template( $subsection, $atts, 'sections/' . $section );
						break;

					default:

						YITH_Live_Chat()->get_console_template();
				}

			} else {
				do_action( 'yith_wcfm_print_section_unauthorized', $this->id );
			}

		}

		function get_subsection_url( $value, $subsection ) {
			return $this->get_url( $this->_subsections[ $subsection ]['slug'] );
		}

		/**
		 * get the edit post link for frontend
		 *
		 * @author Andrea Grillo    <andrea.grillo@yithemes.com>
		 * @return string post link
		 * @since  1.0.0
		 */
		public static function get_edit_product_link( $macro_id ) {
			return add_query_arg( array( 'macro_id' => $macro_id, ), yith_wcfm_get_section_url( 'live-chat', 'ylc-macro' ) );
		}

		/**
		 * WP Enqueue Scripts
		 *
		 * @author Alberto Ruggiero
		 * @return void
		 * @since  1.0.0
		 */
		public function enqueue_section_scripts() {

			wp_enqueue_style( 'yith-wcfm-live-chat', YITH_WCFM_URL . 'assets/css/live-chat.css', array(), YITH_WCFM_VERSION );

			if ( version_compare( YLC_VERSION, '1.4.0', '>=' ) ) {
				YITH_Live_Chat()->register_styles_scripts( true );
			} else {
				YITH_Live_Chat()->premium_admin_scripts();
			}


			switch ( $this->get_current_subsection( true ) ) {

				case 'chat-logs':
				case 'offline-messages':

					if ( version_compare( YLC_VERSION, '1.4.0', '<' ) ) {
						YITH_Live_Chat()->load_fontawesome();
					}

					wp_enqueue_style( 'ylc-tiptip' );
					wp_enqueue_style( 'ylc-styles' );

					wp_enqueue_script( 'jquery-tiptip' );
					wp_enqueue_script( 'ylc-admin-premium-table' );

					break;

				case 'ylc-macro':
				case 'ylc-macros':
					break;
				default:

					if ( version_compare( YLC_VERSION, '1.4.0', '<' ) ) {
						YITH_Live_Chat()->admin_frontend_scripts();

					}

					wp_enqueue_style( 'select2' );
					wp_enqueue_style( 'ylc-console' );

					wp_enqueue_script( 'select2' );
					wp_enqueue_script( 'ylc-engine-console' );

			}

		}

		/**
		 * Print an admin notice
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.3.3
		 * @return void
		 * @use    admin_notices hooks
		 */
		public function show_wc_notice( $message = 'success' ) {
			switch ( $message ) {
				case 'success':
					$message = __( 'Macro Saved', 'yith-frontend-manager-for-woocommerce' );
					$type    = 'success';
					break;

				case 'error':
					$message = __( 'Unable to save macro', 'yith-frontend-manager-for-woocommerce' );
					$type    = 'error';
					break;
			}

			if ( ! empty( $message ) ) {
				wc_print_notice( $message, $type );
			}
		}

	}

}
