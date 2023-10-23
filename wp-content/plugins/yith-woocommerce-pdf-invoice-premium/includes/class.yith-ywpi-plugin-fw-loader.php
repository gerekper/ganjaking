<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase, WordPress.Files.FileName.InvalidClassFileName
/**
 * Class to load the plugin-fw
 *
 * @package YITH\PDF_Invoice\Classes
 * @since   1.0.0
 * @author  YITH <plugins@yithemes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_YWPI_Plugin_FW_Loader' ) ) {
	/**
	 * YITH_YWPI_Plugin_FW_Loader class
	 */
	class YITH_YWPI_Plugin_FW_Loader {

		/**
		 * Panel Object
		 *
		 * @var $panel
		 */
		protected $panel;

		/**
		 * YITH WooCommerce Pdf invoice panel page
		 *
		 * @var string
		 */
		protected $panel_page = 'yith_woocommerce_pdf_invoice_panel';

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
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			// Add stylesheets and scripts files.
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			add_action( 'before_woocommerce_init', array( $this, 'declare_wc_features_support' ) );

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
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {
			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs = array(
				'documents_type' => array(
					'title' => __( 'Invoices', 'yith-woocommerce-pdf-invoice' ),
					'icon'  => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"></path></svg>',
				),
				'settings'       => array(
					'title' => __( 'Settings', 'yith-woocommerce-pdf-invoice' ),
					'icon'  => 'settings',
				),
				'template'       => array(
					'title' => _x( 'PDF Templates', 'admin tab title', 'yith-woocommerce-pdf-invoice' ),
					'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="23px" height="20px" viewBox="0 0 23 20" enable-background="new 0 0 23 20" xml:space="preserve"><path fill="#94A2B8" d="M17.2,20H5.8C2.602,20,0,17.398,0,14.2V5.8C0,2.602,2.602,0,5.8,0h11.4C20.398,0,23,2.602,23,5.8v8.4 C23,17.398,20.398,20,17.2,20z M5.8,1.6c-2.316,0-4.2,1.884-4.2,4.2v8.4c0,2.315,1.884,4.2,4.2,4.2h11.4c2.315,0,4.2-1.885,4.2-4.2 V5.8c0-2.316-1.885-4.2-4.2-4.2H5.8z M6.438,9.549V8.07h0.297c0.408,0,0.612,0.247,0.612,0.74c0,0.255-0.053,0.442-0.157,0.561 C7.085,9.489,6.933,9.549,6.735,9.549H6.438z M4.856,6.931V13h1.581v-2.313H6.99c0.651,0,1.146-0.156,1.483-0.471 C8.811,9.902,8.979,9.43,8.979,8.801c0-0.266-0.034-0.513-0.102-0.739S8.703,7.638,8.558,7.471S8.231,7.172,8.01,7.076 C7.789,6.979,7.528,6.931,7.228,6.931H4.856z M9.575,6.931V13h2.295c0.459,0,0.816-0.084,1.07-0.251 c0.256-0.167,0.447-0.391,0.574-0.671c0.127-0.281,0.205-0.604,0.234-0.969c0.027-0.366,0.042-0.747,0.042-1.144 c0-0.396-0.015-0.777-0.042-1.143c-0.029-0.365-0.107-0.688-0.234-0.969c-0.127-0.281-0.318-0.504-0.574-0.672 c-0.254-0.167-0.611-0.25-1.07-0.25H9.575z M11.156,11.861V8.07h0.314c0.154,0,0.275,0.021,0.366,0.064s0.16,0.132,0.208,0.268 c0.049,0.136,0.08,0.329,0.094,0.578c0.014,0.25,0.021,0.578,0.021,0.986c0,0.408-0.008,0.737-0.021,0.986 c-0.014,0.25-0.045,0.442-0.094,0.578c-0.048,0.137-0.117,0.226-0.208,0.268c-0.091,0.043-0.212,0.064-0.366,0.064H11.156z M14.548,6.931V13h1.582v-2.499h1.887V9.26H16.13V8.223h2.014V6.931H14.548z"/></svg>',
				),
			);

			if ( YITH_Electronic_Invoice()->enable === 'yes' ) {
				$admin_tabs['electronic-invoice'] = array(
					'title'       => __( 'Electronic Invoice', 'yith-woocommerce-pdf-invoice' ),
					'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="23px" height="20px" viewBox="0 0 23 20" enable-background="new 0 0 23 20" xml:space="preserve"><path fill="#94A2B8" d="M17.2,20H5.8C2.602,20,0,17.398,0,14.2V5.8C0,2.602,2.602,0,5.8,0h11.4C20.398,0,23,2.602,23,5.8v8.4 C23,17.398,20.398,20,17.2,20z M5.8,1.6c-2.316,0-4.2,1.884-4.2,4.2v8.4c0,2.315,1.884,4.2,4.2,4.2h11.4c2.315,0,4.2-1.885,4.2-4.2 V5.8c0-2.316-1.885-4.2-4.2-4.2H5.8z M9.374,6.931V13h3.672v-1.292h-2.091v-1.207h1.887V9.26h-1.887V8.223h2.015V6.931H9.374z"/></svg>',
					'description' => __( 'Configure the settings for the electronic invoice.', 'yith-woocommerce-pdf-invoice' ),
				);
			}

			/**
			 * APPLY_FILTERS: yith_ywpi_settings_panel_capability
			 *
			 * Filter the settings panel capability.
			 *
			 * @param string the capability. Default: manage_options.
			 *
			 * @return string
			 */
			$args = array(
				'ui_version'       => 2,
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => 'YITH WooCommerce PDF Invoices & Packing Slips',
				'menu_title'       => 'PDF Invoices & Packing Slips',
				'capability'       => apply_filters( 'yith_ywpi_settings_panel_capability', 'manage_options' ),
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'plugin_slug'      => YITH_YWPI_SLUG,
				'plugin-url'       => YITH_YWPI_URL,
				'page'             => $this->panel_page,
				'admin-tabs'       => apply_filters( 'yith_ywpi_admin_tabs', $admin_tabs ),
				'options-path'     => YITH_YWPI_DIR . '/plugin-options',
				'class'            => yith_set_wrapper_class(),
				'is_premium'       => defined( 'YITH_YWPI_PREMIUM' ),
				'your_store_tools' => array(
					'items' => array(
						'gift-cards'             => array(
							'name'           => 'Gift Cards',
							'icon_url'       => YITH_YWPI_URL . 'assets/images/plugins/gift-cards.svg',
							'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-gift-cards/',
							'description'    => _x(
								'Sell gift cards in your shop to increase your earnings and attract new customers.',
								'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Gift Cards',
								'yith-woocommerce-pdf-invoice'
							),
							'is_active'      => defined( 'YITH_YWGC_PREMIUM' ),
							'is_recommended' => true,
						),
						'ajax-product-filter'    => array(
							'name'           => 'Ajax Product Filter',
							'icon_url'       => YITH_YWPI_URL . 'assets/images/plugins/ajax-product-filter.svg',
							'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-ajax-product-filter/',
							'description'    => _x(
								'Help your customers to easily find the products they are looking for and improve the user experience of your shop.',
								'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Ajax Product Filter',
								'yith-woocommerce-pdf-invoice'
							),
							'is_active'      => defined( 'YITH_WCAN_PREMIUM' ),
							'is_recommended' => true,
						),
						'booking'                => array(
							'name'           => 'Booking and Appointment',
							'icon_url'       => YITH_YWPI_URL . 'assets/images/plugins/booking.svg',
							'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-booking/',
							'description'    => _x(
								'Enable a booking/appointment system to manage renting or booking of services, rooms, houses, cars, accommodation facilities and so on.',
								'[YOUR STORE TOOLS TAB] Description for plugin YITH Bookings',
								'yith-woocommerce-pdf-invoice'
							),
							'is_active'      => defined( 'YITH_WCBK_PREMIUM' ),
							'is_recommended' => false,

						),
						'request-a-quote'        => array(
							'name'           => 'Request a Quote',
							'icon_url'       => YITH_YWPI_URL . 'assets/images/plugins/request-a-quote.svg',
							'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-request-a-quote/',
							'description'    => _x(
								'Hide prices and/or the "Add to cart" button and let your customers request a custom quote for every product.',
								'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Request a Quote',
								'yith-woocommerce-pdf-invoice'
							),
							'is_active'      => defined( 'YITH_YWRAQ_PREMIUM' ),
							'is_recommended' => false,
						),
						'product-addons'         => array(
							'name'           => 'Product Add-Ons & Extra Options',
							'icon_url'       => YITH_YWPI_URL . 'assets/images/plugins/product-add-ons.svg',
							'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-product-add-ons/',
							'description'    => _x(
								'Add paid or free advanced options to your product pages using fields like radio buttons, checkboxes, drop-downs, custom text inputs, and more.',
								'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Product Add-Ons',
								'yith-woocommerce-pdf-invoice'
							),
							'is_active'      => defined( 'YITH_WAPO_PREMIUM' ),
							'is_recommended' => false,
						),
						'dynamic-pricing'        => array(
							'name'           => 'Dynamic Pricing and Discounts',
							'icon_url'       => YITH_YWPI_URL . 'assets/images/plugins/dynamic-pricing-and-discounts.svg',
							'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-dynamic-pricing-and-discounts/',
							'description'    => _x(
								'Increase conversions through dynamic discounts and price rules, and build powerful and targeted offers.',
								'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Dynamic Pricing and Discounts',
								'yith-woocommerce-pdf-invoice'
							),
							'is_active'      => defined( 'YITH_YWDPD_PREMIUM' ),
							'is_recommended' => false,
						),
						'customize-my-account'   => array(
							'name'           => 'Customize My Account Page',
							'icon_url'       => YITH_YWPI_URL . 'assets/images/plugins/customize-myaccount-page.svg',
							'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-customize-my-account-page/',
							'description'    => _x(
								'Customize the My Account page of your customers by creating custom sections with promotions and ad-hoc content based on your needs.',
								'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Customize My Account',
								'yith-woocommerce-pdf-invoice'
							),
							'is_active'      => defined( 'YITH_WCMAP_PREMIUM' ),
							'is_recommended' => false,
						),
						'recover-abandoned-cart' => array(
							'name'           => 'Recover Abandoned Cart',
							'icon_url'       => YITH_YWPI_URL . 'assets/images/plugins/recover-abandoned-cart.svg',
							'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-recover-abandoned-cart/',
							'description'    => _x(
								'Contact users who have added products to the cart without completing the order and try to recover lost sales.',
								'[YOUR STORE TOOLS TAB] Description for plugin Recover Abandoned Cart',
								'yith-woocommerce-pdf-invoice'
							),
							'is_active'      => defined( 'YITH_YWRAC_PREMIUM' ),
							'is_recommended' => false,
						),
					),
				),
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

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Retrieve panel page
		 *
		 * @return string
		 */
		public function get_panel_page() {
			return $this->panel_page;
		}

		/**
		 * Declare support for WooCommerce features.
		 */
		public function declare_wc_features_support() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', YITH_YWPI_INIT, true );
			}
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
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once 'plugin-fw/lib/yit-upgrade.php';
			}

			YIT_Upgrade()->register( YITH_YWPI_SLUG, YITH_YWPI_INIT );
		}
	}
}
