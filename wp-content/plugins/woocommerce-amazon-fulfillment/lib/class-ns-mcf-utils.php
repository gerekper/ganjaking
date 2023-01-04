<?php
/**
 * Utility class for helper functions and re-usuable code.
 *
 * TODO: 4.1.0 This class should consolidate all helper functions across the project.
 * TODO: Code will likely need to be moved in here FROM class-ns-fba-logs.
 * TODO: When all the utils functionality is here and tested well, then class-ns-mcf-utils.php can be deleted.
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @since 4.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'NS_MCF_Utils' ) ) {

	/**
	 * Inventory class. For handling all API and local operations and functions working with product inventory.
	 */
	class NS_MCF_Utils extends NS_MCF_Integration {

		/**
		 * Check if LWA is configured.
		 *
		 * @return bool
		 */
		public function is_configured() {
			$option = get_option( 'ns_fba_lwa_configured' );
			return ! empty( $option ) && true === (bool) $option;
		}

		/**
		 * Correctly return true / false regardless of the settings version either before or after WC_Integration
		 * (previous version of NS FBA used empty setting vs. value whereas WC_Integration uses 'no' vs. 'yes').
		 *
		 * @param string $setting The option setting.
		 *
		 * @return bool
		 */
		public function isset_on( $setting ): bool {
			if ( 'no' === $setting || empty( $setting ) ) {
				return false;
			} else {
				return true;
			}
		}

		/**
		 * Normalize on / off for legacy NS FBA settings data. Eventually this can be removed.
		 *
		 * @param string $setting The option setting.
		 *
		 * @return string
		 */
		public function isset_how( $setting ): string {
			if ( empty( $setting ) || 'no' === $setting ) {
				return 'no';
			} else {
				return 'yes';
			}
		}

		/**
		 * Send an email message using phpmailer.
		 *
		 * @param string $message The email message.
		 * @param string $subject Optional. The email subject.
		 */
		public function mail_message( $message, $subject = '' ) {

			// if a custom address is set use that otherwise use the admin email.
			$to = '';
			if ( isset( $this->ns_fba->options['ns_fba_notify_email'] ) && '' !== $this->ns_fba->options['ns_fba_notify_email'] ) {
				$to = $this->ns_fba->options['ns_fba_notify_email'];
			} else {
				$to = get_option( 'admin_email' );
			}

			// set the headers for HTML and FROM.
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			$headers[] = 'From: NS FBA <' . get_option( 'admin_email' ) . '>' . "\r\n";
			wp_mail( $to, $subject, $message, $headers );
		}

		/**
		 * Check if there are things that can block the order from being fulfiled by Amazon.
		 *
		 * @param WC_Order $order The order.
		 * @param bool     $is_manual_send If its a manual send.
		 *
		 * @throws Exception If international fulfilment is disabled, an error is thrown.
		 *
		 * @return bool
		 */
		public function is_order_amazon_fulfill( $order, $is_manual_send ) {
			// check if there are any conditions or settings which block the order from being sent to Amazon.
			// assume the order is supposed to be sent to Amazon.
			$is_order_amazon_fulfill = true;

			// if this is a manual send to FBA then don't check any other conditions.
			if ( $is_manual_send ) {
				return true;
			}

			// check if international fulfillment is disabled and if this order is international.
			$country          = new WC_Countries();
			$base_country     = $country->get_base_country();
			$shipping_country = get_post_meta( $order->get_id(), '_shipping_country', true );

			if ( $this->isset_on( $this->ns_fba->options['ns_fba_disable_international'] ) && $base_country !== $shipping_country ) {
				$is_order_amazon_fulfill = false;
				throw new Exception( __( 'This order was NOT sent to FBA because International fulfillment is disabled in the NS FBA settings and the shipping address country does not match the base location country in the WooCommerce settings.', $this->ns_fba->text_domain ) );
			}

			// check if any shipping methods are disabled and if this order is using one of them.
			$order_shipping_method = $order->get_shipping_method();

			// try to reverse translate the shipping method back to English if WPML translated it to another language at checkout.
			$excluded_shipping_options = $this->ns_fba->options['ns_fba_disable_shipping'];
			if ( is_array( $excluded_shipping_options ) && count( $excluded_shipping_options ) > 0 ) {
				foreach ( $excluded_shipping_options as $excl_key => $excluded_option ) {
					// error_log ( 'Excluded Shipping Option: '. $excluded_option . '<br /><br />', 3, $this->ns_fba->err_log_path );.
					// md5 used per WPML Yuri because "that is the standard procedure when a string does not have a specific name attached to it".
					$excluded_option_trans = apply_filters( 'wpml_translate_single_string', $excluded_option, 'woocommerce', md5( $excluded_option ) );
					// add the translation to the array.
					array_push( $excluded_shipping_options, $excluded_option_trans );
				}
				if ( in_array( $order_shipping_method, $excluded_shipping_options, true ) ) {
					$is_order_amazon_fulfill = false;
					throw new Exception( __( 'This order was NOT sent to FBA because it is using a Shipping Method that is disabled for FBA in the NS FBA settings.', $this->ns_fba->text_domain ) );
				}
			}

			// allow other plugins to filter this order with their own fulfillment rules.
			$is_order_amazon_fulfill = apply_filters( 'ns_fba_is_order_fulfill', $is_order_amazon_fulfill, $order );

			// if a filter sets the order to not be fulfilled then throw that.
			if ( ! $is_order_amazon_fulfill ) {
				throw new Exception( __( 'This order was NOT sent to FBA because a different plugin has modified the filter: ns_fba_is_order_fulfill.', $this->ns_fba->text_domain ) );
			}

			return $is_order_amazon_fulfill;
		}

		/**
		 * Check if the order item can be processed by Amazon.
		 *
		 * @param WC_Order      $order The order.
		 * @param WC_Order_Item $item  The Order item.
		 * @param WC_Product    $item_product The Item product.
		 * @param int           $product_id The product id.
		 * @param bool          $is_manual_send If its a manual send.
		 *
		 * @return bool
		 */
		public function is_order_item_amazon_fulfill( $order, $item, $item_product, $product_id, $is_manual_send ) {
			// check if there are any conditions which block the order from being sent to Amazon
			// assume the order item is supposed to go to Amazon.
			$is_order_item_amazon_fulfill = true;
			$order_note                   = '';

			// if this is a virtual item then automatically return false - this handler is primarily needed for
			// variable product scenarios where 1 variation is physical and another variation is virtual and
			// the overall product is set to fulfill with amazon but the virtual variation should never be sent.
			// first we have to make sure we have and get a variation.
			if ( $item->get_variation_id() ) {
				if ( 'product_variation' === get_post_type( $item->get_variation_id() ) ) {
					$product = wc_get_product( $item->get_variation_id() );
					if ( $product->is_virtual() ) {
						return false;
					}
				}
			}

			// if vacation mode is ON then don't check any other conditions.
			if ( $this->isset_on( $this->ns_fba->options['ns_fba_vacation_mode'] ) ) {
				return true;
			}

			// TODO: Future handling for both parent and variation level settings on/off fulfillment.
			$maybe_parent_id = $item_product->get_parent_id();
			// Simple product check if the setting for Fulfill with Amazon FBA is turned ON.
			if ( $item_product && 'yes' === get_post_meta( $product_id, 'ns_fba_is_fulfill', true ) ) {
				$is_order_item_amazon_fulfill = true;
				// Variation product check for parent product setting where Fulfill with Amazon is ON.
			} elseif ( ! empty( $maybe_parent_id ) && 'yes' === get_post_meta( $maybe_parent_id, 'ns_fba_is_fulfill', true ) ) {
				$is_order_item_amazon_fulfill = true;
			} else {
				$is_order_item_amazon_fulfill = false;
				// translators: The sku.
				$order_note .= sprintf( __( 'The Order Item with SKU: %s is not set to Fulfill with Amazon FBA in its product settings. It will not be sent to FBA for this order. ', $this->ns_fba->text_domain ), $item_product->get_sku() );
			}

			// if this is a manual send to FBA then don't check any other conditions.
			// Prevent sending products we do not want fulfiled.
			if ( $is_order_item_amazon_fulfill && $is_manual_send ) {
				return true;
			}

			// check if the Quantity Max Filter is set and violated.
			if ( ! empty( $this->ns_fba->options['ns_fba_quantity_max_filter'] ) && $item['qty'] > $this->ns_fba->options['ns_fba_quantity_max_filter'] ) {
				$is_order_item_amazon_fulfill = false;
				// translators: 1: The SKU 2: The quantity.
				$order_note .= sprintf( __( 'The Order Item with SKU: %1$s has a Qty = %2$s which is > the Quantity Max Filter setting in NS FBA. It will not be sent to FBA for this order.', $this->ns_fba->text_domain ), $item_product->get_sku(), $item['qty'] );
			}

			// set up a test for the value before and after the filter.
			$is_order_item_before_filter = $is_order_item_amazon_fulfill;

			// allow other plugins to filter this order_item with their own fulfillment rules.
			$is_order_item_amazon_fulfill = apply_filters( 'ns_fba_is_order_item_fulfill', $is_order_item_amazon_fulfill, $item );

			if ( $is_order_item_before_filter && ! $is_order_item_amazon_fulfill ) {
				// translators: The sku.
				$order_note .= sprintf( __( 'Fulfillment status for the Order Item with SKU: %s was modified by a different plugin using the filter: ns_fba_is_order_item_fulfill. It will not be sent to FBA for this order.', $this->ns_fba->text_domain ), $item_product->get_sku() );
			}

			if ( ! empty( $order_note ) ) {
				$order->add_order_note( $order_note );
			}

			return $is_order_item_amazon_fulfill;
		}

		/**
		 * Gets WC_Product by SKU
		 *
		 * @param string $sku The product SKU.
		 */
		public function get_product_by_sku( $sku ) {
			$product_id = wc_get_product_id_by_sku( $sku );

			// phpcs:ignore
			//error_log( "Product ID for SKU " . $sku . " is: " . $product_id );

			// Product ID = 0 | FALSE if there is no product matching on SKU.
			if ( $product_id ) {
				$product = wc_get_product( $product_id );
				// phpcs:ignore
				//error_log( "Product object has SKU: " . $product->get_sku() );
				return $product;
			}
			return null;
		}

		/**
		 * Delete old logs.
		 *
		 * @return int The total files deleted
		 */
		public function delete_older_logs() {
			$days          = $this->ns_fba->options['ns_fba_clean_logs_interval'] ? (int) $this->ns_fba->options['ns_fba_clean_logs_interval'] : 30;
			$path_dir      = $this->ns_fba->plugin_path . 'logs/';
			$files_deleted = 0;
			// Open the directory.
			$handle = opendir( $path_dir );
			if ( $handle ) {
				// Loop through the directory.
				while ( false !== ( $file = readdir( $handle ) ) ) {
					// Check the file we're doing is actually a file.
					if ( is_file( $path_dir . $file ) ) {
						// Check if the file is older than X days old.
						if ( filemtime( $path_dir . $file ) < ( time() - ( $days * 24 * 60 * 60 ) ) ) {
							// Do the deletion.
							unlink( $path_dir . $file );
							$files_deleted++;
						}
					}
				}
			}
			return $files_deleted;
		}


		/**
		 * Check if the string is within bounds of acceptable length.
		 *
		 * @param  string  $string Argument to limit.
		 * @param  integer $limit Limit size in characters. Defaults to 40.
		 *
		 * @return bool Return true or false.
		 */
		public static function is_valid_length( $string, $limit = 40 ) {
			if ( function_exists( 'mb_strlen' ) && ( mb_strlen( $string ) > $limit ) ) {
				return false;
			} elseif ( strlen( $string ) > $limit ) {
				return false;
			}

			return true;
		}

	} // class.
}
