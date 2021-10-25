<?php
/*
 * Email Template Tab
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSEmailExpiredPointsTemplate' ) ) {

    class RSEmailExpiredPointsTemplate {

        public static function init() {

            add_action( 'woocommerce_rs_settings_tabs_fpemailexpiredpoints' , array( __CLASS__ , 'register_settings' ) ) ;

            add_action( 'woocommerce_update_options_fprsmodules_fpemailexpiredpoints' , array( __CLASS__ , 'update_settings' ) ) ;

            add_action( 'woocommerce_admin_field_emailexpiry_templates_table' , array( __CLASS__ , 'emailexpiry_templates_table' ) ) ;

            add_action( 'woocommerce_admin_field_rs_enable_emailexpiry_module' , array( __CLASS__ , 'enable_module' ) ) ;

            add_action( 'rs_display_save_button_fpemailexpiredpoints' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

            add_action( 'rs_display_reset_button_fpemailexpiredpoints' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;

            add_action( 'fp_action_to_reset_settings_fpemailexpiredpoints' , array( __CLASS__ , 'reset_emailexpiry_module' ) ) ;
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_email_template_expire_activated' ) , 'rs_email_template_expire_checkbox' , 'rs_email_template_expire_activated' ) ;
        }

        public static function settings_fields() {
            global $wpdb ;
            $TableName     = $wpdb->prefix . 'rs_expiredpoints_email' ;
            $Templates     = $wpdb->get_results( "SELECT template_name FROM $TableName WHERE rs_status='ACTIVE'" , ARRAY_A ) ;
            $TemplateNames = array() ;
            if ( srp_check_is_array( $Templates ) ) {
                foreach ( $Templates as $Template ) {
                    $TemplateNames[ $Template[ 'template_name' ] ] = $Template[ 'template_name' ] ;
                }
            }
            return apply_filters( 'woocommerce_fpemailexpiredpoints_settings' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Email Template for Expire' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_email_template_expire_module' ,
                ) ,
                array(
                    'type' => 'rs_enable_emailexpiry_module' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_email_template_expire_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Email Templates Settings for Expire' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_email_expired_point_template_setting'
                ) ,
                array(
                    'name'    => __( 'Select Template ' , SRP_LOCALE ) ,
                    'id'      => 'rs_select_template' ,
                    'class'   => 'rs_select_template' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_select_template' ,
                    'options' => $TemplateNames ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_general_tab_' ) ,
                array(
                    'type' => 'emailexpiry_templates_table' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_email_expired_point_template_setting' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                    ) ) ;
        }

        public static function register_settings() {
            woocommerce_admin_fields( RSEmailExpiredPointsTemplate::settings_fields() ) ;
        }

        public static function update_settings() {
            woocommerce_update_options( RSEmailExpiredPointsTemplate::settings_fields() ) ;
            if ( isset( $_POST[ 'rs_email_template_expire_checkbox' ] ) ) {
                update_option( 'rs_email_template_expire_activated' , $_POST[ 'rs_email_template_expire_checkbox' ] ) ;
            } else {
                update_option( 'rs_email_template_expire_activated' , 'no' ) ;
            }
            wp_safe_redirect( esc_url_raw( add_query_arg( 'rs_saved' , '1' , $_SERVER[ 'REQUEST_URI' ] ) ) ) ;
            exit() ;
        }

        public static function emailexpiry_templates_table() {
            ?>
            <style type="text/css">                
                .chosen-container .chosen-results {
                    clear: both;
                }
                .chosen-container {
                    position:absolute !important;
                }
                .rs_local_senders_expired{
                    display:none;
                }
            </style>
            <?php
            global $wpdb ;
            $TableName       = $wpdb->prefix . 'rs_expiredpoints_email' ;
            $ActiveTemplates = $wpdb->get_results( "SELECT template_name FROM $TableName WHERE rs_status='ACTIVE'" , ARRAY_A ) ;
            if ( empty( $ActiveTemplates ) ) {
                ?>
                <script type='text/javascript'>
                    jQuery( document ).ready( function () {
                        jQuery( '#rs_select_template' ).parent().parent().hide() ;
                    } ) ;
                </script>
                <?php
            }
            if ( isset( $_GET[ 'rs_new_email_expired' ] ) && (isset( $_GET[ 'rs_saved' ] )) ) {
                $TemplateData = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $TableName WHERE id = %d" , get_option( 'rs_new_template_id_for_expiry' ) ) , OBJECT ) ;
                $Template     = $TemplateData[ 0 ] ;
                echo self::table_for_template( $Template , true ) ;
            } else if ( isset( $_GET[ 'rs_new_email_expired' ] ) && ( ! isset( $_GET[ 'rs_saved' ] )) ) {
                echo self::table_for_template( array() , false ) ;
            } else if ( isset( $_GET[ 'rs_edit_email_expired' ] ) ) {
                $TemplateData = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $TableName WHERE id = %d" , $_GET[ 'rs_edit_email_expired' ] ) , OBJECT ) ;
                $Template     = $TemplateData[ 0 ] ;
                echo self::table_for_template( $Template , true , 'edit' ) ;
            } else {
                $SavedTemplates = $wpdb->get_results( "SELECT * FROM $TableName" , OBJECT ) ;
                $NewTemplateURL = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpemailexpiredpoints' , 'rs_new_email_expired' => 'template' ) , SRP_ADMIN_URL ) ;
                ?>
                <a href='<?php echo $NewTemplateURL ?>'>
                    <input type="button" name="rs_new_email_expired_template" id="rs_new_email_expired_template" class="button rs_email_button" value="New Template">
                </a>
                <p>
                    <?php _e( 'Search:' , SRP_LOCALE ) ; ?><input id="rs_email_expired_templates" type="text"/>
                    <?php _e( 'Page Size:' , SRP_LOCALE ) ; ?>
                    <select id="changepagesizertemplates">
                        <option value="1">1</option>
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </p>
                <table class="wp-list-table widefat fixed posts" data-filter = "#rs_email_templates_expired" data-page-size="5" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next" id="rs_email_templates_table_expired" cellspacing="0">
                    <thead>
                        <tr>
                            <th scope='col' data-toggle="true" class='manage-column column-serial_number'><?php _e( 'S.No' , SRP_LOCALE ) ; ?></th>
                            <th scope='col' id='rs_user_names' class='manage-column column-rs_user_name'><?php _e( 'Template Name' , SRP_LOCALE ) ; ?></th>
                            <th scope='col' id='rs_expired_from_name' class='manage-column column-rs_expired_from_name'><?php _e( 'From Name' , SRP_LOCALE ) ; ?></th>
                            <th scope='col' id='rs_expired_from_email' class='manage-column column-rs_expired_from_email'><?php _e( 'From Email' , SRP_LOCALE ) ; ?></th>
                            <th scope="col" id='rs_subject_expired' class='manage-column column-rs_subject_expired'><?php _e( 'Email Subject' , SRP_LOCALE ) ; ?></th>
                            <th scope='col' id='rs_message_expired' class='manage-column column-rs_message_expired'><?php _e( 'Email Message' , SRP_LOCALE ) ; ?></th>
                            <th scope='col' id='rs_no_of_days' class='manage-column column-rs_no_of_days'><?php _e( 'No of days' , SRP_LOCALE ) ; ?></th>
                            <th scope="col" id="rs_email_status" class="manage-column column-rs_email_status"><?php _e( 'Status' , SRP_LOCALE ) ; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ( srp_check_is_array( $SavedTemplates ) ) {
                            $i = 1 ;
                            foreach ( $SavedTemplates as $each_template ) {
                                $EditTemplateURL = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpemailexpiredpoints' , 'rs_edit_email_expired' => $each_template->id ) , SRP_ADMIN_URL ) ;
                                $FromName        = $each_template->sender_opt == 'local' ? $each_template->from_name : get_option( 'woocommerce_email_from_name' ) ;
                                $FromEmail       = $each_template->sender_opt == 'local' ? $each_template->from_email : get_option( 'woocommerce_email_from_address' ) ;
                                $Message         = strip_tags( $each_template->message ) ;
                                $Message         = (strlen( $Message ) > 80) ? substr( $Message , 0 , 80 ) . '...' : $Message ;
                                $Status          = $each_template->rs_status ;
                                $ButtonText      = ($Status == 'ACTIVE') ? __( 'Deactivate' , SRP_LOCALE ) : __( 'Activate' , SRP_LOCALE ) ;
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $i ; ?>&nbsp;&nbsp;
                                        <span>
                                            <a href="<?php echo $EditTemplateURL ; ?>"><?php _e( 'Edit' , SRP_LOCALE ) ; ?></a>&nbsp;&nbsp;
                                        </span>
                                        <span>
                                            <a href="" class="rs_delete_expired" data-id="<?php echo $each_template->id ; ?>" ><?php _e( 'Delete' , SRP_LOCALE ) ; ?></a>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $each_template->template_name ; ?>
                                    </td>
                                    <td>
                                        <?php echo $FromName ; ?>
                                    </td>
                                    <td>
                                        <?php echo $FromEmail ; ?>
                                    </td>
                                    <td>
                                        <?php echo $each_template->subject ; ?>
                                    </td>
                                    <td>
                                        <?php echo $Message ; ?>
                                    </td>
                                    <td>
                                        <?php echo $each_template->noofdays ; ?>
                                    </td>
                                    <td>
                                        <a href="#" class="button rs_expired_mail_active" data-rsmailid="<?php echo $each_template->id ; ?>" data-currentstate="<?php echo $Status ; ?>"><?php echo $ButtonText ; ?></a>
                                    </td>
                                </tr>
                                <?php
                                $i ++ ;
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <div style="clear:both;">
                    <div class="pagination pagination-centered"></div>
                </div>
                <?php
            }
        }

        public static function table_for_template( $Template , $Bool , $edit = '' ) {
            $EditorId        = empty( $edit ) ? "rs_email_new_expired" : "rs_email_expired_template_edit" ;
            $Textarea        = array( 'textarea_name' => $EditorId ) ;
            $Content         = $Bool ? $Template->message : __( "Hi {rsfirstname} {rslastname}, <br><br>Please check the below table which shows about your earned points with an expiry date. You can make use of those points to get discount on future purchases in {rssitelink} <br><br> {rs_points_expire} <br><br> Thanks" , SRP_LOCALE ) ;
            $TemplateName    = $Bool ? $Template->template_name : __( 'Default' , SRP_LOCALE ) ;
            $NonActiveStatus = $Bool ? selected( $Template->rs_status , 'NOTACTIVE' , false ) : '' ;
            $ActiveStatus    = $Bool ? selected( $Template->rs_status , 'ACTIVE' , false ) : '' ;
            $Woo             = $Bool ? checked( $Template->sender_opt , 'woo' , false ) : checked( 'woo' , 'woo' , false ) ;
            $Local           = $Bool ? checked( $Template->sender_opt , 'local' , false ) : '' ;
            $FromName        = $Bool ? $Template->from_name : __( 'Admin' , SRP_LOCALE ) ;
            $FromMail        = $Bool ? $Template->from_email : '' ;
            $Subject         = $Bool ? $Template->subject : '' ;
            $NoofDays        = $Bool ? $Template->noofdays : '' ;
            $ReturnURL       = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpemailexpiredpoints' ) , SRP_ADMIN_URL ) ;
            ?>
            <table class="widefat">
                <tr><td><span><strong>{rssitelink}</strong> - <?php _e( 'Use this Shortcode to insert the Cart Link in the mail' , SRP_LOCALE ) ; ?></span></td></tr>
                <tr><td><span><strong>{rsfirstname}</strong> - <?php _e( 'Use this Shortcode to insert Receiver First Name in the mail' , SRP_LOCALE ) ; ?></span></td></tr>
                <tr><td><span><strong>{rslastname}</strong> - <?php _e( 'Use this Shortcode to insert Receiver Last Name in the mail' , SRP_LOCALE ) ; ?></span></td></tr>
                <tr><td><span><strong>{rs_points_expire}</strong> - <?php _e( 'Use this shortcode to display the earned points with an expiry date in the Table' , SRP_LOCALE ) ; ?></span></td></tr>
                <tr><td><span><strong>{site_referral_url}</strong> - <?php _e( 'Use this Shortcode for displaying the Referral Link' , SRP_LOCALE ) ; ?></span></td></tr>                 
                <tr>
                    <td><?php _e( 'Template Name' , SRP_LOCALE ) ?>:</td>
                    <td>
                        <input type="text" name="rs_email_expired_name" id="rs_email_expired_name" value="<?php echo $TemplateName ; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td><?php _e( 'Template Status' , SRP_LOCALE ) ?>:</td>
                    <td>
                        <select name="rs_expired_template_status" id="rs_expired_template_status">                       
                            <option value="NOTACTIVE" <?php echo $NonActiveStatus ; ?> ><?php _e( 'Deactivated' , SRP_LOCALE ) ; ?></option>
                            <option value="ACTIVE" <?php echo $ActiveStatus ; ?> ><?php _e( 'Activated' , SRP_LOCALE ) ; ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?php _e( 'Email Sender Option' , SRP_LOCALE ) ?>:</td>
                    <td>
                        <input type="radio" name="rs_sender_opt_expired" id="rs_sender_woo_expired" value="woo" <?php echo $Woo ; ?> class="rs_sender_opt_expired"/><?php _e( 'Woocommerce' , SRP_LOCALE ) ; ?>
                        <input type="radio" name="rs_sender_opt_expired" id="rs_sender_local" value="local" <?php echo $Local ; ?> class="rs_sender_opt_expired"><?php _e( 'Local' , SRP_LOCALE ) ; ?>
                    </td>
                </tr>
                <tr class="rs_local_senders_expired">
                    <td><?php _e( 'From Name' , SRP_LOCALE ) ?>:</td>
                    <td>
                        <input type="text" name="rs_expired_from_name" id="rs_expired_from_name" value="<?php echo $FromName ; ?>"/>
                    </td>
                </tr>
                <tr class="rs_local_senders_expired">
                    <td><?php _e( 'From Email' , SRP_LOCALE ) ?>:</td>
                    <td>
                        <input type="text" name="rs_expired_from_email" id="rs_expired_from_email" value="<?php echo $FromMail ; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td><?php _e( 'Email Subject' , SRP_LOCALE ) ?>:</td>
                    <td>
                        <input type="text" name="rs_subject_expired" id="rs_subject_expired" value="<?php echo $Subject ; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td><?php _e( 'No of Days' , SRP_LOCALE ) ?>:</td>
                    <td>
                        <input type="text" name="rs_no_of_days" id="rs_no_of_days" value="<?php echo $NoofDays ; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td><?php _e( 'Email Message' , SRP_LOCALE ) ?>:</td>
                    <td>
                        <?php
                        wp_editor( $Content , $EditorId , $Textarea ) ;
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="button" name="rs_save_new_expired_template" class="button button-primary button-large" id="rs_save_new_expired_template" value="<?php _e( 'Save' , SRP_LOCALE ) ; ?>">
                        <a href="<?php echo $ReturnURL ?>"><input type="button" class="button" name="returntolist" value=<?php _e( "Return to Mail Templates" , SRP_LOCALE ) ; ?>></a>
                    </td>
                </tr>
            </table>
            <?php
        }

        public static function reset_emailexpiry_module() {
            $Settings = RSEmailTemplate::settings_fields() ;
            RSTabManagement::reset_settings( $Settings ) ;
        }

    }

    RSEmailExpiredPointsTemplate::init() ;
}