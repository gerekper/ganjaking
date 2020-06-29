<?php
if( !defined('ABSPATH')){
	exit;
}
if( !class_exists( 'YITH_Funds_Redeem_Payments')){

	class YITH_Funds_Redeem_Payments{

		protected static $redeem_payments_table_name = 'yith_redeem_funds_payments';

		/**
		 * create the Redeem fund payments table
		 * @author YITH
		 * @since 1.4.0
		 */
		public static function create_table(){

			global $wpdb;

			/**
			 * Check if dbDelta() exists
			 */
			if ( ! function_exists( 'dbDelta' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			}

			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();

			$table_name = $wpdb->prefix . self::$redeem_payments_table_name;
			$create = "CREATE TABLE IF NOT EXISTS $table_name (
                        ID bigint(20) NOT NULL AUTO_INCREMENT,
                        vendor_id bigint(20) NOT NULL,
                        user_id bigint(20) NOT NULL,
                        amount double(15,4) NOT NULL,
                        currency varchar(10) NOT NULL,
                        status varchar(100) NOT NULL,
                        note text NOT NULL,
                        payment_date DATETIME NOT NULL DEFAULT '000-00-00 00:00:00',
                        payment_date_gmt DATETIME NOT NULL DEFAULT '000-00-00 00:00:00',
                        gateway_id varchar(100),
                        PRIMARY KEY (ID)
                        ) $charset_collate;";
			dbDelta( $create );

		}




	}
}