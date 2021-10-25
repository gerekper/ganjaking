<?php
/*
 * Nominee Setting Tab
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSNominee' ) ) {

    class RSNominee {

        public static function init() {
            add_action( 'woocommerce_rs_settings_tabs_fpnominee' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsmodules_fpnominee' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action( 'rs_default_settings_fpnominee' , array( __CLASS__ , 'set_default_value' ) ) ;

            add_action( 'woocommerce_admin_field_rs_select_nominee_for_user' , array( __CLASS__ , 'rs_select_user_as_nominee' ) ) ;

            add_action( 'woocommerce_admin_field_rs_select_nominee_for_user_shortcode' , array( __CLASS__ , 'rs_select_user_as_nominee_shortcode' ) ) ;

            add_action( 'admin_head' , array( __CLASS__ , 'rs_chosen_for_nominee_tab' ) ) ;

            add_action( 'woocommerce_admin_field_rs_select_nominee_for_user_in_checkout' , array( __CLASS__ , 'rs_select_user_as_nominee_in_checkout' ) ) ;

            add_action( 'woocommerce_admin_field_rs_nominee_list_table' , array( __CLASS__ , 'rs_function_to_display_nominee_list_table' ) ) ;

            add_action( 'admin_head' , array( __CLASS__ , 'rs_function_to_enable_disable_nominee' ) ) ;

            add_action( 'wp_ajax_nopriv_rs_action_to_enable_disable_nominee' , array( __CLASS__ , 'rs_ajax_function_to_enable_disable' ) ) ;

            add_action( 'wp_ajax_rs_action_to_enable_disable_nominee' , array( __CLASS__ , 'rs_ajax_function_to_enable_disable' ) ) ;

            add_action( 'fp_action_to_reset_module_settings_fpnominee' , array( __CLASS__ , 'reset_nominee_module' ) ) ;

            add_action( 'woocommerce_admin_field_rs_enable_disable_nominee_module' , array( __CLASS__ , 'enable_module' ) ) ;
            
            add_action( 'rs_display_save_button_fpnominee' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

            add_action( 'rs_display_reset_button_fpnominee' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce ;
            global $wp_roles ;
            foreach ( $wp_roles->roles as $values => $key ) {
                $userroleslug[] = $values ;
                $userrolename[] = $key[ 'name' ] ;
            }

            $newcombineduserrole = array_combine( ( array ) $userroleslug , ( array ) $userrolename ) ;
            return apply_filters( 'woocommerce_fpnominee_settings' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Nominee Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_nominee_module'
                ) ,
                array(
                    'type' => 'rs_enable_disable_nominee_module' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_nominee_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Nominee Settings for Product Purchase in Checkout Page' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_nominee_setting_in_checkout'
                ) ,
                array(
                    'name'    => __( 'Nominee Field' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_nominee_field_in_checkout' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_nominee_field_in_checkout' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'My Nominee Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the My Nominee Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_nominee_title_in_checkout' ,
                    'std'      => 'My Nominee' ,
                    'default'  => 'My Nominee' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_nominee_title_in_checkout' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Nominee User Selection' , SRP_LOCALE ) ,
                    'id'      => 'rs_select_type_of_user_for_nominee_checkout' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'By User(s)' , SRP_LOCALE ) ,
                        '2' => __( 'By User Role(s)' , SRP_LOCALE ) ,
                    ) ,
                    'newids'  => 'rs_select_type_of_user_for_nominee_checkout' ,
                ) ,
                array(
                    'type' => 'rs_select_nominee_for_user_in_checkout' ,
                ) ,
                array(
                    'name'        => __( 'User Role Selection' , SRP_LOCALE ) ,
                    'id'          => 'rs_select_users_role_for_nominee_checkout' ,
                    'css'         => 'min-width:343px;' ,
                    'std'         => '' ,
                    'default'     => '' ,
                    'placeholder' => 'Search for a User Role' ,
                    'type'        => 'multiselect' ,
                    'options'     => $newcombineduserrole ,
                    'newids'      => 'rs_select_users_role_for_nominee_checkout' ,
                    'desc_tip'    => false ,
                ) ,
                array(
                    'name'    => __( 'Checkout Page Nominee is identified based on' , SRP_LOCALE ) ,
                    'id'      => 'rs_select_type_of_user_for_nominee_name_checkout' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'User Email ' , SRP_LOCALE ) ,
                        '2' => __( 'Username' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_nominee_setting_in_checkout' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Nominee Settings for Product Purchase in My Account Page' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_nominee_setting'
                ) ,
                array(
                    'name'    => __( 'Nominee Field' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_nominee_field' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_nominee_field' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'My Nominee Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the My Nominee Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_nominee_title' ,
                    'std'      => 'My Nominee' ,
                    'default'  => 'My Nominee' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_nominee_title' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Nominee User Selection' , SRP_LOCALE ) ,
                    'id'      => 'rs_select_type_of_user_for_nominee' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'By User(s)' , SRP_LOCALE ) ,
                        '2' => __( 'By User Role(s)' , SRP_LOCALE ) ,
                    ) ,
                    'newids'  => 'rs_select_type_of_user_for_nominee' ,
                ) ,
                array(
                    'type' => 'rs_select_nominee_for_user' ,
                ) ,
                array(
                    'name'        => __( 'User Role Selection' , SRP_LOCALE ) ,
                    'id'          => 'rs_select_users_role_for_nominee' ,
                    'css'         => 'min-width:343px;' ,
                    'std'         => '' ,
                    'default'     => '' ,
                    'placeholder' => 'Search for a User Role' ,
                    'type'        => 'multiselect' ,
                    'options'     => $newcombineduserrole ,
                    'newids'      => 'rs_select_users_role_for_nominee' ,
                    'desc_tip'    => false ,
                ) ,
                array(
                    'name'    => __( 'My Account Page Nominee is identified based on' , SRP_LOCALE ) ,
                    'id'      => 'rs_select_type_of_user_for_nominee_name' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'User Email ' , SRP_LOCALE ) ,
                        '2' => __( 'Username' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Nominee Field - Shortcode' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_nominee_field_shortcode' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_nominee_field_shortcode' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'My Nominee Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the My Nominee Label' , SRP_LOCALE ) ,
                    'id'       => 'rs_my_nominee_title_shortcode' ,
                    'std'      => 'My Nominee' ,
                    'default'  => 'My Nominee' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_my_nominee_title_shortcode' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Nominee User Selection' , SRP_LOCALE ) ,
                    'id'      => 'rs_select_type_of_user_for_nominee_shortcode' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'By User(s)' , SRP_LOCALE ) ,
                        '2' => __( 'By User Role(s)' , SRP_LOCALE ) ,
                    ) ,
                    'newids'  => 'rs_select_type_of_user_for_nominee_shortcode' ,
                ) ,
                array(
                    'type' => 'rs_select_nominee_for_user_shortcode' ,
                ) ,
                array(
                    'name'        => __( 'User Role Selection' , SRP_LOCALE ) ,
                    'id'          => 'rs_select_users_role_for_nominee_shortcode' ,
                    'css'         => 'min-width:343px;' ,
                    'std'         => '' ,
                    'default'     => '' ,
                    'placeholder' => 'Search for a User Role' ,
                    'type'        => 'multiselect' ,
                    'options'     => $newcombineduserrole ,
                    'newids'      => 'rs_select_users_role_for_nominee_shortcode' ,
                    'desc_tip'    => false ,
                ) ,
                array(
                    'name'    => __( 'My Account Page Nominee is identified based on' , SRP_LOCALE ) ,
                    'id'      => 'rs_select_type_of_user_for_nominee_name_shortcode' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_select_type_of_user_for_nominee_name_shortcode' ,
                    'options' => array(
                        '1' => __( 'User Email ' , SRP_LOCALE ) ,
                        '2' => __( 'Username' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_nominee_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Nominated Users List' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_nominated_user_list'
                ) ,
                array(
                    'type' => 'rs_nominee_list_table'
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_nominated_user_list' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                    ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields( RSNominee::reward_system_admin_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options( RSNominee::reward_system_admin_fields() ) ;
            if ( isset( $_POST[ 'rs_select_users_role_for_nominee' ] ) ) {
                update_option( 'rs_select_users_role_for_nominee' , $_POST[ 'rs_select_users_role_for_nominee' ] ) ;
            } else {
                update_option( 'rs_select_users_role_for_nominee' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_select_users_role_for_nominee_shortcode' ] ) ) {
                update_option( 'rs_select_users_role_for_nominee_shortcode' , $_POST[ 'rs_select_users_role_for_nominee_shortcode' ] ) ;
            } else {
                update_option( 'rs_select_users_role_for_nominee_shortcode' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_select_users_list_for_nominee' ] ) ) {
                update_option( 'rs_select_users_list_for_nominee' , $_POST[ 'rs_select_users_list_for_nominee' ] ) ;
            } else {
                update_option( 'rs_select_users_list_for_nominee' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_select_users_list_for_nominee_shortcode' ] ) ) {
                update_option( 'rs_select_users_list_for_nominee_shortcode' , $_POST[ 'rs_select_users_list_for_nominee_shortcode' ] ) ;
            } else {
                update_option( 'rs_select_users_list_for_nominee_shortcode' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_select_users_role_for_nominee_checkout' ] ) ) {
                update_option( 'rs_select_users_role_for_nominee_checkout' , $_POST[ 'rs_select_users_role_for_nominee_checkout' ] ) ;
            } else {
                update_option( 'rs_select_users_role_for_nominee_checkout' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_select_users_list_for_nominee_in_checkout' ] ) ) {
                update_option( 'rs_select_users_list_for_nominee_in_checkout' , $_POST[ 'rs_select_users_list_for_nominee_in_checkout' ] ) ;
            } else {
                update_option( 'rs_select_users_list_for_nominee_in_checkout' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_nominee_module_checkbox' ] ) ) {
                update_option( 'rs_nominee_activated' , $_POST[ 'rs_nominee_module_checkbox' ] ) ;
            } else {
                update_option( 'rs_nominee_activated' , 'no' ) ;
            }
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function set_default_value() {
            foreach ( RSNominee::reward_system_admin_fields() as $setting )
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_nominee_activated' ) , 'rs_nominee_module_checkbox' ,'rs_nominee_activated') ;
        }

        /*
         * Function to Select user as Nominee
         */

        public static function rs_select_user_as_nominee() {
            $field_id    = "rs_select_users_list_for_nominee" ;
            $field_label = "User Selection" ;
            $getuser     = get_option( 'rs_select_users_list_for_nominee' ) ;
            echo user_selection_field( $field_id , $field_label , $getuser ) ;
        }

        public static function rs_select_user_as_nominee_shortcode() {
            $field_id    = "rs_select_users_list_for_nominee_shortcode" ;
            $field_label = "User Selection" ;
            $getuser     = get_option( 'rs_select_users_list_for_nominee_shortcode' ) ;
            echo user_selection_field( $field_id , $field_label , $getuser ) ;
        }

        /*
         * Function for choosen in Select user role as Nominee
         */

        public static function rs_chosen_for_nominee_tab() {
            global $woocommerce ;
            if ( isset( $_GET[ 'page' ] ) ) {
                if ( isset( $_GET[ 'tab' ] ) && isset( $_GET[ 'section' ] ) ) {
                    if ( $_GET[ 'section' ] == 'fpnominee' ) {
                        if ( ( float ) $woocommerce->version > ( float ) ('2.2.0') ) {
                            echo rs_common_select_function( '#rs_select_users_role_for_nominee' ) ;
                            echo rs_common_select_function( '#rs_select_users_role_for_nominee_checkout' ) ;
                            echo rs_common_select_function( '#rs_select_users_role_for_nominee_shortcode' ) ;
                        } else {
                            echo rs_common_chosen_function( '#rs_select_users_role_for_nominee' ) ;
                            echo rs_common_chosen_function( '#rs_select_users_role_for_nominee_checkout' ) ;
                            echo rs_common_chosen_function( '#rs_select_users_role_for_nominee_shortcode' ) ;
                        }
                    }
                }
            }
        }

        public static function rs_select_user_as_nominee_in_checkout() {
            $field_id    = "rs_select_users_list_for_nominee_in_checkout" ;
            $field_label = "User Selection" ;
            $getuser     = get_option( 'rs_select_users_list_for_nominee_in_checkout' ) ;
            echo user_selection_field( $field_id , $field_label , $getuser ) ;
        }

        public static function rs_function_to_display_nominee_list_table() {
            $newwp_list_table_for_users = new WP_List_Table_for_Nominee() ;
            $newwp_list_table_for_users->prepare_items() ;
            $plugin_url                 = WP_PLUGIN_URL ;
            $newwp_list_table_for_users->display() ;
        }

        public static function rs_function_to_enable_disable_nominee() {
            ?>
            <script type="text/javascript">
                jQuery( document ).ready( function () {
                    jQuery( '.rs_enable_disable' ).click( function () {
                        var userid = jQuery( this ).attr( 'data-userid' ) ;
                        var checkboxvalue = jQuery( this ).is( ':checked' ) ? 'yes' : 'no' ;
                        var nomineeid = jQuery( this ).attr( 'data-nomineeid' ) ;
                        var dataparam = ( {
                            action : 'rs_action_to_enable_disable_nominee' ,
                            userid : userid ,
                            checkboxvalue : checkboxvalue ,
                            nomineeid : nomineeid
                        } ) ;
                        jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , dataparam ,
                                function ( response ) {
                                    console.log( response ) ;
                                } , 'json' ) ;
                    } ) ;
                } ) ;
            </script>
            <?php
        }

        public static function rs_ajax_function_to_enable_disable() {
            if ( isset( $_POST[ 'userid' ] ) && $_POST[ 'userid' ] != '' ) {
                $userid    = $_POST[ 'userid' ] ;
                $nomineeid = $_POST[ 'nomineeid' ] ;
                if ( isset( $_POST[ 'checkboxvalue' ] ) ) {
                    update_user_meta( $userid , 'rs_enable_nominee' , $_POST[ 'checkboxvalue' ] ) ;
                }
            }
        }

        public static function reset_nominee_module() {
            $settings = RSNominee::reward_system_admin_fields() ;
            RSTabManagement::reset_settings( $settings ) ;
        }

    }

    RSNominee::init() ;
}
