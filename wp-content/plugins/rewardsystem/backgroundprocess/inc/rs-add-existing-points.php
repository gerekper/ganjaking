<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ;
}
if ( ! class_exists( 'RS_Add_Existing_Points_For_User' ) ) {

    /**
     * RS_Add_Existing_Points_For_User Class.
     */
    class RS_Add_Existing_Points_For_User extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action = 'rs_add_old_points_for_user_updater' ;

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
            $this->add_existing_points_for_user( $item ) ;
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
            $offset      = get_option( 'rs_old_points_background_updater_offset' ) ;
            $args        = array(
                'fields'       => 'ID' ,
                'meta_key'     => '_my_reward_points' ,
                'meta_value'   => '' ,
                'meta_compare' => '!='
                    ) ;
            $UserId      = get_users( $args ) ;
            $SlicedArray = array_slice( $UserId , $offset , 1000 ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                RS_Main_Function_for_Background_Process::callback_to_add_existing_points_for_user( $offset ) ;
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 75 ) ;
            } else {
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 100 ) ;
                FP_WooCommerce_Log::log( 'Existing Points for User(s) added Successfully' ) ;
                delete_option( 'rs_old_points_background_updater_offset' ) ;
            }
        }

        public function add_existing_points_for_user( $UserId ) {
            if ( $UserId != 'no_users' ) {
                global $wpdb ;
                $OldPoints   = get_user_meta( $UserId , '_my_reward_points' , true ) ;
                $PointsTable = $wpdb->prefix . "rspointexpiry" ;
                $Query       = $wpdb->get_row( "SELECT * FROM $PointsTable WHERE userid = $UserId and expirydate = 999999999999" , ARRAY_A ) ;
                if ( srp_check_is_array( $Query ) ) {
                    $Points = $Query[ 'earnedpoints' ] + $OldPoints ;
                    $wpdb->update( $PointsTable , array( 'earnedpoints' => $Points ) , array( 'id' => $Query[ 'id' ] ) ) ;
                } else {
                    $wpdb->insert( $PointsTable , array(
                        'earnedpoints'      => $OldPoints ,
                        'usedpoints'        => '' ,
                        'expiredpoints'     => '0' ,
                        'userid'            => $UserId ,
                        'earneddate'        => time() ,
                        'expirydate'        => 999999999999 ,
                        'checkpoints'       => 'OUP' ,
                        'orderid'           => '' ,
                        'totalearnedpoints' => '' ,
                        'totalredeempoints' => '' ,
                        'reasonindetail'    => ''
                    ) ) ;
                }
            }
            return $UserId ;
        }

    }

}