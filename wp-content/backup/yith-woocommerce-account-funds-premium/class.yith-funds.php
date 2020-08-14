<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Funds' ) ) {

	class YITH_Funds {

		/**
		 * @var YITH_Funds unique instance
		 */
		protected static $_instance;
		/**
		 * @var YIT_Plugin_Panel_WooCommerce
		 */
		protected $_panel;

		/**
		 * @var string panel page
		 */
		protected $_panel_page = 'yith_funds_panel';

		public $is_wc_2_7;

		/**
		 * YITH_Funds constructor.
		 */
		public function __construct() {

			$this->is_wc_2_7 = version_compare( WC()->version, '2.7.0', '>=' );

			// Load Plugin Framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'load_privacy' ), 20 );
			//Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_FUNDS_DIR . '/' . basename( YITH_FUNDS_FILE ) ), array(
				$this,
				'action_links'
			) );
			//Add row meta
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			//Add action for register and update plugin
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			//Add YITH FUNDS menu
			add_action( 'admin_menu', array( $this, 'add_menu' ), 5 );

			//add admin style and script
			add_action( 'admin_enqueue_scripts', array( $this, 'include_admin_scripts' ) );

			if ( apply_filters( 'ywf_user_enable_edit_funds', true ) ) {
				//add deposit column in user table
				add_action( 'manage_users_columns', array( $this, 'add_user_deposit_column' ) );
				add_action( 'manage_users_custom_column', array( $this, 'show_user_deposit_column' ), 10, 3 );
				add_action( 'yith_account_funds_user_log', array( $this, 'users_log_table' ) );
			}

			//add custom image-select field
			add_action( 'woocommerce_admin_field_image-select', array( $this, 'show_woocommerce_upload_field' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'include_frontend_scripts' ) );


			//Add custom gateway
			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateway_funds_class' ) );
			//	add_filter( 'woocommerce_is_checkout', array( $this, 'load_script_checkout' ), 5 );
			add_action( 'widgets_init', array( $this, 'register_ywf_widgets' ) );


			//add to my-account the new endpoints
			add_action( 'woocommerce_before_my_account', array( $this, 'show_customer_funds' ) );
			add_action( 'woocommerce_before_my_account', array( $this, 'show_customer_make_deposit_form' ), 20 );
			add_action( 'woocommerce_before_my_account', array( $this, 'show_customer_recent_history' ), 30 );

			//customer email
			add_filter( 'woocommerce_email_classes', array( $this, 'add_woocommerce_emails' ) );


			add_action( 'init', array( $this, 'create_deposit_product' ), 15 );
			add_filter( 'product_type_selector', array( $this, 'add_product_type' ) );
			add_filter( 'product_type_options', array( $this, 'add_type_option' ) );

			YITH_Fund_EndPoints();
			YITH_YWF_Cart_Process();
			YITH_YWF_Deposit_Fund_Checkout();
			YWF_Log();
			YITH_YWF_Order();

			add_action( 'init', array( $this, 'fund_compatibility_subscription' ), 5 );
			add_action( 'init', array( $this, 'fund_compatibility' ), 25 );
			//add admin notices
			add_action( 'admin_notices', array( $this, 'show_admin_notices' ) );

			/*GDPR integration*/


			add_filter( 'wp_privacy_personal_data_exporters', array(
				$this,
				'register_export_account_fund_info'
			), 10, 2 );
			add_filter( 'wp_privacy_personal_data_erasers', array(
				$this,
				'register_eraser_account_fund_info'
			), 10, 2 );


			add_action( 'wp_loaded', array( $this, 'redirect_at_make_a_deposit_page' ), 25 );

			$show_checkbox = get_option( 'ywf_user_privacy', 'no' );

			if ( 'yes' == $show_checkbox ) {

				add_action( 'woocommerce_edit_account_form', array( $this, 'show_checkbox' ), 15 );
				add_action( 'woocommerce_save_account_details', array( $this, 'register_customer_choose' ), 20 );
			}

		}

		/**
		 * @return YITH_Funds unique access
		 * @since 1.0.0
		 * @author YITHEMES
		 */
		public static function get_instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * load plugin fw
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function plugin_fw_loader() {

			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return array
		 * @return mixed
		 * @use plugin_action_links_{$plugin_file_name}
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.0
		 */
		public function action_links( $links ) {

			$links = yith_add_action_links( $links, $this->_panel_page, true );

			return $links;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 * @param  $init_file
		 *
		 * @return   array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_FUNDS_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug']       = YITH_FUNDS_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

		/**
		 * add YITH Funds menu under YITH_Plugins
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_menu() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = apply_filters( 'yith_funds_add_tab', array(
				'general-settings'   => __( 'Settings', 'yith-woocommerce-account-funds' ),
				'user-log-settings'  => __( 'Users\' funds', 'yith-woocommerce-account-funds' ),
				'emails-multi-tab'   => __( 'Email settings', 'yith-woocommerce-account-funds' ),
				'endpoints-settings' => __( 'Funds endpoints', 'yith-woocommerce-account-funds' ),
				'privacy-settings'   => __( 'Account & Privacy', 'yith-woocommerce-account-funds' ),
				//'vendor-multi-tab'   => __( 'Vendors & Funds', 'yith-woocommerce-account-funds' ),
			) );



			$args = array(
				'create_menu_page'   => true,
				'parent_slug'        => '',
				'page_title'         => __( 'Account Funds', 'yith-woocommerce-account-funds' ),
				'plugin_description' => __( 'Let customers deposit funds in their online wallet in your store and use them at any time to proceed with faster checkout', 'yith-woocommerce-account-funds' ),
				'menu_title'         => 'Account Funds',
				'capability'         => 'manage_options',
				'parent'             => '',
				'parent_page'        => 'yith_plugin_panel',
				'class'              => yith_set_wrapper_class(),
				'page'               => $this->_panel_page,
				'admin-tabs'         => $admin_tabs,
				'options-path'       => YITH_FUNDS_DIR . '/plugin-options'
			);

			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_FUNDS_DIR . 'plugin-fw/lib/yith-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}


		/** Register plugins for activation tab
		 * @return void
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_FUNDS_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_FUNDS_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_FUNDS_INIT, YITH_FUNDS_SECRET_KEY, YITH_FUNDS_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once( YITH_FUNDS_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YITH_FUNDS_SLUG, YITH_FUNDS_INIT );
		}

		public function add_gateway_funds_class( $methods ) {

			$methods[] = 'WC_Gateway_YITH_Funds';

			return $methods;
		}


		/**
		 * if current endpoint is make-a-deposit, load checkout scripts
		 * @since 1.0.7
		 */
		public function load_script_checkout( $is_checkout ) {


			if ( ywf_is_make_deposit() ) {
				return true;
			}


			return $is_checkout;
		}


		/**
		 * check if user profile is complete
		 * @return bool
		 * @since 1.0.0
		 * @author YITHEMES
		 */
		public function check_user_profile() {

			$billing_country         = WC()->customer->get_billing_country();
			$customer_country_fields = WC()->countries->get_address_fields( $billing_country );
			$user_id                 = get_current_user_id();
			/**
			 * @var WP_User $user
			 */
			$user = get_user_by( 'id', $user_id );

			foreach ( $customer_country_fields as $key => $value ) {

				if ( isset( $value['required'] ) && $value['required'] ) {

					$current_field = $user->get( $key );

					if ( empty( $current_field ) ) {
						return false;
					}
				}
			}

			return true;
		}


		/**
		 * add deposit column in user table
		 *
		 * @param $columns
		 *
		 * @return mixed
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 */
		public function add_user_deposit_column( $columns ) {

			$columns['user_deposit'] = __( 'Deposit', 'yith-woocommerce-account-funds' );

			return $columns;
		}

		/**
		 * show user deposit in user table
		 *
		 * @param $value
		 * @param $column_name
		 * @param $user_id
		 *
		 * @return string
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 */
		public function show_user_deposit_column( $value, $column_name, $user_id ) {

			if ( 'user_deposit' === $column_name ) {

				$customer = new YITH_YWF_Customer( $user_id );
				$funds    = apply_filters( 'yith_admin_user_deposit_column', $customer->get_funds() );
				$value    = wc_price( $funds );

				$show_log_params = array(
					'page'    => 'yith_funds_panel',
					'user_id' => $user_id,
					'tab'     => 'user-log-settings',
					'action'  => 'update'
				);

				$show_log_link       = esc_url( add_query_arg( $show_log_params, admin_url( 'admin.php' ) ) );
				$actions['show_log'] = sprintf( '<a href="%s">%s</a>', $show_log_link, __( 'Show logs', 'yith-woocommerce-account-funds' ) );


				$value .= $this->row_actions( $actions );
			}

			return $value;
		}

		/**
		 * @param $actions
		 * @param bool $always_visible
		 *
		 * @return string
		 */
		public function row_actions( $actions, $always_visible = false ) {
			$action_count = count( $actions );
			$i            = 0;

			if ( ! $action_count ) {
				return '';
			}

			$out = '<div class="' . ( $always_visible ? 'row-actions visible' : 'row-actions' ) . '">';
			foreach ( $actions as $action => $link ) {
				++ $i;
				( $i == $action_count ) ? $sep = '' : $sep = ' | ';
				$out .= "<span class='$action'>$link$sep</span>";
			}
			$out .= '</div>';

			$out .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details' ) . '</span></button>';

			return $out;
		}

		public function register_ywf_widgets() {

			require_once( 'includes/widgets/class.yith-ywf-make-a-deposit-widget.php' );
			require_once( 'includes/widgets/class.yith-ywf-view-user-funds-widget.php' );
			register_widget( 'YITH_YWF_Make_a_Deposit_Widget' );
			register_widget( 'YITH_YWF_View_User_Funds_Widget' );
		}

		public function show_woocommerce_upload_field( $option ) {

			$option['option'] = $option;
			wc_get_template( 'admin/image-select.php', $option, '', YITH_FUNDS_TEMPLATE_PATH );
		}

		/**
		 * include admin style and script
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function include_admin_scripts() {

			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			if ( isset( $_GET['page'] ) && 'yith_funds_panel' === $_GET['page'] ) {
				$is_customize_active = defined( 'YITH_WCMAP_PREMIUM' ) && YITH_WCMAP_PREMIUM;
				wp_enqueue_script( 'ywf_admin_script', YITH_FUNDS_ASSETS_URL . 'js/ywf-admin' . $suffix . '.js', array( 'jquery' ), YITH_FUNDS_VERSION, true );

				$params = array(
					'is_customize_active' => $is_customize_active,
					'wc_currency'         => get_woocommerce_currency_symbol()
				);
				wp_localize_script( 'ywf_admin_script', 'ywf_admin', $params );


				wp_enqueue_style( 'ywf_admin_style', YITH_FUNDS_ASSETS_URL . 'css/ywf_backend.css', array(), YITH_FUNDS_VERSION );
			}

			if ( isset( $_GET['post'] ) && get_post_type( $_GET['post'] ) === 'shop_order' ) {
				wp_enqueue_script( 'ywf_order_admin_script', YITH_FUNDS_ASSETS_URL . 'js/ywf-order-admin' . $suffix . '.js', array( 'jquery' ), YITH_FUNDS_VERSION, true );

				$params = array(
					'ajax_url' => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
					'messages' => array(
						'tot_av_refund_tip'    => __( 'You cannot refund an amount greater than user\'s total funds available.', 'yith-woocommerce-account-funds' ),
						'error_message_refund' => __( 'Attention! User\'s current funds are lower than the amount you are entering', 'yith-woocommerce-account-funds' ),
						'no_automatic_refund'  => __( 'Attention! It is not possible to refund this order automatically because it was partially paid with funds. Only the manual refund option is available.', 'yith-woocommerce-account-funds' ),
						'error_wrong_funds'    => __( 'Attention! It is not possible to set a value < 0', 'yith-woocommerce-account-funds' ),
					),
					'actions'  => array(
						'add_funds' => 'add_funds'
					)
				);
				wp_localize_script( 'ywf_order_admin_script', 'ywf_params', $params );
			}
		}

		/**
		 * include style and script
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function include_frontend_scripts() {

			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			wp_enqueue_style( 'ywf_style', YITH_FUNDS_ASSETS_URL . 'css/ywf_frontend.css', array(), YITH_FUNDS_VERSION );
			wp_enqueue_script( 'ywf_script', YITH_FUNDS_ASSETS_URL . 'js/ywf-frontend' . $suffix . '.js', array( 'jquery' ), YITH_FUNDS_VERSION );
		}

		/**
		 * show customer funds
		 */
		public function show_customer_funds() {

			echo do_shortcode( '[yith_ywf_show_user_fund]' );
		}

		/**
		 * show make a deposit form
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function show_customer_make_deposit_form() {

			echo do_shortcode( '[yith_ywf_make_a_deposit_form]' );
		}

		/**
		 * show fund history
		 */
		public function show_customer_recent_history() {

			if ( YWF_Log()->count_log() > 0 ) {
				$endpoint_slug         = ywf_get_view_history_slug();
				$endpoint_url          = esc_url( wc_get_endpoint_url( $endpoint_slug ) );
				$title                 = sprintf( '<h2>%s</h2><span class="ywf_show_all_history"><a href="%s">(%s)</a></span>', __( 'Recent history', 'yith-woocoomerce-account-funds' ), $endpoint_url, __( 'Show all', 'yith-woocommerce-account-funds' ) );
				$query_args['limit']   = 5;
				$query_args['offset']  = 0;
				$query_args['user_id'] = get_current_user_id();

				$additional_params = array(
					'user_log_items'   => YWF_Log()->get_log( $query_args ),
					'page_links'       => false,
					'show_filter_form' => false,
					'show_total'       => false,
				);

				$additional_params['atts'] = $additional_params;

				echo $title;
				wc_get_template( 'view-deposit-history.php', $additional_params, '', YITH_FUNDS_TEMPLATE_PATH );
			}
		}

		/**
		 * add new email class
		 *
		 * @param array $emails
		 *
		 * @return array
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 */
		public function add_woocommerce_emails( $emails ) {

			$emails['YITH_YWF_Customer_Email']        = include( YITH_FUNDS_INC . 'emails/class.yith-ywf-customer-email.php' );
			$emails['YITH_YWF_Advise_Customer_Email'] = include( YITH_FUNDS_INC . 'emails/class.yith-ywf-advise-customer-email.php' );
			//$emails['YITH_YWF_Vendor_Redeem_Email']   = include( YITH_FUNDS_INC . 'emails/class.yith-ywf-vendor-redeem-email.php' );

			return $emails;
		}

		public function users_log_table() {

			wc_get_template( 'admin/user-log-table.php', array(), '', YITH_FUNDS_TEMPLATE_PATH );

		}


		/**
		 * initialize compatibility class
		 * @author YITHEMES
		 * @since 1.0.1
		 */
		public function fund_compatibility() {
			YITH_FUNDS_Compatibility();
		}

		/**
		 * Add the integration with YITH WooCommerce Subscription plugin.
		 *
		 */
		public function fund_compatibility_subscription() {
			/**YITH WooCommerce Subscription*/
			if ( defined( 'YITH_YWSBS_PREMIUM' ) && version_compare( YITH_YWSBS_VERSION, '1.5.2', '>' ) ) {
				require_once( YITH_FUNDS_INC . 'compatibility/yith-woocommerce-subscription/class.yith-funds-yith-subscription-integration.php' );
				require_once( YITH_FUNDS_INC . 'compatibility/yith-woocommerce-subscription/class.yith-funds-yith-subscription.php' );
				YITH_Funds_YITH_Subscription_Integration();
			}
		}

		public function show_admin_notices() {

			$is_customize_active = defined( 'YITH_WCMAP_PREMIUM' ) && YITH_WCMAP_PREMIUM;
			if ( ( isset( $_GET['page'] ) && 'yith_funds_panel' == $_GET['page'] ) && ( isset( $_GET['tab'] ) && 'endpoints-settings' == $_GET['tab'] ) && $is_customize_active ) {

				$message   = __( 'Customize My Account Page is activated, you can change the YITH Account Funds Endpoints here', 'yith-woocommerce-account-funds' );
				$admin_url = admin_url( 'admin.php' );
				$args      = array(
					'page' => 'yith_wcmap_panel',
					'tab'  => 'endpoints'
				);
				$page_url  = esc_url( add_query_arg( $args, $admin_url ) );
				$message   = sprintf( '%1$s <a href="%2$s">%2$s</a>', $message, $page_url );
				?>

                <div class="notice notice-info" style="padding-right: 38px;position: relative;">
                    <p><?php echo $message; ?></p>
                </div>

				<?php
			}
		}

		public function create_deposit_product() {

			$deposit_id      = get_option( '_ywf_deposit_id', - 1 );
			$deposit_product = wc_get_product( $deposit_id );

			if ( $deposit_id == - 1 || ! $deposit_product ) {

				$deposit_id = wp_insert_post( array(
						'post_title'   => __( 'YITH Deposit', 'yith-woocommerce-account-funds' ),
						'post_type'    => 'product',
						'post_status'  => 'private',
						'post_content' => __( 'This product has been created by YITH Account Funds Plugin, please not remove', 'yith-woocommerce-account-funds' )
					)
				);

				wp_set_object_terms( $deposit_id, 'ywf_deposit', 'product_type' );

				$catalog_visibility_meta = version_compare( WC()->version, '2.7.0', '>=' ) ? 'catalog_visibility' : '_visibility';
				$product                 = wc_get_product( $deposit_id );

				yit_save_prop( $product, '_sold_individually', 'yes' );
				yit_save_prop( $product, $catalog_visibility_meta, 'hidden' );
				yit_save_prop( $product, '_virtual', 'yes' );
				yit_save_prop( $product, '_downloadable', 'yes' );


				update_option( '_ywf_deposit_id', $deposit_id );
			}

		}

		public function add_product_type( $product_type ) {

			$product_type['ywf_deposit'] = __( 'Deposit', 'yith-woocommerce-account-funds' );

			return $product_type;
		}

		public function add_type_option( $array ) {
			if ( isset( $array["virtual"] ) ) {
				$css_class     = $array["virtual"]["wrapper_class"];
				$add_css_class = 'show_if_ywf_deposit';
				$class         = empty( $css_class ) ? $add_css_class : $css_class .= ' ' . $add_css_class;

				$array["virtual"]["wrapper_class"] = $class;
			}
			if ( isset( $array['downloadable'] ) ) {
				$css_class     = $array["downloadable"]["wrapper_class"];
				$add_css_class = 'show_if_ywf_deposit';
				$class         = empty( $css_class ) ? $add_css_class : $css_class .= ' ' . $add_css_class;

				$array["downloadable"]["wrapper_class"] = $class;
			}

			return $array;
		}

		/**
		 * register export action
		 *
		 * @param array $exporters
		 *
		 * @return array
		 * @author Salvatore Strano
		 * @since 1.0.22
		 *
		 */
		public function register_export_account_fund_info( $exporters ) {

			$exporters['ywsfl-export-list'] = array(
				'exporter_friendly_name' => __( 'Account Funds Info', 'yith-woocommerce-account-funds' ),
				'callback'               => array( $this, 'export_account_fund_info' )
			);

			return $exporters;
		}

		/**
		 * export account fund info
		 *
		 * @param $email_address
		 * @param int $page
		 *
		 * @return array
		 * @since 1.0.22
		 *
		 * @author  Salvatore Strano
		 */
		public function export_account_fund_info( $email_address, $page = 1 ) {
			$data_to_export = array();

			$user = get_user_by( 'email', $email_address );

			$account_info = array();
			if ( $user instanceof WP_User ) {

				$user_id = $user->ID;

				global $wpdb;

				$table_name = $wpdb->prefix . 'ywf_user_fund_log';
				$query      = $wpdb->prepare( "SELECT type_operation, COUNT(*) as total_op, SUM( ABS( fund_user ) ) as total FROM {$table_name} WHERE user_id = %d GROUP BY type_operation", $user_id );

				$list = $wpdb->get_results( $query, ARRAY_A );


				if ( count( $list ) > 0 ) {
					$type_operation = ywf_get_operation_type();

					foreach ( $list as $data ) {


						$type_operation_name = isset( $type_operation[ $data['type_operation'] ] ) ? $type_operation[ $data['type_operation'] ] : 'N/A';
						$total_info          = __( 'for a total of ', 'yith-woocommerce-account_funds' );
						$value               = sprintf( '%d x %s %s %s', $data['total_op'], _n( $type_operation_name, $type_operation_name . 's', $data['total_op'], 'yith-woocommerce-account-funds' ), $total_info, wc_price( $data['total'] ) );
						$account_info[]      = array(
							'name'  => $type_operation_name,
							'value' => $value
						);

					}
				}

				$available_funds = get_user_meta( $user_id, '_customer_fund', true );

				$account_info[] = array(
					'name'  => __( 'Available fund', 'yith-woocommerce-account-funds' ),
					'value' => wc_price( $available_funds )
				);

			}
			$data_to_export[] = array(
				'group_id'    => 'ywf_fund_info',
				'group_label' => __( 'Account Funds Log', 'yith-woocommerce-account-funds' ),
				'data'        => $account_info,
				'item_id'     => 'account_fund_id'
			);

			return array(
				'data' => $data_to_export,
				'done' => true
			);

		}

		/**
		 * @param $erasers
		 *
		 * @return array
		 * @author  Salvatore Strano
		 * @since 1.0.22
		 */
		public function register_eraser_account_fund_info( $erasers ) {

			$erasers['ywsfl-export-list'] = array(
				'eraser_friendly_name' => __( 'Account funds', 'yith-woocommerce-account-funds' ),
				'callback'             => array( $this, 'eraser_account_fund_info' ),
			);

			return $erasers;

		}


		/**
		 * @param string $email_address
		 * @param int $page
		 *
		 * @return array
		 * @since 1.0.22
		 * @author Salvatore Strano
		 */
		public function eraser_account_fund_info( $email_address, $page = 1 ) {

			$response = array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);

			$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

			if ( ! $user instanceof WP_User ) {
				return $response;
			}

			$user_id = $user->ID;

			global $wpdb;

			$deleted = $wpdb->delete( $wpdb->prefix . 'ywf_user_fund_log', array( 'user_id' => $user_id ), array( '%d' ) );

			if ( $deleted > 0 ) {
				$response['items_removed'] = true;
				$response['messages'][]    = sprintf( '%d %s', $deleted, _n( 'Item removed from Account Fund log', 'Items removed from Account Fund log', $deleted, 'yith-woocommerce-account-funds' ) );
			}

			delete_user_meta( $user_id, '_customer_fund' );

			$response['messages'][] = __( 'Removed user meta _customer_fund properly', 'yith-woocommerce-account-funds' );

			return $response;
		}

		public function redirect_at_make_a_deposit_page() {

			if ( isset( $_POST['make_a_deposit_form'] ) && wp_verify_nonce( $_POST['make_a_deposit_form'], 'make_a_deposit_form' ) ) {

				$make_a_deposit_url = wc_get_endpoint_url( yith_account_funds_get_endpoint_slug( 'make-a-deposit' ), '', wc_get_page_permalink( 'myaccount' ) );

				$amount = isset( $_POST['amount'] ) ? $_POST['amount'] : '';
				$lang   = isset( $_POST['lang'] ) ? $_POST['lang'] : '';


				$query_args = array(
					'amount' => $amount
				);

				if ( ! empty( $lang ) ) {
					$query_args['lang'] = $lang;
				}

				$make_a_deposit_url = esc_url_raw( add_query_arg( $query_args, $make_a_deposit_url ) );

				wp_safe_redirect( $make_a_deposit_url );
				exit();
			}
		}

		public function show_checkbox() {

			wc_get_template( 'ywf-email-checkbox-agree.php', array(), '', YITH_FUNDS_TEMPLATE_PATH . 'woocommerce/myaccount/' );
		}

		/**
		 * @param int $user_id
		 */
		public function register_customer_choose( $user_id ) {

			$agree_choose = isset( $_POST['ywf_agree_send_email'] );

			update_user_meta( $user_id, '_ywf_agree_to_send_email', $agree_choose );
		}

		public function load_privacy() {
			require_once( YITH_FUNDS_INC . '/class.yith-ywf-privacy-policy.php' );
		}

	}
}

