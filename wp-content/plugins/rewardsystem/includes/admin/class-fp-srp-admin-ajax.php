<?php
/*
 * Admin Ajax
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'FP_Rewardsystem_Admin_Ajax' ) ) {

    /**
     * FP_Rewardsystem_Admin_Ajax Class
     */
    class FP_Rewardsystem_Admin_Ajax {

        /**
         * FP_Rewardsystem_Admin_Ajax Class initialization
         */
        public static function init() {
            $actions = array(
                'update_start_date'            => false ,
                'update_end_date'              => false ,
                'update_user_selection_format' => false ,
                'update_date_type'             => false ,
                'update_report_start_date'     => false ,
                'update_report_end_date'       => false ,
                'update_user_type'             => false ,
                'update_selected_user'         => false ,
                'update_report_date_type'      => false ,
                'update_type_of_points'        => false ,
                'updatestatusforemail'         => false ,
                'updatestatusforemailexpiry'   => false ,
                'newemailexpirytemplate'       => false ,
                'editemailexpirytemplate'      => false ,
                'deletetemplateforemailexpiry' => false ,
                'unsubscribeuser'              => false ,
                'sendmail'                     => false ,
                'newemailtemplate'             => false ,
                'editemailtemplate'            => false ,
                'deletetemplateforemail'       => false ,
                'activatemodule'               => false ,
                'fp_reset_settings'            => false ,
                'fp_reset_users_data'          => false ,
                'fp_reset_order_meta'          => false ,
                'generatepointurl'             => false ,
                'removepointurl'               => false ,
                'add_wcf_fields'               => false ,
                'wcf_field_type'               => false ,
                'cus_field_search'             => false ,
                'srp_user_search'              => true ,
                'send_points_data'             => true ,
                'enable_reward_program'        => true ,
                'add_coupon_usage_reward_rule' => false ,
                    ) ;

            foreach ( $actions as $action => $nopriv ) {
                add_action( 'wp_ajax_' . $action , array( __CLASS__ , $action ) ) ;

                if ( $nopriv )
                    add_action( 'wp_ajax_nopriv_' . $action , array( __CLASS__ , $action ) ) ;
            }
        }

        public static function update_start_date() {
            check_ajax_referer( 'fp-start-date' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'start_date' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                delete_option( 'selected_start_date' ) ;
                update_option( 'selected_start_date' , $_POST[ 'start_date' ] ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function update_end_date() {
            check_ajax_referer( 'fp-end-date' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'end_date' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                delete_option( 'selected_end_date' ) ;
                update_option( 'selected_end_date' , $_POST[ 'end_date' ] ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function update_user_selection_format() {
            check_ajax_referer( 'fp-user-selection' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'selected_format' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                update_option( 'selected_format' , $_POST[ 'selected_format' ] ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function update_report_start_date() {
            check_ajax_referer( 'fp-start-date' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'export_report_startdate' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                delete_option( 'selected_report_start_date' ) ;
                update_option( 'selected_report_start_date' , $_POST[ 'export_report_startdate' ] ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function update_report_end_date() {
            check_ajax_referer( 'fp-end-date' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'export_report_enddate' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                delete_option( 'selected_report_end_date' ) ;
                update_option( 'selected_report_end_date' , $_POST[ 'export_report_enddate' ] ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function update_user_type() {
            check_ajax_referer( 'fp-user-type' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'user_type' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                update_option( 'selected_user_type_report' , $_POST[ 'user_type' ] ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function update_selected_user() {
            check_ajax_referer( 'fp-selected-user' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'selectedusers' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                $selecteduser = srp_check_is_array( $_POST[ 'selectedusers' ] ) ? $_POST[ 'selectedusers' ] : explode( ',' , $_POST[ 'selectedusers' ] ) ;
                update_option( 'rs_selected_user_list_export_report' , $selecteduser ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function update_report_date_type() {
            check_ajax_referer( 'fp-date-type' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'datetype' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                update_option( 'fp_date_type' , $_POST[ 'datetype' ] ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function update_date_type() {
            check_ajax_referer( 'fp-date-type' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'datetype' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                update_option( 'fp_date_type_selection' , $_POST[ 'datetype' ] ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function update_type_of_points() {
            check_ajax_referer( 'fp-points-type' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                if ( isset( $_POST[ 'totalpoints' ] ) ) {
                    delete_option( 'export_total_points' ) ;
                    update_option( 'export_total_points' , $_POST[ 'totalpoints' ] ) ;
                }
                if ( isset( $_POST[ 'earnpoints' ] ) ) {
                    delete_option( 'export_earn_points' ) ;
                    update_option( 'export_earn_points' , $_POST[ 'earnpoints' ] ) ;
                }
                if ( isset( $_POST[ 'redeempoints' ] ) ) {
                    delete_option( 'export_redeem_points' ) ;
                    update_option( 'export_redeem_points' , $_POST[ 'redeempoints' ] ) ;
                }
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function updatestatusforemail() {
            check_ajax_referer( 'fp-update-status' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'row_id' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                global $wpdb ;
                $TableName = $wpdb->prefix . 'rs_templates_email' ;
                $Status    = $_POST[ 'status' ] == 'ACTIVE' ? 'NOTACTIVE' : 'ACTIVE' ;
                $wpdb->update( $TableName , array( 'rs_status' => $Status ) , array( 'id' => $_POST[ 'row_id' ] ) ) ;
                wp_send_json_success( array( 'content' => $Status ) ) ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function updatestatusforemailexpiry() {
            check_ajax_referer( 'fp-update-status' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'row_id' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                global $wpdb ;
                $TableName = $wpdb->prefix . 'rs_expiredpoints_email' ;
                $Status    = $_POST[ 'status' ] == 'ACTIVE' ? 'NOTACTIVE' : 'ACTIVE' ;
                $wpdb->update( $TableName , array( 'rs_status' => $Status ) , array( 'id' => $_POST[ 'row_id' ] ) ) ;
                wp_send_json_success( array( 'content' => $Status ) ) ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function newemailexpirytemplate() {
            check_ajax_referer( 'fp-new-template' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'templatename' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                global $wpdb ;
                $TableName = $wpdb->prefix . 'rs_expiredpoints_email' ;
                $wpdb->insert( $TableName , array(
                    'template_name' => stripslashes( $_POST[ 'templatename' ] ) ,
                    'sender_opt'    => stripslashes( $_POST[ 'senderoption' ] ) ,
                    'from_name'     => stripslashes( $_POST[ 'fromname' ] ) ,
                    'from_email'    => stripslashes( $_POST[ 'fromemail' ] ) ,
                    'subject'       => stripslashes( $_POST[ 'subject' ] ) ,
                    'message'       => stripslashes( $_POST[ 'message' ] ) ,
                    'noofdays'      => stripslashes( $_POST[ 'noofdays' ] ) ,
                    'rs_status'     => stripslashes( $_POST[ 'templatestatus' ] ) ,
                ) ) ;
                update_option( 'rs_new_template_id_for_expiry' , $wpdb->insert_id ) ;
                wp_send_json_success( array( 'content' => "Settings Saved" ) ) ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function editemailexpirytemplate() {
            check_ajax_referer( 'fp-edit-template' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'templateid' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                global $wpdb ;
                $TableName = $wpdb->prefix . 'rs_expiredpoints_email' ;
                $wpdb->update( $TableName , array(
                    'template_name' => stripslashes( $_POST[ 'templatename' ] ) ,
                    'sender_opt'    => stripslashes( $_POST[ 'senderoption' ] ) ,
                    'from_name'     => stripslashes( $_POST[ 'fromname' ] ) ,
                    'from_email'    => stripslashes( $_POST[ 'fromemail' ] ) ,
                    'subject'       => stripslashes( $_POST[ 'subject' ] ) ,
                    'message'       => stripslashes( $_POST[ 'message' ] ) ,
                    'noofdays'      => stripslashes( $_POST[ 'noofdays' ] ) ,
                    'rs_status'     => stripslashes( $_POST[ 'templatestatus' ] ) ,
                        ) , array( 'id' => $_POST[ 'templateid' ] ) ) ;
                wp_send_json_success( array( 'content' => "Settings Updated" ) ) ;
            } catch ( Exception $ex ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function deletetemplateforemailexpiry() {
            check_ajax_referer( 'fp-delete-template' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'row_id' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                global $wpdb ;
                $TableName = $wpdb->prefix . 'rs_expiredpoints_email' ;
                $wpdb->delete( $TableName , array( 'id' => $_POST[ 'row_id' ] ) ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function unsubscribeuser() {
            check_ajax_referer( 'fp-unsubscribe-email' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'unsubscribe' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                if ( is_array( $_POST[ 'unsubscribe' ] ) ) {
                    foreach ( $_POST[ 'unsubscribe' ] as $unsubscribeuser ) {
                        $user_info        = get_userdata( $unsubscribeuser ) ;
                        $headers          = "MIME-Version: 1.0\r\n" ;
                        $headers          .= "Content-Type: text/html; charset=UTF-8\r\n" ;
                        $headers          .= "From: " . get_option( 'woocommerce_email_from_name' ) . " <" . get_option( 'woocommerce_email_from_address' ) . ">\r\n" ;
                        $headers          .= "Reply-To: " . get_option( 'woocommerce_email_from_name' ) . " <" . get_option( 'woocommerce_email_from_address' ) . ">\r\n" ;
                        $emailsubject     = $_POST[ 'emailsubject' ] ;
                        $findemailsubject = str_replace( '[sitename]' , get_option( 'blogname' ) , $emailsubject ) ;
                        $message          = $_POST[ 'emailmessage' ] ;
                        update_option( 'rs_subject_for_user_unsubscribe' , $_POST[ 'emailsubject' ] ) ;
                        update_option( 'rs_message_for_user_unsubscribe' , $_POST[ 'emailmessage' ] ) ;
                        $subject          = $findemailsubject ;
                        $to               = is_object( $user_info ) ? $user_info->user_email : '' ;
                        update_user_meta( $unsubscribeuser , 'unsub_value' , 'yes' ) ;
                        wp_mail( $to , $subject , $message , $headers ) ;
                    }
                }
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function newemailtemplate() {
            check_ajax_referer( 'fp-new-template' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'templatename' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                global $wpdb ;
                $TableName = $wpdb->prefix . 'rs_templates_email' ;

                if ( ! is_array( $_POST[ 'sendmailselected' ] ) )
                    $_POST[ 'sendmailselected' ] = explode( ',' , $_POST[ 'sendmailselected' ] ) ;

                $wpdb->insert( $TableName , array(
                    'template_name'        => stripslashes( $_POST[ 'templatename' ] ) ,
                    'sender_opt'           => stripslashes( $_POST[ 'senderoption' ] ) ,
                    'from_name'            => stripslashes( $_POST[ 'fromname' ] ) ,
                    'from_email'           => stripslashes( $_POST[ 'fromemail' ] ) ,
                    'subject'              => stripslashes( $_POST[ 'subject' ] ) ,
                    'message'              => stripslashes( $_POST[ 'message' ] ) ,
                    'rs_status'            => stripslashes( $_POST[ 'templatestatus' ] ) ,
                    'earningpoints'        => stripslashes( $_POST[ 'earningpoints' ] ) ,
                    'redeemingpoints'      => stripslashes( $_POST[ 'redeemingpoints' ] ) ,
                    'mailsendingoptions'   => stripslashes( $_POST[ 'mailsendingoptions' ] ) ,
                    'rsmailsendingoptions' => stripslashes( $_POST[ 'rsmailsendingoptions' ] ) ,
                    'minimum_userpoints'   => stripslashes( $_POST[ 'minuserpoints' ] ) ,
                    'sendmail_options'     => isset( $_POST[ 'sendmailoptions' ] ) ? stripslashes( $_POST[ 'sendmailoptions' ] ) : '' ,
                    'sendmail_to'          => serialize( $_POST[ 'sendmailselected' ] ) ,
                ) ) ;
                update_option( 'rs_new_template_id' , $wpdb->insert_id ) ;
                wp_send_json_success( array( 'content' => "Settings Saved" ) ) ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function editemailtemplate() {
            check_ajax_referer( 'fp-edit-template' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'templateid' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                global $wpdb ;
                $TableName = $wpdb->prefix . 'rs_templates_email' ;
                $wpdb->update( $TableName , array(
                    'template_name'        => stripslashes( $_POST[ 'templatename' ] ) ,
                    'sender_opt'           => stripslashes( $_POST[ 'senderoption' ] ) ,
                    'from_name'            => stripslashes( $_POST[ 'fromname' ] ) ,
                    'from_email'           => stripslashes( $_POST[ 'fromemail' ] ) ,
                    'subject'              => stripslashes( $_POST[ 'subject' ] ) ,
                    'message'              => stripslashes( $_POST[ 'message' ] ) ,
                    'rs_status'            => stripslashes( $_POST[ 'templatestatus' ] ) ,
                    'earningpoints'        => stripslashes( $_POST[ 'earningpoints' ] ) ,
                    'redeemingpoints'      => stripslashes( $_POST[ 'redeemingpoints' ] ) ,
                    'mailsendingoptions'   => stripslashes( $_POST[ 'mailsendingoptions' ] ) ,
                    'rsmailsendingoptions' => stripslashes( $_POST[ 'rsmailsendingoptions' ] ) ,
                    'minimum_userpoints'   => stripslashes( $_POST[ 'minuserpoints' ] ) ,
                    'sendmail_options'     => isset( $_POST[ 'sendmailoptions' ] ) ? stripslashes( $_POST[ 'sendmailoptions' ] ) : '' ,
                    'sendmail_to'          => serialize( $_POST[ 'sendmailselected' ] ) ,
                        ) , array( 'id' => $_POST[ 'templateid' ] ) ) ;
                wp_send_json_success( array( 'content' => "Settings Updated" ) ) ;
            } catch ( Exception $ex ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function sendmail() {
            if ( ! isset( $_POST ) || ! isset( $_POST[ 'email_id' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                $to             = $_POST[ 'email_id' ] ;
                $email_subject  = isset( $_POST[ 'rs_subject' ] ) ? ($_POST[ 'rs_subject' ]) : '' ;
                $content        = "Hi, You have earned X amount of points on this site which can be used for getting discount on future purchases. Thanks." ;
                $templatestatus = isset( $_POST[ 'rs_status_template' ] ) ? ($_POST[ 'rs_status_template' ]) : 'NOTACTIVE' ;
                $senderoption   = isset( $_POST[ 'rs_sender_options' ] ) ? ($_POST[ 'rs_sender_options' ]) : 'woo' ;
                $from_name      = isset( $_POST[ 'rs_from_name' ] ) ? ($_POST[ 'rs_from_name' ]) : '' ;
                $from_email     = isset( $_POST[ 'rs_from_email' ] ) ? ($_POST[ 'rs_from_email' ]) : '' ;
                if ( $templatestatus == 'ACTIVE' ) {
                    ob_start() ;
                    wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $email_subject ) ) ;
                    echo $content ;
                    wc_get_template( 'emails/email-footer.php' ) ;
                    $msg_content = ob_get_clean() ;
                    $headers     = "MIME-Version: 1.0\r\n" ;
                    $headers     .= "Content-Type: text/html; charset=UTF-8\r\n" ;
                    if ( $senderoption == 'local' ) {
                        FPRewardSystem::$rs_from_email_address = $from_email ;
                        FPRewardSystem::$rs_from_name          = $from_name ;
                    }
                    add_filter( 'woocommerce_email_from_address' , 'rs_alter_from_email_of_woocommerce' , 10 , 2 ) ;
                    add_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;
                    $mailer                                = WC()->mailer() ;
                    if ( $mailer->send( $to , $email_subject , $msg_content , $headers ) )
                        wp_send_json_success( array( 'content' => 'Mail Sent' ) ) ;
                    remove_filter( 'woocommerce_email_from_address' , 'rs_alter_from_email_of_woocommerce' , 10 , 2 ) ;
                    remove_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;
                    FPRewardSystem::$rs_from_email_address = false ;
                    FPRewardSystem::$rs_from_name          = false ;
                }
                wp_send_json_success( array( 'content' => 'Mail Not Sent' ) ) ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function deletetemplateforemail() {
            check_ajax_referer( 'fp-delete-template' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'row_id' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                global $wpdb ;
                $TableName = $wpdb->prefix . 'rs_templates_email' ;
                $wpdb->delete( $TableName , array( 'id' => $_POST[ 'row_id' ] ) ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function activatemodule() {
            check_ajax_referer( 'fp-activate-module' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'enable' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                $metakey = $_POST[ 'metakey' ] ;
                $enable  = $_POST[ 'enable' ] ;
                update_option( $metakey , $enable ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function fp_reset_settings() {
            check_ajax_referer( 'rs-reset-tab' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                foreach ( RSGeneralTabSetting::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                        update_option( 'rs_earn_point' , '1' ) ;
                        update_option( 'rs_earn_point_value' , '1' ) ;
                        update_option( 'rs_redeem_point' , '1' ) ;
                        update_option( 'rs_redeem_point_value' , '1' ) ;
                        update_option( 'rs_redeem_point_for_cash_back' , '1' ) ;
                        update_option( 'rs_redeem_point_value_for_cash_back' , '1' ) ;
                    }
                }

                foreach ( RSAddorRemovePoints::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSProductPurchaseModule::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSReferralSystemModule::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSRewardPointsForAction::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSPointExpiryModule::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSRedeemingModule::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSPointPriceModule::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSEmailModule::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSGiftVoucher::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSMessage::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSSocialReward::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSSms::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSCashbackModule::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSPointURL::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSGatewayModule::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSSendPointsModule::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSLocalization::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSAdvancedSetting::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSDiscountsCompatability::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                foreach ( RSCouponCompatability::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                delete_option( 'rewards_dynamic_rule_couponpoints' ) ;

                foreach ( RSNominee::reward_system_admin_fields() as $setting ) {
                    if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                        delete_option( $setting[ 'newids' ] ) ;
                        add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                    }
                }

                delete_option( 'rewards_dynamic_rule_manual' ) ;

                delete_transient( 'woocommerce_cache_excluded_uris' ) ;

                wp_send_json_success() ;
            } catch ( Exception $ex ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function fp_reset_users_data() {
            check_ajax_referer( 'reset-data-for-user' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'resetdatafor' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                global $wpdb ;
                $PointsTable    = $wpdb->prefix . 'rspointexpiry' ;
                $PointsLogTable = $wpdb->prefix . 'rsrecordpoints' ;
                $UsermetaTable  = $wpdb->prefix . 'usermeta' ;
                $UserIDs        = array() ;
                $OrderId        = array() ;

                if ( isset( $_POST[ 'resetpreviousorder' ] ) && $_POST[ 'resetpreviousorder' ] == 1 ) {
                    $args    = array( 'post_type' => 'shop_order' , 'numberposts' => '-1' , 'meta_query' => array( 'relation' => 'AND' , array( 'key' => 'reward_points_awarded' , 'compare' => 'EXISTS' ) , array( 'key' => 'earning_point_once' , 'compare' => 'EXISTS' ) ) , 'post_status' => 'published' , 'fields' => 'ids' , 'cache_results' => false ) ;
                    $OrderId = get_posts( $args ) ;
                }

                if ( isset( $_POST[ 'resetmanualreferral' ] ) && $_POST[ 'resetmanualreferral' ] == 1 )
                    delete_option( 'rewards_dynamic_rule_manual' , true ) ;

                $ResetUserPoints  = isset( $_POST[ 'rsresetuserpoints' ] ) ? $_POST[ 'rsresetuserpoints' ] : '' ;
                $ResetUserLogs    = isset( $_POST[ 'rsresetuserlogs' ] ) ? $_POST[ 'rsresetuserlogs' ] : '' ;
                $ResetMasterLogs  = isset( $_POST[ 'rsresetmasterlogs' ] ) ? $_POST[ 'rsresetmasterlogs' ] : '' ;
                $ResetReferrallog = isset( $_POST[ 'resetreferrallog' ] ) ? $_POST[ 'resetreferrallog' ] : '' ;

                if ( $_POST[ 'resetdatafor' ] == '2' && isset( $_POST[ 'rsselectedusers' ] ) ) {   //Selected User                    
                    $UserIDs = ! is_array( $_POST[ 'rsselectedusers' ] ) ? explode( ',' , $_POST[ 'rsselectedusers' ] ) : $_POST[ 'rsselectedusers' ] ;
                    if ( srp_check_is_array( $UserIDs ) && $ResetReferrallog == '1' ) {
                        foreach ( $UserIDs as $UserId ) {
                            $ReferralLogs = get_option( 'rs_referral_log' ) ;
                            if ( ! isset( $ReferralLogs[ $UserId ] ) )
                                continue ;

                            unset( $ReferralLogs[ $UserId ] ) ;
                            update_option( 'rs_referral_log' , $ReferralLogs ) ;
                        }
                    }
                } else {
                    $UserIDs = $wpdb->get_col( "SELECT ID FROM $wpdb->prefix" . "users" ) ;
                }
                $UserIDs = implode( ',' , $UserIDs ) ;
                if ( $ResetUserPoints == '1' ) {
                    $wpdb->query( "DELETE FROM $PointsTable WHERE userid IN ($UserIDs)" ) ;
                    $wpdb->query( "DELETE FROM $UsermetaTable WHERE meta_key IN ('_my_reward_points','rs_earned_points_before_delete','rs_user_total_earned_points','rs_expired_points_before_delete','rs_redeem_points_before_delete') AND user_id IN ($UserIDs)" ) ;
                }
                if ( $ResetUserLogs == '1' ) {
                    $wpdb->query( "UPDATE $PointsLogTable SET showuserlog = true WHERE userid IN ($UserIDs)" ) ;
                    $wpdb->query( "DELETE FROM $UsermetaTable WHERE meta_key IN ('_my_points_log') AND user_id IN ($UserIDs)" ) ;
                }
                if ( $ResetMasterLogs == '1' ) {
                    $wpdb->query( "UPDATE $PointsLogTable SET showmasterlog = true WHERE userid IN ($UserIDs)" ) ;
                    delete_option( 'rsoveralllog' ) ;
                }
                if ( $ResetReferrallog == '1' )
                    delete_option( 'rs_referral_log' , true ) ;

                $reset_record_table_log = isset( $_POST[ 'resetrecordlogtable' ] ) ? wc_clean( wp_unslash( $_POST[ 'resetrecordlogtable' ] ) ) : '' ;
                $reset_type             = isset( $_POST[ 'resetdatafor' ] ) ? wc_clean( wp_unslash( $_POST[ 'resetdatafor' ] ) ) : wc_clean( wp_unslash( $_POST[ 'resetdatafor' ] ) ) ;
                $UserIDs                = ! is_array( $_POST[ 'rsselectedusers' ] ) ? explode( ',' , wc_clean( wp_unslash( $_POST[ 'rsselectedusers' ] ) ) ) : wc_clean( wp_unslash( $_POST[ 'rsselectedusers' ] ) ) ;

                if ( '1' == $reset_type ) {
                    $wpdb->query( "TRUNCATE TABLE $PointsLogTable" ) ;
                } else {
                    if ( srp_check_is_array( array_filter( ( array ) ( $UserIDs ) ) ) && $reset_record_table_log ) {
                        $wpdb->query( "DELETE FROM $PointsTable WHERE userid IN ($UserIDs)" ) ;
                    }
                }

                wp_send_json_success( array( 'content' => $OrderId ) ) ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function fp_reset_order_meta() {
            check_ajax_referer( 'reset-previous-order-meta' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'ids' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                if ( $_POST[ 'ids' ] == 'done' )
                    wp_send_json_success( array( 'content' => 'success' ) ) ;

                $order = $_POST[ 'ids' ] ;
                foreach ( $order as $order_id ) {
                    delete_post_meta( $order_id , 'reward_points_awarded' ) ;
                    delete_post_meta( $order_id , 'earning_point_once' ) ;
                }
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function srp_user_search() {
            check_ajax_referer( 'fp-user-search' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                $display_user    = array() ;
                $customers_query = new WP_User_Query( array(
                    'fields'         => 'all' ,
                    'orderby'        => 'display_name' ,
                    'search'         => '*' . $_REQUEST[ 'term' ] . '*' ,
                    'search_columns' => array( 'ID' , 'user_login' , 'user_email' , 'user_nicename' )
                        ) ) ;
                $customers       = $customers_query->get_results() ;
                $current_user_id = get_current_user_id() ;
                if ( get_option( 'rs_select_send_points_user_type' ) == '1' ) {
                    if ( ! empty( $customers ) ) {
                        foreach ( $customers as $customer ) {
                            if ( $current_user_id != $customer->ID ) {
                                $display_user[ $customer->ID ] = $customer->display_name . ' (#' . $customer->ID . ' - ' . sanitize_email( $customer->user_email ) . ')' ;
                            }
                        }
                    }
                }
                if ( get_option( 'rs_select_send_points_user_type' ) == '2' ) {
                    if ( get_option( 'rs_select_users_list_for_send_point' ) != '' ) {
                        $userids      = ! is_array( get_option( 'rs_select_users_list_for_send_point' ) ) ? array_filter( array_map( 'absint' , ( array ) explode( ',' , get_option( 'rs_select_users_list_for_send_point' ) ) ) ) : get_option( 'rs_select_users_list_for_send_point' ) ;
                        $display_user = self::display_select_field( $userids , $customers , $current_user_id ) ;
                    }
                }
                wp_send_json( $display_user ) ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function display_select_field( $userids , $customers , $current_user_id ) {
            $found_customers = array() ;
            if ( ! empty( $customers ) ) {
                foreach ( $customers as $customer ) {
                    if ( $current_user_id != $customer->ID ) {
                        if ( in_array( $customer->ID , $userids ) ) {
                            $found_customers[ $customer->ID ] = $customer->display_name . ' (#' . $customer->ID . ' - ' . sanitize_email( $customer->user_email ) . ')' ;
                        }
                    }
                }
            }
            return $found_customers ;
        }

        public static function send_points_data() {
            check_ajax_referer( 'fp-send-points-data' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'points' ] ) || ! isset( $_POST[ 'receiver_info' ] ) || ($_POST[ 'receiver_info' ] == '' ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                global $wpdb ;
                global $woocommerce ;
                $ApprovalType  = get_option( 'rs_request_approval_type' ) ;
                $SenderId      = $_POST[ 'senderid' ] ;
                $SenderEmail   = get_userdata( $SenderId )->user_email ;
                $SenderName    = $_POST[ 'sendername' ] ;
                $Points        = $_POST[ 'points' ] ;
                $Receiver_info = $_POST[ 'receiver_info' ] ;

                if ( '1' == get_option( 'rs_send_points_user_selection_field' , 1 ) ) {
                    $user = get_user_by( 'ID' , $Receiver_info ) ;
                } else {
                    $user = is_object( get_user_by( 'login' , $Receiver_info ) ) ? get_user_by( 'login' , $Receiver_info ) : get_user_by( 'email' , $Receiver_info ) ;
                }

                if ( ! is_object( $user ) ) {
                    throw new Exception( 'invalid_username_error' ) ;
                }

                $ReceiverId = $user->ID ;
                if ( get_current_user_id() == $ReceiverId ) {
                    throw new Exception( esc_html__( 'Invalid User' , SRP_LOCALE ) ) ;
                }

                if ( '2' == get_option( 'rs_select_send_points_user_type' ) && ! in_array( $ReceiverId , ( array ) get_option( 'rs_select_users_list_for_send_point' ) ) ) {
                    throw new Exception( 'restricted_username_error' ) ;
                }

                $ReceiverName      = $user->user_login ;
                $ReceiverFirstName = $user->first_name ;
                $ReceiverLastName  = $user->last_name ;
                $ReceiverEmail     = $user->user_email ;

                $Status = ( $ApprovalType == '1' ) ? $_POST[ 'status' ] : 'Paid' ;
                update_option( 'rs_reason_for_send_points_mail' , $_POST[ 'reason' ] ) ;
                if ( get_option( 'rs_mail_for_send_points_notification_admin' ) == 'yes' ) {
                    $Email_subject = get_option( 'rs_email_subject_for_send_points_notification_admin' ) ;
                    $Email_message = get_option( 'rs_email_message_for_send_points_notification_admin' ) ;
                    $message       = str_replace( array( '[sender]' , '[receiver]' , '[points]' ) , array( $SenderName , $ReceiverName , $Points ) , $Email_message ) ;
                    if ( get_option( 'rs_mail_sender_for_admin' ) == 'woocommerce' ) {
                        $admin_email = get_option( 'admin_email' ) ;
                        $admin_name  = get_bloginfo( 'name' , 'display' ) ;
                    } else {
                        $admin_email = get_option( 'rs_from_email_for_sendpoints_for_admin' ) ;
                        $admin_name  = get_option( 'rs_from_name_for_sendpoints_for_admin' ) ;
                    }
                    $headers = "MIME-Version: 1.0\r\n" ;
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n" ;
                    $headers .= "Reply-To: " . $admin_name . " <" . $admin_email . ">\r\n" ;

                    $ReplaceValue = ( $ApprovalType == '1' ) ? array( 'Manual Approval' , 'Still Waiting' ) : array( 'Auto Approval' , 'Accepted' ) ;
                    $AdminMsg     = str_replace( array( '[Type]' , '[request_status]' ) , $ReplaceValue , $message ) ;
                    $AdminMsg     = do_shortcode( $AdminMsg ) ;
                    ob_start() ;
                    wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $Email_subject ) ) ;
                    echo $AdminMsg ;
                    wc_get_template( 'emails/email-footer.php' ) ;
                    $woo_temp_msg = ob_get_clean() ;
                    if ( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) {
                        wp_mail( $admin_email , $Email_subject , $AdminMsg , $headers ) ;
                    } else {
                        $mailer = WC()->mailer() ;
                        $mailer->send( $admin_email , $Email_subject , $woo_temp_msg , $headers ) ;
                    }
                }
                if ( $ApprovalType == '2' ) {
                    if ( get_option( 'rs_mail_for_send_points_for_user' ) == 'yes' ) {
                        $email_subject                = get_option( 'rs_email_subject_for_send_points' ) ;
                        $email_message                = get_option( 'rs_email_message_for_send_points' ) ;
                        $message                      = str_replace( array( '[rs_sendpoints]' , '[specific_user]' , '[user_name]' ) , array( $Points , $SenderName , $ReceiverName ) , $email_message ) ;
                        $Email_message                = str_replace( array( '[status]' , '[reason_message]' , '[rsfirstname]' , '[rslastname]' ) , array( 'Accepted' , $_POST[ 'reason' ] , $ReceiverFirstName , $ReceiverLastName ) , $message ) ;
                        $Email_message                = do_shortcode( $Email_message ) ;
                        add_filter( 'woocommerce_email_footer_text' , 'srp_footer_link' ) ;
                        ob_start() ;
                        wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $email_subject ) ) ;
                        echo $Email_message ;
                        wc_get_template( 'emails/email-footer.php' ) ;
                        $woo_temp_msg                 = ob_get_clean() ;
                        $headers                      = "MIME-Version: 1.0\r\n" ;
                        $headers                      .= "From: \"{$SenderName}\" <{$SenderEmail}>\n" . "Content-Type: text/html; charset=\"" . get_option( 'blog_charset' ) . "\"\n" ;
                        $headers                      .= "Reply-To: " . $ReceiverName . " <" . $ReceiverEmail . ">\r\n" ;
                        FPRewardSystem::$rs_from_name = $SenderName ;
                        add_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;
                        if ( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) {
                            wp_mail( $ReceiverEmail , $email_subject , $Email_message , $headers ) ;
                        } else {
                            $mailer = WC()->mailer() ;
                            $mailer->send( $ReceiverEmail , $email_subject , $woo_temp_msg , $headers ) ;
                        }
                        remove_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 1 ) ;
                    }
                    if ( get_option( 'rs_mail_for_send_points_confirmation_mail_for_user' ) == 'yes' ) {
                        $email_subject = get_option( 'rs_email_subject_for_send_points_confirmation' ) ;
                        $email_message = get_option( 'rs_email_message_for_send_points_confirmation' ) ;
                        $message       = str_replace( array( '[user_name]' , '[request]' , '[points]' , '[receiver_name]' ) , array( $SenderName , 'Accepted' , $Points , $ReceiverName ) , $email_message ) ;
                        $Email_message = str_replace( array( '[status]' , '[reason_message]' , '[rsfirstname]' , '[rslastname]' ) , array( 'Accepted' , $_POST[ 'reason' ] , $ReceiverFirstName , $ReceiverLastName ) , $message ) ;
                        $Email_message = do_shortcode( $Email_message ) ;
                        if ( get_option( 'rs_mail_sender_for_admin' ) == 'woocommerce' ) {
                            $admin_email = get_option( 'admin_email' ) ;
                            $admin_name  = get_bloginfo( 'name' , 'display' ) ;
                        } else {
                            $admin_email = get_option( 'rs_from_email_for_sendpoints_for_admin' ) ;
                            $admin_name  = get_option( 'rs_from_name_for_sendpoints_for_admin' ) ;
                        }
                        add_filter( 'woocommerce_email_footer_text' , 'srp_footer_link' ) ;
                        ob_start() ;
                        wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $email_subject ) ) ;
                        echo $Email_message ;
                        wc_get_template( 'emails/email-footer.php' ) ;
                        $woo_temp_msg                 = ob_get_clean() ;
                        $headers                      = "MIME-Version: 1.0\r\n" ;
                        $headers                      .= "From: \"{$admin_name}\" <{$admin_email}>\n" . "Content-Type: text/html; charset=\"" . get_option( 'blog_charset' ) . "\"\n" ;
                        $headers                      .= "Reply-To: " . $SenderName . " <" . $SenderEmail . ">\r\n" ;
                        FPRewardSystem::$rs_from_name = $admin_name ;
                        add_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;
                        if ( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) {
                            wp_mail( $SenderEmail , $email_subject , $Email_message , $headers ) ;
                        } else {
                            $mailer = WC()->mailer() ;
                            $mailer->send( $SenderEmail , $email_subject , $woo_temp_msg , $headers ) ;
                        }
                        remove_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 1 ) ;
                    }
                    $table_args = array(
                        'user_id'           => $ReceiverId ,
                        'pointstoinsert'    => $Points ,
                        'checkpoints'       => 'SP' ,
                        'totalearnedpoints' => $Points ,
                        'nomineeid'         => $SenderId ,
                            ) ;
                    RSPointExpiry::insert_earning_points( $table_args ) ;
                    RSPointExpiry::record_the_points( $table_args ) ;
                }
                $wpdb->insert( $wpdb->prefix . "sumo_reward_send_point_submitted_data" , array( 'userid' => $SenderId , 'userloginname' => $SenderName , 'pointstosend' => $Points , 'sendercurrentpoints' => $_POST[ 'senderpoints' ] , 'status' => $Status , 'selecteduser' => $ReceiverId , 'date' => date_i18n( 'Y-m-d H:i:s' ) ) ) ;
                $redeempoints = RSPointExpiry::perform_calculation_with_expiry( $Points , $SenderId ) ;
                $table_args   = array(
                    'user_id'     => $SenderId ,
                    'usedpoints'  => $Points ,
                    'checkpoints' => ( $ApprovalType == '1' ) ? 'SPB' : 'SENPM' ,
                    'nomineeid'   => $ReceiverId ,
                        ) ;

                RSPointExpiry::record_the_points( $table_args ) ;

                wp_send_json_success( array( 'content' => 'success' ) ) ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function generatepointurl() {
            check_ajax_referer( 'fp-generate-url' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'points' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                $NewArr       = array( uniqid() => $_POST ) ;
                $PreValue     = get_option( 'points_for_url_click' ) ;
                $UpdatedValue = srp_check_is_array( $PreValue ) ? array_merge( $PreValue , $NewArr ) : $NewArr ;
                update_option( 'points_for_url_click' , $UpdatedValue ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function removepointurl() {
            check_ajax_referer( 'fp-remove-url' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'uniqueid' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                $PreValue = get_option( 'points_for_url_click' ) ;
                if ( srp_check_is_array( $PreValue ) )
                    if ( array_key_exists( $_POST[ 'uniqueid' ] , $PreValue ) )
                        unset( $PreValue[ $_POST[ 'uniqueid' ] ] ) ;

                $PreValue = array_filter( $PreValue ) ;
                update_option( 'points_for_url_click' , $PreValue ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function enable_reward_program() {
            check_ajax_referer( 'earn-reward-points' , 'sumo_security' ) ;

            if ( ! isset( $_POST ) || ! isset( $_POST[ 'enable_reward_points' ] ) )
                throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

            try {
                update_user_meta( get_current_user_id() , 'allow_user_to_earn_reward_points' , $_POST[ 'enable_reward_points' ] ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function add_wcf_fields() {
            check_ajax_referer( 'srp-cus-reg-fields-nonce' , 'sumo_security' ) ;

            try {
                if ( ! isset( $_POST ) || ! isset( $_POST[ 'count' ] ) )
                    throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

                ob_start() ;
                $key     = $_POST[ 'count' ] ;
                ?>                
                <tr class="rs_rule_creation_for_custom_reg_field">
                <input type="hidden" id="rs_rule_id_for_custom_reg_field" value="<?php echo $key ; ?>"/>
                <td class="column-columnname">
                    <?php
                    $args    = array(
                        'class'              => 'wc-product-search rs_search_custom_field' ,
                        'id'                 => 'rs_rule_for_custom_reg_field[' . $key . '][custom_fields]' ,
                        'name'               => 'rs_rule_for_custom_reg_field[' . $key . '][custom_fields]' ,
                        'type'               => 'customfields' ,
                        'action'             => 'cus_field_search' ,
                        'multiple'           => false ,
                        'css'                => 'width: 100%;' ,
                        'placeholder'        => 'Select Custom Fields' ,
                        'options'            => array() ,
                        'translation_string' => SRP_LOCALE
                            ) ;
                    rs_custom_search_fields( $args ) ;
                    ?>
                </td>
                <td class="column-columnname">
                    <p class="rs_label_for_cus_field_type"></p>
                    <input type="hidden" class="rs_label_for_cus_field_type_hidden" name="rs_rule_for_custom_reg_field[<?php echo $key ; ?>][field_type]" value=""/>
                </td>
                <td class="column-columnname">
                    <input type="number" style="width:75% !important;" name="rs_rule_for_custom_reg_field[<?php echo $key ; ?>][reward_points]" min="0" value=""/>
                </td>
                <td class="column-columnname">
                    <p class="rs_label_for_datepicker_type"></p>
                </td>
                <td class="column-columnname">
                    <p class="rs_label_award_points_for_filling_datepicker"></p>
                </td>
                <td class="column-columnname">
                    <span class="rs_remove_rule_for_custom_reg_field button-primary"><?php _e( 'Remove Rule' , SRP_LOCALE ) ; ?></span>
                </td>
                </tr>
                <?php
                $content = ob_get_clean() ;
                ob_end_clean() ;
                wp_send_json_success( array( 'content' => $content ) ) ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function cus_field_search() {
            try {
                global $wpdb ;
                $listofcusfields = array() ;
                $data            = $wpdb->get_results( $wpdb->prepare( "SELECT ID as id, post_title as title FROM $wpdb->posts WHERE post_type=%s AND post_status=%s And post_title  LIKE %s" , 'fpcf_custom_fields' , 'fpcf_enabled' , '%' . $_GET[ 'term' ] . '%' ) , ARRAY_A ) ;
                if ( srp_check_is_array( $data ) ) {
                    foreach ( $data as $data_value ) {
                        $listofcusfields[ $data_value[ 'id' ] ] = $data_value[ 'title' ] ;
                    }
                }
                wp_send_json( $listofcusfields ) ;
            } catch ( Exception $ex ) {
                wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
            }
        }

        public static function wcf_field_type() {
            check_ajax_referer( 'srp-cus-reg-fields-nonce' , 'sumo_security' ) ;

            try {
                if ( ! isset( $_POST ) || ! isset( $_POST[ 'field_id' ] ) )
                    throw new exception( __( 'Invalid Request' , SRP_LOCALE ) ) ;

                $field_id   = $_POST[ 'field_id' ] ;
                $field_data = fpcf_get_custom_fields( $field_id ) ;
                wp_send_json_success( array( 'content' => strtoupper( $field_data->field_type ) ) ) ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function add_coupon_usage_reward_rule() {

            check_ajax_referer( 'srp-add-coupon-usage-rule-nonce' , 'sumo_security' ) ;

            try {

                if ( ! isset( $_POST[ 'rule_count' ] ) ) {
                    throw new exception( esc_html__( 'Invalid Request' , SRP_LOCALE ) ) ;
                }

                $coupons = get_posts(
                        array(
                            'post_type'   => 'shop_coupon' ,
                            'numberposts' => '-1' ,
                            's'           => '-sumo_' ,
                            'post_status' => 'publish' )
                        ) ;
                if ( ! srp_check_is_array( $coupons ) ) {
                    throw new exception( esc_html__( 'Since there is no coupon created in WooCommerce, you cannot add a rule.' , SRP_LOCALE ) ) ;
                }

                $saved_rules = get_option( 'rewards_dynamic_rule_couponpoints' ) ;
                if ( srp_check_is_array( $saved_rules ) && 1 == $_POST[ 'rule_count' ] ) {
                    $key = count( $saved_rules ) + absint( $_POST[ 'rule_count' ] ) ;
                } else {
                    $key = absint( $_POST[ 'rule_count' ] ) ;
                }

                ob_start() ;
                include (SRP_PLUGIN_PATH . '/includes/admin/views/add-coupon-usage-reward-rule.php') ;
                $html = ob_get_contents() ;
                ob_end_clean() ;

                wp_send_json_success( array( 'html' => $html ) ) ;
            } catch ( Exception $ex ) {
                wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
            }
        }

    }

    FP_Rewardsystem_Admin_Ajax::init() ;
}