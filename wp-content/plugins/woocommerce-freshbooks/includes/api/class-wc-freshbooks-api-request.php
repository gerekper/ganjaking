<?php
/**
 * WooCommerce FreshBooks
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce FreshBooks to newer
 * versions in the future. If you wish to customize WooCommerce FreshBooks for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-freshbooks/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;


/**
 * FreshBooks API Request Class
 *
 * Generates XML to perform an API request
 *
 * @link http://developers.freshbooks.com/
 *
 * @since 3.0
 * @extends XMLWriter
 */
class WC_FreshBooks_API_Request extends \XMLWriter implements Framework\SV_WC_API_Request {


	/** @var array request data prior to conversion to XML */
	private $request_data;

	/** @var string request xml */
	private $request_xml;

	/** @var \WC_FreshBooks_Order optional order object if this request was associated with an order */
	private $order;


	/**
	 * Construct request object
	 *
	 * @since 3.0
	 */
	public function __construct() {

		// Create XML document in memory
		$this->openMemory();

		// Set XML version & encoding
		$this->startDocument( '1.0', 'UTF-8' );
	}


	/** Invoice methods ******************************************************/


	/**
	 * Create an invoice for the given order
	 *
	 * @link http://developers.freshbooks.com/docs/invoices/#invoice.create
	 *
	 * @param \WC_FreshBooks_Order $order order instance
	 * @since 3.0
	 */
	public function create_invoice( WC_FreshBooks_Order $order ) {

		// store the order object for later use
		$this->order = $order;

		$this->request_data = array(
			'request' => array(
				'@attributes' => array( 'method' => 'invoice.create' ),
				'invoice'     => $this->get_invoice_data( $order ),
			),
		);

		// set invoice number to order number if enabled
		if ( 'yes' === get_option( 'wc_freshbooks_use_order_number' ) ) {

			$invoice_number = ltrim( $order->get_order_number(), _x( '#', 'hash before the order number', 'woocommerce-freshbooks' ) );

			// add optional prefix
			$invoice_number = get_option( 'wc_freshbooks_invoice_number_prefix' ) . $invoice_number;

			$this->request_data['request']['invoice']['number'] = $invoice_number;
		}
	}


	/**
	 * Update an invoice for the given order
	 *
	 * @link http://developers.freshbooks.com/docs/invoices/#invoice.update
	 *
	 * @param \WC_FreshBooks_Order $order order instance
	 * @since 3.2.0
	 */
	public function update_invoice( WC_FreshBooks_Order $order ) {

		// store the order object for later use
		$this->order = $order;

		// set base data
		$this->request_data = array(
			'request' => array(
				'@attributes' => array( 'method' => 'invoice.update' ),
				'invoice'     => $this->get_invoice_data( $order ),
			),
		);

		// set required data
		// TODO: Replace the rest of WC_FreshBooks_Order props with getters {BR 2017-02-25}
		$this->request_data['request']['invoice']['invoice_id'] = $order->invoice_id;
		$this->request_data['request']['invoice']['status'] = $order->get_invoice_status();
	}


	/**
	 * Get the invoice data for an order
	 *
	 * @param \WC_FreshBooks_Order $order order instance
	 * @return array invoice data
	 * @since 3.2.0
	 */
	private function get_invoice_data( WC_FreshBooks_Order $order ) {

		$invoice_data = [
			'client_id'     => $order->invoice_client_id,
			'status'        => 'draft',
			'date'          => $order->get_date_created( 'edit' )->date( 'Y-m-d' ),
			'currency_code' => $order->get_currency(),
			'language'      => get_option( 'wc_freshbooks_invoice_language', 'en' ),
			'first_name'    => $order->get_billing_first_name( 'edit' ),
			'last_name'     => $order->get_billing_last_name( 'edit' ),
			'organization'  => $order->get_billing_company( 'edit' ),
			'p_street1'     => $order->get_billing_address_1( 'edit' ),
			'p_street2'     => $order->get_billing_address_2( 'edit' ),
			'p_city'        => $order->get_billing_city( 'edit' ),
			'p_state'       => $order->get_billing_state( 'edit' ),
			'p_country'     => $this->get_full_country_name( $order->get_billing_country( 'edit' ) ),
			'p_code'        => $order->get_billing_postcode( 'edit' ),
			'lines'         => [ 'line' => $this->get_invoice_lines( $order ) ],
		];

		// set return URL to view order URL if registered customer
		$user_id = $order->get_user_id();

		if ( ! empty( $user_id ) ) {

			$this->request_data['request']['invoice']['return_uri'] = $order->get_view_order_url();
		}

		/**
		 * Set VAT number if one exists for the order
		 *
		 * @since 3.10.0
		 * @param array $vat_key vat number meta keys
		 * @param \WC_FreshBooks_Order instance of a FreshBooks order
		 */
		$vat_number_meta_keys = apply_filters( 'wc_freshbooks_vat_meta_keys', array(
			'VAT Number',
			'vat_number',                // Taxamo
			'_billing_wc_avatax_vat_id', // AvaTax
		), $order );

		foreach ( $vat_number_meta_keys as $meta_key ) {

			$meta_data_value = $order->get_meta( $meta_key, true );

			if ( ! empty( $meta_data_value ) ) {

				$this->request_data['request']['client']['vat_name']   = 'VAT Number';
				$this->request_data['request']['client']['vat_number'] = $meta_data_value;

				break;
			}
		}

		return $invoice_data;
	}


