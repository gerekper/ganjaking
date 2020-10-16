<?php

/*
 * Master Log Tab
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSMasterLog' ) ) {

    class RSMasterLog {

        public static function init() {

            add_action( 'woocommerce_rs_settings_tabs_fprsmasterlog' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsmasterlog' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action( 'rs_default_settings_fprsmasterlog' , array( __CLASS__ , 'set_default_value' ) ) ;

            add_action( 'woocommerce_admin_field_rs_select_users_master_log' , array( __CLASS__ , 'rs_select_user_to_export_master_log' ) ) ;

            add_action( 'woocommerce_admin_field_rs_masterlog' , array( __CLASS__ , 'points_log_table' ) ) ;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce ;
            return apply_filters( 'woocommerce_rewardsystem_myaccount_settings' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Master Log Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_masterlog_setting' ,
                ) ,
                array(
                    'name'    => __( 'Export Master Log for' , SRP_LOCALE ) ,
                    'id'      => 'rs_export_import_masterlog_option' ,
                    'class'   => 'rs_export_import_masterlog_option' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'radio' ,
                    'options' => array( '1' => 'All Users' , '2' => 'Selected Users' ) ,
                    'newids'  => 'rs_export_import_masterlog_option' ,
                ) ,
                array(
                    'name'    => __( 'Select the users that you wish to Export Master Log' , SRP_LOCALE ) ,
                    'id'      => 'rs_export_masterlog_users_list' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'rs_select_users_master_log' ,
                    'newids'  => 'rs_export_masterlog_users_list' ,
                ) ,
                array(
                    'type' => 'rs_masterlog' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_masterlog_setting' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                    ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields( RSMasterLog::reward_system_admin_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options( RSMasterLog::reward_system_admin_fields() ) ;
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function set_default_value() {
            foreach ( RSMasterLog::reward_system_admin_fields() as $setting )
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
        }

        public static function rs_select_user_to_export_master_log() {
            $field_id    = "rs_export_masterlog_users_list" ;
            $field_label = "Select the users that you wish to Export Master Log" ;
            $getuser     = get_option( 'rs_export_masterlog_users_list' ) ;
            echo user_selection_field( $field_id , $field_label , $getuser ) ;
        }

        public static function outputCSV( $data ) {
            $output = fopen( "php://output" , "w" ) ;
            if ( srp_check_is_array( $data ) ) {
                foreach ( $data as $row ) {
                    if ( $row != false ) {
                        fputcsv( $output , $row ) ; // here you can change delimiter/enclosure
                    }
                }
            }
            fclose( $output ) ;
        }

        public static function points_log_table() {
            $Obj = new WP_List_Table_for_Master_Log() ;
            $Obj->prepare_items() ;
            echo '<tr valign ="top">
            <td class="forminp forminp-select">
                <input type="button" id="rs_export_master_log_csv" class="rs_export_button" name="rs_export_master_log_csv" value="Export Master Log as CSV"/>
            </td></tr></p>' ;
            $Obj->search_box( 'Search' , 'search_id' ) ;
            $Obj->display() ;
            if ( isset( $_GET[ 'export_log' ] ) && $_GET[ 'export_log' ] == 'yes' ) {
                ob_end_clean() ;
                header( "Content-type: text/csv" ) ;
                header( "Content-Disposition: attachment; filename=reward_points_masterlog " . date_i18n( 'Y-m-d' ) . ".csv" ) ;
                header( "Pragma: no-cache" ) ;
                header( "Expires: 0" ) ;
                echo "Username,Points,Event,Date,Expiry Date" . "\n" ;
                self::outputCSV( get_option( 'rs_data_to_export' ) ) ;
                delete_option( 'rs_data_to_export' ) ;
                exit() ;
            }
        }

    }

    RSMasterLog::init() ;
}