<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ;
}
if ( ! class_exists( 'RS_Remove_Points_For_User' ) ) {

    /**
     * RS_Remove_Points_For_User Class.
     */
    class RS_Remove_Points_For_User extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action ;

        /**
         * @var string
         */
        protected $user_id ;

        /**
         * Initiate new background process
         */
        public function __construct() {

            $this->user_id = get_current_user_id() ;

            $this->action = 'rs_remove_points_for_user_updater_' . $this->user_id ;

            parent::__construct() ;
        }

        /**
         * Get User Id
         */
        public function get_user_id() {
            return $this->user_id ;
        }

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
            $this->remove_points_for_user( $item ) ;
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
            $offset        = get_user_meta( $this->get_user_id() , 'rs_remove_points_background_updater_offset' , true ) ;
            $selected_user = get_user_meta( $this->get_user_id() , 'selected_user' ) ;
            $SlicedArray   = array_slice( $selected_user , $offset , 1000 ) ;
            if ( is_array( $SlicedArray ) && ! empty( $SlicedArray ) ) {
                RS_Main_Function_for_Background_Process::callback_to_remove_points_for_user( $selected_user , $offset ) ;
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 75 ) ;
            } else {
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 100 ) ;
                FP_WooCommerce_Log::log( 'Points for User(s) removed Successfully' ) ;
                delete_user_meta( $this->get_user_id() , 'rs_remove_points_background_updater_offset' ) ;
                delete_user_meta( $this->get_user_id() , 'selected_user' ) ;
                delete_user_meta( $this->get_user_id() , 'selected_options' ) ;
            }
        }

        public function remove_points_for_user( $UserId ) {
            if ( $UserId != 'no_users' ) {
                $selected_options = get_user_meta( $this->get_user_id() , 'selected_options' , true ) ;
                if ( $selected_options[ 'state' ] == 'yes' ) {
                    update_option( 'rs_email_subject_for_remove' , $selected_options[ 'subject' ] ) ;
                    update_option( 'rs_email_message_for_remove' , $selected_options[ 'message' ] ) ;
                    global $wpdb ;
                    $table_name = $wpdb->prefix . 'rspointexpiry' ;
                    $PointsData = new RS_Points_data( $UserId ) ;
                    $to         = is_object( get_userdata( $UserId ) ) ? get_userdata( $UserId )->user_email : '' ;
                    if ( $selected_options[ 'points' ] <= $PointsData->total_available_points() ) {
                        $pointsredeemed = RSPointExpiry::perform_calculation_with_expiry( $selected_options[ 'points' ] , $UserId ) ;
                        $table_args     = array(
                            'user_id'     => $UserId ,
                            'usedpoints'  => $selected_options[ 'points' ] ,
                            'checkpoints' => 'MRP' ,
                            'date'        => '999999999999' ,
                            'reason'      => $selected_options[ 'reason' ] ,
                                ) ;
                        RSPointExpiry::record_the_points( $table_args ) ;

                        if ( ($selected_options[ 'enablemail' ] == "true" ) ) {
                            $PointsData->reset( $UserId ) ;
                            $finalmsg = str_replace( array( '[rs_deleted_points]' , '[balance_points]' , '[site_name]' ) , array( $selected_options[ 'points' ] , $PointsData->total_available_points() , get_option( 'blogname' ) ) , $selected_options[ 'message' ] ) ;

                            global $unsublink2 ;
                            $wpnonce    = wp_create_nonce( 'rs_unsubscribe_' . $UserId ) ;
                            $unsublink  = esc_url_raw( add_query_arg( array( 'userid' => $UserId , 'unsub' => 'yes' , 'nonce' => $wpnonce ) , site_url() ) ) ;
                            $unsublink2 = str_replace( '{rssitelinkwithid}' , $unsublink , get_option( 'rs_unsubscribe_link_for_email' ) ) ;
                            add_filter( 'woocommerce_email_footer_text' , 'srp_footer_link' ) ;

                            ob_start() ;
                            wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $selected_options[ 'subject' ] ) ) ;
                            echo $finalmsg ;
                            wc_get_template( 'emails/email-footer.php' ) ;

                            $headers = "MIME-Version: 1.0\r\n" ;
                            $headers .= "Content-Type: text/html; charset=UTF-8\r\n" ;
                            $headers .= "From: " . get_option( 'woocommerce_email_from_name' ) . " <" . get_option( 'woocommerce_email_from_address' ) . ">\r\n" ;
                            $headers .= "Reply-To: " . get_option( 'woocommerce_email_from_name' ) . " <" . get_option( 'woocommerce_email_from_address' ) . ">\r\n" ;

                            $woo_temp_msg = ob_get_clean() ;

                            if ( WC_VERSION <= ( float ) ('2.2.0') ) {

                                wp_mail( $to , $selected_options[ 'subject' ] , $woo_temp_msg , $headers = '' ) ;
                            } else {

                                $mailer = WC()->mailer() ;
                                $mailer->send( $to , $selected_options[ 'subject' ] , $woo_temp_msg , $headers ) ;
                            }
                        }
                    }
                }
            }
            return $UserId ;
        }

    }

}