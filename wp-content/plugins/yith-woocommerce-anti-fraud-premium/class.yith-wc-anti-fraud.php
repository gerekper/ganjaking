<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Main class
 *
 * @class   YITH_WC_Anti_Fraud
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */

if ( ! class_exists( 'YITH_WC_Anti_Fraud' ) ) {

	class YITH_WC_Anti_Fraud {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Anti_Fraud
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Panel object
		 *
		 * @var     /Yit_Plugin_Panel object
		 * @since   1.0.0
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $_panel = null;

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-anti-fraud/';

		/**
		 * @var string Plugin official documentation
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-anti-fraud/';

		/**
		 * @var string YITH WooCommerce Anti-Fraud panel page
		 */
		protected $_panel_page = 'yith-wc-anti-fraud';

		/**
		 * @var array Active Anti-Fraud Rules
		 */
		protected $rules = array();

		/**
		 * @var array Active risk thresholds
		 */
		protected $risk_thresholds = array();

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Anti_Fraud
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self;

			}

			return self::$instance;

		}

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			if ( ! function_exists( 'WC' ) ) {
				return;
			}

			//Load plugin framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 12 );
			add_filter( 'plugin_action_links_' . plugin_basename( YWAF_DIR . '/' . basename( YWAF_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 5 );
			add_filter( 'yith_plugin_fw_get_field_template_path', array( $this, 'add_custom_fields' ), 10, 2 );

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
			add_action( 'plugins_loaded', array( $this, 'include_privacy_text' ), 20 );

			if ( get_option( 'ywaf_enable_plugin' ) == 'yes' ) {

				$this->includes();

				$this->rules           = $this->get_ywaf_rules();
				$this->risk_thresholds = $this->get_ywaf_thresholds();

				add_action( 'woocommerce_order_status_changed', array( $this, 'start_order_check' ), 99, 3 );
				add_action( 'woocommerce_checkout_order_processed', array( $this, 'check_status_on_checkout' ) );

				add_action( 'ywaf_after_fraud_check', array( $this, 'add_email_to_blacklist' ), 10, 2 );
				add_action( 'ywaf_after_fraud_check', array( $this, 'add_address_to_blacklist' ), 10, 2 );
				add_action( 'ywaf_after_fraud_check', array( $this, 'send_admin_mail' ), 10, 2 );
				add_action( 'ywaf_paypal_cron', array( $this, 'paypal_cron' ) );
				add_action( 'ywaf_paypal_data_cron', array( $this, 'paypal_data_cron' ) );
				add_filter( 'ywaf_after_check_status', array( $this, 'set_order_status' ), 10, 3 );
				add_filter( 'ywaf_paypal_check', array( $this, 'check_paypal' ), 10, 2 );
				add_filter( 'ywaf_check_blacklist', array( $this, 'check_blacklist' ), 10, 2 );
				add_filter( 'yith_wcet_email_template_types', array( $this, 'add_yith_wcet_template' ) );
				add_filter( 'woocommerce_email_classes', array( $this, 'paypal_mail' ) );
				add_action( 'valid-paypal-standard-ipn-request', array( $this, 'get_paypal_payer_address' ), 15 );
				add_filter( 'woocommerce_payment_complete_order_status', array( $this, 'set_paypal_order_onhold' ), 10, 3 );

				if ( is_admin() ) {

					add_action( 'admin_notices', array( $this, 'admin_notices' ) );
					add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_ywaf_column' ), 11 );
					add_action( 'manage_shop_order_posts_custom_column', array( $this, 'render_ywaf_column' ), 3 );
					add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_admin' ) );
					add_filter( 'woocommerce_admin_order_actions', array( $this, 'table_action' ), 10, 2 );
					add_filter( 'ywaf_paypal_status', array( $this, 'paypal_status' ), 10, 1 );
					add_action( 'woocommerce_admin_settings_sanitize_option_ywaf_rules_risk_country_list', array( $this, 'sanitize_empty_array' ) );
					add_action( 'woocommerce_admin_settings_sanitize_option_ywaf_address_blacklist_list', array( $this, 'sanitize_address_field' ) );

				} else {

					add_action( 'woocommerce_before_my_account', array( $this, 'verify_paypal_code' ) );
					add_action( 'woocommerce_before_customer_login_form', array( $this, 'verify_paypal_code' ) );
					add_action( 'woocommerce_view_order', array( $this, 'view_order' ), 5, 1 );
					add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_frontend' ) );
					add_action( 'woocommerce_download_product', array( $this, 'prevent_download' ), 10, 6 );

				}

			}

		}

		/**
		 * Files inclusion
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		private function includes() {

			if ( is_admin() ) {
				include_once( 'includes/class-ywaf-metabox.php' );
			}

			include_once( 'includes/functions.ywaf-gdpr.php' );
			include_once( 'includes/class-ywaf-ajax.php' );
			include_once( 'includes/class-ywaf-rules.php' );
			include_once( 'includes/rules/class-ywaf-first-order.php' );
			include_once( 'includes/rules/class-ywaf-international-order.php' );
			include_once( 'includes/rules/class-ywaf-ip-country.php' );
			include_once( 'includes/rules/class-ywaf-addresses-matching.php' );
			include_once( 'includes/rules/class-ywaf-proxy.php' );
			include_once( 'includes/rules/class-ywaf-suspicious-email.php' );
			include_once( 'includes/rules/class-ywaf-risk-country.php' );
			include_once( 'includes/rules/class-ywaf-high-amount.php' );
			include_once( 'includes/rules/class-ywaf-high-amount-fixed.php' );
			include_once( 'includes/rules/class-ywaf-many-attempts.php' );
			include_once( 'includes/rules/class-ywaf-ip-multiple-details.php' );
			include_once( 'includes/rules/class-ywaf-blacklist.php' );
			include_once( 'includes/rules/class-ywaf-address-blacklist.php' );
			include_once( 'includes/rules/class-ywaf-paypal.php' );

			do_action( 'ywaf_custom_includes' );

		}

		/**
		 * Initialize custom fields
		 *
		 * @since   1.2.2
		 *
		 * @param   $path  string
		 * @param   $field array
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function add_custom_fields( $path, $field ) {

			if ( $field['type'] == 'ywaf-custom-checklist' ) {
				$path = YWAF_TEMPLATE_PATH . '/admin/ywaf-custom-checklist.php';
			}

			if ( $field['type'] == 'ywaf-address-list' ) {
				$path = YWAF_TEMPLATE_PATH . '/admin/ywaf-address-list.php';
			}

			return $path;

		}

		/**
		 * Get Anti-Fraud rules
		 *
		 * @since   1.0.0
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function get_ywaf_rules() {

			$rules = array(
				'YWAF_First_Order'         => array(
					'rule'   => new YWAF_First_Order(),
					'active' => get_option( 'ywaf_rules_first_order_enable' ),
				),
				'YWAF_International_Order' => array(
					'rule'   => new YWAF_International_Order(),
					'active' => get_option( 'ywaf_rules_international_order_enable' ),
				),
				'YWAF_IP_Country'          => array(
					'rule'   => new YWAF_IP_Country(),
					'active' => get_option( 'ywaf_rules_ip_country_enable' ),
				),
				'YWAF_Addresses_Matching'  => array(
					'rule'   => new YWAF_Addresses_Matching(),
					'active' => get_option( 'ywaf_rules_addresses_matching_enable' ),
				),
				'YWAF_Proxy'               => array(
					'rule'   => new YWAF_Proxy(),
					'active' => get_option( 'ywaf_rules_proxy_enable' ),
				),
				'YWAF_Risk_Country'        => array(
					'rule'   => new YWAF_Risk_Country(),
					'active' => get_option( 'ywaf_rules_risk_country_enable' ),
				),
				'YWAF_High_Amount'         => array(
					'rule'   => new YWAF_High_Amount(),
					'active' => get_option( 'ywaf_rules_high_amount_enable' ),
				),
				'YWAF_High_Amount_Fixed'   => array(
					'rule'   => new YWAF_High_Amount_Fixed(),
					'active' => get_option( 'ywaf_rules_high_amount_fixed_enable' ),
				),
				'YWAF_Many_Attempts'       => array(
					'rule'   => new YWAF_Many_Attempts(),
					'active' => get_option( 'ywaf_rules_many_attempts_enable' ),
				),
				'YWAF_IP_Multiple_Details' => array(
					'rule'   => new YWAF_IP_Multiple_Details(),
					'active' => get_option( 'ywaf_rules_ip_multiple_details_enable' ),
				),
				'YWAF_Suspicious_Email'    => array(
					'rule'   => new YWAF_Suspicious_Email(),
					'active' => get_option( 'ywaf_rules_suspicious_email_enable' ),
				),
			);

			return apply_filters( 'ywaf_rules', $rules );

		}

		/**
		 * Get Anti-Fraud thresholds
		 *
		 * @since   1.0.0
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function get_ywaf_thresholds() {

			$thresholds = array(
				'medium' => get_option( 'ywaf_medium_risk_threshold', 25 ),
				'high'   => get_option( 'ywaf_high_risk_threshold', 75 ),
			);

			return $thresholds;

		}

		/**
		 * On change order status perform a fraud check
		 *
		 * @since   1.0.0
		 *
		 * @param   $id         integer
		 * @param   $old_status string
		 * @param   $new_status string
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function start_order_check( $id, $old_status, $new_status ) {

			$allowed_statuses = apply_filters( 'ywaf_allowed_statuses_check', array( 'completed', 'processing', 'on-hold' ) );

			if ( in_array( $new_status, $allowed_statuses ) ) {

				$this->set_fraud_check( $id );

			}

		}

		/**
		 * Set fraud check
		 *
		 * @since   1.0.0
		 *
		 * @param   $order_id integer
		 * @param   $repeat   boolean
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function set_fraud_check( $order_id, $repeat = false ) {

			$parent_order = wp_get_post_parent_id( $order_id );

			if ( $parent_order ) {
				return;
			}

			$order      = wc_get_order( $order_id );
			$is_deposit = ( $order->get_created_via() == 'yith_wcdp_balance_order' );

			if ( $is_deposit || apply_filters( 'ywaf_can_skip_check_order', false, $order ) ) {
				return;
			}

			if ( $repeat ) {
				$order->delete_meta_data( 'ywaf_risk_factor' );
			}

			$can_check = apply_filters( 'ywaf_paypal_check', true, $order );

			if ( $can_check ) {

				$risk_factor = $order->get_meta( 'ywaf_risk_factor' );

				if ( $risk_factor && ! $repeat ) {
					return;
				}

				$order->add_order_note( __( 'Fraud risk check in progress.', 'yith-woocommerce-anti-fraud' ) );
				$order->update_meta_data( 'ywaf_check_status', 'process' );
				$order->save();

				$this->process_fraud_check( $order );

			}

		}

		/**
		 * Process fraud check
		 *
		 * @since   1.0.0
		 *
		 * @param   $order WC_Order
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function process_fraud_check( $order = null ) {

			if ( $order == null ) {
				return;
			}

			$risk         = $this->calculate_risk_score( $order );
			$risk_score   = $risk['risk_score'];
			$failed_rules = $risk['failed_rules'];

			$order_status = apply_filters( 'ywaf_after_check_status', array(
				'status' => $order->get_status(),
				'note'   => __( 'Fraud risk check completed.', 'yith-woocommerce-anti-fraud' ),
			), $risk_score, $order );
			$risk_level   = $this->get_risk_level( ( $risk_score > 100 ) ? 100 : $risk_score );
			$check_status = ( $risk_level['class'] == 'high' || $risk_level['class'] == 'medium' ) ? $risk_level['class'] . '_risk' : 'success';

			$order->add_order_note( $order_status['note'] );
			$order->set_status( $order_status['status'] );
			$order->update_meta_data( 'ywaf_risk_factor', array(
				'score'        => ( $risk_score > 100 ) ? 100 : $risk_score,
				'failed_rules' => $failed_rules
			) );
			$order->update_meta_data( 'ywaf_check_status', $check_status );
			$order->save();

			do_action( 'ywaf_after_fraud_check', $order, $risk_score );

		}

		/**
		 * Calculate risk score for the order
		 *
		 * @since   1.1.2
		 *
		 * @param   $order WC_Order
		 *
		 * @return  array Map with risk_score and failed_rules params
		 * @author  Alberto Ruggiero
		 */
		public function calculate_risk_score( $order ) {

			if ( $order == null ) {
				return array( 'risk_score' => 0, 'failed_rules' => array() );
			}

			$blacklisted_email = $this->check_blacklist( $order );
			if ( $blacklisted_email ) {
				return array( 'risk_score' => 100, 'failed_rules' => array( 'YWAF_Blacklist' ) );
			}

			$blacklisted_address = $this->check_address_blacklist( $order );
			if ( $blacklisted_address ) {
				return array( 'risk_score' => 100, 'failed_rules' => array( 'YWAF_Address_Blacklist' ) );
			}

			$total_risk_points = 0;
			$max_risk_points   = 0;
			$failed_rules      = array();

			foreach ( $this->rules as $key => $rule ) {

				if ( $rule['active'] == 'yes' ) {

					if ( $rule['rule']->get_fraud_risk( $order ) === true ) {
						$total_risk_points += $rule['rule']->get_points();
						$failed_rules[]    = $key;
					}

					$max_risk_points += 10;

				}

			}

			$risk_score = round( ( $total_risk_points / $max_risk_points ) * 100, 1 );

			return array( 'risk_score' => $risk_score, 'failed_rules' => $failed_rules );

		}

		/**
		 * Process fraud check at checkout
		 *
		 * @since   1.0.0
		 *
		 * @param   $order_id integer
		 *
		 * @throws  Exception
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function check_status_on_checkout( $order_id ) {

			if ( get_option( 'ywaf_check_high_risk_checkout' ) != 'yes' ) {
				return;
			}

			$order = wc_get_order( $order_id );
			$risk  = $this->calculate_risk_score( $order );

			$risk_score   = $risk['risk_score'];
			$failed_rules = $risk['failed_rules'];

			if ( $risk_score >= get_option( 'ywaf_high_risk_threshold', 75 ) ) {

				$risk_level   = $this->get_risk_level( ( $risk_score > 100 ) ? 100 : $risk_score );
				$check_status = ( $risk_level['class'] == 'high' || $risk_level['class'] == 'medium' ) ? $risk_level['class'] . '_risk' : 'success';

				$order->add_order_note( __( 'Fraud risk check not passed. Fraud risk is high!', 'yith-woocommerce-anti-fraud' ) );
				$order->set_status( 'cancelled' );
				$order->update_meta_data( 'ywaf_risk_factor', array(
					'score'        => ( $risk_score > 100 ) ? 100 : $risk_score,
					'failed_rules' => $failed_rules
				) );
				$order->update_meta_data( 'ywaf_check_status', $check_status );
				$order->save();

				throw new Exception( get_option( 'ywaf_checkout_error_message' ) );

			}

		}


		/**
		 * Set risk level
		 *
		 * @since   1.0.0
		 *
		 * @param   $risk_points integer
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function get_risk_level( $risk_points ) {

			$data = array(
				'class' => '',
				'tip'   => ''
			);

			if ( $risk_points === '' ) {

				$data['class'] = '';
				$data['color'] = '#cacaca';
				$data['tip']   = __( 'No check performed', 'yith-woocommerce-anti-fraud' );

			} else {

				switch ( true ) {

					case $risk_points >= $this->risk_thresholds['high']:

						$data['class'] = 'high';
						$data['color'] = '#c83c3d';
						$data['tip']   = __( 'High Risk', 'yith-woocommerce-anti-fraud' );

						break;

					case $risk_points >= $this->risk_thresholds['medium'] && $risk_points < $this->risk_thresholds['high']:

						$data['class'] = 'medium';
						$data['color'] = '#ffa200';
						$data['tip']   = __( 'Medium Risk', 'yith-woocommerce-anti-fraud' );

						break;

					default:

						$data['class'] = 'low';
						$data['color'] = '#00a208';
						$data['tip']   = __( 'Low Risk', 'yith-woocommerce-anti-fraud' );

				}

			}

			return $data;

		}

		/**
		 * Set custom order status
		 *
		 * @since   1.0.0
		 *
		 * @param   $order_status array
		 * @param   $risk_score   integer
		 * @param   $order        WC_Order
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 *
		 */
		public function set_order_status( $order_status, $risk_score, $order ) {

			switch ( true ) {

				case  $risk_score >= $this->risk_thresholds['high']:

					$order_status['status'] = 'cancelled';
					$order_status['note']   = __( 'Fraud risk check not passed. Fraud risk is high!', 'yith-woocommerce-anti-fraud' );
					break;

				case $risk_score >= $this->risk_thresholds['medium'] && $risk_score < $this->risk_thresholds['high']:

					$order_status['status'] = 'on-hold';
					$order_status['note']   = __( 'Fraud risk check passed with medium fraud risk.', 'yith-woocommerce-anti-fraud' );
					break;

				default:

					$paypal_methods       = array( 'paypal', 'paypal_express', 'yith-paypal-ec', 'ppec_paypal' );
					$order_payment_method = $order->get_payment_method();

					if ( ( get_option( 'ywaf_paypal_enable' ) == 'yes' ) && in_array( $order_payment_method, $paypal_methods ) ) {

						$order_status['status'] = $order->needs_processing() ? 'processing' : 'completed';

					}

					$order_status['note'] = __( 'Fraud risk check passed with low fraud risk', 'yith-woocommerce-anti-fraud' );
					break;

			}

			return $order_status;

		}

		/**
		 * Check if billing email is in blacklist
		 *
		 * @since   1.0.0
		 *
		 * @param   $order  WC_Order
		 *
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function check_blacklist( $order ) {

			$result = false;

			if ( get_option( 'ywaf_email_blacklist_enable' ) == 'yes' ) {

				$blacklist = new YWAF_Blacklist();

				if ( $blacklist->get_fraud_risk( $order ) === true ) {

					$result = true;

				}

			}

			return $result;

		}

		/**
		 * Check if billing or shipping address is in blacklist
		 *
		 * @since   1.0.0
		 *
		 * @param   $order  WC_Order
		 *
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function check_address_blacklist( $order ) {

			$result = false;

			if ( get_option( 'ywaf_address_blacklist_enable' ) == 'yes' ) {

				$blacklist = new YWAF_Address_Blacklist();

				if ( $blacklist->get_fraud_risk( $order ) === true ) {

					$result = true;

				}

			}

			return $result;

		}

		/**
		 * Add billing email in blacklist
		 *
		 * @since   1.0.0
		 *
		 * @param   $order       WC_Order
		 * @param   $risk_score  integer
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_email_to_blacklist( $order, $risk_score ) {

			if ( get_option( 'ywaf_email_blacklist_enable' ) == 'yes' && get_option( 'ywaf_email_blacklist_auto_add' ) == 'yes' ) {

				$blacklist = new YWAF_Blacklist();

				if ( $risk_score >= $this->risk_thresholds['high'] && ! $blacklist->get_fraud_risk( $order ) ) {

					$blacklist->add_to_blacklist( $order );

				}

			}

		}

		/**
		 * Add address in blacklist
		 *
		 * @since   1.0.0
		 *
		 * @param   $order       WC_Order
		 * @param   $risk_score  integer
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_address_to_blacklist( $order, $risk_score ) {

			if ( get_option( 'ywaf_address_blacklist_enable' ) == 'yes' && get_option( 'ywaf_address_blacklist_auto_add' ) == 'yes' ) {

				$blacklist = new YWAF_Address_Blacklist();

				if ( $risk_score >= $this->risk_thresholds['high'] && ! $blacklist->get_fraud_risk( $order ) ) {

					$blacklist->add_to_blacklist( $order );

				}

			}

		}

		/**
		 * Send email to administrator
		 *
		 * @since   1.0.5
		 *
		 * @param   $order       WC_Order
		 * @param   $risk_score  integer
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function send_admin_mail( $order, $risk_score ) {

			if ( get_option( 'ywaf_admin_mail_enable' ) === 'yes' && apply_filters( 'ywaf_send_for_every_status', true, $risk_score ) ) {
				$wc_email = WC_Emails::instance();
				$email    = $wc_email->emails['YWAF_Admin_Notification'];
				$email->trigger( $order );
			}

		}

		/**
		 * Check if paypal orders as On-Hold
		 *
		 * @since   1.0.0
		 *
		 * @param   $status   string
		 * @param   $order_id integer
		 * @param   $order    WC_Order
		 *
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function set_paypal_order_onhold( $status, $order_id, $order ) {

			$paypal_methods       = array( 'paypal', 'paypal_express', 'yith-paypal-ec', 'ppec_paypal' );
			$order_payment_method = $order->get_payment_method();

			if ( ( get_option( 'ywaf_paypal_enable' ) == 'yes' ) && in_array( $order_payment_method, $paypal_methods ) ) {

				$value              = get_option( 'ywaf_paypal_verified' );
				$verified_addresses = ( $value != '' ) ? explode( ',', $value ) : array();
				$parent_order       = wp_get_post_parent_id( $order->get_id() );

				if ( $parent_order ) {
					$order = wc_get_order( $parent_order );
				}

				$paypal_email_fields = apply_filters( 'ywaf_paypal_email_fields', array(
					'Payer PayPal address',
					'paypal_email'
				) );

				$paypal_email = '';

				foreach ( $paypal_email_fields as $email_field ) {

					if ( $paypal_email == '' ) {
						$paypal_email = $order->get_meta( $email_field );
					}

				}

				if ( $paypal_email == '' || ! in_array( $paypal_email, $verified_addresses ) ) {

					$status = 'on-hold';

				}

			}

			return $status;

		}

		/**
		 * Check if paypal email is verified
		 *
		 * @since   1.0.0
		 *
		 * @param   $result boolean
		 * @param   $order  WC_Order
		 *
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function check_paypal( $result, $order ) {

			$paypal_methods       = array( 'paypal', 'paypal_express', 'yith-paypal-ec', 'ppec_paypal' );
			$order_payment_method = $order->get_payment_method();

			if ( ( get_option( 'ywaf_paypal_enable' ) == 'yes' ) && in_array( $order_payment_method, $paypal_methods ) ) {

				$paypal = new YWAF_PayPal();

				if ( $paypal->get_fraud_risk( $order ) === true || $order->get_meta( 'ywaf_paypal_check' ) == 'process' || $order->get_meta( 'ywaf_paypal_check' ) == 'failed' ) {

					$result = false;

				}

			}

			return $result;

		}

		/**
		 * Custom status for PayPal verification pending
		 *
		 * @since   1.0.0
		 *
		 * @param   $value string
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function paypal_status( $value ) {

			global $post;

			$order        = wc_get_order( $post->ID );
			$paypal_check = $order->get_meta( 'ywaf_paypal_check' );

			switch ( $paypal_check ) {

				case 'process':
					$tip   = __( 'Waiting for PayPal verification.', 'yith-woocommerce-anti-fraud' );
					$value = sprintf( '<mark class="paypal tips" data-tip="%s">%s</mark>', $tip, $tip );
					break;

				case 'waiting':
					$tip   = __( 'Waiting for PayPal transaction data.', 'yith-woocommerce-anti-fraud' );
					$value = sprintf( '<mark class="paypal tips" data-tip="%s">%s</mark>', $tip, $tip );
					break;

				case 'failed':
					$tip   = __( 'PayPal verification failed.', 'yith-woocommerce-anti-fraud' );
					$value = sprintf( '<mark class="paypal-failed tips" data-tip="%s">%s</mark>', $tip, $tip );
					break;

			}

			return $value;

		}

		/**
		 * Add the YWAF_PayPal_Verify and YWAF_Admin_Notification classes to WooCommerce mail classes
		 *
		 * @since   1.0.0
		 *
		 * @param   $email_classes array
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function paypal_mail( $email_classes ) {

			$email_classes['YWAF_PayPal_Verify']      = include( 'includes/class-ywaf-paypal-email.php' );
			$email_classes['YWAF_Admin_Notification'] = include( 'includes/class-ywaf-admin-notification-email.php' );

			return $email_classes;
		}

		/**
		 * Send PayPal verification email
		 *
		 * @since   1.0.0
		 *
		 * @param   $order WC_Order
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function paypal_mail_send( $order ) {

			$paypal_email_fields = apply_filters( 'ywaf_paypal_email_fields', array(
				'Payer PayPal address',
				'paypal_email'
			) );

			$paypal_email = '';

			foreach ( $paypal_email_fields as $email_field ) {

				if ( $paypal_email == '' ) {
					$paypal_email = $order->get_meta( $email_field );
				}

			}

			$wc_email = WC_Emails::instance();
			$email    = $wc_email->emails['YWAF_PayPal_Verify'];

			$email->trigger( $order, $paypal_email );

		}

		/**
		 * Daily check of orders waiting paypal verification
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function paypal_cron() {

			$args = array(
				'post_type'      => 'shop_order',
				'posts_per_page' => - 1,
				'post_status'    => 'any',
				'meta_query'     => array(
					array(
						'key'     => 'ywaf_paypal_check',
						'value'   => 'process',
						'compare' => '='
					)
				)

			);

			add_filter( 'posts_where', array( $this, 'paypal_waiting_where' ) );
			$resend_query = new WP_Query( $args );
			remove_filter( 'posts_where', array( $this, 'paypal_waiting_where' ) );

			if ( $resend_query->have_posts() ) {

				while ( $resend_query->have_posts() ) {

					$resend_query->the_post();

					$order = wc_get_order( $resend_query->post->ID );

					$this->paypal_mail_send( $order );

				}
			}

			wp_reset_query();
			wp_reset_postdata();

			add_filter( 'posts_where', array( $this, 'paypal_cancel_where' ) );
			$cancel_query = new WP_Query( $args );
			remove_filter( 'posts_where', array( $this, 'paypal_cancel_where' ) );

			if ( $cancel_query->have_posts() ) {

				while ( $cancel_query->have_posts() ) {

					$cancel_query->the_post();

					$order = wc_get_order( $cancel_query->post->ID );
					$order->update_status( 'wc-cancelled', 'PayPal verification failed. The email address is not verified.' );
					$order->update_meta_data( 'ywaf_risk_factor', array(
						'score'        => 100,
						'failed_rules' => array( 'YWAF_PayPal' )
					) );
					$order->update_meta_data( 'ywaf_check_status', 'high_risk' );
					$order->update_meta_data( 'ywaf_paypal_check', 'failed' );
					$order->save();

					$this->add_email_to_blacklist( $order, 100 );
					$this->add_address_to_blacklist( $order, 100 );
					$this->send_admin_mail( $order, 100 );

				}

			}

			wp_reset_query();
			wp_reset_postdata();

		}

		/**
		 * Daily check of orders waiting paypal verification
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function paypal_data_cron() {

			$args = array(
				'post_type'      => 'shop_order',
				'posts_per_page' => - 1,
				'post_status'    => 'any',
				'meta_query'     => array(
					array(
						'key'     => 'ywaf_paypal_check',
						'value'   => 'waiting',
						'compare' => '='
					)
				)

			);

			$recheck_query = new WP_Query( $args );

			if ( $recheck_query->have_posts() ) {

				while ( $recheck_query->have_posts() ) {

					$recheck_query->the_post();

					$this->set_fraud_check( $recheck_query->post->ID );

				}
			}

			wp_reset_query();
			wp_reset_postdata();

		}

		/**
		 * Set custom where condition
		 *
		 * @since   1.0.0
		 *
		 * @param   $where string
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function paypal_waiting_where( $where = '' ) {

			$resend_days = get_option( 'ywaf_paypal_resend_days' );
			$cancel_days = get_option( 'ywaf_paypal_cancel_days' );

			$where .= " AND post_date > '" . date( 'Y-m-d', strtotime( '-' . $cancel_days . ' days' ) ) . "'" . " AND post_date <= '" . date( 'Y-m-d', strtotime( '-' . $resend_days . ' days' ) ) . "'";

			return $where;

		}

		/**
		 * Set custom where condition
		 *
		 * @since   1.0.0
		 *
		 * @param   $where string
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function paypal_cancel_where( $where = '' ) {

			$cancel_days = get_option( 'ywaf_paypal_cancel_days' );

			$where .= " AND post_date <= '" . date( 'Y-m-d', strtotime( '-' . $cancel_days . ' days' ) ) . "'";

			return $where;

		}

		/**
		 * Get Payer address for PayPal check
		 *
		 * @since   1.0.0
		 *
		 * @param   $posted array
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function get_paypal_payer_address( $posted ) {

			if ( get_option( 'ywaf_paypal_enable' ) == 'yes' ) {

				if ( empty( $posted['custom'] ) ) {
					return;
				}

				$custom = json_decode( $posted['custom'] );

				if ( $custom && is_object( $custom ) ) {
					$order_id  = $custom->order_id;
					$order_key = $custom->order_key;
				} else {
					// Nothing was found.
					return;
				}

				$order = wc_get_order( $order_id );

				if ( ! $order ) {
					// We have an invalid $order_id, probably because invoice_prefix has changed.
					$order_id = wc_get_order_id_by_order_key( $order_key );
					$order    = wc_get_order( $order_id );
				}

				if ( ! $order || $order->get_order_key() !== $order_key ) {
					return;
				}

				if ( $order ) {

					if ( ! empty( $posted['payer_email'] ) ) {

						$order->add_meta_data( 'Payer PayPal address', wc_clean( $posted['payer_email'] ) );
						$order->save();

					}

				}

			}

		}

		/**
		 * If is active YITH WooCommerce Email Templates, add YWAF to list
		 *
		 * @since   1.0.0
		 *
		 * @param   $templates array
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function add_yith_wcet_template( $templates ) {

			$templates[] = array(
				'id'   => 'yith-anti-fraud',
				'name' => 'YITH WooCommerce Anti-Fraud',
			);
			$templates[] = array(
				'id'   => 'yith-anti-fraud-admin',
				'name' => 'YITH WooCommerce Anti-Fraud Admin',
			);

			return $templates;

		}

		/**
		 * ADMIN FUNCTIONS
		 */

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 * @use     /Yit_Plugin_Panel class
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		public function add_menu_page() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array(
				'general'           => __( 'General Settings', 'yith-woocommerce-anti-fraud' ),
				'blacklist'         => __( 'Emails Blacklist Settings', 'yith-woocommerce-anti-fraud' ),
				'address-blacklist' => __( 'Addresses Blacklist Settings', 'yith-woocommerce-anti-fraud' ),
				'paypal'            => __( 'PayPal Settings', 'yith-woocommerce-anti-fraud' ),
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => _x( 'Anti-Fraud', 'plugin name in admin page title', 'yith-woocommerce-anti-fraud' ),
				'menu_title'       => 'Anti-Fraud',
				'capability'       => apply_filters( 'ywaf_change_capability', 'manage_options' ),
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YWAF_DIR . 'plugin-options',
				'class'            => yith_set_wrapper_class(),
			);

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * Enqueue script file
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function enqueue_scripts_admin() {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script( 'ywaf-admin-knob', YWAF_ASSETS_URL . '/js/jquery.knob' . $suffix . '.js', array( 'jquery' ) );

			wp_enqueue_style( 'ywaf-admin', YWAF_ASSETS_URL . '/css/ywaf-admin' . $suffix . '.css' );
			wp_enqueue_script( 'ywaf-admin', YWAF_ASSETS_URL . '/js/ywaf-admin' . $suffix . '.js', array( 'jquery', 'ywaf-admin-knob' ) );

			if ( isset( $_GET['post'] ) ) {

				$query_args = array(
					'action'   => 'ywaf_fraud_risk_check',
					'order_id' => $_GET['post'],
					'single'   => '1',
					'_wpnonce' => wp_create_nonce( 'ywaf-check-fraud-risk' )
				);

				$args = array(
					'ajax_url' => add_query_arg( $query_args, str_replace( array( 'https:', 'http:' ), '', admin_url( 'admin-ajax.php' ) ) )
				);

				wp_localize_script( 'ywaf-admin', 'ywaf', $args );

			}

		}

		/**
		 * Advise if no rule is active
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function admin_notices() {

			$active_rules = 0;

			foreach ( $this->rules as $rule ) {

				if ( $rule['active'] == 'yes' ) {

					$active_rules ++;

				}

			}

			if ( $active_rules === 0 && get_option( 'ywaf_email_blacklist_enable' ) != 'yes' && get_option( 'ywaf_paypal_enable' ) != 'yes' && get_option( 'ywaf_address_blacklist_enable' ) != 'yes' ): ?>
                <div class="error">
                    <p>
						<?php _e( 'You must activate at least one rule to monitor your orders against fraud.', 'yith-woocommerce-anti-fraud' ); ?>
                    </p>
                </div>
			<?php endif;

		}

		/**
		 * Add the order fraud risk column
		 *
		 * @since   1.0.0
		 *
		 * @param   $columns array
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function add_ywaf_column( $columns ) {

			$label = __( 'Fraud Risk Level', 'yith-woocommerce-anti-fraud' );

			$columns = array_merge( array_slice( $columns, 0, 1 ), array( 'ywaf_status' => '<span class="ywaf_status tips" data-tip="' . $label . '">' . $label . '</span>' ), array_slice( $columns, 1 ) );

			return $columns;

		}

		/**
		 * Render the order fraud risk column
		 *
		 * @since   1.0.0
		 *
		 * @param   $column string
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function render_ywaf_column( $column ) {

			if ( 'ywaf_status' == $column ) {

				global $post;

				$order        = wc_get_order( $post->ID );
				$risk_factor  = $order->get_meta( 'ywaf_risk_factor' );
				$risk_data    = $this->get_risk_level( isset ( $risk_factor['score'] ) ? $risk_factor['score'] : '' );
				$failed_rules = '<br />';

				if ( isset( $risk_factor['failed_rules'] ) ) {

					foreach ( $risk_factor['failed_rules'] as $failed_rule ) {

						if ( class_exists( $failed_rule ) ) {

							$rule = new $failed_rule;

							$failed_rules .= esc_html( $rule->get_message() ) . '<br />';

						}

					}
				}

				echo apply_filters( 'ywaf_paypal_status', sprintf( '<mark class="%s tips" data-tip="%s">%s</mark>', $risk_data['class'], $risk_data['tip'] . $failed_rules, $risk_data['tip'] ) );

			}

		}

		/**
		 * Add fraud check button to order table
		 *
		 * @since   1.0.0
		 *
		 * @param   $actions array
		 * @param   $order   WC_Order
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function table_action( $actions, $order ) {

			$risk_factor  = $order->get_meta( 'ywaf_risk_factor' );
			$button_label = ( $risk_factor ) ? __( 'Repeat fraud risk check', 'yith-woocommerce-anti-fraud' ) : __( 'Start fraud risk check', 'yith-woocommerce-anti-fraud' );
			$query_args   = array(
				'action'   => 'ywaf_fraud_risk_check',
				'order_id' => $order->get_id(),
				'_wpnonce' => wp_create_nonce( 'ywaf-check-fraud-risk' ),
				'repeat'   => ( $risk_factor ) ? 'true' : 'false'
			);
			$ajax_url     = add_query_arg( $query_args, str_replace( array( 'https:', 'http:' ), '', admin_url( 'admin-ajax.php' ) ) );

			$actions['ywaf-check'] = array(
				'url'    => $ajax_url,
				'name'   => $button_label,
				'action' => 'ywaf-check'

			);

			return $actions;

		}

		/**
		 * Sanitize empty array
		 *
		 * @since   1.1.1
		 *
		 * @param   $value array
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function sanitize_empty_array( $value ) {
			return ( empty( $value ) ? array() : $value );
		}

		/**
		 * Sanitize address array
		 *
		 * @since   1.2.4
		 *
		 * @param   $value array
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function sanitize_address_field( $value ) {

			if ( empty( $value ) ) {
				$value = '';
			} else {
				$value = maybe_serialize( $value );
			}

			return $value;

		}

		/**
		 * FRONTEND FUNCTIONS
		 */

		/**
		 * Enqueue script file
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function enqueue_scripts_frontend() {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( 'ywaf-frontend', YWAF_ASSETS_URL . '/js/ywaf-frontend' . $suffix . '.js', array( 'jquery' ) );

			$query_args = array(
				'action'   => 'resend_paypal_email',
				'_wpnonce' => wp_create_nonce( 'ywaf-resend-email' )
			);
			$args       = array(
				'ajax_url' => add_query_arg( $query_args, str_replace( array( 'https:', 'http:' ), '', admin_url( 'admin-ajax.php' ) ) )
			);

			wp_localize_script( 'ywaf-frontend', 'ywaf', $args );

		}

		/**
		 * Verify paypal email address and proceed to anti-fraud check
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function verify_paypal_code() {

			if ( isset( $_GET['ywaf_pvk'] ) && get_option( 'ywaf_paypal_enable' ) == 'yes' ) {

				$params     = explode( ',', base64_decode( $_GET['ywaf_pvk'] ) );
				$order_id   = str_replace( '#', '', base64_decode( $params[0] ) );
				$email      = base64_decode( $params[1] );
				$order_date = base64_decode( $params[2] );
				$order      = wc_get_order( $order_id );

				if ( $order && $order->get_date_created() == $order_date ) {

					$paypal_check = $order->get_meta( 'ywaf_paypal_check' );

					if ( $paypal_check == 'process' ) {

						$paypal_email_fields = apply_filters( 'ywaf_paypal_email_fields', array(
							'Payer PayPal address',
							'paypal_email'
						) );

						$stored_email = '';

						foreach ( $paypal_email_fields as $email_field ) {

							if ( $stored_email == '' ) {
								$stored_email = $order->get_meta( $email_field );
							}

						}

						if ( $email == $stored_email ) {

							$order->update_meta_data( 'ywaf_paypal_check', 'success' );
							$order->save();

							$paypal = new YWAF_PayPal();
							$paypal->add_to_verified( $email );

							$this->set_fraud_check( $order->get_id() );

							wc_add_notice( __( 'PayPal email address has been successfully checked!', 'yith-woocommerce-anti-fraud' ), 'success' );
							wc_print_notices();

						}

					}

				}

			}

		}

		/**
		 * Show info for order status
		 *
		 * @since   1.0.0
		 *
		 * @param   $order_id integer
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function view_order( $order_id ) {

			$order = wc_get_order( $order_id );

			$paypal_check         = $order->get_meta( 'ywaf_paypal_check' );
			$check_status         = $order->get_meta( 'ywaf_check_status' );
			$paypal_methods       = array( 'paypal', 'paypal_express', 'yith-paypal-ec', 'ppec_paypal' );
			$order_payment_method = $order->get_payment_method();

			switch ( true ) {

				case $paypal_check == 'process':

					?>

                    <h2>
						<?php _e( 'Order Status', 'yith-woocommerce-anti-fraud' ); ?>
                    </h2>
                    <p>
						<?php _e( 'In order to complete the order, you have to complete your verification process by clicking on the link we sent to your PayPal email address.', 'yith-woocommerce-anti-fraud' ) ?>
                    </p>
                    <p>
						<?php _e( 'If you have not received the verification email, you can ask for a new by clicking the following button', 'yith-woocommerce-anti-fraud' ) ?>
                    </p>
                    <p>
                        <input type="hidden" id="ywcc_order_id" value="<?php echo $order->get_id(); ?>" />
                        <button type="button" class="button button-primary ywaf-resend-email"><?php _e( 'Re-send email', 'yith-woocommerce-anti-fraud' ) ?></button>
                    </p>
					<?php

					break;

				case $paypal_check == 'failed':

					?>

                    <h2>
						<?php _e( 'Order Status', 'yith-woocommerce-anti-fraud' ); ?>
                    </h2>
                    <p>
						<?php _e( 'We could not verify your PayPal email address and therefore your order has been cancelled. If you think an error has occurred, contact our customer service.', 'yith-woocommerce-anti-fraud' ) ?>
                    </p>

					<?php

					break;

				case $check_status == 'high_risk':

					?>

                    <h2>
						<?php _e( 'Order Status', 'yith-woocommerce-anti-fraud' ); ?>
                    </h2>
                    <p>
						<?php _e( 'The order has not passed anti-fraud tests, therefore it has been cancelled. If you think an error has occurred, contact our customer service.', 'yith-woocommerce-anti-fraud' ) ?>
                    </p>

					<?php

					break;

				default:

			}

			if ( get_option( 'ywaf_protect_downloads' ) == 'yes' && get_option( 'ywaf_paypal_enable' ) == 'yes' && in_array( $order_payment_method, $paypal_methods ) && $order->has_downloadable_item() ) {


				if ( $paypal_check == 'process' ) {
					$message = __( 'Files cannot be downloaded because the PayPal verification for this order has failed!', 'yith-woocommerce-anti-fraud' );
				} else {
					$message = __( 'Files cannot be downloaded because there is a pending PayPal verification for this order.', 'yith-woocommerce-anti-fraud' );
				}

				wc_print_notice( $message, 'error' );

			}

		}

		/**
		 * Alter links if user cannot download
		 *
		 * @since   1.0.0
		 *
		 * @param   $user_email  string
		 * @param   $order_key   string
		 * @param   $product_id  integer
		 * @param   $user_id     integer
		 * @param   $download_id integer
		 * @param   $order_id    integer
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function prevent_download( $user_email, $order_key, $product_id, $user_id, $download_id, $order_id ) {

			$order                = wc_get_order( $order_id );
			$paypal_methods       = array( 'paypal', 'paypal_express', 'yith-paypal-ec', 'ppec_paypal' );
			$order_payment_method = $order->get_payment_method();

			if ( get_option( 'ywaf_protect_downloads' ) == 'yes' && get_option( 'ywaf_paypal_enable' ) == 'yes' && in_array( $order_payment_method, $paypal_methods ) && $order->has_downloadable_item() ) {
				$paypal_check = $order->get_meta( 'ywaf_paypal_check' );

				if ( $paypal_check != 'success' ) {

					if ( class_exists( 'YITH_WooCommerce_Sequential_Order_Number' ) ) {
						$order_num = get_option( 'ywson_order_prefix' ) . $order->get_meta( 'ywson_custom_number_order' ) . get_option( 'ywson_order_suffix' );
					} else {
						$order_num = $order->get_id();
					}

					$order_link = '<a href="' . esc_url( wc_get_endpoint_url( 'view-order', $order_id, wc_get_page_permalink( 'myaccount' ) ) ) . '" class="wc-forward">' . sprintf( __( '#%s', 'yith-woocommerce-anti-fraud' ), $order_num ) . '</a>';

					if ( $paypal_check == 'failed' ) {
						$message = sprintf( __( 'This file cannot be downloaded because the PayPal verification for the order %s has failed!', 'yith-woocommerce-anti-fraud' ), $order_link );
					} else {
						$message = sprintf( __( 'This file cannot be downloaded because there is a pending PayPal verification for the order %s.', 'yith-woocommerce-anti-fraud' ), $order_link );
					}

					wp_die( $message, '', array( 'response' => 403 ) );

				}

			}

		}

		/**
		 * YITH FRAMEWORK
		 */

		/**
		 * Load plugin framework
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Andrea Grillo
		 * <andrea.grillo@yithemes.com>
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
		 * Plugin row meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @since   1.0.0
		 *
		 * @param   $new_row_meta_args array
		 * @param   $plugin_meta       string
		 * @param   $plugin_file       string
		 * @param   $plugin_data       array
		 * @param   $status            string
		 * @param   $init_file         string
		 *
		 * @return  array
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWAF_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug']       = YWAF_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;

		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 * @since   1.0.0
		 *
		 * @param   $links | links plugin array
		 *
		 * @return  mixed
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->_panel_page, true );

			return $links;
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @since   2.0.0
		 * @return  void
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once 'plugin-fw/licence/lib/yit-licence.php';
				require_once 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YWAF_INIT, YWAF_SECRET_KEY, YWAF_SLUG );
		}

		/**
		 * Register privacy text
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function include_privacy_text() {
			include_once( 'includes/class-ywaf-privacy.php' );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @since   2.0.0
		 * @return  void
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once( 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YWAF_SLUG, YWAF_INIT );
		}


	}

}