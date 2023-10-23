<?php //phpcs:ignore
/**
 * Main plugin class
 *
 * @package YITH\ReviewReminder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YWRR_Review_Reminder' ) ) {

	/**
	 * Implements features of YWRR plugin
	 *
	 * @class   YWRR_Review_Reminder
	 * @since   1.0.0
	 * @author  YITH <plugins@yithemes.com>
	 * @package YITH
	 */
	class YWRR_Review_Reminder {

		/**
		 * Panel object
		 *
		 * @since   1.0.0
		 * @var     /Yit_Plugin_Panel object
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $panel;

		/**
		 * YITH WooCommerce Review Reminder panel page
		 *
		 * @var string
		 */
		protected $panel_page = 'yith_ywrr_panel';

		/**
		 * Single instance of the class
		 *
		 * @since 1.1.5
		 * @var YWRR_Review_Reminder
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YWRR_Review_Reminder
		 * @since 1.1.5
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;

		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function __construct() {

			if ( ! function_exists( 'WC' ) ) {
				return;
			}

			// Load Plugin Framework.
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'include_privacy_text' ), 20 );
			// Register plugin to licence/update system.
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
			add_action( 'init', array( $this, 'set_plugin_requirements' ), 20 );
			// Add action links.
			add_filter( 'plugin_action_links_' . plugin_basename( YWRR_DIR . '/' . basename( YWRR_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			add_action( 'init', array( $this, 'init_crons' ) );
			add_action( 'init', array( $this, 'includes' ), 15 );

			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 5 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

			add_filter( 'woocommerce_email_classes', array( $this, 'add_ywrr_email' ) );
			add_action( 'woocommerce_init', array( $this, 'load_wc_mailer' ) );
			add_action( 'ywrr_email_header', array( $this, 'get_email_header' ), 10, 2 );
			add_action( 'ywrr_email_footer', array( $this, 'get_email_footer' ), 10, 3 );
			add_action( 'init', array( $this, 'set_ywrr_image_sizes' ) );
			add_action( 'template_redirect', array( $this, 'redirect_to_login' ), 10 );
			add_filter( 'woocommerce_login_redirect', array( $this, 'login_redirect' ) );
			add_filter( 'yith_wcet_email_template_types', array( $this, 'add_yith_wcet_template' ) );
			add_action( 'yith_wcet_after_email_styles', array( $this, 'add_yith_wcet_styles' ), 10, 3 );
			add_filter( 'woocommerce_email_styles', array( $this, 'add_ywrr_styles' ), 10, 2 );
			add_filter( 'ywrr_product_permalink', array( $this, 'set_product_permalink' ), 10, 3 );
			add_filter( 'yith_plugin_fw_get_field_template_path', array( $this, 'add_custom_fields' ), 10, 2 );

			if ( 'yes' === get_option( 'ywrr_refuse_requests' ) ) {
				add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'show_request_option' ) );
				add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_request_option' ) );
				add_action( 'woocommerce_edit_account_form', array( $this, 'show_request_option_my_account' ) );
				add_action( 'woocommerce_save_account_details', array( $this, 'save_request_option_my_account' ) );
			}

			if ( 'yes' === get_option( 'ywrr_schedule_order_column' ) ) {
				add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_ywrr_column' ), 11 );
				add_action( 'manage_shop_order_posts_custom_column', array( $this, 'render_ywrr_column' ), 3, 2 );
				add_filter( 'manage_woocommerce_page_wc-orders_columns', array( $this, 'add_ywrr_column' ), 11 );
				add_action( 'manage_woocommerce_page_wc-orders_custom_column', array( $this, 'render_ywrr_column' ), 3, 2 );
				add_filter( 'manage_yith_booking_posts_columns', array( $this, 'add_ywrr_column' ), 11 );
				add_action( 'manage_yith_booking_posts_custom_column', array( $this, 'render_ywrr_column_bookings' ), 3, 2 );
				add_action( 'admin_footer', array( $this, 'order_schedule_template' ) );
			}

			add_filter( 'woocommerce_mail_callback', array( $this, 'mail_use_mandrill' ) );
			add_action( 'load-edit.php', array( $this, 'process_bulk_actions' ) );
			add_action( 'handle_bulk_actions-woocommerce_page_wc-orders', array( $this, 'process_bulk_actions' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

		}

		/**
		 * Cron initialization
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function init_crons() {

			$ve = get_option( 'gmt_offset' ) > 0 ? '+' : '-';

			if ( ! wp_next_scheduled( 'ywrr_daily_send_mail_job' ) ) {
				wp_schedule_event( strtotime( '00:00 ' . $ve . get_option( 'gmt_offset' ) . ' HOURS' ), 'daily', 'ywrr_daily_send_mail_job' );
			}

			if ( ! wp_next_scheduled( 'ywrr_hourly_send_mail_job' ) ) {
				wp_schedule_event( strtotime( '00:00 ' . $ve . get_option( 'gmt_offset' ) . ' HOURS' ), 'hourly', 'ywrr_hourly_send_mail_job' );
			}

		}

		/**
		 * Files inclusion
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function includes() {

			include_once 'includes/ywrr-functions.php';
			include_once 'includes/class-ywrr-unsubscribe.php';
			include_once 'includes/admin/class-ywrr-ajax.php';

			if ( is_admin() ) {
				include_once 'includes/admin/meta-boxes/class-ywrr-meta-box.php';
				include_once 'includes/admin/class-yith-custom-table.php';
				include_once 'includes/admin/tables/class-ywrr-blocklist-table.php';
				include_once 'includes/admin/tables/class-ywrr-schedule-table.php';
			}

		}

		/**
		 * Initializes Javascript with localization
		 *
		 * @return  void
		 * @since   1.1.5
		 */
		public function admin_scripts() {

			if ( ywrr_vendor_check() ) {
				return;
			}

			if ( isset( $_GET['page'] ) && 'yith_ywrr_panel' === $_GET['page'] ) { //phpcs:ignore

				wp_enqueue_style( 'ywrr-admin', yit_load_css_file( YWRR_ASSETS_URL . 'css/ywrr-admin.css' ), array(), YWRR_VERSION );
				wp_enqueue_script( 'ywrr-admin', yit_load_js_file( YWRR_ASSETS_URL . 'js/ywrr-admin.js' ), array(), YWRR_VERSION, false );

				$params = array(
					'ajax_url'               => admin_url( 'admin-ajax.php' ),
					'mail_wrong'             => esc_html__( 'Please insert a valid email address', 'yith-woocommerce-review-reminder' ),
					'before_send_test_email' => esc_html__( 'Sending test email...', 'yith-woocommerce-review-reminder' ),
					'please_wait'            => esc_html__( 'Please wait...', 'yith-woocommerce-review-reminder' ),
					'assets_url'             => YWRR_ASSETS_URL,
				);

				wp_localize_script( 'ywrr-admin', 'ywrr_admin', $params );

			}

			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;

			if ( $screen && ( ( in_array( $screen->id, array( wc_get_page_screen_id( 'shop-order' ), 'shop_order' ), true ) ) || ( in_array( $screen->post_type, array( wc_get_page_screen_id( 'shop-order' ), 'shop_order' ), true ) ) || 'yith_booking' === $screen->post_type ) ) {

				wp_enqueue_style( 'yith-plugin-fw-fields' );
				wp_enqueue_script( 'yith-plugin-fw-fields' );
				wp_enqueue_style( 'ywrr-actions', yit_load_css_file( YWRR_ASSETS_URL . 'css/ywrr-actions.css' ), array( 'yith-plugin-fw-fields' ), YWRR_VERSION );
				wp_enqueue_script( 'ywrr-actions', yit_load_js_file( YWRR_ASSETS_URL . 'js/ywrr-actions.js' ), array( 'jquery', 'wc-backbone-modal', 'yith-plugin-fw-fields' ), YWRR_VERSION, false );

				$params = array(
					'ajax_url'              => admin_url( 'admin-ajax.php' ),
					'send_button_label'     => esc_html__( 'Send', 'yith-woocommerce-review-reminder' ),
					'schedule_button_label' => esc_html__( 'Schedule', 'yith-woocommerce-review-reminder' ),
					'missing_date_error'    => esc_html__( 'Please, select a date.', 'yith-woocommerce-review-reminder' ),
					'send_label'            => esc_html__( 'Review Reminder: Send email', 'yith-woocommerce-review-reminder' ),
					'reschedule_label'      => esc_html__( 'Review Reminder: Reschedule email', 'yith-woocommerce-review-reminder' ),
					'cancel_label'          => esc_html__( 'Review Reminder: Cancel email', 'yith-woocommerce-review-reminder' ),
				);

				wp_localize_script( 'ywrr-actions', 'ywrr_actions', $params );

			}

		}

		/**
		 * Get the email header.
		 *
		 * @param string $email_heading The email heading text.
		 * @param mixed  $template      The email template.
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function get_email_header( $email_heading, $template = false ) {

			if ( ! $template ) {
				$template = get_option( 'ywrr_mail_template' );
			}

			$templates       = ywrr_get_templates();
			$email_templates = ywrr_check_ywcet_active() && get_option( 'ywrr_mail_template_enable' ) === 'yes';

			if ( in_array( $template, $templates, true ) && ! $email_templates ) {
				wc_get_template( 'emails/' . $template . '/email-header.php', array( 'email_heading' => $email_heading ), false, YWRR_TEMPLATE_PATH );
			} else {
				wc_get_template(
					'emails/email-header.php',
					array(
						'email_heading' => $email_heading,
						'mail_type'     => 'yith-review-reminder',
					)
				);
			}

		}

		/**
		 * Get the email footer.
		 *
		 * @param string $unsubscribe_url  The unsubscribe link URL.
		 * @param mixed  $template         The email template.
		 * @param string $unsubscribe_text The unsubscribe link text.
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function get_email_footer( $unsubscribe_url, $template, $unsubscribe_text ) {

			if ( ! $template ) {
				$template = get_option( 'ywrr_mail_template' );
			}

			$templates       = ywrr_get_templates();
			$email_templates = ywrr_check_ywcet_active() && get_option( 'ywrr_mail_template_enable' ) === 'yes';

			if ( in_array( $template, $templates, true ) && ! $email_templates ) {

				wc_get_template(
					'emails/' . $template . '/email-footer.php',
					array(
						'unsubscribe_url'  => $unsubscribe_url,
						'unsubscribe_text' => $unsubscribe_text,
					),
					false,
					YWRR_TEMPLATE_PATH
				);

			} else {

				wc_get_template( 'emails/email-footer.php', array( 'mail_type' => 'yith-review-reminder' ) );

			}

		}

		/**
		 * ADMIN FUNCTIONS
		 */

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return  void
		 * @since   1.0.0
		 * @use     /Yit_Plugin_Panel class
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		public function add_menu_page() {

			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs = array(
				'mail'      => esc_html__( 'Email Settings', 'yith-woocommerce-review-reminder' ),
				'settings'  => esc_html__( 'Request Settings', 'yith-woocommerce-review-reminder' ),
				'schedule'  => esc_html__( 'Scheduled Emails List', 'yith-woocommerce-review-reminder' ),
				'blocklist' => esc_html__( 'Blocklist', 'yith-woocommerce-review-reminder' ),
			);

			$args = array(
				'create_menu_page' => true,
				'plugin_slug'      => YWRR_SLUG,
				'is_premium'       => true,
				'parent_slug'      => '',
				'page_title'       => 'YITH WooCommerce Review Reminder',
				'menu_title'       => 'Review Reminder',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YWRR_DIR . '/plugin-options',
				'class'            => yith_set_wrapper_class(),
			);

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Add the YWRR_Request_Mail class to WooCommerce mail classes
		 *
		 * @param array $email_classes WooCommerce emails list.
		 *
		 * @return  array
		 * @since   1.0.0
		 */
		public function add_ywrr_email( $email_classes ) {

			$email_classes['YWRR_Request_Mail'] = include 'includes/emails/class-ywrr-request-mail.php';

			return $email_classes;
		}

		/**
		 * Hook the mailer functions
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function load_wc_mailer() {

			add_filter( 'send_ywrr_mail', array( $this, 'send_transactional_email' ), 10, 1 );

		}

		/**
		 * Instantiate WC_Emails instance and send transactional emails
		 *
		 * @param array $args Email arguments.
		 *
		 * @return  boolean
		 * @since   1.0.0
		 */
		public function send_transactional_email( $args = array() ) {

			try {

				WC_Emails::instance(); // Init self so emails exist.

				return apply_filters( 'send_ywrr_mail_notification', $args );

			} catch ( Exception $e ) {

				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					trigger_error( 'Transactional email triggered fatal error for callback ' . current_filter(), E_USER_WARNING ); //phpcs:ignore
				}

				return false;
			}

		}

		/**
		 * Add the schedule column
		 *
		 * @param array $columns Table columns.
		 *
		 * @return  array
		 * @since   1.2.2
		 */
		public function add_ywrr_column( $columns ) {

			if ( ! ywrr_vendor_check() ) {
				$columns['ywrr_status'] = esc_html__( 'Review Reminder', 'yith-woocommerce-review-reminder' );
			}

			return $columns;

		}

		/**
		 * Render the schedule column in orders page
		 *
		 * @param string           $column Column name.
		 * @param integer|WC_Order $order  Order ID/Order Object.
		 *
		 * @return  void
		 * @since   1.2.2
		 */
		public function render_ywrr_column( $column, $order ) {

			if ( ! ywrr_vendor_check() && 'ywrr_status' === $column ) {

				if ( ! $order instanceof WC_Order ) {
					$order = wc_get_order( $order );
				}

				if ( ! $order ) {
					return;
				}

				$customer_id    = $order->get_user_id();
				$customer_email = $order->get_billing_email();

				if ( ywrr_check_blocklist( $customer_id, $customer_email ) === true ) {

					$is_funds    = $order->get_meta( '_order_has_deposit' ) === 'yes';
					$is_deposits = $order->get_created_via() === 'yith_wcdp_balance_order';
					/**
					 * APPLY_FILTERS: ywrr_skip_renewal_orders
					 *
					 * Check if plugin should skip subscription renewal orders.
					 *
					 * @param boolean $value Value to check if renewals should be skipped.
					 *
					 * @return boolean
					 */
					$is_renew = $order->get_meta( 'is_a_renew' ) === 'yes' && apply_filters( 'ywrr_skip_renewal_orders', true );
					/**
					 * APPLY_FILTERS: ywrr_can_ask_for_review
					 *
					 * Check if plugin can ask for a review.
					 *
					 * @param boolean  $value Value to check if the review can be asked.
					 * @param WC_Order $order The order to check.
					 *
					 * @return boolean
					 */
					$can_ask_review = apply_filters( 'ywrr_can_ask_for_review', true, $order );

					if ( (int) ywrr_check_reviewable_items( $order->get_id() ) === 0 || $is_funds || $is_deposits || $is_renew || ! $can_ask_review ) {

						ywrr_get_noreview_message( 'no-items' );

						if ( ywrr_multivendor_enabled() ) {

							$suborders = ywrr_get_suborders( $order->get_id() );

							if ( ! empty( $suborders ) ) {
								?>
								<br/>
								<?php

								foreach ( $suborders as $suborder_id ) {

									if ( (int) ywrr_check_reviewable_items( $suborder_id ) === 0 ) {
										/* translators: %s suborder number */
										printf( esc_html__( 'Suborder #%s has no reviewable items', 'yith-woocommerce-review-reminder' ), esc_html( $suborder_id ) );

									} else {
										/**
										 * APPLY_FILTERS: yith_wcmv_edit_order_uri
										 *
										 * Get edit vendor order uri.
										 *
										 * @param string  $uri         The order uri.
										 * @param integer $suborder_id The suborder ID.
										 *
										 * @return string
										 */
										$order_uri = apply_filters( 'yith_wcmv_edit_order_uri', esc_url( 'post.php?post=' . absint( $suborder_id ) . '&action=edit' ), absint( $suborder_id ) );
										/* translators: %s suborder number */
										$link_text = sprintf( esc_html__( 'Suborder %s has reviewable items', 'yith-woocommerce-review-reminder' ), '<strong>#' . $suborder_id . '</strong>' );

										printf( '<a href="%s">%s</a><br />', esc_url( $order_uri ), wp_kses_post( $link_text ) );
									}
								}
							}
						}
					} else {
						ywrr_get_send_box( $order->get_id(), $order );
					}
				} else {
					ywrr_get_noreview_message();
				}
			}

		}

		/**
		 * Render the schedule column in bookings page
		 *
		 * @param string  $column  Column name.
		 * @param integer $post_id Post ID.
		 *
		 * @return  void
		 * @since   1.6.0
		 */
		public function render_ywrr_column_bookings( $column, $post_id ) {

			if ( ! ywrr_vendor_check() && 'ywrr_status' === $column ) {

				$booking = yith_get_booking( $post_id );

				if ( ! $booking ) {
					return;
				}

				$order = $booking->get_order();
				if ( ! $order ) {
					ywrr_get_noreview_message( 'no-booking' );

					return;
				}
				$customer_id    = $order->get_user_id();
				$customer_email = $order->get_billing_email();

				if ( ywrr_check_blocklist( $customer_id, $customer_email ) === true ) {

					$is_funds    = $order->get_meta( '_order_has_deposit' ) === 'yes';
					$is_deposits = $order->get_created_via() === 'yith_wcdp_balance_order';
					/**
					 * APPLY_FILTERS: ywrr_skip_renewal_orders
					 *
					 * Check if plugin should skip subscription renewal orders.
					 *
					 * @param boolean $value Value to check if renewals should be skipped.
					 *
					 * @return boolean
					 */
					$is_renew = $order->get_meta( 'is_a_renew' ) === 'yes' && apply_filters( 'ywrr_skip_renewal_orders', true );
					/**
					 * APPLY_FILTERS: ywrr_can_ask_for_review
					 *
					 * Check if plugin can ask for a review.
					 *
					 * @param boolean  $value Value to check if the review can be asked.
					 * @param WC_Order $order The order to check.
					 *
					 * @return boolean
					 */
					$can_ask_review = apply_filters( 'ywrr_can_ask_for_review', true, $order );

					if ( ! ywrr_items_has_comments_opened( $booking->get_product_id() ) || ywrr_user_has_commented( $booking->get_product_id(), $customer_email ) || $is_funds || $is_deposits || $is_renew || ! $can_ask_review ) {
						ywrr_get_noreview_message( 'no-booking' );
					} else {
						ywrr_get_send_box( $post_id, $order, $booking->get_id(), $booking->get_order_item_id() );
					}
				} else {
					ywrr_get_noreview_message();
				}
			}

		}

		/**
		 * Set up backbone modal for schedule actions
		 *
		 * @return  void
		 * @since   1.6.0
		 */
		public function order_schedule_template() {

			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;

			if ( ! ywrr_vendor_check() && $screen && ( ( in_array( $screen->id, array( wc_get_page_screen_id( 'shop-order' ), 'shop_order' ), true ) ) || ( in_array( $screen->post_type, array( wc_get_page_screen_id( 'shop-order' ), 'shop_order' ), true ) ) || 'yith_booking' === $screen->post_type ) ) {
				$action_type    = array(
					'id'      => 'action_type',
					'name'    => 'action_type',
					'type'    => 'radio',
					'options' => array(
						'now'      => esc_html__( 'Now', 'yith-woocommerce-review-reminder' ),
						'schedule' => esc_html__( 'Choose a date', 'yith-woocommerce-review-reminder' ) . '{{{data.additional_label}}}',
					),
					'value'   => 'now',
				);
				$schedule_date  = array(
					'id'    => 'schedule_date',
					'name'  => 'schedule_date',
					'type'  => 'datepicker',
					'data'  => array(
						'date-format' => 'yy-mm-dd',
						'min-date'    => 1,
					),
					'value' => '{{{data.scheduled_date}}}',
				);
				$buttons        = array(
					'type'    => 'buttons',
					'buttons' => array(
						array(
							'name'  => esc_html__( 'Send', 'yith-woocommerce-review-reminder' ),
							'class' => 'button-primary ywrr-email-action',
						),
						array(
							'name'  => esc_html__( 'Cancel', 'yith-woocommerce-review-reminder' ),
							'class' => 'modal-close',
						),
					),
				);
				$delete_buttons = array(
					'type'    => 'buttons',
					'buttons' => array(
						array(
							'name'  => esc_html__( 'Delete', 'yith-woocommerce-review-reminder' ),
							'class' => 'button-primary ywrr-delete-action',
						),
						array(
							'name'  => esc_html__( 'Cancel', 'yith-woocommerce-review-reminder' ),
							'class' => 'modal-close',
						),
					),
				);

				?>
				<script type="text/template" id="tmpl-ywrr-actions">
					<div class="wc-backbone-modal">
						<div class="wc-backbone-modal-content yith-plugin-fw yith-plugin-ui ywrr-actions-modal">
							<section class="wc-backbone-modal-main" role="main">
								<header class="wc-backbone-modal-header">
									<h1><?php esc_html_e( 'Schedule a review reminder email', 'yith-woocommerce-review-reminder' ); ?>:</h1>
									<button class="modal-close modal-close-link dashicons dashicons-no-alt">
										<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'yith-woocommerce-review-reminder' ); ?></span>
									</button>
								</header>
								<article>
									<?php esc_html_e( 'Send this reminder email on', 'yith-woocommerce-review-reminder' ); ?>:
									<br/>
									<br/>
									<?php
									yith_plugin_fw_get_field( $action_type, true );
									?>
									<div class="ywrr-modal-datepicker">
										<?php
										yith_plugin_fw_get_field( $schedule_date, true );
										?>
									</div>
									<div class="error-message"></div>
									<input class="ywrr-order-id" type="hidden" value="<?php echo '{{{data.order_id}}}'; ?>">
									<input class="ywrr-order-item-id" type="hidden" value="<?php echo '{{{data.order_item_id}}}'; ?>">
									<input class="ywrr-booking-id" type="hidden" value="<?php echo '{{{data.booking_id}}}'; ?>">
									<input class="ywrr-order-date" type="hidden" value="<?php echo '{{{data.order_date}}}'; ?>">
									<input class="ywrr-row-id" type="hidden" value="<?php echo '{{{data.row_id}}}'; ?>">
								</article>
								<footer>
									<?php yith_plugin_fw_get_field( $buttons, true ); ?>
								</footer>
							</section>
						</div>
					</div>
					<div class="wc-backbone-modal-backdrop modal-close"></div>
				</script>
				<script type="text/template" id="tmpl-ywrr-delete">
					<div class="wc-backbone-modal">
						<div class="wc-backbone-modal-content yith-plugin-fw yith-plugin-ui ywrr-delete-modal">
							<section class="wc-backbone-modal-main" role="main">
								<header class="wc-backbone-modal-header">
									<h1><?php esc_html_e( 'Cancel a review reminder email', 'yith-woocommerce-review-reminder' ); ?>:</h1>
									<button class="modal-close modal-close-link dashicons dashicons-no-alt">
										<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'yith-woocommerce-review-reminder' ); ?></span>
									</button>
								</header>
								<article>
									<?php esc_html_e( 'Do you want to cancel the reminder email?', 'yith-woocommerce-review-reminder' ); ?>
									<br/>
									<div class="error-message"></div>
									<input class="ywrr-order-id" type="hidden" value="<?php echo '{{{data.order_id}}}'; ?>">
									<input class="ywrr-order-item-id" type="hidden" value="<?php echo '{{{data.order_item_id}}}'; ?>">
									<input class="ywrr-booking-id" type="hidden" value="<?php echo '{{{data.booking_id}}}'; ?>">
									<input class="ywrr-row-id" type="hidden" value="<?php echo '{{{data.row_id}}}'; ?>">
								</article>
								<footer>
									<?php yith_plugin_fw_get_field( $delete_buttons, true ); ?>
								</footer>
							</section>
						</div>
					</div>
					<div class="wc-backbone-modal-backdrop modal-close"></div>
				</script>
				<?php
			}
		}

		/**
		 * Trigger bulk actions to orders
		 *
		 * @return  void
		 * @throws  Exception An exception.
		 * @since   1.2.2
		 */
		public function process_bulk_actions() {

			$getted    = $_GET;     //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$requested = $_REQUEST; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( ywrr_vendor_check() ) {
				return;
			}

			$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
			$action        = $wp_list_table->current_action();

			// Bail out if this is not a status-changing action.
			if ( strpos( $action, 'ywrr_' ) === false ) {
				return;
			}

			$processed = 0;
			$post_ids  = array_map( 'absint', (array) ( isset( $requested['order'] ) ? $requested['order'] : $requested['post'] ) );

			if ( isset( $getted['post_type'] ) && 'yith_booking' === $getted['post_type'] ) {
				foreach ( $post_ids as $post_id ) {

					$booking = yith_get_booking( $post_id );
					$order   = $booking->get_order();
					$ok      = false;

					if ( ! $order ) {
						continue;
					}

					$customer_id    = $order->get_user_id();
					$customer_email = $order->get_billing_email();

					if ( ywrr_check_blocklist( $customer_id, $customer_email ) === true ) {

						if ( ! ywrr_items_has_comments_opened( $booking->get_product_id() ) || ywrr_user_has_commented( $booking->get_product_id(), $customer_email ) ) {
							continue;
						}

						switch ( substr( $action, 5 ) ) {

							case 'send':
								$today      = new DateTime( current_time( 'mysql' ) );
								$order_date = $order->get_date_modified();

								if ( ! $order_date ) {
									$order_date = $order->get_date_created();
								}

								$pay_date        = new DateTime( gmdate( 'Y-m-d H:i:s', yit_datetime_to_timestamp( $order_date ) ) );
								$days            = $pay_date->diff( $today );
								$items_to_review = array( $booking->get_order_item_id() );
								$email_result    = ywrr_send_email( $order->get_id(), $days->days, $items_to_review, array(), 'booking' );

								if ( true === $email_result ) {

									if ( (int) ywrr_check_exists_schedule( $order->get_id(), $booking->get_id() ) !== 0 ) {
										ywrr_change_schedule_status( $order->get_id(), 'sent', $booking->get_id() );
									} else {
										ywrr_log_unscheduled_email( $order, $booking->get_id(), ywrr_get_review_list_forced( $items_to_review, $order->get_id() ) );
									}

									$ok = true;

								}

								break;

							case 'reschedule':
								$scheduled_date = gmdate( 'Y-m-d', strtotime( current_time( 'mysql' ) . ' + ' . get_option( 'ywrr_mail_schedule_day' ) . ' days' ) );

								if ( (int) ywrr_check_exists_schedule( $order->get_id(), $booking->get_id() ) !== 0 ) {
									$items_to_review = array( $booking->get_order_item_id() );
									$list            = ywrr_get_review_list_forced( $items_to_review, $order->get_id() );
									ywrr_reschedule( $order->get_id(), $scheduled_date, $list );
								} else {
									ywrr_schedule_booking_mail( $booking->get_id() );
								}

								$ok = true;

								break;

							case 'cancel':
								if ( (int) ywrr_check_exists_schedule( $order->get_id(), $booking->get_id() ) !== 0 ) {
									ywrr_change_schedule_status( $order->get_id(), 'cancelled', $booking->get_id() );
									$ok = true;
								}
								break;
						}

						if ( $ok ) {
							$processed ++;
						}
					}
				}
			} else {
				foreach ( $post_ids as $post_id ) {

					$order = wc_get_order( $post_id );
					$ok    = false;

					if ( ! $order ) {
						continue;
					}

					$customer_id    = $order->get_user_id();
					$customer_email = $order->get_billing_email();

					if ( ywrr_check_blocklist( $customer_id, $customer_email ) === true ) {

						if ( (int) ywrr_check_reviewable_items( $order->get_id() ) === 0 ) {
							continue;
						}

						switch ( substr( $action, 5 ) ) {

							case 'send':
								$today      = new DateTime( current_time( 'mysql' ) );
								$order_date = $order->get_date_modified();

								if ( ! $order_date ) {
									$order_date = $order->get_date_created();
								}

								$pay_date     = new DateTime( gmdate( 'Y-m-d H:i:s', yit_datetime_to_timestamp( $order_date ) ) );
								$days         = $pay_date->diff( $today );
								$email_result = ywrr_send_email( $order->get_id(), $days->days );

								if ( true === $email_result ) {

									if ( (int) ywrr_check_exists_schedule( $order->get_id() ) !== 0 ) {
										ywrr_change_schedule_status( $order->get_id(), 'sent' );
									} else {
										ywrr_log_unscheduled_email( $order );
									}

									$ok = true;

								}
								break;

							case 'reschedule':
								$scheduled_date = gmdate( 'Y-m-d', strtotime( current_time( 'mysql' ) . ' + ' . get_option( 'ywrr_mail_schedule_day' ) . ' days' ) );

								if ( (int) ywrr_check_exists_schedule( $order->get_id() ) !== 0 ) {
									ywrr_reschedule( $order->get_id(), $scheduled_date );
								} else {
									ywrr_schedule_mail( $order->get_id() );
								}

								$ok = true;

								break;

							case 'cancel':
								if ( (int) ywrr_check_exists_schedule( $order->get_id() ) !== 0 ) {
									ywrr_change_schedule_status( $order->get_id() );
									$ok = true;
								}
								break;
						}

						if ( $ok ) {
							$processed ++;
						}
					}
				}
			}

			$sendback_args = array(
				'ywrr_action' => substr( $action, 5 ),
				'processed'   => $processed,
				'ids'         => join( ',', $post_ids ),
			);

			if ( isset( $getted['page'] ) ) {
				$sendback_args['page'] = $getted['page'];
			} else {
				$sendback_args['post_type'] = $getted['post_type'];
			}

			$sendback = add_query_arg(
				$sendback_args,
				''
			);

			wp_safe_redirect( esc_url_raw( $sendback ) );
			exit();
		}

		/**
		 * Show admin notices
		 *
		 * @return  void
		 * @since   1.6.0
		 */
		public function admin_notices() {

			$message   = '';
			$classes   = '';
			$requested = $_REQUEST; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$screen    = function_exists( 'get_current_screen' ) ? get_current_screen() : false;

			if ( ! ywrr_vendor_check() && $screen && ( ( in_array( $screen->id, array( wc_get_page_screen_id( 'shop-order' ), 'shop_order' ), true ) ) || ( in_array( $screen->post_type, array( wc_get_page_screen_id( 'shop-order' ), 'shop_order' ), true ) ) || 'yith_booking' === $screen->post_type ) ) {
				if ( isset( $requested['ywrr_action'] ) ) {

					$number = isset( $requested['processed'] ) ? absint( $requested['processed'] ) : 0;

					switch ( $requested['ywrr_action'] ) {
						case 'send':
							/* translators: %s emails number */
							$message = sprintf( _n( 'Review Reminder: %s email sent.', 'Review Reminder: %s emails sent', $number, 'yith-woocommerce-review-reminder' ), number_format_i18n( $number ) );
							break;

						case 'reschedule':
							/* translators: %s emails number */
							$message = sprintf( _n( 'Review Reminder: %s email rescheduled.', 'Review Reminder: %s emails rescheduled.', $number, 'yith-woocommerce-review-reminder' ), number_format_i18n( $number ) );
							break;

						case 'cancel':
							/* translators: %s emails number */
							$message = sprintf( _n( 'Review Reminder: %s email cancelled.', 'Review Reminder: %s emails cancelled.', $number, 'yith-woocommerce-review-reminder' ), number_format_i18n( $number ) );
							break;

						default:
							$message = '';
					}

					$classes = 'notice-success is-dismissible';

				}
			}

			if ( $message ) {
				echo '<div class="notice ' . esc_attr( $classes ) . '"><p>' . esc_html( $message ) . '</p></div>';
			}

		}


		/**
		 * Initialize custom fields
		 *
		 * @param string $path  Field path.
		 * @param array  $field Field options.
		 *
		 * @return  string
		 * @since   1.6.0
		 */
		public function add_custom_fields( $path, $field ) {

			if ( 'ywrr-custom-checklist' === $field['type'] ) {
				$path = YWRR_DIR . 'includes/admin/fields/ywrr-custom-checklist.php';
			}

			return $path;

		}

		/**
		 * Sets Mandrill as mailer if enabled
		 *
		 * @param string $mailer_func Mailer function name.
		 *
		 * @return  string
		 * @since   1.6.0
		 */
		public function mail_use_mandrill( $mailer_func ) {
			return get_option( 'ywrr_mandrill_enable' ) === 'yes' ? 'ywrr_mandrill_send' : $mailer_func;
		}

		/**
		 * Set image sizes for email
		 *
		 * @return  void
		 * @since   1.0.4
		 */
		public function set_ywrr_image_sizes() {

			add_image_size( 'ywrr_picture', 135, 135, true );

		}

		/**
		 * If is active YITH WooCommerce Email Templates, add YWRR to list
		 *
		 * @param array $templates Email templates list.
		 *
		 * @return  array
		 * @since   1.0.0
		 */
		public function add_yith_wcet_template( $templates ) {

			$templates[] = array(
				'id'   => 'yith-review-reminder',
				'name' => 'YITH WooCommerce Review Reminder',
			);

			return $templates;

		}

		/**
		 * If is active YITH WooCommerce Email Templates, add YWRR styles
		 *
		 * @param integer  $premium_style Unused.
		 * @param array    $meta          Template meta.
		 * @param WC_Email $current_email Current email object.
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function add_yith_wcet_styles( $premium_style, $meta, $current_email ) {

			if ( isset( $current_email ) && 'yith-review-reminder' === $current_email->id ) {
				ywrr_email_styles();

				?>
				.ywrr-table td.title-column a{
				color:<?php echo esc_attr( $meta['base_color'] ); ?>;
				}
				<?php

			}

		}

		/**
		 * Add YWRR styles to WC Emails
		 *
		 * @param string           $css   The CSS for the email.
		 * @param WC_Email|boolean $email The email object.
		 *
		 * @return  string
		 * @since   1.0.0
		 */
		public function add_ywrr_styles( $css, $email = false ) {

			if ( $email && 'yith-review-reminder' === $email->id ) {
				$email_templates = ywrr_check_ywcet_active() && get_option( 'ywrr_mail_template_enable' ) === 'yes';
				$templates       = ywrr_get_templates();
				ob_start();

				if ( in_array( $email->template_type, $templates, true ) && ! $email_templates ) {
					wc_get_template( 'emails/' . $email->template_type . '/email-styles.php', array(), false, YWRR_TEMPLATE_PATH );
				} else {
					wc_get_template( 'emails/email-styles.php' );
				}

				ywrr_email_styles();
				$css = ob_get_clean();
			}

			return $css;

		}

		/**
		 * Set the link to the product
		 *
		 * @param string  $permalink   Product permalink.
		 * @param integer $customer_id Customer ID.
		 * @param boolean $no_login    Check if login should not performed.
		 *
		 * @return  string
		 * @since   1.0.4
		 */
		public function set_product_permalink( $permalink, $customer_id, $no_login = false ) {

			$link_type = get_option( 'ywrr_mail_item_link' );

			switch ( $link_type ) {
				case 'custom':
					$permalink .= ywrr_check_hash( get_option( 'ywrr_mail_item_link_hash' ) );
					break;
				case 'review':
					$permalink .= '#tab-reviews';
					break;
			}

			$query_args = array();

			if ( get_option( 'ywrr_enable_analytics' ) === 'yes' ) {

				$campaign_source  = str_replace( ' ', '%20', get_option( 'ywrr_campaign_source' ) );
				$campaign_medium  = str_replace( ' ', '%20', get_option( 'ywrr_campaign_medium' ) );
				$campaign_term    = str_replace( ',', '+', get_option( 'ywrr_campaign_term' ) );
				$campaign_content = str_replace( ' ', '%20', get_option( 'ywrr_campaign_content' ) );
				$campaign_name    = str_replace( ' ', '%20', get_option( 'ywrr_campaign_name' ) );

				$query_args['utm_source'] = $campaign_source;
				$query_args['utm_medium'] = $campaign_medium;

				if ( '' !== $campaign_term ) {
					$query_args['utm_term'] = $campaign_term;
				}

				if ( '' !== $campaign_content ) {
					$query_args['utm_content'] = $campaign_content;
				}

				$query_args['utm_campaign'] = $campaign_name;

			}

			if ( 'yes' === get_option( 'ywrr_login_from_link' ) && ! $no_login && 0 !== $customer_id ) {
				$query_args['ywrr_login'] = 1;
			}

			if ( ! empty( $query_args ) ) {
				$permalink = add_query_arg( $query_args, $permalink );
			}

			return $permalink;

		}

		/**
		 * FRONTEND FUNCTIONS
		 */

		/**
		 * Show email request checkbox in checkout page
		 *
		 * @return  void
		 * @since   1.2.6
		 */
		public function show_request_option() {

			if ( ! ywrr_check_blocklist( get_current_user_id(), '' ) ) {
				return;
			}

			/**
			 * APPLY_FILTERS: ywrr_checkout_option_label
			 *
			 * Checkout label text.
			 *
			 * @param string $value The checkout label.
			 *
			 * @return string
			 */
			$label = apply_filters( 'ywrr_checkout_option_label', get_option( 'ywrr_refuse_requests_label' ) );

			if ( ! empty( $label ) ) {

				woocommerce_form_field(
					'ywrr_receive_requests',
					array(
						'type'  => 'checkbox',
						'class' => array( 'form-row-wide' ),
						'label' => $label,
					),
					0
				);

			}

		}

		/**
		 * Save email request checkbox in checkout page
		 *
		 * @return  void
		 * @since   1.2.6
		 */
		public function save_request_option() {
			$posted = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( empty( $posted['ywrr_receive_requests'] ) && isset( $posted['billing_email'] ) && '' !== $posted['billing_email'] ) {
				ywrr_add_to_blocklist( get_current_user_id(), $posted['billing_email'] );
			}
		}

		/**
		 * Add customer request option to edit account page
		 *
		 * @return  void
		 * @since   1.2.6
		 */
		public function show_request_option_my_account() {

			/**
			 * APPLY_FILTERS: ywrr_checkout_option_label
			 *
			 * Checkout label text.
			 *
			 * @param string $value The checkout label.
			 *
			 * @return string
			 */
			$label = apply_filters( 'ywrr_checkout_option_label', get_option( 'ywrr_refuse_requests_label' ) );

			?>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="ywrr_receive_requests">
					<input
							name="ywrr_receive_requests"
							type="checkbox"
							class=""
							value="1"
						<?php checked( ywrr_check_blocklist( get_current_user_id(), '' ) ); ?>
					/> <?php echo esc_html( $label ); ?>
				</label>
			</p>
			<?php

		}

		/**
		 * Save customer request option from edit account page
		 *
		 * @param integer $customer_id Customer ID.
		 *
		 * @return  void
		 * @since   1.2.6
		 */
		public function save_request_option_my_account( $customer_id ) {
			$posted = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing

			if ( isset( $posted['billing_email'] ) && '' !== $posted['billing_email'] ) {

				if ( isset( $posted['ywrr_receive_requests'] ) ) {

					ywrr_remove_from_blocklist( $customer_id );

				} else {

					$email = get_user_meta( $customer_id, 'billing_email' );

					ywrr_add_to_blocklist( $customer_id, $email );

				}
			}

		}

		/**
		 * Initializes Javascript
		 *
		 * @return  void
		 * @since   1.0.4
		 */
		public function frontend_scripts() {

			if ( get_option( 'ywrr_mail_item_link' ) === 'product' ) {
				return;
			}

			wp_enqueue_script( 'ywrr-frontend', yit_load_js_file( YWRR_ASSETS_URL . 'js/ywrr-frontend.js' ), array( 'jquery' ), YWRR_VERSION, true );

			$params = array(
				'reviews_tab'  => get_option( 'ywrr_mail_item_link' ) === 'review' ? '#tab-reviews' : ywrr_check_hash( get_option( 'ywrr_mail_item_link_hash' ) ),
				'reviews_form' => ywrr_check_hash( get_option( 'ywrr_comment_form_id' ) ),
				'offset'       => apply_filters( 'ywrr_comment_form_offset', get_option( 'ywrr_comment_form_offset' ) ),
			);

			wp_localize_script( 'ywrr-frontend', 'ywrr', $params );

		}

		/**
		 * Redirects to login page if querystring is set
		 *
		 * @return  void
		 * @since   1.6.0
		 */
		public function redirect_to_login() {

			if ( ! is_user_logged_in() && isset( $_GET['ywrr_login'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended

				global $post;

				$product_link = $this->set_product_permalink( get_permalink( $post->ID ), 0, true );
				$redirect     = add_query_arg( 'redirect_to', rawurlencode( $product_link ), wc_get_page_permalink( 'myaccount' ) );

				wp_safe_redirect( $redirect );
				exit();

			}

		}

		/**
		 * Redirects to product page after login
		 *
		 * @param string $redirect_to Redicrect URL.
		 *
		 * @return  string
		 * @since   1.6.0
		 */
		public function login_redirect( $redirect_to ) {
			$requested = $_REQUEST; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $requested['redirect_to'] ) ) {
				return urldecode( $requested['redirect_to'] );
			} else {
				return $redirect_to;
			}
		}

		/**
		 * YITH FRAMEWORK
		 */

		/**
		 * Load plugin framework
		 *
		 * @return  void
		 * @since   1.0.0
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
		 * Action Links
		 *
		 * Add the action links to plugin admin page
		 *
		 * @param array $links links plugin array.
		 *
		 * @return  array
		 * @since   1.0.0
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->panel_page, true, YWRR_SLUG );

			return $links;
		}

		/**
		 * Plugin row meta
		 *
		 * Add the action links to plugin admin page
		 *
		 * @param array  $new_row_meta_args Row meta args.
		 * @param array  $plugin_meta       Plugin meta.
		 * @param string $plugin_file       Plugin File.
		 * @param array  $plugin_data       Plugin data.
		 * @param string $status            Status.
		 * @param string $init_file         Init file.
		 *
		 * @return  array
		 * @since   1.0.0
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWRR_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) === $plugin_file ) {
				$new_row_meta_args['is_premium'] = true;
				$new_row_meta_args['slug']       = YWRR_SLUG;

			}

			return $new_row_meta_args;
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once 'plugin-fw/licence/lib/yit-licence.php';
				require_once 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YWRR_INIT, YWRR_SECRET_KEY, YWRR_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YWRR_SLUG, YWRR_INIT );
		}

		/**
		 * Register privacy text
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function include_privacy_text() {
			include_once 'includes/class-ywrr-privacy.php';
		}

		/**
		 * Add Plugin Requirements
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function set_plugin_requirements() {

			$plugin_data  = get_plugin_data( plugin_dir_path( __FILE__ ) . '/init.php' );
			$plugin_name  = $plugin_data['Name'];
			$requirements = array(
				'wp_cron_enabled' => true,
			);
			yith_plugin_fw_add_requirements( $plugin_name, $requirements );
		}
	}

}
