<?php
if( !defined( 'ABSPATH' ) ){
    exit;
}

if( !class_exists( 'YITH_PADP_Receiver_Commission' ) ){

    class YITH_PADP_Receiver_Commission{

        /**
         * @var YITH_PADP_Receiver_Commission $instance
         */
        protected static $instance;
        
        public static $table_name = 'yith_padp_receiver_commissions';

        public static $db_version = YITH_PAYPAL_ADAPTIVE_DB_VERSION;

        public function __construct()
        {

        }

        
        public static function install()
        {
            $current_db_version = get_option( 'yith_padp_db_version', '0' );

            if( get_option( 'yith_paypal_adaptive_payment_commissions_table_created' ) ){

                return ;
            }

            if( version_compare( $current_db_version, '1.0.0', '<' ) ) {
               
                global $wpdb;

                $charset_collate = $wpdb->get_charset_collate();

                $table_name = $wpdb->prefix . self::$table_name;

                $sql = "CREATE TABLE IF NOT EXISTS $table_name ( 
								  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
                                  `order_id` bigint(20) ,
                                  `user_id` bigint(20), 
                                  `transaction_id` varchar(255),  
                                  `transaction_status` varchar(255),
                                  `transaction_value` double(15,4),
                                  `transaction_date` timestamp DEFAULT CURRENT_TIMESTAMP,
                                  PRIMARY KEY (`ID`) ) $charset_collate";

                if( !function_exists( 'dbDelta' ) ) {
                    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                }

                dbDelta( $sql );

                $current_db_version = '1.0.0';
            }
            add_option( 'yith_paypal_adaptive_payment_commissions_table_created', true );
            update_option( 'yith_padp_db_version', self::$db_version );
        }


        public function add_transaction( $user_id, $order_id, $value, $transaction_status, $transaction_id = '' ) {

            $user_transaction_exist = $this->user_transaction_exist( $user_id, $order_id );
            
            if( $user_transaction_exist ){
                //update
                $result = $this->update( $user_id, $order_id, $value, $transaction_status, $transaction_id );
            }else{
                
                //insert new transaction
                $result = $this->add( $user_id, $order_id, $value, $transaction_status, $transaction_id  );
            }

            return $result;
        }

	    /**
	     * anonymize transaction set user_id = 0
	     * @param $ID
	     *
	     * @return false|int
	     */
        public function anonymize_transaction( $ID ){

        	global $wpdb;

        	$data = array(
        		'user_id' => 0
	        );

        	$where = array(
        		'ID' => $ID
	        );

	        $table_name = $wpdb->prefix . self::$table_name;

	        return $wpdb->update( $table_name, $data , $where );
        }

        /**
         * @param int $user_id
         * @param int $order_id
         * @param float $value
         * @param string $transaction_status
         * @param string $transaction_id
         * @return false|int
         */
        public function add( $user_id, $order_id, $value, $transaction_status, $transaction_id = '' ){
            
            $args = array(
               'order_id'    => $order_id,
               'user_id'     =>  $user_id,
               'transaction_id' => $transaction_id,
               'transaction_status' => $transaction_status,
               'transaction_value'  => $value 
            );
            
            global  $wpdb;
            $table_name = $wpdb->prefix . self::$table_name;
            
            return $wpdb->insert( $table_name, $args );
        }

        /**
         * @param int $user_id
         * @param int $order_id
         * @param float $value
         * @param string $transaction_status
         * @param string $transaction_id
         * @return false|int
         */
        public function update( $user_id, $order_id, $value, $transaction_status, $transaction_id ) {

            $data = array(
                'transaction_id' => $transaction_id,
                'transaction_status' => $transaction_status,
                'transaction_value'  => $value
            );

            $where = array(
                'user_id' => $user_id,
                'order_id' => $order_id
            );
            global  $wpdb;
            $table_name = $wpdb->prefix . self::$table_name;

            return $wpdb->update( $table_name, $data, $where, array( '%s', '%s', '%f'), array( '%d', '%d' ) );
        }

        /**
         * update the transaction status by order
         * @author YITHEMES
         * @since 1.0.0
         * @param $order_id
         * @param $new_status
         * @return false|int
         */
        public function update_by_order( $user_id, $order_id, $new_status ){

            $data = array(
                'transaction_status' => $new_status,
            );

            $where = array(
                'user_id' => $user_id,
                'order_id' => $order_id
            );

            global  $wpdb;
            $table_name = $wpdb->prefix . self::$table_name;

            return $wpdb->update( $table_name, $data, $where, array( '%s' ), array( '%d' ) );
        }

        /**
         * @param bool $user_id
         * @param bool $transaction_status
         * @param bool $offset
         * @param bool $limit
         * @return array|null|object
         */
        public function get_transaction( $user_id = false, $transaction_status = false, $offset = false, $limit = false ){
            
            if( YITH_PayPal_Adaptive_Payments_Integrations::is_multivendor_active() ){
                
                return apply_filters( 'yith_paypal_adaptive_payments_get_transactions', array(), $user_id, $transaction_status, $offset, $limit );
                
            }else {
                global $wpdb;
                $table_name = $wpdb->prefix . self::$table_name;

                $query = "SELECT * FROM $table_name";
                $order_by = "ORDER BY transaction_date DESC";
                $extra_query = array();
                $limit_query = '';

                if( $user_id ) {

                    $extra_query[] = $wpdb->prepare( "user_id = %d", $user_id );
                }

                if( $transaction_status ) {
                    $extra_query[] = $wpdb->prepare( "transaction_status LIKE %s", $transaction_status );
                }

                if( $offset!=='' && $limit!=='' ) {
                    $limit_query = $wpdb->prepare( "LIMIT %d, %d", $offset, $limit );
                }

                if( count( $extra_query )>0 ) {

                    $query .= ' WHERE ' . $extra_query[0];


                    for ( $i = 1; $i<count( $extra_query ); $i++ ) {
                        $query .= ' AND ' . $extra_query[$i];
                    }

                }

                $query .= ' ' . $order_by . ' ' . $limit_query;


                $results = $wpdb->get_results( $query , ARRAY_A );
                return $results;
            }

        }

        /**
         * count all commission user 
         * @author YITHEMES
         * @since 1.0.0
         * @return int
         */
        public function count_commission( $user_id )
        {

            if( YITH_PayPal_Adaptive_Payments_Integrations::is_multivendor_active() ) {

                return apply_filters( 'yith_paypal_adaptive_payments_count_transaction', 0, $user_id );

            }
            else {
                global $wpdb;
                $table_name = $wpdb->prefix . self::$table_name;
                $query = $wpdb->prepare( "SELECT COUNT(*) FROM {$table_name} WHERE user_id = %d", $user_id );

                return $wpdb->get_var( $query );
            }
        }

        /**
         * @param int $user_id
         * @param int $order_id
         * @return bool
         */
        public function user_transaction_exist( $user_id, $order_id ){

            global $wpdb;
            $table_name = $wpdb->prefix . self::$table_name;
            $query = "SELECT user_id FROM $table_name WHERE user_id = %s AND order_id = %s LIMIT 1";
            
            $query = $wpdb->prepare( $query, $user_id, $order_id );
            
            $result = $wpdb->get_var( $query );
            
            return !empty( $result ) ; 
        }
        /**
         * @author YITHEMES
         * @since 1.0.0
         * @return YITH_PADP_Receiver_Commission
         */
        public static function  get_instance(){

            if( is_null( self::$instance ) ){

                self::$instance = new self();
            }
            return self::$instance;
        }
    }
}

/**
 * @return YITH_PADP_Receiver_Commission
 */
function YITH_PADP_Receiver_Commission() {

    return YITH_PADP_Receiver_Commission::get_instance();
}


YITH_PADP_Receiver_Commission();