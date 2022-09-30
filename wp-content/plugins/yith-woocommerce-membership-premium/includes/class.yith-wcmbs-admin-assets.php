<?php
/**
 * Assets Admin class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Membership
 * @version 1.0.0
 */


if ( ! defined( 'YITH_WCMBS' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Admin_Assets' ) ) {
	/**
	 * YITH WooCommerce Membership Assets Admin
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMBS_Admin_Assets {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCMBS_Admin_Assets
		 * @since 1.0.0
		 */
		protected static $instance;


		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCMBS_Admin_Assets
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		private function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 20 );

			add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ), 99, 1 );
		}

		/**
		 * Add custom screen ids to standard WC
		 *
		 * @access public
		 *
		 * @param array $screen_ids
		 *
		 * @return array
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function add_screen_ids( $screen_ids ) {
			// used for example to include tip-tip css style
			$screen_ids[] = 'edit-yith-wcmbs-plan';
			$screen_ids[] = 'yith-wcmbs-plan';
			$screen_ids[] = 'users';

			return $screen_ids;
		}

		public function admin_scripts() {
			global $pagenow;
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_style( 'yith-wcmbs-admin-styles', YITH_WCMBS_ASSETS_URL . '/css/admin.css', array(), YITH_WCMBS_VERSION );
			wp_register_style( 'yith-wcmbs-membership-statuses', YITH_WCMBS_ASSETS_URL . '/css/membership-statuses.css', array(), YITH_WCMBS_VERSION );


			wp_register_script( 'yith-wcmbs-admin', YITH_WCMBS_ASSETS_URL . '/js/admin' . $suffix . '.js', array( 'jquery', 'jquery-tiptip', 'jquery-ui-sortable', 'select2', 'jquery-ui-tabs', 'wp-color-picker', 'jquery-ui-datepicker', 'wp-util' ), YITH_WCMBS_VERSION, true );
			wp_localize_script( 'yith-wcmbs-admin', 'yith_wcmbs_admin', array(
				'customer_nonce' => wp_create_nonce( 'search-customers' ),
			) );

			wp_register_script( 'yith-wcmbs-admin-protected-links', YITH_WCMBS_ASSETS_URL . '/js/admin-protected-links' . $suffix . '.js', array( 'jquery', 'wc-enhanced-select', 'wp-util' ), YITH_WCMBS_VERSION, true );
			wp_localize_script( 'yith-wcmbs-admin-protected-links', 'yith_wcmbs_protected_links_params', array(
				'i18n' => array(
					'uploadFileTitle'      => esc_attr__( 'Upload File', 'yith-woocommerce-membership' ),
					'uploadFileButtonText' => esc_attr__( 'Use this file', 'yith-woocommerce-membership' ),
				),
			) );

			$protected_link_post_types = apply_filters( 'yith_wcmbs_protected_link_post_types', array( 'post', 'page', 'product' ) );

			if ( $this->is( 'panel' ) || in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php', 'upload.php' ) ) || $this->is( array( 'users', 'user-edit', 'profile' ) ) ) {
				wp_enqueue_style( 'yith-wcmbs-admin-styles' );
				wp_enqueue_style( 'yith-wcmbs-membership-statuses' );
				wp_enqueue_script( 'yith-wcmbs-admin' );
			}

			if ( $this->is( $protected_link_post_types ) ) {
				wp_enqueue_script( 'yith-wcmbs-admin-protected-links' );
				wp_enqueue_style( 'woocommerce_admin_styles' );
			}

			if ( $this->is( YITH_WCMBS_Manager()->post_types ) ) {
				wp_enqueue_style( 'yith-plugin-fw-fields' );
				wp_enqueue_script( 'yith-plugin-fw-fields' );
			}
		}

		/**
		 * @param array|string $id
		 * @param string       $arg
		 *
		 * @return bool
		 */
		public function is( $id, $arg = '' ) {
			$panel_page = 'yith_wcmbs_panel';
			$screen     = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			$screen_id  = $screen ? $screen->id : false;
			$value      = false;

			$panel_cpt_screen_ids = array(
				YITH_WCMBS_Post_Types::$membership,
				YITH_WCMBS_Post_Types::$plan,
				YITH_WCMBS_Post_Types::$thread,
				'edit-' . YITH_WCMBS_Post_Types::$membership,
				'edit-' . YITH_WCMBS_Post_Types::$plan,
				'edit-' . YITH_WCMBS_Post_Types::$thread,
			);
			switch ( $id ) {
				case 'panel':
					$is_panel = strpos( $screen_id, 'page_' . $panel_page ) > - 1;
					$is_cpt   = in_array( $screen_id, $panel_cpt_screen_ids );
					if ( $is_panel || $is_cpt ) {
						$value = true;
						if ( $is_panel && ! ! $arg ) {
							$value = isset( $_GET['tab'] ) && $_GET['tab'] === $arg;
						}
					}

					break;
				default:
					if ( is_array( $id ) ) {
						$value = in_array( $screen_id, $id );
					} elseif ( $id === $screen_id ) {
						$value = true;
					}
					break;
			}

			return $value;
		}
	}
}