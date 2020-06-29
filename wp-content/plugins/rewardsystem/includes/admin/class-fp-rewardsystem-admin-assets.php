<?php
/*
 * Admin Assests
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSAdminAssets' ) ) {

    class RSAdminAssets {

        public static function init() {
            add_filter( 'woocommerce_custom_nav_menu_items' , array( __CLASS__ , 'set_custom_menu_items' ) ) ;
            add_action( 'add_meta_boxes' , array( __CLASS__ , 'add_meta_box_for_earned' ) ) ;
            add_action( 'manage_shop_order_posts_custom_column' , array( __CLASS__ , 'srp_custom_orders_list_column_content' ) , 12 , 2 ) ;
            add_filter( 'manage_edit-shop_order_columns' , array( __CLASS__ , 'srp_custom_shop_order_column' ) , 12 ) ;
            add_filter( 'views_edit-shop_order' , array( __CLASS__ , 'srp_custom_menu_referrer_name' ) ) ;
            add_filter( 'request' , array( __CLASS__ , 'srp_custom_menu_request_query' ) ) ;
        }

        public static function menu_restriction_based_on_user_role() {
            global $wp_roles ;
            $UserRole     = '' ;
            $remove_menus = array() ;
            if ( is_object( $wp_roles ) ) {
                foreach ( $wp_roles->role_names as $value => $key ) {
                    $user = new WP_User( get_current_user_id() ) ;
                    if ( srp_check_is_array( $user->roles ) ) {
                        foreach ( $user->roles as $role )
                            $UserRole = $role ;
                    }
                    if ( $UserRole == $value )
                        $remove_menus = (get_option( 'rewardpoints_userrole_menu_restriction' . $value )) ;
                }
            }
            $tabtoshow = array( 'fprsgeneral' , 'fprsmodules' , 'fprsaddremovepoints' , 'fprsmessage' , 'fprslocalization' , 'fprsuserrewardpoints' , 'fprsmasterlog' , 'fprssupport' , 'fprsadvanced' , 'fprsshortcodes' ) ;
            if ( srp_check_is_array( $remove_menus ) ) {
                foreach ( $remove_menus as $remove_menu ) {
                    if ( ($key = array_search( $remove_menu , $tabtoshow )) !== false ) {
                        unset( $tabtoshow[ $key ] ) ;
                    }
                }
            }
            return $tabtoshow ;
        }

        public static function list_of_tabs() {
            return array(
                'fprsgeneral'          => 'General' ,
                'fprsmodules'          => 'Modules' ,
                'fprsaddremovepoints'  => 'Add/Remove Reward Points' ,
                'fprsmessage'          => 'Messages' ,
                'fprslocalization'     => 'Localization' ,
                'fprsuserrewardpoints' => 'User Reward Points' ,
                'fprsmasterlog'        => 'Master Log' ,
                'fprsshortcodes'       => 'Shortcode' ,
                'fprsadvanced'         => 'Advanced' ,
                'fprssupport'          => 'Support'
                    ) ;
        }

        public static function set_custom_menu_items( $endpoints ) {
            $reward_content_title    = get_option( 'rs_my_reward_content_title' ) ;
            $url_title               = get_option( 'rs_my_reward_url_title' ) != '' ? get_option( 'rs_my_reward_url_title' ) : 'sumo-rewardpoints' ;
            $endpoints[ $url_title ] = $reward_content_title ;
            return $endpoints ;
        }

        public static function add_meta_box_for_earned() {
            if ( get_option( 'rs_product_purchase_activated' ) != 'yes' && get_option( 'rs_redeeming_activated' ) != 'yes' )
                return ;

            add_meta_box( 'order_earned_points' , 'Earned Point and Redeem Points For Current Order' , array( __CLASS__ , 'add_meta_box_to_earned_and_redeem_points' ) , 'shop_order' , 'normal' , 'low' ) ;
        }

        public static function add_meta_box_to_earned_and_redeem_points( $order ) {
            if ( get_option( 'rs_product_purchase_activated' ) != 'yes' && get_option( 'rs_redeeming_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_enable_msg_for_earned_points' ) != 'yes' && get_option( 'rs_enable_msg_for_redeem_points' ) != 'yes' )
                return ;

            if ( ! is_object( $order ) )
                return ;

            $earned_redeemed_message = get_earned_redeemed_points_message( $order->ID ) ;
            if ( ! srp_check_is_array( $earned_redeemed_message ) )
                return ;

            foreach ( $earned_redeemed_message as $msgforearnedpoints => $msgforredeempoints ) {
                $replacemsgforearnedpoints = $msgforearnedpoints ;
                $replacemsgforredeempoints = $msgforredeempoints ;
            }
            ?>
            <table width="100%" style=" border-radius: 10px; border-style: solid; border-color: #dfdfdf;">
                <tr>
                    <?php if ( get_option( 'rs_enable_msg_for_earned_points' ) == 'yes' && get_option( 'rs_product_purchase_activated' ) == 'yes' ) { ?>
                        <td style="text-align:center; background-color:#F1F1F1">
                            <h3><?php _e( 'Earned Points' , SRP_LOCALE ) ; ?></h3>
                        </td>
                    <?php } if ( get_option( 'rs_enable_msg_for_redeem_points' ) == 'yes' && get_option( 'rs_redeeming_activated' ) == 'yes' ) { ?>
                        <td style="text-align:center;background-color:#F1F1F1">
                            <h3><?php _e( 'Redeem Points' , SRP_LOCALE ) ; ?></h3>
                        </td>
                    <?php } ?>
                </tr>
                <tr>
                    <?php if ( get_option( 'rs_enable_msg_for_earned_points' ) == 'yes' && get_option( 'rs_product_purchase_activated' ) == 'yes' ) { ?>
                        <td style="text-align:center">
                            <?php echo $replacemsgforearnedpoints ; ?>
                        </td>
                    <?php } if ( get_option( 'rs_enable_msg_for_redeem_points' ) == 'yes' && get_option( 'rs_redeeming_activated' ) == 'yes' ) { ?>
                        <td style="text-align:center">
                            <?php echo $replacemsgforredeempoints ; ?>
                        </td>
                    <?php } ?>
                </tr>
            </table>
            <?php
        }

        public static function srp_custom_orders_list_column_content( $column , $post_id ) {
            if ( get_option( 'rs_referral_activated' ) != 'yes' )
                return ;

            if ( $column != "referrer_name" )
                return ;

            $referrer_name = get_post_meta( $post_id , '_referrer_name' , true ) ;
            if ( ! $referrer_name ) {
                echo '-' ;
                return ;
            }

            if ( get_user_by( 'ID' , $referrer_name ) ) {
                $referrer_name = get_user_by( 'ID' , $referrer_name )->user_login ;
            } else {
                if ( get_option( 'rs_generate_referral_link_based_on_user' ) == 2 )
                    $referrer_name = ! empty( get_user_by( 'ID' , $referrer_name )->user_login ) ? get_user_by( 'ID' , $referrer_name )->user_login : $referrer_name ;
            }

            echo ! empty( $referrer_name ) ? $referrer_name : '-' ;
        }

        public static function srp_custom_shop_order_column( $columns ) {
            if ( get_option( 'rs_referral_activated' ) != 'yes' )
                return $columns ;

            $add_column = array() ;

            foreach ( $columns as $key => $column ) {
                $add_column[ $key ] = $column ;

                if ( $key == 'order_status' )
                    $add_column[ 'referrer_name' ] = __( 'Referrer Name' , SRP_LOCALE ) ;
            }

            return $add_column ;
        }

        /*
         * Add Custom a views
         */

        public static function srp_custom_menu_referrer_name( $views ) {
            if ( get_option( 'rs_referral_activated' ) != 'yes' )
                return $views ;

            $referrer_name_count = self::get_referrer_name_count() ;
            if ( $referrer_name_count < 0 )
                return $views ;

            global $post_type , $wp_query ;

            $ref_name_class = '' ;
            if ( isset( $wp_query->query[ 'meta_key' ] ) && $wp_query->query[ 'meta_key' ] == '_referrer_name' )
                $ref_name_class = isset( $_GET[ 'srp_referrer_name' ] ) ? 'current' : '' ;

            $query_string = admin_url( 'edit.php?post_type=shop_order' ) ;
            $query_string = add_query_arg( 'srp_referrer_name' , 'yes' , $query_string ) ;

            $views[ 'srp_referrer_name' ] = '<a href="' . esc_url( $query_string ) . '" class="' . esc_attr( $ref_name_class ) . '">' . __( 'Referrer Name' , SRP_LOCALE ) . ' (' . $referrer_name_count . ')</a>' ;

            return $views ;
        }

        /**
         * Filters and sorting handler
         */
        public static function srp_custom_menu_request_query( $vars ) {
            if ( get_option( 'rs_referral_activated' ) != 'yes' )
                return $vars ;

            global $typenow , $wp_query , $wp_post_statuses ;

            if ( 'shop_order' === $typenow ) {
                if ( isset( $_GET[ 'srp_referrer_name' ] ) ) {
                    $vars[ 'meta_key' ]     = '_referrer_name' ;
                    $vars[ 'meta_value' ]   = '' ;
                    $vars[ 'meta_compare' ] = '!=' ;
                }
            }

            return $vars ;
        }

        /**
         *  Get Referrer Name Count
         */
        public static function get_referrer_name_count() {
            $args = array(
                'posts_per_page' => -1 ,
                'post_type'      => 'shop_order' ,
                'post_status'    => 'any' ,
                'meta_key'       => '_referrer_name' ,
                'meta_compare'   => 'EXISTS' ,
                'fields'         => 'ids'
                    ) ;

            $count = array_filter( self::srp_check_query_having_posts( $args ) ) ;

            return count( $count ) ;
        }

        public static function srp_check_query_having_posts( $args ) {
            $post       = array() ;
            $query_post = new WP_Query( $args ) ;
            if ( isset( $query_post->posts ) && srp_check_is_array( $query_post->posts ) )
                $post       = $query_post->posts ;

            return $post ;
        }

    }

    RSAdminAssets::init() ;
}