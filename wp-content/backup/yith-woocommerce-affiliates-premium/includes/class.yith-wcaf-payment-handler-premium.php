<?php
/**
 * Payment Handler Premium class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Payment_Handler_Premium' ) ) {
	/**
	 * WooCommerce Payment Handler Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Payment_Handler_Premium extends YITH_WCAF_Payment_Handler {
		/**
		 * Single instance of the class for each token
		 *
		 * @var \YITH_WCAF_Payment_Handler_Premium
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/**
		 * Payment schedule event
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_payment_type = 'manually';

		/**
		 * Gateway for automatic payments
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_payment_default_gateway = null;

		/**
		 * Date for day-based automatic payments
		 *
		 * @var int
		 * @since 1.0.0
		 */
		protected $_payment_date = 15;

		/**
		 * Whether to pay only commissions older than a certan number of days
		 *
		 * @var bool
		 * @since 1.2.2
		 */
		protected $_pay_only_old_commissions = false;

		/**
		 * Number of days before a brand new commission should be paid
		 *
		 * @var int
		 * @since 1.2.2
		 */
		protected $_payment_commission_age = 15;

		/**
		 * Threshold for earnings-based automatic payments
		 *
		 * @var float
		 * @since 1.0.0
		 */
		protected $_payment_threshold = 50;

		/**
		 * Array of available fields for invoices
		 *
		 * @var array
		 */
		protected $_available_invoice_fields = array();

		/**
		 * Registered gateways
		 *
		 * @var mixed
		 * @since 1.0.0
		 */
		private $_available_gateways = array();

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAF_Payment_Handler_Premium
		 * @since 1.0.0
		 */
		public function __construct() {
			// init class
			$this->_retrieve_options();
			parent::__construct();
			$this->init_available_gateways();

			// handle gateway notification
			add_action( 'yith_wcaf_ipn_received', array( $this, 'handle_notification' ), 10, 1 );

			// setup schedules
			add_action( 'wp', array( $this, 'pay_affiliates_setup_schedule' ) );
			add_action( 'update_option_yith_wcaf_payment_type', array( $this, 'pay_commissions_delete_schedule' ) );

			// automatic payment actions
			add_action( 'yith_wcaf_commission_status_pending', array( $this, 'pay_on_threshold_reached' ), 15, 1 );
			add_action( 'pay_commissions_action_schedule', array( $this, 'pay_commissions' ) );

			// withdraw actions
			add_action( 'wp_ajax_get_withdraw_amount', array( $this, 'get_withdraw_amount_via_ajax' ) );
			add_filter( 'yith_wcaf_available_endpoints', array( $this, 'add_withdraw_endpoint' ), 10 );
			add_action( 'update_option_yith_wcaf_payment_type', array( $this, 'fix_withdraw_endpoint' ), 10, 2 );
			add_filter( 'yith_wcaf_custom_dashboard_sections', array( $this, 'add_withdraw_section' ), 10, 3 );
			add_action( 'init', array( $this, 'download_invoice' ), 15 );

			// add commissions panel handling
			add_action( 'admin_action_yith_wcaf_complete_payment', array( $this, 'handle_payments_panel_actions' ) );

			add_filter( 'yith_wcaf_general_settings', array( $this, 'filter_general_settings' ) );
		}

		/* === INIT METHODS === */

		/**
		 * Retrieve options for payment from db
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function _retrieve_options() {
			$this->_payment_type             = get_option( 'yith_wcaf_payment_type', $this->_payment_type );
			$this->_payment_default_gateway  = get_option( 'yith_wcaf_payment_default_gateway', $this->_payment_default_gateway );
			$this->_payment_date             = get_option( 'yith_wcaf_payment_date', $this->_payment_date );
			$this->_pay_only_old_commissions = 'yes' == get_option( 'yith_wcaf_payment_pay_only_old_commissions', 'no' );
			$this->_payment_commission_age   = get_option( 'yith_wcaf_payment_commission_age', $this->_payment_commission_age );
			$this->_payment_threshold        = get_option( 'yith_wcaf_payment_threshold', $this->_payment_threshold );
		}

		/**
		 * Filter general settings, to add notification settings
		 *
		 * @param $settings mixed Original settings array
		 *
		 * @return mixed Filtered settings array
		 * @since 1.0.0
		 */
		public function filter_general_settings( $settings ) {
			$gateways                = $this->get_available_gateways();
			$gateway_options         = array();
			$gateway_options['none'] = __( 'None', 'yith-woocommerce-affiliates' );

			if ( ! empty( $gateways ) ) {
				foreach ( $gateways as $id => $gateway ) {
					$gateway_options[ $id ] = $gateway['label'];
				}
			}

			$payment_settings = array_merge(
				array(
					'payment-options' => array(
						'title' => __( 'Payment', 'yith-woocommerce-affiliates' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'yith_wcaf_payment_options'
					),

					'payment-type' => array(
						'title'    => __( 'Payment type', 'yith-woocommerce-affiliates' ),
						'type'     => 'select',
						'desc'     => __( 'Choose payment mode to pay commissions to your affiliates', 'yith-woocommerce-affiliates' ),
						'id'       => 'yith_wcaf_payment_type',
						'options'  => array(
							'manually'                   => __( 'Manually', 'yith-woocommerce-affiliates' ),
							'automatically_on_threshold' => __( 'Automatically when reaching a threshold', 'yith-woocommerce-affiliates' ),
							'automatically_on_date'      => __( 'Automatically on a specific month date', 'yith-woocommerce-affiliates' ),
							'automatically_on_both'      => __( 'Automatically on a specific month date, if a specific threshold is reached', 'yith-woocommerce-affiliates' ),
							'automatically_every_day'    => __( 'Automatically every day', 'yith-woocommerce-affiliates' ),
							// @since 1.3.0
							'let_user_request'           => __( 'Let user request payment', 'yith-woocommerce-affiliates' )
						),
						'default'  => 'manually',
						'desc_tip' => true
					),

					'payment-default-gateway' => array(
						'title'    => __( 'Default gateway', 'yith-woocommerce-affiliates' ),
						'type'     => 'select',
						'desc'     => __( 'Choose a gateway to execute automatic payments; if you select "none" payments will only be registered but no payment requests will be issued to the gateway', 'yith-woocommerce-affiliates' ),
						'id'       => 'yith_wcaf_payment_default_gateway',
						'options'  => $gateway_options,
						'default'  => 'none',
						'desc_tip' => true,
						'css'      => 'min-width: 300px'
					),

					'payment-date' => array(
						'title'             => __( 'Payment date', 'yith-woocommerce-affiliates' ),
						'type'              => 'number',
						'desc'              => __( 'Choose a day of the month for commission payments', 'yith-woocommerce-affiliates' ),
						'id'                => 'yith_wcaf_payment_date',
						'css'               => 'max-width: 50px;',
						'default'           => 15,
						'custom_attributes' => array(
							'min'  => 1,
							'max'  => 28,
							'step' => 1
						),
						'desc_tip'          => true
					),

					'payment-threshold' => array(
						'title'             => __( 'Payment threshold', 'yith-woocommerce-affiliates' ),
						'type'              => 'number',
						'desc'              => __( 'Choose a minimum amount that an affiliate must earn before a payment is issued', 'yith-woocommerce-affiliates' ),
						'id'                => 'yith_wcaf_payment_threshold',
						'css'               => 'min-width: 50px;',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 'any'
						),
						'default'           => '50',
						'desc_tip'          => true
					),

					'payment-pay-only-old-commissions' => array(
						'title'   => __( 'Pay only commissions older than', 'yith-woocommerce-affiliates' ),
						// @since 1.2.2
						'type'    => 'checkbox',
						'desc'    => __( 'Choose whether to pay automatically only commissions older than a certain number of days or not', 'yith-woocommerce-affiliates' ),
						// @since 1.2.2
						'id'      => 'yith_wcaf_payment_pay_only_old_commissions',
						'default' => 'no',
					),

					'payment-commission-age' => array(
						'title'             => __( 'Commission age', 'yith-woocommerce-affiliates' ),
						// @since 1.2.2
						'type'              => 'number',
						'desc'              => __( 'Choose the minimum amount of days that should pass before commissions can be paid automatically', 'yith-woocommerce-affiliates' ),
						// @since 1.2.2
						'id'                => 'yith_wcaf_payment_commission_age',
						'css'               => 'max-width: 50px;',
						'default'           => 15,
						'custom_attributes' => array(
							'min'  => 1,
							'step' => 1
						),
						'desc_tip'          => true
					),

					'payment-require-invoice' => array(
						'title'   => __( 'Require invoice', 'yith-woocommerce-affiliates' ),
						'type'    => 'checkbox',
						'desc'    => __( 'Require customer an invoice before allowing payment to be requested', 'yith-woocommerce-affiliates' ),
						'id'      => 'yith_wcaf_payment_require_invoice',
						'default' => 'yes'
					),

					// @since 1.3.0
					'payment-invoice-mode'    => array(
						'title'    => __( 'Invoice mode', 'yith-woocommerce-affiliates' ),
						'type'     => 'select',
						'desc'     => __( 'Choose how user should submit his invoice', 'yith-woocommerce-affiliates' ),
						'id'       => 'yith_wcaf_payment_invoice_mode',
						'options'  => array(
							'upload'   => __( 'Let user upload his custom invoice', 'yith-woocommerce-affiliates' ),
							'generate' => __( 'Generate invoice from customer data', 'yith-woocommerce-affiliates' ),
							'both'     => __( 'Let user choose his preferred method', 'yith-woocommerce-affiliates' )
						),
						'default'  => 'both',
						'desc_tip' => true
					),

					'payment-invoice-example' => array(
						'title'    => __( 'Invoice example', 'yith-woocommerce-affiliates' ),
						'type'     => 'url',
						'desc'     => __( 'Enter url of an example invoice that you want to show to your affiliates', 'yith-woocommerce-affiliates' ),
						'id'       => 'yith_wcaf_payment_invoice_example',
						'default'  => '',
						'css'      => 'min-width: 300px;',
						'desc_tip' => true
					),

					'payment-invoice-company-section' => array(
						'title'    => __( 'Company details', 'yith-woocommerce-affiliates' ),
						'type'     => 'textarea',
						'desc'     => __( 'Enter details about your company that you want to show in your invoice', 'yith-woocommerce-affiliates' ),
						'id'       => 'yith_wcaf_payment_invoice_company_section',
						'default'  => '',
						'css'      => 'min-width: 300px; min-height: 100px;',
						'desc_tip' => true
					),

					'payment-invoice-fields' => array(
						'title'   => __( 'Invoice fields', 'yith-woocommerce-affiliates' ),
						'type'    => 'multiselect',
						'desc'    => __( 'Select fields that you want to require to your affiliates to generate invoice', 'yith-woocommerce-affiliates' ),
						'id'      => 'yith_wcaf_payment_invoice_fields',
						'options' => $this->get_available_invoice_fields(),
						'default' => array( 'first_name', 'last_name', 'address', 'city', 'vat' ),
						'css'     => 'min-width: 300px;',
						'class'   => 'wc-enhanced-select',
					),

					'payment-invoice-show-terms-field'  => array(
						'title'   => __( 'Show Terms & Conditions field', 'yith-woocommerce-affiliates' ),
						'type'    => 'checkbox',
						'desc'    => __( 'Show "Terms & Condition" checkbox on withdraw form', 'yith-woocommerce-affiliates' ),
						'id'      => 'yith_wcaf_payment_invoice_show_terms_field',
						'default' => 'no'
					),
					'payment-invoice-terms-label'       => array(
						'title'   => __( 'Terms & Conditions label', 'yith-woocommerce-affiliates' ),
						'type'    => 'text',
						'desc'    => __( 'Label for Terms & Condition checkbox; use <code>%TERMS%</code> placeholder to include a link to Terms & Condition page', 'yith-woocommerce-affiliates' ),
						'id'      => 'yith_wcaf_payment_invoice_terms_label',
						'default' => __( 'Please, read an accept our %TERMS%' )
					),
					'payment-invoice-terms-anchor-url'  => array(
						'title'   => __( 'Terms & Conditions anchor url', 'yith-woocommerce-affiliates' ),
						'type'    => 'text',
						'desc'    => __( 'Url to Terms & Conditions page; will be used to generate anchor inside Terms & Conditions label', 'yith-woocommerce-affiliates' ),
						'id'      => 'yith_wcaf_payment_invoice_terms_anchor_url',
						'default' => ''
					),
					'payment-invoice-terms-anchor-text' => array(
						'title'   => __( 'Terms & Conditions anchor text', 'yith-woocommerce-affiliates' ),
						'type'    => 'text',
						'desc'    => __( 'Text used to generate anchor inside Terms & Conditions label', 'yith-woocommerce-affiliates' ),
						'id'      => 'yith_wcaf_payment_invoice_terms_anchor_text',
						'default' => ''
					),

					'payment-invoice-template' => array(
						'title'    => __( 'Invoice template', 'yith-woocommerce-affiliates' ),
						'type'     => 'yith_wcaf_template',
						'id'       => 'yith_wcaf_payment_invoice_template',
						'template' => 'invoices/affiliate-invoice.php'
					),

					'payment-pending-notify-admin' => array(
						'title'   => __( 'Notify admin', 'yith-woocommerce-affiliates' ),
						'type'    => 'checkbox',
						'desc'    => sprintf( '%s <a href="%s">%s</a>', __( 'Notify admin when a new payment is issued; customize email on', 'yith-woocommerce-affiliates' ), esc_url( add_query_arg( array(
							'page'    => 'wc-settings',
							'tab'     => 'email',
							'section' => 'yith_wcaf_admin_paid_commission_email'
						), admin_url( 'admin.php' ) ) ), __( 'WooCommerce Settings Page', 'yith-woocommerce-affiliates' ) ),
						'id'      => 'yith_wcaf_payment_pending_notify_admin',
						'default' => 'yes'
					),

					'payment-options-end' => array(
						'type' => 'sectionend',
						'id'   => 'yith_wcaf_payment_options'
					),
				),
				apply_filters( 'yith_wcaf_gateway_options', array() )
			);

			$settings['settings'] = yith_wcaf_append_items( $settings['settings'], 'commission-options-end', $payment_settings );

			return $settings;
		}

		/* === HELPER METHODS === */

		/**
		 * Returns available fields for invoice
		 *
		 * @return array Available fields, as name => label array
		 */
		public function get_available_invoice_fields() {

			if ( empty( $this->_available_invoice_fields ) ) {
				$this->_available_invoice_fields = apply_filters( 'yith_wcaf_invoice_fields', array(
					'number'            => __( 'Invoice number', 'yith-woocommerce-affiliates' ),
					'type'              => __( 'Type', 'yith-woocommerce-affiliates' ),
					'first_name'        => __( 'First name', 'yith-woocommerce-affiliates' ),
					'last_name'         => __( 'Last name', 'yith-woocommerce-affiliates' ),
					'company'           => __( 'Company', 'yith-woocommerce-affiliates' ),
					'billing_country'   => __( 'Billing country', 'yith-woocommerce-affiliates' ),
					'billing_address_1' => __( 'Billing address', 'yith-woocommerce-affiliates' ),
					'billing_city'      => __( 'Billing city', 'yith-woocommerce-affiliates' ),
					'billing_state'     => __( 'Billing state', 'yith-woocommerce-affiliates' ),
					'billing_postcode'  => __( 'Billing zip code', 'yith-woocommerce-affiliates' ),
					'vat'               => __( 'Company VAT', 'yith-woocommerce-affiliates' ),
					'cif'               => __( 'CIF/SSN', 'yith-woocommerce-affiliates' ),
				) );
			}

			return $this->_available_invoice_fields;
		}

		/**
		 * Register payments for a bunch of commissions; will create different mass pay foreach affiliate referred by commissions
		 *
		 * @param $commissions_id       array|int Array of commissions to pay IDs, or single commission id
		 * @param $proceed_with_payment bool Whether to call gateways to pay, or just register payments
		 * @param $gateway              bool|string Gateway to use for payments; default value (false) force to use default gateway
		 *
		 * @return mixed Array with payment status, when \$proceed_with_payment is enabled; false otherwise
		 */
		public function register_payment( $commissions_id, $proceed_with_payment = true, $gateway = false ) {
			// if no commission passed, return
			if ( empty( $commissions_id ) ) {
				return array(
					'status'         => false,
					'messages'       => __( 'You have to select at least one commission', 'yith-woocommerce-affiliates' ),
					'can_be_paid'    => array(),
					'cannot_be_paid' => array()
				);
			}

			// if single commission id provided, convert it to array
			if ( ! is_array( $commissions_id ) ) {
				$commissions_id = (array) $commissions_id;
			}

			$payments       = array();
			$to_pay         = array();
			$can_be_paid    = array();
			$cannot_be_paid = array();

			foreach ( $commissions_id as $id ) {
				$commission = YITH_WCAF_Commission_Handler()->get_commission( $id );

				// if can't find commission, continue
				if ( ! $commission ) {
					continue;
				}

				$affiliate_id = $commission['affiliate_id'];
				$affiliate    = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $affiliate_id, true );

				// if can't find affiliate, continue
				if ( ! $affiliate ) {
					continue;
				}

				// if current status doesn't allow payments, continue
				$available_status_change = YITH_WCAF_Commission_Handler()->get_available_status_change( $id );
				if ( ! in_array( 'pending-payment', $available_status_change ) && ! in_array( $commission['status'], YITH_WCAF_Commission_Handler_Premium()->payment_status ) ) {
					continue;
				}

				$payment_email = $affiliate['payment_email'];

				// commission can be paid; switch status to pending-payment
				$commission_status = $this->get_commission_status_after_payment( $gateway, $proceed_with_payment );
				YITH_WCAF_Commission_Handler_Premium()->change_commission_status( $id, $commission_status['status'], $commission_status['message'] );

				// register commissions sent to payment processor for payment
				$can_be_paid[] = $id;

				// if there is no payment registered for the affiliate, set one
				if ( ! isset( $payments[ $affiliate_id ] ) ) {
					$payments[ $affiliate_id ]                  = array();
					$payments[ $affiliate_id ]['affiliate_id']  = $affiliate_id;
					$payments[ $affiliate_id ]['payment_email'] = $payment_email;
					$payments[ $affiliate_id ]['gateway']       = $gateway ? $gateway : '';
					$payments[ $affiliate_id ]['amount']        = 0;
					$payments[ $affiliate_id ]['commissions']   = array();
				}

				$payments[ $affiliate_id ]['commissions'][] = $commission;
				$payments[ $affiliate_id ]['amount']        += floatval( $commission['amount'] );
			}

			// register payments
			if ( ! empty( $payments ) ) {
				foreach ( $payments as $payment ) {
					$commissions  = $payment['commissions'];
					$payment_args = $payment;
					unset( $payment_args['commissions'] );

					$payment_id = $this->add( $payment_args, $commissions );

					$proceed_with_payment = apply_filters( 'yith_wcaf_proceed_with_payment', $proceed_with_payment, $payment_id, $payment );

					if ( $payment_id && $proceed_with_payment ) {
						$to_pay[] = $payment_id;
					}
				}
			}

			// register commissions that cannot be sent to payment processor for payment
			$cannot_be_paid = array_diff( $commissions_id, $can_be_paid );

			// proceed with payments
			if ( ! empty( $to_pay ) ) {
				$res = $this->pay( $gateway, $to_pay );

				return array(
					'status'         => $res['status'],
					'messages'       => $res['messages'],
					'can_be_paid'    => $can_be_paid,
					'cannot_be_paid' => $cannot_be_paid
				);
			}

			return array(
				'status'         => true,
				'messages'       => __( 'Payment correctly registered', 'yith-woocommerce-affiliates' ),
				'can_be_paid'    => $can_be_paid,
				'cannot_be_paid' => $cannot_be_paid
			);
		}

		/**
		 * Register payment for all pending commission of an affiliate; will create different mass pay foreach affiliate referred by commissions
		 *
		 * @param $affiliate_id         int Affiliate id
		 * @param $proceed_with_payment bool Whether to call gateways to pay, or just register payments
		 * @param $gateway              bool|string Gateway to use for payments; default value (false) force to use default gateway
		 * @param $apply_filters        bool Whether to apply any kind of filters to the query that retrieves commissions to pay (affiliate_id and commission status will be filtered anyway)
		 *
		 * @return mixed Array with payment status, when \$proceed_with_payment is enabled; false otherwise
		 */
		public function pay_all_affiliate_commissions( $affiliate_id, $proceed_with_payment = true, $gateway = false, $apply_filters = false ) {
			$args = array(
				'affiliate_id' => $affiliate_id,
				'status'       => 'pending'
			);
			if ( $apply_filters ) {
				if ( $this->_pay_only_old_commissions && ! empty( $this->_payment_commission_age ) ) {
					$current_time   = time();
					$threshold_time = $current_time - ( $this->_payment_commission_age * DAY_IN_SECONDS );

					$args['interval'] = array(
						'end_date' => date( 'Y-m-d H:i:s', $threshold_time ),
					);
				}

				$args = apply_filters( 'yith_wcaf_pay_all_affiliates_commissions_artgs', $args );
			}

			$commissions = YITH_WCAF_Commission_Handler()->get_commissions( $args );

			if ( empty( $commissions ) ) {
				return array(
					'status'         => false,
					'message'        => __( 'Affiliate needs to have at least one unpaid commission', 'yith-woocommerce-affiliates' ),
					'can_be_paid'    => array(),
					'cannot_be_paid' => array()
				);
			}

			$commissions_ids = wp_list_pluck( $commissions, 'ID' );

			return $this->register_payment( $commissions_ids, $proceed_with_payment, $gateway );
		}

		/**
		 * Register payments for all commissions older then a specific timestamp; will create different mass pay foreach affiliate referred by commissions
		 *
		 * @param $threshold_timestamp  int Timestamp that should be used as threshold; older pending commissions will be paid
		 * @param $proceed_with_payment bool Whether to call gateways to pay, or just register payments
		 * @param $gateway              bool|string Gateway to use for payments; default value (false) force to use default gateway
		 *
		 * @return mixed Array with payment status, when \$proceed_with_payment is enabled; false otherwise
		 */
		public function pay_all_commissions_older_than( $threshold_timestamp, $proceed_with_payment = true, $gateway = false ) {
			$commissions = YITH_WCAF_Commission_Handler()->get_commissions( array(
				'status'   => 'pending',
				'interval' => array(
					'end_date' => date( 'Y-m-d H:i:s', $threshold_timestamp ),
				),
			) );

			if ( empty( $commissions ) ) {
				return array(
					'status'         => false,
					'message'        => __( 'Affiliate needs to have at least one unpaid commission', 'yith-woocommerce-affiliates' ),
					'can_be_paid'    => array(),
					'cannot_be_paid' => array()
				);
			}

			$commissions_ids = wp_list_pluck( $commissions, 'ID' );

			return $this->register_payment( $commissions_ids, $proceed_with_payment, $gateway );
		}

		/* === GATEWAYS HANDLING METHODS === */

		/**
		 * Init available gateways
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function init_available_gateways() {
			$this->_available_gateways = apply_filters(
				'yith_wcaf_available_gateways',
				array_merge(
					$this->_available_gateways,
					array(
						'paypal' => array(
							'path'     => YITH_WCAF_INC . 'gateways/class.yith-wcaf-paypal-gateway.php',
							'label'    => __( 'PayPal', 'yith-woocommerce-affiliates' ),
							'class'    => 'YITH_WCAF_Paypal_Gateway',
							'mass_pay' => true
						)
					),
					! class_exists( 'YITH_Funds' ) ? array() : array(
						'funds' => array(
							'path'     => YITH_WCAF_INC . 'gateways/class.yith-wcaf-yith-ywf-gateway.php',
							'label'    => __( 'Account Funds', 'yith-woocommerce-affiliates' ),
							'class'    => 'YITH_WCAF_YITH_YWF',
							'mass_pay' => true
						)
					),
					! class_exists( 'YITH_PayPal_PayOuts' ) ? array() : array(
						'payouts' => array(
							'path'     => YITH_WCAF_INC . 'gateways/class.yith-wcaf-payouts-gateway.php',
							'label'    => __( 'PayPal Payouts', 'yith-woocommerce-affiliates' ),
							'class'    => 'YITH_WCAF_PayOuts_Gateway',
							'mass_pay' => true
						)
					)
				)
			);


			// include gateways files
			if ( ! empty( $this->_available_gateways ) ) {
				foreach ( $this->_available_gateways as $gateway_id => $gateway_info ) {
					require_once( apply_filters( 'yith_wcaf_gateway_inclusion_path', $gateway_info['path'] ) );

					if ( function_exists( $gateway_info['class'] ) ) {
						$gateway_class = $gateway_info['class'];
						$gateway_class();
					}
				}
			}
		}

		/**
		 * Pay a payment instance previously created
		 *
		 * @param $gateway           string A valid gateway slug
		 * @param $payment_instances int|array Payment id(s)
		 *
		 * @return bool|mixed Payment status; false on failure
		 */
		public function pay( $gateway, $payment_instances ) {
			// if not gateway is sent, mark as completed
			if ( ! $gateway ) {
				if ( ! empty( $payment_instances ) ) {
					foreach ( $payment_instances as $instance ) {
						$this->change_payment_status( $instance, 'completed' );
						do_action( 'yith_wcaf_payment_sent', $instance );
					}
					do_action( 'yith_wcaf_payments_sent', (array) $payment_instances );
				}

				return array(
					'status'   => true,
					'messages' => __( 'Payments registered correctly', 'yith-woocommerce-affiliates' )
				);
			}

			// if not a registered gateway, return false
			if ( ! in_array( $gateway, array_keys( $this->_available_gateways ) ) ) {
				return array(
					'status'   => false,
					'messages' => __( 'No gateway found with the specified ID', 'yith-woocommerce-affiliates' )
				);
			}

			$gateway_class = $this->_available_gateways[ $gateway ]['class'];

			// if does not exist a singleton instance of the gateway, return false
			if ( ! function_exists( $gateway_class ) ) {
				return array(
					'status'   => false,
					'messages' => __( 'Gateway class doesn\'t match required structure; missing singleton access function', 'yith-woocommerce-affiliates' )
				);
			}

			// if does not exists method pay on gateway object, return false
			if ( ! method_exists( $gateway_class(), 'pay' ) ) {
				return array(
					'status'   => false,
					'messages' => __( 'Gateway class doesn\'t match required structure; missing pay() method', 'yith-woocommerce-affiliates' )
				);
			}

			// return payment status
			$res = $gateway_class()->pay( $payment_instances );

			if ( $res['status'] ) {
				do_action( 'yith_wcaf_payments_sent', (array) $payment_instances );
			}

			return $res;
		}

		/**
		 * Handle IPN notification (gateway should call specific action to trigger this method)
		 *
		 * @param $payment_detail mixed Payment details received by Gateway
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function handle_notification( $payment_detail ) {
			$request_id = $payment_detail['unique_id'];

			$payment = $this->get_payment( $request_id );

			if ( ! $payment ) {
				return;
			}

			$status = $payment['status'];

			if ( $status != 'pending' ) {
				return;
			}

			if ( $payment_detail['status'] == 'Completed' ) {
				$new_status = 'completed';
			} elseif ( in_array( $payment_detail['status'], array( 'Failed', 'Returned', 'Reversed', 'Blocked' ) ) ) {
				$new_status = 'cancelled';
			} else {
				$new_status = 'pending';
			}

			if ( $new_status != 'pending' ) {
				$this->change_payment_status( $request_id, $new_status );

				if ( $new_status == 'completed' ) {
					$this->update( $request_id, array(
						'transaction_key' => $payment_detail['txn_id']
					) );
				}
			}
		}

		/**
		 * Returns a list of available gateways
		 *
		 * @return mixed List of available gateways
		 * @since 1.0.0
		 */
		public function get_available_gateways() {
			return $this->_available_gateways;
		}

		/**
		 * Returns readable version of gateway name
		 *
		 * @param $gateway string Gateway unique ID
		 *
		 * @return string Human friendly version of gateway name
		 * @since 1.0.0
		 */
		public function get_readable_gateway( $gateway ) {
			$label = '';
			if ( isset( $this->_available_gateways[ $gateway ] ) ) {
				$label = $this->_available_gateways[ $gateway ]['label'];
			}

			return apply_filters( "yith_wcaf_{$gateway}_payment_gateway_name", $label );
		}

		/* === WITHDRAW METHODS */

		/**
		 * Check whether payment has invoice
		 *
		 * @param $payment_id int Payment ID
		 *
		 * @return string|bool Invoice path when invoice exists, false otherwise
		 */
		public function has_invoice( $payment_id ) {
			$invoice_path = apply_filters( 'yith_wcaf_get_invoice_path', YITH_WCAF_INVOICES_DIR . $payment_id . '.pdf' );

			return file_exists( $invoice_path ) ? $invoice_path : false;
		}

		/**
		 * Get path to invoice
		 *
		 * @param $payment_id int Payment ID
		 *
		 * @return string Path to invoice, or empty if there is no invoice
		 */
		public function get_invoice_path( $payment_id ) {
			if ( $invoice = $this->has_invoice( $payment_id ) ) {
				return $invoice;
			}

			return '';
		}

		/**
		 * Get url to invoice
		 *
		 * @param $payment_id int Payment ID
		 *
		 * @return string Url to invoice, or empty if there is no invoice
		 */
		public function get_invoice_url( $payment_id ) {
			if ( $invoice = $this->has_invoice( $payment_id ) ) {
				return apply_filters( 'yith_wcaf_get_invoice_url', YITH_WCAF_INVOICES_URL . $payment_id . '.pdf' );
			}

			return '';
		}

		/**
		 * Get url to let user download invoice
		 *
		 * @param $payment_id int Payment ID
		 *
		 * @return string Url to download invoice, or empty if there is no invoice
		 */
		public function get_invoice_publishable_url( $payment_id ) {
			if ( $invoice = $this->has_invoice( $payment_id ) ) {
				return apply_filters( 'yith_wcaf_get_invoice_publishable_url', wp_nonce_url( add_query_arg( 'download_payment_invoice', $payment_id ), 'download-invoice' ) );
			}

			return '';
		}

		/**
		 * Starts download of invoice when customer with correct permissions visit publishable link
		 *
		 * @return void
		 */
		public function download_invoice() {
			if ( ! isset( $_GET['download_payment_invoice'] ) ) {
				return;
			}

			$redirect = remove_query_arg( array( 'download_payment_invoice', '_wpnonce' ) );

			if ( ! is_user_logged_in() || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'download-invoice' ) ) {
				( ! is_admin() ) && wc_add_notice( __( 'You haven\'t sufficient permission to access this content!', 'yith-woocommerce-affiliates' ) );

				wp_safe_redirect( $redirect );
				die;
			}

			$payment_id = intval( $_GET['download_payment_invoice'] );
			$payment    = $this->get_payment( $payment_id );

			if ( $payment && ( $invoice_path = $this->get_invoice_path( $payment_id ) ) && ( $payment['user_id'] == get_current_user_id() || current_user_can( 'manage_woocommerce' ) ) ) {
				WC_Download_Handler::download( $invoice_path, 0 );
				die;
			}

			( ! is_admin() ) && wc_add_notice( __( 'There was an error while processing download of your invoice; please, try again later!', 'yith-woocommerce-affiliates' ) );
			wp_safe_redirect( $redirect );
			die;

		}

		/**
		 * Add endpoint for withdraw section
		 *
		 * @param $endpoints array Array of defined endpoints
		 *
		 * @return array Array of filtered endpoints
		 * @since 1.3.0
		 */
		public function add_withdraw_endpoint( $endpoints ) {
			if ( 'let_user_request' == $this->_payment_type ) {
				$pivot = array_search( 'payments', array_keys( $endpoints ) );

				if ( ! $pivot ) {
					$endpoints['withdraw'] = __( 'Withdraw', 'yith-woocommerce-affiliates' );
				} else {
					$first_chunk  = array_slice( $endpoints, 0, $pivot + 1 );
					$second_chunk = array_slice( $endpoints, $pivot );

					$endpoints = array_merge(
						$first_chunk,
						array(
							'withdraw' => __( 'Withdraw', 'yith-woocommerce-affiliates' )
						),
						$second_chunk
					);
				}
			}

			return $endpoints;
		}

		/**
		 * Mark rewrite rules for flush when adding withdraw endpoint
		 *
		 * @param $old_value string Old yith_wcaf_payment_type option value
		 * @param $value     string New yith_wcaf_payment_type option value
		 *
		 * @return void
		 * @since 1.3.0
		 */
		public function fix_withdraw_endpoint( $old_value, $value ) {
			if ( 'let_user_request' != $old_value && 'let_user_request' == $value ) {
				update_option( '_yith_wcaf_flush_rewrite_rules', true );
			}
		}

		/**
		 * Returns output of withdraw endpoint when correct queryvar is found
		 *
		 * @param $content    string Ednpoint content
		 * @param $query_vars array Current query vars
		 * @param $atts       mixed Array of shortcodes attributes
		 *
		 * @return string Section content, or empty string
		 * @since 1.0.0
		 */
		public function add_withdraw_section( $content, $query_vars, $atts ) {
			if ( 'let_user_request' == $this->_payment_type && isset( $query_vars['withdraw'] ) ) {
				$content = YITH_WCAF_Shortcode_Premium::affiliate_dashboard_withdraw( $atts );
			}

			return $content;
		}

		/**
		 * Check whether affiliate can withdraw or not
		 *
		 * @param $affiliate_id int Affiliate id
		 *
		 * @return bool True or false, depending on number of active payments affiliate has
		 */
		public function can_affiliate_withdraw( $affiliate_id ) {
			$active_payments = $this->get_payments( array(
				'status'       => array(
					'on-hold',
					'pending'
				),
				'affiliate_id' => $affiliate_id
			) );

			return apply_filters( 'yit_wcaf_can_affiliate_withdraw', empty( $active_payments ), $active_payments, $affiliate_id );
		}

		/**
		 * Print withdraw amount using start and end date to retrieve related commissions
		 *
		 * @return void
		 */
		public function get_withdraw_amount_via_ajax() {
			check_ajax_referer( 'get-withdraw-amount', 'security' );

			$from = isset( $_POST['withdraw_from'] ) ? sanitize_text_field( $_POST['withdraw_from'] ) : false;
			$to   = isset( $_POST['withdraw_to'] ) ? sanitize_text_field( $_POST['withdraw_to'] ) : false;

			if ( ! is_user_logged_in() ) {
				echo wc_price( 0 );
				die;
			}

			if ( ! preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $from ) || ! preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $to ) ) {
				echo wc_price( 0 );
				die;
			}

			$user_id   = get_current_user_id();
			$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( $user_id );

			$commissions = YITH_WCAF_Commission_Handler()->get_commissions( array(
				'status'       => 'pending',
				'interval'     => array(
					'start_date' => date( 'Y-m-d 00:00:00', strtotime( $from ) ),
					'end_date'   => date( 'Y-m-d 23:59:59', strtotime( $to ) )
				),
				'affiliate_id' => $affiliate['ID']
			) );

			if ( empty( $commissions ) ) {
				echo wc_price( 0 );
				die;
			}

			$payment_amount = array_sum( array_map( 'floatval', wp_list_pluck( $commissions, 'amount' ) ) );

			echo wc_price( $payment_amount );
			die;
		}

		/**
		 * Create a new invoice and store it in the appropriate directory
		 *
		 * @param $payment_id int Payment id (default file name)
		 * @param $amount     float Amount to be reported in the invoice
		 * @param $from       string Formatted from date
		 * @param $to         string Formatted to date
		 * @param $args       array Array of additional params
		 *
		 * @return void
		 */
		public function generate_invoice( $payment_id, $amount, $from, $to, $args = array() ) {
			$invoice_fields          = array_keys( $this->get_available_invoice_fields() );
			$invoice_company_section = get_option( 'yith_wcaf_payment_invoice_company_section', '' );

			$site_url          = isset( $args['site_url'] ) ? $args['site_url'] : str_replace( array(
				'https://',
				'http://'
			), '', get_site_url() );
			$affiliate_landing = isset( $args['affiliate_landing'] ) ? $args['affiliate_landing'] : YITH_WCAF()->get_affiliate_dashboard_url();
			$current_date      = isset( $args['current_date'] ) ? $args['current_date'] : date_i18n( wc_date_format() );
			$currency          = isset( $args['currency'] ) ? $args['currency'] : get_woocommerce_currency();

			$replacements = array(
				'{{payment_id}}'        => $payment_id,
				'{{current_date}}'      => $current_date,
				'{{start_date}}'        => date_i18n( wc_date_format(), strtotime( $from ) ),
				'{{end_date}}'          => date_i18n( wc_date_format(), strtotime( $to ) ),
				'{{site_url}}'          => $site_url,
				'{{title}}'             => apply_filters( 'yith_wcaf_invoice_title', sprintf( _x( 'Affiliate commission withdrawal on %s', 'Withdraw invoice description', 'yith-woocommerce-affiliates' ), $site_url ) ),
				'{{description}}'       => apply_filters( 'yith_wcaf_invoice_description', sprintf( _x( 'Affiliate commission withdrawal on %s', 'Withdraw invoice description', 'yith-woocommerce-affiliates' ), $site_url ) ),
				'{{affiliate_program}}' => apply_filters( 'yith_wcaf_invoice_affiliate_program', sprintf( _x( '%s Affiliate Program', 'Withdraw invoice affiliate program name', 'yith-woocommerce-affiliates' ), $site_url ) ),
				'{{affiliate_landing}}' => apply_filters( 'yith_wcaf_invoice_affiliate_landing', $affiliate_landing ),
				'{{withdraw_amount}}'   => wc_price( $amount, array( 'currency' => apply_filters( 'yith_wcaf_invoice_currency', $currency ) ) ),
				'{{company_section}}'   => nl2br( $invoice_company_section )
			);

			// process replacement value for each field
			foreach ( $invoice_fields as $field ) {
				$value = isset( $args[ $field ] ) ? $args[ $field ] : '';

				switch ( $field ) {
					case 'cif':
						$value = $value ? sprintf( _x( 'CIF/SSN: %s', 'Invoice template', 'yith-woocommerce-affiliates' ), $value ) : '';
					case 'first_name':
					case 'last_name':
						if ( isset( $args['type'] ) && 'personal' != $args['type'] ) {
							$value = '';
						}
						break;
					case 'vat':
						$value = $value ? sprintf( _x( 'VAT: %s', 'Invoice template', 'yith-woocommerce-affiliates' ), $value ) : '';
					case 'company':
						if ( isset( $args['type'] ) && 'business' != $args['type'] ) {
							$value = '';
						}
						break;
					case 'billing_state':
						if ( ! empty( $args['billing_country'] ) && ! empty( $value ) ) {
							$country_states = WC()->countries->get_states( $args['billing_country'] );

							if ( ! empty( $country_states ) ) {
								$value = isset( $country_states[ $value ] ) ? $country_states[ $value ] : $value;
								$value = ! empty( $value ) ? '(' . $value . ')' : '';
							}
						}
						break;
				}

				$value = apply_filters( 'yith_wcaf_invoice_replacement', $value, $field, $args );

				$replacements["{{{$field}}}"] = $value;
			}

			$replacements = array_map( 'wp_kses_post', $replacements );

			// retrieve invoice template
			ob_start();
			yith_wcaf_get_template( 'affiliate-invoice.php', $args, 'invoices' );
			$invoice_html_template = ob_get_clean();
			$invoice_html_template = str_replace( array_keys( $replacements ), array_values( $replacements ), $invoice_html_template );

			// retrieve invoice CSS
			ob_start();
			yith_wcaf_get_template( 'affiliate-invoice.css', array(), 'invoices' );
			$invoice_css = ob_get_clean();

			// generate pdf invoice
			if ( ! class_exists( 'Mpdf' ) ) {
				include_once( YITH_WCAF_DIR . 'vendor/autoload.php' );
			}

			$mpdf = new \Mpdf\Mpdf();
			$mpdf->WriteHTML( $invoice_css, 1 );
			$mpdf->WriteHTML( $invoice_html_template, 2 );
			$template = $mpdf->Output( 'document', 'S' );

			$this->save_generated_invoice( $template, $payment_id . '.pdf' );
		}

		/**
		 * Move temp file uploaded by the user to its final destination
		 *
		 * @param $uploaded_file string Temp file name
		 * @param $filename      string New file name
		 * @param $subfolder     string|bool Subfolder where file should be placed (referred to @see YITH_WCAF_INVOICES_DIR); false to save in main folder
		 *
		 * @return bool Operation status
		 * @since 1.3.0
		 */
		public function save_uploaded_invoice( $uploaded_file, $filename, $subfolder = false ) {
			$destination_path = YITH_WCAF_INVOICES_DIR;

			if ( $subfolder ) {
				$destination_path .= '/' . $subfolder;
			}

			if ( ! file_exists( $destination_path ) ) {
				wp_mkdir_p( $destination_path );
			}

			$save_file_path = sprintf( "%s/%s", $destination_path, $filename );

			return move_uploaded_file( $uploaded_file, $save_file_path );
		}

		/**
		 * Save brand new PDF file into invoices folder
		 *
		 * @param $template  string PDF raw template
		 * @param $filename  string New file name
		 * @param $subfolder string|bool Subfolder where file should be placed (referred to @see YITH_WCAF_INVOICES_DIR); false to save in main folder
		 *
		 * @return bool Operation status
		 * @since 1.3.0
		 */
		public function save_generated_invoice( $template, $filename, $subfolder = false ) {
			$destination_path = YITH_WCAF_INVOICES_DIR;

			if ( $subfolder ) {
				$destination_path .= '/' . $subfolder;
			}

			if ( ! file_exists( $destination_path ) ) {
				wp_mkdir_p( $destination_path );
			}

			$save_file_path = sprintf( "%s/%s", $destination_path, $filename );

			return file_put_contents( $save_file_path, $template );
		}

		/**
		 * Process withdraw request submitted by the user; prints errors, or register a new on-hold payment
		 *
		 * @return void
		 * @since 1.3.0
		 */
		public function process_withdraw_request() {
			if ( ! isset( $_POST['_withdraw_nonce'] ) || ! wp_verify_nonce( $_POST['_withdraw_nonce'], 'yith_wcaf_withdraw' ) ) {
				return;
			}

			if ( ! is_user_logged_in() ) {
				wc_add_notice( _x( 'You must be logged in, in order to request a withdraw', 'Withdraw error message', 'yith-woocommerce-affiliates' ), 'error' );

				return;
			}

			$user_id   = get_current_user_id();
			$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( $user_id );

			if ( ! $this->can_affiliate_withdraw( $affiliate['ID'] ) ) {
				wc_add_notice( _x( 'You already have a pending payment; please, wait until our team process your previous request, before submitting a new one', 'Withdraw error message', 'yith-woocommerce-affiliates' ), 'error' );

				return;
			}

			$from          = isset( $_POST['withdraw_from'] ) ? sanitize_text_field( $_POST['withdraw_from'] ) : false;
			$to            = isset( $_POST['withdraw_to'] ) ? sanitize_text_field( $_POST['withdraw_to'] ) : false;
			$payment_email = isset( $_POST['payment_email'] ) ? apply_filters( 'yith_wcaf_sanitized_payment_email', sanitize_email( $_POST['payment_email'] ), $_POST['payment_email'] ) : $affiliate['payment_email'];

			// check data submitted
			if ( empty( $from ) ) {
				wc_add_notice( _x( 'Please, enter a valid "From" date', 'Withdraw error message', 'yith-woocommerce-affiliates' ), 'error' );

				return;
			}

			if ( ! preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $from ) ) {
				wc_add_notice( _x( 'You entered a malformed date in "From" field; please double check value you entered and try again', 'Withdraw error message', 'yith-woocommerce-affiliates' ), 'error' );

				return;
			}

			if ( empty( $from ) ) {
				wc_add_notice( _x( 'Please, enter a valid "To" date', 'Withdraw error message', 'yith-woocommerce-affiliates' ), 'error' );

				return;
			}

			if ( ! preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $to ) ) {
				wc_add_notice( _x( 'You entered a malformed date in "To" field; please double check value you entered and try again', 'Withdraw error message', 'yith-woocommerce-affiliates' ), 'error' );

				return;
			}

			if ( $from > $to ) {
				wc_add_notice( _x( 'Please, make sure to select a valid interval of dates', 'Withdraw error message', 'yith-woocommerce-affiliates' ), 'error' );

				return;
			}

			if ( apply_filters( 'yith_wcaf_withdraw_valid_payment_email', true ) && ! $payment_email ) {
				wc_add_notice( _x( 'Please, enter a valid payment email where we can issue the payment', 'Withdraw error message', 'yith-woocommerce-affiliates' ), 'error' );

				return;
			}

			if ( 'upload' != get_option( 'yith_wcaf_payment_invoice_mode' ) && 'yes' == get_option( 'yith_wcaf_payment_invoice_show_terms_field' ) && ! isset( $_POST['terms'] ) ) {
				wc_add_notice( _x( 'Please, accept our Terms & Conditions', 'Withdraw error message', 'yith-woocommerce-affiliates' ), 'error' );

				return;
			}

			// max value admitted for to is today date
			$today = date( 'Y-m-d' );
			$to    = $to > $today ? $today : $to;

			// retrieve commissions for specified interval, and calculate payment total
			$formatted_from = date( 'Y-m-d 00:00:00', strtotime( $from ) );
			$formatted_to   = date( 'Y-m-d 23:59:59', strtotime( $to ) );

			$commissions = YITH_WCAF_Commission_Handler()->get_commissions( array(
				'status'       => 'pending',
				'interval'     => array(
					'start_date' => $formatted_from,
					'end_date'   => $formatted_to
				),
				'affiliate_id' => $affiliate['ID']
			) );

			if ( empty( $commissions ) ) {
				wc_add_notice( _x( 'There are no commissions for the specified interval; change dates and try again', 'Withdraw error message', 'yith-woocommerce-affiliates' ), 'error' );

				return;
			}

			$payment_amount = apply_filters( 'yith_wcaf_withdraw_amount', array_sum( array_map( 'floatval', wp_list_pluck( $commissions, 'amount' ) ) ), $from, $to, $affiliate['ID'] );

			// check if payment total matches requirements
			$min_withdraw = get_option( 'yith_wcaf_payment_threshold', 0 );
			$max_withdraw = YITH_WCAF_Affiliate_Handler()->get_affiliate_balance( $affiliate['ID'] );

			$min_withdraw = max( 0, floatval( $min_withdraw ) );

			if ( apply_filters( 'yith_wcaf_withdraw_amount_allow_exceeding_max', true ) && ( $payment_amount - $min_withdraw <= 0.01 || $payment_amount - $max_withdraw > 0.01 ) ){
				wc_add_notice( _x( 'Payment amount doesn\'t match requirements; please, select another interval and try again', 'Withdraw error message', 'yith-woocommerce-affiliates' ), 'error' );

				return;
			}

			// check data submitted for the invoice (file or fields)
			$require_invoice  = get_option( 'yith_wcaf_payment_require_invoice', 'yes' );
			$invoice_mode     = get_option( 'yith_wcaf_payment_invoice_mode', 'both' );
			$invoice_fields   = get_option( 'yith_wcaf_payment_invoice_fields', array() );
			$available_fields = $this->get_available_invoice_fields();
			$sanitized_fields = array();

			$invoice_processed = false;

			if ( $require_invoice == 'yes' ) {

				if ( in_array( $invoice_mode, array( 'upload', 'both' ) ) ) {
					// check if user selected invoice and store it
					$post_file = isset( $_FILES['invoice_file'] ) ? $_FILES['invoice_file'] : false;

					if ( $post_file && ! empty( $post_file['tmp_name'] ) ) {

						// check uploaded file to see if ti matches requirements
						if ( empty( $post_file['name'] ) ) {
							wc_add_notice( _x( 'There was an error with the invoice file you uploaded; please, try again', 'Withdraw error message', 'yith-woocommerce-affiliates' ), 'error' );

							return;
						}

						if ( ! empty( $post_file['error'] ) ) {
							wc_add_notice( sprintf( _x( 'There was an error with upload: %s', 'Withdraw error message', 'yith-woocommerce-affiliates' ), $post_file['error'] ), 'error' );

							return;
						}

						$file_name         = sanitize_file_name( strtolower( $post_file['name'] ) );
						$allowed_ext_array = apply_filters( 'yith_wcaf_invoice_upload_allowed_extensions', array( 'pdf' ) );
						$file_ext          = pathinfo( $file_name, PATHINFO_EXTENSION );

						if ( ! empty( $allowed_ext_array ) && ( ! in_array( $file_ext, $allowed_ext_array ) ) ) {
							wc_add_notice( _x( 'The invoice file you selected has an invalid extension; please, choose another file', 'Withdraw error message', 'yith-woocommerce-affiliates' ), 'error' );

							return;
						}

						$max_size_byte = 1048576 * apply_filters( 'yith_wcaf_max_invoice_size', 3 );

						if ( $max_size_byte && $post_file['size'] > $max_size_byte ) {
							wc_add_notice( _x( 'The invoice file you selected is too big; please, choose another file', 'Withdraw error message', 'yith-woocommerce-affiliates' ), 'error' );

							return;
						}

						$invoice_processed = 'upload';
					}
				}

				if ( ! $invoice_processed && in_array( $invoice_mode, array(
						'generate',
						'both'
					) ) && ! empty( $invoice_fields ) ) {
					// generate invoice using fields submitted
					foreach ( $invoice_fields as $field ) {
						if ( empty( $_POST[ $field ] ) ) {
							// exception for countries that do not require states
							$get_states = WC()->countries->get_states( $_POST['billing_country'] );
							if ( $field == 'billing_state' && ! empty( $_POST['billing_country'] ) && empty( $get_states ) ) {
								continue;
							}

							// business vs personal required information
							if ( in_array( 'type', $invoice_fields ) && isset( $_POST['type'] ) ) {
								$type = in_array( $_POST['type'], array(
									'business',
									'personal'
								) ) ? $_POST['type'] : 'personal';

								if ( 'personal' == $type && in_array( $field, array( 'company', 'vat' ) ) ) {
									continue;
								} elseif ( 'business' == $type && in_array( $field, array(
										'first_name',
										'last_name',
										'cif'
									) ) ) {
									continue;
								}
							}

							wc_add_notice( _x( 'Please, enter all required data in order to automatically generate invoice', 'Withdraw error message', 'yith-woocommerce-affiliates' ), 'error' );

							return;
						} elseif ( ! $sanitized_value = $this->sanitize_invoice_field( $field, $_POST[ $field ], $_POST ) ) {
							wc_add_notice( sprintf( _x( 'Please, make sure to enter a valid value for %s field', 'Withdraw error message', 'yith-woocommerce-affiliates' ), $available_fields[ $field ] ), 'error' );

							return;
						}

						$sanitized_fields[ $field ] = $sanitized_value;
					}

					// save invoice information, for future use
					YITH_WCAF_Affiliate_Handler_Premium()->save_affiliate_invoice_profile( $affiliate['user_id'], $sanitized_fields );

					$invoice_processed = 'generate';
				}

				if ( ! $invoice_processed ) {
					wc_add_notice( _x( 'You need to enter a valid invoice in order to submit your withdraw request', 'Withdraw error message', 'yith-woocommerce-affiliates' ), 'error' );

					return;
				}
			}

			// everything's gold! Let's proceed with payment creation
			YITH_WCAF_Affiliate_Handler()->update( $affiliate['ID'], array( 'payment_email' => $payment_email ) );
			$this->register_payment( wp_list_pluck( $commissions, 'ID' ), false );

			$payment_id = $this->last_id();

			// now that we have payment ID, we can process invoice (if required)
			if ( 'upload' == $invoice_processed ) {

				// move tmp file to upload dir
				$this->save_uploaded_invoice( $post_file['tmp_name'], $payment_id . '.' . $file_ext );

			} elseif ( 'generate' == $invoice_processed ) {

				// generate a new pdf invoice and store it
				$this->generate_invoice( $payment_id, $payment_amount, $formatted_from, $formatted_to, $sanitized_fields );

			}
			do_action( 'yith_wcaf_after_process_withdraw_request', $payment_id, $affiliate );

			// let the user know payment has been registered
			wc_add_notice( apply_filters( 'yith_wcaf_withdraw_created_message', _x( 'Your withdraw was correctly registered. An admin will review it as soon as possible', 'Withdraw success message', 'yith-woocommerce-affiliates' ) ) );
		}

		/**
		 * Sanitize invoice fields
		 * Returns sanitized value when possible, false when field value doesn't match requirements
		 *
		 * @param $field       string Field name
		 * @param $value       string Field value
		 * @param $posted_data mixed Array of data submitted with the POST
		 *
		 * @return bool|string Sanitized value or false on failure
		 */
		protected function sanitize_invoice_field( $field, $value, $posted_data ) {
			switch ( $field ) {
				case 'billing_country':
					$countries = WC()->countries->get_countries();

					if ( ! in_array( strtoupper( $value ), array_map( 'strtoupper', array_keys( $countries ) ) ) ) {
						return false;
					}
					break;
				case 'billing_state':
					if ( ! isset( $posted_data['billing_country'] ) ) {
						return false;
					}

					$country      = $posted_data['billing_country'];
					$valid_states = WC()->countries->get_states( $country );

					if ( empty( $valid_states ) ) {
						$value = sanitize_text_field( $value );
					} elseif ( ! in_array( strtoupper( $value ), array_map( 'strtoupper', array_keys( $valid_states ) ) ) ) {
						return false;
					}
					break;
				case 'billing_postcode':
					if ( ! isset( $posted_data['billing_country'] ) ) {
						return false;
					}

					$country = $posted_data['billing_country'];
					$value   = wc_format_postcode( $value, $country );

					if ( ! WC_Validation::is_postcode( $value, $country ) ) {
						return false;
					}
					break;
				default:
					$value = sanitize_text_field( $value );
					break;
			}

			return apply_filters( 'yith_wcaf_sanitized_invoice_field', $value, $field, $posted_data );
		}

		/* === SCHEDULED ACTIONS METHODS === */

		/**
		 * Setup scheduled events for affiliates payments
		 *
		 * From 1.2.2 it schedules a single daily event (pay_commissions_action_schedule), that handles all kind of automatic payments
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function pay_affiliates_setup_schedule() {
			if ( in_array( $this->_payment_type, array(
					'automatically_on_date',
					'automatically_on_both',
					'automatically_every_day'
				) ) && ! wp_next_scheduled( 'pay_commissions_action_schedule' ) ) {
				wp_schedule_event( time(), 'daily', 'pay_commissions_action_schedule' );
			}
		}

		/**
		 * Delete schedule when date option is changed
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function pay_commissions_delete_schedule() {
			wp_clear_scheduled_hook( 'pay_commissions_action_schedule' );
		}

		/**
		 * When a commission switch to pending, check if threshold reached, and eventually pay affiliate commissions
		 *
		 * @param $commission_id int Commission id of the last commission
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function pay_on_threshold_reached( $commission_id ) {
			if ( $this->_payment_type != 'automatically_on_threshold' || empty( $this->_payment_threshold ) || empty( $this->_payment_default_gateway ) ) {
				return;
			}

			$pay     = $this->_payment_default_gateway != 'none';
			$gateway = $this->_payment_default_gateway != 'none' ? $this->_payment_default_gateway : false;

			$commission = YITH_WCAF_Commission_Handler()->get_commission( $commission_id );

			if ( ! $commission ) {
				return;
			}

			$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $commission['affiliate_id'] );

			if ( ! $affiliate ) {
				return;
			}

			if ( $affiliate['balance'] > $this->_payment_threshold ) {
				$this->pay_all_affiliate_commissions( $affiliate['ID'], $pay, $gateway, true );
			}
		}

		/**
		 * Pay all pending commission for all affiliates (should be executed once a month; if also threshold is enabled, check it before pay affilaite)
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function pay_on_date_reached() {
			if ( ( $this->_payment_type != 'automatically_on_date' && $this->_payment_type != 'automatically_on_both' ) || empty( $this->_payment_date ) || empty( $this->_payment_default_gateway ) ) {
				return;
			}

			$pay     = $this->_payment_default_gateway != 'none';
			$gateway = $this->_payment_default_gateway != 'none' ? $this->_payment_default_gateway : false;

			$affiliates = YITH_WCAF_Affiliate_Handler()->get_affiliates();

			if ( ! $affiliates ) {
				return;
			}

			foreach ( $affiliates as $affiliate ) {
				if ( $this->_payment_type == 'automatically_on_both' && ( empty( $this->_payment_threshold ) || $affiliate['earnings'] <= $this->_payment_threshold ) ) {
					continue;
				}

				$this->pay_all_affiliate_commissions( $affiliate['ID'], $pay, $gateway, true );
			}
		}

		/**
		 * Execute necessary operations for affiliate payment, during scheduled action
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function pay_commissions() {
			// check whether there is a default gateway
			if ( empty( $this->_payment_default_gateway ) ) {
				return;
			}

			// check whether _payment_type is a valid schedule
			if ( ! in_array( $this->_payment_type, array(
				'automatically_on_date',
				'automatically_on_both',
				'automatically_every_day'
			) ) ) {
				return;
			}

			// check whether user supplied a valid _payment_date, if _payment_type occurs on a specific date
			if ( ( $this->_payment_type == 'automatically_on_date' || $this->_payment_type == 'automatically_on_both' ) && empty( $this->_payment_date ) ) {
				return;
			}

			// check whether we're in the correct _payment_date, if _payment_type occurs on a specific date
			if ( ( $this->_payment_type == 'automatically_on_date' || $this->_payment_type == 'automatically_on_both' ) ) {
				$current_day = date( 'j' );

				if ( $current_day != $this->_payment_date ) {
					return;
				}
			}

			$pay     = $this->_payment_default_gateway != 'none';
			$gateway = $this->_payment_default_gateway != 'none' ? $this->_payment_default_gateway : false;

			$affiliates = YITH_WCAF_Affiliate_Handler()->get_affiliates();

			if ( ! $affiliates ) {
				return;
			}

			foreach ( $affiliates as $affiliate ) {
				if ( $this->_payment_type == 'automatically_on_both' && ( empty( $this->_payment_threshold ) || $affiliate['earnings'] <= $this->_payment_threshold ) ) {
					continue;
				}

				$this->pay_all_affiliate_commissions( $affiliate['ID'], $pay, $gateway, true );
			}
		}

		/* === PANEL PAYMENTS METHODS === */

		/**
		 * Print payment panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_payment_panel() {
			// define variables to use in template
			$payment_id = isset( $_REQUEST['payment_id'] ) ? $_REQUEST['payment_id'] : false;

			if ( ! empty( $payment_id ) && $payment = $this->get_payment( $payment_id ) ) {

				// affiliate
				$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $payment['affiliate_id'] );

				if ( ! $affiliate ) {
					return;
				}

				// update payment email, if required
				if ( isset( $_POST['update_payment_email'] ) && isset( $_POST['_payment_email'] ) && is_email( $_POST['_payment_email'] ) ) {
					$payment_email = trim( $_POST['_payment_email'] );

					$this->update( $payment_id, array( 'payment_email' => $payment_email ) );
					$payment['payment_email'] = $payment_email;
				}

				// regenerate payment invoice, if required
				if ( isset( $_POST['regenerate_invoice'] ) && isset( $_POST['from'] ) && isset( $_POST['to'] ) ) {
					$formatted_from = date( 'Y-m-d 00:00:00', strtotime( $_POST['from'] ) );
					$formatted_to   = date( 'Y-m-d 23:59:59', strtotime( $_POST['to'] ) );

					if ( $formatted_from && $formatted_to ) {
						$defaults = YITH_WCAF_Affiliate_Handler_Premium()->get_affiliate_invoice_profile( $payment['user_id'] );
						$args     = wp_parse_args( array_map( 'sanitize_text_field', array_filter( $_POST, 'is_string' ) ), $defaults );

						$this->generate_invoice( $payment_id, $payment['amount'], $formatted_from, $formatted_to, $args );
						YITH_WCAF_Affiliate_Handler_Premium()->save_affiliate_invoice_profile( $affiliate['user_id'], $args );
					}
				}

				// retrieve affiliate user
				$user_info = get_userdata( $payment['user_id'] );
				$user      = get_user_by( 'id', $payment['user_id'] );

				$user_name = '';
				if ( $user_info->first_name || $user_info->last_name ) {
					$user_name .= esc_html( ucfirst( $user_info->first_name ) . ' ' . ucfirst( $user_info->last_name ) );
				} else {
					$user_name .= esc_html( ucfirst( $user_info->display_name ) );
				}

				$user_email = $user_info->user_email;

				// retrieve gateway specifications and commissions
				$payment_label     = $this->get_readable_gateway( $payment['gateway'] );
				$payment_trans_key = isset( $payment['transaction_key'] ) ? $payment['transaction_key'] : __( 'N/A', 'yith-woocommerce-affiliates' );
				$commissions       = $this->get_payment_commissions( $payment_id );

				// retrieve notes
				$payment_notes = $this->get_payment_notes( $payment_id );

				// retrieve available payment actions
				$available_payment_actions = array();
				$gateways                  = $this->get_available_gateways();

				if ( $payment['status'] == 'on-hold' && ! empty( $gateways ) ) {
					foreach ( $gateways as $id => $gateway ) {
						$available_payment_actions[ 'pay_via_' . $id ] = sprintf( __( 'Pay via %s', 'yith-woocommerce-affiliates' ), $gateway['label'] );
					}
				}

				// retrieve invoice (if any)
				if ( file_exists( YITH_WCAF_INVOICES_DIR . $payment_id . '.pdf' ) ) {
					$invoice = $payment_id . '.pdf';
				}

				// retrieve invoice profile
				$invoice_fields            = YITH_WCAF_Payment_Handler_Premium()->get_available_invoice_fields();
				$invoice_profile           = YITH_WCAF_Affiliate_Handler_Premium()->get_affiliate_invoice_profile( $user->ID );
				$formatted_invoice_profile = YITH_WCAF_Affiliate_Handler_Premium()->get_formatted_affiliate_invoice_profile( $user->ID );

				// require rate panel template
				include( YITH_WCAF_DIR . 'templates/admin/payment-panel-detail.php' );
			} else {
				// prepare user rates table items
				$payments_table = new YITH_WCAF_Payments_Table_Premium();
				$payments_table->prepare_items();

				// require rate panel template
				include( YITH_WCAF_DIR . 'templates/admin/payment-panel-table.php' );
			}
		}

		/**
		 * Process bulk action for current view
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function process_bulk_actions() {
			if ( ! empty( $_REQUEST['payments'] ) ) {
				$current_action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
				$current_action = ( empty( $current_action ) && isset( $_REQUEST['action2'] ) ) ? $_REQUEST['action2'] : $current_action;
				$redirect       = esc_url_raw( remove_query_arg( array( 'action', 'action2', 'payments' ) ) );

				if ( empty( $current_action ) ) {
					return;
				}

				switch ( $current_action ) {
					case 'switch-to-completed':
					case 'switch-to-on-hold':
					case 'switch-to-cancelled':
					case 'delete':
						parent::process_bulk_actions();
						break;
					default:
						// handles payment actions
						$matches = array();

						if ( preg_match( '^pay_via_([a-zA-Z_-]*)$^', $current_action, $matches ) ) {
							// group payments by gateway
							$to_pay  = array();
							$gateway = $matches[1];

							if ( in_array( $gateway, array_keys( $this->_available_gateways ) ) ) {
								foreach ( $_REQUEST['payments'] as $payment_id ) {
									$payment = $this->get_payment( $payment_id );

									if ( ! $payment || $payment['status'] != 'on-hold' ) {
										continue;
									}

									if ( ! isset( $to_pay[ $gateway ] ) ) {
										$to_pay[ $gateway ] = array();
									}

									$to_pay[ $gateway ][] = $payment_id;
								}
							}

							if ( ! empty( $to_pay ) ) {
								foreach ( $to_pay as $gateway => $payment_instances ) {
									$payments = implode( '_', $payment_instances );
									$res      = $this->pay( $gateway, $payment_instances );

									if ( ! $res['status'] ) {
										$errors   = is_array( $res['messages'] ) ? implode( ',', $res['messages'] ) : $res['messages'];
										$redirect = add_query_arg( array(
											'payment_failed'            => true,
											"payment_error_{$payments}" => urlencode( $errors )
										), $redirect );
									} else {
										$redirect = add_query_arg( array(
											'payment_success'             => true,
											"payment_success_{$payments}" => true
										), $redirect );
									}
								}
							}
						}
						break;
				}

				if ( isset( $_GET['payment_id'] ) ) {
					$redirect = add_query_arg( 'payment_id', intval( $_GET['payment_id'] ), $redirect );
				}

				wp_redirect( esc_url_raw( $redirect ) );
				die();
			}
		}

		/**
		 * Process action to complete payments on payment panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function handle_payments_panel_actions() {
			$payment_id = isset( $_REQUEST['payment_id'] ) ? $_REQUEST['payment_id'] : 0;
			$gateway_id = isset( $_REQUEST['gateway'] ) ? $_REQUEST['gateway'] : '';
			$redirect   = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( $_REQUEST['redirect_to'] ) : esc_url_raw( add_query_arg( array(
				'page' => 'yith_wcaf_panel',
				'tab'  => 'payments'
			), admin_url( 'admin.php' ) ) );

			if ( ! $payment_id || ! $gateway_id || ! in_array( $gateway_id, array_keys( $this->_available_gateways ) ) ) {
				wp_redirect( $redirect );
				die();
			}

			$payment = $this->get_payment( $payment_id );

			if ( ! $payment ) {
				wp_redirect( $redirect );
				die();
			}

			$res = $this->pay( $gateway_id, $payment_id );

			if ( ! $res['status'] ) {
				$errors   = is_array( $res['messages'] ) ? implode( ',', $res['messages'] ) : $res['messages'];
				$redirect = esc_url_raw( add_query_arg( array(
					'payment_failed'              => true,
					"payment_error_{$payment_id}" => urlencode( $errors )
				), $redirect ) );
			} else {
				$redirect = esc_url_raw( add_query_arg( array(
					'payment_success'               => true,
					"payment_success_{$payment_id}" => true
				), $redirect ) );
			}

			wp_redirect( $redirect );
			die();
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAF_Payment_Handler_Premium
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}
	}
}

/**
 * Unique access to instance of YITH_WCAF_Payment_Handler_Premium class
 *
 * @return \YITH_WCAF_Payment_Handler_Premium
 * @since 1.0.0
 */
function YITH_WCAF_Payment_Handler_Premium() {
	return YITH_WCAF_Payment_Handler_Premium::get_instance();
}