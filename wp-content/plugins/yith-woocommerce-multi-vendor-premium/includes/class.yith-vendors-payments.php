<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Vendors_Payments
 * @package    Yithemes
 * @since      Version 1.6
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Vendors_Payments' ) ) {

	class YITH_Vendors_Payments {

		/**
		 * Payments table name
		 *
		 * @var string
		 * @since 1.0
		 * @access protected
		 */
		protected static $_payments_table_name = 'yith_vendors_payments';

		/**
		 * Payments relationship table name
		 *
		 * @var string
		 * @since 1.0
		 * @access protected
		 */
		protected static $_payments_relationship_table_name = 'yith_vendors_payments_relationship';

		/**
		 * construct
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'add_transaction_table_wpdb' ), 0 );
			add_action( 'switch_blog', array( $this, 'add_transaction_table_wpdb' ), 0 );
		}

		/**
		 * Get all commissions id by payment
		 * @author Salvatore Strano
		 * @param int $payment_id
		 * @param string $status
		 *
		 * @return array
		 */
		public function get_commissions_by_payment_id( $payment_id, $status = 'processing' ){

			$commission_ids = array();

			if( $payment_id ){

				global $wpdb;
				$query =  $wpdb->prepare("SELECT relationship.commission_id AS commission_id FROM {$wpdb->prefix}yith_vendors_payments_relationship AS relationship JOIN
										{$wpdb->prefix}yith_vendors_payments AS payments ON relationship.payment_id = payments.ID AND payments.status = %s AND relationship.payment_id = %d", $status, $payment_id );

				$commission_ids = $wpdb->get_col( $query );

			}

			return $commission_ids;
		}

		/**
		 * Update payment status
		 * @author Salvatore Strano
		 * @param int $payment_id
		 * @param string $status
		 * @return bool
		 */
		public function update_payment_status( $payment_id, $status  ){

			$data = array(
				'status' => $status
			);
			$where = array(
				'ID' => $payment_id
			);

			global $wpdb;

			$table_name = "{$wpdb->prefix}yith_vendors_payments";

			$update = $wpdb->update( $table_name, $data, $where  );

			return $update;
		}

		/**
		 * Register vendor payments relationship into database
		 * @author Salvatore Strano
		 * @param int $payment_id
		 * @param  int $commission_id
		 */
		public function add_vendor_payment_relationship( $payment_id, $commission_id ){

			if( !empty( $payment_id ) && !empty( $commission_id ) ){

				global $wpdb;

				$table_name = "{$wpdb->prefix}yith_vendors_payments_relationship";


				$wpdb->insert( $table_name, array( 'payment_id'=> $payment_id,
				                                   'commission_id' => $commission_id
					)
				);
			}
		}

		/**
		 * Register Vendor payments into database
		 *
		 * @param array $payment
		 * @author  Salvatore Strano
		 * @return int|false
		 * $payment is an array with these key/
		 * array( 'vendor_id', 'user_id','amount', 'status', 'payment_date', 'payment_date_gmt' )
		 *
		 * The status can be :
		 * paid
		 * failed
		 * processing
		 */
		public function add_vendor_payments_log( $payment ){

			global  $wpdb;

			$insert_id = false;
			if( !empty( $payment['vendor_id'] ) && !empty( $payment['user_id'] ) && !empty( $payment['amount'] ) && !empty( $payment['currency'] ) && !empty( $payment['status'] ) ){

				$table_name = "{$wpdb->prefix}yith_vendors_payments";

				$result = $wpdb->insert( $table_name, $payment );

				if( $result ){
					$insert_id = $wpdb->insert_id;
				}
			}

			return $insert_id;
		}

		/**
		 * Add note to payments
		 *
		 * @param int $payment_id
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 *
		 */
		public function add_note( $payment_id, $note ){
			global  $wpdb;

			if( ! empty( $payment_id ) && ! empty( $note ) ){

				$table_name = "{$wpdb->prefix}yith_vendors_payments";
				$note = serialize( $note );

				$wpdb->update( $table_name, array( 'note' => $note ), array( 'ID' => $payment_id ) );
			}
		}

		/**
		 * Register Vendor payments and relationship into database
		 *
		 * @param array $payment
		 * @author  Salvatore Strano
		 * @return int|false
		 * $payment is an array with these key/
		 * array( 'vendor_id', 'user_id','amount', 'status', 'payment_date', 'payment_date_gmt' )
		 *
		 * The status can be :
		 * paid
		 * failed
		 * processing
		 */
		public function add_payment( $args ){
			$payment_id = 0;
			if( ! empty( $args['payment'] ) ){
				$payment_id = $this->add_vendor_payments_log( $args['payment'] );
			}

			if( ! empty( $args['commission_ids'] ) && ! empty( $payment_id ) ){
				$commission_ids = $args['commission_ids'];
				foreach( $commission_ids as $commission_id ){
					$this->add_vendor_payment_relationship( $payment_id, $commission_id );
				}
			}

			return $payment_id;
		}

		/**
		 * Create the {$wpdb->prefix}_yith_vendor_commissions table
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 * @return void
		 * @see    dbDelta()
		 */
		public static function create_transaction_table() {

			/**
			 * If exists yith_product_vendors_commissions_table_created option return null
			 */
			if ( get_option( 'yith_product_vendors_payments_table_created' ) ) {
				return;
			}

			/**
			 * Check if dbDelta() exists
			 */
			if ( ! function_exists( 'dbDelta' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			}

			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();

			$table_name = $wpdb->prefix . self::$_payments_table_name;
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

			$table_name = $wpdb->prefix . self::$_payments_relationship_table_name;
			$create = "CREATE TABLE IF NOT EXISTS $table_name (
                        payment_id bigint(20) NOT NULL,
                        commission_id bigint(20) NOT NULL,
                        PRIMARY KEY ( `payment_id`, `commission_id`)
                        ) $charset_collate;";
			dbDelta( $create );

			add_option( 'yith_product_vendors_payments_table_created', true );
		}

		/**
		 * Commissions API - set table name
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 * @return void
		 */
		public function add_transaction_table_wpdb() {
			global $wpdb;
			$wpdb->payments              = $wpdb->prefix . self::$_payments_table_name;
			$wpdb->tables[]              = self::$_payments_table_name;
			$wpdb->payments_relationship = $wpdb->prefix . self::$_payments_relationship_table_name;
			$wpdb->tables[]              = self::$_payments_relationship_table_name;
		}
	}
}