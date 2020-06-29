<?php
/**
 * Main Premium class
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

if ( ! class_exists( 'YITH_WCAF_Premium' ) ) {
	/**
	 * WooCommerce Affiliates Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Premium extends YITH_WCAF {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCAF
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAF_Premium
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();

			// register shortcodes
			remove_action( 'init', array( 'YITH_WCAF_Shortcode', 'init' ), 5 );
			add_action( 'init', array( 'YITH_WCAF_Shortcode_Premium', 'init' ), 5 );

			// emails init
			add_filter( 'woocommerce_email_classes', array( $this, 'register_email_classes' ) );
			add_filter( 'woocommerce_email_actions', array( $this, 'register_email_actions' ) );
			add_filter( 'woocommerce_locate_core_template', array( $this, 'register_woocommerce_template' ), 10, 3 );

			// YITH SBS compatibility methods
			add_filter( 'ywsbs_renew_order_item_meta_data', array(
				$this,
				'remove_subscription_renew_order_item_meta'
			), 10, 3 );
		}

		/* === INSTALL METHODS === */

		/**
		 * Execute plugin installation
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function install() {
			$this->_install_tables();
			$this->_install_folders();

			if ( is_admin() ) {
				$this->_install_pages();
			}

			// init affiliate
			YITH_WCAF_Affiliate_Premium();

			// init handlers
			YITH_WCAF_Affiliate_Handler_Premium();
			YITH_WCAF_Click_Handler_Premium();
			YITH_WCAF_Rate_Handler_Premium();
			YITH_WCAF_Commission_Handler_Premium();
			YITH_WCAF_Payment_Handler_Premium();
			YITH_WCAF_Coupon_Handler();

			/**
			 * @since 1.2.0 Added Role affiliate
			 */
			$this->_install_role();

			/**
			 * @since 1.3.0 Moved before standby, to let other part of the application filter endpoints list before init
			 */
			$this->_install_endpoints();

			do_action( 'yith_wcaf_standby' );
		}

		/**
		 * Install folders and protect them
		 *
		 * @return void
		 * @since 1.3.0
		 */
		protected function _install_folders() {
			if ( ! file_exists( YITH_WCAF_INVOICES_DIR ) ) {
				$files = array(
					array(
						'base'    => YITH_WCAF_INVOICES_DIR,
						'file'    => 'index.html',
						'content' => '',
					),
					array(
						'base'    => YITH_WCAF_INVOICES_DIR,
						'file'    => '.htaccess',
						'content' => 'deny from all',
					)
				);


				foreach ( $files as $file ) {
					if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
						if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {
							fwrite( $file_handle, $file['content'] );
							fclose( $file_handle );
						}
					}
				}
			}
		}

		/* === WC EMAILS === */

		/**
		 * Register email classes for affiliate
		 *
		 * @param $classes mixed Array of email class instances
		 *
		 * @return mixed Filtered array of email class instances
		 * @since 1.0.0
		 */
		public function register_email_classes( $classes ) {
			$classes['YITH_WCAF_Admin_New_Affiliate_Email']         = include_once( YITH_WCAF_INC . 'emails/class.yith-wcaf-admin-new-affiliate-email.php' );
			$classes['YITH_WCAF_Admin_Pending_Commission_Email']    = include_once( YITH_WCAF_INC . 'emails/class.yith-wcaf-admin-pending-commission-email.php' );
			$classes['YITH_WCAF_Admin_Paid_Commission_Email']       = include_once( YITH_WCAF_INC . 'emails/class.yith-wcaf-admin-paid-commission-email.php' );
			$classes['YITH_WCAF_Customer_Status_Change_Email']      = include_once( YITH_WCAF_INC . 'emails/class.yith-wcaf-customer-status-change-email.php' );
			$classes['YITH_WCAF_Customer_Ban_Email']                = include_once( YITH_WCAF_INC . 'emails/class.yith-wcaf-customer-ban-email.php' );
			$classes['YITH_WCAF_Customer_New_Coupon_Email']         = include_once( YITH_WCAF_INC . 'emails/class.yith-wcaf-customer-new-coupon-email.php' );
			$classes['YITH_WCAF_Customer_Pending_Commission_Email'] = include_once( YITH_WCAF_INC . 'emails/class.yith-wcaf-customer-pending-commission-email.php' );
			$classes['YITH_WCAF_Customer_Paid_Commission_Email']    = include_once( YITH_WCAF_INC . 'emails/class.yith-wcaf-customer-paid-commission-email.php' );

			return $classes;
		}

		/**
		 * Register email action for affiliate
		 *
		 * @param $emails mixed Array of registered actions
		 *
		 * @return mixed Filtered array of registered actions
		 * @since 1.0.0
		 */
		public function register_email_actions( $emails ) {
			$emails = array_merge(
				$emails,
				array(
					'yith_wcaf_new_affiliate',
					'yith_wcaf_affiliate_status_updated',
					'yith_wcaf_affiliate_banned',
					'yith_wcaf_affiliate_coupon_saved',
					'yith_wcaf_paid_commission',
					'yith_wcaf_commission_status_pending',
					'yith_wcaf_commission_status_paid',
					'yith_wcaf_payments_sent',
					'yith_wcaf_payment_sent'
				)
			);

			return $emails;
		}

		/**
		 * Locate default templates of woocommerce in plugin, if exists
		 *
		 * @param $core_file     string
		 * @param $template      string
		 * @param $template_base string
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function register_woocommerce_template( $core_file, $template, $template_base ) {
			$located = yith_wcaf_locate_template( $template );

			if ( $located && file_exists( $located ) ) {
				return $located;
			} else {
				return $core_file;
			}
		}

		/* === YITH SUBSCRIPTION COMPATIBILITY === */

		/**
		 * Return false to avoid YITH Subscription cloning specific affiliates meta into renew orders
		 *
		 * @param $register      bool Whether to register meta to renew or not
		 * @param $order_item_id int Order item id
		 * @param $meta_key      string Order item meta key
		 *
		 * @return bool Whether to register meta to renew order
		 * @since 1.2.4
		 */
		public function remove_subscription_renew_order_item_meta( $register, $order_item_id, $meta_key ) {
			if ( in_array( $meta_key, array(
				'_yith_wcaf_commission_id',
				'_yith_wcaf_commission_rate',
				'_yith_wcaf_commission_amount'
			) ) ) {
				return false;
			}

			return $register;
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAF
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
 * Unique access to instance of YITH_WCAF_Premium class
 *
 * @return \YITH_WCAF_Premium
 * @since 1.0.0
 */
function YITH_WCAF_Premium() {
	return YITH_WCAF_Premium::get_instance();
}