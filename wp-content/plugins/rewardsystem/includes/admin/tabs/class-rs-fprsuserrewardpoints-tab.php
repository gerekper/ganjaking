<?php
/*
 * User Reward Points Tab
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSUserRewardPoints' ) ) {

    class RSUserRewardPoints {

        public static function init() {
            add_action( 'woocommerce_rs_settings_tabs_fprsuserrewardpoints' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsuserrewardpoints' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action( 'woocommerce_admin_field_wplisttable_for_log' , array( __CLASS__ , 'display_log' ) ) ;

            add_action( 'woocommerce_admin_field_user_and_user_role_filter' , array( __CLASS__ , 'user_and_user_role_filter' ) ) ;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            $ListofRoles = fp_user_roles() ;
            return apply_filters( 'woocommerce_fprsuserrewardpoints_settings' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'User Reward Points Log' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_user_reward_points_setting' ,
                ) ,
                array(
                    'name'     => __( 'Filter by' , SRP_LOCALE ) ,
                    'id'       => 'rs_filter_type_for_log' ,
                    'class'    => 'rs_filter_type_for_log' ,
                    'newids'   => 'rs_filter_type_for_log' ,
                    'std'      => '1' ,
                    'defaults' => '1' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'User(s)' , SRP_LOCALE ) ,
                        '2' => __( 'User Role(s)' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'        => __( 'Select the User Role(s)' , SRP_LOCALE ) ,
                    'id'          => 'rs_userrole_for_reward_log' ,
                    'css'         => 'min-width:343px;' ,
                    'std'         => '' ,
                    'default'     => '' ,
                    'placeholder' => 'Search for a User Role' ,
                    'type'        => 'multiselect' ,
                    'options'     => $ListofRoles ,
                    'newids'      => 'rs_userrole_for_reward_log' ,
                ) ,
                array(
                    'type' => 'user_and_user_role_filter' ,
                ) ,
                array(
                    'type' => 'wplisttable_for_log' ,
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
            woocommerce_admin_fields( RSUserRewardPoints::reward_system_admin_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options( RSUserRewardPoints::reward_system_admin_fields() ) ;
        }

        public static function user_and_user_role_filter() {
            $field_id    = "rs_select_user_for_reward_log" ;
            $field_label = "Select the User(s)" ;
            $getuser     = get_option( 'rs_select_user_for_reward_log' ) ;
            echo user_selection_field( $field_id , $field_label , $getuser ) ;
            ?>
            <tr>
                <td></td>
                <td>
                    <input type="submit" value="<?php _e( 'Search' , SRP_LOCALE ) ?>" id="rs_submit_for_user_role_log" class="button-primary" name="rs_submit_for_user_role_log">
                </td>
            </tr>
            <?php
        }

        public static function display_log() {
            if ( ( ! isset( $_GET[ 'view' ] )) && ( ! isset( $_GET[ 'edit' ] )) ) {
                $UserRewardListTable = new WP_List_Table_for_Users() ;
                $UserRewardListTable->prepare_items() ;
                $UserRewardListTable->search_box( 'Search Users' , 'search_id' ) ;
                $UserRewardListTable->display() ;
            } elseif ( isset( $_GET[ 'view' ] ) ) {
                $UserRewardLogListTable = new WP_List_Table_for_View_Log() ;
                $UserRewardLogListTable->prepare_items() ;
                $UserRewardLogListTable->search_box( 'Search' , 'search_id' ) ;
                $UserRewardLogListTable->display() ;
                ?>
                <a href="<?php echo remove_query_arg( array( 'view' ) , get_permalink() ) ; ?>">Go Back</a>
                <?php
            } else {
                $UserId        = $_GET[ 'edit' ] ;
                $PointsData    = new RS_Points_Data( $UserId ) ;
                $Points        = $PointsData->total_available_points() ;
                $PointsEntered = isset( $_POST[ 'rs_points' ] ) ? $_POST[ 'rs_points' ] : 0 ;
                $reason        = isset( $_POST[ 'reason_in_detail' ] ) ? $_POST[ 'reason_in_detail' ] : '' ;
                $table_args    = array(
                    'user_id'           => $UserId ,
                    'pointstoinsert'    => $PointsEntered ,
                    'checkpoints'       => isset( $_POST[ 'rs_add_points_for_user' ] ) ? 'MAURP' : 'MRURP' ,
                    'totalearnedpoints' => $PointsEntered ,
                    'reason'            => $reason ,
                        ) ;

                if ( isset( $_POST[ 'rs_add_points_for_user' ] ) ) {
                    if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
                        $object = new RewardPointsOrder( 0 , 'no' ) ;
                        $object->check_point_restriction( $PointsEntered , 0 , 'MAURP' , $UserId , '' , '' , '' , '' , $reason ) ;
                    } else {
                        RSPointExpiry::insert_earning_points( $table_args ) ;
                        RSPointExpiry::record_the_points( $table_args ) ;
                    }
                }

                if ( isset( $_POST[ 'rs_remove_point_for_user' ] ) ) {
                    if ( $PointsEntered <= $Points ) {
                        RSPointExpiry::perform_calculation_with_expiry( $PointsEntered , $UserId ) ;
                        RSPointExpiry::record_the_points( $table_args ) ;
                        $redirect = add_query_arg( 'saved' , 'true' , get_permalink() ) ;
                        wp_safe_redirect( $redirect ) ;
                        exit() ;
                    }
                }
                ?>
                <h3><?php _e( 'Update User Reward Points' , SRP_LOCALE ) ; ?></h3>
                <table class="form-table">
                    <tr valign ="top">
                        <th class="titledesc" scope="row">
                            <label><?php _e( 'Current Points for User' , SRP_LOCALE ) ; ?></label>
                        </th>
                        <td class="forminp forminp-text">
                            <input type="number" style="min-width:150px;" readonly="readonly" value="<?php echo $Points ; ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th class="titledesc" scope="row">
                            <label><?php _e( 'Enter Points' , SRP_LOCALE ) ; ?></label>
                        </th>
                        <td class="forminp forminp-text">
                            <input type="number" style="min-width:150px;" required='required' min="0" id="rs_points" name="rs_points">
                            <div class='rs_add_remove_points_errors' style="color: red;font-size:14px;"></div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th class="titledesc" scope="row">
                            <label><?php _e( 'Reason in Detail' , SRP_LOCALE ) ; ?></label>
                        </th>
                        <td class="forminp forminp-text">
                            <textarea cols='40' rows='5' name='reason_in_detail' required='required'></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td>
                            <input type='submit' name='rs_add_points_for_user' class='button-primary rs_add_point_for_user' value='<?php _e( 'Add Points' , SRP_LOCALE ) ; ?>' />
                            <input type='hidden' id ="rs_selected_user_id" value='<?php echo esc_html( $UserId ) ; ?>'>
                        </td>
                        <td style="width:10px;">
                            <input type='submit' name='rs_remove_point_for_user' class='button-primary rs_remove_point_for_user' value='<?php _e( 'Remove Points' , SRP_LOCALE ) ; ?>' />
                        </td>
                        <td>
                            <a href="<?php echo remove_query_arg( array( 'edit' , 'saved' ) , get_permalink() ) ; ?>">
                                <input type='submit' class='button-primary' value='<?php _e( 'Go Back' , SRP_LOCALE ) ; ?>'/>
                            </a>
                        </td>
                    </tr>
                </table>
                <?php
            }
        }

    }

    RSUserRewardPoints::init() ;
}