<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ;
}
if ( ! class_exists( 'RS_Export_Log' ) ) {

    /**
     * RS_Export_Log Class.
     */
    class RS_Export_Log extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action = 'rs_export_log_for_user_updater' ;

        /**
         * Task
         *
         * Override this method to perform any actions required on each
         * queue item. Return the modified item for further processing
         * in the next pass through. Or, return false to remove the
         * item from the queue.
         *
         * @param mixed $item Queue item to iterate over
         *
         * @return mixed
         */
        protected function task( $item ) {
            $this->export_logs_for_user( $item ) ;
            return false ;
        }

        /**
         * Complete
         *
         * Override if applicable, but ensure that the below actions are
         * performed, or, call parent::complete().
         */
        protected function complete() {
            parent::complete() ;
            $offset            = get_option( 'rs_export_log_background_updater_offset' ) ;
            $UserSelectionType = get_option( 'fp_user_selection_type' ) ;
            $Selecteduser      = get_option( 'fp_selected_users' ) ;
            if ( $UserSelectionType == '1' ) {
                $args   = array( 'fields' => 'ID' ) ;
                $UserId = get_users( $args ) ;
            } else if ( $UserSelectionType == '2' ) {
                $UserId = srp_check_is_array( $Selecteduser ) ? $Selecteduser : explode( ',' , $Selecteduser ) ;
            }
            $SlicedArray = array_slice( $UserId , $offset , 1000 ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                RS_Main_Function_for_Background_Process::callback_to_export_log_for_user( $offset ) ;
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 75 ) ;
            } else {
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 100 ) ;
                FP_WooCommerce_Log::log( 'Log for User(s) exported Successfully' ) ;
                delete_option( 'rs_export_log_background_updater_offset' ) ;
            }
        }

        public function export_logs_for_user( $UserId ) {
            if ( $UserId != 'no_users' ) {
                global $wpdb ;
                $table_name = $wpdb->prefix . 'rsrecordpoints' ;
                $UserType   = get_option( 'fp_user_selection_type' ) ;
                $data       = array() ;
                $datas      = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE userid = %d" , $UserId ) , ARRAY_A ) ;
                $datas      = $datas + ( array ) get_option( 'rsoveralllog' ) ;
                if ( srp_check_is_array( $datas ) ) {
                    foreach ( $datas as $values ) {
                        if ( empty( $values ) )
                            continue ;

                        if ( isset( $values[ 'earnedpoints' ] ) ) {
                            $username     = get_user_meta( $values[ 'userid' ] , 'nickname' , true ) ;
                            $refuserid    = get_user_meta( $values[ 'refuserid' ] , 'nickname' , true ) ;
                            $nomineeid    = get_user_meta( $values[ 'nomineeid' ] , 'nickname' , true ) ;
                            $usernickname = get_user_meta( $values[ 'userid' ] , 'nickname' , true ) ;
                            $earnpoints   = $values[ 'earnedpoints' ] ;
                            $redeempoints = $values[ 'redeempoints' ] ;
                            $eventname    = RSPointExpiry::msg_for_log( true , true , true , $earnpoints , $values[ 'checkpoints' ] , $values[ 'productid' ] , $values[ 'orderid' ] , $values[ 'variationid' ] , $values[ 'userid' ] , $refuserid , $values[ 'reasonindetail' ] , $redeempoints , true , $nomineeid , $usernickname , $values[ 'nomineepoints' ] ) ;
                        } else {
                            $username               = get_user_meta( $values[ 'userid' ] , 'nickname' , true ) ;
                            $earnpoints             = round_off_type( $values[ 'totalvalue' ] ) ;
                            $redeempoints           = round_off_type( $values[ 'totalvalue' ] ) ;
                            $eventname              = $values[ 'eventname' ] ;
                            $values[ 'earneddate' ] = $values[ 'date' ] ;
                        }
                        $data[] = array(
                            'user_name' => $username ,
                            'points'    => empty( $earnpoints ) ? $redeempoints : $earnpoints ,
                            'event'     => $eventname ,
                            'date'      => date_display_format( $values[ 'earneddate' ] ) ,
                            'expiry_date' => 999999999999 != $values[ 'expirydate' ] ? date_display_format( $values[ 'expirydate' ] ) : '-' ,
                                ) ;
                    }
                }
                $olddata   = get_option( 'rs_data_to_export' ) ;
                $mergedata = array_merge( $olddata , $data ) ;
                update_option( 'rs_data_to_export' , $mergedata ) ;
            }
        }

    }

}