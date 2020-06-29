<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Frontend_Manager_Section_Commissions' ) && function_exists( 'YITH_Vendors' ) ) {

    class YITH_Frontend_Manager_Section_Commissions extends YITH_WCFM_Section {

        /**
         * Constructor method
         *
         * @return \YITH_Frontend_Manager_Section
         * @since 1.0.0
         */
        public function __construct() {
            $this->id = 'commissions';
            $this->_default_section_name = _x( 'Commissions', '[Frontend]: Commissions menu item', 'yith-frontend-manager-for-woocommerce' );

            /*
             *  Multi Vendor
             */
            if ( ! function_exists( 'YITH_Vendors' ) || apply_filters( 'yith_wcfm_remove_commissions_menu_item', false ) ) {
                add_filter( 'yith_wcfm_get_sections_before_print_navigation', array( $this, 'remove_commissions_menu_item_for_admin' ) );
            }

            add_filter( 'yith_wcmv_commission_get_order_uri', array( $this, 'get_order_uri' ), 10, 2 );
            add_filter( 'yith_wcmv_commission_get_product_uri', array( $this, 'get_product_uri' ), 10, 2 );
            add_filter( 'yith_wcmv_commissions_list_table_reset_filter_url', array( $this, 'get_section_url' ) );

            add_action( 'yith_wcfm_commissions_section_deps', array( $this, 'class_alias' ), 20 );

            $this->deps();

            /*
             *  Construct
             */
            parent::__construct();
        }

        /* === SECTION METHODS === */

        /**
         * define class alias
         *
         * @author Andrea Grillo    <andrea.grillo@yithemes.com>
         * @return void
         * @since 1.0.0
         */
        public function class_alias(){
            $commissions_list_class_name = apply_filters( 'yith_wcfm_commissions_list_table_class_alias', 'YITH_Commissions_List_Table' );
            class_exists( $commissions_list_class_name ) && class_alias( $commissions_list_class_name, 'YITH_WCMV_Commissions_List_Table' );
        }

        /**
         * Required files for this section
         *
         * @author Andrea Grillo    <andrea.grillo@yithemes.com>
         * @return void
         * @since 1.0.0
         */
        public function deps(){
            if( ! class_exists( 'WP_List_Table' ) ){
                require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
            }

            if( ! class_exists( 'WP_Posts_List_Table' ) ){
                require_once( ABSPATH . 'wp-admin/includes/class-wp-posts-list-table.php' );
            }

            if( ! class_exists( 'YITH_Commissions_List_Table' ) ){
                $file = YITH_WPV_PATH . 'includes/lib/class.yith-commissions-list-table.php';
                file_exists( $file ) && require_once ( $file );
            }

            do_action( 'yith_wcfm_commissions_section_deps', $this );

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            require_once( YITH_WCFM_LIB_PATH . 'class.yith-frontend-manager-commissions-list-table.php' );
        }


        /**
         * Get section url
         *
         * @param $url
         * @return string
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.0
         */
        public function get_section_url( $url ){
            $section_url = yith_wcfm_get_section_url( 'current' );
            return ! empty( $section_url ) ? $section_url : $url;
        }

        /**
         * Print shortcode function
         *
         * @author Andrea Grillo    <andrea.grillo@yithemes.com>
         * @return void
         * @since  1.0.0
         */
        public function print_shortcode( $atts = array(), $content = '', $tag ) {
            $section = $this->id;
            $subsection_prefix = $this->get_shortcodes_prefix() . $section;
            $subsection = $tag != $subsection_prefix ? str_replace( $subsection_prefix . '_', '', $tag ) : $section;

            if( apply_filters( 'yith_wcfm_print_commissions_section', true, $subsection, $section, $atts ) ){

                $atts['section_obj'] = $this;

                $atts = apply_filters( 'yith_wcfm_commissions_args', $atts, $subsection, $section );

                $this->print_section( $subsection, $section, $atts );
            }

            else {
                do_action( 'yith_wcfm_print_section_unauthorized', $this->id );
            }
        }

        /**
         * WP Enqueue Scripts
         *
         * @author Corrado Porzio <corradoporzio@gmail.com>
         * @return void
         * @since  1.0.0
         */
        public function enqueue_section_scripts() {

            global $wp_scripts;

            // CSS

            wp_enqueue_style( 'yith-wcfm-reports', YITH_WCFM_URL . 'assets/css/commissions.css', array(), YITH_WCFM_VERSION );
            wp_enqueue_style( 'yith-wc-product-vendors-admin' );

            // CSS WooCommerce
            $screen         = get_current_screen();
            $screen_id      = $screen ? $screen->id : '';

            // Sitewide menu CSS
            wp_enqueue_style( 'woocommerce_admin_menu_styles' );
            wp_enqueue_style( 'woocommerce_admin_styles' );
            wp_enqueue_style( 'jquery-ui-style' );
            wp_enqueue_style( 'wp-color-picker' );

            if ( in_array( $screen_id, array( 'dashboard' ) ) ) {
                wp_enqueue_style( 'woocommerce_admin_dashboard_styles' );
            }

            if ( in_array( $screen_id, array( 'woocommerce_page_wc-reports', 'toplevel_page_wc-reports' ) ) ) {
                wp_enqueue_style( 'woocommerce_admin_print_reports_styles' );
            }

            /**
             * @deprecated 2.3
             */
            if ( has_action( 'woocommerce_admin_css' ) ) {
                do_action( 'woocommerce_admin_css' );
                _deprecated_function( 'The woocommerce_admin_css action', '2.3', 'admin_enqueue_scripts' );
            }

            // JS
            wp_enqueue_script( 'woocommerce_admin' );
            wp_enqueue_script( 'wc-admin-order-meta-boxes' );

        }

        /**
         * Remove vendor panel menu item for adminstrator
         *
         * @param $sections
         * @return mixed
         */
        public function remove_commissions_menu_item_for_admin( $sections ){
            if( isset( $sections[ $this->id ] ) ){
                unset( $sections[ $this->id ] );
            }
            return $sections;
        }

        /**
         * Get the order uri
         *
         * @return string order_uri
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @sicne 1.0
         */
        public function get_order_uri( $order_uri, $order_id ){
            return $order_uri = empty( YITH_Frontend_Manager()->gui ) ? $order_uri :  YITH_Frontend_Manager_Section_Orders::get_edit_order_permalink( $order_id );
        }

        /**
         * Get the prdocut uri
         *
         * @return string order_uri
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @sicne 1.0
         */
        public function get_product_uri( $product_uri, $product_id ){
            return $product_uri = empty( YITH_Frontend_Manager()->gui ) ? $product_uri : YITH_Frontend_Manager_Section_Products::get_edit_product_link( $product_id );
        }
    }

}
