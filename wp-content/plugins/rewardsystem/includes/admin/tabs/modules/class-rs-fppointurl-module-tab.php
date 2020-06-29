<?php
/*
 * Point URL Setting Tsb
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSPointURL' ) ) {

    class RSPointURL {

        public static function init() {

            add_action( 'woocommerce_rs_settings_tabs_fppointurl' , array( __CLASS__ , 'register_admin_options' ) ) ;

            add_action( 'rs_default_settings_fppointurl' , array( __CLASS__ , 'set_default_value' ) ) ;

            add_action( 'woocommerce_update_options_fprsmodules_fppointurl' , array( __CLASS__ , 'update_settings' ) ) ;

            add_action( 'woocommerce_admin_field_rs_enable_disable_point_url_module' , array( __CLASS__ , 'enable_module' ) ) ;

            add_action( 'woocommerce_admin_field_generate_point_url' , array( __CLASS__ , 'settings_to_generate_point_url' ) ) ;

            add_action( 'fp_action_to_reset_module_settings_fppointurl' , array( __CLASS__ , 'reset_points_url_module' ) ) ;

            add_action( 'rs_display_save_button_fppointurl' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

            add_action( 'rs_display_reset_button_fppointurl' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
        }

        public static function settings_option() {
            return apply_filters( 'woocommerce_fppointurl_settings' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Point URL Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_point_url_module'
                ) ,
                array(
                    'type' => 'rs_enable_disable_point_url_module' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_point_url_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Point URL Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_pointurl_setting'
                ) ,
                array(
                    'name'              => __( 'Name' , SRP_LOCALE ) ,
                    'desc_tip'          => false ,
                    'id'                => 'rs_label_for_site_url' ,
                    'newids'            => 'rs_label_for_site_url' ,
                    'type'              => 'text' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'custom_attributes' => array(
                        'required' => 'required'
                    )
                ) ,
                array(
                    'name'     => __( 'Site URL' , SRP_LOCALE ) ,
                    'desc'     => __( '(If it is empty,then we consider Site URL as Base URL)' , SRP_LOCALE ) ,
                    'desc_tip' => false ,
                    'id'       => 'rs_site_url' ,
                    'newids'   => 'rs_site_url' ,
                    'type'     => 'text' ,
                    'std'      => site_url() ,
                    'default'  => site_url() ,
                ) ,
                array(
                    'name'              => __( 'Points' , SRP_LOCALE ) ,
                    'id'                => 'rs_point_for_url' ,
                    'newids'            => 'rs_point_for_url' ,
                    'type'              => 'text' ,
                    'std'               => '' ,
                    'default'           => '' ,
                    'custom_attributes' => array(
                        'required' => 'required'
                    )
                ) ,
                array(
                    'name'    => __( 'Validity' , SRP_LOCALE ) ,
                    'id'      => 'rs_time_limit_for_pointurl' ,
                    'newids'  => 'rs_time_limit_for_pointurl' ,
                    'type'    => 'select' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'options' => array(
                        '1' => __( 'Unlimited' , SRP_LOCALE ) ,
                        '2' => __( 'Limited' , SRP_LOCALE ) ,
                    )
                ) ,
                array(
                    'name'    => __( 'Expiry Time' , SRP_LOCALE ) ,
                    'id'      => 'rs_expiry_time_for_pointurl' ,
                    'newids'  => 'rs_expiry_time_for_pointurl' ,
                    'type'    => 'text' ,
                    'std'     => '' ,
                    'default' => '' ,
                ) ,
                array(
                    'name'    => __( 'Usage Count' , SRP_LOCALE ) ,
                    'id'      => 'rs_count_limit_for_pointurl' ,
                    'newids'  => 'rs_count_limit_for_pointurl' ,
                    'type'    => 'select' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'options' => array(
                        '1' => __( 'Unlimited' , SRP_LOCALE ) ,
                        '2' => __( 'Limited' , SRP_LOCALE ) ,
                    )
                ) ,
                array(
                    'name'    => __( 'Count' , SRP_LOCALE ) ,
                    'id'      => 'rs_count_for_pointurl' ,
                    'newids'  => 'rs_count_for_pointurl' ,
                    'type'    => 'text' ,
                    'std'     => '' ,
                    'default' => '' ,
                ) ,
                array(
                    'type' => 'generate_point_url' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_pointurl_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Email Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_point_url_email_settings'
                ) ,
                array(
                    'name'    => __( 'Enable To Send Mail For Point URL Reward Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enabling this option will send Point URL Points through Mail' , SRP_LOCALE ) ,
                    'id'      => 'rs_send_mail_point_url' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_send_mail_point_url' ,
                ) ,
                array(
                    'name'    => __( 'Email Subject For Point URL Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_subject_point_url' ,
                    'std'     => 'Point URL - Notification' ,
                    'default' => 'Point URL - Notification' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_subject_point_url' ,
                ) ,
                array(
                    'name'    => __( 'Email Message For Point URL Points' , SRP_LOCALE ) ,
                    'id'      => 'rs_email_message_point_url' ,
                    'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_email_message_point_url' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_point_url_email_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Success Message Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_pointurl_message_setting'
                ) ,
                array(
                    'name'    => __( 'Success Message to display when Points associated URL is accessed' , SRP_LOCALE ) ,
                    'id'      => 'rs_success_message_for_pointurl' ,
                    'newids'  => 'rs_success_message_for_pointurl' ,
                    'type'    => 'text' ,
                    'std'     => '[points] Points added for [offer_name]' ,
                    'default' => '[points] Points added for [offer_name]' ,
                ) ,
                array(
                    'name'    => __( 'Log to be displayed in My Account and Master Log' , SRP_LOCALE ) ,
                    'id'      => 'rs_message_for_pointurl' ,
                    'newids'  => 'rs_message_for_pointurl' ,
                    'type'    => 'text' ,
                    'std'     => '[points] Points added, from Visited Point URL' ,
                    'default' => '[points] Points added, from Visited Point URL' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_pointurl_message_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Error Message Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_sk_message_setting1'
                ) ,
                array(
                    'name'    => __( 'Error Message displayed when the Points associated URL was already accessed' , SRP_LOCALE ) ,
                    'id'      => 'failure_msg_for_accessed_url' ,
                    'newids'  => 'failure_msg_for_accessed_url' ,
                    'type'    => 'textarea' ,
                    'std'     => 'You cannot get Points for this link because you have already claimed' ,
                    'default' => 'You cannot get coupon for this link because you have already claimed' ,
                ) ,
                array(
                    'name'    => __( 'Error Message displayed when Points associated URL is accessed after Expiry' , SRP_LOCALE ) ,
                    'id'      => 'failure_msg_for_expired_url' ,
                    'newids'  => 'failure_msg_for_expired_url' ,
                    'type'    => 'text' ,
                    'std'     => '[offer_name] has been Expired' ,
                    'default' => '[offer_name] has been Expired' ,
                ) ,
                array(
                    'name'    => __( 'Error Message displayed when Usage Count has been exceeded' , SRP_LOCALE ) ,
                    'id'      => 'failure_msg_for_count_exceed' ,
                    'newids'  => 'failure_msg_for_count_exceed' ,
                    'type'    => 'text' ,
                    'std'     => 'Usage of Link Limitation reached' ,
                    'default' => 'Usage of Link Limitation reached' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_sk_message_setting1' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Shortcode used in Point URL' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_shortcode_for_points_url'
                ) ,
                array(
                    'type' => 'title' ,
                    'desc' => '<b>[points]</b> - To display points earned for using url<br><br>'
                    . '<b>[offer_name]</b> - To display url has been expired'
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_shortcode_for_points_url' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                    ) ) ;
        }

        public static function register_admin_options() {
            woocommerce_admin_fields( RSPointURL::settings_option() ) ;
        }

        public static function update_settings() {
            woocommerce_update_options( RSPointURL::settings_option() ) ;
            if ( isset( $_POST[ 'rs_point_url_module_checkbox' ] ) ) {
                update_option( 'rs_point_url_activated' , $_POST[ 'rs_point_url_module_checkbox' ] ) ;
            } else {
                update_option( 'rs_point_url_activated' , 'no' ) ;
            }
        }

        public static function set_default_value() {
            foreach ( RSPointURL::settings_option() as $setting )
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_point_url_activated' ) , 'rs_point_url_module_checkbox' , 'rs_point_url_activated' ) ;
        }

        public static function settings_to_generate_point_url() {
            ?>
            <tr>
                <td></td>
                <td>
                    <input type="submit" id="rs_button_for_point_url" class="rs_export_button" value="<?php _e( 'Generate Point URL' , SRP_LOCALE ) ; ?>"/>
                </td>
            </tr>
            <table>        
                <tr valign="top">
                    <td>
                        <p>
                            <label><?php _e( 'Search:' , SRP_LOCALE ) ; ?></label>
                            <input id="filterings_pointurl" type="text"/>
                            <label><?php _e( 'Page Size:' , SRP_LOCALE ) ; ?></label>
                            <select id="changepagesizers_for_url">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </p>
                    </td>
                </tr>
            </table>    
            <table id="rs_table_for_point_url" class="wp-list-table widefat fixed posts  rs_table_for_point_url" data-filter = "#filterings_pointurl" data-page-size="5" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next">
                <thead>
                    <tr>
                        <th><?php _e( 'S.No' , SRP_LOCALE ) ; ?></th>
                        <th><?php _e( 'Name for Point URL' , SRP_LOCALE ) ; ?></th>
                        <th><?php _e( 'URL' , SRP_LOCALE ) ; ?></th>                    
                        <th><?php _e( 'Point(s)' , SRP_LOCALE ) ; ?></th>
                        <th><?php _e( 'Date' , SRP_LOCALE ) ; ?></th>
                        <th><?php _e( 'Time Limit' , SRP_LOCALE ) ; ?></th>
                        <th><?php _e( 'Count Limit' , SRP_LOCALE ) ; ?></th>
                        <th><?php _e( 'Current Usage Count' , SRP_LOCALE ) ; ?></th>                    
                        <th><?php _e( 'Delete' , SRP_LOCALE ) ; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i            = 1 ;
                    $PointURLData = get_option( 'points_for_url_click' ) ;
                    if ( srp_check_is_array( $PointURLData ) ) {
                        foreach ( $PointURLData as $key => $value ) {
                            $add_query = add_query_arg( 'rsid' , $key , $value[ 'url' ] ) ;
                            ?>
                            <tr>
                                <td><?php echo $i ; ?></td>
                                <td><?php echo $value[ 'name' ] ; ?></td>
                                <td><?php echo $add_query ; ?></td>
                                <td><?php echo $value[ 'points' ] ; ?></td>
                                <td><?php echo $value[ 'date' ] ; ?></td>
                                <td><?php echo $value[ 'time_limit' ] == '1' ? __( 'Unlimited' , SRP_LOCALE ) : __( 'Limited' , SRP_LOCALE ) ; ?></td>                            
                                <td><?php echo $value[ 'count_limit' ] == '1' ? __( 'Unlimited' , SRP_LOCALE ) : __( 'Limited' , SRP_LOCALE ) ; ?></td>
                                <td><?php echo $value[ 'current_usage_count' ] ; ?></td>
                                <td><div data-uniqid="<?php echo $key ; ?>" class="rs_remove_point_url">x</div></td>
                            </tr>    
                            <?php
                            $i ++ ;
                        }
                    }
                    ?>
                </tbody>
            </table>
            <div style="clear:both;" class="rs_pagination">
                <div class="pagination pagination-centered"></div>
            </div>
            <?php
        }

        public static function reset_points_url_module() {
            $settings = RSPointURL::settings_option() ;
            RSTabManagement::reset_settings( $settings ) ;
        }

    }

    RSPointURL::init() ;
}