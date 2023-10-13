<?php
/**
 * Class YITH_WCBK_Admin
 * Admin Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Admin' ) ) {
	/**
	 * YITH_WCBK_Admin class.
	 */
	class YITH_WCBK_Admin {
		use YITH_WCBK_Extensible_Singleton_Trait;

		const PANEL_PAGE = 'yith_wcbk_panel';

		/**
		 * The panel
		 *
		 * @var YIT_Plugin_Panel_WooCommerce $panel
		 */
		protected $panel;

		/**
		 * YITH_WCBK_Admin constructor.
		 */
		protected function __construct() {
			add_filter( 'admin_body_class', array( $this, 'add_classes_to_body' ) );

			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			add_action( 'yith_wcbk_print_global_availability_rules_tab', array( $this, 'print_global_availability_rules_tab' ) );
			add_action( 'yith_wcbk_print_global_price_rules_tab', array( $this, 'print_global_price_rules_tab' ) );
			add_action( 'yith_wcbk_print_logs_tab', array( $this, 'print_logs_tab' ) );

			add_filter( 'yith_plugin_fw_panel_wc_extra_row_classes', array( $this, 'add_class_to_fields_having_after_html' ), 10, 2 );
			add_action( 'yith_plugin_fw_get_field_after', array( $this, 'print_field_after_html' ), 10, 1 );

			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCBK_DIR . '/' . basename( YITH_WCBK_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 3 );

			add_filter( 'removable_query_args', array( __CLASS__, 'removable_query_args' ), 10, 2 );

			YITH_WCBK_Product_Post_Type_Admin::get_instance();
			YITH_WCBK_Tools::get_instance();

			YITH_WCBK_Booking_Calendar::get_instance();

			YITH_WCBK_Legacy_Elements::get_instance();

			$this->notices();
		}

		/**
		 * Add classes in body
		 *
		 * @param string $classes The classes.
		 *
		 * @return string
		 */
		public function add_classes_to_body( $classes ) {
			$classes .= ' yith-booking-admin';

			return $classes;
		}

		/**
		 * Action Links
		 * add the action links to plugin admin page
		 *
		 * @param array $links Plugin links.
		 *
		 * @return  array
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			return yith_add_action_links( $links, self::PANEL_PAGE, defined( 'YITH_WCBK_PREMIUM' ), YITH_WCBK_SLUG );
		}

		/**
		 * Adds action links to plugin admin page
		 *
		 * @param array    $row_meta_args Row meta args.
		 * @param string[] $plugin_meta   An array of the plugin's metadata, including the version, author, author URI, and plugin URI.
		 * @param string   $plugin_file   Path to the plugin file relative to the plugins directory.
		 *
		 * @return array
		 */
		public function plugin_row_meta( $row_meta_args, $plugin_meta, $plugin_file ) {
			$init = defined( 'YITH_WCBK_INIT' ) ? YITH_WCBK_INIT : false;
			$init = defined( 'YITH_WCBK_EXTENDED_INIT' ) ? YITH_WCBK_EXTENDED_INIT : $init;
			if ( ! ! $init && $init === $plugin_file ) {
				$row_meta_args['slug']        = YITH_WCBK_SLUG;
				$row_meta_args['is_premium']  = defined( 'YITH_WCBK_PREMIUM' );
				$row_meta_args['is_extended'] = defined( 'YITH_WCBK_EXTENDED' );
			}

			return $row_meta_args;
		}

		/**
		 * Print the Global availability rules tab
		 */
		public function print_global_availability_rules_tab() {
			include YITH_WCBK_VIEWS_PATH . 'settings-tabs/html-global-availability-rules.php';
		}

		/**
		 * Print the Global price rules tab
		 */
		public function print_global_price_rules_tab() {
			include YITH_WCBK_VIEWS_PATH . 'settings-tabs/html-global-price-rules.php';
		}

		/**
		 * Print the Logs tab
		 */
		public function print_logs_tab() {
			$logger = yith_wcbk_logger();

			if ( ! empty( $_REQUEST['yith-wcbk-logs-action'] ) ) {
				switch ( $_REQUEST['yith-wcbk-logs-action'] ) {
					case 'delete-logs':
						if ( isset( $_REQUEST['yith-wcbk-logs-nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_REQUEST['yith-wcbk-logs-nonce'] ) ), 'yith_wcbk_delete_logs' ) ) {
							$logger->delete_logs();
							wp_safe_redirect( remove_query_arg( array( 'yith-wcbk-logs-action', 'yith-wcbk-logs-nonce' ) ) );
							exit;
						}
						break;
				}
			}

			include YITH_WCBK_VIEWS_PATH . 'settings-tabs/html-logs.php';
		}

		/**
		 * Print an HTML after the field, if set.
		 *
		 * @param array $field The field.
		 *
		 * @since 3.0.0
		 */
		public function print_field_after_html( $field ) {
			if ( ! empty( $field['yith-wcbk-after-html'] ) ) {
				echo '<span class="yith-wcbk-plugin-fw-field__after-html">' . $field['yith-wcbk-after-html'] . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		/**
		 * Add a CSS class to fields having an "after-html" set.
		 *
		 * @param array $classes The CSS classes.
		 * @param array $field   The field.
		 *
		 * @since 3.0.0
		 */
		public function add_class_to_fields_having_after_html( $classes, $field ) {
			if ( ! empty( $field['yith-wcbk-after-html'] ) ) {
				$classes[] = 'yith-wcbk-plugin-fw-field--with-after-html';
			}

			return $classes;
		}

		/**
		 * Retrieve the documentation URL.
		 *
		 * @return string
		 */
		protected function get_doc_url(): string {
			return 'https://docs.yithemes.com/yith-woocommerce-booking-extended/';
		}

		/**
		 * Retrieve the admin panel tabs.
		 *
		 * @return array
		 */
		protected function get_admin_panel_tabs(): array {
			return array(
				'dashboard'     => array(
					'title' => _x( 'Dashboard', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
					'icon'  => 'dashboard',
				),
				'settings'      => array(
					'title' => _x( 'Settings', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
					'icon'  => 'settings',
				),
				'configuration' => array(
					'title' => _x( 'Configuration', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
					'icon'  => 'configuration',
				),
				'tools'         => array(
					'title' => _x( 'Tools', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
					'icon'  => 'tools',
				),
				'modules'       => array(
					'title'       => _x( 'Modules', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
					'description' => __( 'Enable the following modules to unlock additional features for your bookable products.', 'yith-booking-for-woocommerce' ),
					'icon'        => 'add-ons',
				),
				'emails'        => array(
					'title'       => _x( 'Emails', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
					'description' => __( 'Manage and configure the email notifications for your bookings.', 'yith-booking-for-woocommerce' ),
					'icon'        => 'email',
				),
			);
		}

		/**
		 * Retrieve the panel arguments.
		 *
		 * @return array
		 * @since 4.0.0
		 */
		protected function get_panel_args(): array {
			$admin_tabs = $this->get_admin_panel_tabs();
			$admin_tabs = apply_filters( 'yith_wcbk_settings_admin_tabs', $admin_tabs );

			return array(
				'ui_version'       => 2,
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => 'YITH Booking and Appointment for WooCommerce',
				'menu_title'       => 'Booking and Appointment',
				'class'            => yith_set_wrapper_class(),
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => self::PANEL_PAGE,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_WCBK_DIR . '/plugin-options',
				'plugin_slug'      => YITH_WCBK_SLUG,
				'plugin_version'   => YITH_WCBK_VERSION,
				'plugin_icon'      => YITH_WCBK_ASSETS_URL . '/images/plugins/booking.svg',
				'is_premium'       => false,
				'is_extended'      => true,
				'help_tab'         => array(),
				'premium_tab'      => array(
					'features' => array(
						array(
							'title'       => __( 'Create free or paid services', 'yith-booking-for-woocommerce' ),
							'description' => __( 'Create free or paid services to associate with your bookable products (e.g.: parking, breakfast, daily cleaning, chauffeur, insurance, etc.).', 'yith-booking-for-woocommerce' ),
						),
						array(
							'title'       => __( 'Additional costs', 'yith-booking-for-woocommerce' ),
							'description' => __( 'Create unlimited costs to apply to your bookings (e.g.: insurance, visitor’s tax, cleaning service, etc.).', 'yith-booking-for-woocommerce' ),
						),
						array(
							'title'       => __( 'Weekly, monthly, last-minute discounts', 'yith-booking-for-woocommerce' ),
							'description' => __( 'Set advanced discounts for weekly, monthly, or last-minute bookings.', 'yith-booking-for-woocommerce' ),
						),
						array(
							'title'       => __( 'Search forms', 'yith-booking-for-woocommerce' ),
							'description' => __( 'Show search forms to allow your users to search for specific bookable products. Choose which filters to enable (dates, people, services, location, etc.) and where to show the form on your site.', 'yith-booking-for-woocommerce' ),
						),
						array(
							'title'       => __( 'Location and Google Maps', 'yith-booking-for-woocommerce' ),
							'description' => __( 'Set the location for your bookable products, show a Google map on the product page and allow your users to search products by location.', 'yith-booking-for-woocommerce' ),
						),
						array(
							'title'       => __( 'Auto-sync availability with external platforms', 'yith-booking-for-woocommerce' ),
							'description' => __( 'Auto-sync the availability of your bookable products with external platforms like Booking, Airbnb, and HomeAway.', 'yith-booking-for-woocommerce' ),
						),
						array(
							'title'       => __( 'Email notifications', 'yith-booking-for-woocommerce' ),
							'description' => __( 'You will also be able to send custom emails before the booking starts and after it ends, deciding in both cases when to send them (for example, an email will be sent one week before the booking starts and the other email will be sent three days after the booking is completed).', 'yith-booking-for-woocommerce' ),
						),
						array(
							'title'       => __( 'Redirect to checkout', 'yith-booking-for-woocommerce' ),
							'description' => __( 'Redirect users to the Checkout page after clicking on the "Book" button (without going through the Cart page).', 'yith-booking-for-woocommerce' ),
						),
						array(
							'title'       => __( 'Customization options', 'yith-booking-for-woocommerce' ),
							'description' => __( 'Advanced options to customize the booking form.', 'yith-booking-for-woocommerce' ),
						),
					),
				),
				'welcome_modals'   => array(
					'show_in'  => function ( $context ) {
						return ! yith_wcbk_is_admin_page( 'panel/modules' );
					},
					'on_close' => function () {
						update_option( 'yith-wcbk-welcome-modal', 'no' );
					},
					'modals'   => array(
						'welcome' => array(
							'type'        => 'welcome',
							'description' => __( 'With this plugin you can manage every kind of bookable product (rooms, houses, sports equipment, bikes, etc.) and services (yoga lessons, medical appointments, legal or business consulting, etc.).', 'yith-booking-for-woocommerce' ),
							'show'        => get_option( 'yith-wcbk-welcome-modal', 'welcome' ) === 'welcome',
							'items'       => array(
								'documentation'  => array(
									'url' => $this->get_doc_url(),
								),
								'modules'        => array(
									'title'       => __( '<mark>Pick the "Modules"</mark> you need for your store', 'yith-booking-for-woocommerce' ),
									'description' => __( 'Enable the free modules or upgrade to premium to get more', 'yith-booking-for-woocommerce' ),
									'url'         => add_query_arg(
										array(
											'page' => self::PANEL_PAGE,
											'tab'  => 'modules',
										),
										admin_url( 'admin.php' )
									),
								),
								'create-product' => array(
									'title'       => __( 'Are you ready? Create your first <mark>bookable product</mark>', 'yith-booking-for-woocommerce' ),
									'description' => __( '...and start the adventure!', 'yith-booking-for-woocommerce' ),
									'url'         => add_query_arg(
										array(
											'post_type'                     => 'product',
											'yith-wcbk-new-booking-product' => 1,
										),
										admin_url( 'post-new.php' )
									),
								),
							),
						),
					),
				),
			);
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @use      YIT_Plugin_Panel_WooCommerce class
		 * @see      plugin-fw/lib/yit-plugin-panel-woocommerce.php
		 */
		public function register_panel() {
			if ( ! empty( $this->panel ) ) {
				return;
			}

			$args        = apply_filters( 'yith_wcbk_plugin_panel_args', $this->get_panel_args() );
			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Retrieve the panel page.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_panel_page() {
			return self::PANEL_PAGE;
		}

		/**
		 * Admin notices instance.
		 *
		 * @return YITH_WCBK_Admin_Notices
		 * @since 3.0.0
		 */
		public function notices() {
			return YITH_WCBK_Admin_Notices::get_instance();
		}

		/**
		 * Handle removable query args.
		 *
		 * @param array $args Query args to be removed.
		 *
		 * @return array
		 * @since 4.0.0
		 */
		public static function removable_query_args( $args ) {
			$args[] = 'yith-wcbk-new-booking-product';

			return $args;
		}
	}
}

/**
 * Unique access to instance of YITH_WCBK_Admin class
 *
 * @return YITH_WCBK_Admin
 */
function yith_wcbk_admin() {
	return YITH_WCBK_Admin::get_instance();
}
