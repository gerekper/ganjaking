<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Implements admin features of YITH WooCommerce Subscription
 *
 * @class   YITH_WC_Subscription_Admin
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_WC_Subscription_Admin' ) ) {
	/**
	 * Class YITH_WC_Subscription_Admin
	 */
	class YITH_WC_Subscription_Admin {


		/**
		 * Single instance of the class
		 *
		 * @var YITH_WC_Subscription_Admin
		 */
		protected static $instance;

		/**
		 * Panel Object
		 *
		 * @var YIT_Plugin_Panel_WooCommerce
		 */
		protected $panel;

		/**
		 * Panel Page
		 *
		 * @var string
		 */
		protected $panel_page = 'yith_woocommerce_subscription';

		/**
		 * YITH_YWSBS_Activities_List_Table
		 *
		 * @var YITH_YWSBS_Activities_List_Table
		 */
		public $cpt_obj_activities;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WC_Subscription_Admin
		 * @since  1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}


		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Add action links.
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_YWSBS_DIR . '/' . basename( YITH_YWSBS_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// Privacy.
			add_action( 'plugins_loaded', array( $this, 'load_privacy_dpa' ), 20 );

			// Panel.
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );


			add_action( 'admin_notices', array( $this, 'add_notices' ) );
			add_action( 'yith_ywsbs_dashboard_tab', array( $this, 'dashboard_tab' ), 10, 2 );

			add_action( 'yit_panel_wc_before_update', array( $this, 'check_empty_panel_options' ) );

			add_filter( 'woocommerce_admin_settings_sanitize_option', array( __CLASS__, 'maybe_regenerate_capabilities' ), 10, 3 );

			add_action( 'admin_action_ywsbs_export_shipping_list', array( $this, 'export_shipping_list' ) );

			if ( apply_filters( 'ywsbs_enable_report', true ) ) {
				add_filter( 'woocommerce_analytics_report_menu_items', array( $this, 'add_dashboard_to_woocommerce_analytics_report_menu' ), 10 );
			}
		}


		/**
		 * Export shipping list
		 *
		 * @throws \Mpdf\MpdfException Throws Exception.
		 */
		public function export_shipping_list() {
			require_once YITH_YWSBS_DIR . 'lib/autoload.php';

			$rows = YWSBS_Subscription_Delivery_Schedules()->get_processing_delivery_schedules();

			$mpdf_args = apply_filters(
				'ywsbs_mpdf_args',
				array(
					'autoScriptToLang' => true,
					'autoLangToFont'   => true,
				)
			);

			if ( is_array( $mpdf_args ) ) {
				$mpdf = new \Mpdf\Mpdf( $mpdf_args );
			} else {
				$mpdf = new \Mpdf\Mpdf();
			}

			$direction            = is_rtl() ? 'rtl' : 'ltr';
			$mpdf->directionality = apply_filters( 'yith_ywsbs_mpdf_directionality', $direction );

			$html    = '';
			$counter = 1;
			if ( $rows ) {
				foreach ( $rows as $row ) {
					$subscription = ywsbs_get_subscription( $row->subscription_id );
					$shipping     = $subscription->get_address_fields( 'shipping', true );
					if ( $shipping ) {

						$shipping = WC()->countries->get_formatted_address( $shipping, '<br>' );
						if ( ! empty( $shipping ) ) {
							$html .= '<div class="address" style="width:40%;padding:3%;margin-left:4%;float:left; margin-bottom: 20px;border:1px solid #cccccc">' . $shipping . '</div>';
							if ( $counter > 1 && 0 === ( $counter % 10 ) ) {
								$mpdf->WriteHTML( $html );
								$mpdf->AddPage();
								$html = '';
							}
							$counter++;
						}
					}
				}
			}

			$mpdf->WriteHTML( $html );

			$pdf      = $mpdf->Output();
			$filename = 'ywsbs-subscriptions-shipping-list-' . gmdate( 'Y-m-d-H-i' ) . '.pdf';
			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: application/pdf; charset=' . get_option( 'blog_charset' ), true );

			$df = fopen( 'php://output', 'w' );

			fwrite( $df, $pdf ); //phpcs:ignore

			fclose( $df ); //phpcs:ignore
		}


		/**
		 * Maybe regenerate the capabilities if the shop manager is disabled.
		 *
		 * @param mixed $value Current value.
		 * @param array $option Option info.
		 * @param mixed $raw_value Raw value.
		 *
		 * @return mixed
		 * @since  2.0.0
		 */
		public static function maybe_regenerate_capabilities( $value, $option, $raw_value ) {
			$enable_shop_manager = get_option( 'ywsbs_enable_shop_manager' );
			if ( isset( $option['id'] ) && 'ywsbs_enable_shop_manager' === $option['id'] && 'yes' !== $value && $enable_shop_manager !== $value ) {
				YWSBS_Subscription_Helper::maybe_regenerate_capabilities();
			}

			return $value;
		}


		/**
		 * Add a panel under YITH menu item
		 *
		 * @return void
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use    Yit_Plugin_Panel class
		 * @see    plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs = array(
				'dashboard'     => esc_html__( 'Dashboard', 'yith-woocommerce-subscription' ),
				'subscription'  => esc_html__( 'Subscriptions', 'yith-woocommerce-subscription' ),
				'general'       => esc_html__( 'General Settings', 'yith-woocommerce-subscription' ),
				'customization' => esc_html__( 'Customization', 'yith-woocommerce-subscription' ),
				'delivery'      => esc_html__( 'Delivery Schedules', 'yith-woocommerce-subscription' ),
			);

			if ( ! apply_filters( 'ywsbs_enable_report', true ) ) {
				unset( $admin_tabs['dashboard'] );
			}

			$args = array(
				'create_menu_page' => apply_filters( 'ywsbs_register_panel_create_menu_page', true ),
				'parent_slug'      => '',
				'page_title'       => 'YITH WooCommerce Subscription',
				'menu_title'       => 'Subscription',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => apply_filters( 'ywsbs_register_panel_parent_page', 'yith_plugin_panel' ),
				'page'             => $this->panel_page,
				'admin-tabs'       => apply_filters( 'ywsbs_register_panel_tabs', $admin_tabs ),
				'options-path'     => YITH_YWSBS_DIR . '/plugin-options',
				'position'         => apply_filters( 'ywsbs_register_panel_position', null ),
				'plugin_slug'      => YITH_YWSBS_SLUG,
				'plugin-url'       => YITH_YWSBS_URL,
				'class'            => yith_set_wrapper_class(),
				'help_tab'         => array(
					'main_video' => array(
						'desc' => _x( 'Check this video to learn how to <b>configure the plugin and create a subscription product:</b>', '[HELP TAB] Video title', 'yith-woocommerce-subscription' ),
						'url'  => array(
							'it' => 'https://www.youtube.com/embed/Zu-XBo1DtJo',
							'es' => 'https://www.youtube.com/embed/mb5jcnWxHMY',
						),
					),
					'playlists'  => array(
						'it' => 'https://www.youtube.com/watch?v=Zu-XBo1DtJo&list=PL9c19edGMs0-OubJGV491qHWl45UEADZn',
						'es' => 'https://www.youtube.com/watch?v=7kX7nxBD2BA&list=PL9Ka3j92PYJOyeFNJRdW9oLPkhfyrXmL1',
					),
				),
			);

			// enable shop manager to set Manage subscriptions.
			if ( 'yes' === get_option( 'ywsbs_enable_shop_manager' ) ) {
				add_filter( 'option_page_capability_yit_' . $args['parent'] . '_options', array( $this, 'change_capability' ) );
				$args['capability'] = 'manage_woocommerce';
			}

			$args['capability'] = apply_filters( 'ywsbs_register_panel_capabilities', $args['capability'] );

			if ( ! class_exists( 'YIT_Plugin_Panel' ) ) {
				include_once YITH_YWSBS_DIR . '/plugin-fw/lib/yit-plugin-panel.php';
			}

			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				include_once YITH_YWSBS_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );

			add_filter( 'yith_plugin_fw_get_field_template_path', array( $this, 'get_yith_panel_custom_template' ), 10, 2 );
			add_filter( 'yith_plugin_fw_wc_panel_pre_field_value', array( $this, 'get_value_of_custom_type_field' ), 10, 2 );
			add_action( 'yith_ywsbs_activities_tab', array( $this, 'activities_tab' ) );
			add_action( 'yith_ywsbs_delivery_schedules_tab', array( $this, 'delivery_status_tab' ) );

		}

		/**
		 * Add custom panel fields.
		 *
		 * @param string $template Template.
		 * @param string $field Fields.
		 *
		 * @return string
		 */
		public function get_yith_panel_custom_template( $template, $field ) {
			$custom_option_types = array(
				'ywsbs-products',
				'show-categories',
				'delivered-scheduled',
			);

			$field_type = $field['type'];

			if ( isset( $field['type'] ) && in_array( $field['type'], $custom_option_types, true ) ) {
				$template = YITH_YWSBS_VIEWS_PATH . "/panel/types/{$field_type}.php";
			}

			return $template;
		}

		/**
		 * Get the value of custom fields.
		 *
		 * @param mixed  $value Value.
		 * @param string $field String.
		 *
		 * @return mixed|void
		 */
		public function get_value_of_custom_type_field( $value, $field ) {
			$custom_option_types = array(
				'inline-fields',
			);

			if ( isset( $field['type'] ) && in_array( $field['type'], $custom_option_types, true ) ) {
				$value = get_option( $field['id'], $field['default'] );
			}

			return $value;
		}

		/**
		 * Activities List Table
		 *
		 * Load the activites on admin page
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function activities_tab() {

			if ( YITH_WC_Activity()->is_activities_list_empty() ) {
				include_once YITH_YWSBS_VIEWS_PATH . '/activities/activities-blank-state.php';
			} else {
				$this->cpt_obj_activities = new YITH_YWSBS_Activities_List_Table();

				$activities_tab = YITH_YWSBS_VIEWS_PATH . '/activities/activities-list-table.php';

				if ( file_exists( $activities_tab ) ) {
					include_once $activities_tab;
				}
			}

		}

		/**
		 * Delivery Schedules List Table
		 *
		 * Load the delivery schedules on admin page
		 *
		 * @return void
		 * @since  2.2.0
		 */
		public function delivery_status_tab() {

			if ( YWSBS_Subscription_Delivery_Schedules()->is_delivery_schedules_table_empty() ) {
				include_once YITH_YWSBS_VIEWS_PATH . '/delivery-schedules/delivery-schedules-blank-state.php';
			} else {
				$this->cpt_obj_delivery_schedules = new YWSBS_Delivery_Schedules_List_Table();

				$delivery_schedules_tab = YITH_YWSBS_VIEWS_PATH . '/delivery-schedules/delivery-schedules-list-table.php';

				if ( file_exists( $delivery_schedules_tab ) ) {
					include_once $delivery_schedules_tab;
				}
			}

		}

		/**
		 * Action Links
		 *
		 * @param array $links Links plugin array.
		 *
		 * @return mixed
		 * @use    plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {

			if ( function_exists( 'yith_add_action_links' ) ) {
				$links = yith_add_action_links( $links, $this->panel_page, true, YITH_YWSBS_SLUG );
			}

			return $links;
		}

		/**
		 * Add the action links to plugin admin page.
		 *
		 * @param array  $new_row_meta_args Plugin Meta New args.
		 * @param string $plugin_meta Plugin Meta.
		 * @param string $plugin_file Plugin file.
		 * @param array  $plugin_data Plugin data.
		 * @param string $status Status.
		 * @param string $init_file Init file.
		 *
		 * @return array
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWSBS_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) === $plugin_file ) {
				$new_row_meta_args['slug']       = YITH_YWSBS_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}


		/**
		 * Includes Privacy DPA Class.
		 */
		public function load_privacy_dpa() {
			if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {
				YITH_YWSBS_Privacy_DPA::get_instance();
			}
		}


		/**
		 * Check the empty options when the panel is saved.
		 *
		 * @since 2.1
		 */
		public function check_empty_panel_options() {

			$post = $_REQUEST; //phpcs:ignore

			if ( isset( $post['yit_panel_wc_options_nonce'], $post['tab'] ) && $post['tab']  === 'general' && wp_verify_nonce( $post['yit_panel_wc_options_nonce'], 'yit_panel_wc_options_' . $this->panel_page ) ) { //phpcs:ignore
				$options_to_check = array(
					'ywsbs_sync_exclude_categories_all_products',
					'ywsbs_sync_exclude_products_all_products',
					'ywsbs_sync_exclude_categories_virtual',
					'ywsbs_sync_exclude_products_virtual',
					'ywsbs_sync_include_product',
					'ywsbs_sync_include_categories',
					'ywsbs_sync_exclude_products_from_categories',
				);
				foreach ( $options_to_check as $option_name ) {
					if ( ! isset( $post[ $option_name ] ) ) {
						update_option( $option_name, '' );
					}
				}

				$registered_url = get_option( 'ywsbs_registered_url' );
				$registered_url = str_replace( array( 'https://', 'http://', 'www.' ), '', $registered_url );
				$current_url    = str_replace( array( 'https://', 'http://', 'www.' ), '', get_site_url() );

				if ( isset( $post['ywsbs_site_staging'] ) ) {
					$old = get_option( 'ywsbs_site_staging' );
					if ( 'yes' !== $old ) {
						yith_subscription_log( 'Changed site staging from ' . $registered_url . ' to ' . $current_url );
					}
				} else {
					update_option( 'ywsbs_site_changed', 'no' );
					update_option( 'ywsbs_site_staging', 'no' );
					update_option( 'ywsbs_registered_url', $current_url );
				}
			}
		}

		/**
		 * Print warning notice when system detects an url change
		 *
		 * @return void
		 * @since 2.2.0
		 */
		public function add_notices() {
			$site_changed = get_option( 'ywsbs_site_changed', 'no' );

			if ( ( ! empty( $_COOKIE['hide_yith_admin_notice_site_staging'] ) && 'yes' === $_COOKIE['hide_yith_admin_notice_site_staging'] ) ) {
				return;
			}

			if ( 'no' === $site_changed ) {
				return;
			}

			wp_enqueue_script( 'yith-ywsbs-admin-notices' );
			?>
			<div id="yith-admin-notice-site-staging" class="notice notice-error is-dismissible">
				<p>
					<?php printf( '<strong>%s</strong> %s', esc_html__( 'YITH WooCommerce Subscription is in staging mode:', 'yith-woocommerce-subscription' ), esc_html__( 'in this way you can work with this installation without to generate duplicate orders. You can disable the staging mode in YITH > Subscription > General settings > Extra settings.', 'yith-woocommerce-subscription' ) ); ?>
				</p>
			</div>
			<?php
		}

		/**
		 * Dashboard tab
		 *
		 * @access public
		 *
		 * @param array $options Options.
		 *
		 * @return void
		 * @since  2.3.0
		 */
		public function dashboard_tab( $options ) {
			// close the wrap div and open the Rood div.
			echo '</div><!-- /.wrap -->';
			echo "<div class='woocommerce-page' >";

			if ( file_exists( YITH_YWSBS_VIEWS_PATH . '/panel/dashboard.php' ) ) {
				include YITH_YWSBS_VIEWS_PATH . '/panel/dashboard.php';
			}
		}

		/**
		 * Add the menu item Subscriptions inside the Analytic menu.
		 *
		 * @param array $report_pages Report pages.
		 * @return mixed
		 * @since 2.3
		 */
		public function add_dashboard_to_woocommerce_analytics_report_menu( array $report_pages ) {
			$new_report_page = array();
			foreach ( $report_pages as $page ) {
				array_push( $new_report_page, $page );
				if ( ! is_null( $page ) && 'woocommerce-analytics-orders' === $page['id'] ) {
					$new_report_page[] = array(
						'id'     => 'yith-woocommerce-subscription-dashboard',
						'title'  => _x( 'Subscriptions', 'Item inside WooCommerce Analytics menu', 'yith-woocommerce-subscription' ),
						'parent' => 'woocommerce-analytics',
						'path'   => '&page=yith_woocommerce_subscription&tab=dashboard',
					);
				}
			}

			return $new_report_page;
		}

	}
}

/**
 * Unique access to instance of YITH_WC_Subscription_Admin class
 *
 * @return YITH_WC_Subscription_Admin
 */
function YITH_WC_Subscription_Admin() { //phpcs:ignore
	return YITH_WC_Subscription_Admin::get_instance();
}
