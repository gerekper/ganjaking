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

if( ! class_exists( 'YITH_Frontend_Manager_Section_Vendor' ) && class_exists( 'YITH_Vendors_Premium' ) ) {

	class YITH_Frontend_Manager_Section_Vendor extends YITH_WCFM_Section {

        /**
         * Current Vendor Object
         */
        public $vendor = null;

		/**
		 * Constructor method
		 *
		 * @return \YITH_Frontend_Manager_Section_Vendor
		 * @since 1.0.0
		 */
		public function __construct() {
            $this->id                    = 'vendor-panel';
            $this->_default_section_name = _x( 'Vendor settings', '[Frontend]: Vendor settings menu item', 'yith-frontend-manager-for-woocommerce' );
            $this->vendor                = yith_get_vendor( 'current', 'user' );

            $this->set_allowed_query_string( array( 'page' => 'yith_vendor_settings' ) );

            if( $this->vendor->is_valid() && $this->vendor->has_limited_access() ){
                /* Disable WordPress admin bar */
                $prevent_admin_access = 'yes' == get_option( 'yith_wcfm_prevent_backend_access_for_vendor', 'no' );
                $remove_wp_bar = 'yes' == get_option( 'yith_wcfm_remove_wp_admin_bar', 'no' );
                //Remove wp admin bar if the option is set or if the user can't access to dashboard
                if( $prevent_admin_access || $remove_wp_bar ) {
                    add_filter( 'show_admin_bar', '__return_false' );
                }

                add_filter( 'yith_wcfm_section_url', array( $this, 'vendor_settings_uri' ), 10, 4 );
                add_action( 'wp_enqueue_scripts', array( YITH_Vendors()->admin, 'enqueue_scripts' ), 5);
                add_filter( 'yith_plugin_fw_panel_url', array( $this, 'change_panel_url' ), 10, 5 );

                add_filter( 'woocommerce_prevent_admin_access', array( $this, 'prevent_admin_access' ), 99 );

                parent::__construct();
            }

            else{
                if( is_admin() ){
                    add_filter( 'yith_wcfm_endpoints_options', array( $this, 'add_endpoints_settings' ) );
                    add_filter( 'yith_wcfm_sections_options', array( $this, 'sections_options' ) );
                }

                add_filter( 'yith_wcfm_get_sections_before_print_navigation', array( $this, 'remove_vendor_menu_item_for_admin' ) );
            }

            add_filter( 'yith_wcfm_settings_options', array( $this, 'add_general_settings_options_for_vendor' ), 20, 2 );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_fw_scripts' ) );
		}


		/**
		 * Enqueue scripts for vendor
		 *
		 * @return void
		 */
		public function enqueue_fw_scripts(){
			$suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script( 'yith-plugin-fw-fields', YIT_CORE_PLUGIN_URL . '/assets/js/yith-fields' . $suffix . '.js', array( 'jquery' ), YITH_WCFM_VERSION, true );
		}

		/* === SECTION METHODS === */

		/**
		 * Print shortcode function
		 *
		 * @author YITH <plugins@yithemes.com>
		 * @return void
		 * @since 1.0.0
		 */
		public function print_shortcode( $atts = array(), $content = '', $tag = '' ) {

		    if( class_exists( 'YITH_Vendors_Admin_Premium' ) ){

		        if( ! empty( YITH_Frontend_Manager()->gui ) ){
		            add_filter( 'yit_panel_hide_sidebar', '__return_true' );
                }

                $this->show_wc_notice();

                YITH_Vendors()->admin->vendor_settings();
                $vendor_panel = YITH_Vendors()->admin->get_vendor_panel();
                $vendor_panel->yit_panel();
            }
		}

        /**
         * Remove vendor panel menu item for adminstrator
         *
         * @param $sections
         * @return mixed
         */
		public function remove_vendor_menu_item_for_admin( $sections ){
		    if( isset( $sections[ $this->id ] ) ){
		        unset( $sections[ $this->id ] );
            }
		    return $sections;
        }

        /**
         * Add general options for vendors
         *
         * @param $options
         * @param $end_id
         * @return mixed
         */
        public function add_general_settings_options_for_vendor( $options, $end_id ){

            $new_options = array(
                'settings_options_prevent_admin_access_vendor' => array(
                    'type'    => 'checkbox',
                    'title'   => __( 'Prevent backend access to vendors', 'yith-frontend-manager-for-woocommerce' ),
                    'desc'    => __( 'This option allows you to prevent vendors from accessing the WordPress dashboard',
                        'yith-frontend-manager-for-woocommerce' ),
                    'id'      => 'yith_wcfm_prevent_backend_access_for_vendor',
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
            $is_save_vendor_panel   = ! empty( $_REQUEST['action'] ) && 'yith_admin_save_fields' == $_REQUEST['action'];
            $is_media_library       = ! empty( $_REQUEST['action'] ) && 'query-attachments' == $_REQUEST['action'];
            $is_upload_image        = ! empty( $_REQUEST['action'] ) && 'upload-attachment' == $_REQUEST['action'];
	        $is_add_attribute       = ! empty( $_REQUEST['action'] ) && 'woocommerce_add_attribute' == $_REQUEST['action'];
	        $is_change_order_status = ! empty( $_REQUEST['action'] ) && 'woocommerce_mark_order_status' == $_REQUEST['action'];
	        $_post = $_post_type = 0;

	        if( ! empty( $_REQUEST['post'] ) ){
		        $_post = $_REQUEST['post'];
		        $_post_type = get_post_type( $_post );
	        }

	        $is_delete_option       = ! empty( $_REQUEST['action'] ) && 'trash' == $_REQUEST['action'] && 'shop_order' == $_post_type;

            if( ! wp_doing_ajax() && ! $is_delete_option && ! $is_upload_image && ! $is_save_vendor_panel && ! $is_media_library && ! $is_add_attribute && ! $is_change_order_status && current_user_can( YITH_Vendors()->get_role_name() ) && 'yes' == get_option( 'yith_wcfm_prevent_backend_access_for_vendor', 'no' ) ){
                $prevent_access = true;
            }

            return $prevent_access;
        }

        /**
         * Get correct vendor panel endpoint uri
         *
         * @param $endpoint_uri
         * @param $slug
         * @param $subsection
         * @return mixed
         */
        public function vendor_settings_uri( $endpoint_uri, $slug, $subsection, $id ){
            if( $id == $this->get_id() ){
                $endpoint_uri = add_query_arg( array( 'page' => YITH_Vendors()->admin->vendor_panel_page ), $endpoint_uri );
            }
            return $endpoint_uri;
        }

		/**
		 * @param $url
		 * @param $page
		 * @param $tab
		 * @param $sub_tab
		 * @param $parent_page
		 *
		 * @return mixed
		 */
        public function change_panel_url( $url, $page, $tab, $sub_tab, $parent_page ){
			if( 'yith_vendor_settings' == $page && ! empty( YITH_Frontend_Manager()->gui ) && YITH_Frontend_Manager()->gui->is_main_page() ){
				$old_url = add_query_arg( array( 'page' => $page ), admin_url( 'admin.php' ) );
				$url = str_replace( $old_url, $this->get_url(), $url );
			}
			return $url;
        }

        /**
         * Section styles and scripts
         *
         * Override this method in section class to enqueue
         * particular styles and scripts only in correct
         * section
         *
         * @return false
         * @since  1.0.0
         */
        public function enqueue_section_scripts(){

            if( $this->is_current() && ! empty( $_GET['page'] ) && 'yith_vendor_settings' == $_GET['page'] ){
                /* Vendor Vacation Module */
                if( ! empty( $_GET['tab'] ) && 'vacation' == $_GET['tab'] ){
                    wp_enqueue_script( 'yith-wpv-datepicker' );
                    wp_enqueue_style( 'jquery-ui-style' );
                }

                /* Vendor Settings */
                if( empty( $_GET['tab'] ) || ( ! empty( $_GET['tab'] ) && 'vendor-settings' == $_GET['tab'] ) ) {
                    wp_enqueue_style( 'select2' );
                    wp_enqueue_script( 'wc-enhanced-select' );
                }
            }
        }

        /**
         * Print an admin notice
         *
         * @since 1.0.15
         * @return void
         * @use admin_notices hooks
         */
        public function show_wc_notice() {
            $message = $type = '';

            if( isset( $_GET['message'] ) ){
                switch( $_GET['message'] ){
                    case 'success':
                        $message = __( 'Option Saved', 'yith-frontend-manager-for-woocommerce' );
                        $type = 'success';
                        break;

                    case 'name_exists':
                        $message = __( 'A vendor with this name already exists.', 'yith-frontend-manager-for-woocommerce' );
                        $type = 'error';
                        break;
                }
            }

            if( ! empty( $message ) ) {
                wc_print_notice( $message, $type );
            }
        }
	}
}

