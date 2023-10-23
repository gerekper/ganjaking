<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * DB Class Handler
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Classes
 *
 * @since   2.0
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_DB' ) ) {
	/**
	 * Class YITH_WCBM_DB
	 *
	 * @abstract
	 */
	abstract class YITH_WCBM_DB {

		const BADGE_RULES_ASSOCIATIONS_TABLE = 'yith_wcbm_badge_rules_associations';

		/**
		 * Get Badge rules associations table name
		 *
		 * @return string
		 */
		public static function get_badge_rules_table_name() {
			global $wpdb;

			return $wpdb->prefix . self::BADGE_RULES_ASSOCIATIONS_TABLE;
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

			$badge_rules_associations_table = $wpdb->prefix . self::BADGE_RULES_ASSOCIATIONS_TABLE;

			$collate = '';
			if ( $wpdb->has_cap( 'collation' ) ) {
				$collate = $wpdb->get_charset_collate();
			}

			$sql = "CREATE TABLE $badge_rules_associations_table (
						`rule_id` bigint(20) NOT NULL,
						`type` varchar(255) NOT NULL,
						`value` varchar(255),
						`badge_id` bigint(20) NOT NULL,
						`enabled` tinyint(1) NOT NULL
                    ) $collate;";

			dbDelta( $sql );
		}

	}
}
