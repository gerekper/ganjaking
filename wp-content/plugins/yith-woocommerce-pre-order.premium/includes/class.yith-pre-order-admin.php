<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if (!defined('YITH_WCPO_VERSION')) {
    exit('Direct access forbidden.');
}

/**
 *
 *
 * @class      YITH_Pre_Order_Admin
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if (!class_exists('YITH_Pre_Order_Admin')) {
    /**
     * Class YITH_Pre_Order_Admin
     *
     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
     */
    class YITH_Pre_Order_Admin {

	    /**
	     * @var Panel object
	     */
	    protected $_panel = null;

	    /**
	     * @var Panel page
	     */
	    protected $_panel_page = 'yith_wcpo_panel';

	    /**
	     * @var bool Show the premium landing page
	     */
	    public $show_premium_landing = true;

	    /**
	     * @var string Official plugin documentation
	     */
	    protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-pre-order/';

	    /**
	     * @var string Official plugin landing page
	     */
	    protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-pre-order/';

	    /**
	     * @var string Official plugin landing page
	     */
	    protected $_premium_live = 'http://plugins.yithemes.com/yith-woocommerce-pre-order/';

	    /**
	     * Single instance of the class
	     *
	     * @since 1.0.0
	     */
	    protected static $instance;

        /**
         * @var $_edit_product_page YITH_Pre_Order_Admin Edit product page object
         */
        protected $_edit_product_page;

	    /**
	     * Returns single instance of the class
	     *
	     * @since 1.0.0
	     */
	    public static function get_instance() {
		    if ( is_null( self::$instance ) ) {
			    self::$instance = new self();
		    }

		    return self::$instance;
	    }

        /**
         * Construct
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0
         */
        public function __construct() {
	        /* === Register Panel Settings === */
	        add_action('admin_menu', array($this, 'register_panel'), 5);
	        /* === Premium Tab === */
	        add_action( 'yith_ywpo_pre_order_premium_tab', array( $this, 'show_premium_landing' ) );


	        /* === Show Plugin Information === */
	        add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCPO_PATH . '/' . basename( YITH_WCPO_FILE ) ), array( $this, 'action_links' ) );
	        add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

            require_once( YITH_WCPO_PATH . 'includes/class.yith-pre-order-edit-product-page.php' );

            $this->instantiate_edit_product_page();
            
            add_filter( 'views_edit-shop_order', array( $this, 'add_pre_ordered_orders_page_view' ) );
            add_action( 'pre_get_posts', array( $this, 'filter_order_for_view' ) );
        }

	    /**
	     * Add a panel under YITH Plugins tab
	     *
	     * @return   void
	     * @since    1.0
	     * @author   Andrea Grillo <andrea.grillo@yithemes.com>
	     * @use      /Yit_Plugin_Panel class
	     * @see      plugin-fw/lib/yit-plugin-panel.php
	     */
	    public function register_panel() {

		    if ( ! empty( $this->_panel ) ) {
			    return;
		    }

		    $menu_title = 'Pre-Order';

		    $admin_tabs = apply_filters( 'yith_wcpo_admin_tabs',
			    array( 'settings' => esc_html__( 'Settings', 'yith-pre-order-for-woocommerce' ) ) );

		    if ( $this->show_premium_landing ) {
			    $admin_tabs['premium'] = esc_html__( 'Premium Version', 'yith-pre-order-for-woocommerce' );
		    }

		    $args = array(
			    'create_menu_page' => true,
			    'parent_slug'      => '',
			    'plugin_slug'      => YITH_WCPO_SLUG,
			    'page_title'       => $menu_title,
			    'menu_title'       => $menu_title,
			    'capability'       => 'manage_options',
			    'parent'           => '',
			    'parent_page'      => 'yith_plugin_panel',
			    'page'             => $this->_panel_page,
			    'admin-tabs'       => $admin_tabs,
			    'class'            => yith_set_wrapper_class(),
			    'options-path'     => YITH_WCPO_OPTIONS_PATH,
			    'links'            => $this->get_sidebar_link()
		    );


		    /* === Fixed: not updated theme/old plugin framework  === */
		    if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
			    require_once( YITH_WCPO_PATH . '/plugin-fw/lib/yit-plugin-panel-wc.php' );
		    }

		    $this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
	    }

	    /**
	     * Premium Tab Template
	     *
	     * Load the premium tab template on admin page
	     *
	     * @return   void
	     * @since    1.0
	     * @author   Andrea Grillo <andrea.grillo@yithemes.com>
	     * @return void
	     */
	    public function show_premium_landing() {
		    if( file_exists( YITH_WCPO_TEMPLATE_PATH . 'admin/premium_tab.php' ) && $this->show_premium_landing ){
			    require_once( YITH_WCPO_TEMPLATE_PATH . 'admin/premium_tab.php' );
		    }
	    }

	    /**
	     * Get the premium landing uri
	     *
	     * @since   1.0.0
	     * @author   Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
	     * @return  string The premium landing link
	     */
	    public function get_premium_landing_uri() {
		    return $this->_premium_landing;
	    }

	    public function action_links( $links ) {
		    $links = yith_add_action_links( $links, $this->_panel_page, false );
		    return $links;
	    }

	    public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCPO_FREE_INIT' ) {
		    if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
			    $new_row_meta_args['slug'] = YITH_WCPO_SLUG;
		    }

		    return $new_row_meta_args;
	    }

	    /**
	     * Sidebar links
	     *
	     * @return   array The links
	     * @since    1.2.1
	     * @author   Andrea Grillo <andrea.grillo@yithemes.com>
	     */
	    public function get_sidebar_link() {
		    $links = array(
			    array(
				    'title' => esc_html__( 'Plugin documentation', 'yith-pre-order-for-woocommerce' ),
				    'url'   => $this->_official_documentation,
			    ),
			    array(
				    'title' => esc_html__( 'Help Center', 'yith-pre-order-for-woocommerce' ),
				    'url'   => 'http://support.yithemes.com/hc/en-us/categories/202568518-Plugins',
			    ),
		    );

		    if ( defined( 'YITH_WCPO_FREE_INIT' ) ) {
			    $links[] = array(
				    'title' => esc_html__( 'Discover the premium version', 'yith-pre-order-for-woocommerce' ),
				    'url'   => $this->_premium_landing,
			    );

			    $links[] = array(
				    'title' => esc_html__( 'Free Vs Premium', 'yith-pre-order-for-woocommerce' ),
				    'url'   => 'https://yithemes.com/themes/plugins/yith-woocommerce-pre-order/#tab-free_vs_premium_tab',
			    );

			    $links[] = array(
				    'title' => esc_html__( 'Premium live demo', 'yith-pre-order-for-woocommerce' ),
				    'url'   => $this->_premium_live
			    );

			    $links[] = array(
				    'title' => esc_html__( 'WordPress support forum', 'yith-pre-order-for-woocommerce' ),
				    'url'   => 'https://wordpress.org/plugins/yith-woocommerce-pre-order/',
			    );

			    $links[] = array(
				    'title' => sprintf( '%s (%s %s)', esc_html__( 'Changelog', 'yith-pre-order-for-woocommerce' ), esc_html__( 'current version', 'yith-pre-order-for-woocommerce' ), YITH_WCPO_VERSION ),
				    'url'   => 'https://yithemes.com/docs-plugins/yith-woocommerce-pre-order/06-changelog-free.html',
			    );
		    }

		    if ( defined( 'YITH_WCPO_PREMIUM' ) ) {
			    $links[] = array(
				    'title' => esc_html__( 'Support platform', 'yith-pre-order-for-woocommerce' ),
				    'url'   => 'https://yithemes.com/my-account/support/dashboard/',
			    );

			    $links[] = array(
				    'title' => sprintf( '%s (%s %s)', esc_html__( 'Changelog', 'yith-pre-order-for-woocommerce' ), esc_html__( 'current version', 'yith-pre-order-for-woocommerce' ), YITH_WCPO_VERSION ),
				    'url'   => 'https://yithemes.com/docs-plugins/yith-woocommerce-pre-order/07-changelog-premium.html',
			    );
		    }

		    return $links;
	    }

        public function instantiate_edit_product_page() {
            return $this->_edit_product_page = new YITH_Pre_Order_Edit_Product_Page();
        }


        public function add_pre_ordered_orders_page_view( $views ) {
            $order_statuses = wc_get_order_statuses();
            if ( 'yes' == get_option( 'yith_wcpo_wc-completed' ) ) {
                unset( $order_statuses['wc-completed'] );
            }
            if ( 'yes' == get_option( 'yith_wcpo_wc-cancelled' ) ) {
                unset( $order_statuses['wc-cancelled'] );
            }
            if ( 'yes' == get_option( 'yith_wcpo_wc-refunded' ) ) {
                unset( $order_statuses['wc-refunded'] );
            }
            if ( 'yes' == get_option( 'yith_wcpo_wc-failed' ) ) {
                unset( $order_statuses['wc-failed'] );
            }
            $args = array(
                'post_type'   => wc_get_order_types(),
                'post_status' => array_keys( $order_statuses ),
                'numberposts' => - 1,
                'fields'      => 'ids',
                'meta_query'  => array(
                    array(
                        'key'     => '_order_has_preorder',
                        'value'   => 'yes',
                        'compare' => '='
                    )
                ));
            // Get all Pre-Order ids
            $pre_ordered_count = get_posts( $args );

            if( $pre_ordered_count ){
                $filter_url = esc_url( add_query_arg( array( 'post_type' => 'shop_order', 'pre-ordered' => true ), admin_url( 'edit.php' ) ) );
                $filter_class = isset( $_GET['pre-ordered'] ) ? 'current' : '';

                $views[ 'pre-ordered' ] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
                    $filter_url, $filter_class, esc_html__( 'Pre-Ordered', 'yith-pre-order-for-woocommerce' ), count( $pre_ordered_count ) );
            }
            return $views;
        }


        public function filter_order_for_view() {
            if( isset( $_GET['pre-ordered'] ) && $_GET['pre-ordered'] ){
                add_filter( 'posts_join', array( $this, 'filter_order_join_for_view' ) );
                add_filter( 'posts_where', array( $this, 'filter_order_where_for_view' ) );
            }
        }

        /**
         * Add joins to order view query
         *
         * @param $join string Original join query section
         * @return string filtered join query section
         * @since 1.0.0
         */
        public function filter_order_join_for_view( $join ) {
            global $wpdb;

            $join .= " LEFT JOIN {$wpdb->prefix}postmeta as i ON {$wpdb->posts}.ID = i.post_id";

            return $join;
        }

        /**
         * Add conditions to order view query
         *
         * @param $where string Original where query section
         * @return string filtered where query section
         * @since 1.0.0
         */
        public function filter_order_where_for_view( $where ) {
            global $wpdb;

            $where .= $wpdb->prepare( " AND i.meta_key = %s AND i.meta_value = %s", array( '_order_has_preorder', 'yes' ) );
            if ( 'yes' == get_option( 'yith_wcpo_wc-completed' ) ) {
                $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_status != %s", array( 'wc-completed' ) );
            }
            if ( 'yes' == get_option( 'yith_wcpo_wc-cancelled' ) ) {
                $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_status != %s", array( 'wc-cancelled' ) );
            }
            if ( 'yes' == get_option( 'yith_wcpo_wc-refunded' ) ) {
                $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_status != %s", array( 'wc-refunded' ) );
            }
            if ( 'yes' == get_option( 'yith_wcpo_wc-failed' ) ) {
                $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_status != %s", array( 'wc-failed' ) );
            }

            return $where;
        }

    }
}