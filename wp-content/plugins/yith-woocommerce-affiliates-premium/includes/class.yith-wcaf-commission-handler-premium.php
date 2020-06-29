<?php
/**
 * Commission Handler Premium class
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

if ( ! class_exists( 'YITH_WCAF_Commission_Handler_Premium' ) ) {
	/**
	 * WooCommerce Commission Handler Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Commission_Handler_Premium extends YITH_WCAF_Commission_Handler {

		/**
		 * Single instance of the class for each token
		 *
		 * @var \YITH_WCAF_Commission_Handler_Premium
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/**
		 * Whether persistent commission calculation is enabled
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_persistent_calculation = 'no';

		/**
		 * Array of products excluded from affiliation program
		 *
		 * @var array
		 * @since 1.2.5
		 */
		protected $_excluded_products = array();

		/**
		 * Array of users excluded from affiliation program
		 *
		 * @var array
		 * @since 1.2.5
		 */
		protected $_excluded_users = array();

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAF_Commission_Handler_Premium
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();

			// Subscription compatibility methods
			$this->handle_subscription_renews();

			add_filter( 'yith_wcaf_general_settings', array( $this, 'filter_general_settings' ), 15 );

			add_action( 'woocommerce_process_shop_order_meta', array( $this, 'assign_order_commissions' ), 10, 1 );

			// handle panel button actions
			add_action( 'admin_init', array( $this, 'export_csv' ) );
			add_action( 'admin_action_yith_wcaf_change_commission_status', array(
				$this,
				'handle_switch_status_panel_actions'
			) );
			add_action( 'admin_action_yith_wcaf_pay_commission', array( $this, 'handle_payments_panel_actions' ) );
			add_action( 'admin_action_yith_wcaf_delete_order_affiliate', array( $this, 'unassign_order_commissions' ) );

			// add commissions overview to admin new order email
			add_action( 'woocommerce_email_order_meta', array( $this, 'add_mail_commission_table' ), 10, 4 );

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
			$premium_settings   = array(
				'commission-persistent-calculation' => array(
					'title'   => __( 'Calculate commissions permanently', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Register referral token within customer and credit commissions to the affiliate for any future customer purchase', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_commission_persistent_calculation',
					'default' => 'no'
				),

				'commission-persistent-avoid-referral-change' => array(
					'title'   => __( 'Prevent referral switch', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => __( 'When "persistent commissions" is enabled, you can choose to bind the customer to a specific affiliate, thus preventing the system from automatically assigning a different affiliate to the customer who visits other affiliate links', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_avoid_referral_change',
					'default' => 'no'
				),

				'commission-persistent-percentage' => array(
					'title'             => __( 'Persistent commissions rate', 'yith-woocommerce-affiliates' ),
					'type'              => 'number',
					'desc'              => __( 'Percentage of first commission rate applied for next purchases (please, use values greater than 100 carefully; no check on final commission amount is performed)', 'yith-woocommerce-affiliates' ),
					'id'                => 'yith_wcaf_persistent_rate',
					'css'               => 'max-width: 50px;',
					'default'           => 0,
					'custom_attributes' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 'any'
					)
				),

				'commission-pending-notify-admin' => array(
					'title'   => __( 'Notify admin', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => sprintf( '%s <a href="%s">%s</a>', __( 'Notify admin when a commission switches to pending; customize email on', 'yith-woocommerce-affiliates' ), esc_url( add_query_arg( array(
						'page'    => 'wc-settings',
						'tab'     => 'email',
						'section' => 'yith_wcaf_admin_pending_commission_email'
					), admin_url( 'admin.php' ) ) ), __( 'WooCommerce Settings Page', 'yith-woocommerce-affiliates' ) ),
					'id'      => 'yith_wcaf_commission_pending_notify_admin',
					'default' => 'yes'
				),
			);
			$exclusion_settings = array(
				'exclusions-options'           => array(
					'title' => __( 'Exclusions', 'yith-woocommerce-affiliates' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'yith_wcaf_exclusions_options'
				),
				'exclusions-excluded-products' => array(
					'title'     => __( 'Excluded products', 'yith-woocommerce-affiliates' ),
					'desc'      => __( 'A list of products excluded from affiliation plan', 'yith-woocommerce-affiliates' ),
					'type'      => 'yith-field',
					'yith-type' => 'ajax-products',
					'multiple'  => true,
					'data'      => array(
						'action'   => 'woocommerce_json_search_products_and_variations',
						'security' => wp_create_nonce( 'search-products' )
					),
					'id'        => 'yith_wcaf_exclusions_excluded_products',
				),
				'exclusions-excluded-users'    => array(
					'title'     => __( 'Excluded users', 'yith-woocommerce-affiliates' ),
					'desc'      => __( 'A list of users excluded from affiliation plan', 'yith-woocommerce-affiliates' ),
					'type'      => 'yith-field',
					'yith-type' => 'ajax-customers',
					'multiple'  => true,
					'id'        => 'yith_wcaf_exclusions_excluded_users',
				),
				'exclusions-options-end'       => array(
					'type' => 'sectionend',
					'id'   => 'yith_wcaf_exclusions_options'
				),
			);

			if ( class_exists( 'YWSBS_Subscription' ) ) {
				/**
				 * @since 1.2.4
				 */
				$premium_settings = array_merge(
					$premium_settings,
					array(
						'commission-subscription-renews-handling' => array(
							'title'   => __( 'Enable commission handling for YITH Subscriptions\' renews', 'yith-woocommerce-affiliates' ),
							'type'    => 'select',
							'desc'    => __( 'Generate commission for YITH WooCommerce Subscription renews when first order was registered to an affiliate', 'yith-woocommerce-affiliates' ),
							'id'      => 'yith_wcaf_subscription_renew_handling',
							'default' => 'none',
							'options' => array(
								'none'                  => __( 'Do not handle renews', 'yith-woocommerce-affiliates' ),
								'only_after_activation' => __( 'Register only first renew, when subscriptions switches from trial to active', 'yith-woocommerce-affiliates' ),
								'all_renews'            => __( 'Register all renews', 'yith-woocommerce-affiliates' )
							)
						)
					)
				);
			}

			if ( class_exists( 'WC_Subscription' ) ) {
				/**
				 * @since 1.6.3
				 */
				$premium_settings = array_merge(
					$premium_settings,
					array(
						'commission-woo-subscription-renews-handling' => array(
							'title'   => __( 'Enable commission handling for WC Subscriptions\' renews', 'yith-woocommerce-affiliates' ),
							'type'    => 'select',
							'desc'    => __( 'Generate commission for WC Subscription renews when first order was registered to an affiliate', 'yith-woocommerce-affiliates' ),
							'id'      => 'yith_wcaf_woo_subscription_renew_handling',
							'default' => 'none',
							'options' => array(
								'none'       => __( 'Do not handle renews', 'yith-woocommerce-affiliates' ),
								'all_renews' => __( 'Register all renews', 'yith-woocommerce-affiliates' )
							)
						)
					)
				);
			}

			$settings['settings']                                    = yith_wcaf_append_items( $settings['settings'], 'commission-exclude-discount', $premium_settings );
			$settings['settings']                                    = yith_wcaf_append_items( $settings['settings'], 'commission-options-end', $exclusion_settings );
			$settings['settings']['commission-general-rate']['desc'] = sprintf( '%s "<a href="%s">%s</a>" %s', __( 'General rate to apply to affiliates;', 'yith-woocommerce-affiliates' ), esc_url( add_query_arg( array(
				'page' => 'yith_wcaf_panel',
				'tab'  => 'rates'
			), admin_url( 'admin.php' ) ) ), __( 'Rate Panel', 'yith-woocommerce-affiliates' ), __( ' can be used to specify rates per user/product', 'yith-woocommerce-affiliates' ) );

			return $settings;
		}

		/* === INIT METHODS === */

		/**
		 * Init class attributes for admin options
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function _retrieve_options() {
			parent::_retrieve_options();

			$this->_persistent_calculation = get_option( 'yith_wcaf_commission_persistent_calculation', $this->_persistent_calculation );
			$this->_excluded_products      = get_option( 'yith_wcaf_exclusions_excluded_products', $this->_excluded_products );
			$this->_excluded_users         = get_option( 'yith_wcaf_exclusions_excluded_users', $this->_excluded_users );
		}

		/* === ORDER HANDLING METHODS === */

		/**
		 * Create orders commissions, on process checkout action, and when an order is untrashed
		 *
		 * @param $order_id     int Order id
		 * @param $token        string Referral token
		 * @param $token_origin string Referral token origin
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function create_order_commissions( $order_id, $token, $token_origin = 'undefined' ) {
			$order     = wc_get_order( $order_id );
			$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_token( $token );

			// if no order or user, return
			if ( ! $order || ! $affiliate || ( is_array( $this->_excluded_users ) && in_array( $affiliate['user_id'], $this->_excluded_users ) ) || ! apply_filters( 'yith_wcaf_create_order_commissions', true, $order_id, $token, $token_origin ) ) {
				return;
			}

			// map commission status on order status
			$commission_status = $this->map_commission_status( $order->get_status() );

			yit_save_prop( $order, '_yith_wcaf_referral', $token );

			// process commission, add order item meta, register order as processed
			$items = $order->get_items( 'line_item' );
			if ( ! empty( $items ) ) {
				foreach ( $items as $item_id => $item ) {
					$product_id = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];

					if ( ! apply_filters( 'yith_wcaf_create_product_commission', ! in_array( $product_id, $this->_excluded_products ), $product_id, $order_id, $token, $token_origin ) ) {
						continue;
					}

					$rate = wc_get_order_item_meta( $item_id, '_yith_wcaf_commission_rate', true );

					if ( ! $rate ) {
						$rate = YITH_WCAF_Rate_Handler()->get_rate( $affiliate, intval( $product_id ), $order_id );

						// correct commission rate, when persistent calculation is enabled
						if ( $this->_persistent_calculation == 'yes' && $token_origin == 'persistent' ) {
							$rate = YITH_WCAF_Rate_Handler()->get_persistent_rate( $rate, $token, $item );
						}
					}

					$commission = $this->_calculate_line_item_commission( $order, $item_id, $item, $rate );

					$commission_args = array(
						'order_id'     => $order_id,
						'affiliate_id' => $affiliate['ID'],
						'line_item_id' => $item_id,
						'rate'         => $rate,
						'amount'       => $commission,
						'status'       => $commission_status,
						'created_at'   => apply_filters( 'yith_wcaf_create_order_commission_use_current_date', true ) ? current_time( 'mysql' ) : yit_get_prop( $order, 'order_date' )
					);

					$old_id = wc_get_order_item_meta( $item_id, '_yith_wcaf_commission_id', true );

					if ( $old_id ) {
						$id = $old_id;
						$this->update( $id, $commission_args );
					} else {
						$id = $this->add( $commission_args );
					}

					if ( $commission_status == 'pending' ) {
						YITH_WCAF_Affiliate_Handler()->update_affiliate_total( $affiliate['ID'], $commission );
					}

					wc_update_order_item_meta( $item_id, '_yith_wcaf_commission_id', $id );
					wc_update_order_item_meta( $item_id, '_yith_wcaf_commission_rate', $rate );
					wc_update_order_item_meta( $item_id, '_yith_wcaf_commission_amount', $commission );
				}
			}
		}

		/* === YITH SUBSCRIPTION COMPATIBILITY === */

		/**
		 * Add actions to handle subscriptions' renews
		 *
		 * @return void
		 * @since 1.2.4
		 */
		public function handle_subscription_renews() {
			$handle_subscription = get_option( 'yith_wcaf_subscription_renew_handling', 'none' );

			if ( 'only_after_activation' == $handle_subscription ) {
				add_action( 'ywsbs_subscription_status_trial_to_active', array(
					$this,
					'generate_commissions_for_subscription_renews'
				), 10, 1 );
			} elseif ( 'all_renews' == $handle_subscription ) {
				add_action( 'ywsbs_renew_order_payed', array(
					$this,
					'generate_commissions_for_subscription_renews'
				), 10, 1 );
			}

			$handle_woo_subscription = get_option( 'yith_wcaf_woo_subscription_renew_handling', 'none' );

			if ( 'all_renews' == $handle_woo_subscription ) {
				add_filter( 'wcs_renewal_order_created', array(
					$this,
					'generate_commissions_for_woo_subscription_renews'
				), 10, 2 );
			}

		}

		/**
		 * Generate commissions for subscription renew
		 *
		 * @param int Subscription object or subscription id
		 *
		 * @return void
		 *
		 * @since 1.2.4
		 */
		function generate_commissions_for_subscription_renews( $subscription_id ) {
			$subscription = ywsbs_get_subscription( $subscription_id );

			$first_order = $subscription->order_id;
			$renew_order = $subscription->renew_order;
			$token       = get_post_meta( $first_order, '_yith_wcaf_referral', true );

			if ( ! empty( $token ) && ! empty( $renew_order ) ) {
				YITH_WCAF_Commission_Handler()->create_order_commissions( $renew_order, $token );
			}
		}

		/**
		 * Generate commissions for WC Subscription renew order
		 *
		 * @param $renew        \WC_Order|\WP_Error Renew order
		 * @param $subscription \WC_Subscription Subscription
		 * @retrn \WC_Order|\WP_Error Unmodified input of the filter
		 */
		function generate_commissions_for_woo_subscription_renews( $renew, $subscription ) {
			$original_order_id = $subscription->get_parent_id();
			$token             = get_post_meta( $original_order_id, '_yith_wcaf_referral', true );

			if ( ! empty( $token ) && ! empty( $renew ) && ! is_wp_error( $renew ) ) {
				YITH_WCAF_Commission_Handler()->create_order_commissions( $renew->get_id(), $token );
			}

			return $renew;
		}

		/* === ASSIGN AFFILIATE TO AN ORDER === */

		/**
		 * Assign affiliate to an order and create commissions
		 *
		 * @param $order_id int|bool If order_id is set, method will use it; otherwise, will get it from $_REQUEST
		 *
		 * @return void
		 * @since 1.0.9
		 */
		public function assign_order_commissions( $order_id = false ) {
			if ( empty( $order_id ) && ! isset( $_REQUEST['order_id'] ) ) {
				return;
			} elseif ( isset( $_REQUEST['order_id'] ) ) {
				$order_id = intval( $_REQUEST['order_id'] );
			}

			if ( ! isset( $_REQUEST['persistent_token'] ) ) {
				return;
			}

			$order = wc_get_order( $order_id );

			$affiliate_id     = intval( $_REQUEST['persistent_token'] );
			$affiliate        = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $affiliate_id );
			$referral_history = yit_get_prop( $order, '_yith_wcaf_referral_history', true );
			$referral_history = ! empty( $referral_history ) ? $referral_history : array();

			if ( ! $affiliate ) {
				return;
			}

			$new_token = $affiliate['token'];
			yit_save_prop( $order, '_yith_wcaf_referral', $new_token );

			$referral_history[] = $new_token;
			yit_save_prop( $order, '_yith_wcaf_referral_history', $referral_history );

			$this->regenerate_order_commissions( $order_id, $new_token );
		}

		/**
		 * Unassign affiliate from an order and delete commissions
		 *
		 * Redirect to order edit page once completed
		 *
		 * @param $order_id int|bool If order_id is set, method will use it; otherwise, will get it from $_REQUEST
		 *
		 * @return void
		 * @since 1.0.9
		 */
		public function unassign_order_commissions( $order_id = false ) {
			if ( empty( $order_id ) && ! isset( $_REQUEST['order_id'] ) ) {
				return;
			} elseif ( isset( $_REQUEST['order_id'] ) ) {
				$order_id = intval( $_REQUEST['order_id'] );
			}

			$order = wc_get_order( $order_id );

			$this->delete_order_commissions( $order_id, true, true );
			yit_delete_prop( $order, '_yith_wcaf_referral' );
			yit_delete_prop( $order, '_yith_wcaf_referral_history' );

			wp_redirect( esc_url( get_edit_post_link( $order_id ) ) );
			die();
		}

		/* === PANEL COMMISSION METHODS === */

		/**
		 * Print commission panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_commission_panel() {
			// define variables to use in template
			$commission_id = isset( $_REQUEST['commission_id'] ) ? $_REQUEST['commission_id'] : false;

			if ( ! empty( $commission_id ) && $this->commission_exists( intval( $commission_id ) ) ) {
				// retrieve commission
				$commission = $this->get_commission( $commission_id );

				// retrieve user
				$user_info = get_userdata( $commission['user_id'] );
				$user      = get_user_by( 'id', $commission['user_id'] );

				// retrieve order
				$order = wc_get_order( $commission['order_id'] );
				$items = $order->get_items( 'line_item' );
				$item  = isset( $items[ $commission['line_item_id'] ] ) ? $items[ $commission['line_item_id'] ] : false;

				/**
				 * @var $item \WC_Order_Item_Product
				 */

				// retrieve product
				if ( $order && $item ) {
					$product = is_object( $item ) ? $item->get_product() : $order->get_product_from_item( $item );
				} else {
					$product = wc_get_product( $commission['product_id'] );
				}

				// retrieve notes
				$commission_notes = $this->get_commission_notes( $commission_id );

				// retrieve refunds
				$refunds        = $this->get_commission_refunds( $commission_id );
				$total_refunded = $this->get_total_commission_refund( $commission_id );

				// retrieve payments
				$active_payments   = YITH_WCAF_Payment_Handler()->get_commission_payments( $commission_id, 'active' );
				$inactive_payments = YITH_WCAF_Payment_Handler()->get_commission_payments( $commission_id, 'inactive' );

				// retrieve available action
				$available_commission_actions = array();

				$available_status_change = $this->get_available_status_change( $commission_id );
				if ( ! empty( $available_status_change ) ) {
					if ( in_array( 'pending-payment', $available_status_change ) && ! in_array( $commission['status'], $this->payment_status ) ) {
						$available_gateways = YITH_WCAF_Payment_Handler_Premium()->get_available_gateways();

						if ( ! empty( $available_gateways ) ) {
							foreach ( $available_gateways as $id => $gateway ) {
								$payment_label                                    = sprintf( __( 'Pay via %s', 'yith-woocommerce-affiliates' ), $gateway['label'] );
								$available_commission_actions[ 'pay_via_' . $id ] = $payment_label;
							}
						}
					}

					foreach ( $available_status_change as $status ) {
						if ( in_array( $status, YITH_WCAF_Commission_Handler()->get_dead_status() ) ) {
							continue;
						}

						// avoid direct ( pending-payment -> pending ) status change
						if ( $commission['status'] == 'pending-payment' && $status == 'pending' ) {
							continue;
						}

						$readable_status                                        = YITH_WCAF_Commission_Handler()->get_readable_status( $status );
						$available_commission_actions[ 'switch-to-' . $status ] = sprintf( __( 'Change status to %s', 'yith-woocommerce-affiliates' ), $readable_status );
					}
				}

				// retrieve order currency
				$order_currency = method_exists( $order, 'get_currency' ) ? $order->get_currency() : $order->get_order_currency();

				// require rate panel template
				include( YITH_WCAF_DIR . 'templates/admin/commission-panel-detail.php' );
			} else {
				// prepare user rates table items
				$commissions_table = new YITH_WCAF_Commissions_Table_Premium();
				$commissions_table->prepare_items();

				// require rate panel template
				include( YITH_WCAF_DIR . 'templates/admin/commission-panel-table.php' );
			}
		}

		/**
		 * Handle actions of status changes from the panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function handle_switch_status_panel_actions() {
			$commission_id = isset( $_REQUEST['commission_id'] ) ? $_REQUEST['commission_id'] : 0;
			$new_status    = isset( $_REQUEST['status'] ) && in_array( $_REQUEST['status'], $this->_available_commission_status ) ? $_REQUEST['status'] : '';
			$redirect      = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( $_REQUEST['redirect_to'] ) : esc_url_raw( add_query_arg( array(
				'page' => 'yith_wcaf_panel',
				'tab'  => 'commissions'
			), admin_url( 'admin.php' ) ) );

			if ( ! $commission_id || ! $new_status ) {
				wp_redirect( $redirect );
				die();
			}

			$res      = $this->change_commission_status( $commission_id, $new_status );
			$redirect = esc_url_raw( add_query_arg( array( 'commission_status_change' => $res ), $redirect ) );

			wp_redirect( $redirect );
			die();
		}

		/**
		 * Handle actions of payment from the panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function handle_payments_panel_actions() {
			$commission_id = isset( $_REQUEST['commission_id'] ) ? $_REQUEST['commission_id'] : 0;
			$gateway       = isset( $_REQUEST['gateway'] ) && in_array( $_REQUEST['gateway'], array_keys( YITH_WCAF_Payment_Handler_Premium()->get_available_gateways() ) ) ? $_REQUEST['gateway'] : '';
			$redirect      = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( $_REQUEST['redirect_to'] ) : esc_url_raw( add_query_arg( array(
				'page' => 'yith_wcaf_panel',
				'tab'  => 'commissions'
			), admin_url( 'admin.php' ) ) );

			if ( ! $commission_id ) {
				wp_redirect( $redirect );
				die();
			}

			$res = YITH_WCAF_Payment_Handler()->register_payment( $commission_id, true, $gateway );

			if ( ! $res['status'] ) {
				$errors   = is_array( $res['messages'] ) ? implode( ',', $res['messages'] ) : $res['messages'];
				$redirect = esc_url_raw( add_query_arg( array( 'commission_payment_failed' => urlencode( $errors ) ), $redirect ) );
			} elseif ( empty( $res['cannot_be_paid'] ) ) {
				$redirect = esc_url_raw( add_query_arg( array( 'commission_paid' => $commission_id ), $redirect ) );
			} else {
				$redirect = esc_url_raw( add_query_arg( array( 'commission_unpaid' => $commission_id ), $redirect ) );
			}

			wp_redirect( $redirect );
			die();
		}

		/**
		 * Process bulk action for current view
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function process_bulk_actions() {
			if ( ! empty( $_REQUEST['commissions'] ) ) {
				$current_action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
				$current_action = ( empty( $current_action ) && isset( $_REQUEST['action2'] ) ) ? $_REQUEST['action2'] : $current_action;
				$redirect       = esc_url_raw( remove_query_arg( array( 'action', 'action2', 'commissions' ) ) );

				// handles payment actions
				$matches = array();

				if ( $current_action == 'pay' || preg_match( '^pay_via_([a-zA-Z_-]*)$^', $current_action, $matches ) ) {
					$gateway              = isset( $matches[1] ) ? $matches[1] : false;
					$proceed_with_payment = $gateway ? true : false;
					$to_pay               = $_REQUEST['commissions'];

					// pay filtered commissions
					$res = YITH_WCAF_Payment_Handler()->register_payment( $to_pay, $proceed_with_payment, $gateway );

					if ( ! $res['status'] ) {
						$errors   = is_array( $res['messages'] ) ? implode( ',', $res['messages'] ) : $res['messages'];
						$redirect = esc_url_raw( add_query_arg( array( 'commission_payment_failed' => urlencode( $errors ) ), $redirect ) );
					} else {
						$redirect = esc_url_raw( add_query_arg( array(
							'commission_paid'   => implode( ',', $res['can_be_paid'] ),
							'commission_unpaid' => implode( ',', $res['cannot_be_paid'] )
						), $redirect ) );
					}

				} else {
					parent::process_bulk_actions();
				}

				if ( isset( $_GET['commission_id'] ) ) {
					return;
				}

				wp_redirect( $redirect );
				die();
			}
		}

		/**
		 * Process export, and generate csv file to download with commissions
		 *
		 * @return void
		 * @since 1.1.1
		 */
		public function export_csv() {
			$query_arg = array();

			if (
				! isset( $_REQUEST['page'] ) ||
				$_REQUEST['page'] != 'yith_wcaf_panel' ||
				! isset( $_REQUEST['tab'] ) ||
				$_REQUEST['tab'] != 'commissions' ||
				! isset( $_REQUEST['export_action'] )
			) {
				return;
			}

			if ( ! empty( $_GET['status'] ) && $_GET['status'] != 'all' ) {
				$query_arg['status'] = $_GET['status'];
			}

			if ( ! empty( $_REQUEST['_product_id'] ) ) {
				$query_arg['product_id'] = $_REQUEST['_product_id'];
			}

			if ( ! empty( $_REQUEST['_user_id'] ) ) {
				$query_arg['user_id'] = $_REQUEST['_user_id'];
			}

			if ( ! empty( $_REQUEST['_from'] ) ) {
				$query_arg['interval']['start_date'] = date( 'Y-m-d 00:00:00', strtotime( $_REQUEST['_from'] ) );
			}

			if ( ! empty( $_REQUEST['_to'] ) ) {
				$query_arg['interval']['end_date'] = date( 'Y-m-d 23:59:59', strtotime( $_REQUEST['_to'] ) );
			}

			$commissions = YITH_WCAF_Commission_Handler()->get_commissions(
				array_merge(
					array(
						'orderby' => isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : 'created_at',
						'order'   => isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'DESC',
					),
					$query_arg
				)
			);

			$headings = apply_filters( 'yith_wcaf_commissions_csv_heading', array(
				'ID',
				'order_id',
				'line_item_id',
				'affiliate_id',
				'rate',
				'amount',
				'refunds',
				'status',
				'created_at',
				'last_edit',
				'user_id',
				'product_id',
				'product_name',
				'user_login',
				'user_email',
				'categories'
			), $commissions );

			$sitename = sanitize_key( get_bloginfo( 'name' ) );
			$sitename .= ( ! empty( $sitename ) ) ? '-' : '';
			$filename = $sitename . 'commissions-' . date( 'Y-m-d' ) . '.csv';

			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );

			$df = fopen( 'php://output', 'w' );

			fputcsv( $df, $headings );

			foreach ( $commissions as $row ) {
				// process extra info
				$categories = wp_get_post_terms( $row['product_id'], 'product_cat' );

				if ( empty( $categories ) ) {
					$row[] = __( 'N/A', 'yith-woocommerce-affiliates' );
				} else {
					$column_items = array();

					foreach ( $categories as $category ) {
						$column_items[] = $category->name;
					}

					$row[] = implode( ' | ', $column_items );
				}

				fputcsv( $df, apply_filters( 'yith_wcaf_commissions_csv_row', $row, $headings ) );
			}

			fclose( $df );

			die();
		}

		/* === COMMISSIONS METABOX === */

		/**
		 * Add metabox to order edit page
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_order_metabox() {
			parent::add_order_metabox();

			add_meta_box( 'yith_wcaf_order_referral_history', __( 'Referral History', 'yith-woocommerce-affiliates' ), array(
				$this,
				'print_referral_history_metabox'
			), array( 'shop_order', 'shop_subscription' ), 'side' );
		}

		/**
		 * Print referral history order metabox
		 *
		 * @param $post \WP_Post Current order post object
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_referral_history_metabox( $post ) {
			// set order id
			$order_id = $post->ID;

			// if we're on wc subscription page, use subscription parent order
			if ( 'shop_subscription' == $post->post_type ) {
				$order_id = $post->post_parent;
			}

			$order = wc_get_order( $order_id );

			// define variables to be used on template
			$referral_history       = yit_get_prop( $order, '_yith_wcaf_referral_history', true );
			$referral_history_users = array();

			if ( $referral_history ) {
				foreach ( $referral_history as $referral ) {
					$user      = array();
					$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_token( $referral );

					if ( ! $affiliate ) {
						continue;
					}

					$referral  = $affiliate['user_id'];
					$user_data = get_userdata( $referral );

					if ( ! $user_data ) {
						return;
					}

					$user['user_email'] = $user_data->user_email;

					$user['username'] = '';
					if ( $user_data->first_name || $user_data->last_name ) {
						$user['username'] .= esc_html( ucfirst( $user_data->first_name ) . ' ' . ucfirst( $user_data->last_name ) );
					} else {
						$user['username'] .= esc_html( ucfirst( $user_data->display_name ) );
					}

					$referral_history_users[] = $user;
				}
			}

			include( YITH_WCAF_DIR . 'templates/admin/referral-history-metabox.php' );
		}

		/* === COMMISSIONS TABLE IN EMAILS === */

		/**
		 * Add commissions table template into "Admin New Order" email
		 *
		 * @param $order         \WC_Order Current order
		 * @param $sent_to_admin bool Whether email is sent to admin or not
		 * @param $plain_text    bool Whether email has HTML content or plain text content
		 * @param $email         \WC_Email Current email object
		 *
		 * @return void
		 * @since 1.1.1
		 */
		public function add_mail_commission_table( $order, $sent_to_admin, $plain_text, $email = false ) {
			if ( ! $sent_to_admin || ! $email || ! isset( $email->id ) || $email->id != 'new_order' ) {
				return;
			}

			$token = yit_get_prop( $order, '_yith_wcaf_referral', true );

			if ( ! $token ) {
				return;
			}

			$commissions = $this->get_commissions( array(
				'order_id' => yit_get_order_id( $order )
			) );

			if ( ! $commissions ) {
				return;
			}

			$subsection = $plain_text ? '/plain' : '';

			yith_wcaf_get_template( 'commissions-table.php', array(
				'commissions' => $commissions,
				'order'       => $order,
				'token'       => $token
			), 'emails' . $subsection );
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAF_Commission_Handler_Premium
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
 * Unique access to instance of YITH_WCAF_Commission_Handler_Premium class
 *
 * @return \YITH_WCAF_Commission_Handler_Premium
 * @since 1.0.0
 */
function YITH_WCAF_Commission_Handler_Premium() {
	return YITH_WCAF_Commission_Handler_Premium::get_instance();
}
