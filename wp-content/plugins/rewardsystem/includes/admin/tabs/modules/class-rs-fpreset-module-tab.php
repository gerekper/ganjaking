<?php
/*
 * Reset Tab Setting
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSReset' ) ) {

    class RSReset {

        public static function init() {
            add_action( 'woocommerce_rs_settings_tabs_fpreset' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsmodules_fpreset' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action( 'woocommerce_admin_field_reset_data_button' , array( __CLASS__ , 'button_to_reset_data' ) ) ;

            add_action( 'woocommerce_admin_field_reset_tab_settings' , array( __CLASS__ , 'button_to_reset_settings' ) ) ;

            add_action( 'woocommerce_admin_field_rs_enable_disable_reset_module' , array( __CLASS__ , 'enable_module' ) ) ;

            add_action( 'woocommerce_admin_field_rs_select_user_to_reset_data' , array( __CLASS__ , 'rs_select_user_to_reset_data' ) ) ;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            return apply_filters( 'woocommerce_fpreset_settings' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Reset Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_reset_module'
                ) ,
                array(
                    'type' => 'rs_enable_disable_reset_module' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_reset_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Reset Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_reset_setting'
                ) ,
                array(
                    'name'    => __( 'Reset Data for' , SRP_LOCALE ) ,
                    'type'    => 'radio' ,
                    'id'      => 'rs_reset_data_all_users' ,
                    'newids'  => 'rs_reset_data_all_users' ,
                    'class'   => 'rs_reset_data_for_users' ,
                    'options' => array(
                        '1' => __( 'All Users' , SRP_LOCALE ) ,
                        '2' => __( 'Selected Users' , SRP_LOCALE ) ,
                    ) ,
                    'std'     => '1' ,
                    'default' => '1' ,
                ) ,
                array(
                    'type' => 'rs_select_user_to_reset_data' ,
                ) ,
                array(
                    'name'    => __( 'Reset User Reward Points' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'id'      => 'rs_reset_user_reward_points' ,
                    'newids'  => 'rs_reset_user_reward_points' ,
                    'class'   => 'rs_reset_user_reward_points' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                ) ,
                array(
                    'name'    => __( 'Reset User Logs' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'id'      => 'rs_reset_user_log' ,
                    'newids'  => 'rs_reset_user_log' ,
                    'class'   => 'rs_reset_user_log' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                ) ,
                array(
                    'name'    => __( 'Reset Master Logs' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'id'      => 'rs_reset_master_log' ,
                    'newids'  => 'rs_reset_master_log' ,
                    'class'   => 'rs_reset_master_log' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                ) ,
                array(
                    'name'    => __( 'Reset Meta for Previous Order(s)' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'id'      => 'rs_reset_previous_order' ,
                    'newids'  => 'rs_reset_previous_order' ,
                    'class'   => 'rs_reset_previous_order' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                ) ,
                array(
                    'name'    => __( 'Reset Referral Reward Table' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'id'      => 'rs_reset_referral_log_table' ,
                    'newids'  => 'rs_reset_referral_log_table' ,
                    'class'   => 'rs_reset_referral_log_table' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                ) ,
                array(
                    'name'    => __( 'Reset Manual Referral Link Settings' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'id'      => 'rs_reset_manual_referral_link' ,
                    'newids'  => 'rs_reset_manual_referral_link' ,
                    'class'   => 'rs_reset_manual_referral_link' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                ) ,
                array(
                    'name'    => __( 'Reset recorded entries in \'rsrecordstable\' at DB' , SRP_LOCALE ) ,
                    'type'    => 'checkbox' ,
                    'id'      => 'rs_reset_record_log_table' ,
                    'newids'  => 'rs_reset_record_log_table' ,
                    'class'   => 'rs_reset_record_log_table' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                ) ,
                array(
                    'type' => 'reset_data_button' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_reset_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Setting to Reset Plugin Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_reset_tab_setting'
                ) ,
                array(
                    'type' => 'reset_tab_settings' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_reset_tab_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                    ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields( RSReset::reward_system_admin_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options( RSReset::reward_system_admin_fields() ) ;
            if ( isset( $_POST[ 'rs_reset_module_checkbox' ] ) ) {
                update_option( 'rs_reset_activated' , $_POST[ 'rs_reset_module_checkbox' ] ) ;
            } else {
                update_option( 'rs_reset_activated' , 'no' ) ;
            }
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_reset_activated' ) , 'rs_reset_module_checkbox' , 'rs_reset_activated' ) ;
        }

        public static function rs_select_user_to_reset_data() {
            $field_id    = "rs_reset_selected_user_data" ;
            $field_label = "Select Users to Reset Data" ;
            $getuser     = get_option( 'rs_reset_selected_user_data' ) ;
            echo user_selection_field( $field_id , $field_label , $getuser ) ;
        }

        public static function button_to_reset_data() {
            ?>
            <tr valign="top">
                <td></td>
                <td>
                    <input type="button" class="button-primary" name="rs_reset_data_submit" id="rs_reset_data_submit" value="Reset Data" />
                    <img class="gif_rs_sumo_reward_button_for_reset" src="<?php echo SRP_PLUGIN_URL ; ?>/assets/images/update.gif" style="width:32px;height:32px;position:absolute"/><br>
                    <div class="rs_reset_success_data"></div>
                </td>
            </tr>
            </table>
            <?php
        }

        public static function button_to_reset_settings() {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">                    
                    <label for="rs_reset_tab_label"><?php _e( 'Click the Button to Reset the Entire Plugin settings (Excluding Plugin Data)' , SRP_LOCALE ) ; ?></label>
                </th>
                <td>
                    <input type="submit" class="button-primary" name="rs_reset_tab" id="rs_reset_tab" value="Reset" />
                    <img class="gif_rs_reset_tab_settings" src="<?php echo SRP_PLUGIN_URL ; ?>/assets/images/update.gif" style="width:32px;height:32px;position:absolute"/><br>
                    <div class="rs_reset_tab_setting_success">
                    </div>
                </td>
            </tr>
            <?php
        }

    }

    RSReset::init() ;
}