	/**
	 * Backwards compatible method to display meta in a formatted list.
	 *
	 * @see \WC_FreshBooks_API_Request::get_invoice_lines()
	 *
	 * @since 3.10.1
	 *
	 * @param array $formatted_meta
	 * @return string
	 */
	private function display_item_meta( $formatted_meta ) {

		$output = '';

		if ( ! empty( $formatted_meta ) ) {

			$meta_list = array();

			foreach ( $formatted_meta as $meta ) {
				// FreshBooks does not allow for HTML
				$meta_list[] = wp_strip_all_tags( $meta->display_key . ': ' . $meta->display_value );
			}

			if ( ! empty( $meta_list ) ) {
				$output .= implode( ', ' , $meta_list );
			}
		}

		return $output;
	}


	/**
	 * Get the invoice line items for a given order. This includes:
	 *
	 * + Products, mapped as FreshBooks items when the admin has linked them
	 * + Fees, mapped as a `Fee` item
	 * + Shipping, mapped as a `Shipping` item
	 * + Taxes, mapped using the tax code as the item
	 *
	 * Note that taxes cannot be added per-product as WooCommerce doesn't provide
	 * any way to get individual tax information per product and FreshBooks requires
	 * a tax name/percentage to be set on a per-product basis. Further, FreshBooks
	 * only allows 2 taxes per product where realistically most stores will have
	 * more
	 *
	 * @param \WC_FreshBooks_Order $order order instance
	 * @return array line items
	 * @since 3.0
	 */
	private function get_invoice_lines( WC_FreshBooks_Order $order ) {

		$line_items = array();

		// add products
		foreach ( $order->get_items() as $item_key => $item ) {

			$product = is_callable( array( $item, 'get_product' ) ) ? $item->get_product() : false;

			// must be a valid product
			if ( $product instanceof \WC_Product ) {

				$item_meta = $product->get_meta( '_wc_freshbooks_item_name', true );
				$item_name = ! empty( $item_meta ) ? $item_meta : $product->get_sku();

				// variation data, item meta, etc
				$meta = $this->display_item_meta( (array) $item->get_formatted_meta_data( '_', true ) );

				// grouped products include a &arr; in the name which must be converted back to an arrow
				$item_description = html_entity_decode( $product->get_title(), ENT_QUOTES, 'UTF-8' ) . ( $meta ? sprintf( ' (%s)', $meta ) : '' );

			} else {

				$item_name        = __( 'Product', 'woocommerce-freshbooks' );
				$item_description = $item['name'];
			}

			$line_items[] = array(
				'name'        => $item_name,
				'description' => apply_filters( 'wc_freshbooks_line_item_description', $item_description, $item, $order, $item_key ), // legacy (pre 3.0) filter
				'unit_cost'   => $this->get_item_unit_price( $order, $item ),
				'quantity'    => $item['qty'],
			);
		}

		$coupons = $order->get_items( 'coupon' );

		// add coupons
		foreach ( $coupons as $coupon_item ) {

			$coupon       = new \WC_Coupon( $coupon_item['name'] );
			$coupon_post  = get_post( $coupon->get_id() );
			$coupon_type  = ( false !== strpos( $coupon->get_discount_type(), '_cart' ) ) ? __( 'Cart Discount', 'woocommerce-freshbooks' ) : __( 'Product Discount', 'woocommerce-freshbooks' );
			$line_items[] = array(
				'name'        => $coupon_item['name'],
				/* translators: Placeholders: %1$s - coupon type, %2$s - coupon description */
				'description' => is_object( $coupon_post ) && $coupon_post->post_excerpt ? sprintf( __( '%1$s - %2$s', 'woocommerce-freshbooks' ), $coupon_type, $coupon_post->post_excerpt ) : $coupon_type,
				'unit_cost'   => Framework\SV_WC_Helper::number_format( $coupon_item['discount_amount'] * - 1 ),
				'quantity'    => 1,
			);
		}

		// manually created orders can't have coupon items, but may have an order discount set
		// which must be added as a line item
		if ( 0 === count( $coupons ) && $order->get_total_discount() > 0 ) {

			$line_items[] = array(
				'name'        => __( 'Order Discount', 'woocommerce-freshbooks' ),
				'description' => __( 'Order Discount', 'woocommerce-freshbooks' ),
				'unit_cost'   => Framework\SV_WC_Helper::number_format( $order->get_total_discount() * -1 ),
				'quantity'    => 1,
			);
		}

		// add fees
		foreach ( $order->get_fees() as $fee_key => $fee ) {

			$line_items[] = array(
				'name'        => __( 'Fee', 'woocommerce-freshbooks' ),
				'description' => $fee['name'],
				'unit_cost'   => $order->get_line_total( $fee ),
				'quantity'    => 1,
			);
		}

		// add shipping
		foreach ( $order->get_shipping_methods() as $shipping_method_key => $shipping_method ) {

			$line_items[] = array(
				'name'        => __( 'Shipping', 'woocommerce-freshbooks' ),
				'description' => ucwords( str_replace( '_', ' ', $shipping_method['method_id'] ) ),
				'unit_cost'   => $shipping_method['cost'],
				'quantity'    => 1,
			);
		}

		// add taxes
		if ( ! $this->use_tax_inclusive_price() ) {

			foreach ( $order->get_tax_totals() as $tax_code => $tax ) {

				$line_items[] = array(
					'name'        => $tax_code,
					'description' => $tax->label,
					'unit_cost'   => Framework\SV_WC_Helper::number_format( $tax->amount ),
					'quantity'    => 1,
				);
			}
		}

		return $line_items;
	}


