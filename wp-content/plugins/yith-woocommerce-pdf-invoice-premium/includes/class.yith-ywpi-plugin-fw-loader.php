<?php // phpcs:ignore WordPress.NamingConventions.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_YWPI_Plugin_FW_Loader' ) ) {

	/**
	 * Implements features related to an invoice document
	 *
	 * @class   YITH_YWPI_Plugin_FW_Loader
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_YWPI_Plugin_FW_Loader {

		/**
		 * Panel Object
		 *
		 * @var $_panel
		 */
		protected $_panel; //phpcs:ignore

		/**
		 * Premium tab template file name
		 *
		 * @var $_premium string
		 */
		protected $_premium = 'premium.php'; //phpcs:ignore

		/**
		 * Premium version landing link
		 *
		 * @var string
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-pdf-invoice/'; //phpcs:ignore

		/**
		 * Plugin official documentation.
		 *
		 * @var string
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-pdf-invoice/'; //phpcs:ignore

		/**
		 * Official plugin landing page
		 *
		 * @var string
		 */
		protected $_premium_live = 'https://plugins.yithemes.com/yith-woocommerce-pdf-invoice/'; //phpcs:ignore

		/**
		 * Official plugin support page
		 *
		 * @var string
		 */
		protected $_support = 'https://yithemes.com/my-account/support/dashboard/'; //phpcs:ignore

		/**
		 * Yith WooCommerce Pdf invoice panel page
		 *
		 * @var string
		 */
		protected $_panel_page = 'yith_woocommerce_pdf_invoice_panel'; //phpcs:ignore

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_YWPI_Plugin_FW_Loader
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

		/**
		 * Construct function
		 *
		 * @return void
		 */
		public function __construct() {
			/**
			 * Register actions and filters to be used for creating an entry on YIT Plugin menu.
			 */
			add_action( 'admin_init', array( $this, 'register_pointer' ) );

			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			// Add stylesheets and scripts files.
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			// Show plugin premium tab.
			add_action( 'yith_pdf_invoice_premium', array( $this, 'premium_tab' ) );

			/**
			 * Register plugin to licence/update system.
			 */
			$this->licence_activation();
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
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
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
			$admin_tabs_first = array();

			$admin_tabs['documents'] = esc_html__( 'Documents Format', 'yith-woocommerce-pdf-invoice' );

			if ( defined( 'YITH_YWPI_PREMIUM' ) ) {
				$admin_tabs_first['documents_type'] = esc_html__( 'Invoices & Credit Notes', 'yith-woocommerce-pdf-invoice' );
				$admin_tabs_first['general']        = esc_html__( 'General options', 'yith-woocommerce-pdf-invoice' );
				$admin_tabs['documents_storage']    = esc_html__( 'Documents Storage', 'yith-woocommerce-pdf-invoice' );
			}

			$admin_tabs             = array_merge( $admin_tabs_first, $admin_tabs );
			$admin_tabs['template'] = esc_html__( 'Template', 'yith-woocommerce-pdf-invoice' );

			if ( YITH_Electronic_Invoice()->enable == 'yes' ) { //phpcs:ignore
				$admin_tabs['electronic-invoice'] = esc_html__( 'Electronic invoice', 'yith-woocommerce-pdf-invoice' );
			}

			if ( ! defined( 'YITH_YWPI_PREMIUM' ) ) {
				$admin_tabs['premium-landing'] = esc_html__( 'Premium Version', 'yith-woocommerce-pdf-invoice' );
			}

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => esc_html_x( 'PDF Invoices & Packing Slips', 'Plugin name in the YITH Menu', 'yith-woocommerce-pdf-invoice' ),
				'menu_title'       => esc_html_x( 'PDF Invoices', 'Plugin name in the YITH Menu', 'yith-woocommerce-pdf-invoice' ),
				'capability'       => apply_filters( 'yith_ywpi_settings_panel_capability', 'manage_options' ),
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'plugin_slug'      => YITH_YWPI_SLUG,
				'plugin-url'       => YITH_YWPI_URL,
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_YWPI_DIR . '/plugin-options',
				'class'            => yith_set_wrapper_class(),
				'help_tab'         => array(
					'main_video' => array(
						'desc' => _x( 'Check this video to learn how to <b>easily manage PDF invoices and credit notes:</b>', '[HELP TAB] Video title', 'yith-woocommerce-pdf-invoice' ),
						'url'  => array(
							'en' => 'https://www.youtube.com/embed/yq2UhK0nd7w',
							'es' => 'https://www.youtube.com/embed/l1Q3fafT7i0',
							'it' => 'https://www.youtube.com/embed/BWYikG2ljN4',
						),
					),
					'playlists'  => array(
						'en' => 'https://www.youtube.com/watch?v=yq2UhK0nd7w&list=PLDriKG-6905lm6nYUWrCintJLVzmCbuXq',
						'es' => 'https://www.youtube.com/watch?v=l1Q3fafT7i0&list=PL9Ka3j92PYJOwOqQ6606LqYqnBtduTR_G',
						'it' => 'https://www.youtube.com/watch?v=BWYikG2ljN4&list=PL9c19edGMs0-LgwfTXvce6YvB2LNMsy02',
					),
					'hc_url'     => 'https://support.yithemes.com/hc/en-us/categories/360003474958-YITH-WOOCOMMERCE-PDF-INVOICE-AND-SHIPPING-LIST',
				),
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {

				require_once 'plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

			if ( defined( 'YITH_YWPI_VERSION' ) ) {
				add_action( 'woocommerce_admin_field_ywpi_logo', array( $this->_panel, 'yit_upload' ), 10, 1 );
			}
		}

		/**
		 * Retrieve panel page
		 *
		 * @return string
		 */
		public function get_panel_page() {
			return $this->_panel_page;
		}

		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function premium_tab() {
			$premium_tab_template = YITH_YWPI_TEMPLATE_DIR . 'admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once $premium_tab_template;
			}
		}

		/**
		 * Register the pointers.
		 *
		 * @return void
		 */
		public function register_pointer() {
			if ( ! class_exists( 'YIT_Pointers' ) ) {
				include_once 'plugin-fw/lib/yit-pointers.php';
			}

			$premium_message = defined( 'YITH_YWPI_PREMIUM' )
				? ''
				: esc_html__( 'YITH WooCommerce PDF Invoices & Packing slips is available in an outstanding PREMIUM version with many new options, discover it now.', 'yith-woocommerce-pdf-invoice' ) .
				' <a href="' . $this->get_premium_landing_uri() . '">' . esc_html__( 'Premium version', 'yith-woocommerce-pdf-invoice' ) . '</a>';

			$args[] = array(
				'screen_id'  => 'plugins',
				'pointer_id' => 'yith_woocommerce_pdf_invoice',
				'target'     => '#toplevel_page_yit_plugin_panel',
				'content'    => sprintf(
					'<h3> %s </h3> <p> %s </p>',
					esc_html__( 'YITH WooCommerce PDF Invoices & Packing slips', 'yith-woocommerce-pdf-invoice' ),
					esc_html__( 'In the YITH Plugins tab, you can find YITH WooCommerce PDF Invoices & Packing slips options.<br> From this menu you can access all settings of activated YITH plugins..', 'yith-woocommerce-pdf-invoice' ) . '<br>' . $premium_message
				),
				'position'   => array(
					'edge'  => 'left',
					'align' => 'center',
				),
				'init'       => defined( 'YITH_YWPI_PREMIUM' ) ? YITH_YWPI_INIT : YITH_YWPI_FREE_INIT,
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
			return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing;
		}

		/**
		 * Add actions to manage licence activation and updates
		 */
		public function licence_activation() {
			if ( ! defined( 'YITH_YWPI_PREMIUM' ) ) {
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
				require_once 'plugin-fw/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_YWPI_INIT, YITH_YWPI_SECRET_KEY, YITH_YWPI_SLUG );
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
			YIT_Upgrade()->register( YITH_YWPI_SLUG, YITH_YWPI_INIT );
		}
	}
}
