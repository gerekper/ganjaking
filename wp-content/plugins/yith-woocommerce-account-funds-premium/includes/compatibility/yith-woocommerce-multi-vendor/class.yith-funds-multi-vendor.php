<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'YITH_FUNDS_MultiVendor' ) ) {

	class YITH_FUNDS_MultiVendor {

		public function __construct() {

			add_action( 'yith_wcmv_suborder_created', array( $this, 'add_funds_in_sub_orders' ), 20, 3 );
			add_filter( 'yith_account_funds_show_order_metabox', array( $this, 'show_fund_editor_metabox' ), 10, 3 );
			add_filter( 'ywf_operation_type', array( $this, 'add_commission_type' ), 10, 1 );

			$vendor_can_redeem      = get_option( 'ywf_vendor_can_redeem', 'no' );
			$payment_method_enabled = ( ( $this->is_paypal_payouts_enabled() && $this->is_paypal_payouts_method() ) ||
			                            ( $this->is_stripe_connect_enabled() && $this->is_stripe_connect_method() ) );
			if ( 'yes' == $vendor_can_redeem && $payment_method_enabled ) {

				$redeem_slug = yith_account_funds_get_endpoint_slug( 'redeem-funds' );

				add_filter( 'yith_account_funds_menu_items', array( $this, 'add_redeem_endpoint' ), 10, 1 );
				add_action( 'woocommerce_account_' . $redeem_slug . '_endpoint', array(
					$this,
					'show_redeem_funds_endpoint'
				) );

				add_filter( 'woocommerce_endpoint_' . $redeem_slug . '_title', array(
					$this,
					'show_redeem_funds_endpoint_title'
				) );

				add_action( 'wp_enqueue_scripts', array( $this, 'include_button_style' ), 20 );

				add_action( 'wp_loaded', array( $this, 'redeem_funds_handler' ), 25 );
				add_action( 'wc_ajax_redeem_funds', array( $this, 'redeem_funds_ajax' ) );


				//manage Payouts item table, to show right information
				if ( $this->is_paypal_payouts_method() ) {
					add_filter( 'yith_payouts_payment_mode', array(
						$this,
						'add_payouts_redeem_funds_payment_mode'
					), 15, 1 );
					add_filter( 'yith_paypal_payout_redeem_funds_items_html', array(
						$this,
						'get_redeem_funds_items_html'
					), 10, 4 );
					add_filter( 'yith_payouts_items_columns', array( $this, 'redeem_funds_column_name' ), 10, 1 );
				}
				//Automatic redeem funds
				add_action( 'yith_commission_status_paid', array( $this, 'redeem_funds_on_threshold' ), 20, 1 );


				add_action( 'wp_loaded', array( $this, 'redeem_funds_setup_schedule' ) );
				add_action( 'update_option_ywf_redeeming_payment_type', array(
					$this,
					'redeem_funds_delete_schedule'
				) );
				add_action( 'ywf_redeem_funds_action_schedule', array( $this, 'redeem_funds_month_cron' ) );

				add_action( 'yith_funds_after_redeem_funds', array( $this, 'prepare_to_send_admin_email' ), 10, 1 );
			}


			add_filter('yith_account_funds_menu_items', array( $this, 'vendor_menu_funds' ), 20 , 1 );
			add_filter('yith_funds_user_is_available', array( $this, 'vendor_can_deposit_funds' ), 20  );
			add_filter('yith_fund_product_is_purchasable', array( $this, 'funds_product_is_purchasable' ), 20,1  );
			add_filter('ywf_is_available_fund_gateway', array( $this, 'vendor_can_use_funds' ), 20 , 1 );
			add_filter('woocommerce_email_enabled_yith_user_funds_email', array( $this, 'enable_charge_funds_email_for_vendor'), 20, 2 );
		}

		/**
		 * @param int $suborder_id
		 * @param int $parent_order_id
		 * @param int $vendor_id
		 */
		public function add_funds_in_sub_orders( $suborder_id, $parent_order_id, $vendor_id ) {

			$parent_order   = wc_get_order( $parent_order_id );
			$order_total    = $parent_order->get_total( 'edit' );
			$suborder       = wc_get_order( $suborder_id );
			$suborder_total = $suborder->get_total();


			$partial_payment    = $parent_order->get_meta( 'ywf_partial_payment' );
			$funds_used         = $parent_order->get_meta( '_order_funds' );
			$order_fund_removed = $parent_order->get_meta( '_order_fund_removed' );


			if ( ! empty( $funds_used ) ) {

				if ( 'yes' == $partial_payment ) {
					$suborder->update_meta_data( 'ywf_partial_payment', 'yes' );
				}


				$suborder_funds = round( ( $funds_used * $suborder_total ) / ( $order_total + $funds_used ), 2 );

				$suborder->update_meta_data( '_order_funds', $suborder_funds );

				$suborder->set_total( $suborder_total - $suborder_funds );
				$suborder->update_meta_data( '_order_fund_removed', $order_fund_removed );

				$suborder->save();
			}
		}

		/**
		 * @param bool $show
		 * @param string $is_partial
		 * @param WC_Order $order
		 */
		public function show_fund_editor_metabox( $show, $is_partial, $order ) {

			$vendor_can_refund = get_option( 'yith_wpv_vendors_option_order_refund_synchronization' );
			$vendor_can_refund = 'yes' == $vendor_can_refund;
			$order_id          = $order->get_id();

			$vendor = yith_get_vendor( 'current', 'user' );
			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				if ( wp_get_post_parent_id( $order_id ) != 0 ) {

					if ( 'yes' == $is_partial && ! $vendor_can_refund ) {
						$show = false;
					}
				}
			}

			return $show;
		}

		/**
		 * @param array $operations
		 *
		 * @return array
		 */
		public function add_commission_type( $operations ) {

			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				$operations['commission']        = __( 'Commission', 'yith-woocommerce-account-funds' );
				$operations['commission_refund'] = __( 'Commission Refunded', 'yith-woocommerce-account-funds' );

			}

			return $operations;
		}

		public function add_redeem_endpoint( $menu_items ) {
			$current_user_id = get_current_user_id();
			$vendor          = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() && $vendor->is_owner( $current_user_id ) ) {

				$slug_redeem_fund                = yith_account_funds_get_endpoint_slug( 'redeem-funds' );
				$redeem_fund_name                = yith_account_funds_get_endpoint_title( 'redeem-funds' );
				$menu_items[ $slug_redeem_fund ] = $redeem_fund_name;
			}

			return $menu_items;
		}

		public function show_redeem_funds_endpoint() {

			wc_get_template( 'redeem-funds.php', array(), YITH_FUNDS_TEMPLATE_PATH, YITH_FUNDS_TEMPLATE_PATH );
		}

		public function show_redeem_funds_endpoint_title() {

			return yith_account_funds_get_endpoint_title( 'redeem-funds' );
		}

		public function include_button_style() {

			$text_color       = get_option( 'ywf_redeeming_button_text_color', array(
				'color'       => '#333333',
				'hover_color' => '#333333'
			) );
			$background_color = get_option( 'ywf_redeeming_button_background_color', array(
				'color'       => '#eeeeee',
				'hover_color' => '#d5d5d5'
			) );
			$css              = '#yith_redeem_button {
							color : ' . $text_color['color'] . ';
							background: ' . $background_color['color'] . ';
						}
						
						#yith_redeem_button:hover{
						color : ' . $text_color['hover_color'] . ';
						background: ' . $background_color['hover_color'] . ';
					}';

			wp_add_inline_style( 'ywf_style', $css );

			$params = array(
				'ajax_url'     => WC_AJAX::get_endpoint( 'redeem-funds' ),
				'redirect_url' => wc_get_endpoint_url( yith_account_funds_get_endpoint_slug( 'redeem-funds' ), 'redeem_made' ),
				'actions'      => array(
					'redeem_funds' => 'redeem_funds'
				)
			);


			wp_enqueue_script( 'ywf_redeem_funds', YITH_FUNDS_ASSETS_URL . 'js/' . yit_load_js_file( 'ywf-redeem-funds.js' ), array( 'jquery' ), YITH_FUNDS_VERSION );

			wp_localize_script( 'ywf_redeem_funds', 'ywf_redeem_funds_args', $params );

		}

		public function redeem_funds_handler() {

			if ( isset( $_POST['yith_redeem_funds'] ) ) {

				$redeem_amount = $_POST['yith_redeem_funds'];

				$this->redeem_funds( $redeem_amount );
			}
		}

		public function redeem_funds_ajax() {

			if ( isset( $_REQUEST['yith_redeem_funds'] ) ) {
				$redeem_amount = $_POST['yith_redeem_funds'];
				$this->redeem_funds( $redeem_amount );
				wp_die( 0 );
			}
		}

		/**
		 * @param $redeem_amount
		 * @param $user_id
		 *
		 * @throws Exception
		 */
		public function redeem( $redeem_amount, $user_id ) {


			try {
				$vendor = yith_get_vendor( $user_id, 'user' );
				if ( $vendor->is_valid() && $vendor->is_owner() ) {
					$args = array(
						'user_id'  => $user_id,
						'currency' => get_option( 'woocommerce_currency' ),
						'amount'   => $redeem_amount
					);

					if ( $this->is_paypal_payouts_enabled() && $this->is_paypal_payouts_method() ) {
						$paypal_email = $vendor->paypal_email;
						if ( ! empty( $paypal_email ) ) {
							$args['paypal_email'] = $paypal_email;
							$results              = YITH_Redeem_Funds_with_Payouts()->redeem( array( $args ) );
						} else {
							throw new Exception( __( 'Impossible to process this request without the PayPal Email set for this Vendor', 'yith-woocommerce-account-funds' ) );
						}
					} elseif ( $this->is_stripe_connect_enabled() && $this->is_stripe_connect_method() ) {

						$stripe_connect_id = get_user_meta( $vendor->get_owner(), 'stripe_user_id', true );

						if ( ! empty( $stripe_connect_id ) ) {
							$args['stripe_user_id'] = $stripe_connect_id;
							$results                = YITH_Redeem_Funds_with_Stripe_Connect()->redeem( array( $args ) );
							if ( count( $results ) > 0 ) {
								throw  new Exception( implode( ',', $results ) );
							}
						} else {
							throw new Exception( __( 'The vendor\'s profile is not connected to Stripe.', 'yith-woocommerce-account-funds' ) );
						}

					}

				}
			} catch ( Exception $e ) {
				wc_add_notice( $e->getMessage(), 'error' );
			}

		}

		/**
		 * @param $redeem_amount
		 */
		public function redeem_funds( $redeem_amount ) {

			try {

				$nonce_value = wc_get_var( $_REQUEST['yith-account-funds-redeem-funds-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

				if ( empty( $nonce_value ) || ! wp_verify_nonce( $nonce_value, 'yith_account_funds-redeem_funds' ) ) {

					throw new Exception( __( 'We were unable to process your request, please try again.', 'yith-woocommerce-account-funds' ) );
				}

				$min_to_redeem    = get_option( 'ywf_min_fund_needs', 50 );
				$max_to_redeem    = get_option( 'ywf_max_fund_redeem', '' );
				$user_id          = get_current_user_id();
				$customer         = new YITH_YWF_Customer( $user_id );
				$funds            = $customer->get_funds();
				$default_currency = get_option( 'woocommerce_currency' );

				if ( $funds < $redeem_amount ) {
					throw  new Exception( __( 'Invalid amount', 'yith-woocommerce-account-funds' ) );

				}
				if ( $redeem_amount < $min_to_redeem ) {

					throw  new Exception( sprintf( __( 'You can\'t redeem funds because you need at least %s in your balance', 'yith-woocommerce-account-funds' ), wc_price( $min_to_redeem, array( 'currency' => $default_currency ) ) ) );
				}

				if ( ! empty( $max_to_redeem ) && $redeem_amount > $max_to_redeem ) {
					throw  new Exception( sprintf( __( 'You can redeem at most %s', 'yith-woocommerce-account-funds' ), wc_price( $max_to_redeem, array( 'currency' => $default_currency ) ) ) );

				}

				$this->redeem( $redeem_amount, $user_id );


			} catch ( Exception $e ) {
				wc_add_notice( $e->getMessage(), 'error' );
			}

			$this->send_ajax_failure_response();

		}

		/**
		 * If checkout failed during an AJAX call, send failure response.
		 */
		protected function send_ajax_failure_response() {
			if ( is_ajax() ) {

				$messages = wc_print_notices( true );

				$response = array(
					'result'  => 	wc_notice_count('error') > 0 ? 'failure' : 'success',
					'message' => isset( $messages ) ? $messages : '',
				);


				wp_send_json( $response );
			}
		}

		/**
		 * check if possible redeem vendor funds if the threshold is reached, when a commission is paid
		 *
		 * @param $commission_id
		 *
		 * @throws Exception
		 * @author YITH
		 */
		public function redeem_funds_on_threshold( $commission_id ) {

			$automatic_redeem_mode = get_option( 'ywf_redeeming_payment_type', 'none' );

			if ( 'automatic' == $automatic_redeem_mode ) {
				$commission = new YITH_Commission( $commission_id );

				$vendor = $commission->get_vendor();

				if ( $vendor->is_valid() ) {
					$user     = $commission->get_user();
					$customer = new YITH_YWF_Customer( $user->ID );

					$funds         = $customer->get_funds();
					$min_to_redeem = get_option( 'ywf_min_fund_needs', 50 );
					$max_to_redeem = get_option( 'ywf_max_fund_redeem', '' );


					if ( $funds >= $min_to_redeem ) {

						if ( ! empty( $max_to_redeem ) && $funds > $max_to_redeem ) {
							$funds = $max_to_redeem;
						}


						$args = array(
							'user_id'  => $user->ID,
							'amount'   => $funds,
							'currency' => get_option( 'woocommerce_currency' )
						);
						if ( $this->is_paypal_payouts_enabled() && $this->is_paypal_payouts_method() ) {

							$paypal_email = $vendor->paypal_email;

							if ( ! empty( $paypal_email ) ) {
								$args['paypal_email'] = $paypal_email;
								YITH_Redeem_Funds_with_Payouts()->redeem( array( $args ) );
							}
						} elseif ( $this->is_stripe_connect_enabled() && $this->is_stripe_connect_method() ) {
							$stripe_connect_id = get_user_meta( $vendor->get_owner(), 'stripe_user_id', true );
							if ( ! empty( $stripe_connect_id ) ) {
								$args['stripe_user_id'] = $stripe_connect_id;
								$results                = YITH_Redeem_Funds_with_Stripe_Connect()->redeem( array( $args ) );
							}
						}

					}
				}
			}
		}


		public function redeem_funds_setup_schedule() {

			$automatic_redeem_mode = get_option( 'ywf_redeeming_payment_type', 'none' );

			if ( 'automatic_with_date' == $automatic_redeem_mode && ! wp_next_scheduled( 'ywf_redeem_funds_action_schedule' ) ) {
				wp_schedule_event( time(), 'daily', 'ywf_redeem_funds_action_schedule' );
			}
		}

		public function redeem_funds_delete_schedule() {

			wp_clear_scheduled_hook( 'ywf_redeem_funds_action_schedule' );
		}

		/**
		 * @throws Exception
		 */
		public function redeem_funds_month_cron() {

			$automatic_redeem_mode = get_option( 'ywf_redeeming_payment_type', 'none' );

			if ( 'automatic_with_date' == $automatic_redeem_mode ) {

				$today               = date( 'j' ); //get the current day
				$how_days_this_month = date( 't' );//get the number days of current month
				$day_to_check        = get_option( 'ywf_redeeming_day', 15 );

				//manage exception for February and for months with 30 days
				$day_to_check = $day_to_check > $how_days_this_month ? $how_days_this_month : $day_to_check;

				if ( $today == $day_to_check ) {
					$vendors       = YITH_Vendors()->get_vendors();
					$vendor_data   = array();
					$min_to_redeem = get_option( 'ywf_min_fund_needs', 50 );
					$max_to_redeem = get_option( 'ywf_max_fund_redeem', '' );
					/**
					 * @var $vendor YITH_Vendor
					 */
					foreach ( $vendors as $vendor ) {
						$user_id  = $vendor->get_owner();
						$customer = new YITH_YWF_Customer( $user_id );
						$funds    = $customer->get_funds();

						if ( $funds >= $min_to_redeem ) {

							if ( ! empty( $max_to_redeem ) && $funds > $max_to_redeem ) {
								$funds = $max_to_redeem;
							}

							$args = array(
								'user_id'  => $user_id,
								'amount'   => $funds,
								'currency' => get_option( 'woocommerce_currency' )
							);
							if ( $this->is_paypal_payouts_enabled() && $this->is_paypal_payouts_method() ) {

								$paypal_email = $vendor->paypal_email;

								if ( ! empty( $paypal_email ) ) {
									$args['paypal_email'] = $paypal_email;
								}
							} elseif ( $this->is_stripe_connect_enabled() && $this->is_stripe_connect_method() ) {
								$stripe_connect_id = get_user_meta( $vendor->get_owner(), 'stripe_user_id', true );

								if ( ! empty( $stripe_connect_id ) ) {
									$args['stripe_user_id'] = $stripe_connect_id;
								}
							}
							$vendor_data[] = $args;
						}
					}

					if ( $this->is_paypal_payouts_enabled() && $this->is_paypal_payouts_method() ) {
						YITH_Redeem_Funds_with_Payouts()->redeem( $vendor_data );
					} elseif ( $this->is_stripe_connect_enabled() && $this->is_stripe_connect_method() ) {

						YITH_Redeem_Funds_with_Stripe_Connect()->redeem( $vendor_data );
					}
				}
		}

	}

	/**
	 * check if is Payouts the payment method selected
	 * @return bool
	 * @since 1.4.0
	 * @author YITH
	 */
	public function is_paypal_payouts_method() {

		return 'yith_payout' == get_option( 'ywf_redeeming_gateway', 'none' );
	}

	/**
	 * check if Stripe Connect the payment method selected
	 * @return bool
	 * @since 1.4.0
	 * @author YITH
	 */
	public	function is_stripe_connect_method() {
		return 'yith_stripe_connect' == get_option( 'ywf_redeeming_gateway', 'none' ) && version_compare( '2.0.4', YITH_WCSC_VERSION, '>=' );
	}

	/**
	 * check if YITH PayPal Payouts plugin is activated
	 * @return bool
	 * @since 1.4.0
	 * @author YITH
	 */
	public 	function is_paypal_payouts_enabled() {

		return defined( 'YITH_PAYOUTS_PREMIUM' ) && version_compare( '1.0.12', YITH_PAYOUTS_VERSION, '>=' );
	}

	/**
	 * check if YITH Stripe Connect plugin is activated
	 * @return bool
	 * @since 1.4.0
	 * @author YITH
	 */
	public function is_stripe_connect_enabled() {
		return defined( 'YITH_WCSC_PREMIUM' );
	}

	/**
	 * @param array $payment_mode
	 *
	 * @return array
	 */
	public	function add_payouts_redeem_funds_payment_mode( $payment_mode ) {

		if ( ! isset( $payment_mode['redeem_funds'] ) ) {

			$payment_mode ['redeem_funds'] = __( 'Redeem Vendor Funds', 'yith-paypal-payouts-for-woocommerce' );
		}


		return $payment_mode;
	}

	public function get_redeem_funds_items_html( $items, $sender_batch_id, $payout, $payout_item ) {

		$args    = array(
			'sender_batch_id' => $sender_batch_id,
			'fields'          => array( 'sender_item_id', 'amount', 'fee' ),
		);
		$results = YITH_Payout_Items()->get_payout_items( $args );
		if ( count( $results ) > 0 ) {
			$total     = 0;
			$total_fee = 0;
			$href_text = _x( 'Redeem Funds Payment for %s', 'Redeem Funds Payment for Jon Doe', 'yith-woocommerce-account-funds' );

			$vendor_url = admin_url( 'term.php' );
			$url_args   = array(
				'taxonomy' => 'yith_shop_vendor',
				'tag_ID'   => ''
			);

			foreach ( $results as $result ) {

				$user_id                   = str_replace( 'redeem_funds_vendor_', '', $result['sender_item_id'] );
				$vendor                    = yith_get_vendor( $user_id, 'user' );
				$redeem_total              = $result['amount'];
				$fee                       = isset( $result['fee'] ) ? $result['fee'] : 0;
				$total_fee                 += $fee;
				$total                     += $redeem_total;
				$affiliate_formatted_total = wc_price( $redeem_total );

				$url_args['tag_ID'] = $vendor->id;
				$vendor_url         = esc_url( add_query_arg( $url_args, $vendor_url ) );

				$user_info = sprintf( '<a href="%s" target="__blank">%s</a>', $vendor_url, $vendor->name );
				$user_info = sprintf( $href_text, $user_info );
				$items     .= '<tr class="wc-order-preview-table__item wc-order-preview-table__item--receiver-commission">
                                                <td class="wc-order-preview-table__column--order_commission">' . $user_info . '
                                                    <p><small>' . $vendor->paypal_email . '</small></p>
                                                </td>	
                                                <td class="wc-order-preview-table__column--total">' . $affiliate_formatted_total . '</td>	
								            </tr>';

			}

			$new_formatted_fee = wc_price( $total_fee );
			$items             .= '<tr class="wc-order-preview-table__item wc-order-preview-table__item--net-total">
								<td class="wc-order-preview-table__column--order_commission"><strong>' . __( 'Fee', 'yith-paypal-payouts-for-woocommerce' ) . '</strong></td>	
								<td class="wc-order-preview-table__column--total">' . $new_formatted_fee . '</td>	
								</tr>';

			$new_formatted_total = wc_price( $total + $total_fee );
			$items               .= '<tr class="wc-order-preview-table__item wc-order-preview-table__item--net-total">
								<td class="wc-order-preview-table__column--order_commission"><strong>' . __( 'Total transaction', 'yith-paypal-payouts-for-woocommerce' ) . '</strong></td>	
								<td class="wc-order-preview-table__column--total">' . $new_formatted_total . '</td>	
								</tr>';
		}

		return $items;
	}

	public	function redeem_funds_column_name( $columns ) {

		$columns['order'] = _x( 'Redeem Funds','Is the title of PayPal Payout detail', 'yith-woocommerce-account-funds' );

		return $columns;
	}

	public	function prepare_to_send_admin_email( $vendor_data ) {

		WC()->mailer();

		do_action( 'ywf_vendor_redeem_email_notification', $vendor_data );
	}

	public function vendor_menu_funds( $menu ){

		if( !$this->vendor_can_deposit_funds() ){

			$make_slug = yith_account_funds_get_endpoint_slug('make-a-deposit');

			if( isset( $menu[$make_slug] ) ){
				unset( $menu[$make_slug]);
			}
		}
		return $menu;
	}

	public function vendor_can_deposit_funds(){
		$vendor = yith_get_vendor('current','user');
		$can_make_deposit = get_option( 'ywf_vendor_can_charge', 'no');

		return  !$vendor->is_valid() || (( $vendor->is_valid() && $vendor->is_owner() ) && 'yes' == $can_make_deposit ) ;
	}

	public function vendor_can_use_funds( $can_use ){

		$vendor = yith_get_vendor('current','user');

		if( $vendor->is_valid() && $vendor->is_owner() && 'no' == get_option('ywf_vendor_can_use','no')){
			$can_use = false;
		}
		return $can_use;
	}

		/**
		 * @param bool $is_enabled
		 * @param WP_User $user
		 * @return bool
		 */
	public function enable_charge_funds_email_for_vendor( $is_enabled, $user ){

		if( $user instanceof  WP_User ) {
			$vendor = yith_get_vendor($user->ID,'user');

			$can_make_deposit = get_option( 'ywf_vendor_can_charge', 'no');

			if ( $vendor->is_valid() && $vendor->is_owner($user->ID) && 'no' == $can_make_deposit ) {
				$is_enabled = false;
			}
		}
		return  $is_enabled;
	}

		/**check if the funds product is purchasable for vendors
		 * @author YITH
		 * @since 1.4.0
		 * @param $is_purchasable
		 * @return bool
		 */
	public function funds_product_is_purchasable( $is_purchasable ){

		$vendor = yith_get_vendor('current','user');
		$can_make_deposit = get_option( 'ywf_vendor_can_charge', 'no');

		if( $vendor->is_valid() && $vendor->is_owner() && 'no' == $can_make_deposit ){
			$is_purchasable = false;
		}
		return $is_purchasable;
	}
}
}

new YITH_FUNDS_MultiVendor();