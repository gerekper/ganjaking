<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ;
}
if ( ! class_exists( 'RS_Export_Report_For_User' ) ) {

    /**
     * RS_Export_Report_For_User Class.
     */
    class RS_Export_Report_For_User extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action = 'rs_export_report_for_user_updater' ;

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
            $this->export_report_for_user( $item ) ;
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
            $offset            = get_option( 'rs_export_report_background_updater_offset' ) ;
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
                RS_Main_Function_for_Background_Process::callback_to_export_report_for_user( $offset ) ;
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 75 ) ;
            } else {
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 100 ) ;
                FP_WooCommerce_Log::log( 'Report for User(s) exported Successfully' ) ;
                delete_option( 'rs_export_report_background_updater_offset' ) ;
            }
        }

        public function export_report_for_user( $UserId ) {
            if ( $UserId != 'no_users' ) {
                global $wpdb ;
                $table_name   = $wpdb->prefix . 'rsrecordpoints' ;
                $UserType     = get_option( 'fp_user_selection_type' ) ;
                $DateType     = get_option( 'fp_date_type' ) ;
                $EarnPoints   = get_option( 'export_earn_points' ) ;
                $RedeemPoints = get_option( 'export_redeem_points' ) ;
                $TotalPoints  = get_option( 'export_total_points' ) ;

                $total_user_points = array() ;

                $PointsData  = new RS_Points_data( $UserId ) ;

                if ( $DateType == '1' ) {
                    $earned_points = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(earnedpoints) FROM $table_name WHERE userid = %d" , $UserId ) ) ;
                    $redeem_points = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(redeempoints) FROM $table_name WHERE userid = %d" , $UserId ) ) ;
                } else {
                    $StartTime     = strtotime( get_option( 'selected_report_start_date' ) . ' ' . '00:00:00' ) ;
                    $EndTime       = strtotime( get_option( 'selected_report_end_date' ) . ' ' . '23:59:00' ) ;
                    $earned_points = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(earnedpoints) FROM $table_name WHERE userid = %d AND earneddate >= %d AND earneddate <= %d" , $UserId , $StartTime , $EndTime ) ) ;
                    $redeem_points = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(redeempoints) FROM $table_name WHERE userid = %dAND earneddate >= %d AND earneddate <= %d" , $UserId , $StartTime , $EndTime ) ) ;
                }

                $total_user_points[ $UserId ] = array( array_sum( $earned_points ) , array_sum( $redeem_points ) ) ;

                $TotalPointsEarned = $PointsData->total_available_points() ;

                if ( srp_check_is_array( $total_user_points ) ) {
                    $Data = array() ;
                    foreach ( $total_user_points as $Id => $Points ) {
                        $PointsEarned   = round_off_type( $Points[ 0 ] ) ;
                        $PointsRedeemed = round_off_type( $Points[ 1 ] ) ;
                        $UserName       = get_user_by( 'id' , $Id )->user_login ;
                        if ( ($EarnPoints == '0') && ($RedeemPoints == '0') && ($TotalPoints == '0') ) {
                            $Heading = "Username,Total Earned Points,Total Redeemed Points,Available Points" . "\n" ;
                            $Data[]  = array( $UserName , $PointsEarned , $PointsRedeemed , $TotalPointsEarned ) ;
                            update_option( 'heading' , $Heading ) ;
                        }
                        if ( ($EarnPoints == '1') && ($RedeemPoints == '0') && ($TotalPoints == '0') ) {
                            $Heading = "Username,Total Earned Points" . "\n" ;
                            $Data[]  = array( $UserName , $PointsEarned ) ;
                            update_option( 'heading' , $Heading ) ;
                        }
                        if ( ($EarnPoints == '0') && ($RedeemPoints == '1') && ($TotalPoints == '0') ) {
                            $Heading = "Username,Total Redeemed Points" . "\n" ;
                            $Data[]  = array( $UserName , $PointsRedeemed ) ;
                            update_option( 'heading' , $Heading ) ;
                        }
                        if ( ($EarnPoints == '0') && ($RedeemPoints == '0') && ($TotalPoints == '1') ) {
                            $Heading = "Username,Available Points" . "\n" ;
                            $Data[]  = array( $UserName , $TotalPointsEarned ) ;
                            update_option( 'heading' , $Heading ) ;
                        }
                        if ( ($EarnPoints == '1') && ($RedeemPoints == '1') && ($TotalPoints == '0') ) {
                            $Heading = "Username,Total Earned Points,Total Redeemed Points" . "\n" ;
                            $Data[]  = array( $UserName , $PointsEarned , $PointsRedeemed ) ;
                            update_option( 'heading' , $Heading ) ;
                        }
                        if ( ($EarnPoints == '1') && ($RedeemPoints == '0') && ($TotalPoints == '1') ) {
                            $Heading = "Username,Total Earned Points,Available Points" . "\n" ;
                            $Data[]  = array( $UserName , $PointsEarned , $TotalPointsEarned ) ;
                            update_option( 'heading' , $Heading ) ;
                        }
                        if ( ($EarnPoints == '0') && ($RedeemPoints == '1') && ($TotalPoints == '1') ) {
                            $Heading = "Username,Total Redeemed Points,Available Points" . "\n" ;
                            $Data[]  = array( $UserName , $PointsRedeemed , $TotalPointsEarned ) ;
                            update_option( 'heading' , $Heading ) ;
                        }
                        if ( ($EarnPoints == '1') && ($RedeemPoints == '1') && ($TotalPoints == '1') ) {
                            $Heading = "Username,Total Earned Points,Total Redeemed Points,Available Points" . "\n" ;
                            $Data[]  = array( $UserName , $PointsEarned , $PointsRedeemed , $TotalPointsEarned ) ;
                            update_option( 'heading' , $Heading ) ;
                        }
                    }
                    $OldData   = ( array ) get_option( 'rs_export_report' ) ;
                    $MergeData = array_merge( $OldData , $Data ) ;
                    update_option( 'rs_export_report' , $MergeData ) ;
                }
            }
        }

    }

}