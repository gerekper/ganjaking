<?php
/*
 * Support Tab Setting
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSCashbackModule' ) ) {

    class RSCashbackModule {

        public static function init() {

            add_action( 'rs_default_settings_fpcashback' , array( __CLASS__ , 'set_default_value' ) ) ;

            add_action( 'woocommerce_rs_settings_tabs_fpcashback' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsmodules_fpcashback' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action( 'woocommerce_admin_field_rs_select_inc_user_search_label' , array( __CLASS__ , 'rs_select_inc_user_search_label' ) ) ;

            add_action( 'woocommerce_admin_field_rs_select_exc_user_search_label' , array( __CLASS__ , 'rs_select_exc_user_search_label' ) ) ;

            add_action( 'woocommerce_admin_field_rs_encash_applications_list' , array( __CLASS__ , 'encash_list_overall_applications' ) ) ;

            add_action( 'woocommerce_admin_field_rs_encash_applications_edit_lists' , array( __CLASS__ , 'encash_applications_list_table' ) ) ;

            add_action( 'woocommerce_admin_field_redeeming_conversion_for_cash_back' , array( __CLASS__ , 'reward_system_redeeming_points_conversion_for_cash_back' ) ) ;

            add_action( 'woocommerce_admin_field_rs_enable_disable_cashback_module' , array( __CLASS__ , 'enable_module' ) ) ;

            add_action( 'fp_action_to_reset_module_settings_fpcashback' , array( __CLASS__ , 'reset_cashback_module' ) ) ;

            add_action( 'rs_display_save_button_fpcashback' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

            add_action( 'rs_display_reset_button_fpcashback' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;

            if ( check_whether_hoicker_is_active() ) {
                add_filter( 'woocommerce_fpcashback' , array( __CLASS__ , 'rs_function_to_add_label_for_wallet' ) ) ;
            }
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce ;
            $walletia_label     = get_option( 'rs_encashing_wallet_menu_label' ) ? get_option( 'rs_encashing_wallet_menu_label' ) : 'Hoicker Wallet' ;
            $list_of_user_roles = fp_user_roles() ;
            if ( check_whether_hoicker_is_active() ) {
                $payment_method = array(
                    '1' => __( 'PayPal' , SRP_LOCALE ) ,
                    '2' => __( 'Custom Payment' , SRP_LOCALE ) ,
                    '4' => __( $walletia_label , SRP_LOCALE ) ,
                    '3' => __( 'All' , SRP_LOCALE ) ,
                        ) ;
            } else {
                $payment_method = array(
                    '1' => __( 'PayPal' , SRP_LOCALE ) ,
                    '2' => __( 'Custom Payment' , SRP_LOCALE ) ,
                    '3' => __( 'All' , SRP_LOCALE ) ,
                        ) ;
                if ( get_option( 'rs_select_payment_method' ) === '4' ) {
                    update_option( 'rs_select_payment_method' , 3 ) ;
                }
            }
            return apply_filters( 'woocommerce_fpcashback' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Cashback Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_cashback_module'
                ) ,
                array(
                    'type' => 'rs_enable_disable_cashback_module' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_cashback_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Cashback Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_reward_point_encashing_settings'
                ) ,
                array(
                    'name'     => __( 'Enable Cashback for Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enable this option to provide the feature to Cashback the Reward Points earned by the Users' , SRP_LOCALE ) ,
                    'id'       => 'rs_enable_disable_encashing' ,
                    'std'      => '2' ,
                    'default'  => '2' ,
                    'type'     => 'select' ,
                    'newids'   => 'rs_enable_disable_encashing' ,
                    'options'  => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Cashback Form accessible selection' , SRP_LOCALE ) ,
                    'id'      => 'rs_user_selection_type_for_cashback' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_user_selection_type_for_cashback' ,
                    'options' => array(
                        '1' => __( 'All User(s)' , SRP_LOCALE ) ,
                        '2' => __( 'Include User(s)' , SRP_LOCALE ) ,
                        '3' => __( 'Exclude User(s)' , SRP_LOCALE ) ,
                        '4' => __( 'All User Role(s)' , SRP_LOCALE ) ,
                        '5' => __( 'Include User role(s)' , SRP_LOCALE ) ,
                        '6' => __( 'Exclude User role(s)' , SRP_LOCALE ) ,
                    )
                ) ,
                array(
                    'type' => 'rs_select_inc_user_search_label' ,
                ) ,
                array(
                    'type' => 'rs_select_exc_user_search_label' ,
                ) ,
                array(
                    'name'        => __( 'Include User Role(s)' , SRP_LOCALE ) ,
                    'id'          => 'rs_select_inc_userrole' ,
                    'css'         => 'min-width:343px;' ,
                    'std'         => '' ,
                    'default'     => '' ,
                    'placeholder' => 'Search for a User Role' ,
                    'type'        => 'multiselect' ,
                    'options'     => $list_of_user_roles ,
                    'newids'      => 'rs_select_inc_userrole' ,
                    'desc_tip'    => false ,
                ) ,
                array(
                    'name'        => __( 'Exclude User Role(s)' , SRP_LOCALE ) ,
                    'id'          => 'rs_select_exc_userrole' ,
                    'css'         => 'min-width:343px;' ,
                    'std'         => '' ,
                    'default'     => '' ,
                    'placeholder' => 'Search for a User Role' ,
                    'type'        => 'multiselect' ,
                    'options'     => $list_of_user_roles ,
                    'newids'      => 'rs_select_exc_userrole' ,
                    'desc_tip'    => false ,
                ) ,
                array(
                    'name'              => __( 'Minimum Points for Cashback of Reward Points' , SRP_LOCALE ) ,
                    'desc'              => __( 'Enter the Minimum points that the user should have in order to Submit the Cashback Request' , SRP_LOCALE ) ,
                    'id'                => 'rs_minimum_points_encashing_request' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'type'              => 'number' ,
                    'newids'            => 'rs_minimum_points_encashing_request' ,
                    'custom_attributes' => array(
                        'min' => 0
                    ) ,
                    'desc_tip'          => true ,
                ) ,
                array(
                    'name'              => __( 'Maximum Points for Cashback of Reward Points' , SRP_LOCALE ) ,
                    'desc'              => __( 'Enter the Maximum points that the user should enter order to Submit the Cashback Request' , SRP_LOCALE ) ,
                    'id'                => 'rs_maximum_points_encashing_request' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'type'              => 'number' ,
                    'newids'            => 'rs_maximum_points_encashing_request' ,
                    'custom_attributes' => array(
                        'min' => 0
                    ) ,
                    'desc_tip'          => true ,
                ) ,
                array(
                    'name'     => __( 'Allow User to Request Cashback' , SRP_LOCALE ) ,
                    'id'       => 'rs_allow_user_to_request_cashback' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'newids'   => 'rs_allow_user_to_request_cashback' ,
                    'options'  => array(
                        '1' => __( 'Editable' , SRP_LOCALE ) ,
                        '2' => __( 'Non-Editable' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Current Reward Points Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_total_points_for_cashback_request' ,
                    'std'      => 'Current Reward Points' ,
                    'default'  => 'Current Reward Points' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_total_points_for_cashback_request' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Points for Cashback Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Points the Label for Cashback' , SRP_LOCALE ) ,
                    'id'       => 'rs_encashing_points_label' ,
                    'std'      => 'Points for Cashback' ,
                    'default'  => 'Points for Cashback' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_encashing_points_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Reason for Cashback Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter label for Reason Cashback' , SRP_LOCALE ) ,
                    'id'       => 'rs_encashing_reason_label' ,
                    'std'      => 'Reason for Cashback' ,
                    'default'  => 'Reason for Cashback' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_encashing_reason_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Payment Method Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Payment Method Label for Cashback' , SRP_LOCALE ) ,
                    'id'       => 'rs_encashing_payment_method_label' ,
                    'std'      => 'Payment Method' ,
                    'default'  => 'Payment Method' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_encashing_payment_method_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Display Payment Method' , SRP_LOCALE ) ,
                    'id'      => 'rs_select_payment_method' ,
                    'std'     => '3' ,
                    'default' => '3' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_select_payment_method' ,
                    'options' => $payment_method ,
                ) ,
                array(
                    'name'    => __( 'Save Payment Details' , SRP_LOCALE ) ,
                    'desc'    => __( 'By enabling this option you can save your customer[s] payment details which they used in the form' , SRP_LOCALE ) ,
                    'id'      => 'rs_allow_admin_to_save_previous_payment_method' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_allow_admin_to_save_previous_payment_method' ,
                ) ,
                array(
                    'name'     => __( 'PayPal Email Address Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter PayPal Email Address Label for Cashback' , SRP_LOCALE ) ,
                    'id'       => 'rs_encashing_payment_paypal_label' ,
                    'std'      => 'PayPal Email Address' ,
                    'default'  => 'PayPal Email Address' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_encashing_payment_paypal_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Custom Payment Details Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Custom Payment Details Label for Cashback' , SRP_LOCALE ) ,
                    'id'       => 'rs_encashing_payment_custom_label' ,
                    'std'      => 'Custom Payment Details' ,
                    'default'  => 'Custom Payment Details' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_encashing_payment_custom_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Cashback Form Submit Button Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Cashback Form Submit Button Label ' , SRP_LOCALE ) ,
                    'id'       => 'rs_encashing_submit_button_label' ,
                    'std'      => 'Submit' ,
                    'default'  => 'Submit' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_encashing_submit_button_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'After submitting the Cashback form' , SRP_LOCALE ) ,
                    'id'      => 'rs_select_type_to_redirect' ,
                    'std'     => 'Submit' ,
                    'default' => 'Submit' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_select_type_to_redirect' ,
                    'options' => array(
                        '1' => __( 'Same Page' , SRP_LOCALE ) ,
                        '2' => __( 'Redirect to the Custom Page' , SRP_LOCALE ) ,
                    )
                ) ,
                array(
                    'name'     => __( 'Custom Page URL' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Custom Page URL' , SRP_LOCALE ) ,
                    'id'       => 'rs_custom_page_url_after_submit' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_custom_page_url_after_submit' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_reward_point_encashing_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Google reCAPTCHA Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_google_recaptcha_settings'
                ) ,
                array(
                    'name'    => __( 'Enable Google reCAPTCHA for Cash Back Form' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_recaptcha_to_display' ,
                    'class'   => 'rs_enable_recaptcha_to_display' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_recaptcha_to_display' ,
                ) ,
                array(
                    'name'     => __( 'Google reCaptcha Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_google_recaptcha_label' ,
                    'std'      => 'Google ReCaptcha' ,
                    'default'  => 'Google reCaptcha' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_google_recaptcha_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Site Key ' , SRP_LOCALE ) ,
                    'id'       => 'rs_google_recaptcha_site_key' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_google_recaptcha_site_key' ,
                    'desc_tip' => false ,
                    'desc'     => sprintf( __( 'You can find the Site key %s' , SRP_LOCALE ) , '<a target="_blank" href="https://www.google.com/recaptcha/admin#list">' . __( "here" , SRP_LOCALE ) . '</a>' ) ,
                ) ,
                array(
                    'name'     => __( 'Secret Key ' , SRP_LOCALE ) ,
                    'id'       => 'rs_google_recaptcha_secret_key' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_google_recaptcha_secret_key' ,
                    'desc_tip' => false ,
                    'desc'     => sprintf( __( 'You can find the Secret key %s' , SRP_LOCALE ) , '<a target="_blank" href="https://www.google.com/recaptcha/admin#list">' . __( "here" , SRP_LOCALE ) . '</a>' ) ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_google_recaptcha_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Redeeming Points Conversion Settings for Cashback' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_redeem_point_conversion_for_cash_back'
                ) ,
                array(
                    'type' => 'redeeming_conversion_for_cash_back' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_redeem_point_conversion_cash_back' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Cashback Request List' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_request_for_cash_back_setting'
                ) ,
                array(
                    'type' => 'rs_encash_applications_list' ,
                ) ,
                array(
                    'type' => 'rs_encash_applications_edit_lists' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_request_for_cash_back_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Email Notification for Cashback' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_email_notfication_for_cashback'
                ) ,
                array(
                    'name'    => __( 'Enable Email Notification for Admin' , SRP_LOCALE ) ,
                    'desc'    => __( 'By enabling this option, admin to receives the email notification when the users request for cashback' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_notification_for_Admin_cashback' ,
                    'class'   => 'rs_email_notification_for_Admin_cashback' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_email_notification_for_Admin_cashback' ,
                ) ,
                array(
                    'name'     => __( 'Email Sender Option' , SRP_LOCALE ) ,
                    'id'       => 'rs_mail_sender_for_admin_for_cashback' ,
                    'class'    => 'rs_mail_sender_for_admin_for_cashback' ,
                    'std'      => 'woocommerce' ,
                    'default'  => 'woocommerce' ,
                    'type'     => 'radio' ,
                    'options'  => array(
                        'woocommerce' => __( 'Woocommerce' , SRP_LOCALE ) ,
                        'local'       => __( 'Local' , SRP_LOCALE ) ,
                    ) ,
                    'newids'   => 'rs_mail_sender_for_admin_for_cashback' ,
                    'desc_tip' => true ,
                    'desc'     => __( 'Woocommerce - Default Email from name and from address <br> Local - Manually Adding name for from name and from address' , SRP_LOCALE ) ,
                ) ,
                array(
                    'name'    => __( '"From" Name' , SRP_LOCALE ) ,
                    'id'      => 'rs_from_name_for_admin_cashback' ,
                    'class'   => 'rs_from_name_for_admin_cashback' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_from_name_for_admin_cashback' ,
                ) ,
                array(
                    'name'    => __( '"From" Email' , SRP_LOCALE ) ,
                    'id'      => 'rs_from_email_for_admin_cashback' ,
                    'class'   => 'rs_from_email_for_admin_cashback' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'email' ,
                    'newids'  => 'rs_from_email_for_admin_cashback' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'id'      => 'rs_email_subject_message_for_cashback' ,
                    'newids'  => 'rs_email_subject_message_for_cashback' ,
                    'class'   => 'rs_email_subject_message_for_cashback' ,
                    'std'     => 'Cashback Request – Notification' ,
                    'default' => 'Cashback Request – Notification' ,
                ) ,
                array(
                    'name'    => __( 'Email Message' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'id'      => 'rs_email_message_for_cashback' ,
                    'newids'  => 'rs_email_message_for_cashback' ,
                    'class'   => 'rs_email_message_for_cashback' ,
                    'std'     => 'Hi,<br><br>The Cashback Request is given by [username] with [_rs_point_for_cashback] points. <br><br> Selected Payment Method : [rs_payment_gateway]<br><br>Thanks<br><br>' ,
                    'default' => 'Hi,<br><br>The Cashback Request is given by [username] with [_rs_point_for_cashback] points. <br><br>Payment Method [rs_payment_gateway]<br><br>Thanks<br><br>' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_email_notification_for_cashback' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'My Cashback Table Label Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_my_cashback_label_settings'
                ) ,
                array(
                    'name'     => __( 'My Cashback Table' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_cashback_table' ,
                    'std'      => '1' ,
                    'desc_tip' => true ,
                    'default'  => '1' ,
                    'newids'   => 'rs_my_cashback_table' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'My Cashback Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the My Cashback Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_cashback_title' ,
                    'std'      => 'My Cashback' ,
                    'default'  => 'My Cashback' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_cashback_title' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'S.No Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Serial Number Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_cashback_sno_label' ,
                    'std'      => 'S.No' ,
                    'default'  => 'S.No' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_cashback_sno_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Username Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Username Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_cashback_userid_label' ,
                    'std'      => 'Username' ,
                    'default'  => 'Username' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_cashback_userid_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Requested for Cashback Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Requested for Cashback Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_cashback_requested_label' ,
                    'std'      => 'Requested for Cashback' ,
                    'default'  => 'Requested for Cashback' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_cashback_requested_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Status Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Status On Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_cashback_status_label' ,
                    'std'      => 'Status' ,
                    'default'  => 'Status' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_cashback_status_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Action Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Action On Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_cashback_action_label' ,
                    'std'      => 'Action' ,
                    'default'  => 'Action' ,
                    'type'     => 'rs_action_for_cash_back' ,
                    'newids'   => 'rs_my_cashback_action_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'My Cashback Table - Shortcode' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_cashback_table_shortcode' ,
                    'std'      => '1' ,
                    'desc_tip' => true ,
                    'default'  => '1' ,
                    'newids'   => 'rs_my_cashback_table_shortcode' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'My Cashback Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the My Cashback Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_cashback_title_shortcode' ,
                    'std'      => 'My Cashback' ,
                    'default'  => 'My Cashback' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_cashback_title_shortcode' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'S.No Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Serial Number Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_cashback_sno_label_shortcode' ,
                    'std'      => 'S.No' ,
                    'default'  => 'S.No' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_cashback_sno_label_shortcode' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Username Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Username Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_cashback_userid_label_shortcode' ,
                    'std'      => 'Username' ,
                    'default'  => 'Username' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_cashback_userid_label_shortcode' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Requested for Cashback Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Requested for Cashback Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_cashback_requested_label_shortcode' ,
                    'std'      => 'Requested for Cashback' ,
                    'default'  => 'Requested for Cashback' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_cashback_requested_label_shortcode' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Status Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Status On Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_cashback_status_label_shortcode' ,
                    'std'      => 'Status' ,
                    'default'  => 'Status' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_cashback_status_label_shortcode' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Action Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Action On Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_cashback_action_label_shortcode' ,
                    'std'      => 'Action' ,
                    'default'  => 'Action' ,
                    'type'     => 'rs_action_for_cash_back' ,
                    'newids'   => 'rs_my_cashback_action_label_shortcode' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_my_cashback_label_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Message Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_message_settings_encashing'
                ) ,
                array(
                    'name'     => __( 'Message displayed for Guest' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Message displayed for Guest' , SRP_LOCALE ) ,
                    'id'       => 'rs_message_for_guest_encashing' ,
                    'std'      => 'Please [rssitelogin] to Cashback your Reward Points.' ,
                    'default'  => 'Please [rssitelogin] to Cashback your Reward Points.' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_message_for_guest_encashing' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Login Link for Guest Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Login link for Guest Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_encashing_login_link_label' ,
                    'std'      => 'Login' ,
                    'default'  => 'Login' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_encashing_login_link_label' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Message displayed for Banned Users' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Message Displayed for Banned Users' , SRP_LOCALE ) ,
                    'id'       => 'rs_message_for_banned_users_encashing' ,
                    'std'      => 'You cannot Cashback Your points' ,
                    'default'  => 'You cannot Cashback Your points' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_message_for_banned_users_encashing' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Message displayed when Users don\'t have Reward Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Message to be Displayed when Users dont have Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_message_users_nopoints_encashing' ,
                    'std'      => 'You Don\'t have points for Cashback' ,
                    'default'  => 'You Don\'t have points for Cashback' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_message_users_nopoints_encashing' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Message displayed when Cashback Request is Submitted' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Message to be Displayed when Cashback Request is Submitted' , SRP_LOCALE ) ,
                    'id'       => 'rs_message_encashing_request_submitted' ,
                    'std'      => 'Cashback Request Submitted' ,
                    'default'  => 'Cashback Request Submitted' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_message_encashing_request_submitted' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_message_settings_encashing' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'CSV Settings (Export CSV for Paypal Mass Payment)' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_csv_message_settings_encashing'
                ) ,
                array(
                    'name'     => __( 'Custom Note for Paypal' , SRP_LOCALE ) ,
                    'desc'     => __( 'A Custom Note for Paypal' , SRP_LOCALE ) ,
                    'id'       => 'rs_encashing_paypal_custom_notes' ,
                    'std'      => 'Thanks for your Business' ,
                    'default'  => 'Thanks for your Business' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_encashing_paypal_custom_notes' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_csv_message_settings_encashing' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Error Message Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_error_settings_encashing'
                ) ,
                array(
                    'name'     => __( 'Error Message displayed when Points for Cashback Field is left Empty' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Message to be Displayed when Points for Cashback Field is Empty' , SRP_LOCALE ) ,
                    'id'       => 'rs_error_message_points_empty_encash' ,
                    'std'      => 'Points for Cashback Field cannot be empty' ,
                    'default'  => 'Points for Cashback Field cannot be empty' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_error_message_points_empty_encash' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message displayed when Points to Cashback Value is not a Number' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Message to be Displayed when Points To Cashback Field value is not a number' , SRP_LOCALE ) ,
                    'id'       => 'rs_error_message_points_number_val_encash' ,
                    'std'      => 'Please Enter only Numbers' ,
                    'default'  => 'Please Enter only Numbers' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_error_message_points_number_val_encash' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message displayed when Points entered for Cashback is more than the Points Earned' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Message to be Displayed when Points entered for Cashback is more than the Points Earned' , SRP_LOCALE ) ,
                    'id'       => 'rs_error_message_points_greater_than_earnpoints' ,
                    'std'      => 'Points Entered for Cashback is more than the Earned Points' ,
                    'default'  => 'Points Entered for Cashback is more than the Earned Points' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_error_message_points_greater_than_earnpoints' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message displayed when Current User Points is less than the Minimum Points for Cashback' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Message to be Displayed when Points entered for Cashback is more than the Maximum Points for Cashback' , SRP_LOCALE ) ,
                    'id'       => 'rs_error_message_currentpoints_less_than_minimum_points' ,
                    'std'      => 'You need a Minimum of [minimum_encash_points] points in order for Cashback' ,
                    'default'  => 'You need a Minimum of [minimum_encash_points] points in order for Cashback' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_error_message_currentpoints_less_than_minimum_points' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message displayed when Points entered to Cashback is less than the Minimum Points and more than Maximum Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Message to be Displayed when Points entered to Cashback is less than the Minimum Points and more than Maximum Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_error_message_points_lesser_than_minimum_points' ,
                    'std'      => 'Please Enter Between [minimum_encash_points] and [maximum_encash_points] ' ,
                    'default'  => 'Please Enter Between [minimum_encash_points] and [maximum_encash_points]' ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_error_message_points_lesser_than_minimum_points' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message displayed when Reason to Cashback Field is Empty' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Message to be Displayed when Reason To Cashback Field is Empty' , SRP_LOCALE ) ,
                    'id'       => 'rs_error_message_reason_encash_empty' ,
                    'std'      => 'Reason to Encash Field cannot be empty' ,
                    'default'  => 'Reason to Encash Field cannot be empty' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_error_message_reason_encash_empty' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message displayed when PayPal Email Address is Empty' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Message to be Displayed when PayPal Email Address is Empty' , SRP_LOCALE ) ,
                    'id'       => 'rs_error_message_paypal_email_empty' ,
                    'std'      => 'Paypal Email Field cannot be empty' ,
                    'default'  => 'Paypal Email Field cannot be empty' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_error_message_paypal_email_empty' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message displayed when PayPal Email Address Format is wrong' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Message to be Displayed when PayPal Email Address format is wrong' , SRP_LOCALE ) ,
                    'id'       => 'rs_error_message_paypal_email_wrong' ,
                    'std'      => 'Enter a Correct Email Address' ,
                    'default'  => 'Enter a Correct Email Address' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_error_message_paypal_email_wrong' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message displayed when Custom Payment Details field is left Empty' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Message to be Displayed when Custom Payment Details field is Empty' , SRP_LOCALE ) ,
                    'id'       => 'rs_error_custom_payment_field_empty' ,
                    'std'      => 'Custom Payment Details Field cannot be empty' ,
                    'default'  => 'Custom Payment Details Field cannot be empty' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_error_custom_payment_field_empty' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message displayed when reCAPTCHA field is Empty' , SRP_LOCALE ) ,
                    'desc'     => __( 'Please Enter Message to be Displayed when reCAPTCHA field is Empty' , SRP_LOCALE ) ,
                    'id'       => 'rs_error_recaptcha_field_empty' ,
                    'std'      => 'reCAPTCHA is mandatory' ,
                    'default'  => 'reCAPTCHA is mandatory' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_error_recaptcha_field_empty' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_error_settings_encashing' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Shortcode used in Form for Cashback' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_shortcode_for_cashback'
                ) ,
                array(
                    'type' => 'title' ,
                    'desc' => '<b>[minimum_encash_points]</b> - To display minimum points required to get cashback<br><br>'
                    . '<b>[maximum_encash_points]</b> - To display maximum points required to get cashback<br><br>'
                    . '<b>[rssitelogin]</b> - To display login link for guests' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_shortcode_for_cashback' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                    ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields( RSCashbackModule::reward_system_admin_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options( RSCashbackModule::reward_system_admin_fields() ) ;
            if ( isset( $_POST[ 'rs_select_inc_user_search' ] ) ) {
                update_option( 'rs_select_inc_user_search' , $_POST[ 'rs_select_inc_user_search' ] ) ;
            } else {
                update_option( 'rs_select_inc_user_search' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_select_exc_user_search' ] ) ) {
                update_option( 'rs_select_exc_user_search' , $_POST[ 'rs_select_exc_user_search' ] ) ;
            } else {
                update_option( 'rs_select_exc_user_search' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_redeem_point_for_cash_back' ] ) ) {
                update_option( 'rs_redeem_point_for_cash_back' , $_POST[ 'rs_redeem_point_for_cash_back' ] ) ;
            } else {
                update_option( 'rs_redeem_point_for_cash_back' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_redeem_point_value_for_cash_back' ] ) ) {
                update_option( 'rs_redeem_point_value_for_cash_back' , $_POST[ 'rs_redeem_point_value_for_cash_back' ] ) ;
            } else {
                update_option( 'rs_redeem_point_value_for_cash_back' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_cashback_module_checkbox' ] ) ) {
                update_option( 'rs_cashback_activated' , $_POST[ 'rs_cashback_module_checkbox' ] ) ;
            } else {
                update_option( 'rs_cashback_activated' , 'no' ) ;
            }
        }

        /**
         * Select the User(s) function 	     
         */
        public static function rs_select_inc_user_search_label() {
            $field_id    = "rs_select_inc_user_search" ;
            $field_label = "Include User(s)" ;
            $getuser     = get_option( 'rs_select_inc_user_search' ) ;
            echo user_selection_field( $field_id , $field_label , $getuser ) ;
        }

        public static function rs_select_exc_user_search_label() {
            $field_id    = "rs_select_exc_user_search" ;
            $field_label = "Exclude User(s)" ;
            $getuser     = get_option( 'rs_select_exc_user_search' ) ;
            echo user_selection_field( $field_id , $field_label , $getuser ) ;
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function set_default_value() {
            foreach ( RSCashbackModule::reward_system_admin_fields() as $setting )
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
        }

        public static function reset_cashback_module() {
            $settings = RSCashbackModule::reward_system_admin_fields() ;
            RSTabManagement::reset_settings( $settings ) ;
            update_option( 'rs_redeem_point_for_cash_back' , '1' ) ;
            update_option( 'rs_redeem_point_value_for_cash_back' , '1' ) ;
        }

        public static function rs_function_to_add_label_for_wallet( $settings ) {
            $updated_settings = array() ;
            foreach ( $settings as $section ) {
                if ( isset( $section[ 'id' ] ) && '_rs_reward_point_encashing_settings' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    $updated_settings[] = array(
                        'name'     => __( 'Hoicker Wallet Label' , SRP_LOCALE ) ,
                        'desc'     => __( 'Please Enter Wallet Label for Cashback' , SRP_LOCALE ) ,
                        'id'       => 'rs_encashing_wallet_label' ,
                        'std'      => 'Cashback will be added to your Hoicker Wallet' ,
                        'default'  => 'Cashback will be added to your Hoicker Wallet' ,
                        'type'     => 'text' ,
                        'newids'   => 'rs_encashing_wallet_label' ,
                        'desc_tip' => true ,
                            ) ;
                    $updated_settings[] = array(
                        'name'     => __( 'Hoicker Wallet Menu Label' , SRP_LOCALE ) ,
                        'desc'     => __( 'Please Enter Wallet Menu Label for Cashback' , SRP_LOCALE ) ,
                        'id'       => 'rs_encashing_wallet_menu_label' ,
                        'std'      => 'Hoicker Wallet' ,
                        'default'  => 'Hoicker Wallet' ,
                        'type'     => 'text' ,
                        'newids'   => 'rs_encashing_wallet_menu_label' ,
                        'desc_tip' => true ,
                            ) ;
                }
                $updated_settings[] = $section ;
            }
            return $updated_settings ;
        }

        public static function encash_list_overall_applications() {
            global $wpdb ;
            global $current_section ;
            global $current_tab ;

            $testListTable = new FPRewardSystemEncashTabList() ;
            $testListTable->prepare_items() ;
            if ( ! isset( $_REQUEST[ 'encash_application_id' ] ) ) {
                $array_list = array() ;
                $message    = '' ;
                if ( 'encash_application_delete' === $testListTable->current_action() ) {
                    $message = '<div class="updated below-h2" id="message"><p>' . sprintf( __( 'Items deleted: %d' ) , count( $_REQUEST[ 'id' ] ) ) . '</p></div>' ;
                }
                echo $message ;
                $testListTable->display() ;
            }
        }

        public static function encash_applications_list_table( $item ) {
            global $wpdb ;
            $table_name = $wpdb->prefix . 'sumo_reward_encashing_submitted_data' ;
            $message    = '' ;
            $notice     = '' ;
            $default    = array(
                'id'                    => 0 ,
                'userid'                => '' ,
                'pointstoencash'        => '' ,
                'encashercurrentpoints' => '' ,
                'reasonforencash'       => '' ,
                'encashpaymentmethod'   => '' ,
                'paypalemailid'         => '' ,
                'otherpaymentdetails'   => '' ,
                'status'                => '' ,
                    ) ;

            if ( isset( $_REQUEST[ 'nonce' ] ) ) {
                if ( wp_verify_nonce( $_REQUEST[ 'nonce' ] , basename( __FILE__ ) ) ) {
                    $item       = shortcode_atts( $default , $_REQUEST ) ;
                    $item_valid = self::encash_validation( $item ) ;
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

                if ( isset( $_REQUEST[ 'encash_application_id' ] ) ) {
                    $item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d" , $_REQUEST[ 'encash_application_id' ] ) , ARRAY_A ) ;

                    if ( ! $item ) {
                        $item   = $default ;
                        $notice = __( 'Item not found' ) ;
                    }
                }
            }
            ?>
            <?php
            if ( isset( $_REQUEST[ 'encash_application_id' ] ) ) {
                ?>
                <script type="text/javascript">
                    jQuery( document ).ready( function () {
                        var currentvalue = jQuery( '#encashpaymentmethod' ).val() ;
                        if ( currentvalue === '1' ) {
                            jQuery( '.paypalemailid' ).parent().parent().css( 'display' , 'table-row' ) ;
                            jQuery( '.otherpaymentdetails' ).parent().parent().css( 'display' , 'none' ) ;
                        } else {
                            jQuery( '.otherpaymentdetails' ).parent().parent().css( 'display' , 'table-row' ) ;
                            jQuery( '.paypalemailid' ).parent().parent().css( 'display' , 'none' ) ;
                        }
                        jQuery( '#encashpaymentmethod' ).change( function () {
                            var thisvalue = jQuery( this ).val() ;
                            if ( thisvalue === '1' ) {
                                jQuery( '.paypalemailid' ).parent().parent().css( 'display' , 'table-row' ) ;
                                jQuery( '.otherpaymentdetails' ).parent().parent().css( 'display' , 'none' ) ;
                            } else {
                                if ( thisvalue === '2' ) {
                                    jQuery( '.paypalemailid' ).parent().parent().css( 'display' , 'none' ) ;
                                    jQuery( '.otherpaymentdetails' ).parent().parent().css( 'display' , 'table-row' ) ;
                                }
                            }
                        } ) ;
                    } ) ;
                </script>
                <?php
                $timeformat   = get_option( 'time_format' ) ;
                $dateformat   = get_option( 'date_format' ) . ' ' . $timeformat ;
                $expired_date = date_i18n( $dateformat ) ;
                ?>
                <div class="wrap">
                    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
                    <h3><?php _e( 'Edit Cashback Status' , SRP_LOCALE ) ; ?><a class="add-new-h2"
                                                                               href="<?php echo get_admin_url( get_current_blog_id() , 'admin.php?page=rewardsystem_callback&tab=encash_applications' ) ; ?>"><?php _e( 'Back to list' ) ?></a>
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
                        <input type="hidden" value="<?php echo $expired_date ; ?>" name="date"/>
                        <div class="metabox-holder" id="poststuff">
                            <div id="post-body">
                                <div id="post-body-content">
                                    <table class="form-table">
                                        <tbody>                                        
                                            <tr>
                                                <th scope="row"><?php _e( 'Points for Cashback' , SRP_LOCALE ) ; ?></th>
                                                <td>
                                                    <input type="text" name="pointstoencash" id="setvendorname" value="<?php echo $item[ 'pointstoencash' ] ; ?>"/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><?php _e( 'Reason for Cashback' , SRP_LOCALE ) ; ?></th>
                                                <td>
                                                    <textarea name="reasonforencash" rows="3" cols="30"><?php echo $item[ 'reasonforencash' ] ; ?></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><?php _e( 'Application Status' , SRP_LOCALE ) ; ?></th>
                                                <td>
                                                    <?php
                                                    $selected_approved         = $item[ 'status' ] == 'Paid' ? "selected=selected" : '' ;
                                                    $selected_rejected         = $item[ 'status' ] == 'Due' ? "selected=selected" : '' ;
                                                    ?>
                                                    <select name = "status">                                                    
                                                        <option value = "Paid" <?php echo $selected_approved ; ?>><?php _e( 'Paid' , SRP_LOCALE ) ; ?></option>
                                                        <option value = "Due" <?php echo $selected_rejected ; ?>><?php _e( 'Due' , SRP_LOCALE ) ; ?></option>
                                                    </select>
                                                </td>
                                            </tr>                                                                                
                                            <tr>
                                                <th scope="row"><?php _e( 'Cashback Payment Option' , SRP_LOCALE ) ; ?></th>
                                                <td>                                             
                                                    <?php
                                                    $selectedpaymentoption     = $item[ 'encashpaymentmethod' ] == 'encash_through_paypal_method' ? "selected=selected" : "" ;
                                                    $mainselectedpaymentoption = $item[ 'encashpaymentmethod' ] == 'encash_through_custom_payment' ? "selected=selected" : "" ;
                                                    ?>
                                                    <select id="encashpaymentmethod" name="encashpaymentmethod">
                                                        <option value="1" <?php echo $selectedpaymentoption ; ?>><?php _e( 'Paypal Address' , SRP_LOCALE ) ; ?></option>
                                                        <option value="2" <?php echo $mainselectedpaymentoption ; ?>><?php _e( 'Custom Payment' , SRP_LOCALE ) ; ?></option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><?php _e( 'User Paypal Email' , SRP_LOCALE ) ; ?></th>
                                                <td>
                                                    <input type="text" name="paypalemailid" class="paypalemailid" value="<?php echo $item[ 'paypalemailid' ] ; ?>"/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><?php _e( 'User Custom Payment Details' , SRP_LOCALE ) ; ?></th>
                                                <td>
                                                    <textarea name='otherpaymentdetails' rows='3' cols='30' id='otherpaymentdetails' class='otherpaymentdetails'><?php echo $item[ 'otherpaymentdetails' ] ; ?></textarea>
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
            <?php } ?>

            <?php
        }

        public static function encash_validation( $item ) {
            $messages = array() ;
            if ( empty( $messages ) )
                return true ;
            return implode( '<br />' , $messages ) ;
        }

        public static function reward_system_redeeming_points_conversion_for_cash_back() {
            ?>
            <tr valign="top">
                <td class="forminp forminp-text">
                    <input type="number" step="any" min="0" value="<?php echo get_option( 'rs_redeem_point_for_cash_back' ) ; ?>" style="max-width:50px;" id="rs_redeem_point_for_cash_back" name="rs_redeem_point_for_cash_back"> <?php _e( 'Redeeming Point(s)' , SRP_LOCALE ) ; ?>
                    &nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
                    <?php echo get_woocommerce_currency_symbol() ; ?> 	<input type="number" step="any" min="0" value="<?php echo get_option( 'rs_redeem_point_value_for_cash_back' ) ; ?>" style="max-width:50px;" id="rs_redeem_point_value_for_cash_back" name="rs_redeem_point_value_for_cash_back"></td>
            </td>
            </tr>
            <?php
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_cashback_activated' ) , 'rs_cashback_module_checkbox' , 'rs_cashback_activated' ) ;
        }

    }

    RSCashbackModule::init() ;
}