<?php
/**
 * Utility class
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @since 2.0.0
 */

if ( ! class_exists( 'NS_FBA_Utils' ) ) {

	class NS_FBA_Utils {

		private $ns_fba;
		
		function __construct( $ns_fba ) {
			// local reference to the main ns_fba object
			$this->ns_fba = $ns_fba;
		}

		function is_configured() {

			// object and WP execution path debugging:
			//error_log( 'Region Selected: ' . $this->ns_fba->options['ns_fba_service_url']);
			//error_log( "WP Backtrace: \n" . str_replace( ',',"\n\t", wp_debug_backtrace_summary() ));
			
			// if it's already configured (like on save) then skip the tests
			//if ( $this->ns_fba->is_configured ) return true;
			
			if ( isset( $_POST['woocommerce_fba_ns_fba_mws_auth_token'] )
			     && '' !== $_POST['woocommerce_fba_ns_fba_mws_auth_token'] ) {
				$this->ns_fba->options['ns_fba_mws_auth_token']        = $_POST['woocommerce_fba_ns_fba_mws_auth_token'];
			}
			if ( isset( $_POST['woocommerce_fba_ns_fba_aws_access_key_id'] )
				&& '' !== $_POST['woocommerce_fba_ns_fba_aws_access_key_id'] ) {
				$this->ns_fba->options['ns_fba_aws_access_key_id']        = $_POST['woocommerce_fba_ns_fba_aws_access_key_id'];
			}
			if ( isset( $_POST['woocommerce_fba_ns_fba_aws_secret_access_key'] )
				&& '' !== $_POST['woocommerce_fba_ns_fba_aws_secret_access_key'] ) {
				$this->ns_fba->options['ns_fba_aws_secret_access_key']    = $_POST['woocommerce_fba_ns_fba_aws_secret_access_key'];
			}
			if ( isset( $_POST['woocommerce_fba_ns_fba_merchant_id'] )
				&& '' !== $_POST['woocommerce_fba_ns_fba_merchant_id'] ) {
				$this->ns_fba->options['ns_fba_merchant_id']              = $_POST['woocommerce_fba_ns_fba_merchant_id'];
			}
			if ( isset( $_POST['woocommerce_fba_ns_fba_marketplace_id'] )
				&& '' !== $_POST['woocommerce_fba_ns_fba_marketplace_id'] ) {
				$this->ns_fba->options['ns_fba_marketplace_id']           = $_POST['woocommerce_fba_ns_fba_marketplace_id'];
			}
			
			/***************************************************************************************
			 * Define the Amazon Constants
			 * REQUIRED by the Amazon PHP library
			 * Amazon PHP library has been modified by Never Settle to offload the signature process
			 *
			 * Access Key ID and Secret Acess Key ID, obtained from: http://aws.amazon.com
			 * MWS Auth Token obtained from: https://sellercentral.amazon.com/apps/manage
			 * All MWS requests must contain a User-Agent header.
			 * The application name and version defined below are used in creating this value.
			 * All MWS requests must contain the seller's merchant ID and marketplace ID.
			 ****************************************************************************************/

			// check if all properties are set and if so, then we can define the constants
			if ((
					(
						isset( $this->ns_fba->options['ns_fba_aws_access_key_id'] ) &&
					    '' !== $this->ns_fba->options['ns_fba_aws_access_key_id'] &&
					    isset( $this->ns_fba->options['ns_fba_aws_secret_access_key'] ) &&
					    '' !== $this->ns_fba->options['ns_fba_aws_secret_access_key']
					) ||
			        (
			        	isset( $this->ns_fba->options['ns_fba_mws_auth_token'] ) &&
			            '' !== $this->ns_fba->options['ns_fba_mws_auth_token']
			        )
				)	&&
			     isset( $this->ns_fba->options['ns_fba_merchant_id'] ) &&
			     '' !== $this->ns_fba->options['ns_fba_merchant_id'] &&
			     isset( $this->ns_fba->options['ns_fba_marketplace_id'] ) &&
			     '' !== $this->ns_fba->options['ns_fba_marketplace_id']
			) {
				if ( ! defined( 'NS_AWS_ACCESS_KEY_ID' ) ) {
					// If there's an MWS Auth Token...
					if ( isset( $this->ns_fba->options['ns_fba_mws_auth_token'] ) &&
					     '' !== $this->ns_fba->options['ns_fba_mws_auth_token'] ) {
						if ( ! defined( 'NS_MWS_AUTH_TOKEN' ) ) {
							define( 'NS_MWS_AUTH_TOKEN', $this->ns_fba->options['ns_fba_mws_auth_token'] );
						}
						// ...then we use the internal NS access key based on current region
						switch ( $this->ns_fba->options['ns_fba_service_url'] ) {
							case 'https://mws-eu.amazonservices.com':
							case 'https://mws.amazonservices.in':
								define( 'NS_AWS_ACCESS_KEY_ID', $this->ns_fba->options['ns_fba_aws_access_key_id_eu'] );
								break;
							case 'https://mws.amazonservices.jp':
							case 'https://mws.amazonservices.com.au':
							case 'https://mws-fe.amazonservices.com':
								define( 'NS_AWS_ACCESS_KEY_ID', $this->ns_fba->options['ns_fba_aws_access_key_id_fe'] );
								break;
							case 'https://mws.amazonservices.cn':
								define( 'NS_AWS_ACCESS_KEY_ID', $this->ns_fba->options['ns_fba_aws_access_key_id_cn'] );
								break;
							default :
								define( 'NS_AWS_ACCESS_KEY_ID', $this->ns_fba->options['ns_fba_aws_access_key_id_na'] );
								break;
						}
					// Otherwise, use the Seller's access key for legacy support
					} else {
						define( 'NS_AWS_ACCESS_KEY_ID', $this->ns_fba->options['ns_fba_aws_access_key_id'] );
					}
				}
				if ( ! defined( 'NS_AWS_SECRET_ACCESS_KEY' ) ) {
					// If there's an MWS Auth Token, then we set the secret key to a fake value
					if ( isset( $this->ns_fba->options['ns_fba_mws_auth_token'] ) &&
					     '' !== $this->ns_fba->options['ns_fba_mws_auth_token'] ) {
						// This value can also be checked inside the MWS PHP Library for deciding whether
						// or not to offload signature processing to our API
						define( 'NS_AWS_SECRET_ACCESS_KEY', 'USE_SIGNATURE_API' );
					// Otherwise, use the Seller's access key for legacy support
					} else {
						define( 'NS_AWS_SECRET_ACCESS_KEY', $this->ns_fba->options['ns_fba_aws_secret_access_key'] );
					}
				}
				if ( ! defined( 'NS_APPLICATION_NAME' ) ) {
					// There are 2 parts to the user-agent string in the MWS PHP library.
					// 1. APPNAME/VERSION
					// 2. (Language=PHP/5.6.30; Platform=Windows NT/i586/6.2; MWSClientVersion=2016-02-01)
					// We use the constant NS_APPLICATION_NAME = "WooCommerceMCF/[version]"
					define( 'NS_APPLICATION_NAME', $this->ns_fba->app_name );
				}
				if ( ! defined( 'NS_APPLICATION_VERSION' ) ) {
					// And we use the constant NS_APPLICATION_VERSION so that the full value of the first part
					// of the UA passed with requests to Amazon is: "WooCommerceMCF/[version]"
					define( 'NS_APPLICATION_VERSION', $this->ns_fba->version );
				}
				if ( ! defined( 'NS_MERCHANT_ID' ) ) {
					define( 'NS_MERCHANT_ID', $this->ns_fba->options['ns_fba_merchant_id'] );
				}
				if ( ! defined( 'NS_MARKETPLACE_ID' ) ) {
					define( 'NS_MARKETPLACE_ID', $this->ns_fba->options['ns_fba_marketplace_id'] );
				}
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Correctly return true / false regardless of the settings version either before or after WC_Integration
		 * (previous version of NS FBA used empty setting vs. value whereas WC_Integration uses 'no' vs. 'yes')
		 */
		function isset_on( $setting ) {
			if ( 'no' == $setting || empty( $setting ) ) {
				//error_log( $setting . ' = false' );
				return false;
			} else {
				//error_log( $setting . ' = true' );
				return true;
			}
		}

		/**
		 * Normalize on / off for legacy NS FBA settings data. Eventually this can be removed.
		 */
		function isset_how( $setting ) {
			if ( empty( $setting ) || 'no' == $setting ) {
				//error_log( $setting . ' = no' );
				return 'no';
			} else {
				//error_log( $setting . ' = yes' );
				return 'yes';
			}
		}

		/**
		 * Send an email message using phpmailer
		 */
		function mail_message( $message, $subject = '' ) {

			// if a custom address is set use that otherwise use the admin email
			$to = '';
			if ( isset( $this->ns_fba->options['ns_fba_notify_email'] ) && $this->ns_fba->options['ns_fba_notify_email'] !== '' ) {
				$to = $this->ns_fba->options['ns_fba_notify_email'];
			} else {
				$to = get_option( 'admin_email' );
			}

			// set the headers for HTML and FROM
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			$headers[] = 'From: NS FBA <' . get_option( 'admin_email' ) . '>' . "\r\n";
			wp_mail( $to, $subject, $message, $headers );

			/*
			 * OLD METHOD
			if ( class_exists( 'PHPMailer' ) ) {
				$mail = new PHPMailer();								//create a new PHPMailer instance
				$mail->setFrom(get_option( 'admin_email' ), 'NS FBA');	//set who the message is to be sent from
				$mail->addAddress(get_option( 'admin_email' ), '');		//set who the message is to be sent to
				$mail->Subject = $subject;								//set the subject line
				$mail->msgHTML( $message );								//set the HTML message

				//send the message, check for errors
				if ( ! $mail->send() ) {
					error_log( '<h3>PHPMailer Error:</h3>' . $mail->ErrorInfo . '<br /><br />', 3, $this->ns_fba->err_log_path );
				}
			}
			*/
		}

		function is_order_amazon_fulfill( $order, $is_manual_send ) {
			// check if there are any conditions or settings which block the order from being sent to Amazon
			// assume the order is supposed to be sent to Amazon
			$is_order_amazon_fulfill = true;

			// if this is a manual send to FBA and the Manual Order Override setting is ON then don't check any other conditions
			if ( $is_manual_send && $this->isset_on( $this->ns_fba->options['ns_fba_manual_order_override'] ) ) {
				return true;
			}

			// check if international fulfillment is disabled and if this order is international
			$country = new WC_Countries;
			$base_country = $country->get_base_country();
			$shipping_country = get_post_meta( $order->get_id(), '_shipping_country', true );

			//error_log( ' base_country = ' . $base_country );
			//error_log( ' ship_country = ' . $shipping_country );
			//error_log( ' disable intl setting = ' . $this->ns_fba->options['ns_fba_disable_international'] );

			if ( $this->isset_on( $this->ns_fba->options['ns_fba_disable_international'] ) && $base_country != $shipping_country ) {
				$is_order_amazon_fulfill = false;
				throw new Exception( __( 'This order was NOT sent to FBA because International fulfillment is disabled in the NS FBA settings and the shipping address country does not match the base location country in the WooCommerce settings.', $this->ns_fba->text_domain ) );
			}

			// check if any shipping methods are disabled and if this order is using one of them
			$order_shipping_method = $order->get_shipping_method();
			//error_log( '<br /><br />Order Shipping Method: '. $order_shipping_method . '<br />', 3, $this->ns_fba->err_log_path );

			//$order_shipping_method_name = apply_filters( 'wpml_translate_single_string', $order_shipping_method, 'woocommerce', 'free_shipping1_shipping_method_title', 'en' );
			//$order_shipping_method_trans = apply_filters( 'wpml_translate_single_string', $order_shipping_method, 'woocommerce', md5( $order_shipping_method ), 'en' );
			//error_log( '<br /><br />Order Shipping Method: '. $order_shipping_method . '<br />', 3, $this->ns_fba->log_path );
			//error_log( 'Order Shipping Method Trans: '. $order_shipping_method_trans . '<br /><br />', 3, $this->ns_fba->log_path );
			//error_log( 'Order Shipping Method Trans Name: '. $order_shipping_method_name . '<br /><br />', 3, $this->ns_fba->err_log_path );
			//error_log( 'Order Shipping Method Trans MD5: '. $order_shipping_method_trans . '<br /><br />', 3, $this->ns_fba->err_log_path );

			// try to reverse translate the shipping method back to English if WPML translated it to another language at checkout
			$excluded_shipping_options = $this->ns_fba->options['ns_fba_disable_shipping'];
			if ( is_array( $excluded_shipping_options ) && count( $excluded_shipping_options ) > 0 ) {
				foreach ( $excluded_shipping_options as $excl_key => $excluded_option ) {
					//error_log ( 'Excluded Shipping Option: '. $excluded_option . '<br /><br />', 3, $this->ns_fba->err_log_path );
					// md5 used per WPML Yuri because "that is the standard procedure when a string does not have a specific name attached to it"
			        $excluded_option_trans = apply_filters( 'wpml_translate_single_string', $excluded_option, 'woocommerce', md5( $excluded_option ) );
					// add the translation to the array
					array_push( $excluded_shipping_options, $excluded_option_trans );
					//error_log ( 'Excluded Shipping Option TRANS: '. $excluded_option_trans . '<br /><br />', 3, $this->ns_fba->err_log_path );
				}
				if ( in_array( $order_shipping_method, $excluded_shipping_options ) ) {
					$is_order_amazon_fulfill = false;
					throw new Exception( __( 'This order was NOT sent to FBA because it is using a Shipping Method that is disabled for FBA in the NS FBA settings.', $this->ns_fba->text_domain ) );
				}
			}

			// allow other plugins to filter this order with their own fulfillment rules
			$is_order_amazon_fulfill = apply_filters( 'ns_fba_is_order_fulfill', $is_order_amazon_fulfill, $order );

			// if a filter sets the order to not be fulfilled then throw that
			if ( ! $is_order_amazon_fulfill ) {
				throw new Exception( __( 'This order was NOT sent to FBA because a different plugin has modified the filter: ns_fba_is_order_fulfill.', $this->ns_fba->text_domain ) );
			}

			return $is_order_amazon_fulfill;
		}

		function is_order_item_amazon_fulfill( $order, $item, $item_product, $product_id, $is_manual_send ) {
			// check if there are any conditions which block the order from being sent to Amazon
			// assume the order item is supposed to go to Amazon
			$is_order_item_amazon_fulfill = true;
			$order_note = '';

			// if this is a virtual item then automatically return false - this handler is primarily needed for
			// variable product scenarios where 1 variation is physical and another variation is virtual and
			// the overall product is set to fulfill with amazon but the virtual variation should never be sent.
			// first we have to make sure we have and get a variation
			if ( $item->get_variation_id() ) {
				//error_log("inside get_variation_id = " . $item->get_variation_id() );
				if ( 'product_variation' === get_post_type( $item->get_variation_id() ) ) {
					$product = wc_get_product( $item->get_variation_id() );
					if ( $product->is_virtual() ) {
						return false;
					}
				}
			}

			// if this is a manual send to FBA and the Manual Order Item Override setting is ON then don't check any other conditions
			if ( $is_manual_send && $this->isset_on( $this->ns_fba->options['ns_fba_manual_item_override'] ) ) {
				return true;
			}

			// if vacation mode is ON then don't check any other conditions
			if ( $this->isset_on( $this->ns_fba->options['ns_fba_vacation_mode'] ) ) {
				return true;
			}

			// check if this order item's product setting for Fulfill with Amazon FBA is turned ON
			if ( $item_product && 'yes' == get_post_meta( $product_id, 'ns_fba_is_fulfill', true ) ) {
				$is_order_item_amazon_fulfill = true;
			} else {
				$is_order_item_amazon_fulfill = false;
				$order_note .= __( 'The Order Item with SKU: ' . $item_product->get_sku() . ' is not set to Fulfill with Amazon FBA in its product settings. It will not be sent to FBA for this order. ', $this->ns_fba->text_domain );
			}

			// check if the Quantity Max Filter is set and violated
			if ( ! empty( $this->ns_fba->options['ns_fba_quantity_max_filter'] ) && $item['qty'] > $this->ns_fba->options['ns_fba_quantity_max_filter'] ) {
				$is_order_item_amazon_fulfill = false;
				$order_note .= __( 'The Order Item with SKU: ' . $item_product->get_sku() . ' has a Qty = ' . $item['qty'] . ' which is > the Quantity Max Filter setting in NS FBA. It will not be sent to FBA for this order.', $this->ns_fba->text_domain );
			}

			// set up a test for the value before and after the filter
			$is_order_item_before_filter = $is_order_item_amazon_fulfill;

			// allow other plugins to filter this order_item with their own fulfillment rules
			$is_order_item_amazon_fulfill = apply_filters( 'ns_fba_is_order_item_fulfill', $is_order_item_amazon_fulfill, $item );

			if ( $is_order_item_before_filter && ! $is_order_item_amazon_fulfill ) {
				$order_note .= __( 'Fulfillment status for the Order Item with SKU: ' . $item_product->get_sku() . ' was modified by a different plugin using the filter: ns_fba_is_order_item_fulfill. It will not be sent to FBA for this order.', $this->ns_fba->text_domain );
			}

			if ( ! empty( $order_note ) ) {
				$order->add_order_note( $order_note );
			}

			return $is_order_item_amazon_fulfill;
		}

		function delete_older_logs() {
			$days = 30;
			$path_dir = $this->ns_fba->plugin_path . 'logs/';
			$files_deleted = 0;
			// Open the directory
			if ( $handle = opendir( $path_dir ) ) {
			    // Loop through the directory
			    while ( false !== ( $file = readdir( $handle ) ) ) {
			        // Check the file we're doing is actually a file
			        if ( is_file( $path_dir . $file ) ) {
			            // Check if the file is older than X days old
			            if ( filemtime( $path_dir . $file ) < ( time() - ( $days * 24 * 60 * 60 ) ) ) {
			                // Do the deletion
			                unlink( $path_dir . $file );
			                $files_deleted++;
			            }
			        }
			    }
			}
			return $files_deleted;
		}
	}
}// End if().
