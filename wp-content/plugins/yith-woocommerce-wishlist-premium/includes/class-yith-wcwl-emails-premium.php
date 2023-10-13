<?php
/**
 * Emails class
 *
 * @package YITH\GiftCards\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_WCWL_Emails_Premium' ) ) {
	/**
	 * YITH_WCWL_Emails_Premium class
	 *
	 * @since   1.0.0
	 * @author  YITH
	 */
	class YITH_WCWL_Emails_Premium extends YITH_WCWL_Emails_Extended {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCWL_Emails_Premium
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
		public function __construct() { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
			parent::__construct();
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

			$emails['yith_wcwl_back_in_stock']  = include YITH_WCWL_INC . 'emails/class-yith-wcwl-back-in-stock-email.php';
			$emails['estimate_mail']            = include YITH_WCWL_INC . 'emails/class-yith-wcwl-estimate-email.php';
			$emails['yith_wcwl_promotion_mail'] = include YITH_WCWL_INC . 'emails/class-yith-wcwl-promotion-email.php';
			$emails['yith_wcwl_on_sale_item']   = include YITH_WCWL_INC . 'emails/class-yith-wcwl-on-sale-item-email.php';

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
			parent::load_wc_mailer();

			add_action( 'send_estimate_mail', array( 'WC_Emails', 'send_transactional_email' ), 10, 4 );
			add_action( 'send_promotion_mail', array( 'WC_Emails', 'send_transactional_email' ), 10, 2 );
			add_action( 'send_on_sale_item_mail', array( 'WC_Emails', 'send_transactional_email' ), 10, 2 );
		}
	}
}
