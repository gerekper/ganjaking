<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Implements YITH WooCommerce Subscription Database Class
 *
 * @class   YITH_WC_Subscription
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_WC_Subscription_DB' ) ) {
	/**
	 * Class YITH_WC_Subscription_DB
	 * handle DB custom tables
	 *
	 * @abstract
	 */
	abstract class YITH_WC_Subscription_DB {


		/**
		 * Database version.
		 *
		 * @var string DB version
		 */
		public static $version = '2.3.0';

		/**
		 * Activity Log table name
		 *
		 * @var string
		 */
		public static $activities_log = 'yith_ywsbs_activities_log';

		/**
		 * Delivery schedules table name
		 *
		 * @var string
		 */
		public static $delivery_schedules = 'yith_ywsbs_delivery_schedules';

		/**
		 * Subscription stats table name
		 *
		 * @var string
		 */
		public static $subscription_stats = 'yith_ywsbs_stats';

		/**
		 * Subscription stats table name
		 *
		 * @var string
		 */
		public static $subscription_order_lookup = 'yith_ywsbs_order_lookup';
		/**
		 * Subscription stats table name
		 *
		 * @var string
		 */
		public static $subscription_revenue_lookup = 'yith_ywsbs_revenue_lookup';



		/**
		 * Subscription customer user table name
		 *
		 * @var string
		 */
		public static $subscription_customer_lookup = 'yith_ywsbs_customer_lookup';

		/**
		 * Install the database
		 *
		 * @return void
		 */
		public static function install() {
			self::create_db_tables();
		}

		/**
		 * Create table
		 *
		 * @param bool $force Force the creation.
		 *
		 * @return void
		 */
		public static function create_db_tables( $force = false ) {
			global $wpdb;

			$current_version = get_option( 'yith_ywsbs_db_version' );

			if ( $force || self::$version !== $current_version ) {
				$wpdb->hide_errors();

				$table_name         = $wpdb->prefix . self::$activities_log;
				$delivery_schedules = $wpdb->prefix . self::$delivery_schedules;
				$subscription_stats = $wpdb->prefix . self::$subscription_stats;
				$order_lookup       = $wpdb->prefix . self::$subscription_order_lookup;
				$revenue_lookup     = $wpdb->prefix . self::$subscription_revenue_lookup;
				$charset_collate    = $wpdb->get_charset_collate();

				$sql = "CREATE TABLE $table_name (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`activity` varchar(255) NOT NULL,
							`status` varchar(255) NOT NULL,
							`subscription` int(11) NOT NULL,
							`order` int(11) NOT NULL,
							`description` varchar(255) NOT NULL,
							`timestamp_date` datetime NOT NULL,
							PRIMARY KEY (id)
						) $charset_collate;";

				$sql .= "CREATE TABLE $delivery_schedules (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `subscription_id` bigint(20),
                    `status` varchar(20),
                    `entry_date` datetime NOT NULL,
                    `scheduled_date` datetime NOT NULL,
                    `sent_on` datetime,
                    PRIMARY KEY (id)
                    ) $charset_collate;";

				$sql .= "CREATE TABLE $subscription_stats (
                    `subscription_id` bigint(20) NOT NULL,
                    `status` varchar(200) NOT NULL,
                    `customer_id`  bigint(20) NOT NULL,
                    `date_created` datetime NOT NULL,
                    `date_created_gmt` datetime NOT NULL,
                    `product_name` varchar(200) NOT NULL,
                    `product_id` bigint(20) NOT NULL,
  					`variation_id` bigint(20) DEFAULT NULL,
  					`currency` varchar(20) NOT NULL,
  					`quantity` int(20) NOT NULL,
  					`fee` double,
  					`total` double,
  					`tax_total` double,
  					`shipping_total` double,
  					`net_total` double,
  					`mrr` double,
  					`arr` double,
  					`next_payment_due_date` varchar(200),
  					`trial` tinyint(1),
  					`conversion_date` datetime,
  					`cancelled_date` datetime,
  					`orders_paid` bigint(20),
                    PRIMARY KEY (`subscription_id`)
                    ) $charset_collate;";

				$sql .= "CREATE TABLE $order_lookup (
                    `order_id` bigint(20) NOT NULL,
                    `subscription_id` bigint(20) NOT NULL,
                    `status` varchar(200) NOT NULL,
                    `customer_id`  bigint(20) NOT NULL,
                    `date_created` datetime NOT NULL,
                    `date_paid` datetime NOT NULL,
  					`total` double,
  					`tax_total` double,
  					`shipping_total` double,
  					`net_total` double,
  					`renew` tinyint(1),
                     PRIMARY KEY (`order_id`,`subscription_id`)
                    ) $charset_collate;";

				$sql .= "CREATE TABLE $revenue_lookup (
                    `subscription_id` bigint(20) NOT NULL,
                    `update_date` datetime NOT NULL,
                    `mrr` double NOT NULL,
                    `arr` double NOT NULL,
                     PRIMARY KEY ( `subscription_id`,`update_date`)
                    ) $charset_collate;";

				if ( ! function_exists( 'dbDelta' ) ) {
					include_once ABSPATH . 'wp-admin/includes/upgrade.php';
				}
				dbDelta( $sql );
				update_option( 'yith_ywsbs_db_version', self::$version );
			}
		}
	}
}
