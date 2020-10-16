<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit() ;
}

if ( ! class_exists( 'RS_Main_Function_for_Background_Process' ) ) {

    /**
     * RS_Main_Function_for_Background_Process Class.
     */
    class RS_Main_Function_for_Background_Process {

        public static $update_simple_product ;
        public static $update_variable_product ;
        public static $update_category ;
        public static $update_previous_order_points ;
        public static $add_points_for_user ;
        public static $remove_points_for_user ;
        public static $refresh_points_for_user ;
        public static $export_points_for_user ;
        public static $export_report_for_user ;
        public static $rs_progress_bar ;
        public static $add_existing_points_for_user ;
        public static $update_points_for_product ;
        public static $update_points_for_social_reward ;
        public static $update_buying_points_for_product ;
        public static $update_point_price_for_product ;
        public static $generate_voucher_codes ;
        public static $export_log_for_user ;
        public static $update_earned_points ;

        public static function init() {
            if ( self::fp_rs_upgrade_file_exists() ) {
                if ( ! class_exists( 'WP_Async_Request' ) )
                    include_once(untrailingslashit( WP_PLUGIN_DIR ) . '/woocommerce/includes/libraries/wp-async-request.php') ;

                if ( ! class_exists( 'WP_Background_Process' ) )
                    include_once(untrailingslashit( WP_PLUGIN_DIR ) . '/woocommerce/includes/libraries/wp-background-process.php') ;

                $background_process = array(
                    'add-existing-points'                  => 'RS_Add_Existing_Points_For_User' ,
                    'add-points'                           => 'RS_Add_Points_For_User' ,
                    'apply-points-for-previous-order'      => 'RS_Apply_Points_For_Previous_Order' ,
                    'bulk-update-for-buying-points'        => 'RS_Bulk_Update_for_Buying_Points' ,
                    'bulk-update-for-point-price'          => 'RS_Bulk_Update_for_Point_Price' ,
                    'bulk-update-points'                   => 'RS_Bulk_Update_for_Purchase_Points' ,
                    'bulk-update-points-for-social-reward' => 'RS_Bulk_Update_for_Social_Reward' ,
                    'export-log'                           => 'RS_Export_Log' ,
                    'export-points'                        => 'RS_Export_Points_For_User' ,
                    'export-report-for-user'               => 'RS_Export_Report_For_User' ,
                    'generate-voucher-code'                => 'RS_Generate_Voucher_Codes' ,
                    'refresh-points'                       => 'RS_Refresh_Points_For_User' ,
                    'remove-points'                        => 'RS_Remove_Points_For_User' ,
                    'update-for-category'                  => 'RS_Update_for_Category' ,
                    'update-for-simple-product'            => 'RS_Update_for_Simple_Product' ,
                    'update-for-variable-product'          => 'RS_Update_for_Variable_Product' ,
                    'update-earned-points'                 => 'RS_Update_Earned_Points' ,
                        ) ;

                foreach ( $background_process as $key => $classname ) {
                    if ( ! class_exists( $classname ) )
                        include_once ( 'inc/rs-' . $key . '.php' ) ;
                }

                if ( ! class_exists( 'FP_Updating_Process_for_RS' ) )
                    include_once('inc/class-fp-rs-updating-process.php') ;

                add_action( 'plugins_loaded' , array( __CLASS__ , 'update_points_for_products' ) ) ;

                $actions = array(
                    'apply_points_for_previous_order' ,
                    'add_points_for_user' ,
                    'remove_points_for_user' ,
                    'update_expired_points_for_user' ,
                    'export_points_for_user' ,
                    'export_report_for_user' ,
                    'add_existing_points_for_user' ,
                    'bulk_update_for_purchase' ,
                    'bulk_update_for_social_reward' ,
                    'bulk_update_for_buying_points' ,
                    'bulk_update_for_point_price' ,
                    'generate_voucher_codes' ,
                    'export_log_for_user' ,
                    'rs_update_earned_points_for_user'
                        ) ;

                foreach ( $actions as $function_name ) {
                    add_action( 'admin_init' , array( __CLASS__ , $function_name ) ) ;
                }

                $set_transient = array(
                    'rs_database_upgrade_process'           => 'set_transient_for_product_update' ,
                    'applypointsforpreviousorder'           => 'set_transient_for_apply_points' ,
                    'rsaddpointforuser'                     => 'set_transient_for_add_points' ,
                    'rsremovepointforuser'                  => 'set_transient_for_remove_points' ,
                    'refreshexpiredpoints'                  => 'set_transient_for_refresh_points' ,
                    'exportpoints'                          => 'set_transient_for_export_points' ,
                    'exportreport'                          => 'set_transient_for_export_report' ,
                    'exportlog'                             => 'set_transient_for_export_log' ,
                    'addoldpoints'                          => 'set_transient_for_old_points' ,
                    'bulk_update_points_for_product'        => 'set_transient_for_bulk_update' ,
                    'bulk_update_points_for_social_rewards' => 'set_transient_for_bulk_update_of_social_reward' ,
                    'bulk_update_buying_points_for_product' => 'set_transient_for_bulk_update_of_buying_points' ,
                    'update_point_price_for_product'        => 'set_transient_for_bulk_update_of_point_price' ,
                    'generatevouchercode'                   => 'set_transient_for_voucher_codes' ,
                        ) ;

                foreach ( $set_transient as $ajax_key => $function_name ) {
                    add_action( 'wp_ajax_' . $ajax_key , array( __CLASS__ , $function_name ) ) ;
                }

                self::$update_simple_product            = new RS_Update_for_Simple_Product() ;
                self::$update_variable_product          = new RS_Update_for_Variable_Product() ;
                self::$update_category                  = new RS_Update_for_Category() ;
                self::$update_previous_order_points     = new RS_Apply_Points_For_Previous_Order() ;
                self::$add_points_for_user              = new RS_Add_Points_For_User() ;
                self::$remove_points_for_user           = new RS_Remove_Points_For_User() ;
                self::$refresh_points_for_user          = new RS_Refresh_Points_For_User() ;
                self::$export_points_for_user           = new RS_Export_Points_For_User() ;
                self::$export_report_for_user           = new RS_Export_Report_For_User() ;
                self::$rs_progress_bar                  = new FP_Updating_Process_for_RS() ;
                self::$add_existing_points_for_user     = new RS_Add_Existing_Points_For_User() ;
                self::$update_points_for_product        = new RS_Bulk_Update_for_Purchase_Points() ;
                self::$update_points_for_social_reward  = new RS_Bulk_Update_for_Social_Reward() ;
                self::$update_buying_points_for_product = new RS_Bulk_Update_for_Buying_Points() ;
                self::$update_point_price_for_product   = new RS_Bulk_Update_for_Point_Price() ;
                self::$generate_voucher_codes           = new RS_Generate_Voucher_Codes() ;
                self::$export_log_for_user              = new RS_Export_Log() ;
                self::$update_earned_points             = new RS_Update_Earned_Points() ;
            }
            add_action( 'admin_head' , array( __CLASS__ , 'rs_display_notice_in_top' ) ) ;
        }

        /* Initializing Background Process for Updating Points for Products */

        public static function update_points_for_products() {
            if ( ! get_transient( 'fp_rs_background_process_transient' ) )
                return ;

            delete_transient( 'fp_rs_background_process_transient' ) ;
            delete_option( 'rs_simple_product_background_updater_offset' ) ;
            FP_WooCommerce_Log::log( 'v18.0 Upgrade Started' ) ;
            self::callback_to_update_simple_product() ;
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'rs_background_process' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            wp_safe_redirect( $redirect_url ) ;
        }

        /* Initializing Background Process for Apply Points for Previous Order */

        public static function set_transient_for_apply_points() {
            check_ajax_referer( 'fp-apply-points' , 'sumo_security' ) ;

            try {
                if ( isset( $_POST[ 'previousorderpointsfor' ] ) )
                    update_option( 'rs_previous_order_points_for' , $_POST[ 'previousorderpointsfor' ] ) ;

                if ( isset( $_POST[ 'awardpointson' ] ) )
                    update_option( 'rs_award_points_on' , $_POST[ 'awardpointson' ] ) ;

                if ( isset( $_POST[ 'fromdate' ] ) )
                    update_option( 'rs_apply_points_fromdate' , $_POST[ 'fromdate' ] ) ;

                if ( isset( $_POST[ 'todate' ] ) )
                    update_option( 'rs_apply_points_todate' , $_POST[ 'todate' ] ) ;

                set_transient( 'fp_background_process_transient_for_apply_points' , true , 30 ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function apply_points_for_previous_order() {
            if ( ! get_transient( 'fp_background_process_transient_for_apply_points' ) )
                return ;

            delete_transient( 'fp_background_process_transient_for_apply_points' ) ;
            delete_option( 'rs_applied_previous_order_background_updater_offset' ) ;
            self::callback_to_apply_points_for_previous_order() ;
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_apply_points' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            wp_safe_redirect( $redirect_url ) ;
        }

        public static function callback_to_apply_points_for_previous_order( $offset = 0 , $limit = 1000 ) {
            $OrderStatusList          = array( 'wc-completed' ) ;
            $OrderStatusToApplyPoints = get_option( 'rs_order_status_control' ) ;
            foreach ( $OrderStatusToApplyPoints as $OrderStatus ) {
                $OrderStatusList[] = 'wc-' . $OrderStatus ;
            }
            $PreviousOrderPointsFor = get_option( 'rs_previous_order_points_for' ) ;
            if ( $PreviousOrderPointsFor == '1' ) {
                $args    = array( 'post_type' => 'shop_order' , 'numberposts' => '-1' , 'meta_query' => array( array( 'key' => 'reward_points_awarded' , 'compare' => 'NOT EXISTS' ) ) , 'post_status' => $OrderStatusList , 'fields' => 'ids' , 'cache_results' => false ) ;
                $OrderId = get_posts( $args ) ;
            } else {
                $args    = array( 'post_type' => 'shop_order' , 'numberposts' => '-1' , 'meta_query' => array( array( 'key' => 'reward_points_awarded' , 'compare' => 'EXISTS' ) ) , 'post_status' => $OrderStatusList , 'fields' => 'ids' , 'cache_results' => false ) ;
                $OrderId = get_posts( $args ) ;
            }
            $SlicedArray = array_slice( $OrderId , $offset , $limit ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                foreach ( $SlicedArray as $id ) {
                    self::$update_previous_order_points->push_to_queue( $id ) ;
                }
            } else {
                self::$update_previous_order_points->push_to_queue( 'no_orders' ) ;
            }
            update_option( 'rs_applied_previous_order_background_updater_offset' , $limit + $offset ) ;

            if ( $offset == 0 )
                FP_WooCommerce_Log::log( 'Process to apply points for previous order(s) Started' ) ;

            self::$rs_progress_bar->fp_increase_progress( 50 ) ;
            self::$update_previous_order_points->save()->dispatch() ;
        }

        /* Initializing Background Process for Add Points */

        public static function set_transient_for_add_points() {
            check_ajax_referer( 'fp-add-points' , 'sumo_security' ) ;

            try {
                $values = array(
                    'usertype'    => isset( $_POST[ 'usertype' ] ) ? $_POST[ 'usertype' ] : '' ,
                    'incuser'     => isset( $_POST[ 'includeuser' ] ) ? $_POST[ 'includeuser' ] : '' ,
                    'excuser'     => isset( $_POST[ 'excludeuser' ] ) ? $_POST[ 'excludeuser' ] : '' ,
                    'incuserrole' => isset( $_POST[ 'includeuserrole' ] ) ? $_POST[ 'includeuserrole' ] : '' ,
                    'excuserrole' => isset( $_POST[ 'excludeuserrole' ] ) ? $_POST[ 'excludeuserrole' ] : '' ,
                    'enablemail'  => isset( $_POST[ 'sendmail_to_add_points' ] ) ? $_POST[ 'sendmail_to_add_points' ] : 'no' ,
                    'subject'     => isset( $_POST[ 'email_subject_to_add_points' ] ) ? $_POST[ 'email_subject_to_add_points' ] : '' ,
                    'message'     => isset( $_POST[ 'email_message_to_add_points' ] ) ? $_POST[ 'email_message_to_add_points' ] : '' ,
                    'points'      => isset( $_POST[ 'points' ] ) ? $_POST[ 'points' ] : 0 ,
                    'reason'      => isset( $_POST[ 'reason' ] ) ? $_POST[ 'reason' ] : '' ,
                    'state'       => isset( $_POST[ 'state' ] ) ? $_POST[ 'state' ] : 'no' ,
                    'expdate'     => isset( $_POST[ 'expireddate' ] ) ? $_POST[ 'expireddate' ] : ''
                        ) ;

                $current_user_id = get_current_user_id() ;
                set_transient( 'fp_background_process_transient_for_add_points_' . $current_user_id , $values , 30 ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function add_points_for_user() {
            $current_user_id = get_current_user_id() ;
            if ( ! get_transient( 'fp_background_process_transient_for_add_points_' . $current_user_id ) )
                return ;

            $data         = get_transient( 'fp_background_process_transient_for_add_points_' . $current_user_id ) ;
            $UserId       = self::get_user_ids( $data ) ;
            update_user_meta( $current_user_id , 'selected_user' , $UserId ) ;
            update_user_meta( $current_user_id , 'selected_options' , $data ) ;
            delete_transient( 'fp_background_process_transient_for_add_points_' . $current_user_id ) ;
            delete_user_meta( $current_user_id , 'rs_add_points_background_updater_offset' ) ;
            self::callback_to_add_points_for_user( $UserId ) ;
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_add_points' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            wp_safe_redirect( $redirect_url ) ;
        }

        public static function callback_to_add_points_for_user( $UserId , $offset = 0 , $limit = 1000 ) {
            $SlicedArray = array_slice( $UserId , $offset , $limit ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                foreach ( $SlicedArray as $id ) {
                    self::$add_points_for_user->push_to_queue( $id ) ;
                }
            } else {
                self::$add_points_for_user->push_to_queue( 'no_users' ) ;
            }

            $user_id = self::$add_points_for_user->get_user_id() ;

            update_user_meta( $user_id , 'rs_add_points_background_updater_offset' , $limit + $offset ) ;

            if ( $offset == 0 )
                FP_WooCommerce_Log::log( 'Process to Add Points for User(s) Started' ) ;

            self::$rs_progress_bar->fp_increase_progress( 50 ) ;
            self::$add_points_for_user->save()->dispatch() ;
        }

        /* Search User and User Role Filter Options get User Ids */

        public static function get_user_ids( $data ) {
            if ( empty( $data[ 'usertype' ] ) )
                return array() ;

            $args = array() ;
            switch ( $data[ 'usertype' ] ) {

                case '2':
                    $args = array(
                        'include' => srp_check_is_array( $data[ 'incuser' ] ) ? $data[ 'incuser' ] : explode( ',' , $data[ 'incuser' ] ) ,
                            ) ;
                    break ;
                case '3':
                    $args = array(
                        'exclude' => srp_check_is_array( $data[ 'excuser' ] ) ? $data[ 'excuser' ] : explode( ',' , $data[ 'excuser' ] ) ,
                            ) ;
                    break ;
                case '4':
                    $args = array(
                        'role__in' => srp_check_is_array( $data[ 'incuserrole' ] ) ? $data[ 'incuserrole' ] : explode( ',' , $data[ 'incuserrole' ] ) ,
                            ) ;
                    break ;
                case '5':
                    $args = array(
                        'role__not_in' => srp_check_is_array( $data[ 'excuserrole' ] ) ? $data[ 'excuserrole' ] : explode( ',' , $data[ 'excuserrole' ] ) ,
                            ) ;
                    break ;
            }

            $args = array_merge( array( 'fields' => 'ids' ) , $args ) ;
            return get_users( $args ) ;
        }

        /* Initializing Background Process for Remove Points */

        public static function set_transient_for_remove_points() {
            check_ajax_referer( 'fp-remove-points' , 'sumo_security' ) ;

            try {
                $values = array(
                    'usertype'    => isset( $_POST[ 'usertype' ] ) ? $_POST[ 'usertype' ] : '' ,
                    'incuser'     => isset( $_POST[ 'includeuser' ] ) ? $_POST[ 'includeuser' ] : '' ,
                    'excuser'     => isset( $_POST[ 'excludeuser' ] ) ? $_POST[ 'excludeuser' ] : '' ,
                    'incuserrole' => isset( $_POST[ 'includeuserrole' ] ) ? $_POST[ 'includeuserrole' ] : '' ,
                    'excuserrole' => isset( $_POST[ 'excludeuserrole' ] ) ? $_POST[ 'excludeuserrole' ] : '' ,
                    'enablemail'  => isset( $_POST[ 'sendmail_to_remove_points' ] ) ? $_POST[ 'sendmail_to_remove_points' ] : 'no' ,
                    'subject'     => isset( $_POST[ 'email_subject_to_remove_points' ] ) ? $_POST[ 'email_subject_to_remove_points' ] : '' ,
                    'message'     => isset( $_POST[ 'email_message_to_remove_points' ] ) ? $_POST[ 'email_message_to_remove_points' ] : '' ,
                    'points'      => isset( $_POST[ 'points' ] ) ? $_POST[ 'points' ] : 0 ,
                    'reason'      => isset( $_POST[ 'reason' ] ) ? $_POST[ 'reason' ] : '' ,
                    'state'       => isset( $_POST[ 'state' ] ) ? $_POST[ 'state' ] : 'no' ,
                        ) ;

                $current_user_id = get_current_user_id() ;
                set_transient( 'fp_background_process_transient_for_remove_points_' . $current_user_id , $values , 30 ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function remove_points_for_user() {
            $current_user_id = get_current_user_id() ;
            if ( ! get_transient( 'fp_background_process_transient_for_remove_points_' . $current_user_id ) )
                return ;

            $data         = get_transient( 'fp_background_process_transient_for_remove_points_' . $current_user_id ) ;
            $UserId       = self::get_user_ids( $data ) ;
            update_user_meta( $current_user_id , 'selected_user' , $UserId ) ;
            update_user_meta( $current_user_id , 'selected_options' , $data ) ;
            delete_transient( 'fp_background_process_transient_for_remove_points_' . $current_user_id ) ;
            delete_user_meta( $current_user_id , 'rs_remove_points_background_updater_offset' ) ;
            self::callback_to_remove_points_for_user( $UserId ) ;
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_remove_points' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            wp_safe_redirect( $redirect_url ) ;
        }

        public static function callback_to_remove_points_for_user( $UserId , $offset = 0 , $limit = 1000 ) {
            $SlicedArray = array_slice( $UserId , $offset , $limit ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                foreach ( $SlicedArray as $id ) {
                    self::$remove_points_for_user->push_to_queue( $id ) ;
                }
            } else {
                self::$remove_points_for_user->push_to_queue( 'no_users' ) ;
            }

            $user_id = self::$add_points_for_user->get_user_id() ;

            update_user_meta( $user_id , 'rs_remove_points_background_updater_offset' , $limit + $offset ) ;

            if ( $offset == 0 )
                FP_WooCommerce_Log::log( 'Process to Remove Points for User(s) Started' ) ;

            self::$rs_progress_bar->fp_increase_progress( 50 ) ;
            self::$remove_points_for_user->save()->dispatch() ;
        }

        /* Initializing Background Process for Refresh Points */

        public static function set_transient_for_refresh_points() {
            check_ajax_referer( 'fp-refresh-points' , 'sumo_security' ) ;

            try {
                set_transient( 'fp_background_process_transient_for_refresh_points' , true , 30 ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function update_expired_points_for_user() {
            if ( ! get_transient( 'fp_background_process_transient_for_refresh_points' ) )
                return ;

            delete_transient( 'fp_background_process_transient_for_refresh_points' ) ;
            delete_option( 'rs_refresh_points_background_updater_offset' ) ;
            self::callback_to_refresh_points_for_user() ;
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_refresh_points' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            wp_safe_redirect( $redirect_url ) ;
        }

        public static function callback_to_refresh_points_for_user( $offset = 0 , $limit = 1000 ) {
            $args        = array( 'fields' => 'ID' ) ;
            $UserId      = get_users( $args ) ;
            $SlicedArray = array_slice( $UserId , $offset , $limit ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                foreach ( $SlicedArray as $id ) {
                    self::$refresh_points_for_user->push_to_queue( $id ) ;
                }
            } else {
                self::$refresh_points_for_user->push_to_queue( 'no_users' ) ;
            }
            update_option( 'rs_refresh_points_background_updater_offset' , $limit + $offset ) ;

            if ( $offset == 0 )
                FP_WooCommerce_Log::log( 'Process to Refresh Expired Points for User(s) Started' ) ;

            self::$rs_progress_bar->fp_increase_progress( 50 ) ;
            self::$refresh_points_for_user->save()->dispatch() ;
        }

        /* Initializing Background Process for Export Points */

        public static function set_transient_for_export_points() {
            check_ajax_referer( 'fp-export-points' , 'sumo_security' ) ;

            try {
                delete_option( 'rs_data_to_impexp' ) ;
                update_option( 'rs_data_to_impexp' , array() ) ;
                if ( isset( $_POST[ 'usertype' ] ) )
                    update_option( 'rs_user_selection_type_to_export_points' , $_POST[ 'usertype' ] ) ;

                if ( isset( $_POST[ 'selecteduser' ] ) )
                    update_option( 'rs_selected_user_to_export_points' , $_POST[ 'selecteduser' ] ) ;

                set_transient( 'fp_background_process_transient_for_export_points' , true , 30 ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function export_points_for_user() {
            if ( ! get_transient( 'fp_background_process_transient_for_export_points' ) )
                return ;

            delete_transient( 'fp_background_process_transient_for_export_points' ) ;
            delete_option( 'rs_export_points_background_updater_offset' ) ;
            self::callback_to_export_points_for_user() ;
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_export_points' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            wp_safe_redirect( $redirect_url ) ;
        }

        public static function callback_to_export_points_for_user( $offset = 0 , $limit = 1000 ) {
            $UserSelectionType = get_option( 'rs_user_selection_type_to_export_points' ) ;
            $Selecteduser      = get_option( 'rs_selected_user_to_export_points' ) ;
            if ( $UserSelectionType == '1' ) {
                $args   = array( 'fields' => 'ID' ) ;
                $UserId = get_users( $args ) ;
            } else if ( $UserSelectionType == '2' ) {
                $UserId = is_array( $Selecteduser ) ? $Selecteduser : explode( ',' , $Selecteduser ) ;
            }
            $SlicedArray = array_slice( $UserId , $offset , 1000 ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                foreach ( $SlicedArray as $id ) {
                    self::$export_points_for_user->push_to_queue( $id ) ;
                }
            } else {
                self::$export_points_for_user->push_to_queue( 'no_users' ) ;
            }
            update_option( 'rs_export_points_background_updater_offset' , $limit + $offset ) ;

            if ( $offset == 0 )
                FP_WooCommerce_Log::log( 'Process to Export Points for User(s) Started' ) ;

            self::$rs_progress_bar->fp_increase_progress( 50 ) ;
            self::$export_points_for_user->save()->dispatch() ;
        }

        /* Initializing Background Process for Export Report */

        public static function set_transient_for_export_report() {
            check_ajax_referer( 'fp-export-report' , 'sumo_security' ) ;

            try {
                delete_option( 'rs_export_report' ) ;
                delete_option( 'heading' ) ;
                update_option( 'rs_export_report' , array() ) ;
                if ( isset( $_POST[ 'usertype' ] ) )
                    update_option( 'fp_user_selection_type' , $_POST[ 'usertype' ] ) ;

                if ( isset( $_POST[ 'selecteduser' ] ) )
                    update_option( 'fp_selected_users' , $_POST[ 'selecteduser' ] ) ;

                set_transient( 'fp_background_process_transient_for_export_report' , true , 30 ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function export_report_for_user() {
            if ( ! get_transient( 'fp_background_process_transient_for_export_report' ) )
                return ;

            delete_transient( 'fp_background_process_transient_for_export_report' ) ;
            delete_option( 'rs_export_report_background_updater_offset' ) ;
            self::callback_to_export_report_for_user() ;
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_export_report' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            wp_safe_redirect( $redirect_url ) ;
        }

        public static function callback_to_export_report_for_user( $offset = 0 , $limit = 1000 ) {
            $UserSelectionType = get_option( 'fp_user_selection_type' ) ;
            $Selecteduser      = get_option( 'fp_selected_users' ) ;
            if ( $UserSelectionType == '1' ) {
                $args   = array( 'fields' => 'ID' ) ;
                $UserId = get_users( $args ) ;
            } else if ( $UserSelectionType == '2' ) {
                $UserId = srp_check_is_array( $Selecteduser ) ? $Selecteduser : explode( ',' , $Selecteduser ) ;
            }
            $SlicedArray = array_slice( $UserId , $offset , 1000 ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                foreach ( $SlicedArray as $id ) {
                    self::$export_report_for_user->push_to_queue( $id ) ;
                }
            } else {
                self::$export_report_for_user->push_to_queue( 'no_users' ) ;
            }
            update_option( 'rs_export_report_background_updater_offset' , $limit + $offset ) ;

            if ( $offset == 0 )
                FP_WooCommerce_Log::log( 'Process to Export Report for User(s) Started' ) ;

            self::$rs_progress_bar->fp_increase_progress( 50 ) ;
            self::$export_report_for_user->save()->dispatch() ;
        }

        /* Initializing Background Process for Adding Old Points */

        public static function set_transient_for_old_points() {
            check_ajax_referer( 'fp-old-points' , 'sumo_security' ) ;

            try {
                set_transient( 'fp_background_process_transient_for_old_points' , true , 30 ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function add_existing_points_for_user() {
            if ( ! get_transient( 'fp_background_process_transient_for_old_points' ) )
                return ;

            delete_transient( 'fp_background_process_transient_for_old_points' ) ;
            delete_option( 'rs_old_points_background_updater_offset' ) ;
            self::callback_to_export_points_for_user() ;
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_old_points' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            wp_safe_redirect( $redirect_url ) ;
        }

        public static function callback_to_add_existing_points_for_user( $offset = 0 , $limit = 1000 ) {
            $args        = array(
                'fields'       => 'ID' ,
                'meta_key'     => '_my_reward_points' ,
                'meta_value'   => '' ,
                'meta_compare' => '!='
                    ) ;
            $UserId      = get_users( $args ) ;
            $SlicedArray = array_slice( $UserId , $offset , 1000 ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                foreach ( $SlicedArray as $id ) {
                    self::$add_existing_points_for_user->push_to_queue( $id ) ;
                }
            } else {
                self::$add_existing_points_for_user->push_to_queue( 'no_users' ) ;
            }
            update_option( 'rs_old_points_background_updater_offset' , $limit + $offset ) ;

            if ( $offset == 0 )
                FP_WooCommerce_Log::log( 'Process to Add Existing Points for User(s) Started' ) ;

            self::$rs_progress_bar->fp_increase_progress( 50 ) ;
            self::$add_existing_points_for_user->save()->dispatch() ;
        }

        /* Initializing Background Process for Bulk Update - Product Purchase */

        public static function set_transient_for_bulk_update() {
            check_ajax_referer( 'product-purchase-bulk-update' , 'sumo_security' ) ;

            try {
                if ( isset( $_POST[ 'productselection' ] ) ) {
                    update_option( 'fp_product_selection_type' , $_POST[ 'productselection' ] ) ;
                    update_option( 'rs_which_product_selection' , $_POST[ 'productselection' ] ) ;
                }

                if ( isset( $_POST[ 'enablereward' ] ) ) {
                    update_option( 'fp_enable_reward' , $_POST[ 'enablereward' ] ) ;
                    update_option( 'rs_local_enable_disable_reward' , $_POST[ 'enablereward' ] ) ;
                }

                if ( isset( $_POST[ 'selectedproducts' ] ) ) {
                    update_option( 'fp_selected_products' , $_POST[ 'selectedproducts' ] ) ;
                    update_option( 'rs_select_particular_products' , $_POST[ 'selectedproducts' ] ) ;
                }

                if ( isset( $_POST[ 'selectedcategories' ] ) ) {
                    update_option( 'fp_selected_categories' , $_POST[ 'selectedcategories' ] ) ;
                    update_option( 'rs_select_particular_categories' , $_POST[ 'selectedcategories' ] ) ;
                }

                if ( isset( $_POST[ 'rewardtype' ] ) ) {
                    update_option( 'fp_reward_type' , $_POST[ 'rewardtype' ] ) ;
                    update_option( 'rs_local_reward_type' , $_POST[ 'rewardtype' ] ) ;
                }

                if ( isset( $_POST[ 'rewardpoints' ] ) ) {
                    update_option( 'fp_reward_points' , $_POST[ 'rewardpoints' ] ) ;
                    update_option( 'rs_local_reward_points' , $_POST[ 'rewardpoints' ] ) ;
                }

                if ( isset( $_POST[ 'rewardpercent' ] ) ) {
                    update_option( 'fp_reward_percent' , $_POST[ 'rewardpercent' ] ) ;
                    update_option( 'rs_local_reward_percent' , $_POST[ 'rewardpercent' ] ) ;
                }

                if ( isset( $_POST[ 'enablereferralreward' ] ) ) {
                    update_option( 'fp_enable_referral_reward' , $_POST[ 'enablereferralreward' ] ) ;
                    update_option( 'rs_local_enable_disable_referral_reward' , $_POST[ 'enablereferralreward' ] ) ;
                }

                if ( isset( $_POST[ 'referralrewardtype' ] ) ) {
                    update_option( 'fp_referral_reward_type' , $_POST[ 'referralrewardtype' ] ) ;
                    update_option( 'rs_local_referral_reward_type' , $_POST[ 'referralrewardtype' ] ) ;
                }

                if ( isset( $_POST[ 'referralrewardpoint' ] ) ) {
                    update_option( 'fp_referral_reward_points' , $_POST[ 'referralrewardpoint' ] ) ;
                    update_option( 'rs_local_referral_reward_point' , $_POST[ 'referralrewardpoint' ] ) ;
                }

                if ( isset( $_POST[ 'referralrewardpercent' ] ) ) {
                    update_option( 'fp_referral_reward_percent' , $_POST[ 'referralrewardpercent' ] ) ;
                    update_option( 'rs_local_referral_reward_percent' , $_POST[ 'referralrewardpercent' ] ) ;
                }

                if ( isset( $_POST[ 'referralrewardtypegettingrefer' ] ) ) {
                    update_option( 'fp_referral_reward_type_for_gettingrefer' , $_POST[ 'referralrewardtypegettingrefer' ] ) ;
                    update_option( 'rs_local_referral_reward_type_get_refer' , $_POST[ 'referralrewardtypegettingrefer' ] ) ;
                }

                if ( isset( $_POST[ 'referralpointforgettingrefer' ] ) ) {
                    update_option( 'fp_referral_reward_points_for_gettingrefer' , $_POST[ 'referralpointforgettingrefer' ] ) ;
                    update_option( 'rs_local_referral_reward_point_for_getting_referred' , $_POST[ 'referralpointforgettingrefer' ] ) ;
                }

                if ( isset( $_POST[ 'referralrewardpercentgettingrefer' ] ) ) {
                    update_option( 'fp_referral_reward_percent_for_gettingrefer' , $_POST[ 'referralrewardpercentgettingrefer' ] ) ;
                    update_option( 'rs_local_referral_reward_percent_for_getting_referred' , $_POST[ 'referralrewardpercentgettingrefer' ] ) ;
                }

                set_transient( 'fp_background_process_transient_for_bulk_update' , true , 30 ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function bulk_update_for_purchase() {
            if ( ! get_transient( 'fp_background_process_transient_for_bulk_update' ) )
                return ;

            delete_transient( 'fp_background_process_transient_for_bulk_update' ) ;
            delete_option( 'fp_bulk_update_points_for_product' ) ;
            self::callback_to_update_points_for_product() ;
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_bulk_update' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            wp_safe_redirect( $redirect_url ) ;
        }

        public static function callback_to_update_points_for_product( $offset = 0 , $limit = 1000 ) {
            if ( get_option( 'fp_product_selection_type' ) == 2 ) {
                $ProductIds = srp_check_is_array( get_option( 'fp_selected_products' ) ) ? get_option( 'fp_selected_products' ) : explode( ',' , get_option( 'fp_selected_products' ) ) ;
            } else {
                $args       = array( 'post_type' => 'product' , 'posts_per_page' => '-1' , 'post_status' => 'publish' , 'fields' => 'ids' , 'cache_results' => false ) ;
                $ProductIds = get_posts( $args ) ;
            }
            $SlicedArray = array_slice( $ProductIds , $offset , 1000 ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                foreach ( $SlicedArray as $id ) {
                    self::$update_points_for_product->push_to_queue( $id ) ;
                }
            } else {
                self::$update_points_for_product->push_to_queue( 'no_products' ) ;
            }
            update_option( 'fp_bulk_update_points_for_product' , $limit + $offset ) ;

            if ( $offset == 0 )
                FP_WooCommerce_Log::log( 'Process to Update Points for Product(s) Started' ) ;

            self::$rs_progress_bar->fp_increase_progress( 50 ) ;
            self::$update_points_for_product->save()->dispatch() ;
        }

        /* Initializing Background Process for Bulk Update - Social Reward */

        public static function set_transient_for_bulk_update_of_social_reward() {
            check_ajax_referer( 'social-reward-bulk-update' , 'sumo_security' ) ;

            try {
                if ( isset( $_POST[ 'productselection' ] ) ) {
                    update_option( 'fp_product_selection_type' , $_POST[ 'productselection' ] ) ;
                    update_option( 'rs_which_social_product_selection' , $_POST[ 'productselection' ] ) ;
                }

                if ( isset( $_POST[ 'enablereward' ] ) ) {
                    update_option( 'fp_enable_reward' , $_POST[ 'enablereward' ] ) ;
                    update_option( 'rs_local_enable_disable_social_reward' , $_POST[ 'enablereward' ] ) ;
                }

                if ( isset( $_POST[ 'selectedproducts' ] ) ) {
                    update_option( 'fp_selected_products' , $_POST[ 'selectedproducts' ] ) ;
                    update_option( 'rs_select_particular_social_products' , $_POST[ 'selectedproducts' ] ) ;
                }

                if ( isset( $_POST[ 'selectedcategories' ] ) ) {
                    update_option( 'fp_selected_category' , $_POST[ 'selectedcategories' ] ) ;
                    update_option( 'rs_select_particular_social_categories' , $_POST[ 'selectedcategories' ] ) ;
                }

                if ( isset( $_POST[ 'fblikerewardtype' ] ) ) {
                    update_option( 'fp_fblike_reward_type' , $_POST[ 'fblikerewardtype' ] ) ;
                    update_option( 'rs_local_reward_type_for_facebook' , $_POST[ 'fblikerewardtype' ] ) ;
                }

                if ( isset( $_POST[ 'fblikerewardpoints' ] ) ) {
                    update_option( 'fp_fblike_reward_points' , $_POST[ 'fblikerewardpoints' ] ) ;
                    update_option( 'rs_local_reward_points_facebook' , $_POST[ 'fblikerewardpoints' ] ) ;
                }

                if ( isset( $_POST[ 'fblikerewardpercent' ] ) ) {
                    update_option( 'fp_fblike_reward_percent' , $_POST[ 'fblikerewardpercent' ] ) ;
                    update_option( 'rs_local_reward_percent_facebook' , $_POST[ 'fblikerewardpercent' ] ) ;
                }

                if ( isset( $_POST[ 'fbsharerewardtype' ] ) ) {
                    update_option( 'fp_fbshare_reward_type' , $_POST[ 'fbsharerewardtype' ] ) ;
                    update_option( 'rs_local_reward_type_for_facebook_share' , $_POST[ 'fbsharerewardtype' ] ) ;
                }

                if ( isset( $_POST[ 'fbsharerewardpoints' ] ) ) {
                    update_option( 'fp_fbshare_reward_points' , $_POST[ 'fbsharerewardpoints' ] ) ;
                    update_option( 'rs_local_reward_points_facebook_share' , $_POST[ 'fbsharerewardpoints' ] ) ;
                }

                if ( isset( $_POST[ 'fbsharerewardpercent' ] ) ) {
                    update_option( 'fp_fbshare_reward_percent' , $_POST[ 'fbsharerewardpercent' ] ) ;
                    update_option( 'rs_local_reward_percent_facebook_share' , $_POST[ 'fbsharerewardpercent' ] ) ;
                }

                if ( isset( $_POST[ 'twitterrewardtype' ] ) ) {
                    update_option( 'fp_twitter_reward_type' , $_POST[ 'twitterrewardtype' ] ) ;
                    update_option( 'rs_local_reward_type_for_twitter' , $_POST[ 'twitterrewardtype' ] ) ;
                }

                if ( isset( $_POST[ 'twitterrewardpoints' ] ) ) {
                    update_option( 'fp_twitter_reward_points' , $_POST[ 'twitterrewardpoints' ] ) ;
                    update_option( 'rs_local_reward_points_twitter' , $_POST[ 'twitterrewardpoints' ] ) ;
                }

                if ( isset( $_POST[ 'twitterrewardpercent' ] ) ) {
                    update_option( 'fp_twitter_reward_percent' , $_POST[ 'twitterrewardpercent' ] ) ;
                    update_option( 'rs_local_reward_percent_twitter' , $_POST[ 'twitterrewardpercent' ] ) ;
                }

                if ( isset( $_POST[ 'gplusrewardtype' ] ) ) {
                    update_option( 'fp_glpus_reward_type' , $_POST[ 'gplusrewardtype' ] ) ;
                    update_option( 'rs_local_reward_type_for_google' , $_POST[ 'gplusrewardtype' ] ) ;
                }

                if ( isset( $_POST[ 'gplusrewardpoints' ] ) ) {
                    update_option( 'fp_glpus_reward_points' , $_POST[ 'gplusrewardpoints' ] ) ;
                    update_option( 'rs_local_reward_points_google' , $_POST[ 'gplusrewardpoints' ] ) ;
                }

                if ( isset( $_POST[ 'gplusrewardpercent' ] ) ) {
                    update_option( 'fp_glpus_reward_percent' , $_POST[ 'gplusrewardpercent' ] ) ;
                    update_option( 'rs_local_reward_percent_google' , $_POST[ 'gplusrewardpercent' ] ) ;
                }

                if ( isset( $_POST[ 'vkrewardtype' ] ) ) {
                    update_option( 'fp_vk_reward_type' , $_POST[ 'vkrewardtype' ] ) ;
                    update_option( 'rs_local_reward_type_for_vk' , $_POST[ 'vkrewardtype' ] ) ;
                }

                if ( isset( $_POST[ 'vkrewardpoints' ] ) ) {
                    update_option( 'fp_vk_reward_points' , $_POST[ 'vkrewardpoints' ] ) ;
                    update_option( 'rs_local_reward_points_vk' , $_POST[ 'vkrewardpoints' ] ) ;
                }

                if ( isset( $_POST[ 'vkrewardpercent' ] ) ) {
                    update_option( 'fp_vk_reward_percent' , $_POST[ 'vkrewardpercent' ] ) ;
                    update_option( 'rs_local_reward_percent_vk' , $_POST[ 'vkrewardpercent' ] ) ;
                }

                if ( isset( $_POST[ 'twitterfollowrewardtype' ] ) ) {
                    update_option( 'fp_twitter_follow_reward_type' , $_POST[ 'twitterfollowrewardtype' ] ) ;
                    update_option( 'rs_local_reward_type_for_twitter_follow' , $_POST[ 'twitterfollowrewardtype' ] ) ;
                }

                if ( isset( $_POST[ 'twitterfollowrewardpoints' ] ) ) {
                    update_option( 'fp_twitter_follow_reward_points' , $_POST[ 'twitterfollowrewardpoints' ] ) ;
                    update_option( 'rs_local_reward_points_twitter_follow' , $_POST[ 'twitterfollowrewardpoints' ] ) ;
                }

                if ( isset( $_POST[ 'twitterfollowrewardpercent' ] ) ) {
                    update_option( 'fp_twitter_follow_reward_percent' , $_POST[ 'twitterfollowrewardpercent' ] ) ;
                    update_option( 'rs_local_reward_percent_twitter_follow' , $_POST[ 'twitterfollowrewardpercent' ] ) ;
                }

                if ( isset( $_POST[ 'instagramrewardtype' ] ) ) {
                    update_option( 'fp_instagram_reward_type' , $_POST[ 'instagramrewardtype' ] ) ;
                    update_option( 'rs_local_reward_type_for_instagram' , $_POST[ 'instagramrewardtype' ] ) ;
                }

                if ( isset( $_POST[ 'instagramrewardpoints' ] ) ) {
                    update_option( 'fp_instagram_reward_points' , $_POST[ 'instagramrewardpoints' ] ) ;
                    update_option( 'rs_local_reward_points_instagram' , $_POST[ 'instagramrewardpoints' ] ) ;
                }

                if ( isset( $_POST[ 'instagramrewardpercent' ] ) ) {
                    update_option( 'fp_instagram_reward_percent' , $_POST[ 'instagramrewardpercent' ] ) ;
                    update_option( 'rs_local_reward_percent_instagram' , $_POST[ 'instagramrewardpercent' ] ) ;
                }

                if ( isset( $_POST[ 'okrewardtype' ] ) ) {
                    update_option( 'fp_ok_reward_type' , $_POST[ 'okrewardtype' ] ) ;
                    update_option( 'rs_local_reward_type_for_ok_follow' , $_POST[ 'okrewardtype' ] ) ;
                }

                if ( isset( $_POST[ 'okrewardpoints' ] ) ) {
                    update_option( 'fp_ok_reward_points' , $_POST[ 'okrewardpoints' ] ) ;
                    update_option( 'rs_local_reward_points_ok_follow' , $_POST[ 'okrewardpoints' ] ) ;
                }

                if ( isset( $_POST[ 'okrewardpercent' ] ) ) {
                    update_option( 'fp_ok_reward_percent' , $_POST[ 'okrewardpercent' ] ) ;
                    update_option( 'rs_local_reward_percent_ok_follow' , $_POST[ 'okrewardpercent' ] ) ;
                }

                set_transient( 'fp_transient_for_bulk_update_of_social_reward' , true , 30 ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function bulk_update_for_social_reward() {
            if ( ! get_transient( 'fp_transient_for_bulk_update_of_social_reward' ) )
                return ;

            delete_transient( 'fp_transient_for_bulk_update_of_social_reward' ) ;
            delete_option( 'fp_bulk_update_points_for_social_reward' ) ;
            self::callback_to_update_points_for_social_reward() ;
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bulk_update_for_social_reward' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            wp_safe_redirect( $redirect_url ) ;
        }

        public static function callback_to_update_points_for_social_reward( $offset = 0 , $limit = 1000 ) {
            if ( get_option( 'fp_product_selection_type' ) == 2 ) {
                $ProductIds = srp_check_is_array( get_option( 'fp_selected_products' ) ) ? get_option( 'fp_selected_products' ) : explode( ',' , get_option( 'fp_selected_products' ) ) ;
            } else {
                $args       = array( 'post_type' => 'product' , 'posts_per_page' => '-1' , 'post_status' => 'publish' , 'fields' => 'ids' , 'cache_results' => false ) ;
                $ProductIds = get_posts( $args ) ;
            }
            $SlicedArray = array_slice( $ProductIds , $offset , 1000 ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                foreach ( $SlicedArray as $id ) {
                    self::$update_points_for_social_reward->push_to_queue( $id ) ;
                }
            } else {
                self::$update_points_for_social_reward->push_to_queue( 'no_products' ) ;
            }
            update_option( 'fp_bulk_update_points_for_social_reward' , $limit + $offset ) ;

            if ( $offset == 0 )
                FP_WooCommerce_Log::log( 'Process to Update Points for Social Reward Started' ) ;

            self::$rs_progress_bar->fp_increase_progress( 50 ) ;
            self::$update_points_for_social_reward->save()->dispatch() ;
        }

        /* Initializing Background Process for Bulk Update - Buying Points */

        public static function set_transient_for_bulk_update_of_buying_points() {
            check_ajax_referer( 'buying-reward-bulk-update' , 'sumo_security' ) ;

            try {
                if ( isset( $_POST[ 'applicableproduct' ] ) ) {
                    update_option( 'fp_product_selection_type' , $_POST[ 'applicableproduct' ] ) ;
                    update_option( 'rs_buying_points_is_applicable' , $_POST[ 'applicableproduct' ] ) ;
                }

                if ( isset( $_POST[ 'enablebuyingpoint' ] ) ) {
                    update_option( 'fp_enable_buying_point' , $_POST[ 'enablebuyingpoint' ] ) ;
                    update_option( 'rs_enable_buying_points' , $_POST[ 'enablebuyingpoint' ] ) ;
                }

                if ( isset( $_POST[ 'buyingpoint' ] ) ) {
                    update_option( 'fp_buying_point' , $_POST[ 'buyingpoint' ] ) ;
                    update_option( 'rs_points_for_buying_points' , $_POST[ 'buyingpoint' ] ) ;
                }

                if ( isset( $_POST[ 'includeproducts' ] ) ) {
                    update_option( 'fp_include_products' , $_POST[ 'includeproducts' ] ) ;
                    update_option( 'rs_include_products_for_buying_points' , $_POST[ 'includeproducts' ] ) ;
                }

                if ( isset( $_POST[ 'excludeproducts' ] ) ) {
                    update_option( 'fp_exclude_products' , $_POST[ 'excludeproducts' ] ) ;
                    update_option( 'rs_exclude_products_for_buying_points' , $_POST[ 'excludeproducts' ] ) ;
                }

                set_transient( 'fp_background_process_transient_for_bulk_update_of_buying_points' , true , 30 ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function bulk_update_for_buying_points() {
            if ( ! get_transient( 'fp_background_process_transient_for_bulk_update_of_buying_points' ) )
                return ;

            delete_transient( 'fp_background_process_transient_for_bulk_update_of_buying_points' ) ;
            delete_option( 'fp_bulk_update_buying_points_for_product' ) ;
            self::callback_to_update_buying_points_for_product() ;
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_buying_points_bulk_update' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            wp_safe_redirect( $redirect_url ) ;
        }

        public static function callback_to_update_buying_points_for_product( $offset = 0 , $limit = 1000 ) {
            if ( get_option( 'fp_product_selection_type' ) == 1 ) {
                $args       = array( 'post_type' => 'product' , 'posts_per_page' => '-1' , 'post_status' => 'publish' , 'fields' => 'ids' , 'cache_results' => false ) ;
                $ProductIds = get_posts( $args ) ;
            } elseif ( get_option( 'fp_product_selection_type' ) == 2 ) {
                $Ids        = srp_check_is_array( get_option( 'fp_include_products' ) ) ? get_option( 'fp_include_products' ) : explode( ',' , get_option( 'fp_include_products' ) ) ;
                $args       = array( 'post_type' => 'product' , 'posts_per_page' => '-1' , 'post_status' => 'publish' , 'include' => $Ids , 'fields' => 'ids' , 'cache_results' => false ) ;
                $ProductIds = get_posts( $args ) ;
            } else {
                $Ids        = srp_check_is_array( get_option( 'fp_exclude_products' ) ) ? get_option( 'fp_exclude_products' ) : explode( ',' , get_option( 'fp_exclude_products' ) ) ;
                $args       = array( 'post_type' => 'product' , 'posts_per_page' => '-1' , 'post_status' => 'publish' , 'exclude' => $Ids , 'fields' => 'ids' , 'cache_results' => false ) ;
                $ProductIds = get_posts( $args ) ;
            }
            $SlicedArray = array_slice( $ProductIds , $offset , 1000 ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                foreach ( $SlicedArray as $id ) {
                    self::$update_buying_points_for_product->push_to_queue( $id ) ;
                }
            } else {
                self::$update_buying_points_for_product->push_to_queue( 'no_products' ) ;
            }
            update_option( 'fp_bulk_update_buying_points_for_product' , $limit + $offset ) ;

            if ( $offset == 0 )
                FP_WooCommerce_Log::log( 'Process to Update Buying Points for Product(s) Started' ) ;

            self::$rs_progress_bar->fp_increase_progress( 50 ) ;
            self::$update_buying_points_for_product->save()->dispatch() ;
        }

        /* Initializing Background Process for Bulk Update - Point Price */

        public static function set_transient_for_bulk_update_of_point_price() {
            check_ajax_referer( 'points-price-bulk-update' , 'sumo_security' ) ;

            try {
                if ( isset( $_POST[ 'productselection' ] ) ) {
                    update_option( 'fp_product_selection_type' , $_POST[ 'productselection' ] ) ;
                    update_option( 'rs_which_point_precing_product_selection' , $_POST[ 'productselection' ] ) ;
                }

                if ( isset( $_POST[ 'enablepointprice' ] ) ) {
                    update_option( 'fp_enable_point_price' , $_POST[ 'enablepointprice' ] ) ;
                    update_option( 'rs_local_enable_disable_point_price' , $_POST[ 'enablepointprice' ] ) ;
                }

                if ( isset( $_POST[ 'selectedproducts' ] ) ) {
                    update_option( 'fp_selected_products' , $_POST[ 'selectedproducts' ] ) ;
                    update_option( 'rs_select_particular_products_for_point_price' , $_POST[ 'selectedproducts' ] ) ;
                }

                if ( isset( $_POST[ 'selectedcategories' ] ) ) {
                    update_option( 'fp_selected_categories' , $_POST[ 'selectedcategories' ] ) ;
                    update_option( 'rs_select_particular_categories_for_point_price' , $_POST[ 'selectedcategories' ] ) ;
                }

                if ( isset( $_POST[ 'pointpricetype' ] ) ) {
                    update_option( 'fp_point_price_type' , $_POST[ 'pointpricetype' ] ) ;
                    update_option( 'rs_local_point_pricing_type' , $_POST[ 'pointpricetype' ] ) ;
                }

                if ( isset( $_POST[ 'pricepoints' ] ) ) {
                    update_option( 'fp_price_points' , $_POST[ 'pricepoints' ] ) ;
                    update_option( 'rs_local_price_points' , $_POST[ 'pricepoints' ] ) ;
                }

                if ( isset( $_POST[ 'pointpricingtype' ] ) ) {
                    update_option( 'fp_point_pricing_type' , $_POST[ 'pointpricingtype' ] ) ;
                    update_option( 'rs_local_point_price_type' , $_POST[ 'pointpricingtype' ] ) ;
                }

                set_transient( 'fp_background_process_transient_for_bulk_update_point_price' , true , 30 ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function bulk_update_for_point_price() {
            if ( ! get_transient( 'fp_background_process_transient_for_bulk_update_point_price' ) )
                return ;

            delete_transient( 'fp_background_process_transient_for_bulk_update_point_price' ) ;
            delete_option( 'fp_bulk_update_point_price_for_product' ) ;
            self::callback_to_update_point_price_for_product() ;
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_bulk_update_point_price' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            wp_safe_redirect( $redirect_url ) ;
        }

        public static function callback_to_update_point_price_for_product( $offset = 0 , $limit = 1000 ) {
            if ( get_option( 'fp_product_selection_type' ) == 2 ) {
                $ProductIds = srp_check_is_array( get_option( 'fp_selected_products' ) ) ? get_option( 'fp_selected_products' ) : explode( ',' , get_option( 'fp_selected_products' ) ) ;
            } else {
                $args       = array( 'post_type' => 'product' , 'posts_per_page' => '-1' , 'post_status' => 'publish' , 'fields' => 'ids' , 'cache_results' => false ) ;
                $ProductIds = get_posts( $args ) ;
            }
            $SlicedArray = array_slice( $ProductIds , $offset , 1000 ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                foreach ( $SlicedArray as $id ) {
                    self::$update_point_price_for_product->push_to_queue( $id ) ;
                }
            } else {
                self::$update_point_price_for_product->push_to_queue( 'no_products' ) ;
            }
            update_option( 'fp_bulk_update_point_price_for_product' , $limit + $offset ) ;

            if ( $offset == 0 )
                FP_WooCommerce_Log::log( 'Process to Update Point Price for Product(s) Started' ) ;

            self::$rs_progress_bar->fp_increase_progress( 50 ) ;
            self::$update_point_price_for_product->save()->dispatch() ;
        }

        /* Initializing Background Process for Voucher Code */

        public static function set_transient_for_voucher_codes() {
            check_ajax_referer( 'fp-create-code' , 'sumo_security' ) ;

            try {
                $voucher_data = array(
                    'codetype'        => $_POST[ 'codetype' ] ,
                    'codelength'      => $_POST[ 'codelength' ] ,
                    'voucherpoint'    => $_POST[ 'voucherpoint' ] ,
                    'noofvoucher'     => $_POST[ 'noofvoucher' ] ,
                    'expiry_date'     => isset( $_POST[ 'expirydate' ] ) ? $_POST[ 'expirydate' ] : '' ,
                    'excludecontent'  => $_POST[ 'excludecontent' ] ,
                    'vouchercreated'  => $_POST[ 'vouchercreated' ] ,
                    'usertype'        => $_POST[ 'usertype' ] ,
                    'usagelimit'      => $_POST[ 'usagelimit' ] ,
                    'usagelimitvalue' => $_POST[ 'usagelimitvalue' ] ,
                        ) ;
                if ( $_POST[ 'enableprefix' ] == 'yes' && $_POST[ 'enablesuffix' ] == 'yes' ) {
                    $voucher_data[ 'prefixvalue' ] = $_POST[ 'prefixvalue' ] ;
                    $voucher_data[ 'suffixvalue' ] = $_POST[ 'suffixvalue' ] ;
                } elseif ( $_POST[ 'enableprefix' ] == 'yes' && $_POST[ 'enablesuffix' ] != 'yes' ) {
                    $voucher_data[ 'prefixvalue' ] = $_POST[ 'prefixvalue' ] ;
                } elseif ( $_POST[ 'enableprefix' ] != 'yes' && $_POST[ 'enablesuffix' ] == 'yes' ) {
                    $voucher_data[ 'suffixvalue' ] = $_POST[ 'suffixvalue' ] ;
                }

                $vouchercodes = self::voucher_codes( $voucher_data ) ;
                update_option( 'rs_expireddate_for_added_points' , $voucher_data[ 'expiry_date' ] ) ;

                update_option( 'rs_voucher_data' , $voucher_data ) ;

                update_option( 'rs_voucher_codes' , $vouchercodes ) ;

                set_transient( 'fp_background_process_transient_for_voucher_code' , true , 30 ) ;
                wp_send_json_success( array( 'content' => $vouchercodes ) ) ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function voucher_codes( $VoucherData ) {
            $prefix_value = isset( $VoucherData[ 'prefixvalue' ] ) ? $VoucherData[ 'prefixvalue' ] : '' ;
            $suffix_value = isset( $VoucherData[ 'suffixvalue' ] ) ? $VoucherData[ 'suffixvalue' ] : '' ;
            $noofvouchers = ( int ) $VoucherData[ 'noofvoucher' ] ;
            if ( $VoucherData[ 'codetype' ] == 'numeric' ) {
                for ( $k = 0 ; $k < $noofvouchers ; $k ++ ) {
                    $random_code = '' ;
                    for ( $j = 1 ; $j <= $VoucherData[ 'codelength' ] ; $j ++ ) {
                        $random_code .= rand( 0 , 9 ) ;
                    }
                    $random_codes[] = $prefix_value . $random_code . $suffix_value ;
                }
            } else {
                $list_of_characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ;
                $character_length   = strlen( $list_of_characters ) ;
                for ( $k = 0 ; $k < $noofvouchers ; $k ++ ) {
                    $randomstring = '' ;
                    for ( $j = 1 ; $j <= $VoucherData[ 'codelength' ] ; $j ++ ) {
                        $randomstring .= $list_of_characters[ rand( 0 , $character_length - 1 ) ] ;
                    }
                    if ( $VoucherData[ 'excludecontent' ] != "" ) {
                        $exclude_string = explode( "," , $VoucherData[ 'excludecontent' ] ) ;
                        $new_array      = array() ;
                        foreach ( $exclude_string as $value ) {
                            $new_array[ $value ] = rand( 0 , 9 ) ;
                        }

                        $randomstring = strtr( $randomstring , $new_array ) ;
                    }
                    $random_codes[] = $prefix_value . $randomstring . $suffix_value ;
                }
            }
            return $random_codes ;
        }

        public static function generate_voucher_codes() {
            if ( ! get_transient( 'fp_background_process_transient_for_voucher_code' ) )
                return ;

            delete_transient( 'fp_background_process_transient_for_voucher_code' ) ;
            delete_option( 'rs_updater_for_voucher_code' ) ;
            self::callback_to_generate_voucher_codes() ;
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_generate_voucher_code' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            wp_safe_redirect( $redirect_url ) ;
        }

        public static function callback_to_generate_voucher_codes( $offset = 0 , $limit = 1000 ) {
            $VoucherCodes = get_option( 'rs_voucher_codes' ) ;
            $SlicedArray  = array_slice( array_unique( $VoucherCodes ) , $offset , 1000 ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                foreach ( $SlicedArray as $id ) {
                    self::$generate_voucher_codes->push_to_queue( $id ) ;
                }
            } else {
                self::$generate_voucher_codes->push_to_queue( 'no_vouchers' ) ;
            }

            update_option( 'rs_updater_for_voucher_code' , $limit + $offset ) ;

            if ( $offset == 0 )
                FP_WooCommerce_Log::log( 'Process to Generate Vocuher Code(s) Started' ) ;

            self::$rs_progress_bar->fp_increase_progress( 50 ) ;
            self::$generate_voucher_codes->save()->dispatch() ;
        }

        /* Initializing Background Process for Export Logs */

        public static function set_transient_for_export_log() {
            check_ajax_referer( 'fp-export-log' , 'sumo_security' ) ;

            try {
                delete_option( 'rs_data_to_export' ) ;
                update_option( 'rs_data_to_export' , array() ) ;
                if ( isset( $_POST[ 'usertype' ] ) )
                    update_option( 'fp_user_selection_type' , $_POST[ 'usertype' ] ) ;

                if ( isset( $_POST[ 'selecteduser' ] ) )
                    update_option( 'fp_selected_users' , $_POST[ 'selecteduser' ] ) ;

                set_transient( 'fp_background_process_transient_for_export_log' , true , 30 ) ;
                wp_send_json_success() ;
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
            }
        }

        public static function export_log_for_user() {
            if ( ! get_transient( 'fp_background_process_transient_for_export_log' ) )
                return ;

            delete_transient( 'fp_background_process_transient_for_export_log' ) ;
            delete_option( 'rs_export_log_background_updater_offset' ) ;
            self::callback_to_export_log_for_user() ;
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_export_log' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            wp_safe_redirect( $redirect_url ) ;
        }

        public static function callback_to_export_log_for_user( $offset = 0 , $limit = 1000 ) {
            $UserSelectionType = get_option( 'fp_user_selection_type' ) ;
            $Selecteduser      = get_option( 'fp_selected_users' ) ;
            if ( $UserSelectionType == '1' ) {
                $args   = array( 'fields' => 'ID' ) ;
                $UserId = get_users( $args ) ;
            } else if ( $UserSelectionType == '2' ) {
                $UserId = srp_check_is_array( $Selecteduser ) ? $Selecteduser : explode( ',' , $Selecteduser ) ;
            }
            $SlicedArray = array_slice( $UserId , $offset , 1000 ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                foreach ( $SlicedArray as $id ) {
                    self::$export_log_for_user->push_to_queue( $id ) ;
                }
            } else {
                self::$export_log_for_user->push_to_queue( 'no_users' ) ;
            }
            update_option( 'rs_export_log_background_updater_offset' , $limit + $offset ) ;

            if ( $offset == 0 )
                FP_WooCommerce_Log::log( 'Process to Export Log for User(s) Started' ) ;

            self::$rs_progress_bar->fp_increase_progress( 50 ) ;
            self::$export_log_for_user->save()->dispatch() ;
        }

        /* Display when required some updates for this plugin */

        public static function rs_display_notice_in_top() {
            global $wpdb ;
            $table_name = $wpdb->prefix . 'rspointexpiry' ;
            if ( (get_option( 'rs_upgrade_success' ) != 'yes' ) && (get_option( 'rs_no_data_to_upgrade' ) != 'yes') && ( ! RSInstall::rs_check_table_exists( $table_name ) ) && (get_option( 'rs_new_update_user' ) != true) ) {
                if ( self::fp_rs_upgrade_file_exists() ) {
                    $link         = "<a id='rs_display_notice' data-methd='cron' href='#'>Click here</a>" ;
                    $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'rs_background_process' => 'yes' ) , SRP_ADMIN_URL ) ) ;
                    ?>
                    <div id="rs_message" class="notice notice-warning"><p><strong><?php _e( "SUMO Reward Points requires Database Upgrade, $link to proceed with the Upgrade" , SRP_LOCALE ) ; ?></strong></p></div>
                    <div id="rs_updating_message" class="updated notice-warning" style="display:none"><p><strong> <?php _e( "SUMO Reward Points Data Update - Your database is being updated in the background." , SRP_LOCALE ) ; ?></strong></p></div>
                    <script type="text/javascript">
                        jQuery( function () {
                            jQuery( document ).on( 'click' , '#rs_display_notice' , function () {
                                var rsconfirm = confirm( "It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?" ) ;
                                if ( rsconfirm === true ) {
                                    var data = { action : "rs_database_upgrade_process" } ;
                                    jQuery.ajax( {
                                        type : "POST" ,
                                        url : ajaxurl ,
                                        data : data ,
                                    } ).done( function ( response ) {
                                        window.location.href = '<?php echo $redirect_url ; ?>' ;
                                    } ) ;
                                }
                                return false ;
                            } ) ;
                        } ) ;
                    </script>
                    <?php
                } else {
                    $support_link = '<a href="http://fantasticplugins.com/support">' . __( 'Support' , SRP_LOCALE ) . '</a>' ;
                    ?><div id="message" class="notice notice-warning"><p><strong> <?php _e( "Upgrade to v18.0 has failed. Please contact our $support_link" , 'recoverabandoncart' ) ; ?></strong></p></div><?php
                }
            }
        }

        public static function set_transient_for_product_update() {
            update_option( 'rs_product_purchase_activated' , 'yes' ) ;
            update_option( 'rs_referral_activated' , 'yes' ) ;
            update_option( 'rs_social_reward_activated' , 'yes' ) ;
            update_option( 'rs_reward_action_activated' , 'yes' ) ;
            update_option( 'rs_point_expiry_activated' , 'yes' ) ;
            update_option( 'rs_redeeming_activated' , 'yes' ) ;
            update_option( 'rs_point_price_activated' , 'yes' ) ;
            update_option( 'rs_email_activated' , 'yes' ) ;
            update_option( 'rs_gift_voucher_activated' , 'yes' ) ;
            update_option( 'rs_sms_activated' , 'yes' ) ;
            update_option( 'rs_cashback_activated' , 'yes' ) ;
            update_option( 'rs_nominee_activated' , 'yes' ) ;
            update_option( 'rs_point_url_activated' , 'yes' ) ;
            update_option( 'rs_gateway_activated' , 'yes' ) ;
            update_option( 'rs_send_points_activated' , 'yes' ) ;
            update_option( 'rs_imp_exp_activated' , 'yes' ) ;
            update_option( 'rs_report_activated' , 'yes' ) ;
            update_option( 'rs_reset_activated' , 'yes' ) ;
            update_option( 'rs_enable_product_category_level_for_product_purchase' , 'yes' ) ;
            update_option( 'rs_enable_product_category_level_for_referral_product_purchase' , 'yes' ) ;
            update_option( 'rs_enable_product_category_level_for_social_reward' , 'yes' ) ;
            update_option( 'rs_enable_product_category_level_for_points_price' , 'yes' ) ;
            if ( get_option( 'rs_global_enable_disable_sumo_reward' ) == '1' ) {
                update_option( 'rs_global_enable_disable_sumo_referral_reward' , '1' ) ;
            }
            $totalcount = self::fp_rs_overall_batch_count() ;
            if ( $totalcount != 0 ) {
                if ( is_object( self::$rs_progress_bar ) ) {
                    self::$rs_progress_bar->fp_delete_option() ;
                    self::$rs_progress_bar->fp_increase_progress( 10 ) ;
                    set_transient( 'fp_rs_background_process_transient' , true , 30 ) ;
                }
            } else {
                add_option( 'rs_no_data_to_upgrade' , 'yes' ) ;
                set_transient( '_welcome_screen_activation_redirect_reward_points' , true , 30 ) ;
            }
        }

        public static function callback_to_update_simple_product( $offset = 0 , $limit = 1000 ) {
            global $wpdb ;
            $ids = $wpdb->get_results( "SELECT DISTINCT ID FROM {$wpdb->posts} as p INNER JOIN {$wpdb->postmeta} as p1 ON p.ID=p1.post_id WHERE p.post_type = 'product' AND p1.meta_key = '_rewardsystemcheckboxvalue' AND p1.meta_value = 'yes' LIMIT $offset,$limit" ) ;
            if ( is_array( $ids ) && ! empty( $ids ) ) {
                foreach ( $ids as $id ) {
                    self::$update_simple_product->push_to_queue( $id->ID ) ;
                }
            } else {
                self::$update_simple_product->push_to_queue( 'rs_data' ) ;
            }
            update_option( 'rs_simple_product_background_updater_offset' , $limit + $offset ) ;

            if ( $offset == 0 )
                FP_WooCommerce_Log::log( 'Simple Product Upgrade Started' ) ;

            self::$rs_progress_bar->fp_increase_progress( 30 ) ;
            self::$update_simple_product->save()->dispatch() ;
        }

        public static function callback_to_update_variable_product( $offset = 0 , $limit = 1000 ) {
            global $wpdb ;
            $ids = $wpdb->get_results( "SELECT DISTINCT ID FROM {$wpdb->posts} as p INNER JOIN {$wpdb->postmeta} as p1 ON p.ID=p1.post_id WHERE p.post_type = 'product' AND p1.meta_key = '_enable_reward_points' AND p1.meta_value = '1' LIMIT $offset,$limit" ) ;
            if ( is_array( $ids ) && ! empty( $ids ) ) {
                foreach ( $ids as $id ) {
                    self::$update_variable_product->push_to_queue( $id->ID ) ;
                }
            } else {
                self::$update_variable_product->push_to_queue( 'rs_data' ) ;
            }
            update_option( 'rs_variable_product_background_updater_offset' , $limit + $offset ) ;

            if ( $offset == 0 )
                FP_WooCommerce_Log::log( 'Variable Product Upgrade Started' ) ;

            self::$rs_progress_bar->fp_increase_progress( 60 ) ;
            self::$update_variable_product->save()->dispatch() ;
        }

        public static function callback_to_update_category( $offset = 0 , $limit = 1000 ) {
            global $wpdb ;
            $ids = $wpdb->get_col( "SELECT DISTINCT term_id FROM {$wpdb->termmeta} WHERE meta_key = 'enable_reward_system_category' AND meta_value = 'yes' LIMIT $offset,$limit" ) ;
            if ( is_array( $ids ) && ! empty( $ids ) ) {
                foreach ( $ids as $id ) {
                    self::$update_category->push_to_queue( $id ) ;
                }
            } else {
                self::$update_category->push_to_queue( 'rs_data' ) ;
            }

            update_option( 'rs_category_background_updater_offset' , $limit + $offset ) ;

            if ( $offset == 0 )
                FP_WooCommerce_Log::log( 'Category Upgrade Started' ) ;

            self::$rs_progress_bar->fp_increase_progress( 90 ) ;
            self::$update_category->save()->dispatch() ;
        }

        public static function fp_rs_overall_batch_count() {
            global $wpdb ;
            $simple_product_ids   = $wpdb->get_col( "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '_rewardsystemcheckboxvalue' AND meta_value = 'yes'" ) ;
            $variable_product_ids = $wpdb->get_col( "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '_enable_reward_points' AND meta_value = '1'" ) ;
            $category_ids         = $wpdb->get_col( "SELECT term_id FROM " . $wpdb->prefix . "termmeta WHERE meta_key = 'enable_reward_system_category' AND meta_value = 'yes'" ) ;
            $total                = count( $simple_product_ids ) + count( $variable_product_ids ) + count( $category_ids ) ;
            return $total ;
        }

        /* Check if Background Related Files exists */

        public static function fp_rs_upgrade_file_exists() {
            $async_file      = file_exists( untrailingslashit( WP_PLUGIN_DIR ) . '/woocommerce/includes/libraries/wp-async-request.php' ) ;
            $background_file = file_exists( untrailingslashit( WP_PLUGIN_DIR ) . '/woocommerce/includes/libraries/wp-background-process.php' ) ;
            if ( $async_file && $background_file )
                return true ;

            return false ;
        }

        public static function rs_update_earned_points_for_user() {

            if ( ! isset( $_GET[ 'page' ] ) ) {
                return ;
            }

            if ( isset( $_GET[ 'page' ] ) && 'sumo-reward-points-welcome-page' != $_GET[ 'page' ] ) {
                return ;
            }

            if ( 'yes' == get_option( 'rs_points_update_success' ) ) {
                return ;
            }

            delete_option( 'rs_earned_points_background_updater_offset' ) ;
            self::callback_to_update_earned_points() ;
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_update_earned_points' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            wp_safe_redirect( $redirect_url ) ;
        }

        public static function callback_to_update_earned_points( $offset = 0 , $limit = 1000 ) {
            global $wpdb ;
            $ids = $wpdb->get_results( "SELECT DISTINCT ID FROM {$wpdb->users} as p INNER JOIN {$wpdb->usermeta} as p1 ON p.ID=p1.user_id WHERE p1.meta_key = 'rs_expired_points_before_delete' AND p1.meta_value > '0' LIMIT $offset,$limit" ) ;
            if ( is_array( $ids ) && ! empty( $ids ) ) {
                foreach ( $ids as $id ) {
                    self::$update_earned_points->push_to_queue( $id->ID ) ;
                }
            } else {
                self::$update_earned_points->push_to_queue( 'no_users' ) ;
            }

            update_option( 'rs_earned_points_background_updater_offset' , $limit + $offset ) ;

            if ( $offset == 0 )
                FP_WooCommerce_Log::log( 'Earned Points Update Started' ) ;

            self::$rs_progress_bar->fp_increase_progress( 50 ) ;
            self::$update_earned_points->save()->dispatch() ;
        }

    }

}