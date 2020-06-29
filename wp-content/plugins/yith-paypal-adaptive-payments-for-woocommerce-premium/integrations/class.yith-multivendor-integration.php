<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'YITH_PayPal_Adaptive_Payments_Multivendor_Integration' ) ) {

	class YITH_PayPal_Adaptive_Payments_Multivendor_Integration {

		public function __construct() {

		    if( apply_filters( 'yith_deprecated_paypal_service_support', false ) ){
			    $this->enable_addon = get_option( 'yith_wcmv_enable_paypal-masspay_gateway', 'yes' );
			    $this->gateway_option = get_option( 'payment_gateway', true );

			    add_filter( 'yith_wcmv_payments_gateway', array( $this, 'add_gateway' ) );
			    add_filter( 'yith_wcqw_panel_payments_options', array( $this, 'change_option_args' ) );
			    add_filter( 'yith_wcmv_paypal_gateways_options', array( $this, 'change_option_args' ) );
			    add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 15 );

			    add_filter( 'yith_wcmv_external_gateway_yith_paypal_adaptive_payments', array( $this, 'add_gateway_to_multi_vendor' ) );
			    add_filter( 'yith_wcmv_show_enabled_gateways_table', array( $this, 'add_gateway_to_list_table' ) );
			    add_filter( 'yith_wcmv_is_yith_paypal_adaptive_payments_gateway_enabled', array( $this, 'is_available' ) );
			    add_filter( 'yith_wcmv_yith_paypal_adaptive_payments_options_admin_url', array( $this, 'get_options_admin_url' ) );


			    add_filter( 'pre_option_yith_wpv_vendors_option_order_management', array(
				    $this,
				    'enable_order_management'
			    ), 10, 2 );
			    add_filter( 'pre_option_yith_wpv_vendors_option_order_synchronization', array(
				    $this,
				    'enable_order_management'
			    ), 10, 2 );



			    add_action( 'woocommerce_admin_field_yith_padp_text', array(
				    $this,
				    'show_payment_adaptive_text'
			    ), 15 );

			    if ( $this->check_multi_vendor_version() && 'yes' == $this->enable_addon && 'adaptive' == $this->gateway_option ) {
				    add_filter( 'yith_padp_add_tab', array( $this, 'remove_receiver_tab' ), 10, 1 );
				    //Enable the multivendor mode for build receivers list

				    add_filter( 'yith_paypal_adaptive_payment_custom_build_receivers', array(
					    $this,
					    'custom_build_receivers'
				    ), 10, 4 );
				    add_action( 'yith_paypal_adaptive_payments_after_process_ipn', array(
					    $this,
					    'process_order_commissions'
				    ), 10, 3 );
				    add_action( 'yith_paypal_adaptive_payments_after_pay_secondary_receiver', array(
					    $this,
					    'process_commissions_secondary_receiver'
				    ), 10, 2 );
				    add_filter( 'yith_paypal_adaptive_payments_get_transactions', array(
					    $this,
					    'get_receiver_commissions'
				    ), 10, 5 );
				    add_filter( 'yith_paypal_adaptive_payments_count_transaction', array(
					    $this,
					    'count_transaction'
				    ), 10, 2 );
				    add_filter( 'yith_paypal_adaptive_payments_show_gateway_notices', array(
					    $this,
					    'show_gateway_notices'
				    ), 10, 1 );
				    add_action( 'yith_paypal_adaptive_payments_show_other_notices', array(
					    $this,
					    'show_gateway_other_notices'
				    ) );
				    add_filter( 'yith_paypal_adaptive_payments_has_receivers', array(
					    $this,
					    'order_has_commissions'
				    ), 10, 1 );
				    add_filter( 'get_terms', array( $this, 'filter_vendor_with_empty_paypal_field' ), 10, 4 );
			    }

			    add_filter( 'yith_paypal_adaptive_payments_is_available', array( $this, 'is_available' ) );

			    add_action( 'init', array( $this, 'my_account_integration' ), 20 );
            }
		}

		public function my_account_integration(){
			$vendor = yith_get_vendor( 'current', 'user' );
			if( ! $vendor->is_valid() && ! current_user_can( 'administrator' ) ){
				$endpoint = yith_paypal_adaptive_payments_receivers_get_endpoint();
				$YITH_PADP_Receivers = YITH_PADP_Receivers();

				remove_action( 'woocommerce_account_'.$endpoint.'_endpoint', array( $YITH_PADP_Receivers, 'show_user_commission' ) );
				remove_filter( 'woocommerce_endpoint_' . $endpoint. '_title',  array( $YITH_PADP_Receivers, 'endpoint_title' ), 10 );
				remove_filter( 'woocommerce_account_menu_items', array( $YITH_PADP_Receivers, 'receiver_commission_menu_items' ) );

				//Compatibility with YITH Themes
				remove_action('yith_myaccount_menu', array( $YITH_PADP_Receivers, 'add_myaccount_menu' ) );
				remove_filter('yit_get_myaccount_menu_icon_list', array( $YITH_PADP_Receivers, 'receiver_account_menu_icon_list' ) );
				remove_filter('yit_get_myaccount_menu_icon_list_fa', array( $YITH_PADP_Receivers, 'receiver_account_menu_icon_list_fa' ) );
			}
        }


		public function check_multi_vendor_version() {
			return ( defined( 'YITH_WPV_VERSION' ) && version_compare( YITH_WPV_VERSION, '1.9.18', '>=' ) );
		}

		/**
		 * force order management system
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param bool $value
		 * @param string $option
		 *
		 * @return string
		 */
		public function enable_order_management( $value, $option ) {
			return 'yes';
		}

		/**
		 * Change the gateway display id
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 1.0.0
		 *
		 * @param string $gateway_id
		 *
		 * @return string filtered gateway id
		 */
		public function add_gateway_to_list_table( $gateways ){
		    $id = YITH_Paypal_Adaptive_Payments_Gateway()->id;
		    $gateways[ $id ] = 'YITH_Paypal_Adaptive_Payments_Gateway';
		    return $gateways;
        }

        public function get_options_admin_url( $admin_url ){
		    $gateway = function_exists( 'YITH_Vendors_Gateway' ) ? YITH_Vendors_Gateway( 'paypal-masspay' ) : false;
	        $admin_url = ! empty( $gateway ) ? admin_url( 'admin.php?page=yith_wpv_panel&tab=gateways&section=' . strtolower( $gateway->get_id() ) ) : $admin_url;
	        return $admin_url;
        }

		/**
		 *
		 */
		public function add_gateway_to_multi_vendor( $gateway ){
		    return YITH_Paypal_Adaptive_Payments_Gateway();
        }

		/**
		 * check if PayPal Adaptive Payments Gateway is available for MultiVendor 1.9.18
		 *
		 * @param bool $is_available
		 *
		 * @return bool
		 */
		public function is_available( $is_available ) {

			$gateway_option = get_option( 'payment_gateway' );

			return $is_available && 'adaptive' == $gateway_option;
		}

		/**
		 * unset the General Settings tab
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param array $tabs
		 *
		 * @return array
		 */
		public function remove_receiver_tab( $tabs ) {

			unset( $tabs['general-settings'] );

			return $tabs;
		}

		/**
		 * build the receivers array with the vendor commission
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param array $receiver_list
		 * @param WC_Order $order
		 * @param string $pay_method
		 * @param string $email_primary_receiver
		 *
		 * @return mixed
		 */
		public function custom_build_receivers( $receiver_list, $order, $pay_method, $email_primary_receiver ) {

			$tot_commission = 0;

			$receiver_list = array();

			$order_id       = yit_get_order_id( $order );
			$payment_method = $order->get_payment_method();
			$suborders      = YITH_Vendors()->orders->get_suborder( $order_id );


			if ( 'yith_paypal_adaptive_payments' == $payment_method && count( $suborders ) > 0 ) {

				foreach ( $suborders as $sub_order_id ) {

					$commissions_ids = $this->get_commission_id( array(
						'order_id' => $sub_order_id,
						'status'   => array(
							'unpaid',
							'processing',
							'pending'
						)
					) );

					if ( count( $commissions_ids ) > 0 ) {

						foreach ( $commissions_ids as $commission_id ) {
							/**
							 * @var YITH_Commission $commission
							 */
							$commission       = YITH_Commission( $commission_id );
							$vendor           = $commission->get_vendor();
							$email            = $vendor->paypal_email;
							$commission_value = round( $commission->get_amount(), 2 );


							if ( $vendor->is_valid() && ! empty( $email ) ) {

								if ( ! isset( $receiver_list[ $email ] ) ) {

									$receiver_list[ $email ] = array(
										'email'  => $email,
										'amount' => number_format( $commission_value, 2, '.', '' )
									);

									if ( 'chained' == $pay_method ) {

										$receiver_list[ $email ]['primary'] = false;
									}
								} else {

									$receiver_list[ $email ]['amount'] = number_format( $receiver_list[ $email ]['amount'] + $commission_value, 2, '.', '' );
								}

								$tot_commission += $commission_value;
							}
						}
					}
				}
			}


			return array( 'tot_commission' => $tot_commission, 'receivers' => $receiver_list );
		}

		/**
		 * process the vendors commissions after paypal payment
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param WC_Order $order
		 * @param array $payment_details
		 * @param string $primary_receiver_email
		 *
		 */
		public function process_order_commissions( $order, $payment_details, $primary_receiver_email ) {

			$payment_info = empty( $payment_details['paymentInfoList']['paymentInfo'] ) ? array() : $payment_details['paymentInfoList']['paymentInfo'];

			if ( count( $payment_info ) > 0 ) {

				foreach ( $payment_info as $info ) {

					$transaction_status = empty( $info['transactionStatus'] ) ? false : $info['transactionStatus'];

					if ( $transaction_status ) {

						$info_email = trim( $info['receiver']['email'] );

						//if the receiver is the primary receiver
						if ( $info_email == $primary_receiver_email ) {

							/**
							 * TODO some actions for primary receivers?
							 */
						} else {
							$this->process_commissions( $order, $info );
						}
					}
				}
			}
		}

		/**
		 * process the vendor commissions after paypal payment
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param WC_Order $order
		 * @param array $info
		 */
		public function process_commissions( $order, $info ) {

			$email    = trim( $info['receiver']['email'] );
			$receiver = $this->get_vendor_by_term_value( 'paypal_email', $email );

			if ( $receiver ) {

				$vendor = yith_get_vendor( $receiver, 'vendor' );

				if ( $vendor->is_valid() ) {

					$commission_transaction = $info['transactionStatus'];
					$order_id               = yit_get_prop( $order, 'id' );
					$payment_method         = $order->get_payment_method();

					if ( 'yith_paypal_adaptive_payments' == $payment_method ) {

						$suborders = YITH_Vendors()->orders->get_suborder( $order_id );


						if ( count( $suborders ) > 0 ) {

							$args = array(
								'status' => array( 'unpaid', 'processing', 'pending' ),

							);
							foreach ( $suborders as $suborder_id ) {

								$args['order_id'] = $suborder_id;
								$commission_ids   = $this->get_commission_id( $args );

								if ( count( $commission_ids ) > 0 ) {
									switch ( $commission_transaction ) {

										case 'COMPLETED':

											$message = __( 'Commission was paid successfully', 'yith-paypal-adaptive-payments-for-woocommerce' );
											$this->update_comissions_status( $commission_ids, 'paid', $message );
											break;
										case 'PENDING':
											$message = __( 'Commission is awaiting further processing', 'yith-paypal-adaptive-payments-for-woocommerce' );
											$this->update_comissions_status( $commission_ids, 'processing', $message );
											break;
										case 'CREATED':
											$message = __( 'Commission payment request was received; funds will be transferred once approval is received', 'yith-paypal-adaptive-payments-for-woocommerce' );
											$this->update_comissions_status( $commission_ids, 'processing', $message );
											break;
										case 'DENIED':
											$message = __( 'Commission transaction was rejected by the receiver', 'yith-paypal-adaptive-payments-for-woocommerce' );
											$this->update_comissions_status( $commission_ids, 'unpaid', $message );
											break;
										case 'PROCESSING':
											$message = __( 'Commission transaction is in progress', 'yith-paypal-adaptive-payments-for-woocommerce' );
											$this->update_comissions_status( $commission_ids, 'processing', $message );
											break;

										case 'FAILED':
											$message = __( 'Commission payment failed', 'yith-paypal-adaptive-payments-for-woocommerce' );
											$this->update_comissions_status( $commission_ids, 'unpaid', $message );
											break;

									}
								}
							}

						}
					}


				}
			}
		}

		/**
		 *
		 * get vendors by term key
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $term_key
		 * @param $term_value
		 *
		 * @return bool|int
		 */
		public function get_vendor_by_term_value( $term_key, $term_value ) {

			global $wpdb;

			$termmeta_table   = YITH_Vendors()->termmeta_table;
			$termmeta_term_id = YITH_Vendors()->termmeta_term_id;

			$query = $wpdb->prepare( "SELECT {$termmeta_table}.$termmeta_term_id FROM {$termmeta_table} INNER JOIN {$wpdb->term_taxonomy} ON {$termmeta_table}.$termmeta_term_id = {$wpdb->term_taxonomy}.term_id WHERE {$wpdb->term_taxonomy}.taxonomy LIKE '%s' AND {$termmeta_table}.meta_key LIKE '%s' AND {$termmeta_table}.meta_value LIKE '%s'",
				'yith_shop_vendor', $term_key, $term_value );

			$receiver = $wpdb->get_col( $query );

			return ! empty( $receiver[0] ) ? $receiver[0] : false;
		}

		/**
		 * update all vendor commissions status
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param array $commissions
		 * @param string $new_status
		 * @param string $message
		 */
		protected function update_comissions_status( $commissions, $new_status, $message = '' ) {

			if ( count( $commissions ) > 0 ) {

				foreach ( $commissions as $commission_id ) {

					$commission = YITH_Commission( $commission_id );

					$commission->update_status( $new_status, $message );

				}
			}
		}

		/**
		 * get all vendor commission ids
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param int $order_id
		 * @param int $vendor_id
		 *
		 * @return array
		 */
		protected function get_commission_id( $args ) {


			global $wpdb;
			$table = ! empty( $wpdb->commissions ) ? $wpdb->commissions : $wpdb->prefix . YITH_Commissions()->get_commissions_table_name();


			$commissions = YITH_Commissions()->get_commissions( $args );


			return $commissions;
		}

		/**
		 * process the vendor commission when an chained delayed payment is made
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param WC_Order $order
		 * @param string $payment_status
		 */
		public function process_commissions_secondary_receiver( $order, $payment_status ) {

			$order_items    = $order->get_items();
			$order_id       = yit_get_prop( $order, 'id' );
			$payment_method = $order->get_payment_method();

			if ( 'yith_paypal_adaptive_payments' == $payment_method ) {

				$suborders      = YITH_Vendors()->orders->get_suborder( $order_id );
				$commission_ids = array();

				if ( count( $suborders ) > 0 ) {

					$args = array(
						'status' => array( 'unpaid', 'processing', 'pending' )
					);
					foreach ( $suborders as $suborder_id ) {

						$args['order_id'] = $suborder_id;
						$commission_ids[] = $this->get_commission_id( $args );

						if ( count( $commission_ids ) > 0 ) {
							switch ( $payment_status ) {

								case 'COMPLETED' :
									$message = __( 'Commission paid successfully', 'yith-paypal-adaptive-payments-for-woocommerce' );
									$this->update_comissions_status( $commission_ids, 'paid', $message );
									break;
								case 'CREATED' :
									$message = __( 'Commission payment request received; funds will be transferred once approval is received', 'yith-paypal-adaptive-payments-for-woocommerce' );
									$this->update_comissions_status( $commission_ids, 'processing', $message );
									break;
								case 'INCOMPLETE':
									$message = __( 'Some transfers succeeded and some failed for a parallel payment', 'yith-paypal-adaptive-payments-for-woocommerce' );
									$this->update_comissions_status( $commission_ids, 'processing', $message );
									break;
								case 'ERROR':
									$message = __( 'The payment failed and all attempted transfers failed, or all completed transfers were successfully
                            reversed', 'yith-paypal-adaptive-payments-for-woocommerce' );
									$this->update_comissions_status( $commission_ids, 'unpaid', $message );
									break;
								case 'REVERSALERROR':
									$message = __( 'One or more transfers failed when attempting to reverse a payment', 'yith-paypal-adaptive-payments-for-woocommerce' );
									$this->update_comissions_status( $commission_ids, 'unpaid', $message );
									break;
							}
						}
					}

				}
			}
		}

		/**
		 * get all vendor commission ( for orders paid via PayPal Adaptive Payment gateway )
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param array $commission
		 * @param int $user_id
		 * @param string $transaction_status
		 * @param int $offset
		 * @param int $limit
		 *
		 * @return array
		 */
		public function get_receiver_commissions( $commission, $user_id, $transaction_status, $offset, $limit ) {

			$vendor = yith_get_vendor( $user_id, 'user' );

			if ( $vendor->is_valid() ) {

				$args        = array(
					'vendor_id' => $vendor->id,
					'orderby'   => 'last_edit',
					'order'     => 'DESC',
					'status'    => $transaction_status ? $transaction_status : 'all',
				);
				$commissions = YITH_Commissions()->get_commissions( $args );


				$commission = array();

				if ( count( $commissions ) > 0 ) {
					foreach ( $commissions as $commission_id ) {

						$comm  = YITH_Commission( $commission_id );
						$order = $comm->get_order();

						$order_id = yit_get_prop( $order, 'id' );
						$gateway  = get_post_meta( $order_id, '_payment_method', true );

						if ( 'yith_paypal_adaptive_payments' == $gateway ) {

							$single_commission = array(
								'order_id'           => $order_id,
								'transaction_value'  => $comm->get_amount(),
								'transaction_status' => $comm->get_status( 'display' ),
								'transaction_date'   => $comm->get_date()
							);

							$commission[] = $single_commission;
						}
					}
				}
			}

			return $commission;
		}

		/**
		 * @param $count
		 * @param $user_id
		 *
		 * @return int
		 */
		public function count_transaction( $count, $user_id ) {

			$vendor = yith_get_vendor( $user_id, 'user' );

			if ( $vendor->is_valid() ) {

				$args        = array(
					'vendor_id' => $vendor->id,
					'status'    => 'all',
				);
				$commissions = YITH_Commissions()->get_commissions( $args );

				$count = count( $commissions );
			}

			return $count;
		}

		/**
		 * @author YITHEMES
		 * @since 1.0.1
		 *
		 * @param bool $show
		 */
		public function show_gateway_notices( $show ) {
			$current_user_id = get_current_user_id();
			$current_user    = yith_get_vendor( $current_user_id, 'user' );

			if ( ! $current_user->is_super_user() ) {
				return false;
			}

			return $show;
		}

		/**
		 * show custom admin notice for Vendors
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param bool $is_enabled
		 */
		public function show_gateway_other_notices( $is_enabled ) {


			if ( empty( $_COOKIE['yith_adp_hide_notice'] ) && empty( $_GET['paypal_email'] ) ) {
				$current_user   = get_current_user_id();
				$current_vendor = yith_get_vendor( $current_user, 'user' );

				if ( $is_enabled && ( $current_vendor->is_super_user() ) ) {

					$args = array(
						'enabled_selling' => true,
					);

					$vendors = YITH_Vendors()->get_vendors( $args );

					$vendors_url = admin_url( 'edit-tags.php' );

					$vendor_params = array(
						'taxonomy'     => 'yith_shop_vendor',
						'post_type'    => 'product',
						'paypal_email' => 'empty'
					);
					$vendor_url    = add_query_arg( $vendor_params, $vendors_url );
					$error_message = '';
					$fix_message   = '';

					if ( ( ! empty( $_GET['taxonomy'] ) && 'yith_shop_vendor' == $_GET['taxonomy'] ) ) {
						$fix_message = __( 'Filter vendor list to fix', 'yith-paypal-adaptive-payments-for-woocommerce' );
					} else {
						$fix_message = __( 'Go to vendor list to fix', 'yith-paypal-adaptive-payments-for-woocommerce' );
					}
					/**
					 * @var YITH_Vendor $vendor
					 */
					foreach ( $vendors as $vendor ) {

						if ( empty( $vendor->paypal_email ) ) {


							$vendor_error = sprintf( '%s<br/><a href="%s">%s</a>', _x( 'Some vendor PayPal email address is empty.', 'PayPal email for vendor is empty',
								'yith-paypal-adaptive-payments-for-woocommerce' ), $vendor_url, $fix_message );

							$error_message = sprintf( '<div class="yith-paypal-notice notice notice-error is-dismissible"><p><strong>%s:</strong> %s</p></div>', __( 'YITH PayPal Adaptive Payments for WooCommerce is active', 'yith-paypal-adaptive-payments-for-woocommerce' ), $vendor_error );
							break;
						}
					}
					echo $error_message;
				}
			}
		}

		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param bool $has_receivers
		 *
		 * @return bool
		 */
		public function order_has_commissions( $has_receivers ) {

			if ( ! empty( WC()->cart ) ) {

				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

					/*
					 * @var WC_Product $product
					 */

					$product = $values['data'];
					$vendor  = yith_get_vendor( $product, 'product' );

					if ( $vendor->is_valid() ) {

						return true;
					}
				}
			}

			return false;

		}

		/**
		 *
		 */
		public function add_gateway( $gateways ) {
			$gateways['adaptive'] = __( 'Adaptive Payments', 'yith-woocommerce-product-vendors' );

			return $gateways;

		}

		public function admin_enqueue_scripts() {
		    $filename = yit_load_js_file( 'yith_vendor_admin_options.js' );
			wp_register_script( 'yith_padp_vendor_admin', YITH_PAYPAL_ADAPTIVE_ASSETS_URL . 'js/' . $filename, array(
				'jquery',
				'yith-wpv-admin'
			), YITH_PAYPAL_ADAPTIVE_VERSION, true );
			$is_paypal_page = ! empty( $_GET['tab'] ) && 'paypal' == $_GET['tab'];
			$is_paypal_section = ! empty( $_GET['tab'] ) && 'gateways' == $_GET['tab'] && ! empty( $_GET['section'] ) && ( 'paypal' == $_GET['section'] || 'paypal-masspay' == $_GET['section'] );
			if ( ! empty( $_GET['page'] ) && 'yith_wpv_panel' == $_GET['page'] && ( $is_paypal_page || $is_paypal_section ) ) {
				wp_enqueue_script( 'yith_padp_vendor_admin' );
			}
		}

		/**
		 *
		 *
		 */
		public function change_option_args( $options ) {
		    $section_name = 'yith_wcmv_paypal_gateways_options' == current_action() ? 'paypal-masspay' : 'paypal';
			$options[ $section_name ]['payment_gateway']['desc'] = __( 'Choose the PayPal service to pay the commissions to vendors.', 'yith-paypal-adaptive-payments-for-woocommerce' );

			if( ! isset( $options[ $section_name ]['payments_enable_service'] ) ){
				$options[ $section_name ]['payments_enable_service'] = array();
            }

			$first = array(
				'payments_options_start' => $options[ $section_name ]['payments_options_start'],
				'payments_options_title' => $options[ $section_name ]['payments_options_title'],
                'payments_enable_service' => $options[ $section_name ]['payments_enable_service'],
				'payment_gateway'        => $options[ $section_name ]['payment_gateway']
			);

			unset( $options[ $section_name ]['payment_gateway'] );
			unset( $options[ $section_name ]['payments_options_start'] );
			unset( $options[ $section_name ]['payments_options_title'] );
			unset( $options[ $section_name ]['payments_enable_service'] );

			$after = $options[ $section_name ];

			$new = array(
				'payment_adaptive_text' => array(
					'id'   => 'payment_adaptive_text',
					'type' => 'yith_padp_text',
				),
			);

			$options[ $section_name ] = array_merge( $first, $new, $after );

			return $options;
		}

		public function show_payment_adaptive_text( $option ) {

			?>
            <tr valign="top" class="paypal_text" style="display: none;">
                <th scope="row"></th>
                <td class="forminp">
					<?php
					$admin_url = admin_url( 'admin.php' );
					$params    = array(
						'page' => 'yith_paypal_adaptive_payments_panel',
						'tab'  => 'gateway-settings'
					);

					$admin_url = esc_url( add_query_arg( $params ), $admin_url );
					echo sprintf( '<span class="description">%s <a href="%s" target="_blank">%s</a></span>', __( 'You can configure  PayPal Adaptive Payments gateway', 'yith-paypal-adaptive-payments-for-woocommerce' ), $admin_url, __( 'here', 'yith-paypal-adaptive-payments-for-woocommerce' ) );
					?>
                </td>
            </tr>
			<?php
		}

		/**
		 * filter the vendor list
		 *
		 * @param array $terms
		 * @param array $taxonomy
		 * @param string $query_vars
		 * @param string $term_query
		 *
		 * @return mixed
		 */
		public function filter_vendor_with_empty_paypal_field( $terms, $taxonomy, $query_vars, $term_query ) {
			$yith_vendors = function_exists( 'YITH_Vendors' ) ? YITH_Vendors() : null;

			if (  ! empty( $yith_vendors ) && isset( $_GET['paypal_email'] ) && isset( $_GET['taxonomy'] ) && 'empty' === $_GET['paypal_email'] && $yith_vendors->get_taxonomy_name() == $_GET['taxonomy'] ) {
				$temp_terms = $terms;
				foreach ( $temp_terms as $key => $term ) {
				   if( isset( $term->term_id ) ) {
					   $pp_email = $yith_vendors->get_term_meta( $term->term_id, 'paypal_email', true );
					   if ( ! empty( $pp_email ) ) {
						   unset( $terms[ $key ] );
					   }
				   }
				}
			}

			return $terms;
		}

		/**
		 * Disable All Gateway features if the integration plugin is disabled
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @param $available_gateways
		 *
		 * @return mixed
		 */
		public function disable_all( $available_gateways ){
			$gateway_id = YITH_Paypal_Adaptive_Payments_Gateway()->get_id();

			if( isset( $available_gateways[ $gateway_id ] ) ){
				unset( $available_gateways[ $gateway_id ] );
			}

			return $available_gateways;
		}
	}
}
new YITH_PayPal_Adaptive_Payments_Multivendor_Integration();