	/**
	 * Get the invoice for the given invoice ID
	 *
	 * @link http://developers.freshbooks.com/docs/invoices/#invoice.get
	 *
	 * @param string $invoice_id FreshBooks invoice ID (not the invoice number)
	 * @since 3.0
	 */
	public function get_invoice( $invoice_id ) {

		$this->request_data = array(
			'request' => array(
				'@attributes' => array( 'method' => 'invoice.get' ),
				'invoice_id'  => $invoice_id,
			),
		);
	}


	/**
	 * Send the invoice for the given id and method
	 *
	 * @link http://developers.freshbooks.com/docs/invoices/#invoice.sendByEmail
	 * @link http://developers.freshbooks.com/docs/invoices/#invoice.sendBySnailMail
	 *
	 * @param string $invoice_id FreshBooks invoice ID (not the invoice number)
	 * @param string $method either `email` or `snail_mail`
	 * @since 3.0
	 */
	public function send_invoice( $invoice_id, $method ) {

		$this->request_data = array(
			'request' => array(
				'@attributes' => array( 'method' => 'invoice.' . ( 'snail_mail' === $method ? 'sendBySnailMail' : 'sendByEmail' ) ),
				'invoice_id' => $invoice_id,
			),
		);
	}


	/** Client methods ******************************************************/


