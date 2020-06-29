<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCMS_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Multistep_Checkout_Admin
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Andrea Grillo <andrea.grillo@yithemes.com>
 *
 */

if ( ! class_exists( 'YITH_Multistep_Checkout_Admin' ) ) {
	/**
	 * Class YITH_Multistep_Checkout_Admin
	 *
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 */
	class YITH_Multistep_Checkout_Admin {

        /**
         * @var Panel object
         */
        protected $_panel = null;


        /**
         * @var Panel page
         */
        protected $_panel_page = 'yith_wcms_panel';

        /**
         * @var bool Show the premium landing page
         */
        public $show_premium_landing = true;

         /**
         * @var string Official plugin documentation
         */
        protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-multi-step-checkout/';

        /**
         * @var string Official plugin landing page
         */
        protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-multi-step-checkout/';

        /**
         * @var string Official plugin landing page
         */
        protected $_premium_live = 'https://plugins.yithemes.com/yith-woocommerce-multi-step-checkout/';

		/**
		 * @var string Official plugin support page
		 */
		protected $_support = 'https://wordpress.org/support/plugin/yith-woocommerce-multi-step-checkout';

        /**
         * Construct
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.0
         */
        public function __construct(){
            /* === Register Panel Settings === */
            add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

            /* === Premium Tab === */
            add_action( 'yith_wcms_premium_tab', array( $this, 'show_premium_landing' ) );

            /* === Show Plugin Information === */
	        add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCMS_PATH . '/' . basename( YITH_WCMS_FILE ) ), array( $this, 'action_links' ) );
	        add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
        }

        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use     /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function register_panel() {

            if ( ! empty( $this->_panel ) ) {
                return;
            }

	        $page_title         = __( 'YITH WooCommerce Multi-step Checkout', 'yith-woocommerce-multi-step-checkout' );
	        $menu_title         = __( 'Multi-step Checkout', 'yith-woocommerce-multi-step-checkout' );
	        $plugin_description = __( 'Split checkout process into more steps', 'yith-woocommerce-multi-step-checkout' );


            $admin_tabs = apply_filters( 'yith_wcms_admin_tabs', array(
                    'settings'      => __( 'Settings', 'yith-woocommerce-multi-step-checkout' ),
                )
            );

            if( $this->show_premium_landing ){
                $admin_tabs['premium'] = __( 'Premium Version', 'yith-woocommerce-multi-step-checkout' );
            }

	        $args = array(
		        'create_menu_page'   => true,
		        'parent_slug'        => '',
		        'page_title'         => $page_title,
		        'menu_title'         => $menu_title,
		        'plugin_description' => $plugin_description,
		        'capability'         => 'manage_options',
		        'parent'             => '',
		        'parent_page'        => 'yit_plugin_panel',
		        'page'               => $this->_panel_page,
		        'admin-tabs'         => $admin_tabs,
		        'options-path'       => YITH_WCMS_OPTIONS_PATH,
		        'class'              => yith_set_wrapper_class(),
	        );


            /* === Fixed: not updated theme/old plugin framework  === */
            if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
                require_once( 'plugin-fw/lib/yit-plugin-panel-wc.php' );
            }

            $this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
        }

        /**
         * Show the premium landing
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.0.0
         * @return void
         */
        public function show_premium_landing(){
            if( file_exists( YITH_WCMS_TEMPLATE_PATH . 'premium.php' )&& $this->show_premium_landing ){
                require_once( YITH_WCMS_TEMPLATE_PATH . 'premium.php' );
            }
        }

         /**
         * Get the premium landing uri
         *
         * @since   1.0.0
         * @author  Andrea Grillo <andrea.grillo@yithemes.com>
         * @return  string The premium landing link
         */
        public function get_premium_landing_uri() {
            return $this->_premium_landing;
        }

        /**
         * Get the panle page id
         *
         * @since   1.2.1
         * @author  Andrea Grillo <andrea.grillo@yithemes.com>
         * @return  string The premium landing link
         */
        public function get_panel_page(){
            return $this->_panel_page;
        }

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @since    1.6.5
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return mixed
		 * @use      plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->_panel_page, false );
			return $links;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $to_add
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @return   Array
		 * @since    1.6.5
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCMS_FREE_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug'] = YITH_WCMS_SLUG;
			}

			return $new_row_meta_args;
		}
    }
}