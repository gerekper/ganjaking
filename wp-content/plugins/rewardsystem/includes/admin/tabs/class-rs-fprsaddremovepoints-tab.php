<?php
/*
 * Add/Remove Points
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSAddorRemovePoints' ) ) {

    class RSAddorRemovePoints {

        public static function init() {

            add_action( 'rs_default_settings_fprsaddremovepoints' , array( __CLASS__ , 'set_default_value' ) ) ;

            add_action( 'woocommerce_rs_settings_tabs_fprsaddremovepoints' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsaddremovepoints' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system

            add_action( 'woocommerce_admin_field_rs_add_remove_remove_reward_points' , array( __CLASS__ , 'rs_buttons_to_add_or_remove_points' ) ) ;

            add_action( 'woocommerce_admin_field_rs_inc_user_to_add_remove_points' , array( __CLASS__ , 'rs_inc_user_to_add_remove_points' ) ) ;

            add_action( 'woocommerce_admin_field_rs_exc_user_to_add_remove_points' , array( __CLASS__ , 'rs_exc_user_to_add_remove_points' ) ) ;

            add_action( 'woocommerce_admin_field_rs_datepicker_for_expiry' , array( __CLASS__ , 'rs_datepicker_for_expiry' ) ) ;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            return apply_filters( 'woocommerce_fprsaddremovepoints_settings' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Add/Remove Reward Points Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_add_remove_points_setting' ,
                ) ,
                array(
                    'name'    => __( 'User Selection Type' , SRP_LOCALE ) ,
                    'type'    => 'select' ,
                    'id'      => 'rs_select_user_type' ,
                    'newids'  => 'rs_select_user_type' ,
                    'class'   => 'rs_select_user_type' ,
                    'options' => array(
                        '1' => __( 'All Users' , SRP_LOCALE ) ,
                        '2' => __( 'Include User(s)' , SRP_LOCALE ) ,
                        '3' => __( 'Exclude User(s)' , SRP_LOCALE ) ,
                        '4' => __( 'Include User Role(s)' , SRP_LOCALE ) ,
                        '5' => __( 'Exclude User Role(s)' , SRP_LOCALE ) ,
                    ) ,
                    'std'     => '1' ,
                    'default' => '1' ,
                ) ,
                array(
                    'type' => 'rs_inc_user_to_add_remove_points' ,
                ) ,
                array(
                    'type' => 'rs_exc_user_to_add_remove_points' ,
                ) ,
                array(
                    'name'        => __( 'Select User role(s) to Include' , SRP_LOCALE ) ,
                    'id'          => 'rs_select_to_include_customers_role' ,
                    'css'         => 'min-width:343px;' ,
                    'std'         => '' ,
                    'default'     => '' ,
                    'placeholder' => 'Search for a User Role' ,
                    'type'        => 'multiselect' ,
                    'options'     => fp_user_roles() ,
                    'newids'      => 'rs_select_to_include_customers_role' ,
                    'desc_tip'    => false ,
                ) ,
                array(
                    'name'     => __( 'Select User role(s) to Exclude' , SRP_LOCALE ) ,
                    'id'       => 'rs_select_to_exclude_customers_role' ,
                    'css'      => 'min-width:343px;' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'multiselect' ,
                    'options'  => fp_user_roles() ,
                    'newids'   => 'rs_select_to_exclude_customers_role' ,
                    'desc_tip' => false ,
                ) ,
                array(
                    'name'              => __( 'Points to Update' , SRP_LOCALE ) ,
                    'type'              => 'number' ,
                    'id'                => 'rs_reward_addremove_points' ,
                    'newids'            => 'rs_reward_addremove_points' ,
                    'class'             => 'rs_reward_addremove_points' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'custom_attributes' => array(
                        'min' => 0 ,
                    )
                ) ,
                array(
                    'name'    => __( 'Reason in Detail' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'id'      => 'rs_reward_addremove_reason' ,
                    'newids'  => 'rs_reward_addremove_reason' ,
                    'class'   => 'rs_reward_addremove_reason' ,
                    'std'     => '' ,
                    'default' => '' ,
                ) ,
                array(
                    'name'    => __( 'Selection Type' , SRP_LOCALE ) ,
                    'type'    => 'select' ,
                    'id'      => 'rs_reward_select_type' ,
                    'newids'  => 'rs_reward_select_type' ,
                    'class'   => 'rs_reward_select_type' ,
                    'options' => array(
                        '1' => __( 'Add Points' , SRP_LOCALE ) ,
                        '2' => __( 'Remove Points' , SRP_LOCALE ) ,
                    ) ,
                    'std'     => '1' ,
                    'default' => '1' ,
                ) ,
                array(
                    'type' => 'rs_datepicker_for_expiry' ,
                ) ,
                array(
                    'name'    => __( 'Enable to send Email Notification for manually adding points' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'id'      => 'send_mail_add_remove_settings' ,
                    'newids'  => 'send_mail_add_remove_settings' ,
                    'class'   => 'send_mail_add_remove_settings' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'id'      => 'rs_email_subject_message' ,
                    'newids'  => 'rs_email_subject_message' ,
                    'class'   => 'rs_email_subject_message' ,
                    'std'     => 'Reward Points Updated – Notification' ,
                    'default' => 'Reward Points Updated – Notification' ,
                ) ,
                array(
                    'name'    => __( 'Message' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'id'      => 'rs_email_message' ,
                    'newids'  => 'rs_email_message' ,
                    'class'   => 'rs_email_message' ,
                    'std'     => '[rs_earned_points] Points have been added to your account.<br><br> Expiry Date : [rs_expiry]<br><br>Available points on the [site_name] are [balance_points]<br><br>' ,
                    'default' => '[rs_earned_points] Points have been added to your account.<br><br> Expiry Date : [rs_expiry]<br><br>Available points on the [site_name] are [balance_points]<br><br>' ,
                ) ,
                array(
                    'name'    => __( 'Enable to send Email Notification for manually removing points' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'id'      => 'send_mail_settings' ,
                    'newids'  => 'send_mail_settings' ,
                    'class'   => 'send_mail_settings' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'id'      => 'rs_email_subject_for_remove' ,
                    'newids'  => 'rs_email_subject_for_remove' ,
                    'class'   => 'rs_email_subject_for_remove' ,
                    'std'     => 'Reward Points Updated – Notification' ,
                    'default' => 'Reward Points Updated – Notification' ,
                ) ,
                array(
                    'name'    => __( 'Message' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'id'      => 'rs_email_message_for_remove' ,
                    'newids'  => 'rs_email_message_for_remove' ,
                    'class'   => 'rs_email_message_for_remove' ,
                    'std'     => '[rs_deleted_points] Points have been removed from your account. The Available points on the [site_name] are [balance_points]' ,
                    'default' => '[rs_deleted_points] Points have been removed from your account. The Available points on the [site_name] are [balance_points]' ,
                ) ,
                array(
                    'type' => 'rs_add_remove_remove_reward_points' ,
                ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                    ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {
            woocommerce_admin_fields( RSAddorRemovePoints::reward_system_admin_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options( RSAddorRemovePoints::reward_system_admin_fields() ) ;
        }

        public static function set_default_value() {
            foreach ( RSAddorRemovePoints::reward_system_admin_fields() as $setting ) {
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
            }
        }

        public static function rs_inc_user_to_add_remove_points() {
            echo rs_common_ajax_function_to_select_user( 'rs_select_to_include_customers' ) ;
            $incfield_id    = "rs_select_to_include_customers" ;
            $incfield_label = esc_html__( 'Select User(s) to Include' , SRP_LOCALE ) ;
            $getincuser     = get_option( 'rs_select_to_include_customers' ) ;
            echo user_selection_field( $incfield_id , $incfield_label , $getincuser ) ;
        }

        public static function rs_exc_user_to_add_remove_points() {
            echo rs_common_ajax_function_to_select_user( 'rs_select_to_exclude_customers' ) ;
            $excfield_id    = "rs_select_to_exclude_customers" ;
            $excfield_label = esc_html__( 'Select User(s) to Exclude' , SRP_LOCALE ) ;
            $getexcuser     = get_option( 'rs_select_to_exclude_customers' ) ;
            echo user_selection_field( $excfield_id , $excfield_label , $getexcuser ) ;
        }

        public static function rs_datepicker_for_expiry() {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rs_expired_date"><?php _e( 'Expires On' , SRP_LOCALE ) ; ?></label>
                </th>
                <td class="forminp forminp-select">
                    <input type="text" class="rs_expired_date" value="" name="rs_expired_date" id="rs_expired_date" />                                
                </td>
            </tr>
            <?php
        }

        public static function rs_buttons_to_add_or_remove_points() {
            ?>
            <tr valign='top'>
                <td>
                    <input type='button' name='rs_remove_points' id='rs_remove_points'  class='button-primary' value='Remove Points'/>                            
                    <img class="gif_rs_sumo_reward_button_for_remove" src="<?php echo SRP_PLUGIN_DIR_URL ; ?>/assets/images/update.gif" style="width:32px;height:32px;position:absolute"/>
                </td>
                <td>
                    <input type='button' name='rs_add_points' id='rs_add_points' class='button-primary rs_button' value='Add Points'/>
                    <img class="gif_rs_sumo_reward_button_for_add" src="<?php echo SRP_PLUGIN_DIR_URL ; ?>/assets/images/update.gif" style="width:32px;height:32px;position:absolute"/><br>
                </td>
            </tr>
            </table>
            <?php
        }

    }

    RSAddorRemovePoints::init() ;
}