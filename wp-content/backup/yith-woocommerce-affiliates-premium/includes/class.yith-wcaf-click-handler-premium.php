<?php
/**
 * Click Handler Premium class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Click_Handler_Premium' ) ) {
	/**
	 * WooCommerce Click Handler Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Click_Handler_Premium extends YITH_WCAF_Click_Handler {

		/**
		 * Single instance of the class for each token
		 *
		 * @var \YITH_WCAF_Click_Handler_Premium
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAF_Click_Handler_Premium
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();

			add_filter( 'yith_wcaf_general_settings', array( $this, 'filter_general_settings' ) );

			// handle automatic log deletion
			add_action( 'wp', array( $this, 'delete_clicks_setup_schedule' ) );
			add_action( 'delete_clicks_action_schedule', array( $this, 'delete_clicks_do_schedule' ) );

			// add hits panel handling
			add_action( 'yith_wcaf_click_panel', array( $this, 'print_click_panel' ) );
			add_action( 'current_screen', array( $this, 'add_screen_option' ) );
			add_action( 'admin_init', array( $this, 'delete_affiliate_hits' ) );
			add_filter( 'manage_yith-plugins_page_yith_wcaf_panel_columns', array( $this, 'add_screen_columns' ) );
			add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
		}

		/**
		 * Filter general settings, to add clicks settings
		 *
		 * @param $settings mixed Original settings array
		 *
		 * @return mixed Filtered settings array
		 * @since 1.0.0
		 */
		public function filter_general_settings( $settings ) {
			$premium_settings = array(
				'click-options' => array(
					'title' => __( 'Click log', 'yith-woocommerce-affiliates' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'yith_wcaf_click_options'
				),

				'click-enabled' => array(
					'title'   => __( 'Enable click registering', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Enable click registering. <small>(Note that visitors IP will be registered within your db)</small>', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_click_enabled',
					'default' => 'yes'
				),

				'click-resolution' => array(
					'title'             => __( 'Hit resolution', 'yith-woocommerce-affiliates' ),
					'type'              => 'number',
					'desc'              => __( 'Number of seconds after which a new visit of the same user with the same refer id is counted as a new hit', 'yith-woocommerce-affiliates' ),
					'id'                => 'yith_wcaf_click_resolution',
					'css'               => 'min-width: 50px;',
					'default'           => 60,
					'custom_attributes' => array(
						'min'  => 1,
						'step' => 1
					),
					'desc_tip'          => true
				),

				'click-auto-delete' => array(
					'title'   => __( 'Auto delete click log', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Select whether to delete automatically click log or not.', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_click_auto_delete',
					'default' => 'no'
				),

				'click-auto-delete-older-than' => array(
					'title'             => __( 'Auto delete older click', 'yith-woocommerce-affiliates' ),
					'type'              => 'number',
					'desc'              => __( 'Number of days after which a click should be deleted', 'yith-woocommerce-affiliates' ),
					'id'                => 'yith_wcaf_click_auto_delete_expiration',
					'css'               => 'min-width: 100px;',
					'default'           => 30,
					'custom_attributes' => array(
						'min'  => 1,
						'max'  => 9999999,
						'step' => 1
					),
					'desc_tip'          => true
				),

				'click-options-end' => array(
					'type' => 'sectionend',
					'id'   => 'yith_wcaf_click_options'
				),
			);

			$settings['settings'] = yith_wcaf_append_items( $settings['settings'], 'commission-options-end', $premium_settings );

			return $settings;
		}

		/**
		 * Schedule clicks delete cron
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function delete_clicks_setup_schedule() {
			$schedule     = get_option( 'yith_wcaf_click_auto_delete', 'no' );
			$is_scheduled = $schedule == 'yes';

			if ( ! $is_scheduled ) {
				wp_clear_scheduled_hook( 'delete_clicks_action_schedule' );
			} elseif ( ! wp_next_scheduled( 'delete_clicks_action_schedule' ) ) {
				wp_schedule_event( time(), 'daily', 'delete_clicks_action_schedule' );
			}
		}

		/**
		 * Execute periodically clicks deletion
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function delete_clicks_do_schedule() {
			$schedule   = get_option( 'yith_wcaf_click_auto_delete', 'no' );
			$expiration = get_option( 'yith_wcaf_click_auto_delete_expiration', 30 );

			if ( $schedule == 'no' ) {
				return;
			}

			$time = sprintf( '-%d day', $expiration );
			$this->delete_hits( array( 'time' => $time ) );
		}

		/* === PANEL CLICK METHODS === */

		/**
		 * Print click panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_click_panel() {
			// define variables used in the template
			$clicks_table = new YITH_WCAF_Clicks_Table();

			// prepare table for view
			$clicks_table->prepare_items();

			// require rate panel template
			include( YITH_WCAF_DIR . 'templates/admin/click-panel.php' );
		}

		/**
		 * Add Screen option
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_screen_option() {
			if ( 'yith-plugins_page_yith_wcaf_panel' == get_current_screen()->id && isset( $_GET['tab'] ) && $_GET['tab'] == 'clicks' ) {
				add_screen_option( 'per_page', array(
					'label'   => __( 'Clicks', 'yith-woocommerce-affiliates' ),
					'default' => 20,
					'option'  => 'edit_clicks_per_page'
				) );

			}
		}

		/**
		 * Save custom screen options
		 *
		 * @param $set    bool Value to filter (default to false)
		 * @param $option string Custom screen option key
		 * @param $value  mixed Custom screen option value
		 *
		 * @return mixed Value to be saved as user meta; false if no value should be saved
		 */
		public function set_screen_option( $set, $option, $value ) {
			return ( isset( $_GET['tab'] ) && 'clicks' == $_GET['tab'] && 'edit_clicks_per_page' == $option ) ? $value : $set;
		}

		/**
		 * Add columns filters to commissions page
		 *
		 * @param $columns mixed Available columns
		 *
		 * @return mixed The columns array to print
		 * @since 1.0.0
		 */
		public function add_screen_columns( $columns ) {
			if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'clicks' ) {
				$columns = array_merge(
					$columns,
					array(
						'status'    => __( 'Status', 'yith-woocommerce-affiliates' ),
						'referrer'  => __( 'Referrer', 'yith-woocommerce-affiliates' ),
						'order'     => __( 'Order', 'yith-woocommerce-affiliates' ),
						'link'      => __( 'Followed URL', 'yith-woocommerce-affiliates' ),
						'origin'    => __( 'Origin URL', 'yith-woocommerce-affiliates' ),
						'date'      => __( 'Date', 'yith-woocommerce-affiliates' ),
						'conv_time' => __( 'Conversion time', 'yith-woocommerce-affiliates' )
					)
				);
			}

			return $columns;
		}

		/**
		 * Delete Hits by affiliate id
		 *
		 * @return void
		 * @since 1.0.10
		 */
		public function delete_affiliate_hits() {
			if ( isset( $_GET['yith_delete_affiliate_log'] ) && intval( $_GET['yith_delete_affiliate_log'] ) ) {
				$this->delete_hits( array( 'affiliate_id' => intval( $_GET['yith_delete_affiliate_log'] ) ) );

				wp_redirect( esc_url_raw( add_query_arg( array(
					'page' => 'yith_wcaf_panel',
					'tab'  => 'clicks'
				), admin_url( 'admin.php' ) ) ) );
				die();
			}
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAF_Click_Handler_Premium
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}
	}
}

/**
 * Unique access to instance of YITH_WCAF_Click_Handler_Premium class
 *
 * @return \YITH_WCAF_Click_Handler_Premium
 * @since 1.0.0
 */
function YITH_WCAF_Click_Handler_Premium() {
	return YITH_WCAF_Click_Handler_Premium::get_instance();
}