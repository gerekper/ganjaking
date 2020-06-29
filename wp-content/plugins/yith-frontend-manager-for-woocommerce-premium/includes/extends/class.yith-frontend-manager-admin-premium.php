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

if( ! class_exists( 'YITH_Frontend_Manager_Admin_Premium' ) ){

    class YITH_Frontend_Manager_Admin_Premium extends YITH_Frontend_Manager_Admin {

        public function __construct() {
            /* Panel Options */
            add_action( 'woocommerce_admin_field_yith_wcfm_sectionstart', array( $this, 'endpoint_section_management' ) );
            add_action( 'woocommerce_admin_field_yith_wcfm_sectionend', array( $this, 'endpoint_section_management' ) );
            add_action( 'woocommerce_admin_field_yith_wcfm_section_disabled_message', array( $this, 'endpoint_section_management' ) );
	        add_action( 'yith_wcfm_admin_tabs', array( $this, 'premium_option_tabs' ) );

            /* General Settings Premium options */
            add_filter( 'yith_wcfm_settings_options', array( $this, 'add_premium_options_to_general_settings' ), 10, 2 );

            /* Prevent WooCommerce Access Admin */
            add_filter( 'woocommerce_prevent_admin_access', array( $this, 'prevent_admin_access' ) );

            /* Register plugin to licence/update system */
            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

            parent::__construct();
        }


        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_activation() {
            if( class_exists( 'YIT_Plugin_Licence' ) ){
            	YIT_Plugin_Licence()->register( YITH_WCFM_INIT, YITH_WCFM_SECRET_KEY, YITH_WCFM_SLUG );
            }
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_updates() {
	        if( class_exists( 'YIT_Upgrade' ) ){
	        	YIT_Upgrade()->register( YITH_WCFM_SLUG, YITH_WCFM_INIT );
	        }
        }

        /**
         * New Panel option for WooCommerce
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.0
         *
         * @param $option
         * @return void
         */
        public function endpoint_section_management( $option ){
            if( ! empty( $option['value'] ) ){
                $sections = YITH_Frontend_Manager()->get_section();
                $obj = isset( $sections[ $option['value'] ] ) ? $sections[ $option['value'] ] : '';
                if( $obj && is_object( $obj ) ){
                    $option_id = yith_wcfm_get_section_enabled_id_from_object( 'option_id', $obj );
                    $is_section_disabled = 'no' == get_option( $option_id, 'yes' ) ? true : false;
                    $class = $is_section_disabled ? 'yith_wcfm_section_disabled' : 'yith_wcfm_section_enabled';

                    if( 'yith_wcfm_sectionstart' == $option['type'] ){
                        printf( '<div class="yith_switch_section yith_wcfm_section_tooltip %s" data-section="%s">', $class, $obj->get_id() );
                    }

                    elseif( 'yith_wcfm_sectionend' == $option['type'] ){
                        echo '</div>';
                    }

                    elseif( $is_section_disabled && 'yith_wcfm_section_disabled_message' == $option['type'] ){
                        printf( '<div class="yith_wcfm_section_disabled_message"><strong>%s</strong>: %s <a href="%s">%s</a> %s</div>',
                            _x( 'Notice', '[Admin] ex. Notice: xxxx', 'yith-frontend-manager-for-woocommerce' ),
                            __( 'This section has been disabled. Enable it in the', 'yith-frontend-manager-for-woocommerce' ),
                            add_query_arg( array( 'page' => 'yith_wcfm_panel', 'tab' => 'sections' ), admin_url( 'admin.php' ) ),
                            _x( 'Sections', '[Admin]: Link to the Sections panel page', 'yith-frontend-manager-for-woocommerce' ),
                            _x( 'tab', '[Admin]: option tab', 'yith-frontend-manager-for-woocommerce' )
                        );
                    }
                }
            }
        }

        /**
         * Premium options for general settings
         *
         * Add premium options for general settings
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.0
         *
         * @param $options
         * @param $end_id
         * @return array the options array
         */
        public function add_premium_options_to_general_settings( $options, $end_id ){
            $new_options = array(
                'settings_options_remove_wp_bar' => array(
                    'type'    => 'checkbox',
                    'title'   => __( 'WordPress admin bar', 'yith-frontend-manager-for-woocommerce' ),
                    'desc'    => __( 'Check this option to remove the WordPress admin bar on the frontend', 'yith-frontend-manager-for-woocommerce' ),
                    'id'      => 'yith_wcfm_remove_wp_admin_bar',
                    'default' => 'no'
                ),

                'settings_options_prevent_admin_access' => array(
                    'type'    => 'checkbox',
                    'title'   => __( 'Prevent backend access to shop managers', 'yith-frontend-manager-for-woocommerce' ),
                    'desc'    => __( 'This option allows you to prevent shop managers from accessing the WordPress dashboard',
                        'yith-frontend-manager-for-woocommerce' ),
                    'id'      => 'yith_wcfm_prevent_backend_access',
                    'default' => 'no'
                ),
            );

            $end_option = array();

            if( isset( $options[ $end_id ] ) ){
                $end_option[ $end_id ] = $options[ $end_id ];
                unset( $options[ $end_id ] );
            }

            return array_merge( $options, $new_options, $end_option );
        }

        /**
         * Check if a shop manager can access to WordPress backend
         *
         * @Author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since  1.0.0
         * @return bool
         * @use    woocommerce_prevent_admin_access hooks
         */
        public function prevent_admin_access( $prevent_access ) {
	        $is_media_library                   = ! empty( $_REQUEST['action'] ) && 'query-attachments' == $_REQUEST['action'];
	        $is_upload_image                    = ! empty( $_REQUEST['action'] ) && 'upload-attachment' == $_REQUEST['action'];
	        $is_add_attribute                   = ! empty( $_REQUEST['action'] ) && 'woocommerce_add_attribute' == $_REQUEST['action'];
	        $is_change_order_status             = ! empty( $_REQUEST['action'] ) && 'woocommerce_mark_order_status' == $_REQUEST['action'];
	        $is_search_products_to_order        = ! empty( $_REQUEST['action'] ) && 'woocommerce_json_search_products_and_variations' == $_REQUEST['action'];
	        $is_add_products_to_order           = ! empty( $_REQUEST['action'] ) && 'woocommerce_add_order_item' == $_REQUEST['action'];
	        $is_add_fee_to_order                = ! empty( $_REQUEST['action'] ) && 'woocommerce_add_order_fee' == $_REQUEST['action'];
	        $is_add_shipping_to_order           = ! empty( $_REQUEST['action'] ) && 'woocommerce_add_order_shipping' == $_REQUEST['action'];
	        $is_add_coupon_discount_to_order    = ! empty( $_REQUEST['action'] ) && 'woocommerce_add_coupon_discount' == $_REQUEST['action'];
	        $is_remove_coupon_discount_to_order = ! empty( $_REQUEST['action'] ) && 'woocommerce_remove_order_coupon' == $_REQUEST['action'];
	        $is_save_oder_items_to_order        = ! empty( $_REQUEST['action'] ) && 'woocommerce_save_order_items' == $_REQUEST['action'];
	        $is_calc_line_taxes_to_order        = ! empty( $_REQUEST['action'] ) && 'woocommerce_calc_line_taxes' == $_REQUEST['action'];
	        $is_add_taxes_to_order              = ! empty( $_REQUEST['action'] ) && 'woocommerce_add_order_tax' == $_REQUEST['action'];
	        $_post                              = $_post_type = 0;

	        if( ! empty( $_REQUEST['post'] ) ){
	        	$_post = $_REQUEST['post'];
	        	$_post_type = get_post_type( $_post );
	        }

            $is_delete_option       = ! empty( $_REQUEST['action'] ) && 'trash' == $_REQUEST['action'] && 'shop_order' == $_post_type;

            if( ! $is_add_taxes_to_order && ! $is_save_oder_items_to_order && ! $is_remove_coupon_discount_to_order && ! $is_calc_line_taxes_to_order && ! $is_add_coupon_discount_to_order && ! $is_add_shipping_to_order && ! $is_add_fee_to_order && ! $is_search_products_to_order && ! $is_add_products_to_order && ! $is_delete_option && ! $is_media_library && ! $is_upload_image && ! $is_add_attribute && ! $is_change_order_status && 'yes' == get_option( 'yith_wcfm_prevent_backend_access', 'no' ) ){
                $current_user = wp_get_current_user();
                if( ! empty( $current_user ) && $current_user instanceof  WP_User  ){
                    if( in_array( 'shop_manager', $current_user->roles ) && ! in_array( 'administrator', $current_user->roles ) ){
                        $prevent_access = true;
                    }
                }
            }
            return $prevent_access;
        }

        /**
         * Switch enabled/disabled section in ajax
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         *
         */
        public function switch_section() {
            if( ! empty( $_GET['section'] ) ){
                $option_id  = 'yith_wcfm_enable_yith_frontend_manager_section_' . $_GET['section'];
                $new_value = 'yes' == get_option( $option_id, 'yes' ) ? 'no' : 'yes';
                update_option( $option_id, $new_value );
            }
            
            die();
        }

	    /**
	     * Manage premium panel tabs
	     *
	     * @since    1.0
	     * @author   Andrea Grillo <andrea.grillo@yithemes.com>
	     * @return   array available tabs
	     */
	    public function premium_option_tabs( $admin_tabs ) {
		    //Remove premium tab
		    if ( isset( $admin_tabs['premium'] ) ) {
			    unset( $admin_tabs['premium'] );
		    }

		    $admin_tabs['skins'] = __( 'Skins', 'yith-frontend-manager-for-woocommerce' );

		    return $admin_tabs;
	    }
    }
}