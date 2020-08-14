<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined ( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

if( ! class_exists( 'YITH_Frontend_Manager_GUI_Premium' ) ){

    class YITH_Frontend_Manager_GUI_Premium extends YITH_Frontend_Manager_GUI{

        /**
         * @var string Menu item id
         */
        public $my_account_menu_item_id = 'yith-wcfm';

        /**
         * YITH_Frontend_Manager_GUI_Premium constructor.
         */
        public function __construct(){
            add_filter( 'woocommerce_account_menu_items', array( $this, 'add_frontend_manager_link_to_my_account' ), 15 );
            add_filter( 'woocommerce_get_endpoint_url', array( $this, 'add_frontend_manager_url_to_my_account' ), 10, 4 );

            /* Disable WordPress admin bar */
            $prevent_admin_access = 'yes' == get_option( 'yith_wcfm_prevent_backend_access', 'no' );
            $remove_wp_bar = 'yes' == get_option( 'yith_wcfm_remove_wp_admin_bar', 'no' );

            //Remove wp admin bar if the option is set or if the user can't access to dashboard
            if( ! current_user_can( 'administrator' ) && ( $prevent_admin_access || $remove_wp_bar ) ) {
                add_filter( 'show_admin_bar', '__return_false' );
            }
            
            parent::__construct();
        }

        /**
         * Add a link to frontend manager dashboard under
         * My Account navigation menu
         * 
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @since    1.0.0
         *           
         * @param $items
         *
         * @return mixed
         */
        public function add_frontend_manager_link_to_my_account( $items ){
            if( YITH_Frontend_Manager()->current_user_can_manage_woocommerce_on_front() ){
                $items[ $this->my_account_menu_item_id ] = apply_filters( 'yith-wcfm-my-account-menu-text', __( 'Frontend Manager', 'yith-frontend-manager-for-woocommerce' ) );    
            }
            return $items;
        }

        /**
         * Change the URL of Frontend Manager endpoint in 
         * My Account navigation menu
         *
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @since    1.0.0
         * 
         * @param $url
         * @param $endpoint
         * @param $value
         * @param $permalink
         *
         * @return string
         */
        public function add_frontend_manager_url_to_my_account( $url, $endpoint, $value, $permalink ){
            if( $this->my_account_menu_item_id == $endpoint ){
                $url = yith_wcfm_get_main_page_url();
            }
            return $url;
        }
    }
}