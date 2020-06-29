<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Stripe
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCSTRIPE' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCStripe_Blacklist_Admin' ) ) {
	/**
	 * Blacklist Admin Pages
	 *
	 * @since 1.1.3
	 */
	class YITH_WCStripe_Blacklist_Admin {

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCStripe_Blacklist_Admin
		 * @since 1.1.3
		 */
		public function __construct() {
			//add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_style' ), 15 );
			add_filter( 'woocommerce_screen_ids', array( $this, 'set_blacklist_table_wc_page' ) );
			add_action( 'admin_init', array( $this, 'blacklist_table_actions' ) );

			// YITH Plugins tab
			add_filter( 'yith_stripe_admin_panels', array( $this, 'add_stripe_panel' ) );
			add_action( 'yith_wcstripe_blacklist_tab', array( $this, 'blacklist_page' ) );
		}

		/**
		 * Add the Commissions menu item in dashboard menu
		 *
		 * @return void
		 * @since  1.1.2
		 */
		public function add_menu_item() {
			$args = array(
				'page_title' => __( 'Stripe blacklist', 'yith-woocommerce-stripe' ),
				'menu_title' => __( 'Stripe blacklist', 'yith-woocommerce-stripe' ),
				'capability' => 'manage_woocommerce',
				'menu_slug'  => 'yith_stripe_blacklist',
				'function'   => array( $this, 'blacklist_page' ),
			);

			extract( $args );

			add_submenu_page( 'woocommerce', $page_title, $menu_title, $capability, $menu_slug, $function );
		}

		/**
		 * Add the panel into the YITH Plugins admin page
		 *
		 * @param array $panels
		 *
		 * @return array
		 * @since 1.1.3
		 *
		 */
		public function add_stripe_panel( $panels = array() ) {
			$panels['blacklist'] = __( 'Blacklist', 'yith-woocommerce-stripe' );

			return $panels;
		}

		/**
		 * Show the Commissions page
		 *
		 * @return void
		 * @since  1.1.2
		 */
		public function blacklist_page() {
			if ( ! class_exists( 'WP_List_Table' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
			}

			include_once( 'class-yith-stripe-blacklist-table.php' );

			/** @var YITH_Stripe_Blacklist_Table $blacklist_table */
			$blacklist_table = new YITH_Stripe_Blacklist_Table();
			$blacklist_table->prepare_items();

			include( YITH_WCSTRIPE_DIR . 'templates/admin/blacklist-table.php' );
		}

		/**
		 * Check if the current admin page is the blacklist page
		 *
		 * @return bool
		 * @since 1.1.3
		 */
		public function is_blacklist_page() {
			$screen = get_current_screen();

			return strpos( $screen->id, 'yith_stripe_blacklist' ) !== false || isset( $_GET['page'] ) && 'yith_wcstripe_panel' == $_GET['page'] && isset( $_GET['tab'] ) && 'blacklist' == $_GET['tab'];
		}

		/**
		 * Check if the current admin page is the blacklist page
		 *
		 * @return bool
		 * @since 1.1.3
		 */
		public function blacklist_page_url() {
			if ( isset( $_GET['page'] ) && 'yith_wcstripe_panel' == $_GET['page'] && isset( $_GET['tab'] ) && 'blacklist' == $_GET['tab'] ) {
				return admin_url( 'admin.php?page=yith_wcstripe_panel&tab=blacklist' );
			} else {
				return admin_url( 'admin.php?page=yith_stripe_blacklist' );
			}
		}

		/**
		 * Include CSS
		 *
		 * @return void
		 * @since 1.1.3
		 */
		public function enqueue_style() {
			if ( ! $this->is_blacklist_page() ) {
				return;
			}

			wp_enqueue_style( 'blacklist-admin', YITH_WCSTRIPE_URL . 'assets/css/admin.css' );
		}

		/**
		 * Set the page with blacklist table as woocommerce admin page
		 *
		 * @param $screen_ids
		 *
		 * @return array
		 * @since 1.1.3
		 */
		public function set_blacklist_table_wc_page( $screen_ids ) {
			$wc_screen_id = sanitize_title( __( 'WooCommerce', 'woocommerce' ) );

			$screen_ids[] = $wc_screen_id . '_page_yith_stripe_blacklist';
			$screen_ids[] = $wc_screen_id . '_page_yith_stripe_blacklist';

			return $screen_ids;
		}

		/**
		 * Blacklist table actions
		 *
		 * @since 1.1.3
		 */
		public function blacklist_table_actions() {
			if ( empty( $_GET['id'] ) || empty( $_GET['action'] ) || empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'stripe_blacklist_action' ) ) {
				return;
			}

			if ( 'unban' == $_GET['action'] ) {
				global $wpdb;
				$wpdb->update( $wpdb->yith_wc_stripe_blacklist, array( 'unbanned' => 1 ), array( 'ID' => intval( $_GET['id'] ) ) );
			} elseif ( 'ban' == $_GET['action'] ) {
				global $wpdb;
				$wpdb->update( $wpdb->yith_wc_stripe_blacklist, array( 'unbanned' => 0 ), array( 'ID' => intval( $_GET['id'] ) ) );
			}

			wp_safe_redirect( remove_query_arg( array( 'action', 'id', '_wpnonce' ) ) );
			exit();
		}

	}
}

new YITH_WCStripe_Blacklist_Admin();