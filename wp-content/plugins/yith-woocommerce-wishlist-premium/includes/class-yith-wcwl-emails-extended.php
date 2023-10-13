<?php
/**
 * Emails class
 *
 * @package YITH\GiftCards\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_WCWL_Emails_Extended' ) ) {
	/**
	 * YITH_WCWL_Emails_Premium class
	 *
	 * @since   1.0.0
	 * @author  YITH
	 */
	class YITH_WCWL_Emails_Extended extends YITH_WCWL_Emails {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCWL_Emails_Extended
		 * @since 1.0.0
		 */
		public static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author YITH
		 */
		public function __construct() {
			parent::__construct();

			// emails handling.
			add_filter( 'woocommerce_email_classes', array( $this, 'add_woocommerce_emails' ) );
			add_action( 'woocommerce_init', array( $this, 'load_wc_mailer' ) );

			/**
			 * Locate the plugin email templates
			 */
			add_filter( 'woocommerce_locate_core_template', array( $this, 'locate_core_template' ), 10, 3 );

			add_filter( 'woocommerce_locate_template', array( $this, 'locate_core_template' ), 10, 3 );

			// back in stock handling.
			add_action( 'woocommerce_product_set_stock_status', array( $this, 'schedule_back_in_stock_emails' ), 10, 3 );
			add_action( 'woocommerce_variation_set_stock_status', array( $this, 'schedule_back_in_stock_emails' ), 10, 3 );
		}

		/**
		 * Locate default templates of woocommerce in plugin, if exists
		 *
		 * @param string $core_file     Location of core files.
		 * @param string $template      Template to search.
		 * @param string $template_base Template base path.
		 *
		 * @return string
		 * @since  2.0.0
		 */
		public function locate_core_template( $core_file, $template, $template_base ) {
			$located = yith_wcwl_locate_template( $template );

			if ( $located ) {
				return $located;
			} else {
				return $core_file;
			}
		}

		/**
		 * Filters woocommerce available mails, to add wishlist related ones
		 *
		 * @param array $emails Array of available emails.
		 * @return array
		 * @since 2.0.0
		 */
		public function add_woocommerce_emails( $emails ) {
			include YITH_WCWL_INC . 'emails/class.yith-wcwl-mail.php';

			$emails['yith_wcwl_back_in_stock'] = include YITH_WCWL_INC . 'emails/class-yith-wcwl-back-in-stock-email.php';

			return $emails;
		}

		/**
		 * Loads WC Mailer when needed
		 *
		 * @return void
		 * @since 1.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function load_wc_mailer() {
			add_action( 'send_back_in_stock_mail', array( 'WC_Emails', 'send_transactional_email' ), 10, 2 );
		}

		/* === BACK IN STOCK HANDLING === */

		/**
		 * Schedule email sending, when an item is back in stock
		 *
		 * @param int         $product_id Product or variation id.
		 * @param string      $stock_status Product stock status.
		 * @param \WC_Product $product Current product.
		 *
		 * @return void
		 */
		public function schedule_back_in_stock_emails( $product_id, $stock_status, $product ) {
			if ( 'instock' !== $stock_status ) {
				return;
			}

			// skip if email ain't active.
			$email_options = get_option( 'woocommerce_yith_wcwl_back_in_stock_settings', array() );

			if ( ! isset( $email_options['enabled'] ) || 'yes' !== $email_options['enabled'] ) {
				return;
			}

			// skip if product is on exclusion list.
			$product_exclusions = ! empty( $email_options['product_exclusions'] ) ? array_map( 'absint', $email_options['product_exclusions'] ) : false;

			if ( $product_exclusions && in_array( $product_id, $product_exclusions, true ) ) {
				return;
			}

			// skip if product category is on exclusion list.
			$product_categories = $product->get_category_ids();

			if ( ! empty( $email_options['category_exclusions'] ) && array_intersect( $product_categories, $email_options['category_exclusions'] ) ) {
				return;
			}

			// retrieve items.
			$items = YITH_WCWL()->get_products(
				array(
					'user_id'     => false,
					'session_id'  => false,
					'wishlist_id' => 'all',
					'product_id'  => $product_id,
				)
			);

			if ( empty( $items ) ) {
				return;
			}

			// queue handling.
			$queue        = get_option( 'yith_wcwl_back_in_stock_queue', array() );
			$unsubscribed = get_option( 'yith_wcwl_unsubscribed_users', array() );

			foreach ( $items as $item ) {
				$user    = $item->get_user();
				$user_id = $item->get_user_id();

				if ( ! $user ) {
					continue;
				}

				// skip if user unsubscribed.
				if ( in_array( $user->user_email, $unsubscribed, true ) ) {
					continue;
				}

				if ( ! isset( $queue[ $user_id ] ) ) {
					$queue[ $user_id ] = array(
						$item->get_product_id() => $item->get_id(),
					);
				} else {
					$queue[ $user_id ][ $item->get_product_id() ] = $item->get_id();
				}
			}

			update_option( 'yith_wcwl_back_in_stock_queue', $queue );
		}
	}
}
