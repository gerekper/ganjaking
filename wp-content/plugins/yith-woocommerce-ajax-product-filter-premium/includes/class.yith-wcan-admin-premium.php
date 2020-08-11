<?php
/**
 * Admin class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Admin_Premium' ) ) {
    /**
     * Admin class.
     * The class manage all the admin behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WCAN_Admin_Premium extends YITH_WCAN_Admin {

        /**
         * Construct
         *
         * @param $version Plugin version
         */
        public function __construct( $version ) {
            parent::__construct( YITH_WCAN_VERSION );

            /* Admin Panel */
            add_filter( 'yith_wcan_settings_tabs', array( $this, 'add_settings_tabs' ) );
            add_action( 'yith_wcan_after_option_panel', array( $this, 'premium_panel_init' ), 10, 1 );

            /* Register plugin to licence/update system */
            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

            /* Premium Options */
            add_filter( 'yith_wcan_panel_frontend_options', array( $this, 'panel_frontend_options' ) );
            add_filter( 'yith_wcan_panel_settings_options', array( $this, 'panel_settings_options' ) );
        }

        /**
         * Add a panel under YITH Plugins tab
         *
         * @param $tabs Tabs array
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use     /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function add_settings_tabs( $tabs ) {
            unset( $tabs['premium'] );

            $to_add = array(
                'general'           => __( 'Settings', 'yith-woocommerce-ajax-navigation' ),
                'seo'               => __( 'SEO', 'yith-woocommerce-ajax-navigation' ),
            );

            return array_merge( $tabs, $to_add );
        }
        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_activation() {
        	if( function_exists( 'YIT_Plugin_Licence' ) ){
		        YIT_Plugin_Licence()->register( YITH_WCAN_INIT, YITH_WCAN_SECRET_KEY, YITH_WCAN_SLUG );
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
        	if( function_exists( 'YIT_Upgrade' ) ){
		        YIT_Upgrade()->register( YITH_WCAN_SLUG, YITH_WCAN_INIT );
	        }
        }

        /**
         * Create default panel option db record
         *
         * @param $args The panel args array
         *
         * @return   void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function premium_panel_init( $args ) {
            $panel_obj = YITH_WCAN()->admin->_panel;
            is_object( $panel_obj ) && ! get_option( YITH_WCAN()->admin->_main_panel_option ) && add_option( YITH_WCAN()->admin->_main_panel_option, YITH_WCAN()->admin->_panel->get_default_options() );
        }

        /**
         * Add Premium Frontend Options
         *
         * @param $options the standard options array
         *
         * @return   void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function panel_frontend_options( $options ){
            $options['frontend']['settings'][] = array( 'type' => 'open' );

            $options['frontend']['settings'][] = array(
                'name'    => _x( 'Enable Scroll up to top', '[Admin] Option name', 'yith-woocommerce-ajax-navigation' ),
                'desc'    => __( 'Select whether you want to enable the "Scroll up to top" option on Desktop, Mobile, or on both of them', 'yith-woocommerce-ajax-navigation' ),
                'id'      => 'yith_wcan_scroll_top_mode',
                'type'    => 'select',
                'options' => array(
                    'disabled' => __( 'Disabled', 'yith-woocommerce-ajax-navigation' ),
                    'mobile'   => __( 'Mobile', 'yith-woocommerce-ajax-navigation' ),
                    'desktop'  => __( 'Desktop', 'yith-woocommerce-ajax-navigation' ),
                    'both'     => __( 'Mobile and Desktop', 'yith-woocommerce-ajax-navigation' ),
                ),
                'std'     => 'mobile'
            );

            $options['frontend']['settings'][] =  array(
                'name' => __( 'Widget Title Tag', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Enter here the HTML tag for the widget title', 'yith-woocommerce-ajax-navigation' ) . ' (Default: <strong>h3.widget-title</strong>)',
                'id'   => 'yith_wcan_ajax_widget_title_class',
                'type' => 'text',
                'std'  => 'h3.widget-title'
            );

            $options['frontend']['settings'][] =  array(
                'name' => __( 'Widget Wrapper Tag', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Enter here the HTML tag for the widget wrapper', 'yith-woocommerce-ajax-navigation' ) . ' (Default: <strong>.widget</strong>)',
                'id'   => 'yith_wcan_ajax_widget_wrapper_class',
                'type' => 'text',
                'std'  => '.widget'
            );

            $options['frontend']['settings'][] = array(
                'name'    => __( 'Order by', 'yith-woocommerce-ajax-navigation' ),
                'desc'    => __( 'Sort by number of products contained or alphabetically', 'yith-woocommerce-ajax-navigation' ),
                'id'      => 'yith_wcan_ajax_shop_terms_order',
                'type'    => 'select',
                'options' => array(
                    'product'       => __( 'Number of products', 'yith-woocommerce-ajax-navigation' ),
                    'alphabetical'  => __( 'Alphabetically', 'yith-woocommerce-ajax-navigation' ),
                    'menu_order'    => __( 'WooCommerce Default', 'yith-woocommerce-ajax-navigation' )
                ),
                'std'     => 'alphabetical'
            );

            $options['frontend']['settings'][] = array(
                'name'    => __( 'Filter style', 'yith-woocommerce-ajax-navigation' ),
                'desc'    => __( 'Select the filter style', 'yith-woocommerce-ajax-navigation' ),
                'id'      => 'yith_wcan_ajax_shop_filter_style',
                'type'    => 'select',
                'options' => array(
                    'standard'      => __( '"x" icon before activated filter', 'yith-woocommerce-ajax-navigation' ),
                    'checkboxes'    => __( 'Checkboxes', 'yith-woocommerce-ajax-navigation' )
                ),
                'std'     => 'standard'
            );

            $options['frontend']['settings'][] = array( 'type' => 'close' );

            return $options;
        }

        /**
         * Add Premium General Options
         *
         * @param $options the standard options array
         *
         * @return   void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function panel_settings_options( $options ){
            $options['general']['settings'][] = array( 'type' => 'open' );

            $options['general']['settings'][] =  array(
                'name' => __( 'Enable ajax shop pagination', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Enable AJAX WooCommerce pagination', 'yith-woocommerce-ajax-navigation' ),
                'id'   => 'yith_wcan_enable_ajax_shop_pagination',
                'type' => 'on-off',
                'std'  => 'no',
            );

            $options['general']['settings'][] =  array(
                'name' => __( 'Shop Pagination Container Anchor', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Enter here the HTML tag for the shop pagination anchor', 'yith-woocommerce-ajax-navigation' ) . ' (Default: <strong>a.page-numbers</strong>)',
                'id'   => 'yith_wcan_ajax_shop_pagination_anchor_class',
                'type' => 'text',
                'std'  => 'a.page-numbers',
                'deps' => array(
                    'ids'     => 'yith_wcan_enable_ajax_shop_pagination',
                    'values'  => 'yes'
                )
            );

            $options['general']['settings'][] = array(
                'name' => __( 'Show current categories', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'This option allows whether to show or hide the current category when you are on it. For example, if I am on “Jeans” category page, the "Jeans" filter is automatically hidden.', 'yith-woocommerce-ajax-navigation' ),
                'id'   => 'yith_wcan_show_current_categories_link',
                'type' => 'on-off',
                'std'  => 'no',
            );

            $options['general']['settings'][] = array(
                'name' => __( 'Show "All Categories" link', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Add a link "See all categories" after a filter is applied', 'yith-woocommerce-ajax-navigation' ),
                'id'   => 'yith_wcan_enable_see_all_categories_link',
                'type' => 'on-off',
                'std'  => 'no',
            );

            $options['general']['settings'][] = array(
                'name' => __( '"All Categories" link text', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Enter here the text for the link "See all categories"', 'yith-woocommerce-ajax-navigation' ),
                'id'   => 'yith_wcan_enable_see_all_categories_link_text',
                'type' => 'text',
                'std'  => __( 'See all categories', 'yith-woocommerce-ajax-navigation' ),
                'deps' => array(
                    'ids'    => 'yith_wcan_enable_see_all_categories_link',
                    'values' => 'yes'
                )
            );

            $options['general']['settings'][] = array(
                'name' => __( 'Show "All Tags" link', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Add a link "See all tags" after a filter is applied', 'yith-woocommerce-ajax-navigation' ),
                'id'   => 'yith_wcan_enable_see_all_tags_link',
                'type' => 'on-off',
                'std'  => 'no',
            );

            $options['general']['settings'][] = array(
                'name' => __( '"All Tags" link text', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Text for "See all tags" link', 'yith-woocommerce-ajax-navigation' ),
                'id'   => 'yith_wcan_enable_see_all_tags_link_text',
                'type' => 'text',
                'std'  => __( 'See all tags', 'yith-woocommerce-ajax-navigation' ),
                'deps' => array(
                    'ids'    => 'yith_wcan_enable_see_all_tags_link',
                    'values' => 'yes'
                )
            );

            $options['general']['settings'][] = array(
                'name' => __( 'Enable Hierarchical Management for Product Tags', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Hack the standard WooCommerce non-hierarchical product tags', 'yith-woocommerce-ajax-navigation' ),
                'id'   => 'yith_wcan_enable_hierarchical_tags_link',
                'type' => 'on-off',
                'std'  => 'no',
            );

            $options['general']['settings'][] = array( 'type' => 'close' );

            return $options;
        }

	    public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCAN_INIT' ) {
		    $new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

		    if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ){
			    $new_row_meta_args['is_premium'] = true;
		    }

		    return $new_row_meta_args;
	    }

	    public function action_links( $links ) {
		    $links = yith_add_action_links( $links, $this->_panel_page, true );
		    return $links;
	    }

    }
}
