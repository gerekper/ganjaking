<?php
/*
 * Support Tab Setting
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSSendPointsModule' ) ) {

    class RSSendPointsModule {

        public static function init() {

            add_action( 'rs_default_settings_fpsendpoints' , array( __CLASS__ , 'set_default_value' ) ) ;

            add_action( 'woocommerce_rs_settings_tabs_fpsendpoints' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsmodules_fpsendpoints' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action( 'woocommerce_admin_field_rs_select_user_for_send' , array( __CLASS__ , 'rs_select_user_to_send_point' ) ) ;

            add_action( 'woocommerce_admin_field_rs_send_point_applications_edit_lists' , array( __CLASS__ , 'send_point_applications_list_table' ) ) ;

            add_action( 'woocommerce_admin_field_rs_send_point_applications_list' , array( __CLASS__ , 'send_list_overall_applications' ) ) ;

            add_action( 'woocommerce_admin_field_rs_enable_disable_send_points_module' , array( __CLASS__ , 'enable_module' ) ) ;

            add_action( 'fp_action_to_reset_module_settings_fpsendpoints' , array( __CLASS__ , 'reset_send_points_module' ) ) ;

            add_action( 'rs_display_save_button_fpsendpoints' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

            add_action( 'rs_display_reset_button_fpsendpoints' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce ;
            return apply_filters( 'woocommerce_fpsendpoints' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Send Point(s) Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_send_points_module'
                ) ,
                array(
                    'type' => 'rs_enable_disable_send_points_module' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_send_points_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Send Point(s) Form Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_send_point_setting'
                ) ,
                array(
                    'name'     => __( 'Enable Send Point(s)' , SRP_LOCALE ) ,
                    'id'       => 'rs_enable_msg_for_send_point' ,
                    'newids'   => 'rs_enable_msg_for_send_point' ,
                    'std'      => '2' ,
                    'default'  => '2' ,
                    'class'    => 'rs_enable_msg_for_send_point' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'User Selection type for Sending Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_select_send_points_user_type' ,
                    'newids'   => 'rs_select_send_points_user_type' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'class'    => 'rs_select_send_points_user_type' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'All Users' , SRP_LOCALE ) ,
                        '2' => __( 'Selected User(s)' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'type' => 'rs_select_user_for_send' ,
                ) ,
                array(
                    'name'     => __( 'Current Reward Points Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_total_send_points_request' ,
                    'std'      => 'Current Reward Points' ,
                    'default'  => 'Current Reward Points' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_total_send_points_request' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Points to Send Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_points_to_send_request' ,
                    'std'      => 'Points to Send' ,
                    'default'  => 'Points to Send' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_points_to_send_request' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Username Selection Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_send_points_user_selection_field' ,
                    'newids'   => 'rs_send_points_user_selection_field' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'class'    => 'rs_send_points_user_selection_field' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Search Field' , SRP_LOCALE ) ,
                        '2' => __( 'Text Field' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                    'desc'     => esc_html__( 'If the search field is selected, then the user can search the other user using username/email id & select them to send points. If Text Field is selected, then the user needs to type the entire username/email id to send points to other users' , SRP_LOCALE )
                ) ,
                array(
                    'name'     => esc_html__( 'Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_send_points_username_field_label' ,
                    'std'      => 'Enter the Username/Email ID' ,
                    'default'  => 'Enter the Username/Email ID' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_send_points_username_field_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => esc_html__( 'Placeholder' , SRP_LOCALE ) ,
                    'id'       => 'rs_send_points_username_placeholder' ,
                    'std'      => 'Enter the Username/Email id' ,
                    'default'  => 'Enter the Username/Email id' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_send_points_username_placeholder' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Select the User to Send Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_select_user_label' ,
                    'std'      => 'Select the user to send' ,
                    'default'  => 'Select the user to send' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_select_user_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Select the User to Send Placeholder' , SRP_LOCALE ) ,
                    'id'       => 'rs_select_user_placeholder' ,
                    'std'      => 'Select User to Send Points' ,
                    'default'  => 'Select User to Send Points' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_select_user_placeholder' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Reason Field' , SRP_LOCALE ) ,
                    'id'      => 'rs_reason_for_send_points_user' ,
                    'class'   => 'rs_reason_for_send_points_user' ,
                    'desc'    => 'By enabling this checkbox, reason field will be displayed as Mandatory in the Send Points Form' ,
                    'std'     => 'yes' ,
                    'default' => 'yes' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_reason_for_send_points_user' ,
                ) ,
                array(
                    'name'     => __( 'Reason for Send Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_reason_for_send_points' ,
                    'std'      => 'Enter Some Reason' ,
                    'default'  => 'Enter Some Reason' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_reason_for_send_points' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Send Point Form Submit Button Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_select_points_submit_label' ,
                    'std'      => 'Submit' ,
                    'default'  => 'Submit' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_select_points_submit_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Send Points Request Approval Type' , SRP_LOCALE ) ,
                    'id'       => 'rs_request_approval_type' ,
                    'newids'   => 'rs_request_approval_type' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'class'    => 'rs_request_approval_type' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Manual Approval' , SRP_LOCALE ) ,
                        '2' => __( 'Auto Approval' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Message to display when Send Points Request is Submitted Successfully' , SRP_LOCALE ) ,
                    'id'       => 'rs_message_send_point_request_submitted' ,
                    'std'      => 'Send Point Request Submitted' ,
                    'default'  => 'Send Point Request Submitted' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_message_send_point_request_submitted' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Message to display when Send Points Request is Submitted via Auto Approval' , SRP_LOCALE ) ,
                    'id'       => 'rs_message_send_point_request_submitted_for_auto' ,
                    'std'      => 'Points has been sent Successfully' ,
                    'default'  => 'Points has been sent Successfully' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_message_send_point_request_submitted_for_auto' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Restriction on Sending Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_limit_for_send_point' ,
                    'newids'   => 'rs_limit_for_send_point' ,
                    'std'      => '2' ,
                    'default'  => '2' ,
                    'class'    => 'rs_limit_for_send_point' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Maximum Points which can be Sent' , SRP_LOCALE ) ,
                    'id'       => 'rs_limit_send_points_request' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_limit_send_points_request' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to display when Send Points is greater than the Maximum Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_err_when_point_greater_than_limit' ,
                    'std'      => 'Please Enter Points less than {limitpoints}' ,
                    'default'  => 'Please Enter Points less than {limitpoints}' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_err_when_point_greater_than_limit' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_send_point_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Send Point(s) Request List' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_request_for_send_point_setting'
                ) ,
                array(
                    'type' => 'rs_send_point_applications_list' ,
                ) ,
                array(
                    'type' => 'rs_send_point_applications_edit_lists' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_request_for_send_point_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Email Notifications for Send Points' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_email_notification_for_send_points'
                ) ,
                array(
                    'name'    => __( 'Enable to Send Email for Sendpoints Request Notification for Admin ' , SRP_LOCALE ) ,
                    'id'      => 'rs_mail_for_send_points_notification_admin' ,
                    'class'   => 'rs_mail_for_send_points_notification_admin' ,
                    'desc'    => 'Enable to Send Email for Sendpoints Request Notification for Admin ' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_mail_for_send_points_notification_admin' ,
                ) ,
                array(
                    'name'     => __( 'Select Email Sender Option for Admin ' , SRP_LOCALE ) ,
                    'id'       => 'rs_mail_sender_for_admin' ,
                    'class'    => 'rs_mail_sender_for_admin' ,
                    'std'      => 'woocommerce' ,
                    'default'  => 'woocommerce' ,
                    'type'     => 'radio' ,
                    'options'  => array(
                        'woocommerce' => __( 'Woocommerce' , SRP_LOCALE ) ,
                        'local'       => __( 'Local' , SRP_LOCALE ) ,
                    ) ,
                    'newids'   => 'rs_mail_sender_for_admin' ,
                    'desc_tip' => true ,
                    'desc'     => __( 'Woocommerce - Default Email from name and from address <br> Local - Manually Adding name for from name and from address' , SRP_LOCALE ) ,
                ) ,
                array(
                    'name'    => __( '"From" Name' , SRP_LOCALE ) ,
                    'id'      => 'rs_from_name_for_sendpoints_for_admin' ,
                    'class'   => 'rs_from_name_for_sendpoints_for_admin' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_from_name_for_sendpoints_for_admin' ,
                ) ,
                array(
                    'name'    => __( '"From" Email' , SRP_LOCALE ) ,
                    'id'      => 'rs_from_email_for_sendpoints_for_admin' ,
                    'class'   => 'rs_from_email_for_sendpoints_for_admin' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'email' ,
                    'newids'  => 'rs_from_email_for_sendpoints_for_admin' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject for Sendpoints Request Notification for Admin' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_for_send_points_notification_admin' ,
                    'class'   => 'rs_email_subject_for_send_points_notification_admin' ,
                    'std'     => 'Store Admin Receives Request - Notification' ,
                    'default' => 'Store Admin Receives Request - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_for_send_points_notification_admin' ,
                ) ,
                array(
                    'name'    => __( 'Email Message for Sendpoints Request Notification for Admin' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_for_send_points_notification_admin' ,
                    'class'   => 'rs_email_message_for_send_points_notification_admin' ,
                    'std'     => 'Hi , <br><br> The Sendpoints request given by [sender] to send [points] points to [receiver] <br><br> Send Points Request Type is [Type]<br><br>The Status is [request_status]<br><br>Thanks<br>' ,
                    'default' => 'Hi , <br><br> The Sendpoints request given by [sender] to send [points] points to [receiver] <br><br> Thanks<br>' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_for_send_points_notification_admin' ,
                ) ,
                array(
                    'name'    => __( 'Enable this option to Send Mail about Sending points Request Status(Accepted or Rejected) to the User(Sender)' , SRP_LOCALE ) ,
                    'id'      => 'rs_mail_for_send_points_confirmation_mail_for_user' ,
                    'class'   => 'rs_mail_for_send_points_confirmation_mail_for_user' ,
                    'desc'    => 'Enable this option to Send Mail about Sending points Request Status(Accepted or Rejected) to the User(Sender) eg: Person A Send the points to Person B in which Confirmation Mail for Person A indicating send points is Accepted or Rejected' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_mail_for_send_points_confirmation_mail_for_user' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject for Confirmation Request' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_for_send_points_confirmation' ,
                    'class'   => 'rs_email_subject_for_send_points_confirmation' ,
                    'std'     => 'Confirmation Request for Send Points - Notification' ,
                    'default' => 'Confirmation Request for Send Points - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_for_send_points_confirmation' ,
                ) ,
                array(
                    'name'    => __( 'Email Message for Confirmation Request' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_for_send_points_confirmation' ,
                    'class'   => 'rs_email_message_for_send_points_confirmation' ,
                    'std'     => 'Hi [user_name] , <br><br>  The Admin [request] the Request to send [points] Points for [receiver_name] <br><br>Thanks<br>' ,
                    'default' => 'Hi [user_name] , <br><br>  The Admin [request] the Request to send [points] Points for [receiver_name] <br><br>Thanks<br>' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_for_send_points_confirmation' ,
                ) ,
                array(
                    'name'    => __( 'Enable this option to Send Mail about Sending points Request Status(Accepted or Rejected) to the User(Receiver)' , SRP_LOCALE ) ,
                    'id'      => 'rs_mail_for_send_points_for_user' ,
                    'class'   => 'rs_mail_for_send_points_for_user' ,
                    'desc'    => 'Enable this option to Send Mail about Sending points Request Status(Accepted or Rejected) to the User(Receiver) eg: Person A Send Points to Person B in which Person B receives an email with request status Accepted or Rejected ' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_mail_for_send_points_for_user' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_for_send_points' ,
                    'class'   => 'rs_email_subject_for_send_points' ,
                    'std'     => 'Send Points - Notification' ,
                    'default' => 'Send Points - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_for_send_points' ,
                ) ,
                array(
                    'name'    => __( 'Email Message' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_for_send_points' ,
                    'class'   => 'rs_email_message_for_send_points' ,
                    'std'     => 'Hi [user_name] , <br><br> You have Got [rs_sendpoints] Points from [specific_user] <br><br> Reason: [reason_message] <br><br>The Request is [status]<br><br> Thanks<br>' ,
                    'default' => 'Hi [user_name] , <br><br> You have Got [rs_sendpoints] Points from [specific_user] <br><br> Reason: [reason_message] <br><br>The Request is [status]<br><br> Thanks<br>' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_for_send_points' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_sendpoints_mail_end' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Error Message(s) for Send Point(s) Form' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_error_msg_setting'
                ) ,
                array(
                    'name'     => __( 'Error Message to be displayed when Points to Send field is left Empty' , SRP_LOCALE ) ,
                    'id'       => 'rs_err_when_point_field_empty' ,
                    'std'      => 'Please Enter the Points to Send' ,
                    'default'  => 'Please Enter the Points to Send' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_err_when_point_field_empty' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to display when User doesn\'t have Points in their Account' , SRP_LOCALE ) ,
                    'id'       => 'rs_msg_when_user_have_no_points' ,
                    'std'      => 'You have no Points to Send' ,
                    'default'  => 'You have no Points to Send' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_msg_when_user_have_no_points' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to be displayed when User entered Points more than the Available Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_error_msg_when_points_is_more' ,
                    'std'      => 'Please Enter the Points less than your Current Points' ,
                    'default'  => 'Please Enter the Points less than your Current Points' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_error_msg_when_points_is_more' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to be displayed when Select User field is left Empty' , SRP_LOCALE ) ,
                    'id'       => 'rs_err_for_empty_user' ,
                    'std'      => 'Please Select the User to Send Points' ,
                    'default'  => 'Please Select the User to Send Points' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_err_for_empty_user' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => esc_html__( 'Error Message to be displayed when a Username/Email ID text field is left Empty' , SRP_LOCALE ) ,
                    'id'       => 'rs_username_empty_error_message' ,
                    'std'      => 'Please enter the username/email id' ,
                    'default'  => 'Please enter the username/email id' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_username_empty_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to be displayed when an invalid Username/Email id entered' , SRP_LOCALE ) ,
                    'id'       => 'rs_invalid_username_error_message' ,
                    'std'      => 'Please enter the valid username/email id' ,
                    'default'  => 'Please enter the valid username/email id' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_invalid_username_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to be displayed when a user entered the restricted Username/Email id' , SRP_LOCALE ) ,
                    'id'       => 'rs_restricted_username_error_message' ,
                    'std'      => 'This user has been restricted to receive points' ,
                    'default'  => 'This user has been restricted to receive points' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_restricted_username_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to be displayed when Enter Some Reason Field is Left Empty' , SRP_LOCALE ) ,
                    'id'       => 'rs_err_for_empty_reason_user' ,
                    'std'      => 'Please Enter Some Reason in the Field ' ,
                    'default'  => 'Please Enter Some Reason in the Field' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_err_for_empty_reason_user' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to be displayed when entered Points is not a Number' , SRP_LOCALE ) ,
                    'id'       => 'rs_err_when_point_is_not_number' ,
                    'std'      => 'Please Enter only the Number' ,
                    'default'  => 'Please Enter only the Number' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_err_when_point_is_not_number' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_error_msg_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Shortcode used in Send Points' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_shortcode_for_send_points'
                ) ,
                array(
                    'type' => 'title' ,
                    'desc' => '<b>{limitpoints}</b> - To display send points limitation'
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_shortcode_for_send_points' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                    ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields( RSSendPointsModule::reward_system_admin_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options( RSSendPointsModule::reward_system_admin_fields() ) ;
            if ( isset( $_POST[ 'rs_send_points_module_checkbox' ] ) ) {
                update_option( 'rs_send_points_activated' , $_POST[ 'rs_send_points_module_checkbox' ] ) ;
            } else {
                update_option( 'rs_send_points_activated' , 'no' ) ;
            }

            //send points users update
            if ( isset( $_POST[ 'rs_select_users_list_for_send_point' ] ) ) {
                update_option( 'rs_select_users_list_for_send_point' , $_POST[ 'rs_select_users_list_for_send_point' ] ) ;
            } else {
                update_option( 'rs_select_users_list_for_send_point' , '' ) ;
            }
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function set_default_value() {
            foreach ( RSSendPointsModule::reward_system_admin_fields() as $setting )
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
        }

        public static function reset_send_points_module() {
            $settings = RSSendPointsModule::reward_system_admin_fields() ;
            RSTabManagement::reset_settings( $settings ) ;
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_send_points_activated' ) , 'rs_send_points_module_checkbox' , 'rs_send_points_activated' ) ;
        }

        public static function rs_select_user_to_send_point() {
            $field_id    = "rs_select_users_list_for_send_point" ;
            $field_label = "Select User(s)" ;
            $getuser     = get_option( 'rs_select_users_list_for_send_point' ) ;
            echo user_selection_field( $field_id , $field_label , $getuser ) ;
        }

        public static function send_point_validation( $item ) {
            $messages = array() ;
            if ( empty( $messages ) )
                return true ;
            return implode( '<br />' , $messages ) ;
        }

        public static function send_point_applications_list_table( $item ) {
            global $wpdb ;
            $table_name = $wpdb->prefix . 'sumo_reward_send_point_submitted_data' ;
            $message    = '' ;
            $notice     = '' ;
            $default    = array(
                'id'                  => 0 ,
                'userid'              => '' ,
                'pointstosend'        => '' ,
                'sendercurrentpoints' => '' ,
                'selecteduser'        => '' ,
                'status'              => '' ,
                    ) ;

            if ( isset( $_REQUEST[ 'nonce' ] ) ) {
                if ( wp_verify_nonce( $_REQUEST[ 'nonce' ] , basename( __FILE__ ) ) ) {
                    $item       = shortcode_atts( $default , $_REQUEST ) ;
                    $item_valid = self::send_point_validation( $item ) ;
                    if ( $item_valid === true ) {
                        if ( $item[ 'id' ] == 0 ) {
                            $result       = $wpdb->insert( $table_name , $item ) ;
                            $item[ 'id' ] = $wpdb->insert_id ;
                            if ( $result ) {
                                $message = __( 'Item was successfully saved' ) ;
                            } else {
                                $notice = __( 'There was an error while saving item' ) ;
                            }
                        } else {
                            $result = $wpdb->update( $table_name , $item , array( 'id' => $item[ 'id' ] ) ) ;
                            if ( $result ) {
                                $message = __( 'Item was successfully updated' ) ;
                            } else {
                                $notice = __( 'There was an error while updating item' ) ;
                            }
                        }
                    } else {
                        // if $item_valid not true it contains error message(s)
                        $notice = $item_valid ;
                    }
                }
            } else {
                // if this is not post back we load item to edit or give new one to create
                $item = $default ;

                if ( isset( $_REQUEST[ 'send_application_id' ] ) ) {
                    $item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d" , $_REQUEST[ 'send_application_id' ] ) , ARRAY_A ) ;

                    if ( ! $item ) {
                        $item   = $default ;
                        $notice = __( 'Item not found' ) ;
                    }
                }
            }
            if ( isset( $_REQUEST[ 'send_application_id' ] ) ) {
                $dateformat = get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ;
                ?>
                <div class="wrap">
                    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
                    <h3>
                        <?php _e( 'Edit Cashback Status' , SRP_LOCALE ) ; ?>
                        <a class="add-new-h2" href="<?php echo get_admin_url( get_current_blog_id() , 'admin.php?page=rewardsystem_callback&tab=send_applications' ) ; ?>"><?php _e( 'Back to list' ) ?></a>
                    </h3>
                    <?php if ( ! empty( $notice ) ): ?>
                        <div id="notice" class="error"><p><?php echo $notice ?></p></div>
                    <?php endif ; ?>
                    <?php if ( ! empty( $message ) ): ?>
                        <div id="message" class="updated"><p><?php echo $message ?></p></div>
                    <?php endif ; ?>
                    <form id="form" method="POST">
                        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ) ?>"/>
                        <input type="hidden" name="id" value="<?php echo $item[ 'id' ] ?>"/>
                        <input type="hidden" name="userid" value="<?php echo $item[ 'userid' ] ; ?>"/>
                        <input type="hidden" value="<?php echo $item[ 'setvendoradmins' ] ; ?>" name="setvendoradmins"/>
                        <input type="hidden" value="<?php echo $item[ 'setusernickname' ] ; ?>" name="setusernickname"/>
                        <input type="hidden" value="<?php echo date_i18n( $dateformat ) ; ?>" name="date"/>
                        <div class="metabox-holder" id="poststuff">
                            <div id="post-body">
                                <div id="post-body-content">
                                    <table class="form-table">
                                        <tbody>                                        
                                            <tr>
                                                <th scope="row"><?php _e( 'Points for Send' , SRP_LOCALE ) ; ?></th>
                                                <td>
                                                    <input type="text" name="pointstosend" id="setvendorname" value="<?php echo $item[ 'pointstosend' ] ; ?>"/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><?php _e( 'Current User Point' , SRP_LOCALE ) ; ?></th>
                                                <td>
                                                    <textarea name="sendercurrentpoints" rows="3" cols="30"><?php echo $item[ 'sendercurrentpoints' ] ; ?></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><?php _e( 'Application Status' , SRP_LOCALE ) ; ?></th>
                                                <td>
                                                    <?php
                                                    $selected_approved = $item[ 'status' ] == 'Paid' ? "selected=selected" : '' ;
                                                    $selected_rejected = $item[ 'status' ] == 'Due' ? "selected=selected" : '' ;
                                                    ?>
                                                    <select name = "status">                                                    
                                                        <option value = "Paid" <?php echo $selected_approved ; ?>><?php _e( 'Paid' , SRP_LOCALE ) ; ?></option>
                                                        <option value = "Due" <?php echo $selected_rejected ; ?>><?php _e( 'Due' , SRP_LOCALE ) ; ?></option>
                                                    </select>
                                                </td>
                                            </tr>                                                                                

                                        </tbody>
                                    </table>
                                    <input type="submit" value="<?php _e( 'Save Changes' , SRP_LOCALE ) ?>" id="submit" class="button-primary" name="submit">
                                </div>
                            </div>
                        </div>                    
                    </form>
                </div>
                <?php
            }
        }

        public static function send_list_overall_applications() {
            global $wpdb ;
            global $current_section ;
            global $current_tab ;
            $testListTable = new FPRewardSystemSendpointTabList() ;
            $testListTable->prepare_items() ;
            if ( ! isset( $_REQUEST[ 'send_application_id' ] ) ) {
                $array_list = array() ;
                $message    = '' ;
                if ( 'send_application_delete' === $testListTable->current_action() ) {
                    $message = '<div class="updated below-h2" id="message"><p>' . sprintf( __( 'Items deleted: %d' ) , count( $_REQUEST[ 'id' ] ) ) . '</p></div>' ;
                }
                echo $message ;
                $testListTable->display() ;
            }
        }

    }

    RSSendPointsModule::init() ;
}