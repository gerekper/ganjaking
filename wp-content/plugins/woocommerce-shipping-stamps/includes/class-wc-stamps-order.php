<?php
/**
 * Order handler class.
 *
 * @package WC_Stamps_Integration
 */

include_once 'trait-order-util.php';

use WooCommerce\Stamps\Order_Util;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Order handler.
 *
 * Controls the meta box shown on orders in admin. Used for printing labels and
 * address verification.
 */
class WC_Stamps_Order {

	use Order_Util;

	/**
	 * Package types.
	 *
	 * @var array
	 */
	private $package_types = array();

	/**
	 * Constructor.
	 *
	 * Set WP hooks and basic props.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'wp_ajax_wc_stamps_verify_address', array( $this, 'ajax_verify_address' ) );
		add_action( 'wp_ajax_wc_stamps_override_address', array( $this, 'ajax_override_address' ) );
		add_action( 'wp_ajax_wc_stamps_accept_address', array( $this, 'ajax_accept_address' ) );
		add_action( 'wp_ajax_wc_stamps_get_rates', array( $this, 'ajax_get_rates' ) );
		add_action( 'wp_ajax_wc_stamps_define_package', array( $this, 'ajax_define_package' ) );
		add_action( 'wp_ajax_wc_stamps_customs', array( $this, 'ajax_customs' ) );
		add_action( 'wp_ajax_wc_stamps_request_label', array( $this, 'ajax_request_label' ) );
		add_action( 'wp_ajax_wc_stamps_get_labels', array( $this, 'ajax_get_labels' ) );
		add_action( 'wp_ajax_wc_stamps_cancel_label', array( $this, 'ajax_cancel_label' ) );
		add_action( 'wp_ajax_wc_stamps_delete_label', array( $this, 'ajax_delete_label' ) );

		$this->package_types = array(
			'Postcard'                  => array(
                'name'        => 'Postcard',
                'description' => ''
            ),
			'Letter'                    => array(
                'name'        => 'Letter',
                'description' => ''
            ),
			'Large Envelope or Flat'    => array(
                'name'        => 'Large Envelope or Flat',
                'description' => __( 'Large envelope or flat. Has one dimension that is between 11 1/2" and 15" long, 6 1/8" and 12" high, or 1/4" and 3/4 thick.', 'woocommerce-shipping-stamps' )
            ),
			'Thick Envelope'            => array(
                'name'        => 'Thick Envelope',
                'description' => __( 'Thick envelope. Envelopes or flats greater than 3/4" at the thickest point.', 'woocommerce-shipping-stamps' )
            ),
			'Package'                   => array(
                'name'        => 'Package',
                'description' => __( 'Package. Longest side plus the distance around the thickest part is less than or equal to 84"', 'woocommerce-shipping-stamps' )
            ),
			'Small Flat Rate Box'       => array(
                'name'        => 'Small Flat Rate Box',
                'description' => __( 'USPS small flat rate box. A special 8-5/8" x 5-3/8" x 1-5/8" USPS box that clearly indicates "Small Flat Rate Box".', 'woocommerce-shipping-stamps' )
            ),
			'Flat Rate Box'             => array(
                'name'        => 'Medium Flat Rate Box',
                'description' => __( 'USPS medium flat rate box. A special 11" x 8 1/2" x 5 1/2" or 14" x 3.5" x 12" USPS box that clearly indicates "Medium Flat Rate Box"', 'woocommerce-shipping-stamps' )
            ),
			'Large Flat Rate Box'       => array(
                'name'        => 'Large Flat Rate Box',
                'description' => __( 'USPS large flat rate box. A special 12" x 12" x 6" USPS box that clearly indicates "Large Flat Rate Box".', 'woocommerce-shipping-stamps' )
            ),
			'Flat Rate Envelope'        => array(
                'name'        => 'Flat Rate Envelope',
                'description' => __( 'USPS flat rate envelope. A special cardboard envelope provided by the USPS that clearly indicates "Flat Rate".', 'woocommerce-shipping-stamps' )
            ),
			'Flat Rate Padded Envelope' => array(
                'name'        => 'Flat Rate Padded Envelope',
                'description' => __( 'USPS flat rate padded envelope.', 'woocommerce-shipping-stamps' )
            ),
			'Large Package'             => array(
                'name'        => 'Large Package',
                'description' => __( 'Large package. Longest side plus the distance around the thickest part is over 84" and less than or equal to 108".', 'woocommerce-shipping-stamps' )
            ),
			'Oversized Package'         => array(
                'name'        => 'Oversized Package',
                'description' => __( 'Oversized package. Longest side plus the distance around the thickest part is over 108" and less than or equal to 130".', 'woocommerce-shipping-stamps' )
            ),
			'Regional Rate Box A'       => array(
                'name'        => 'Regional Rate Box A',
                'description' => __( 'USPS regional rate box A. A special 10 15/16" x 2 3/8" x 12 13/ 16" or 10" x 7" x 4 3/4" USPS box that clearly indicates "Regional Rate Box A". 15 lbs maximum weight.', 'woocommerce-shipping-stamps' )
            ),
			'Regional Rate Box B'       => array(
                'name'        => 'Regional Rate Box B',
                'description' => __( 'USPS regional rate box B. A special 14 3/8" x 2 2/8" x 15 7/8" or 12" x 10 1/4" x 5" USPS box that clearly indicates "Regional Rate Box B". 20 lbs maximum weight.', 'woocommerce-shipping-stamps' )
            ),
			'Legal Flat Rate Envelope'  => array(
                'name'        => 'Legal Flat Rate Envelope',
                'description' => __( 'USPS flat rate padded envelope.', 'woocommerce-shipping-stamps' )
            ),
		);
	}

	/**
	 * Enqueue styles.
	 */
	public function styles() {
		wp_enqueue_style( 'wc_stamps_admin_css', plugins_url( 'assets/css/admin.css', WC_STAMPS_INTEGRATION_FILE ), array(), WC_STAMPS_INTEGRATION_VERSION );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 1.3.4
	 * @version 1.3.4
	 */
	public function scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'wc_stamps_clipboard_js', plugins_url( 'assets/js/clipboard' . $suffix . '.js', WC_STAMPS_INTEGRATION_FILE ), array(), WC_STAMPS_INTEGRATION_VERSION );
		wp_enqueue_script( 'wc_stamps_admin_js', plugins_url( 'assets/js/admin' . $suffix . '.js', WC_STAMPS_INTEGRATION_FILE ), array( 'jquery', 'wc_stamps_clipboard_js' ), WC_STAMPS_INTEGRATION_VERSION );
		wp_localize_script(
			'wc_stamps_admin_js',
			'wc_stamps_admin',
			array(
				'copy_to_clipboard_fallback_i18n' => __( 'Please press Ctrl/Cmd+C to copy.', 'woocommerce-shipping-stamps' ),
			)
		);
	}

	/**
	 * Add meta boxes.
	 */
	public function add_meta_boxes() {
		add_meta_box( 'wc_stamps_get_label', __( 'Shipping Labels', 'woocommerce-shipping-stamps' ), array( $this, 'output' ), $this->get_order_admin_screen(), 'side' );
	}

	/**
	 * Verify an address.
	 */
	public function ajax_verify_address() {
		check_ajax_referer( 'stamps', 'security' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$order  = wc_get_order( absint( $_POST['order_id'] ) );
		$result = WC_Stamps_API::verify_address( $order );

		if ( is_wp_error( $result ) ) {
			wp_send_json( array( 'error' => $result->get_error_message() ) );
		} else {
			$result_hash  = isset( $result['hash'] ) ? $result['hash'] : '';
			$overide_hash = isset( $result['overide_hash'] ) ? $result['overide_hash'] : '';

			$order->update_meta_data( '_stamps_response', $result );
			$order->update_meta_data( '_stamps_hash', $result_hash );
			$order->update_meta_data( '_stamps_override_hash', $overide_hash );
			$order->save();

			wp_send_json( array( 'html' => $this->get_address_verification_result_html( $result ) ) );
		}
	}

	/**
	 * Override address - verification complete.
	 */
	public function ajax_override_address() {
		check_ajax_referer( 'stamps', 'security' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$order = wc_get_order( absint( $_POST['order_id'] ) );

		// To indicate that the merchant has elected to "continue without changes"
		// we will overwrite the CleanseHash (if any) in _stamps_hash with the
		// OverrideHash.
		//
		// Then, in WC_Stamps_API::purchase_label, we'll see that has happened and we will
		// send the OverrideHash field in the request instead of CleanseHash.
		$overide_hash          = $order->get_meta( '_stamps_override_hash' );
		$verified_address_hash = md5( $order->get_formatted_shipping_address() );

		$order->update_meta_data( '_stamps_hash', $overide_hash );
		$order->update_meta_data( '_stamps_verified_address_hash', $verified_address_hash );
		$order->save();

		wp_send_json( array( 'reload' => true ) );
	}

	/**
	 * Accept address - verification complete.
	 *
	 * This is AJAX handler for wc_stamps_accept_address action.
	 */
	public function ajax_accept_address() {
		check_ajax_referer( 'stamps', 'security' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$order = wc_get_order( absint( $_POST['order_id'] ) );

		// Update address to stamps version.
		$result = $order->get_meta( '_stamps_response' );

		$shipping_name  = explode( ' ', $result['address']['full_name'] );
		$shipping_last  = array_pop( $shipping_name );
		$shipping_first = implode( ' ', $shipping_name );

		$order->set_shipping_first_name( $shipping_first );
		$order->set_shipping_last_name( $shipping_last );
		$order->set_shipping_company( $result['address']['company'] );
		$order->set_shipping_address_1( $result['address']['address_1'] );
		$order->set_shipping_address_2( $result['address']['address_2'] );
		$order->set_shipping_city( $result['address']['city'] );
		$order->set_shipping_state( $result['address']['state'] );
		$order->set_shipping_postcode( $result['address']['postcode'] );

		if ( ! empty( $result['address']['country'] ) ) {
			$order->set_shipping_country( $result['address']['country'] );
		}

		$formatted_shipping_address_hash = md5( $order->get_formatted_shipping_address() );

		$order->update_meta_data( '_stamps_verified_address_hash', $formatted_shipping_address_hash );
		$order->save();

		wp_send_json( array( 'reload' => true ) );
	}

	/**
	 * Define packages.
	 *
	 * This is AJAX handler for wc_stamps_define_package action.
	 */
	public function ajax_define_package() {
		check_ajax_referer( 'stamps', 'security' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$order = wc_get_order( absint( $_POST['order_id'] ) );
		wp_send_json( array( 'html' => $this->get_packages_html( $order ), 'step' => 'rates' ) );
	}

	/**
	 * Get rates from stamps.
	 *
	 * This is AJAX handler for wc_stamps_get_rates action.
	 */
	public function ajax_get_rates() {
		check_ajax_referer( 'stamps', 'security' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$order  = wc_get_order( absint( $_POST['order_id'] ) );
		$params = array();
		parse_str( stripslashes( sanitize_text_field( $_POST['data'] ) ), $params );

		$rates = WC_Stamps_API::get_rates( $order, array(
			'date'   => sanitize_text_field( $params['stamps_package_date'] ),
			'type'   => sanitize_text_field( $params['stamps_package_type'] ),
			'weight' => wc_get_weight( wc_format_decimal( (float) $params['stamps_package_weight'] ), 'lbs' ),
			'value'  => wc_format_decimal( (float) $params['stamps_package_value'] ),
			'length' => wc_get_dimension( wc_format_decimal( (float) $params['stamps_package_length'] ), 'in' ),
			'width'  => wc_get_dimension( wc_format_decimal( (float) $params['stamps_package_width'] ), 'in' ),
			'height' => wc_get_dimension( wc_format_decimal( (float) $params['stamps_package_height'] ), 'in' ),
		) );

		if ( is_wp_error( $rates ) ) {
			wp_send_json( array( 'error' => $rates->get_error_message() ) );
		} else {
			wp_send_json( array( 'html' => $this->get_rates_html( $order, $rates ), 'step' => 'rates' ) );
		}
	}

	/**
	 * Get a posted rate object as well as addons.
	 *
	 * @param array $posted Posted data.
	 *
	 * @return array|false Stamps Rate in array format.
	 */
	public function get_posted_rate( $posted ) {
		if ( ! empty( $posted['stamps_rate'] ) ) {
			$rate = $posted['stamps_rate'];

			$rate          = json_decode( $rate );
			$rate_code     = md5( $rate->ServiceType . $rate->PackageType );
			$rate_addons   = isset( $posted[ 'rate-' . $rate_code ] ) ? array_map( 'wc_clean', array_keys( $posted[ 'rate-' . $rate_code ] ) ) : array();
			$chosen_addons = array();

			if ( isset( $rate->AddOns ) && isset( $rate->AddOns->AddOnV7 ) ) {
				foreach ( (array) $rate->AddOns->AddOnV7 as $key => $addon ) {
					if ( in_array( $addon->AddOnType, $rate_addons ) ) {
						$chosen_addons[] = array( 'AddOnType' => wc_clean( $addon->AddOnType ) );
					}
				}
			}
		} elseif ( ! empty( $posted['parsed_rate'] ) ) { // This rate has been processed already.
			$rate = stripslashes( $posted['parsed_rate'] );

			$rate          = json_decode( $rate );
			$chosen_addons = isset( $rate->AddOns->AddOnV7 ) ? $rate->AddOns->AddOnV7 : array();
		} else {
			return false;
		}

		// Put rate in array, keeping only the data we need.
		$posted_rates = array(
			'FromZIPCode'   => wc_clean( $rate->FromZIPCode ),
			'ToCountry'     => wc_clean( $rate->ToCountry ),
			'WeightLb'      => wc_clean( isset( $rate->WeightLb ) ? $rate->WeightLb : '' ),
			'WeightOz'      => wc_clean( isset( $rate->WeightOz ) ? $rate->WeightOz : '' ),
			'ShipDate'      => wc_clean( $rate->ShipDate ),
			'InsuredValue'  => isset( $rate->InsuredValue ) ? wc_clean( $rate->InsuredValue ) : '',
			'CODValue'      => isset( $rate->CODValue ) ? wc_clean( $rate->CODValue ) : '',
			'DeclaredValue' => isset( $rate->DeclaredValue ) ? wc_clean( $rate->DeclaredValue ) : '',
			'Length'        => wc_clean( isset( $rate->Length ) ? $rate->Length : '' ),
			'Width'         => wc_clean( isset( $rate->Width ) ? $rate->Width : '' ),
			'Height'        => wc_clean( isset( $rate->Height ) ? $rate->Height : '' ),
			'PackageType'   => wc_clean( $rate->PackageType ),
			'ServiceType'   => wc_clean( $rate->ServiceType ),
			'PrintLayout'   => get_option( 'wc_settings_stamps_print_layout', 'Normal' ),
			'AddOns'        => array(
				'AddOnV7' => $chosen_addons,
			),
		);

		if ( ! empty( $rate->ToZIPCode ) ) {
			$posted_rates['ToZIPCode'] = wc_clean( $rate->ToZIPCode );
		}

		return $posted_rates;
	}

	/**
	 * Get chosen rate and ask for customs information.
	 *
	 * This is AJAX handler for wc_stamps_customs action.
	 */
	public function ajax_customs() {
		check_ajax_referer( 'stamps', 'security' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$order  = wc_get_order( absint( $_POST['order_id'] ) );
		$params = array();
		parse_str( stripslashes( sanitize_text_field( $_POST['data'] ) ), $params );

		wp_send_json( array( 'html' => $this->get_customs_html( $order, $this->get_posted_rate( $params ) ), 'step' => 'customs' ) );
	}

	/**
	 * Get label for a rate.
	 *
	 * This is AJAX handler for wc_stamps_request_label action.
	 */
	public function ajax_request_label() {
		check_ajax_referer( 'stamps', 'security' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$order  = wc_get_order( absint( $_POST['order_id'] ) );
		$params = array();
		parse_str( stripslashes( sanitize_text_field( $_POST['data'] ) ), $params );

		if ( ! empty( $params['stamps_customs_content_type'] ) ) {
			$customs = array(
				'ContentType'       => sanitize_text_field( $params['stamps_customs_content_type'] ),
				'Comments'          => sanitize_text_field( $params['stamps_customs_comments'] ),
				'LicenceNumber'     => sanitize_text_field( $params['stamps_customs_licence'] ),
				'CertificateNumber' => sanitize_text_field( $params['stamps_customs_certificate'] ),
				'InvoiceNumber'     => $order->get_order_number(),
				'OtherDescribe'     => sanitize_text_field( $params['stamps_customs_other'] ),
				'CustomsLines'      => array(),
			);

			if ( ! empty( $params['stamps_customs_item_description'] ) ) {
				foreach ( $params['stamps_customs_item_description'] as $key => $desc ) {
					$line = array(
						'Description'     => sanitize_text_field( $desc ),
						'Quantity'        => absint( $params['stamps_customs_item_quantity'][ $key ] ),
						'Value'           => wc_format_decimal( $params['stamps_customs_item_value'][ $key ] ),
						'WeightLb'        => wc_format_decimal( $params['stamps_customs_item_weight'][ $key ] ),
						'HSTariffNumber'  => sanitize_text_field( $params['stamps_customs_item_hs_tariff'][ $key ] ),
						'CountryOfOrigin' => sanitize_text_field( $params['stamps_customs_item_origin'][ $key ] ),
					);
					if ( empty( $line['HSTariffNumber'] ) ) {
						unset( $line['HSTariffNumber'] );
					}
					if ( empty( $line['CountryOfOrigin'] ) ) {
						unset( $line['CountryOfOrigin'] );
					}
					$customs['CustomsLines'][] = $line;
				}
			}
		} else {
			$customs = false;
		}

		$rate = $this->get_posted_rate( $params );
		if ( $rate ) {
			$label = WC_Stamps_API::purchase_label( $order, array( 'rate' => $rate, 'customs' => $customs ) );
		} else {
			$label = new WP_Error( 'stamps', 'No rate posted' );
		}

		if ( is_wp_error( $label ) ) {
			wp_send_json( array( 'error' => $label->get_error_message() ) );
		} else {
			wp_send_json( array( 'html' => $this->get_label_html( $label ), 'step' => 'labels' ) );
		}
	}

	/**
	 * Get labels.
	 *
	 * This is AJAX handler for wc_stamps_get_labels action.
	 */
	public function ajax_get_labels() {
		check_ajax_referer( 'stamps', 'security' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$order  = wc_get_order( absint( $_POST['order_id'] ) );
		$order_id = $order->get_id();
		$labels = WC_Stamps_Labels::get_order_labels( $order_id );

		wp_send_json( array( 'html' => $this->get_labels_html( $labels ), 'step' => 'labels' ) );
	}

	/**
	 * Cancel/refund a label.
	 *
	 * This is AJAX handler for wc_stamps_cancel_label action.
	 */
	public function ajax_cancel_label() {
		check_ajax_referer( 'stamps', 'security' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$order    = wc_get_order( absint( $_POST['order_id'] ) );
		$cancel   = false;
		$label_id = absint( $_POST['action_id'] );

		if ( ! empty( $label_id ) ) {
			$cancel = WC_Stamps_API::cancel_label( $order, get_post_meta( $label_id, 'StampsTxID', true ) );
		}

		if ( is_wp_error( $cancel ) ) {
			wp_send_json( array( 'error' => $cancel->get_error_message() ) );
		} else {
			WC_Stamps_Labels::delete_label( $label_id );
			$labels = WC_Stamps_Labels::get_order_labels( $order->get_id() );
			ob_start();
			echo '<div class="success updated"><p>' . esc_html__( 'The label was refunded. Refund requests are generally processed within 1 to 2 weeks.', 'woocommerce-shipping-stamps' ) . '</p></div>';
      // Skip escaping since we're outputting HTML and values are already escaped.
      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->get_labels_html( $labels );
			wp_send_json( array( 'html' => ob_get_clean(), 'step' => 'labels' ) );
		}
	}

	/**
	 * Delete a label.
	 *
	 * This is AJAX handler for wc_stamps_delete_label action.
	 */
	public function ajax_delete_label() {
		check_ajax_referer( 'stamps', 'security' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die();
		}

		$order    = wc_get_order( absint( $_POST['order_id'] ) );
		$label_id = absint( $_POST['action_id'] );

		WC_Stamps_Labels::delete_label( $label_id );
		$labels = WC_Stamps_Labels::get_order_labels( $order->get_id() );
		wp_send_json( array( 'html' => $this->get_labels_html( $labels ), 'step' => 'labels' ) );
	}

	/**
	 * Address verification html - step 1.
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @return string
	 */
	public function get_address_verification_html( $order ) {
		ob_start();
		include( 'views/html-address-verification.php' );
		return ob_get_clean();
	}

	/**
	 * Get address verification html based on stored results.
	 *
	 * @param array $result Result from Stamps API.
	 */
	public function get_address_verification_result_html( $result ) {
		ob_start();
		include( 'views/html-address-verification-result.php' );
		return ob_get_clean();
	}

	/**
	 * Get html for defining packages.
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @return string
	 */
	public function get_packages_html( $order ) {
		ob_start();

		$total_weight = 0;
		$total_cost   = 0;

		// Calculate the package weight from shipping item meta.
		foreach ( $order->get_items( 'shipping' ) as $item_id => $item ) {
			if ( ! empty( $item->get_meta( '_package_weight' ) ) ) {
				$package_weights = json_decode( $item->get_meta( '_package_weight' ), true );

				foreach ( $package_weights as $package_name => $weight ) {
					$total_weight += floatval( $weight );
				}
			}
		}

		$products_weight = 0;

		foreach ( $order->get_items() as $item_id => $item ) {
			if ( is_callable( array( $item, 'get_product' ) ) ) {
				$product = $item->get_product();
			} else {
				$product = $order->get_product_from_item( $item );
			}

			if ( ! is_a( $product, 'WC_Product' ) ) {
				continue;
			}

			if ( ! is_callable( array( $product, 'needs_shipping' ) ) ) {
				continue;
			}

			if ( ! $product->needs_shipping() ) {
				continue;
			}

			if ( ! $product->get_weight() ) {
				$weight = 0;
			} else {
				$weight = $product->get_weight();
			}

			$products_weight += $weight * $item['qty'];
			$total_cost      += $item->get_subtotal();
		}

		// Use product weight if _package_weight cannot be found in order item shipping.
		if ( empty( $total_weight ) ) {
			$total_weight = $products_weight;
		}

		$num_days = absint( get_option( 'wc_settings_stamps_shipping_date', 1 ) );
		$ship_date = date( "Y-m-d", current_time( 'timestamp' ) + ( $num_days * DAY_IN_SECONDS ) );

		include( 'views/html-package.php' );
		return ob_get_clean();
	}

	/**
	 * Get html for listing rates.
	 *
	 * @param WC_Order $order Order object.
	 * @param array    $rates Rates from Stamps API.
	 *
	 * @return string
	 */
	public function get_rates_html( $order, $rates ) {
		ob_start();
		include( 'views/html-rates.php' );
		return ob_get_clean();
	}

	/**
	 * Output html for listing addons.
	 *
	 * @param object $rate Rate.
	 */
	public static function addons_html( $rate ) {
		$raw_addons = $rate->rate_object->AddOns->AddOnV7;
		$rate_code  = md5( $rate->rate_object->ServiceType . $rate->rate_object->PackageType );
		$addons     = array();

		// Build array of addons.
		foreach ( $raw_addons as $addon ) {
			$addons[ $addon->AddOnType ]['addon']      = $addon;
			$addons[ $addon->AddOnType ]['sub_addons'] = array();
		}

		// RequiresAllOf.
		foreach ( $raw_addons as $addon ) {
			if ( ! empty( $addon->RequiresAllOf ) ) {
				if ( is_array( $addon->RequiresAllOf->RequiresOneOf->AddOnTypeV7 ) ) {
					foreach ( $addon->RequiresAllOf->RequiresOneOf->AddOnTypeV7 as $required_addon ) {
						unset( $addons[ $addon->AddOnType ] );
						if ( isset( $addons[ $required_addon ] ) ) {
							$addons[ $required_addon ]['sub_addons'][ $addon->AddOnType ] = $addon;
						}
					}
				} else {
					unset( $addons[ $addon->AddOnType ] );
					if ( isset( $addons[ $addon->RequiresAllOf->RequiresOneOf->AddOnTypeV7 ] ) ) {
						$addons[ $addon->RequiresAllOf->RequiresOneOf->AddOnTypeV7 ]['sub_addons'][ $addon->AddOnType ] = $addon;
					}
				}
			}
		}

		// Output in nested list!
		echo '<ul>';
		foreach ( $addons as $addon_key => $addon ) {
			$disable_addons = array();
			if ( ! empty( $addon['addon']->ProhibitedWithAnyOf ) && ! empty( $addon['addon']->ProhibitedWithAnyOf->AddOnTypeV7 ) ) {
				foreach ( array( $addon['addon']->ProhibitedWithAnyOf->AddOnTypeV7 ) as $prohibited_addon_key ) {
					if ( is_array( $prohibited_addon_key ) ) {
						$disable_addons[] = array_map( 'trim', $prohibited_addon_key );
					} else {
						$disable_addons[] = trim( $prohibited_addon_key );
					}
				}
			}

			$disable_addons = wp_json_encode( $disable_addons );
			$disable_addons = function_exists( 'wc_esc_json' ) ? wc_esc_json( $disable_addons ) : _wp_specialchars( $disable_addons, ENT_QUOTES, 'UTF-8', true );

			echo '<li><label><input type="checkbox" name="' . esc_attr( 'rate-' . $rate_code . '[' . $addon_key . ']' ) . '" data-type="' . esc_attr( $addon_key ) . '" data-disable_addons="' . esc_attr( $disable_addons ) . '" /> ' . esc_html( WC_Stamps_API::get_addon_type_name( $addon['addon']->AddOnType ) . ( isset( $addon['addon']->Amount ) ? ' (' . strip_tags( wc_price( $addon['addon']->Amount ) ) . ')' : '' ) ) . '</label>';

			if ( ! empty( $addon['sub_addons'] ) ) {
				echo '<ul style="display:none">';

				foreach ( $addon['sub_addons'] as $sub_addon_key => $sub_addon ) {
					$disable_addons = array();
					if ( ! empty( $sub_addon->ProhibitedWithAnyOf ) && ! empty( $sub_addon->ProhibitedWithAnyOf->AddOnTypeV7 ) ) {
						foreach ( array( $sub_addon->ProhibitedWithAnyOf->AddOnTypeV7 ) as $prohibited_addon_key ) {
							if ( is_array( $prohibited_addon_key ) ) {
								$disable_addons[] = array_map( 'trim', $prohibited_addon_key );
							} else {
								$disable_addons[] = trim( $prohibited_addon_key );
							}
						}
					}

					$disable_addons = wp_json_encode( $disable_addons );
					$disable_addons = function_exists( 'wc_esc_json' ) ? wc_esc_json( $disable_addons ) : _wp_specialchars( $disable_addons, ENT_QUOTES, 'UTF-8', true );

					echo '<li><label><input type="checkbox" name="' . esc_attr( 'rate-' . $rate_code . '[' . $sub_addon_key . ']' ) . '" data-type="' . esc_attr( $sub_addon_key ) . '" data-disable_addons="' . esc_attr( $disable_addons ) . '" /> ' . esc_html( WC_Stamps_API::get_addon_type_name( $sub_addon->AddOnType ) . ( isset( $sub_addon->Amount ) ? ' (' . strip_tags( wc_price( $sub_addon->Amount ) ) . ')' : '' ) ) . '</label></li>';
				}

				echo '</ul>';
			}
			echo '</li>';
		}
		echo '</ul>';
	}

	/**
	 * Get HTML for customs information.
	 *
	 * @param WC_Order $order       Order object.
	 * @param array    $stamps_rate Rates from Stamps API.
	 *
	 * @return string
	 */
	public function get_customs_html( $order, $stamps_rate ) {
		ob_start();
		include( 'views/html-customs.php' );
		return ob_get_clean();
	}

	/**
	 * Get HTML for listing labels.
	 *
	 * @param WP_Error|WC_Stamps_Label $label Label or WP_Error.
	 *
	 * @return string
	 */
	public function get_label_html( $label ) {
		ob_start();

		if ( is_wp_error( $label ) ) {
			echo '<div class="error"><p>' . esc_html( $label->get_error_message() ) . '</p></div>';
			echo '<p><button type="submit" class="button stamps-action" data-stamps_action="define_package">' . esc_attr__( 'Try again', 'woocommerce-shipping-stamps' ) . '</button></p>';
		} else {
			include( 'views/html-label.php' );
			echo '<p><button type="submit" class="button stamps-action" data-stamps_action="get_labels">' . esc_attr__( 'View all labels', 'woocommerce-shipping-stamps' ) . '</button></p>';
		}

		return ob_get_clean();
	}

	/**
	 * Get HTML for listing labels.
	 *
	 * @param array $labels List of labels of an order.
	 *
	 * @return string
	 */
	public function get_labels_html( $labels ) {
		ob_start();
		include( 'views/html-labels.php' );
		return ob_get_clean();
	}

	/**
	 * Output the meta box.
	 *
	 * @param $post_or_order_object
	 *
	 * @return void
	 */
	public function output( $post_or_order_object ) {
		$order = $this->init_theorder_object( $post_or_order_object );

		$step         = 'address';
		$address_hash = $order->get_meta( '_stamps_verified_address_hash' );

		if ( $address_hash && md5( $order->get_formatted_shipping_address() ) === $address_hash ) {
			$step = 'rates';
		}

		$labels = WC_Stamps_Labels::get_order_labels( $order->get_id() );
		if ( $labels && sizeof( $labels ) > 0 ) {
			$step = 'labels';
		}

		include( 'views/html-meta-box.php' );
	}

	/**
	 * Check whether a given order needs customs step to print label.
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @return bool Returns true if given country and/or state in an order needs custom step.
	 * @see https://github.com/woocommerce/woocommerce-shipping-stamps/issues/73.
	 *
	 * @version 1.3.3
	 *
	 * @since 1.3.3
	 */
	public function needs_customs_step( $order ) {
		// Non US countries will require customs step.
		if ( 'US' !== $order->get_shipping_country() ) {
			return true;
		}

		// Army address will require customs step.
		if ( in_array( $order->get_shipping_state(), array( 'AA', 'AE', 'AP' ) ) ) {
			return true;
		}

		return false;
	}
}
new WC_Stamps_Order();
