<?php

/*
 * GDPR Compliance
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly
}

if ( ! class_exists( 'SRP_Privacy' ) ) :

    /**
     * SRP_Privacy class
     */
    class SRP_Privacy {

        /**
         * SRP_Privacy constructor.
         */
        public function __construct() {
            $this->init_hooks() ;
        }

        /**
         * Register SUMO Reward Points
         */
        public function init_hooks() {
            // This hook registers SUMO Reward Points data exporters.
            add_filter( 'wp_privacy_personal_data_exporters' , array( __CLASS__ , 'register_exporters' ) ) ;
            // This hook registers SUMO Reward Points data erasers.
            add_filter( 'wp_privacy_personal_data_erasers' , array( __CLASS__ , 'register_erasers' ) ) ;

            add_action( 'admin_init' , array( __CLASS__ , 'add_privacy_content_for_reward_points' ) , 20 ) ;
        }

        /**
         * Register SUMO Reward Points Exporters
         */
        public static function register_exporters( $exporter ) {
            $exporter[ 'srp-customer-data' ] = array(
                'exporter_friendly_name' => __( 'Reward Points' , SRP_LOCALE ) ,
                'callback'               => array( __CLASS__ , 'customer_data_exporter' ) ,
                    ) ;

            return $exporter ;
        }

        /**
         * Register SUMO Reward Points Erasers
         */
        public static function register_erasers( $erasers ) {
            $erasers[ 'srp-customer-data' ] = array(
                'eraser_friendly_name' => __( 'Reward Points' , SRP_LOCALE ) ,
                'callback'             => array( __CLASS__ , 'customer_data_eraser' ) ,
                    ) ;

            return $erasers ;
        }

        /**
         * Finds and exports customer data by email address.
         */
        public static function customer_data_exporter( $email_address , $page ) {
            $data_to_export = array() ;
            $user_data      = get_user_by( 'email' , $email_address ) ; // Check if user has an ID in the DB to load stored personal data.
            if ( ! $user_data instanceof WP_User ) {
                return array(
                    'data' => $data_to_export ,
                    'done' => true ,
                        ) ;
            }

            $data_to_export[] = array(
                'group_id'    => 'srp-customer-data' ,
                'group_label' => __( 'Reward Points' , SRP_LOCALE ) ,
                'item_id'     => 'user' ,
                'data'        => self::get_customer_personal_data( $user_data ) ,
                    ) ;

            return array(
                'data' => $data_to_export ,
                'done' => true ,
                    ) ;
        }

        /**
         * Finds and erases customer data by email address.     
         */
        public static function customer_data_eraser( $email_address , $page ) {
            $response = array(
                'items_removed'  => false ,
                'items_retained' => false ,
                'messages'       => array() ,
                'done'           => true ,
                    ) ;

            $user = get_user_by( 'email' , $email_address ) ; // Check if user has an ID in the DB to load stored personal data.
            if ( ! $user instanceof WP_User ) {
                return $response ;
            }

            $user_id                     = $user->ID ;
            self::remove_data_from_table( $user_id ) ;
            $response[ 'messages' ][]    = sprintf( __( 'Removed personal data from SUMO Reward Points for %s.' , SRP_LOCALE ) , $user_id ) ;
            $response[ 'items_removed' ] = true ;

            return $response ;
        }

        /**
         * Find customer data    
         */
        public static function get_customer_personal_data( $user_data ) {
            $user_id               = $user_data->ID ;
            $PointsData            = new RS_Points_Data( $user_id ) ;
            $available_points      = $PointsData->total_available_points() ;
            $total_earned_points   = $PointsData->total_earned_points() ;
            $total_redeemed_points = $PointsData->total_redeemed_points() ;
            $total_expired_points  = $PointsData->total_expired_points() ;
            $payment_detail        = RSPointExpiry::get_paypal_id_form_cashback_form( $user_id ) ;
            return array(
                array(
                    'name'  => __( 'User ID' , SRP_LOCALE ) ,
                    'value' => $user_id ,
                ) ,
                array(
                    'name'  => __( 'Available Points' , SRP_LOCALE ) ,
                    'value' => $available_points ,
                ) ,
                array(
                    'name'  => __( 'Total Earned Points' , SRP_LOCALE ) ,
                    'value' => $total_earned_points ,
                ) ,
                array(
                    'name'  => __( 'Total Redeemed Points' , SRP_LOCALE ) ,
                    'value' => $total_redeemed_points ,
                ) ,
                array(
                    'name'  => __( 'Total Expired Points' , SRP_LOCALE ) ,
                    'value' => $total_expired_points ,
                ) ,
                array(
                    'name'  => __( 'Payment Details for Cashback' , SRP_LOCALE ) ,
                    'value' => $payment_detail == '' ? '-' : $payment_detail ,
                ) ,
                    ) ;
        }

        public static function remove_data_from_table( $user_id ) {
            global $wpdb ;
            $table_names = array(
                'rspointexpiry' ,
                'rsrecordpoints' ,
                'sumo_reward_encashing_submitted_data'
                    ) ;
            foreach ( $table_names as $name ) {
                $tname = $wpdb->prefix . $name ;
                $res   = $wpdb->query( $wpdb->prepare( "DELETE FROM $tname WHERE userid = %d" , $user_id ) ) ;
            }
        }

        /**
         * Return the privacy policy content for SUMO Reward Points.
         */
        public static function get_privacy_content() {
            return

                    '<p>' . __( 'This includes the basics of what personal data your store may be collecting, storing and sharing. Depending on what settings are enabled and which additional plugins are used, the specific information shared by your store will vary' , 'rewardasystem' ) . '</p>' .
                    '<h2>' . __( 'What the Plugin Does' , 'rewardasystem' ) . '</h2>' .
                    '<p>' . __( '- Reward Points can be earned for various actions such as account signup, product purchase, social promotion, product review, etc' , 'rewardasystem' ) . '</p>' .
                    '<p>' . __( '- The earned reward points can be used for getting a discount on future purchases' , 'rewardasystem' ) . '</p>' .
                    '<p>' . __( '- The earned points can also be requested as a cashback for which your payment details may be collected' , 'rewardasystem' ) . '</p>' .
                    '<p>' . __( '- Emails can be sent to the user for various actions such as Reward Points Earning, Reward Points Redeeming, etc' , 'rewardasystem' ) . '</p>' .
                    '<h2>' . __( 'What we collect and store' , 'rewardasystem' ) . '</h2>' .
                    '<h3>' . __( '- User ID' , 'rewardasystem' ) . '</h3>' .
                    '<p>' . __( 'The user id is used for storing the points earned, redeemed by the user' , 'rewardasystem' ) . '</p>' .
                    '<h3>' . __( '- Payment Details for Cashback' , 'rewardasystem' ) . '</h3>' .
                    '<p>' . __( 'The user\'s PayPal email id/Payment details are collected during the cashback requests' , 'rewardasystem' ) . '</p>' .
                    '<h3>' . __( '- Cookies' , 'rewardasystem' ) . '</h3>' .
                    '<p>' . __( 'We use cookies to award points for Referral Account Sign up and Referral Product Purchases' , 'rewardasystem' ) . '</p>' .
                    '<h3>' . __( '- Phone Number' , 'rewardasystem' ) . '</h3>' .
                    '<p>' . __( 'We use the WooCommere billing phone number to send SMS notifications about the Reward Points Earned/Redeemed.' , 'rewardasystem' ) . '</p>' ;
        }

        /**
         * Add the privacy policy text to the policy postbox.
         */
        public static function add_privacy_content_for_reward_points() {

            if ( ! function_exists( 'wp_add_privacy_policy_content' ) )
                return ;

            $content = SRP_Privacy :: get_privacy_content() ;
            wp_add_privacy_policy_content( __( 'SUMO Reward Points' , SRP_LOCALE ) , $content ) ;
        }

    }

    new SRP_Privacy() ;

endif;