	/**
	 * Create an invoice for the given order
	 *
	 * @link http://developers.freshbooks.com/docs/clients/#client.create
	 *
	 * @param \WC_FreshBooks_Order $order order instance
	 * @since 3.0
	 */
	public function create_client( WC_FreshBooks_Order $order ) {

		// store the order object for later use
		$this->order = $order;

		$this->request_data = [
			'request' => [
				'@attributes' => [
					'method' => 'client.create',
				],
				'client'      => [
					'first_name'    => $order->get_billing_first_name( 'edit' ),
					'last_name'     => $order->get_billing_last_name( 'edit' ),
					'organization'  => $order->get_billing_company( 'edit' ),
					'email'         => $order->get_billing_email( 'edit' ),
					'work_phone'    => $order->get_billing_phone( 'edit' ),
					'language'      => get_option( 'wc_freshbooks_invoice_language', 'en' ),
					'currency_code' => $order->get_currency(),
					'p_street1'     => $order->get_billing_address_1( 'edit' ),
					'p_street2'     => $order->get_billing_address_2( 'edit' ),
					'p_city'        => $order->get_billing_city( 'edit' ),
					'p_state'       => $order->get_billing_state( 'edit' ),
					'p_country'     => $this->get_full_country_name( $order->get_billing_country( 'edit' ) ),
					'p_code'        => $order->get_billing_postcode( 'edit' ),
					's_street1'     => $order->get_shipping_address_1( 'edit' ),
					's_street2'     => $order->get_shipping_address_2( 'edit' ),
					's_city'        => $order->get_shipping_city( 'edit' ),
					's_state'       => $order->get_shipping_state( 'edit' ),
					's_country'     => $this->get_full_country_name( $order->get_shipping_country( 'edit' ) ),
					's_code'        => $order->get_shipping_postcode( 'edit' ),
				],
			],
		];

		// set client username to WP username if registered customer
		$user_id = $order->get_user_id();

		if ( ! empty( $user_id ) ) {

			$user = $order->get_user();

			$this->request_data['request']['client']['username'] = $user->user_login;
		}

		// set VAT number if one exists for the order
		$vat_number_meta_keys = array(
			'VAT Number',
			'vat_number',                // Taxamo
			'_billing_wc_avatax_vat_id', // AvaTax
		);

		foreach ( $vat_number_meta_keys as $meta_key ) {

			$meta_value = $order->get_meta( $meta_key, true );

			if ( ! empty( $meta_value ) ) {

				$this->request_data['request']['client']['vat_name']   = 'VAT Number';
				$this->request_data['request']['client']['vat_number'] = $meta_value;

				break;
			}
		}
	}


	/**
	 * Get clients in DESC order by client_id
	 *
	 * Note that while FreshBooks supports pagination, it's not supported in
	 * this release so clients are limited to a maximum number of 100
	 *
	 * @link http://developers.freshbooks.com/docs/clients/#client.list
	 *
	 * @param string $status client status, either `active`, `archived` or `deleted`
	 * @param string $email optionally filter clients by email
	 * @since 3.0
	 */
	public function get_clients( $status, $email = null ) {

		// base request
		$this->request_data = array(
			'request' => array(
				'@attributes' => array( 'method' => 'client.list' ),
				'per_page'    => 100,
				'folder'      => $status,
			),
		);

		// filter by email
		if ( $email ) {
			$this->request_data['request']['email'] = $email;
		}
	}


	/** Payment methods ******************************************************/


	/**
	 * Create a payment for the invoice
	 *
	 * @link http://developers.freshbooks.com/docs/payments/#payment.create
	 *
	 * @param \WC_FreshBooks_Order $order order instance
	 * @since 3.0
	 */
	public function create_payment( WC_FreshBooks_Order $order ) {

		// store the order object for later use
		$this->order = $order;

		/**
		 * FreshBooks Invoice Payment Type Filter.
		 *
		 * Allow actors to set a specific payment type used for the FreshBooks invoice
		 * prior to it being determined based on the mapping set by the admin.
		 *
		 * @param string $type FreshBooks payment type
		 * @param string $payment_method WooCommerce payment method
		 * @since 3.3.3
		 */
		$type = apply_filters( 'wc_freshbooks_payment_type', $order->get_invoice_payment_type(), $order->get_payment_method_id() );

		$paid_date  = $order->get_date_paid( 'edit' );
		$order_date = $order->get_date_created( 'edit' );
		$date_time  = ! empty( $paid_date ) ? $paid_date : $order_date;

		// base request
		$this->request_data = array(
			'request' => array(
				'@attributes' => array( 'method' => 'payment.create' ),
				'payment'     => array(
					'invoice_id' => $order->get_meta( '_wc_freshbooks_invoice_id' ),
					'date'       => $date_time->date( 'Y-m-d' ),
					'amount'     => Framework\SV_WC_Helper::number_format( $order->get_total() ),
				),
			),
		);

		// add payment type if available
		if ( ! empty( $type ) ) {
			$this->request_data['request']['payment']['type'] = $type;
		}
	}


	/**
	 * Get a single payment
	 *
	 * @link http://developers.freshbooks.com/docs/payments/#payment.get
	 *
	 * @param string $payment_id
	 * @since 3.0
	 */
	public function get_payment( $payment_id ) {

		$this->request_data = array(
			'request' => array(
				'@attributes' => array( 'method' => 'payment.get' ),
				'payment_id'  => $payment_id,
			),
		);
	}


