<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WPV_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Vendors_Frontend
 * @package    Yithemes
 * @since      Version 2.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Vendors_Frontend' ) ) {

    /**
     * Class YITH_Vendors_Frontend
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     */
    class YITH_Vendors_Frontend  {

        /**
         * Constructor
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function __construct() {

            /* Shop Page */
            add_action( 'woocommerce_after_shop_loop_item', array( $this, 'woocommerce_template_vendor_name' ), 6  );
            add_action( 'woocommerce_product_query', array( $this, 'check_vendors_selling_capabilities' ), 10, 1 );

            /* Single Product */
            add_filter( 'woocommerce_product_tabs', array( $this, 'add_product_vendor_tab' ) );
            add_action( 'woocommerce_single_product_summary', array( $this, 'woocommerce_template_vendor_name' ), 5 );
            add_action( 'template_redirect', array( $this, 'exit_direct_access_no_selling_capabilities' ) );

            /* Related Products */
            if( YITH_Vendors()->is_wc_2_6 || YITH_Vendors()->is_wc_lower_2_6 ){
	            add_filter( 'woocommerce_related_products_args', array( $this, 'related_products_args' ), 15 );
            }

            /* Ajax Product Filter Support */
            add_filter( 'yith_wcan_product_taxonomy_type', array( $this, 'add_taxonomy_page' ) );

            /* Vendor Admin Bar */
            add_action( 'template_redirect', array( YITH_Vendors(), 'remove_wp_bar_admin_menu' ) );

            /* MyAccount -> My Order: Disable suborder view */
            add_filter( 'woocommerce_my_account_my_orders_query', array( $this, 'my_account_my_orders_query' ) );

            add_filter( 'woocommerce_customer_get_downloadable_products' , array( $this ,'get_downloadable_products' ) );

            /* Support to Adventure Tours Product Type */
            class_exists( 'WC_Tour_WP_Query' ) && add_filter( 'yith_wcmv_vendor_get_products_query_args', array( $this, 'add_wc_tour_query_type' ) );
            
            /* Body Classes */
            add_filter( 'body_class', array( $this, 'body_class' ), 20 );

            /* Support to YITH Theme FW 2.0 - Sidebar Layout */
            add_filter( 'yit_layout_option_is_product_tax', array( $this, 'show_sidebar_in_vendor_store_page' ) );
        }

        /**
         * Check if the user see a store vendor page
         *
         * @return bool
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function is_vendor_page() {
            return apply_filters( 'yith_wcmv_is_vendor_page', is_tax( YITH_Vendors()->get_taxonomy_name() ) );
        }

        /**
         * Add product vendor tabs in single product page
         *
         * check if the product is property of a specific vendor and add a new tab "Vendor" with the vendor information
         *
         * @param $tabs array The single product tabs
         *
         * @return   array The tab array
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use woocommerce_product_tabs filter
         */
        public function add_product_vendor_tab( $tabs ) {
            global $product;

            $vendor = yith_get_vendor( $product, 'product' );

            $show_tab = defined( 'YITH_WPV_FREE_INIT' ) ? true : 'yes' == get_option( 'yith_wpv_show_vendor_tab_in_single', 'yes' );

            if ( $vendor->is_valid() && $show_tab ) {

                $tab_title = apply_filters( 'yith_single_product_vendor_tab_name', get_option( 'yith_wpv_vendor_tab_text_text', YITH_Vendors()->get_vendors_taxonomy_label( 'singular_name' ) )  );

                $args = array(
                    'title'    => empty( $tab_title ) ? YITH_Vendors()->get_vendors_taxonomy_label( 'singular_name' ) : $tab_title,
                    'priority' => 99,
                    'callback' => array( $this, 'get_vendor_tab' )
                );

                /**
                 * Use yith_wc_vendor as array key. Not use vendor to prevent conflict with wc vendor extension
                 */
                $tabs['yith_wc_vendor'] = apply_filters( 'yith_woocommerce_product_vendor_tab', $args );
            }

            return $tabs;
        }

        /**
         * Get Vendor product tab template
         *
         * @return   array The tab array
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @fire     yith_woocommerce_product_vendor_tab_template filter
         */
        public function get_vendor_tab() {
            global $product;

            $vendor = yith_get_vendor( $product, 'product' );

            if( $vendor->is_valid() ){
                $args = array(
                	'vendor'             => $vendor,
                    'vendor_name'        => $vendor->name,
                    'vendor_description' => $vendor->description,
                    'vendor_url'         => $vendor->get_url()
                );

                $args = apply_filters( 'yith_woocommerce_product_vendor_tab_template', $args );

                yith_wcpv_get_template( 'vendor-tab', $args, 'woocommerce/single-product' );
            }
        }

        /**
         * Add vendor name after product title
         *
         * @return   string The title
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use     the_title filter
         */
        public function woocommerce_template_vendor_name() {
            global $product;

            if ( ! empty( $product ) && is_object( $product ) ) {
                $vendor = yith_get_vendor( $product, 'product' );

                if ( $vendor->is_valid() ) {
                    $args          = array(
                        'vendor' => $vendor,
                        'label_color' => 'color: ' . get_option( 'yith_vendors_color_name' )
                    );

                    $template_info = array(
                        'name'    => 'vendor-name-title',
                        'args'    => $args,
                        'section' => is_product() ? 'woocommerce/single-product' : 'woocommerce/loop',
                    );

                    $template_info = apply_filters( 'yith_woocommerce_vendor_name_template_info', $template_info );

                    extract( $template_info );

                    yith_wcpv_get_template( $name, $args, $section );
                }
            }
        }

        /**
         * check if vendor has selling capabilities
         *
         * @return   string The title
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use     the_title filter
         */
        public function check_vendors_selling_capabilities( $query, $set = true ) {

            $to_exclude = YITH_Vendors()->get_vendors(
                array(
                    'enabled_selling' => false,
                    'owner'           => false,
                    'fields' => 'ids'
                )
            );

            if ( $to_exclude ) {
                $vendor_tax_query = array(
                    'taxonomy' => YITH_Vendors()->get_taxonomy_name(),
                    'field'    => 'id',
                    'terms'    => apply_filters( 'yith_wcmv_to_exclude_terms_in_loop', $to_exclude ),
                    'operator' => 'NOT IN' //use NOT IN in query args to include the super admin products
                );

                if( $set ){
                    $current_tax_query = isset( $query->query_vars['tax_query'] ) ? $query->query_vars['tax_query'] : array();
                    $current_tax_query[] = $vendor_tax_query;
                    $query->set( 'tax_query', $current_tax_query );
                } else {
                    return array( $vendor_tax_query );
                }
            }
        }

        /**
         * exit if the vendor account hasn't selling capabilities
         *
         * @return   string The title
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use     template_redirect filter
         */
        public function exit_direct_access_no_selling_capabilities() {
            global $post;

            if ( ! empty( $post->post_type ) && is_singular( 'product' ) ) {
                $vendor = yith_get_vendor( $post, 'product' );

                if ( $vendor && 'no' == $vendor->enable_selling ) {
                    $this->redirect_404();
                }
            }
        }

        /**
         * exit if the vendor account hasn't selling capabilities
         *
         * @param    $exit bool Default: true. If true call exit function.
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function redirect_404( $exit = true ) {
            include( get_query_template( '404' ) );

            if ( $exit ) {
                exit;
            }
        }

        /**
         * Exclude the not enable vendors to Related products
         *
         * @param $args The related products query args
         *
         * @return mixed|array the query args
         */
        public function related_products_args( $args ){

            $args['tax_query'] = $this->check_vendors_selling_capabilities( false, false );
            return $args;

        }

        /**
         * Add vendor taxonomy page to Ajax Product Filter plugin
         *
         * @param $pages The widget taxonomy pages
         *
         * @return mixed|array The allowed taxonomy
         */
        public function add_taxonomy_page( $pages ){
            $pages[] = YITH_Vendors()->get_taxonomy_name();
            return $pages;
        }

        /**
         * Filter the My account -> My Order page
         *
         * Disable suborder view
         *
         * @param $query_args Unfiltered query args
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.6
         * @return array The order query args
         * @use woocommerce_my_account_my_orders_query hook
         */
        public function my_account_my_orders_query( $query_args ){
            $query_args['post_parent'] = 0;
            return $query_args;
        }

        /**
         * Filter download permission (show only parent order)
         * 
         * @author Salvatore Strano
         * @param $downloads
         * @return array
         */
        public function get_downloadable_products( $downloads ){

            $new_downloads = array();

            foreach ( $downloads as $download ){

                $order_id = $download['order_id'];
                $order = wc_get_order( $order_id );

                //show only parent order download

                $post_parent = get_post_field( 'post_parent', $order_id );
               
                if( $post_parent == 0 ){
                    $new_downloads[] = $download;
                }
            }

            return $new_downloads;
        }

        /**
         * Add Support to Adventure Tours Product Type
         *
         * Add the correct wc_query arg to get_posts array
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.9.13
         * @return $args array WP_Query array
         */
        public function add_wc_tour_query_type( $args ){
            $args['wc_query'] = 'tours';
            return $args;
        }
        
        /**
         * Add body classes on frontend 
         * 
         * @param array $classes
         * @return array body classes array 
         *               
         * @since 1.9.18
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function body_class( $classes ){
            if( is_user_logged_in() ){
                $vendor = yith_get_vendor( 'current', 'user' );
                if( $vendor->is_valid() ){
                    $classes[] = 'yith_wcmv_user_is_vendor';
                }

                else {
                    $classes[] = 'yith_wcmv_user_is_not_vendor';
                }
            }
            return $classes;
        }

        /**
         * Support to single vendor sidebar for YITH FW 2.0 theme
         *
         * @param $is_product_taxonomy
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return  bool
         */
        public function show_sidebar_in_vendor_store_page( $is_product_taxonomy ){
            if( YITH_Vendors()->frontend->is_vendor_page() ){
                $is_product_taxonomy = true;
            }

            return $is_product_taxonomy;
        }
    }
}

