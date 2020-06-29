<?php
!defined( 'YITH_POS' ) && exit; // Exit if accessed directly


if ( !class_exists( 'YITH_POS_DB' ) ) {
	/**
	 * Class YITH_POS_DB
	 * handle DB custom tables
	 *
	 * @abstract
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	abstract class YITH_POS_DB {

		/** @var string DB version */
		public static $version = '1.0.0';

		public static $register_session     = 'yith_pos_register_sessions';

		/**
		 * install
		 */
		public static function install() {
			self::create_db_tables();
		}

		/**
		 * create tables
		 *
		 * @param bool $force
		 */
		public static function create_db_tables( $force = false ) {
			global $wpdb;

			$current_version = get_option( 'yith_pos_db_version' );

			if ( $force || $current_version != self::$version ) {
				$wpdb->hide_errors();

				$register_session_table_name = $wpdb->prefix . self::$register_session;
				$charset_collate          = $wpdb->get_charset_collate();

				$sql
					= "CREATE TABLE $register_session_table_name (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `store_id` bigint(20) NOT NULL,
                    `register_id` bigint(20) NOT NULL,
                    `open` datetime NOT NULL,
                    `closed` datetime,
                    `cashiers` longtext,
                    `total` varchar(255),
                    `cash_in_hand` longtext,
                    `note` varchar(255),
                    `report` longtext,
                    PRIMARY KEY (id)
                    ) $charset_collate;";

				if ( !function_exists( 'dbDelta' ) ) {
					require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				}
				dbDelta( $sql );
				update_option( 'yith_pos_db_version', self::$version );
			}
		}
	}
}