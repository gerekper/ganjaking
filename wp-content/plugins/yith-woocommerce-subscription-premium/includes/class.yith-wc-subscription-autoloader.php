<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Autoloader class. This is used to decrease memory consumption
 *
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_WC_Subscription_Autoloader' ) ) {
	/**
	 * Class YITH_WC_Subscription_Autoloader
	 *
	 * @since 2.0.0
	 */
	class YITH_WC_Subscription_Autoloader {


		/**
		 * Constructor
		 *
		 * @since 2.0.0
		 */
		public function __construct() {
			if ( function_exists( '__autoload' ) ) {
				spl_autoload_register( '__autoload' );
			}

			spl_autoload_register( array( $this, 'autoload' ) );
		}

		/**
		 * Autoload callback
		 *
		 * @param  string $class Load the class.
		 * @since  2.0.0
		 */
		public function autoload( $class ) {
			$class         = strtolower( $class );
			$file          = 'class.' . str_replace( '_', '-', $class ) . '.php';
			$privacy_files = array( 'class.ywsbs-subscription-privacy.php', 'class.yith-ywsbs-privacy-dpa.php' );
			$admin_files   = array(
				'class.ywsbs-product-post-type-admin.php',
				'class.ywsbs-shop-order-post-type-admin.php',
				'class.ywsbs-subscription-post-type-admin.php',
				'class.ywsbs-subscription-list-table.php',
				'class.yith-ywsbs-activities-list-table.php',
				'class.ywsbs-delivery-schedules-list-table.php',
			);
			$path          = YITH_YWSBS_INC;

			if ( in_array( $file, $privacy_files, true ) ) {
				$path .= 'privacy/';
			}

			if ( in_array( $file, $admin_files, true ) ) {
				$path .= 'admin/';
			}

			if ( file_exists( $path . $file ) && is_readable( $path . $file ) ) {
				include_once $path . $file;
			}

		}
	}
}

new YITH_WC_Subscription_Autoloader();