	/** Item methods ******************************************************/

	/**
	 * Get invoice items for the given status
	 *
	 * @link http://developers.freshbooks.com/docs/items/#item.list
	 *
	 * @param string $status either `active`, `archived`, or `deleted`
	 * @param int $page the page to request
	 * @since 3.0
	 */
	public function get_items( $status, $page = 1 ) {

		$this->request_data = array(
			'request' => array(
				'@attributes' => array( 'method' => 'item.list' ),
				'per_page'    => 100,
				'page'        => $page,
				'folder'      => $status,
			),
		);
	}


	/** Webhook methods ******************************************************/


	/**
	 * Get webhooks, filtered to this site
	 *
	 * @link http://developers.freshbooks.com/docs/callbacks/#callback.list
	 *
	 * @since 3.1
	 */
	public function get_webhooks() {

		$this->request_data = array(
			'request' => array(
				'@attributes' => array( 'method' => 'callback.list' ),
				'uri' => add_query_arg( array( 'wc-api' => 'WC_FreshBooks_Webhooks' ), home_url( '/' ) ),
			),
		);
	}


	/**
	 * Create a webhook
	 *
	 * @link http://developers.freshbooks.com/docs/callbacks/#callback.create
	 *
	 * @since 3.0
	 */
	public function create_webhook() {

		$this->request_data = array(
			'request' => array(
				'@attributes' => array( 'method' => 'callback.create' ),
				'callback'    => array(
					'event' => 'all',
					'uri'   => add_query_arg( array( 'wc-api' => 'WC_FreshBooks_Webhooks' ), home_url( '/' ) ),
				),
			),
		);
	}


	/**
	 * Verify a created webhook
	 *
	 * @link http://developers.freshbooks.com/docs/callbacks/#callback.verify
	 *
	 * @param string $webhook_id created webhook ID
	 * @param string $verifier provided verifier received after webhook creation
	 * @since 3.0
	 */
	public function verify_webhook( $webhook_id, $verifier ) {

		$this->request_data = array(
			'request' => array(
				'@attributes' => array( 'method' => 'callback.verify' ),
				'callback'    => array(
					'callback_id' => $webhook_id,
					'verifier'    => $verifier,
				),
			),
		);
	}


	/**
	 * Delete a webhook
	 *
	 * @link http://developers.freshbooks.com/docs/callbacks/#callback.delete
	 *
	 * @param string $webhook_id webhook ID
	 * @since 3.1
	 */
	public function delete_webhook( $webhook_id ) {

		$this->request_data = array(
			'request' => array(
				'@attributes' => array( 'method' => 'callback.delete' ),
				'callback_id' => $webhook_id,
			),
		);
	}


	/** Misc methods ******************************************************/


	/**
	 * Get the languages for the FreshBooks account
	 *
	 * @link http://developers.freshbooks.com/docs/languages/#language.list
	 *
	 * @since 3.0
	 */
	public function get_languages() {

		$this->request_data = array(
			'request' => array(
				'@attributes' => array( 'method' => 'language.list' ),
				'per_page'   => 100,
			),
		);
	}



	/** XML Methods ******************************************************/


	/**
	 * Helper to return completed XML document
	 *
	 * @since 3.0
	 * @return string XML
	 */
	public function to_xml() {

		if ( ! empty( $this->request_xml ) ) {

			return $this->request_xml;
		}

		/**
		 * API Request Data
		 *
		 * Allow actors to modify the request data before it's sent to FreshBooks.
		 * Data like client/invoice contacts, notes, etc. can be added with this
		 * filter
		 *
		 * @since 3.0
		 * @param WC_FreshBooks_Order $order order instance
		 * @param \WC_FreshBooks_API_Request $this, API request class instance
		 */
		apply_filters( 'wc_freshbooks_api_request_data', $this->request_data, $this->order, $this );

		// generate XML from request data, recursively using the `request` root element
		$this->array_to_xml( 'request', $this->request_data['request'] );

		$this->endDocument();

		return $this->request_xml = $this->outputMemory();
	}


