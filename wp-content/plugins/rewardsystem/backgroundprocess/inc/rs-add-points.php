<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ;
}
if ( ! class_exists( 'RS_Add_Points_For_User' ) ) {

    /**
     * RS_Add_Points_For_User Class.
     */
    class RS_Add_Points_For_User extends WP_Background_Process {

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

            $this->action = 'rs_add_points_for_user_updater_' . $this->user_id ;

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
            $this->add_points_for_user( $item ) ;
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
            $offset        = get_user_meta( $this->get_user_id() , 'rs_add_points_background_updater_offset' , true ) ;
            $selected_user = get_user_meta( $this->get_user_id() , 'selected_user' ) ;
            $SlicedArray   = array_slice( $selected_user , $offset , 1000 ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                RS_Main_Function_for_Background_Process::callback_to_add_points_for_user( $selected_user , $offset ) ;
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 75 ) ;
            } else {
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 100 ) ;
                FP_WooCommerce_Log::log( 'Points for User(s) added Successfully' ) ;
                delete_user_meta( $this->get_user_id() , 'rs_add_points_background_updater_offset' ) ;
                delete_user_meta( $this->get_user_id() , 'selected_user' ) ;
                delete_user_meta( $this->get_user_id() , 'selected_options' ) ;
            }
        }

        public function add_points_for_user( $UserId ) {
            if ( $UserId != 'no_users' ) {
                $selected_options = get_user_meta( $this->get_user_id() , 'selected_options' , true ) ;
                if ( $selected_options[ 'state' ] == 'yes' ) {
                    update_option( 'rs_email_subject_message' , $selected_options[ 'subject' ] ) ;
                    update_option( 'rs_email_message' , $selected_options[ 'message' ] ) ;
                    $to         = is_object( get_userdata( $UserId ) ) ? get_userdata( $UserId )->user_email : '' ;
                    $new_obj    = new RewardPointsOrder( 0 , 'no' ) ;
                    $PointsData = new RS_Points_data( $UserId ) ;
                    if ( get_option( 'rs_enable_disable_max_earning_points_for_user' ) == 'yes' ) {
                        $new_obj->check_point_restriction( $selected_options[ 'points' ] , 0 , 'MAP' , $UserId , '' , '' , '' , '' , $selected_options[ 'reason' ] ) ;
                    } else {
                        $valuestoinsert = array( 'expireddate' => strtotime( $selected_options[ 'expdate' ] ) , 'manualaddpoints' => 'yes' , 'pointstoinsert' => $selected_options[ 'points' ] , 'event_slug' => 'MAP' , 'user_id' => $UserId , 'reasonindetail' => $selected_options[ 'reason' ] , 'totalearnedpoints' => $selected_options[ 'points' ] ) ;
                        $new_obj->total_points_management( $valuestoinsert ) ;
                    }

                    if ( ($selected_options[ 'enablemail' ] == "true") && ($selected_options[ 'points' ] != '') ) {
                        $Expiry            = ( get_option( 'rs_expireddate_for_added_points' ) != '' ) ? get_option( 'rs_expireddate_for_added_points' ) : 'All Time Usage' ;
                        $shortcode_message = str_replace( '[rs_earned_points]' , $selected_options[ 'points' ] , str_replace( '[rs_expiry]' , $Expiry , $selected_options[ 'message' ] ) ) ;
                        $replaced_message  = str_replace( '[balance_points]' , $PointsData->total_available_points() , $shortcode_message ) ;
                        $finalmsg          = str_replace( '[site_name]' , get_option( 'blogname' ) , $replaced_message ) ;

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
                        
                        remove_filter( 'woocommerce_email_footer_text' , 'srp_footer_link' ) ;
                    }
                }
            }
            return $UserId ;
        }

    }

}