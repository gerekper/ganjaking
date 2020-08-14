<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWBC_Plugin_FW_Loader' ) ) {

	/**
	 * Implements features related to an invoice document
	 *
	 * @class   YWBC_Plugin_FW_Loader
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YWBC_Plugin_FW_Loader {

		/**
		 * @var $_panel Panel Object
		 */
		protected $_panel;

		/**
		 * @var $_premium string Premium tab template file name
		 */
		protected $_premium = 'premium.php';

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-barcodes-and-qr-codes/';

		/**
		 * @var string Plugin official documentation
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-barcodes/';

        /**
         * @var string Official plugin landing page
         */
        protected $_premium_live = 'https://plugins.yithemes.com/yith-woocommerce-barcodes/';

        /**
         * @var string Official plugin support page
         */
        protected $_support = 'https://yithemes.com/my-account/support/dashboard/';

		/**
		 * @var string Plugin panel page
		 */
		protected $_panel_page = 'yith_woocommerce_barcodes_panel';

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

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

		public function __construct() {
			/**
			 * Register actions and filters to be used for creating an entry on YIT Plugin menu
			 */
			add_action( 'admin_init', array( $this, 'register_pointer' ) );

			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			//  Add stylesheets and scripts files
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			//  Show plugin premium tab
			add_action( 'yith_question_answer_premium', array( $this, 'premium_tab' ) );

            /* === Show Plugin Information === */

            add_filter( 'plugin_action_links_' . plugin_basename( YITH_YWBC_DIR . '/' . basename( YITH_YWBC_FILE ) ), array(
                $this,
                'action_links',
            ) );

            add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			/**
			 * register plugin to licence/update system
			 */
			$this->licence_activation();

			/**
			 * add custom woocommerce field "apply-barcodes"
			 *
			 */
			add_action( 'woocommerce_admin_field_apply-barcodes', array( $this, 'show_apply_barcodes_field' ) );

            /**
             * add custom woocommerce field "print-barcodes"
             *
             */
            add_action( 'woocommerce_admin_field_print-barcodes', array( $this, 'show_print_barcodes_field' ) );

			/**
			 * add custom woocommerce field "print-barcodes"
			 *
			 */
			add_action( 'woocommerce_admin_field_print-barcodes-by-products', array( $this, 'show_print_barcodes_by_products_field' ) );
		}

		/**
		 * Load YIT core plugin
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( !defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( !empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
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

			$admin_tabs['product'] = esc_html__( 'Product barcodes', 'yith-woocommerce-barcodes' );
			$admin_tabs['order']   = esc_html__( 'Order barcodes', 'yith-woocommerce-barcodes' );
			$admin_tabs['print']    = esc_html__( 'Print barcodes', 'yith-woocommerce-barcodes' );
			$admin_tabs['shortcode']    = esc_html__( 'Shortcodes', 'yith-woocommerce-barcodes' );

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => 'Barcodes',
				'menu_title'       => 'Barcodes',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_YWBC_DIR . '/plugin-options',
                'class'            => yith_set_wrapper_class(),
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {

				require_once( 'plugin-fw/lib/yit-plugin-panel-wc.php' );
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
		public function premium_tab() {
			$premium_tab_template = YITH_YWBC_TEMPLATES_DIR . '/admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once( $premium_tab_template );
			}
		}

		public function register_pointer() {
			if ( ! class_exists( 'YIT_Pointers' ) ) {
				include_once( 'plugin-fw/lib/yit-pointers.php' );
			}

			$premium_message = defined( 'YITH_YWBC_PREMIUM' )
				? ''
				: esc_html__( 'YITH WooCommerce Barcodes is available in an outstanding PREMIUM version with many new options, discover it now.', 'yith-woocommerce-barcodes' ) .
				  ' <a href="' . $this->get_premium_landing_uri() . '">' . esc_html__( 'Premium version', 'yith-woocommerce-barcodes' ) . '</a>';

			$args[] = array(
				'screen_id'  => 'plugins',
				'pointer_id' => 'yith_woocommerce_barcodes',
				'target'     => '#toplevel_page_yit_plugin_panel',
				'content'    => sprintf( '<h3> %s </h3> <p> %s </p>',
					esc_html__( 'YITH WooCommerce Barcodes', 'yith-woocommerce-barcodes' ),
					esc_html__( 'In YIT Plugins tab you can find YITH WooCommerce Barcodes options.<br> From this menu you can access all settings of YITH plugins activated.', 'yith-woocommerce-barcodes' ) . '<br>' . $premium_message
				),
				'position'   => array( 'edge' => 'left', 'align' => 'center' ),
				'init'       => defined( 'YITH_YWBC_PREMIUM' ) ? YITH_YWBC_INIT : YITH_YWBC_FREE_INIT,
			);

			YIT_Pointers()->register( $args );
		}

		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
			return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing . '?refer_id=1030585';
		}

		//region    ****    licence related methods ****

		/**
		 * Add actions to manage licence activation and updates
		 */
		public function licence_activation() {
			if ( ! defined( 'YITH_YWBC_PREMIUM' ) ) {
				return;
			}

			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_YWBC_DIR . '/plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_YWBC_DIR . '/plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_YWBC_INIT, YITH_YWBC_SECRET_KEY, YITH_YWBC_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YITH_YWBC_SLUG, YITH_YWBC_INIT );
		}
		//endregion

		/**
		 * show the custom woocommerce field
		 * @since 1.0.2
		 *
		 * @param array $option
		 */
		public function show_print_barcodes_field( $option ) {

			$option['option'] = $option;

			wc_get_template( '/admin/print-barcodes.php', $option, '', YITH_YWBC_TEMPLATE_PATH );
		}

		/**
		 * show the custom woocommerce field
		 * @since 1.0.2
		 *
		 * @param array $option
		 */
		public function show_print_barcodes_by_products_field( $option ) {
			$option['option'] = $option;

			wc_get_template( '/admin/print-barcodes-by-products.php', $option, '', YITH_YWBC_TEMPLATE_PATH );
		}


        /**
         * show the custom woocommerce field
         * @since 1.2.8
         *
         * @param array $option
         */
        public function show_apply_barcodes_field( $option ) {

            $option['option'] = $option;

            wc_get_template( '/admin/apply-barcodes.php', $option, '', YITH_YWBC_TEMPLATE_PATH );
        }

        /**
         * Action links
         *
         *
         * @return void
         * @since    1.2.3
         * @author   Daniel Sanchez <daniel.sanchez@yithemes.com>
         */
        public function action_links( $links ) {

            $links = yith_add_action_links( $links, $this->_panel_page, true );
            return $links;

        }

        /**
         * Plugin Row Meta
         *
         *
         * @return void
         * @since    1.2.3
         * @author   Daniel Sanchez <daniel.sanchez@yithemes.com>
         */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWBC_INIT' ) {

            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
                $new_row_meta_args[ 'slug' ] = YITH_YWBC_SLUG;
                $new_row_meta_args[ 'is_premium' ] = true;
            }

            return $new_row_meta_args;
        }

	}
}

YWBC_Plugin_FW_Loader::get_instance();
