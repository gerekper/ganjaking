<?php
/**
 * Class YITH_WCBK_DB
 * Handle DB
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_DB' ) ) {
	/**
	 * Class YITH_WCBK_DB
	 *
	 * @abstract
	 */
	abstract class YITH_WCBK_DB {

		const BOOKING_NOTES_TABLE                    = 'yith_wcbk_booking_notes';
		const LOGS_TABLE                             = 'yith_wcbk_logs';
		const EXTERNAL_BOOKINGS_TABLE                = 'yith_wcbk_external_bookings';
		const BOOKING_META_LOOKUP_TABLE              = 'yith_wcbk_booking_meta_lookup';
		const PRODUCT_RESOURCES                      = 'yith_wcbk_product_resources';
		const BOOKING_RESOURCES                      = 'yith_wcbk_booking_resources';
		const GLOBAL_AVAILABILITY_RULES              = 'yith_wcbk_global_availability_rules';
		const GLOBAL_AVAILABILITY_RULES_ASSOCIATIONS = 'yith_wcbk_global_availability_rules_associations';
		const GLOBAL_PRICE_RULES                     = 'yith_wcbk_global_price_rules';
		const GLOBAL_PRICE_RULES_ASSOCIATIONS        = 'yith_wcbk_global_price_rules_associations';

		/**
		 * Booking Notes table
		 *
		 * @var string
		 * @deprecated 3.5 | use YITH_WCBK_DB::BOOKING_NOTES_TABLE instead
		 */
		public static $booking_notes_table = 'yith_wcbk_booking_notes';

		/**
		 * Log table
		 *
		 * @var string
		 * @deprecated 3.5 | use YITH_WCBK_DB::LOGS_TABLE instead
		 */
		public static $log_table = 'yith_wcbk_logs';

		/**
		 * External Bookings table
		 *
		 * @var string
		 * @deprecated 3.5 | use YITH_WCBK_DB::EXTERNAL_BOOKINGS_TABLE instead
		 */
		public static $external_bookings_table = 'yith_wcbk_external_bookings';

		/**
		 * Install
		 *
		 * @deprecated 3.0.0
		 */
		public static function install() {
			self::create_db_tables();
		}

		/**
		 * Register custom tables within $wpdb object.
		 */
		public static function define_tables() {
			global $wpdb;

			// List of tables without prefixes.
			$tables = array(
				self::BOOKING_NOTES_TABLE                    => self::BOOKING_NOTES_TABLE,
				self::LOGS_TABLE                             => self::LOGS_TABLE,
				self::EXTERNAL_BOOKINGS_TABLE                => self::EXTERNAL_BOOKINGS_TABLE,
				self::BOOKING_META_LOOKUP_TABLE              => self::BOOKING_META_LOOKUP_TABLE,
				self::PRODUCT_RESOURCES                      => self::PRODUCT_RESOURCES,
				self::BOOKING_RESOURCES                      => self::BOOKING_RESOURCES,
				self::GLOBAL_AVAILABILITY_RULES              => self::GLOBAL_AVAILABILITY_RULES,
				self::GLOBAL_AVAILABILITY_RULES_ASSOCIATIONS => self::GLOBAL_AVAILABILITY_RULES_ASSOCIATIONS,
				self::GLOBAL_PRICE_RULES                     => self::GLOBAL_PRICE_RULES,
				self::GLOBAL_PRICE_RULES_ASSOCIATIONS        => self::GLOBAL_PRICE_RULES_ASSOCIATIONS,
			);

			foreach ( $tables as $name => $table ) {
				$wpdb->$name    = $wpdb->prefix . $table;
				$wpdb->tables[] = $table;
			}
		}

		/**
		 * Create tables
		 *
		 * @noinspection SqlNoDataSourceInspection
		 */
		public static function create_db_tables() {
			global $wpdb;

			$wpdb->hide_errors();

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			$booking_notes_table                    = $wpdb->prefix . self::BOOKING_NOTES_TABLE;
			$logs_table                             = $wpdb->prefix . self::LOGS_TABLE;
			$external_bookings_table                = $wpdb->prefix . self::EXTERNAL_BOOKINGS_TABLE;
			$booking_meta_lookup_table              = $wpdb->prefix . self::BOOKING_META_LOOKUP_TABLE;
			$product_resources                      = $wpdb->prefix . self::PRODUCT_RESOURCES;
			$booking_resources                      = $wpdb->prefix . self::BOOKING_RESOURCES;
			$global_availability_rules              = $wpdb->prefix . self::GLOBAL_AVAILABILITY_RULES;
			$global_availability_rules_associations = $wpdb->prefix . self::GLOBAL_AVAILABILITY_RULES_ASSOCIATIONS;
			$global_price_rules                     = $wpdb->prefix . self::GLOBAL_PRICE_RULES;
			$global_price_rules_associations        = $wpdb->prefix . self::GLOBAL_PRICE_RULES_ASSOCIATIONS;
			$collate                                = '';

			if ( $wpdb->has_cap( 'collation' ) ) {
				$collate = $wpdb->get_charset_collate();
			}

			$sql = "CREATE TABLE $booking_notes_table (
						`id` bigint(20) NOT NULL AUTO_INCREMENT,
						`booking_id` bigint(20) NOT NULL,
						`type` varchar(255) NOT NULL,
						`description` TEXT NOT NULL,
						`note_date` datetime NOT NULL,
						PRIMARY KEY (`id`)
                    ) $collate;
                    
					CREATE TABLE $logs_table (
						`id` bigint(20) NOT NULL AUTO_INCREMENT,
						`description` text NOT NULL,
						`type` varchar(255) NOT NULL DEFAULT '',
						`group` varchar(255) NOT NULL,
						`date` datetime NOT NULL,
						PRIMARY KEY (`id`)
                    ) $collate;

					CREATE TABLE $external_bookings_table (
						`id` bigint(20) NOT NULL AUTO_INCREMENT,
						`product_id` bigint(20),
						`from` bigint(20),
						`to` bigint(20),
						`description` text,
						`summary` text,
						`location` varchar(255),
						`uid` varchar(255),
						`calendar_name` varchar(255) DEFAULT '',
						`source` varchar(255),
						`date` datetime,
						PRIMARY KEY (`id`)
                    ) $collate;

					CREATE TABLE $booking_meta_lookup_table (
					  `booking_id` bigint(20) NOT NULL,
					  `product_id` bigint(20) NOT NULL,
					  `order_id` bigint(20) NOT NULL,
					  `user_id` bigint(20) NOT NULL,
					  `status` varchar(100) NOT NULL default 'bk-unpaid',
					  `from` datetime NOT NULL,
					  `to` datetime NOT NULL,
					  `persons` integer NOT NULL default 1,
					  PRIMARY KEY  (`booking_id`),
					  KEY `product_id` (`product_id`),
					  KEY `order_id` (`order_id`),
					  KEY `user_id` (`user_id`),
					  KEY `status` (`status`)
					) $collate;

					CREATE TABLE $product_resources (
					  product_id bigint(20) NOT NULL,
					  resource_id bigint(20) NOT NULL,
					  base_price double NOT NULL default 0,
					  fixed_price double NOT NULL default 0,
					  multiply_base_price_per_person tinyint(1) NOT NULL default 0,
					  multiply_fixed_price_per_person tinyint(1) NOT NULL default 0,
					  priority int(11) NOT NULL default 0,
					  PRIMARY KEY product_resource ( product_id, resource_id ),
					  KEY resource_id (resource_id)
					) $collate;

					CREATE TABLE $booking_resources (
					  booking_id bigint(20) NOT NULL,
					  resource_id bigint(20) NOT NULL,
					  PRIMARY KEY booking_resource ( booking_id, resource_id ),
					  KEY resource_id (resource_id)
					) $collate;

					CREATE TABLE $global_availability_rules (
					  id bigint(20) NOT NULL AUTO_INCREMENT,
					  name varchar(255) NOT NULL DEFAULT '',
					  type varchar(100) NOT NULL DEFAULT '',
					  enabled tinyint(1) NOT NULL DEFAULT 1,
					  date_ranges text NOT NULL DEFAULT '',
					  availabilities text NOT NULL DEFAULT '',
					  priority int(11) NOT NULL default 0,
					  exclude_products tinyint(1) NOT NULL DEFAULT 0,
					  PRIMARY KEY id (id)
					) $collate;

					CREATE TABLE $global_availability_rules_associations (
					  rule_id bigint( 20 ) NOT null,
					  object_id bigint( 20 ) NOT null
					) $collate;

					CREATE TABLE $global_price_rules (
					  id bigint(20) NOT NULL AUTO_INCREMENT,
					  name varchar(255) NOT NULL DEFAULT '',
					  type varchar(100) NOT NULL DEFAULT '',
					  enabled tinyint(1) NOT NULL DEFAULT 1,
					  conditions text NOT NULL DEFAULT '',
					  change_base_price tinyint(1) NOT NULL DEFAULT 0,
					  base_price_operator varchar(20) NOT NULL DEFAULT '',
					  base_price decimal(26,8) NOT NULL DEFAULT 0,
					  change_base_fee tinyint(1) NOT NULL DEFAULT 0,
					  base_fee_operator varchar(20) NOT NULL DEFAULT '',
					  base_fee decimal(26,8) NOT NULL DEFAULT 0,
					  priority int(11) NOT NULL default 0,
					  exclude_products tinyint(1) NOT NULL DEFAULT 0,
					  PRIMARY KEY id (id)
					) $collate;

					CREATE TABLE $global_price_rules_associations (
					  rule_id bigint( 20 ) NOT null,
					  object_id bigint( 20 ) NOT null
					) $collate;
                    ";

			dbDelta( $sql );
		}
	}
}