	/**
	 * Convert array into XML by recursively generating child elements
	 *
	 * @since 3.0
	 * @param string|array $element_key name for element, e.g. <per_page>
	 * @param string|array $element_value value for element, e.g. 100
	 * @return string generated XML
	 */
	private function array_to_xml( $element_key, $element_value = array() ) {

		if ( is_array( $element_value ) ) {

			// handle attributes
			if ( '@attributes' === $element_key ) {
				foreach ( $element_value as $attribute_key => $attribute_value ) {

					$this->startAttribute( $attribute_key );
					$this->text( $attribute_value );
					$this->endAttribute();
				}
				return;
			}

			// handle multi-elements (e.g. multiple <Order> elements)
			if ( is_numeric( key( $element_value ) ) ) {

				// recursively generate child elements
				foreach ( $element_value as $child_element_key => $child_element_value ) {

					$this->startElement( $element_key );

					foreach ( $child_element_value as $sibling_element_key => $sibling_element_value ) {
						$this->array_to_xml( $sibling_element_key, $sibling_element_value );
					}

					$this->endElement();
				}

			} else {

				// start root element
				$this->startElement( $element_key );

				// recursively generate child elements
				foreach ( $element_value as $child_element_key => $child_element_value ) {
					$this->array_to_xml( $child_element_key, $child_element_value );
				}

				// end root element
				$this->endElement();
			}

		} else {

			// handle single elements
			if ( '@value' === $element_key ) {

				$this->text( $element_value );

			} else {

				// wrap element in CDATA tags if it contains illegal characters
				if ( false !== strpos( $element_value, '<' ) || false !== strpos( $element_value, '>' ) ) {

					$this->startElement( $element_key );
					$this->writeCdata( $element_value );
					$this->endElement();

				} else {

					$this->writeElement( $element_key, $element_value );
				}

			}

			return;
		}
	}


	/** Helper Methods ******************************************************/


	/**
	 * Returns the method for this request. Freshbooks uses the API default
	 * (POST)
	 *
	 * @since 3.5.0
	 * @see SV_WC_API_Request::get_method()
	 * @return null
	 */
	public function get_method() {}


	/**
	 * Returns the request path for this request. Freshbooks request paths
	 * do not vary per request.
	 *
	 * @since 3.5.0
	 * @see SV_WC_API_Request::get_path()
	 * @return string
	 */
	public function get_path() {
		return '';
	}


	/**
	 * Gets the request data.
	 *
	 * Implements interface method.
	 *
	 * @since 3.12.0
	 *
	 * @return array
	 */
	public function get_data() {

		return array();
	}


	/**
	 * Gets the request params.
	 *
	 * Implements interface method.
	 *
	 * @since 3.12.0
	 *
	 * @return array
	 */
	public function get_params() {

		return array();
	}


	/**
	 * Returns the string representation of this request
	 *
	 * @since 3.0
	 * @return string request XML
	 */
	public function to_string() {

		return $this->to_xml();
	}


	/**
	 * Returns the string representation of this request with any and all
	 * sensitive elements masked or removed
	 *
	 * @since 3.2.0
	 * @see SV_WC_API_Request::to_string_safe()
	 * @return string the request XML, safe for logging/displaying
	 */
	public function to_string_safe() {

		$request = $this->to_xml();

		$dom = new DOMDocument();

		// suppress errors for invalid XML syntax issues
		if ( @$dom->loadXML( $request ) ) {
			$dom->formatOutput = true;
			$request = $dom->saveXML();
		}

		return $request;
	}


	/**
	 * Returns the order associated with this request, if there was one
	 *
	 * @since 3.0
	 * @return \WC_FreshBooks_Order order object
	 */
	public function get_order() {

		return $this->order;
	}


	/**
	 * Return the full country name if available.
	 *
	 * @since 3.8.0
	 * @param string $country_code The country code.
	 * @return string
	 */
	private function get_full_country_name( $country_code ) {

		$countries = WC()->countries->get_countries();

		return isset( $countries[ $country_code ] ) ? $countries[ $country_code ] : $country_code;
	}


	/**
	 * Returns unit price.
	 *
	 * @since 3.11.4
	 *
	 * @param WC_FreshBooks_Order $order
	 * @param $item
	 *
	 * @return float
	 */
	private function get_item_unit_price( $order, $item ) {

		if  ( $this->use_tax_inclusive_price() ) {
			return $order->get_item_subtotal( $item, true );
		}

		return wc_prices_include_tax() ? $order->get_item_subtotal( $item, false, false ) : $order->get_item_subtotal( $item );
	}


	/**
	 * Determines weather we should send tax inclusive prices.
	 *
	 * @since 3.11.4
	 *
	 * @return bool
	 */
	private function use_tax_inclusive_price() {

		return wc_prices_include_tax() && 'yes' === get_option( 'wc_freshbooks_send_tax_inclusive_price' );
	}


}
