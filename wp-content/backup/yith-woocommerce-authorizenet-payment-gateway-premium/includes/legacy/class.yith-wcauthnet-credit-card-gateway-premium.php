<?php
/*  Copyright 2013  Your Inspiration Themes  (email : plugins@yithemes.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Gateway class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Authorize.net
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAUTHNET' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAUTHNET_Credit_Card_Gateway_Premium' ) ) {
	/**
	 * WooCommerce Authorize.net gateway class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAUTHNET_Credit_Card_Gateway_Premium extends YITH_WCAUTHNET_Credit_Card_Gateway {

		/**
		 * @const Sandbox payment url
		 */
		const AUTHORIZE_NET_SANDBOX_PAYMENT_URL = 'https://test.authorize.net/gateway/transact.dll';

		/**
		 * @const Public payment url
		 */
		const AUTHORIZE_NET_PRODUCTION_PAYMENT_URL = 'https://secure2.authorize.net/gateway/transact.dll';

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCAUTHNET_Credit_Card_Gateway_Premium
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAUTHNET_Credit_Card_Gateway_Premium
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Create unique instance of YITH_WCAUTHNET_Credit_Card_Gateway_Premium class
		 *
		 * @return \YITH_WCAUTHNET_Credit_Card_Gateway_Premium Unique instance
		 * @since 1.0.0
		 */
		public function __construct() {
			// request parent constructor
			parent::__construct();

			// add premium fields
			$this->add_advanced_options();

			// init api handler object
			$this->api                  = YITH_WCAUTHNET_CIM_API();
			$this->api->login_id        = $this->login_id;
			$this->api->transaction_key = $this->transaction_key;
			$this->api->sandbox         = ( $this->sandbox == 'yes' );

			$this->payment_method = $this->get_option( 'payment_method' );
			$this->cim_handling   = $this->get_option( 'cim_handling' );
			$this->itemized       = $this->get_option( 'itemized' );

			$this->api->itemized     = ( $this->itemized == 'yes' );
			$this->api->cim_handling = ( $this->cim_handling == 'yes' );

			// gateway requires fields only if API methods are used
			$this->has_fields = (bool) ( 'api' == $this->payment_method );

			// register support for cc fields, only with direct checkout method
			if ( $this->has_fields() ) {
				$this->supports = array( 'default_credit_card_form', 'refunds' );
			}

			// register payment form print, only for redirect payment method
			if ( 'redirect' == $this->payment_method ) {
				add_action( 'woocommerce_receipt_yith_wcauthnet_gateway', array(
					$this,
					'print_authorize_net_payment_form'
				), 10, 1 );
			}
		}

		/**
		 * Add premium options to admin
		 *
		 * @param $options array Array of current
		 *
		 * @return array Filtered array of options
		 * @since 1.0.0
		 */
		public function add_advanced_options() {
			$options         = $this->form_fields;
			$premium_options = array(
				'payment_method' => array(
					'title'       => __( 'Request mode', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'select',
					'description' => __( 'Selecting "Redirect", users will be able to complete the payment on the pages of Authorize.net; on the contrary, the option "API" will allow using the potentialities of the APIs to complete the purchase without external redirects.', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'options'     => array(
						'redirect' => __( 'Redirect', 'yith-woocommerce-authorizenet-payment-gateway' ),
						'api'      => __( 'API', 'yith-woocommerce-authorizenet-payment-gateway' )
					),
					'default'     => 'api'
				),
				'cim_handling'   => array(
					'title'       => __( 'Enable Customer Information Manager (CIM)', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'checkbox',
					'description' => __( 'It allows the system to store the payment methods of the users, in order to use them in the future without the need to write the details again.', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'default'     => 'yes'
				),
				'itemized'       => array(
					'title'       => __( 'Enable itemized transaction', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'checkbox',
					'description' => __( 'Enable the registration of the items in the cart during the transaction (up to a maximum of 30 items)', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'default'     => 'yes'
				),

			);

			$this->form_fields = array_slice( $options, 0, array_search( 'md5_hash', array_keys( $options ) ) + 1, true ) + $premium_options + array_slice( $options, array_search( 'md5_hash', array_keys( $options ) ) + 1, count( $options ) - 1, true );
		}

		/**
		 * Process payment
		 *
		 * @param $order_id int Current order id
		 *
		 * @return null|array Null on failure; array on success ( id provided: 'status' [string] textual status of the payment / 'redirect' [string] Url where to redirect user )
		 */
		public function process_payment( $order_id ) {
			$order = wc_get_order( $order_id );

			if ( $this->payment_method == 'api' ) {
				$result = $this->_process_api_payment( $order );
			} else {
				$result = $this->_process_external_payment( $order );
			}

			return $result;
		}

		/**
		 * Process refund request, via API calls
		 *
		 * @param $order_id int Order id
		 * @param $amount   float|null Amount to refund; if null, the entire amount will be refunded
		 * @param $reason   string Reason for refund
		 *
		 * @return bool Status of the operation
		 * @since 1.0.0
		 */
		public function process_refund( $order_id, $amount = null, $reason = '' ) {
			$order = wc_get_order( $order_id );

			if ( ! $order || ! $this->can_refund_order( $order ) ) {
				$this->log->add( 'authorize.net', 'Refund Failed: No transaction ID' );
				$order->add_order_note( __( 'Authorize.net refund failed: No transaction ID', 'yith-woocommerce-authorizenet-payment-gateway' ) );

				return false;
			}

			$response = $this->api->crete_refund_transaction( $order, $amount, $this->_get_refund_details( $order ) );

			if ( ! empty( $response ) ) {
				$transaction_status = (string) $response->transactionResponse->responseCode;

				if ( 1 == $transaction_status ) {
					$this->log->add( 'authorize.net', 'Refund Result: ' . print_r( $response, true ) );
					$order->add_order_note( __( 'Authorize.net refund approved', 'yith-woocommerce-authorizenet-payment-gateway' ) );
				} else {
					$response_message = '';

					if ( is_array( $response->transactionResponse->errors->error ) ) {
						foreach ( $response->transactionResponse->errors->error as $error ) {
							$response_message .= $error->errorText . '\n';
						}
					} else {
						$response_message = $response->transactionResponse->errors->error->errorText;
					}

					$this->log->add( 'authorize.net', sprintf( __( 'Refund error: %s', 'yith-woocommerce-authorizenet-payment-gateway' ), $response_message ) );
					$order->add_order_note( sprintf( __( 'Refund error: %s', 'yith-woocommerce-authorizenet-payment-gateway' ), $response_message ) );

					return false;
				}
			} else {
				$this->log->add( 'authorize.net', __( 'Refund error: unknown error', 'yith-woocommerce-authorizenet-payment-gateway' ) );
				$order->add_order_note( __( 'Refund error: unknown error', 'yith-woocommerce-authorizenet-payment-gateway' ) );

				return false;
			}

			return true;
		}

		/* === API PAYMENT METHODS === */

		/**
		 * Process payment with API calls
		 *
		 * @param $order \WC_Order Current order
		 *
		 * @return null|array Null on failure; array on success ( id provided: 'status' [string] textual status of the payment / 'redirect' [string] Url where to redirect user )
		 */
		protected function _process_api_payment( $order ) {
			$payment = $this->_get_payment_details();

			if ( 'credit_card' == $payment->type && ( empty( $payment->card_number ) || empty( $payment->expiration_date ) ) ) {
				wc_add_notice( __( 'Missing required information', 'yith-woocommerce-authorizenet-payment-gateway' ), 'error' );

				return;
			}

			// user data
			$user_id         = get_current_user_id();
			$user_id         = ! $user_id ? $order->customer_user : $user_id;
			$payment_profile = ! empty( $_POST['authorize_net_payment_profile'] ) ? $_POST['authorize_net_payment_profile'] : false;

			$this->api->sandbox = ( $this->sandbox == 'yes' );

			// create a new payment profile, if user is already registered on Authorize.net
			if ( ! empty( $user_id ) && $this->cim_handling == 'yes' ) {
				$user_profile = get_user_meta( $user_id, '_authorize_net_profile_id', true );

				// update user profile with new billing email
				if ( ! empty( $user_profile ) ) {
					$this->api->update_customer_profile( $order, $user_profile );
				}

				// if a customer payment profile is already set, update its billing fields
				if ( ! empty( $user_profile ) && ! empty( $payment_profile ) ) {

					$payment_methods = get_user_meta( get_current_user_id(), '_authorize_net_payment_profiles', true );

					$masked_payment_data                  = new StdClass();
					$masked_payment_data->type            = 'credit_card';
					$masked_payment_data->card_number     = $payment_methods[ $payment_profile ]['account_num'];
					$masked_payment_data->expiration_date = 'XXXX';
					$this->api->update_customer_payment_profile( $order, $user_profile, $payment_profile, $masked_payment_data );
				}

				// if no customer payment profile is done, create a new one
				if ( ! empty( $user_profile ) && empty( $payment_profile ) ) {
					$response = $this->api->create_customer_payment_profile( $order, $user_profile, $payment );

					// register new payment method for user on DB
					if ( isset( $response->customerPaymentProfileId ) ) {
						$payment_profile_id          = (string) $response->customerPaymentProfileId;
						$registered_payment_profiles = get_user_meta( $user_id, '_authorize_net_payment_profiles', true );
						$new_record                  = array(
							'profile_id'      => $payment_profile_id,
							'account_num'     => 'XXXX' . substr( $payment->card_number, - 4 ),
							'expiration_date' => $payment->expiration_date,
							'default'         => false
						);

						if ( empty( $registered_payment_profiles ) ) {
							$registered_payment_profiles = array();
							$new_record['default']       = true;
						}

						$new_payment_profiles = $registered_payment_profiles + array( $payment_profile_id => $new_record );

						update_user_meta( $user_id, '_authorize_net_payment_profiles', $new_payment_profiles );
					}
				}
			}

			$cim_transaction_type = '';
			switch ( $this->transaction_type ) {
				case 'AUTH_ONLY':
					$cim_transaction_type = 'authOnlyTransaction';
					break;
				default:
					$cim_transaction_type = 'authCaptureTransaction';
					break;
			}

			// process request; this will alse create a new user and a new payment profile, if user is not registered
			$response = $this->api->create_payment_transaction( $order, $payment, $cim_transaction_type );

			if ( ! empty( $response ) ) {
				if ( ! empty( $response->transactionResponse ) ) {
					$transaction_status = (int) $response->transactionResponse->responseCode;

					if ( is_array( $response->messages ) ) {
						$transaction_message = (string) $response->messages->message[0]->text;
					} else {
						$transaction_message = (string) $response->messages->message->text;
					}
					$transaction_id          = (string) $response->transactionResponse->transId;
					$transaction_amount      = false;
					$transaction_email       = false;
					$transaction_hash        = (string) $response->transactionResponse->transHash;
					$transaction_account_num = (string) $response->transactionResponse->accountNumber;

					if ( ! empty( $response->transactionResponse->userFields->userField ) ) {
						foreach ( $response->transactionResponse->userFields->userField as $field ) {
							${(string) $field->name} = (string) $field->value;
						}
					}

					if ( 1 == $transaction_status ) {
						if ( $order->has_status( 'completed' ) ) {
							if ( 'yes' == $this->debug ) {
								$this->log->add( 'authorize.net', 'Aborting, Order #' . $order->id . ' is already complete.' );
							}

							return array(
								'status'   => 'success',
								'redirect' => $this->get_return_url( $order )
							);
						} else {
							// Validate amount
							if ( $order->get_total() != $transaction_amount ) {
								if ( 'yes' == $this->debug ) {
									$this->log->add( 'authorize.net', 'Payment error: Amounts do not match (gross ' . $transaction_amount . ')' );
								}

								// Put this order on-hold for manual checking
								$order->update_status( 'on-hold', sprintf( __( 'Validation error: Authorize.net amounts do not match with (%s).', 'yith-woocommerce-authorizenet-payment-gateway' ), $transaction_amount ) );

								wc_add_notice( sprintf( __( 'Validation error: Authorize.net amounts do not match with (%s).', 'yith-woocommerce-authorizenet-payment-gateway' ), $transaction_amount ), 'error' );

								return;
							}

							// Validate Email Address
							if ( strcasecmp( trim( $order->billing_email ), trim( $transaction_email ) ) != 0 ) {
								if ( 'yes' == $this->debug ) {
									$this->log->add( 'authorize.net', "Payment error: Authorize.net email ({$transaction_email}) does not match our email ({$order->billing_email})" );
								}

								// Put this order on-hold for manual checking
								$order->update_status( 'on-hold', sprintf( __( 'Validation error: Authorize.net responses from a different email address than (%s).', 'yith-woocommerce-authorizenet-payment-gateway' ), $transaction_email ) );

								wc_add_notice( sprintf( __( 'Validation error: Authorize.net responses from a different email address than (%s).', 'yith-woocommerce-authorizenet-payment-gateway' ), $transaction_email ), 'error' );

								return;
							}

							// Redundant md5 hash check
							/*
							if( ! empty( $this->md5_hash ) ){
								$expected_hash = strtoupper( md5( $this->md5_hash . $this->login_id . $transaction_id . number_format( $order->get_total(), 2, '.', '' ) ) );

								if( strcasecmp( trim( $transaction_hash ), trim( $expected_hash ) ) != 0 ) {
									if ( 'yes' == $this->debug ) {
										$this->log->add( 'authorize.net', "Payment error: MD5 Hash control failed" );
									}

									// Put this order on-hold for manual checking
									$order->update_status( 'on-hold', __( 'Validation error: MD5 Hash control failed.', 'yith-woocommerce-authorizenet-payment-gateway' ) );

									wc_add_notice( __( 'Validation error: MD5 Hash control failed.', 'yith-woocommerce-authorizenet-payment-gateway' ), 'error' );

									return;
								}
							}
							*/

							// Mark as complete
							$order->add_order_note( sprintf( __( 'Authorize.net payment completed (message: %s). Transaction ID: %s', 'yith-woocommerce-authorizenet-payment-gateway' ), $transaction_message, $transaction_id ) );
							$order->payment_complete( $transaction_id );

							if ( ! empty( $transaction_account_num ) ) {
								update_post_meta( $order->id, 'x_card_num', wc_clean( $transaction_account_num ) );
								update_post_meta( $order->id, 'x_card_expiration_date', wc_clean( $payment->expiration_date ) );
							}

							// register profile for user
							if ( $this->cim_handling == 'yes' && ! empty( $user_id ) ) {
								if ( isset( $response->profileResponse->customerProfileId ) ) {
									update_user_meta( $user_id, '_authorize_net_profile_id', (string) $response->profileResponse->customerProfileId );

									if ( isset( $response->profileResponse->customerPaymentProfileIdList ) ) {
										$payment_profile_id          = (string) $response->profileResponse->customerPaymentProfileIdList->numericString;
										$registered_payment_profiles = get_user_meta( $user_id, '_authorize_net_payment_profiles', true );
										$new_record                  = array(
											'profile_id'      => $payment_profile_id,
											'account_num'     => $transaction_account_num,
											'expiration_date' => $payment->expiration_date,
											'default'         => false
										);

										if ( empty( $registered_payment_profiles ) ) {
											$registered_payment_profiles = array();
											$new_record['default']       = true;
										}

										$new_payment_profiles = $registered_payment_profiles + array( $payment_profile_id => $new_record );

										update_user_meta( $user_id, '_authorize_net_payment_profiles', $new_payment_profiles );
									}
								}
							}

							// Remove cart
							WC()->cart->empty_cart();

							if ( 'yes' == $this->debug ) {
								$this->log->add( 'authorize.net', 'Payment Result: ' . print_r( $response, true ) );
							}

							return array(
								'result'   => 'success',
								'redirect' => $this->get_return_url( $order )
							);
						}
					} else {
						$response_message = '';

						if ( is_array( $response->transactionResponse->errors->error ) ) {
							foreach ( $response->transactionResponse->errors->error as $error ) {
								$response_message .= $error->errorText . '\n';
							}
						} else {
							$response_message = $response->transactionResponse->errors->error->errorText;
						}

						if ( 'yes' == $this->debug ) {
							$this->log->add( 'authorize.net', 'Payment error: ' . $response_message );
						}

						wc_add_notice( sprintf( __( 'Payment error: %s', 'yith-woocommerce-authorizenet-payment-gateway' ), $response_message ), 'error' );

						return array(
							'result'   => 'fail',
							'redirect' => ''
						);
					}
				} else {
					$response_message = '';
					if ( isset( $response->messages ) ) {
						$response_message = $response->messages->message->text;
					}

					if ( 'yes' == $this->debug ) {
						$this->log->add( 'authorize.net', 'Payment error: ' . $response_message );
					}

					wc_add_notice( sprintf( __( 'Payment error: %s', 'yith-woocommerce-authorizenet-payment-gateway' ), $response_message ), 'error' );

					return array(
						'result'   => 'fail',
						'redirect' => ''
					);
				}
			} else {
				wc_add_notice( __( 'An error occurred while processing the payment; please try again later', 'yith-woocommerce-authorizenet-payment-gateway' ), 'error' );

				return;
			}
		}

		/**
		 * Returns an object with details for payment
		 *
		 * @return \StdClass
		 * @since 1.0.0
		 */
		protected function _get_payment_details() {
			$payment_obj = new StdClass();

			if ( ! empty( $_POST['authorize_net_payment_profile'] ) ) {
				$payment_obj->type                = "profile";
				$payment_obj->customer_profile_id = get_user_meta( get_current_user_id(), '_authorize_net_profile_id', true );
				$payment_obj->payment_profile_id  = $_POST['authorize_net_payment_profile'];
			} else {
				$payment_obj->type            = "credit_card";
				$payment_obj->card_number     = str_replace( array(
					' ',
					'-'
				), '', $_POST[ $this->id . '-card-number' ] );
				$payment_obj->expiration_date = str_replace( array(
					'/',
					' '
				), '', $_POST[ $this->id . '-card-expiry' ] );

				if ( isset( $_POST[ $this->id . '-card-cvc' ] ) ) {
					$payment_obj->cvv = $_POST[ $this->id . '-card-cvc' ];
				}
			}

			return $payment_obj;
		}

		/**
		 * Returns an object with details for refund
		 *
		 * @param $order \WC_Order Order to refund
		 *
		 * @return \StdClass
		 * @since 1.0.0
		 */
		protected function _get_refund_details( $order ) {
			$payment_obj = new StdClass();

			$payment_obj->type            = "credit_card";
			$payment_obj->card_number     = get_post_meta( $order->id, 'x_card_num', true );
			$payment_obj->expiration_date = get_post_meta( $order->id, 'x_card_expiration_date', true );

			return $payment_obj;
		}

		/**
		 * Display the payment fields on the checkout page
		 *
		 * @since  1.0
		 */
		public function payment_fields() {

			if ( $this->description ) {
				echo '<p>' . wp_kses_post( $this->description ) . '</p>';
			}

			if ( 'api' == $this->payment_method ) {
				if ( is_user_logged_in() && 'yes' == $this->cim_handling ) {
					$payment_profiles = get_user_meta( get_current_user_id(), '_authorize_net_payment_profiles', true );
					if ( ! empty( $payment_profiles ) ) {
						// Include payment form template
						$template_name = 'authorize-net-credit-card-form.php';
						$locations     = array(
							trailingslashit( WC()->template_path() ) . $template_name,
							$template_name
						);

						$template = locate_template( $locations );

						if ( ! $template ) {
							$template = YITH_WCAUTHNET_DIR . 'templates/' . $template_name;
						}

						include_once( $template );
					} else {
						$this->credit_card_form();
					}
				} else {
					$this->credit_card_form();
				}
			}
		}

		/* === DIRECT PAYMENT METHODS === */

		/**
		 * Prints Authorize.net checkout form
		 *
		 * @param $order_id int Current order id
		 *
		 * @return void
		 */
		public function print_authorize_net_payment_form( $order_id ) {
			$order          = wc_get_order( $order_id );
			$order_number   = $order->get_order_number();
			$order_total    = $order->get_total();
			$order_currency = method_exists( $order, 'get_currency' ) ? $order->get_currency() : $order->get_order_currency();

			// Define variables to use in the template
			$login_id            = $this->login_id;
			$amount              = $order_total;
			$invoice             = $order_id;
			$sequence            = $order_id;
			$version             = '3.1';
			$relay_response      = 'TRUE';
			$type                = $this->transaction_type;
			$description         = 'Order ' . $order_number;
			$show_form           = 'PAYMENT_FORM';
			$currency_code       = $order_currency;
			$first_name          = $order->billing_first_name;
			$last_name           = $order->billing_last_name;
			$company             = $order->billing_company;
			$address             = $order->billing_address_1 . ' ' . $order->billing_address_2;
			$country             = $order->billing_country;
			$phone               = $order->billing_phone;
			$state               = $order->billing_state;
			$city                = $order->billing_city;
			$zip                 = $order->billing_postcode;
			$email               = $order->billing_email;
			$ship_to_first_name  = $order->shipping_first_name;
			$ship_to_last_name   = $order->shipping_last_name;
			$ship_to_address     = $order->shipping_address_1;
			$ship_to_city        = $order->shipping_city;
			$ship_to_zip         = $order->shipping_postcode;
			$ship_to_state       = $order->shipping_state;
			$cancel_url          = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : WC()->cart->get_checkout_url();
			$cancel_button_label = apply_filters( 'yith_wcauthnet_cancel_button_label', __( 'Cancel Payment', 'yith-woocommerce-authorizenet-payment-gateway' ) );
			$relay_url           = esc_url( add_query_arg( 'wc-api', $this->id, user_trailingslashit( home_url() ) ) );

			// Itemized request information
			$tax_info  = array();
			$item_info = array();

			if ( 'yes' == $this->itemized ) {
				$line_items = $order->get_items( 'line_item' );
				$taxes      = $order->get_taxes();

				if ( ! empty( $line_items ) && count( $line_items ) <= 30 ) {
					foreach ( $line_items as $key => $item ) {
						$item_subtotal = $order->get_item_subtotal( $item );
						$item_taxable  = ( $order->get_item_tax( $item ) != 0 ) ? 'Y' : 'N';

						$item_info[] = "{$key}<|>{$item['name']}<|><|>{$item['qty']}<|>{$item_subtotal}<|>{$item_taxable}";
					}
				}

				if ( ! empty( $taxes ) ) {
					foreach ( $taxes as $key => $tax ) {
						$tax_info[] = $tax['label'] . "<|><|>" . number_format( $tax['tax_amount'], 2, '.', '' );
					}
				}
			}

			if ( 'yes' == $this->sandbox ) {
				$process_url = self::AUTHORIZE_NET_SANDBOX_PAYMENT_URL;
			} else {
				$process_url = self::AUTHORIZE_NET_PRODUCTION_PAYMENT_URL;
			}

			// Security params
			$timestamp = time();

			if ( phpversion() >= '5.1.2' ) {
				$fingerprint = hash_hmac( "md5", $this->login_id . "^" . $order_id . "^" . $timestamp . "^" . number_format( $order_total, 2, '.', '' ) . "^" . $order_currency, $this->transaction_key );
			} else {
				$fingerprint = bin2hex( mhash( MHASH_MD5, $this->login_id . "^" . $order_id . "^" . $timestamp . "^" . number_format( $order_total, 2, '.', '' ) . "^" . $order_currency, $this->transaction_key ) );
			}

			// Include payment form template
			$template_name = 'authorize-net-payment-form.php';
			$locations     = array(
				trailingslashit( WC()->template_path() ) . $template_name,
				$template_name
			);

			$template = locate_template( $locations );

			if ( ! $template ) {
				$template = YITH_WCAUTHNET_DIR . 'templates/' . $template_name;
			}

			include_once( $template );
		}

		/* === UTILITY METHODS === */

		/**
		 * Can the order be refunded via authorize.net?
		 *
		 * @param WC_Order $order
		 *
		 * @return bool
		 */
		public function can_refund_order( $order ) {
			return $order && $order->get_transaction_id();
		}
	}
}

/**
 * Unique access to instance of YITH_WCAUTHNET_Credit_Card_Gateway_Premium class
 *
 * @return \YITH_WCAUTHNET_Credit_Card_Gateway_Premium
 * @since 1.0.0
 */
function YITH_WCAUTHNET_Credit_Card_Gateway_Premium() {
	return YITH_WCAUTHNET_Credit_Card_Gateway_Premium::get_instance();
}