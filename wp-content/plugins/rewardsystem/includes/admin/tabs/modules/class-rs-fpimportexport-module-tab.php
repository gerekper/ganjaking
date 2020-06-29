<?php
/*
 * Import Export Tab Setting
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSImportExport' ) ) {

    class RSImportExport {

        public static function init() {
            add_action( 'woocommerce_rs_settings_tabs_fpimportexport' , array( __CLASS__ , 'register_admin_settings' ) ) ;

            add_action( 'woocommerce_update_options_fprsmodules_fpimportexport' , array( __CLASS__ , 'update_settings' ) ) ;

            add_action( 'woocommerce_admin_field_rs_import_export_selected_user' , array( __CLASS__ , 'user_selection_field' ) ) ;

            add_action( 'woocommerce_admin_field_import_export' , array( __CLASS__ , 'settings_to_impexp_points' ) ) ;

            add_action( 'woocommerce_admin_field_rs_enable_disable_imp_exp_module' , array( __CLASS__ , 'enable_module' ) ) ;

            add_action( 'rs_default_settings_fpimportexport' , array( __CLASS__ , 'set_default_value' ) ) ;

            add_action( 'fp_action_to_reset_module_settings_fpimportexport' , array( __CLASS__ , 'reset_imp_exp_module' ) ) ;
        }

        public static function settings_option() {
            return apply_filters( 'woocommerce_rewardsystem_gift_voucher_settings' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Import/Export Points Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_imp_exp_module'
                ) ,
                array(
                    'type' => 'rs_enable_disable_imp_exp_module' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_imp_exp_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Import/Export User Points in CSV Format' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_import_export_setting'
                ) ,
                array(
                    'name'     => __( 'Export available Points for' , SRP_LOCALE ) ,
                    'desc'     => __( 'Here you can set whether to Export Reward Points for All Users or Selected Users' , SRP_LOCALE ) ,
                    'id'       => 'rs_export_import_user_option' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'radio' ,
                    'options'  => array( '1' => 'All Users' , '2' => 'Selected Users' ) ,
                    'newids'   => 'rs_export_import_user_option' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Select the User(s) for whom you wish to Export Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Here you select the users to whom you wish to Export Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_import_export_users_list' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'rs_import_export_selected_user' ,
                    'newids'   => 'rs_import_export_users_list' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Users are identified based on' , SRP_LOCALE ) ,
                    'desc'     => __( 'Here you can set whether to Export CSV Format with Username or Userid or Emailid' , SRP_LOCALE ) ,
                    'id'       => 'rs_csv_format' ,
                    'class'    => 'rs_csv_format' ,
                    'newids'   => 'rs_csv_format' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'radio' ,
                    'options'  => array( '1' => 'Username' , '2' => 'Email-Id' ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Export User Points for' , SRP_LOCALE ) ,
                    'desc'     => __( 'Here you can set whether to Export Reward Points for All Time or Selected Date' , SRP_LOCALE ) ,
                    'id'       => 'rs_export_import_date_option' ,
                    'class'    => 'rs_export_import_date_option' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'radio' ,
                    'options'  => array( '1' => 'All Time' , '2' => 'Selected Date' ) ,
                    'newids'   => 'rs_export_import_date_option' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'type' => 'import_export' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_import_export_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                    ) ) ;
        }

        public static function register_admin_settings() {
            woocommerce_admin_fields( self::settings_option() ) ;
        }

        public static function update_settings() {
            woocommerce_update_options( self::settings_option() ) ;
            if ( isset( $_POST[ 'rs_imp_exp_module_checkbox' ] ) ) {
                update_option( 'rs_imp_exp_activated' , $_POST[ 'rs_imp_exp_module_checkbox' ] ) ;
            } else {
                update_option( 'rs_imp_exp_activated' , 'no' ) ;
            }
        }

        public static function set_default_value() {
            foreach ( self::settings_option() as $setting )
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_imp_exp_activated' ) , 'rs_imp_exp_module_checkbox' , 'rs_imp_exp_activated' ) ;
        }

        public static function user_selection_field() {
            $field_id    = "rs_import_export_users_list" ;
            $field_label = "Select the Users that you wish to Export Reward Points" ;
            $getuser     = get_option( 'rs_import_export_users_list' ) ;
            echo user_selection_field( $field_id , $field_label , $getuser ) ;
        }

        public static function settings_to_impexp_points() {
            if ( isset( $_POST[ 'rs_import_user_points' ] ) || isset( $_POST[ 'rs_import_user_points_old' ] ) )
                self::imp_user_points() ;
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rs_point_export_start_date"><?php _e( 'Start Date' , SRP_LOCALE ) ; ?></label>
                </th>
                <td class="forminp forminp-select">
                    <input type="text" class="rs_point_export_start_date" value="" name="rs_point_export_start_date" id="rs_point_export_start_date" />
                </td>
            </tr>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rs_point_export_end_date"><?php _e( 'End Date' , SRP_LOCALE ) ; ?></label>
                </th>
                <td class="forminp forminp-select">
                    <input type="text" class="rs_point_export_end_date" value="" name="rs_point_export_end_date" id="rs_point_export_end_date" />
                </td>
            </tr>
            <tr valign ="top">
                <th class="titledesc" scope="row">
                    <label><?php _e( 'Export User Points to CSV' , SRP_LOCALE ) ; ?></label>
                </th>
                <td class="forminp forminp-select">
                    <input type="button" id="rs_export_user_points_csv" class="rs_export_button" name="rs_export_user_points_csv" value="<?php _e( 'Export User Points' , SRP_LOCALE ) ; ?>"/>
                </td>
            </tr>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rs_import_user_points_csv"><?php _e( 'Import User Points to CSV' , SRP_LOCALE ) ; ?></label>
                </th>
                <td class="forminp forminp-select">
                    <input type="file" id="rs_import_user_points_csv" name="file" />
                </td>
            </tr>
            <tr valign="top">
                <td class="forminp forminp-select">
                    <input type="submit" id="rs_import_user_points" class="rs_export_button" name="rs_import_user_points" value="<?php _e( 'Import CSV for Version 10.0 (Above 10.0)' , SRP_LOCALE ) ; ?>"/>
                </td>
                <td class="forminp forminp-select">
                    <input type="submit" id="rs_import_user_points_old" class="rs_export_button" name="rs_import_user_points_old" value="<?php _e( 'Import CSV for Older Version (Below 10.0)' , SRP_LOCALE ) ; ?>"/>
                </td>
            </tr>
            <?php if ( srp_check_is_array( get_option( 'rewardsystem_csv_array' ) ) ) { ?>
                <table class="wp-list-table widefat fixed posts">
                    <thead>
                        <tr>
                            <th>
                                <?php _e( 'Username' , SRP_LOCALE ) ; ?>
                            </th>
                            <th>
                                <?php _e( 'User Reward Points' , SRP_LOCALE ) ; ?>
                            </th>
                            <th>
                                <?php _e( 'Expiry Date' , SRP_LOCALE ) ; ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ( get_option( 'rewardsystem_csv_array' )as $newcsv ) {
                            ?>
                            <tr>
                                <td>
                                    <?php echo (isset( $newcsv[ 0 ] ) && ! empty( $newcsv[ 0 ] )) ? $newcsv[ 0 ] : '' ; ?>
                                </td>
                                <td>
                                    <?php echo (isset( $newcsv[ 1 ] ) && ! empty( $newcsv[ 1 ] )) ? $newcsv[ 1 ] : '0' ; ?>
                                </td>
                                <td>
                                    <?php
                                    if ( isset( $newcsv[ 2 ] ) ) {
                                        $date = ($newcsv[ 2 ] == '999999999999') ? '-' : date( 'm/d/Y h:i:s A T' , $newcsv[ 2 ] ) ;
                                        echo $date ;
                                    } else {
                                        echo '-' ;
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <table>
                    <tr valign="top">
                        <td>
                            <input type="submit" id="rs_new_action_reward_points" name="rs_new_action_reward_points" value="<?php _e( 'Override Existing User Points' , SRP_LOCALE ) ; ?>"/>
                        </td>
                        <td>
                            <input type="submit" id="rs_exist_action_reward_points" name="rs_exist_action_reward_points" value="<?php _e( 'Add Points with Already Earned Points' , SRP_LOCALE ) ; ?>"/>
                        </td>
                    </tr>
                </table>
                <?php
            }
            $DataToImport = get_option( 'rewardsystem_csv_array' ) ;
            if ( isset( $_POST[ 'rs_new_action_reward_points' ] ) || isset( $_POST[ 'rs_exist_action_reward_points' ] ) ) {
                if ( srp_check_is_array( $DataToImport ) ) {
                    if ( isset( $_POST[ 'rs_new_action_reward_points' ] ) )
                        self::delete_existing_points( $DataToImport ) ;

                    $CheckPoint = isset( $_POST[ 'rs_new_action_reward_points' ] ) ? 'IMPOVR' : 'IMPADD' ;
                    self::import_points_for_user( $DataToImport , $CheckPoint ) ;
                }
                delete_option( 'rewardsystem_csv_array' ) ;
                $redirect = add_query_arg( array( 'saved' => 'true' ) ) ;
                wp_safe_redirect( $redirect ) ;
                exit() ;
            }

            if ( isset( $_GET[ 'export_points' ] ) && $_GET[ 'export_points' ] == 'yes' ) {
                ob_end_clean() ;
                header( "Content-type: text/csv;charset=utf-8" ) ;
                header( "Content-Disposition: attachment; filename=reward_points_" . date_i18n( 'Y-m-d' ) . ".csv" ) ;
                header( "Pragma: no-cache" ) ;
                header( "Expires: 0" ) ;
                $data = get_option( 'rs_data_to_impexp' ) ;
                self::outputCSV( $data ) ;
                exit() ;
            }
        }

        public static function imp_user_points() {
            if ( $_FILES[ "file" ][ "error" ] > 0 ) {
                echo "Error: " . $_FILES[ "file" ][ "error" ] . "<br>" ;
            } else {
                $mimes = array( 'text/csv' ,
                    'text/plain' ,
                    'application/csv' ,
                    'text/comma-separated-values' ,
                    'application/excel' ,
                    'application/vnd.ms-excel' ,
                    'application/vnd.msexcel' ,
                    'text/anytext' ,
                    'application/octet-stream' ,
                    'application/txt' ) ;
                if ( in_array( $_FILES[ 'file' ][ 'type' ] , $mimes ) ) {
                    self::inputCSV( $_FILES[ "file" ][ "tmp_name" ] ) ;
                } else {
                    ?>
                    <style type="text/css">
                        div.error {
                            display:block;
                        }
                    </style>
                    <?php
                }
            }
        }

        public static function delete_existing_points( $DataToImport ) {
            global $wpdb ;
            $PonitsTable = $wpdb->prefix . 'rspointexpiry' ;
            foreach ( $DataToImport as $ImpValues ) {
                $Username = isset( $ImpValues[ 0 ] ) && ! empty( $ImpValues[ 0 ] ) ? $ImpValues[ 0 ] : '' ;
                $UserInfo = get_user_by( 'login' , $Username ) ? get_user_by( 'login' , $Username ) : get_user_by( 'email' , $Username ) ;

                if ( ! $UserInfo )
                    continue ;

                $wpdb->delete( $PonitsTable , array( 'userid' => $UserInfo->ID ) ) ;
            }
        }

        public static function import_points_for_user( $DataToImport , $CheckPoint ) {
            foreach ( $DataToImport as $ImpValues ) {
                $Username = (isset( $ImpValues[ 0 ] ) && ! empty( $ImpValues[ 0 ] )) ? $ImpValues[ 0 ] : '' ;
                $Points   = (isset( $ImpValues[ 1 ] ) && ! empty( $ImpValues[ 1 ] )) ? $ImpValues[ 1 ] : 0 ;
                $Date     = (isset( $ImpValues[ 2 ] ) && ! empty( $ImpValues[ 2 ] )) ? date( 'm/d/Y h:i:s A T' , $ImpValues[ 2 ] ) : 999999999999 ;
                $UserInfo = get_user_by( 'login' , $Username ) ? get_user_by( 'login' , $Username ) : get_user_by( 'email' , $Username ) ;
                if ( ! $UserInfo )
                    continue ;

                $UserId     = $UserInfo->ID ;
                $Date       = is_numeric( $Date ) ? 999999999999 : strtotime( $Date ) ;
                $table_args = array(
                    'user_id'           => $UserInfo->ID ,
                    'pointstoinsert'    => $Points ,
                    'checkpoints'       => $CheckPoint ,
                    'totalearnedpoints' => $Points ,
                    'date'              => $Date
                        ) ;
                RSPointExpiry::insert_earning_points( $table_args ) ;
                RSPointExpiry::record_the_points( $table_args ) ;
            }
        }

        public static function outputCSV( $data ) {
            $output = fopen( "php://output" , "w" ) ;
            foreach ( $data as $row ) {
                if ( $row != false ) {
                    fputcsv( $output , $row ) ; // here you can change delimiter/enclosure
                }
            }
            fclose( $output ) ;
        }

        public static function inputCSV( $data_path ) {
            if ( ! ($handle = fopen( $data_path , "r" )) )
                return ;

            while ( ($data = fgetcsv( $handle , 1000 , "," )) !== FALSE ) {
                $datas        = $data[ 2 ] != 0 ? strtotime( $data[ 2 ] ) : '' ;
                $collection[] = array_filter( array( $data[ 0 ] , $data[ 1 ] , $datas ) ) ;
            }
            update_option( 'rewardsystem_csv_array' , array_merge( array_filter( $collection ) ) ) ;
            fclose( $handle ) ;
        }

        public static function reset_imp_exp_module() {
            $settings = RSImportExport::settings_option() ;
            RSTabManagement::reset_settings( $settings ) ;
        }

    }

    RSImportExport::init() ;
}