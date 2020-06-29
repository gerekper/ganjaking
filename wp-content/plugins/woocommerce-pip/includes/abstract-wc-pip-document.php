<?php
/**
 * WooCommerce Print Invoices/Packing Lists
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Print
 * Invoices/Packing Lists to newer versions in the future. If you wish to
 * customize WooCommerce Print Invoices/Packing Lists for your needs please refer
 * to http://docs.woocommerce.com/document/woocommerce-print-invoice-packing-list/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * PIP Document abstract class
 *
 * Provides an abstract model for documents handled by this plugin
 *
 * @since 3.0.0
 */
abstract class WC_PIP_Document {


	/** @var string the document type identifier */
	public $type = '';

	/** @var string the document name */
	public $name = '';

	/** @var string the document name (plural) */
	public $name_plural = '';

	/** @var \WC_Order an order associated to this document */
	public $order;

	/** @var int WC_Order id */
	public $order_id = 0;

	/** @var array $order_ids Used in multiple documents context */
	public $order_ids = [];

	/** @var string Sort order items by column key */
	protected $sort_items_by = 'product';

	/** @var bool document may have refunds to account for */
	protected $may_have_refunds = false;

	/** @var array table headers */
	protected $table_headers = [];

	/** @var array column widths */
	protected $column_widths = [];

	/** @var string[] array of fields that are optional to be displayed */
	protected $optional_fields = [];

	/** @var string[] array of chosen fields to be displayed */
	protected $chosen_fields;

	/** @var bool Whether this document should display a shipping address */
	protected $show_shipping_address = false;

	/** @var bool Whether this document should display a billing address */
	protected $show_billing_address = false;

	/** @var bool Whether this document should display the shipping method */
	protected $show_shipping_method = false;

	/** @var bool Whether this document should display the header */
	protected $show_header = false;

	/** @var bool Whether this document should display coupons used */
	protected $show_coupons_used = false;

	/** @var bool Whether this document should display customer details */
	protected $show_customer_details = false;

	/** @var bool Whether this document should display the customer note */
	protected $show_customer_note = false;

	/** @var bool Whether this document should display terms and conditions */
	protected $show_terms_and_conditions = false;

	/** @var bool Whether this document should display the footer */
	protected $show_footer = false;

	/** @var bool whether to force item prices to be tax exclusive */
	protected $show_prices_excluding_tax = false;

	/** @var bool Whether to hide virtual items from list */
	protected $hide_virtual_items = false;


	/**
	 * PIP Document constructor
	 *
	 * @since 3.0.0
	 * @param array $args
	 */
	public function __construct( $args ) {

		// multiple order ids, used in bulk actions
		if ( isset( $args['order_ids'] ) ) {

			if ( is_array( $args['order_ids'] ) ) {
				$this->order_ids = array_map( 'intval', $args['order_ids'] );
			} else {
				$this->order_ids = (array) explode( ',', $args['order_ids'] );
			}

			$this->may_have_refunds = true;
		}

		// set the order object
		if ( isset( $args['order'] ) && $args['order'] instanceof \WC_Order ) {
			$this->order = $args['order'];
		} elseif ( isset( $args['order_id'] ) && is_numeric( $args['order_id'] ) ) {
			$this->order = wc_get_order( (int) $args['order_id'] );
		} else {
			$this->order = wc_get_order( 0 );
		}

		// set order properties
		if ( $this->order instanceof \WC_Order ) {
			$this->order_id         = $this->order->get_id();
			$this->may_have_refunds = $this->may_have_refunds ?: ! empty( $this->order->get_refunds() );
		}

		// get custom styles
		add_action( 'wc_pip_styles', array( $this, 'custom_styles' ) );

		// add styles in template head
		add_action( 'wc_pip_head', array( $this, 'output_styles' ) );

		// update document counters upon actions
		add_action( 'wc_pip_print',      array( $this, 'upon_print' ), 10, 2 );
		add_action( 'wc_pip_send_email', array( $this, 'upon_send_email' ), 10, 2 );
	}


	/**
	 * Check the document type
	 *
	 * @since 3.0.0
	 * @param array|string $type
	 * @return bool
	 */
	public function is_type( $type ) {
		return is_array( $type ) ? in_array( $this->type, $type, true ) : $type === $this->type;
	}


	/**
	 * Custom styles to be added in stylesheet
	 *
	 * @since 3.0.0
	 */
	public function custom_styles() {

		echo stripslashes( get_option( 'wc_pip_custom_styles', '' ) );
	}


	/**
	 * Output CSS styles in template file
	 *
	 * @since 3.0.0
	 */
	public function output_styles() {

		wc_pip()->get_template( 'styles', array(
			'document' => $this,
		) );
	}


	/**
	 * Checks if customer shipping address should be shown in the document
	 *
	 * @since 3.0.0
	 * @return bool, True if shown
	 */
	public function show_shipping_address() {

		/**
		 * Filters if the customer shipping address should be shown in the document.
		 *
		 * @since 3.0.2
		 * @param bool $show_shipping_address Whether to show shipping address on the document or not.
		 * @param string $type WC_PIP_Document type
		 * @param \WC_Order $order The WC Order object
		 */
		return apply_filters( 'wc_pip_document_show_shipping_address', $this->show_shipping_address, $this->type, $this->order );
	}


	/**
	 * Checks if customer billing address should be shown in the document
	 *
	 * @since 3.0.0
	 * @return bool, True if shown
	 */
	public function show_billing_address() {

		/**
		 * Filters if the customer billing address should be shown in the document.
		 *
		 * @since 3.0.2
		 * @param bool $show_billing_address Whether to show billing address on the document or not.
		 * @param string $type WC_PIP_Document type
		 * @param \WC_Order $order The WC Order object
		 */
		return apply_filters( 'wc_pip_document_show_billing_address', $this->show_billing_address, $this->type, $this->order );
	}


	/**
	 * Checks if order shipping method should be shown in the document.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function show_shipping_method() {

		/**
		 * Filters if the shipping method should be shown in the document.
		 *
		 * @since 3.6.0
		 *
		 * @param bool $show_shipping_method whether or not to show shipping method on the document
		 * @param string $type document type
		 * @param \WC_Order $order order object
		 */
		 return (bool) apply_filters( 'wc_pip_document_show_shipping_method', $this->show_shipping_method, $this->type, $this->order );
	}


	/**
	 * Checks if header should be shown in the document
	 *
	 * @since 3.0.3
	 * @return bool, True if shown
	 */
	public function show_header() {

		/**
		 * Filters if the header should be shown in the document.
		 *
		 * @since 3.0.3
		 * @param bool $show_header Whether to show the header on the document or not.
		 * @param string $type WC_PIP_Document type
		 * @param \WC_Order $order The WC Order object
		 */
		return apply_filters( 'wc_pip_document_show_header', $this->show_header, $this->type, $this->order );
	}


	/**
	 * Checks if customer details should be shown in the document
	 *
	 * @since 3.0.0
	 * @return bool, True if shown
	 */
	public function show_customer_details() {
		return $this->show_customer_details;
	}


	/**
	 * Checks if order coupons used should be shown in the document
	 *
	 * @since 3.0.0
	 * @return bool, True if shown
	 */
	public function show_coupons_used() {
		return $this->show_coupons_used;
	}


	/**
	 * Checks if customer note should be shown in the document
	 *
	 * @since 3.0.0
	 * @return bool, True if shown
	 */
	public function show_customer_note() {
		return $this->show_customer_note;
	}


	/**
	 * Checks if terms and conditions section should be shown in the document
	 *
	 * @since 3.0.0
	 * @return bool, True if shown
	 */
	public function show_terms_and_conditions() {

		/**
		 * Filters if the terms & conditions should be shown in the document.
		 *
		 * @since 3.0.2
		 * @param bool $show_terms_and_conditions Whether to show terms & conditions on the document or not.
		 * @param string $type WC_PIP_Document type
		 * @param \WC_Order $order The WC Order object
		 */
		return apply_filters( 'wc_pip_document_show_terms_and_conditions', $this->show_terms_and_conditions, $this->type, $this->order );
	}


	/**
	 * Checks if the footer should be shown in the document
	 *
	 * @since 3.0.3
	 * @return bool, True if shown
	 */
	public function show_footer() {

		/**
		 * Filters if the footer should be shown in the document.
		 *
		 * @since 3.0.3
		 * @param bool $show_footer Whether to show the footer on the document or not.
		 * @param string $type WC_PIP_Document type
		 * @param \WC_Order $order The WC Order object
		 */
		return apply_filters( 'wc_pip_document_show_footer', $this->show_footer, $this->type, $this->order );
	}


	/**
	 * Get the document template HTML
	 *
	 * @since 3.0.0
	 * @param array $args
	 */
	public function output_template( $args = array() ) {

		if ( ! $this->order instanceof \WC_Order ) {
			return;
		}

		$template_args = wp_parse_args( $args, array(
			'document'  => $this,
			'order'     => $this->order,
			'order_id'  => $this->order_id,
			'order_ids' => $this->order_ids,
			'type'      => $this->type,
		) );

		$original_order = $this->order;

		wc_pip()->get_template( 'head', $template_args );

		if ( ! empty( $this->order_ids ) && is_array( $this->order_ids ) ) {

			// Documents for multiple orders
			foreach ( $this->order_ids as $order_id ) {

				$wc_order = wc_get_order( (int) $order_id );

				$template_args['order']    = $this->order    = $wc_order;
				$template_args['order_id'] = $this->order_id = $wc_order->get_id();

				if ( $wc_order ) {
					$this->get_template_body( $template_args );
				}
			}

			// Restore the original order
			$template_args['order']    = $this->order    = $original_order;
			$template_args['order_id'] = $this->order_id = $original_order->get_id();

		} else {

			// Single document for an individual order
			$this->get_template_body( $template_args );

		}

		wc_pip()->get_template( 'foot', $template_args );
	}


	/**
	 * Get template body
	 *
	 * @since 3.0.0
	 * @param $args
	 */
	protected function get_template_body( $args ) {

		// Return if there is no items available in the order, i.e. exclude virtual items completely if setting is on.
		// This way we avoid printing empty documents and save trees. ;)
		if ( 'invoice' !== $this->type && 0 === $this->get_items_count() ) {
			return;
		}

		wc_pip()->get_template( 'content/order-table-before', $args );
		wc_pip()->get_template( 'content/order-table',        $args );
		wc_pip()->get_template( 'content/order-table-items',  $args );
		wc_pip()->get_template( 'content/order-table-after',  $args );
	}


	/**
	 * Get shipping method
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function get_shipping_method() {

		if ( ! $this->order instanceof \WC_Order ) {
			return '';
		}

		if ( $the_shipping_method = $this->order->get_shipping_method() ) {
			$shipping_method = $the_shipping_method;
		} else {
			$shipping_method =  __( 'No shipping', 'woocommerce-pip' );
		}

		/**
		 * Filters the shipping method(s).
		 *
		 * @since 3.0.0
		 * @param string $shipping_method The shipping method
		 * @param string $type WC_PIP_Document type
		 * @param \WC_Order $order The WC Order object
		 */
		return apply_filters( 'wc_pip_document_shipping_method', $shipping_method, $this->type, $this->order );
	}


	/**
	 * Gets coupons used for purchase order.
	 *
	 * @since 3.0.0
	 *
	 * @return string[] array of coupon codes
	 */
	public function get_coupons_used() {

		if ( ! $this->order instanceof \WC_Order ) {
			$coupons = [];
		} elseif ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.7' ) ) {
			$coupons = $this->order->get_coupon_codes();
		} else {
			$coupons = $this->order->get_used_coupons();
		}

		/**
		 * Filters the document's coupons used.
		 *
		 * @since 3.0.0
		 *
		 * @param string[] $coupons order coupons array
		 * @param string $document_type PIP document type
		 * @param \WC_Order $order order object
		 */
		return apply_filters( 'wc_pip_document_coupons_used', $coupons, $this->type, $this->order );
	}


	/**
	 * Gets the customer details.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_customer_details() {

		$customer_details = [];
		$billing_email    = $this->order->get_billing_email();
		$billing_phone    = $this->order->get_billing_phone();

		if ( ! empty( $billing_email ) ) {

			$customer_details['customer-email'] = [
				'label' => __( 'Email:', 'woocommerce-pip' ),
				'value' => '<a href="mailto:' . $billing_email . '">' . $billing_email . '</a>',
			];
		}

		if ( ! empty( $billing_phone ) ) {

			$customer_details['customer-phone'] = [
				'label' => __( 'Phone:', 'woocommerce-pip' ),
				'value' => '<a href="tel:' . $billing_phone . '">' . $billing_phone . '</a>',
			];
		}

		/**
		 * Filters the document's customer details.
		 *
		 * @since 3.0.0
		 *
		 * @param array $customer_details Associative array
		 * @param int $order_id WC_Order id
		 * @param string $type WC_PIP_Document type
		 * @param \WC_PIP_Document $document An instance of this document
		 */
		return apply_filters( 'wc_pip_document_customer_details', $customer_details, $this->order_id, $this->type, $this );
	}


	/**
	 * Returns the customer's note attached to the related order.
	 *
	 * @since 3.0.0
	 *
	 * @return string may contain HTML
	 */
	public function get_customer_note() {

		$customer_note = $this->order instanceof \WC_Order ? $this->order->get_customer_note( 'edit' ) : null;
		$customer_note = ! empty( $customer_note ) ? nl2br( stripslashes( trim( $customer_note ) ) ) : '';

		/**
		 * Filter's the document's customer note.
		 *
		 * @since 3.0.0
		 *
		 * @param string $customer_note HTML text
		 * @param int $order_id WC_Order id
		 * @param string $document_type document type
		 */
		return (string) apply_filters( 'wc_pip_document_customer_note', $customer_note, $this->order_id, $this->type );
	}


	/**
	 * Get invoice date
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function get_invoice_date() {

		if ( ! $this->order instanceof \WC_Order ) {
			return '';
		}

		$order_datetime = $this->order->get_date_created( 'edit' );

		if ( ! $order_datetime instanceof \DateTime ) {
			return '';
		}

		$invoice_date = $order_datetime->date_i18n( wc_date_format() );

		// For backwards compatibility, we keep the order date as a mysql string as before WC 3.0.
		$order_date = $order_datetime->date( 'Y-m-d H:i:s' );

		/**
		 * Filter's the invoice date.
		 *
		 * @since 3.0.0
		 * @param string $invoice_date Formatted date (with `wc_date_format()`)
		 * @param int $order_id WC_Order id
		 * @param string $order_date Order date in mysql format
		 * @param string $type PIP Document type
		 */
		return apply_filters( 'wc_pip_invoice_date', $invoice_date, $this->order_id, $order_date, $this->type );
	}


	/**
	 * Checks if the document associated order has an invoice number.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function has_invoice_number() {

		// do not call the method to get the invoice number directly or it may trigger the generation of a number, which we may not need yet
		if ( $this->order_id > 0 && ( $order = wc_get_order( $this->order_id ) ) ) {
			$invoice_number = $order->get_meta( '_pip_invoice_number' );
		}

		return ! empty( $invoice_number );
	}


	/**
	 * Get invoice number.
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function get_invoice_number() {

		$invoice_number = null;

		if ( $this->order_id > 0 ) {

			// check if the invoice number already exists, if so get that one...
			if ( $order = wc_get_order( $this->order_id ) ) {
				$invoice_number = $order->get_meta( '_pip_invoice_number' );
			}

			// ...otherwise, generate one and return it
			if ( empty( $invoice_number ) ) {
				$invoice_number = wc_pip()->get_handler_instance()->generate_invoice_number( $this->order_id, $this );
			}
		}

		return is_string( $invoice_number ) || is_numeric( $invoice_number ) ? (string) $invoice_number : '';
	}


	/**
	 * Returns the document header.
	 *
	 * @since 3.0.0
	 *
	 * @return string may contain HTML
	 */
	public function get_header() {

		$header = nl2br( stripslashes( trim( get_option( 'wc_pip_header', '' ) ) ) );

		/**
		 * Filters the document header.
		 *
		 * @since 3.0.0
		 *
		 * @param string $header document header HTML
		 * @param int $order_id WC_Order id
		 * @param string $document_type document type
		 */
		return (string) apply_filters( 'wc_pip_document_header', $header, $this->order_id, $this->type );
	}


	/**
	 * Returns the document footer.
	 *
	 * @since 3.0.0
	 *
	 * @return string may contain HTML
	 */
	public function get_footer() {

		$footer = nl2br( stripslashes( trim( get_option( 'wc_pip_footer', '' ) ) ) );

		/**
		 * Filters the document footer.
		 *
		 * @since 3.0.0
		 *
		 * @param string $footer document footer HTML
		 * @param int $order_id WC_Order id
		 * @param string $document_type document type
		 */
		return (string) apply_filters( 'wc_pip_document_footer', $footer, $this->order_id, $this->type );
	}


	/**
	 * Get company logo
	 *
	 * @since 3.0.0
	 * @return string HTML
	 */
	public function get_company_logo() {

		$image_html = '';

		if ( $image_url = get_option( 'wc_pip_company_logo', '' ) ) {

			/**
			 * Filters the logo max width.
			 *
			 * @since 3.0.0
			 * @param string $size size in pixels
			 * @param int $order_id WC_Order id
			 * @param string $type PIP Document type
			 */
			$max_width  = apply_filters( 'wc_pip_document_company_logo_max_width', get_option( 'wc_pip_company_logo_max_width', '300' ) . 'px', $this->order_id, $this->type );

			$image_html = '<img src="' . $image_url . '" class="wc-pip-logo logo" style="max-width:' . $max_width . '" /><br />';
		}

		/**
		 * Filters the company logo.
		 *
		 * @since 3.0.0
		 * @param string $image_html Image HTML
		 * @param string $image_url Image URL
		 * @param int $order_id WC_Order id
		 * @param string $type PIP Document type
		 */
		return apply_filters( 'wc_pip_document_company_logo', $image_html, $image_url, $this->order_id, $this->type );
	}


	/**
	 * Get company name
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function get_company_name() {

		$company_name = get_option( 'wc_pip_company_name', get_bloginfo( 'name' ) );

		/**
		 * Filters the company name.
		 *
		 * @since 3.0.0
		 * @param string $company_name Company name
		 * @param int $order_id WC_Order id
		 * @param string $document_type WC_PIP_Document type
		 */
		return apply_filters( 'wc_pip_document_company_name', $company_name, $this->order_id, $this->type );
	}


	/**
	 * Get company extra info (slogan, subtitle)
	 *
	 * @since 3.0.0
	 * @return string HTML
	 */
	public function get_company_extra_info() {

		$company_extra_info = nl2br( stripslashes( get_option( 'wc_pip_company_extra', '' ) ) );

		/**
		 * Filters the company extra info.
		 *
		 * @since 3.0.0
		 * @param string $company_extra_info Extra info
		 * @param int $order_id WC_Order id
		 * @param string $document_type WC_PIP_Document type
		 */
		return apply_filters( 'wc_pip_document_company_extra_info', $company_extra_info, $this->order_id, $this->type );
	}


	/**
	 * Returns the company VAT number.
	 *
	 * @since 3.5.0
	 *
	 * @return string
	 */
	public function get_company_vat_number() {

		$vat_number = trim( get_option( 'wc_pip_company_vat_number', '' ) );

		/**
		 * Filters the company VAT number.
		 *
		 * @since 3.5.0
		 *
		 * @param string $vat_number a VAT number or empty string when unspecified
		 * @param \WC_PIP_Document the current document object
		 */
		return (string) apply_filters( 'wc_pip_document_company_vat_number', $vat_number, $this );
	}


	/**
	 * Get company URL
	 *
	 * @since 3.0.0
	 * return string URL
	 */
	public function get_company_url() {

		$company_url = get_option( 'wc_pip_company_url', get_bloginfo( 'url' ) );

		/**
		 * Filters the company url.
		 *
		 * @since 3.0.0
		 * @param string $company_url Company URL
		 * @param int $order_id WC_Order id
		 * @param string $document_type WC_PIP_Document type
		 */
		return apply_filters( 'wc_pip_document_company_url', $company_url, $this->order_id, $this->type );
	}


	/**
	 * Get company link
	 *
	 * @since 3.0.0
	 * @param string $text Optional, text for link (defaults to the url itself)
	 * @return string Formatted HTML
	 */
	public function get_company_link( $text = '' ) {

		if ( $url = $this->get_company_url() ) {

			$link_text = empty( $text ) ? $url : $text;

			return '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $this->get_company_name() ) . '">' . $link_text . '</a>';
		}

		return $text;
	}


	/**
	 * Get company address
	 *
	 * @since 3.0.0
	 * @return string HTML
	 */
	public function get_company_address() {

		$company_address = nl2br( stripslashes( get_option( 'wc_pip_company_address', '' ) ) );

		/**
		 * Filters the company address.
		 *
		 * @since 3.0.0
		 * @param string $company_address Company address
		 * @param int $order_id WC_Order id
		 * @param string $document_type WC_PIP_Document type
		 */
		return apply_filters( 'wc_pip_document_company_address', $company_address, $this->order_id, $this->type );
	}


	/**
	 * Returns the store returns policy or terms and conditions.
	 *
	 * @since 3.0.0
	 *
	 * @return string may contain HTML
	 */
	public function get_return_policy() {

		$terms_and_conditions = nl2br( stripslashes( trim( get_option( 'wc_pip_return_policy', '' ) ) ) );

		/**
		 * Filters the return policy.
		 *
		 * @since 3.0.0
		 *
		 * @param string $terms_and_conditions HTML text
		 * @param int $order_id \WC_Order id
		 * @param string $document_type \WC_PIP_Document type
		 */
		return (string) apply_filters( 'wc_pip_document_terms_and_conditions', $terms_and_conditions, $this->order_id, $this->type );
	}


	/**
	 * Gets optional chosen fields to be displayed for the table order items.
	 *
	 * @since 3.8.0
	 *
	 * @return string[]
	 */
	protected function get_chosen_fields() {

		$document_key  = str_replace( '-', '_', $this->type );
		$chosen_fields = get_option( "wc_pip_{$document_key}_show_optional_fields", $this->optional_fields ?: [] );

		if ( is_string( $chosen_fields )  ) {

			// at the moment the invoice only has one optional field, the SKY, which is toggled by a checkbox
			if ( 'invoice' === $this->type ) {
				$this->chosen_fields = 'yes' === $chosen_fields ? [ 'sku' ] : [];
				// in other documents, if only one field is stored, this may be a string, but we want to output an array of strings
			} else {
				$this->chosen_fields = (array) $chosen_fields;
			}

		} else {

			$this->chosen_fields = ! is_array( $chosen_fields ) ? [] : $chosen_fields;
		}

		return $this->chosen_fields;
	}


	/**
	 * Gets the document's table headers.
	 *
	 * @since 3.0.0
	 *
	 * @return array associative array of keys and column names
	 */
	public function get_table_headers() {

		$chosen_fields = $this->get_chosen_fields();

		// filter out disabled fields
		foreach ( array_keys( $this->table_headers ) as $column ) {

			if ( in_array( $column, $this->optional_fields, true ) && ! in_array( $column, $chosen_fields, true ) ) {

				unset( $this->table_headers[ $column ] );
			}
		}

		// bail out if we are on customizer
		if ( is_customize_preview() ) {
			return $this->table_headers;
		}

		/**
		 * Filters the table headers.
		 *
		 * @since 3.0.0
		 *
		 * @param array $table_headers Table column headers
		 * @param int $order_id WC_Order id
		 * @param string $document_type WC_PIP_Document type
		 */
		return apply_filters( 'wc_pip_document_table_headers', $this->table_headers, $this->order_id, $this->type );
	}


	/**
	 * Gets the document table column widths.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_column_widths() {

		$freed_up_width = 0;
		$chosen_fields  = $this->get_chosen_fields();

		// if any fields have been chosen not to be displayed, remove corresponding widths
		foreach ( $this->column_widths as $column => $width ) {

			if ( in_array( $column, $this->optional_fields, true ) && ! in_array( $column, $chosen_fields, true ) ) {

				unset( $this->column_widths[ $column ] );

				$freed_up_width += $width;
			}
		}

		// add back the freed up width by distributing it to existing columns
		foreach ( array_keys( $this->column_widths ) as $column ) {

			if ( $freed_up_width > 0 ) {

				$this->column_widths[ $column ]++;

				$freed_up_width--;
			}
		}

		$column_widths = $this->column_widths;

		// do not filter column widths on Customizer
		if ( ! is_customize_preview() ) {

			/**
			 * Filters the table column widths.
			 *
			 * @since 3.0.0
			 *
			 * @param array $column_widths Column widths
			 * @param int $order_id WC_Order id
			 * @param string $document_type WC_PIP_Document type
			 */
			$column_widths = (array) apply_filters( 'wc_pip_document_column_widths', $column_widths, $this->order_id, $this->type );
		}

		// ensure to set any column added by filtering with a default weight of 1
		$column_widths = wp_parse_args( $column_widths, array_fill_keys( array_keys( $this->get_table_headers() ), 1 ) );
		$total_width   = array_sum( $column_widths );

		foreach ( $column_widths as $name => $width ) {
			$column_widths[ $name ] = ( (float) $width / $total_width ) * 100;
		}

		return $column_widths;
	}


	/**
	 * Gets the table footer column span.
	 *
	 * Calculates the relative footer span for a given number of footer cells.
	 *
	 * @since 3.0.0
	 *
	 * @param int $cells table row cells count
	 * @return int column span
	 */
	public function get_table_footer_column_span( $cells ) {

		$table_headers = $this->get_table_headers();
		$cols          = count( $table_headers );

		// the hidden id col doesn't span
		if ( isset( $table_headers['id'] ) ) {
			$cols--;
		}

		return max( 1, ( $cols + 1 ) - (int) $cells );
	}


	/**
	 * Gets items count.
	 *
	 * @since 3.0.0
	 *
	 * @return int
	 */
	public function get_items_count() {

		$items = $this->order->get_items();
		$count = 0;

		foreach ( $items as $item_id => $item_data ) {

			$product  = isset( $item_data['product_id'] ) ? wc_get_product( $item_data['product_id'] ) : null;
			$item_qty = isset( $item_data['qty'] ) ? max( 0, (float) $item_data['qty'] ) : 1;

			if ( $this->maybe_hide_virtual_item( $item_data ) || $this->maybe_hide_deleted_product( $product ) ) {
				continue;
			}

			// subtract any refunded quantity
			if ( $this->may_have_refunds ) {
				$refund_qty = absint( $this->order->get_qty_refunded_for_item( $item_id ) );
				$item_qty   = max( 0, $item_qty - $refund_qty );
			}

			$count += $item_qty;
		}

		/**
		 * Filters the order items count.
		 *
		 * @since 3.0.0
		 *
		 * @param int $count items count
		 * @param array $items items in WC_Order
		 * @param \WC_Order $order order object
		 */
		return (int) apply_filters( 'wc_pip_order_items_count', $count, $items, $this->order );
	}


	/**
	 * Get document table body's rows
	 *
	 * This is generally a list of order items
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function get_table_rows() {

		$table_cells = array();

		if ( ! is_object( $this->order ) ) {
			return $table_cells;
		}

		$items = $this->order->get_items();

		// allow 0 rows for invoices, which could have refunds
		if ( $this->get_items_count() > 0 || ( 'invoice' === $this->type && $this->get_items_count() >= 0 ) ) {

			foreach ( $items as $id => $item ) {

				$table_row_data = $this->get_table_row_order_item_data( $id, $item );

				if ( ! empty( $table_row_data ) ) {
					$table_cells[ $id ] = $table_row_data;
				}
			}

			if ( $this->sort_order_items_alphabetically() ) {
				usort( $table_cells, array( $this, 'sort_order_items_by_column_key' ) );
			}
		}

		$table_rows[] = array(
			'items' => $table_cells,
		);

		/**
		 * Filters the document's table rows.
		 *
		 * @since 3.0.0
		 * @param array $table_rows Items row data (maybe alphabetically sorted).
		 * @param array $items Items raw data (unsorted).
		 * @param int $order_id WC_Order ID.
		 * @param string $document_type The document type.
		 * @param \WC_PIP_Document $document The document object.
		 */
		return apply_filters( 'wc_pip_document_table_rows', $table_rows, $items, $this->order->get_id(), $this->type, $this );
	}


	/**
	 * Determines whether order items in document should be sorted alphabetically.
	 *
	 * @since 3.6.5
	 *
	 * @return bool
	 */
	protected function sort_order_items_alphabetically() {

		/**
		 * Filters if items in document tables should be sorted alphabetically.
		 *
		 * By default items are sorted alphabetically but this can be set to false.
		 *
		 * @since 3.0.0
		 *
		 * @param bool $sort_alphabetically Default true, set to false to keep default WooCommerce order sorting
		 * @param int $order_id the order ID
		 * @param string $type the document type
		 */
		return (bool) apply_filters( 'wc_pip_document_sort_order_items_alphabetically', true, $this->order ? $this->order->get_id() : absint( $this->order_id ), $this->type );
	}


	/**
	 * Sort order items by column key
	 *
	 * `usort()` function callback, returns:
	 *
	 * -1 - $row_1 is below $row_2
	 *  0 - $row_1 is equal to $row_2
	 *  1 - $row_1 is above $row_2
	 *
	 * @since 3.0.5
	 * @param array $row_1 First row to compare for sorting
	 * @param array $row_2 Second row to compare for sorting
	 * @return int
	 */
	protected function sort_order_items_by_column_key( $row_1, $row_2 ) {

		/**
		 * Filter the sorting order for order items
		 *
		 * By default items are sorted by product name, but sku can be used
		 *
		 * @since 3.0.2
		 * @param string $sort_order_items_key Default 'product', can be set to any column key such as 'sku', 'price', 'weight', etc.
		 * @param int $order_id WC_Order id
		 * @param string $type Document type
		 */
		$sort_order_items_key = apply_filters( 'wc_pip_document_sort_order_items_key', $this->sort_items_by, $this->order_id, $this->type );

		// sanity check, ensure the array contains the requested key
		if ( ! is_string( $sort_order_items_key ) || ! isset( $row_1[ $sort_order_items_key ], $row_2[ $sort_order_items_key ] ) ) {

			$compare = 0;

		} else {

			$item_1_value = wp_strip_all_tags( $row_1[ $sort_order_items_key ], true );
			$item_2_value = wp_strip_all_tags( $row_2[ $sort_order_items_key ], true );

			switch ( $sort_order_items_key ) {

				// numerical sorting
				case 'price':
				case 'quantity':
				case 'weight':

					// strip out any non-numerical characters (except '.' dot)
					$item_1_value = preg_replace( '/[^0-9.]+/i', '', $item_1_value );
					$item_2_value = preg_replace( '/[^0-9.]+/i', '', $item_2_value );

					// compare numerical values
					$compare = (float) $item_1_value < (float) $item_2_value ? -1 : 1;

				break;

				// alphabetical string sorting (product name, SKU...)
				default:
					$compare = strcmp( $item_1_value, $item_2_value );
				break;

			}

			// prepare arguments for filter
			$item_1_id   = isset( $row_1['id'] ) ? $this->get_item_id_from_order_table_row_cell_html( $row_1['id'] ) : 0;
			$item_2_id   = isset( $row_2['id'] ) ? $this->get_item_id_from_order_table_row_cell_html( $row_2['id'] ) : 0;
			$filter_args = array(
				'sort_key' => $sort_order_items_key,
				'row_1'    => $row_1, // raw data
				'row_2'    => $row_2, // raw data
				'item_1'   => array( $item_1_id => $item_1_value ),
				'item_2'   => array( $item_2_id => $item_2_value ),
			);

			/**
			 * Filters the usort callback to sort order items in document table.
			 *
			 * @since 3.3.5
			 *
			 * @param int $compare this should be a valid usort callback return value (an integer between -1 and 1)
			 * @param array $filter_args array of arguments used to compare 2 items at one time
			 * @param \WC_PIP_Document $document the current document object
			 */
			$compare = apply_filters( 'wc_pip_sort_order_item_rows', $compare, $filter_args, $this );
		}

		return (int) $compare;
	}


	/**
	 * Extract a product id from an HTML row cell
	 *
	 * @since 3.0.5
	 * @param string $html HTML with data-id attribute
	 * @return int Will return 0 if not found or unsuccessful
	 */
	private function get_item_id_from_order_table_row_cell_html( $html ) {

		$product_id = 0;

		$dom = new \DOMDocument();
		$dom->loadHTML( $html ) ;

		if ( $tags = $dom->getElementsByTagName( 'span' ) ) {

			foreach ( $tags as $span ) {
				$product_id = $span->getAttribute( 'data-item-id' );
			}
		}

		return is_numeric( $product_id ) ? (int) $product_id : 0;
	}


	/**
	 * Gets the table row order item data.
	 *
	 * This method applies filter hooks
	 * @see \WC_PIP_Document::get_order_item_data()
	 * for child documents implementation
	 *
	 * @since 3.0.0
	 *
	 * @param string $item_id item id
	 * @param \WC_Order_Item_Product $item WC_Order item meta
	 * @return array
	 */
	protected function get_table_row_order_item_data( $item_id, $item ) {

		$product   = $item->get_product();
		$item_data = $this->get_order_item_data( $item_id, $item, $product );

		/**
		 * Filters if the order item should be visible on the document.
		 *
		 * @since 3.0.0
		 *
		 * @param bool $item_visible
		 * @param array $item WC_Order item meta
		 * @param string $document_type
		 */
		if ( ! apply_filters( 'wc_pip_order_item_visible', true, $item, $this->type ) ) {
			$item_data = [];
		}

		/**
		 * Filters the table row item data.
		 *
		 * @since 3.0.0
		 *
		 * @param array $item_data The item data.
		 * @param array $item WC_Order item meta.
		 * @param \WC_Product $product Product object.
		 * @param int $order_id WC_Order ID.
		 * @param string $document_type The document type.
		 * @param \WC_PIP_Document $document The document object.
		 */
		return apply_filters( 'wc_pip_document_table_row_item_data', $item_data, $item, $product, $this->order_id, $this->type, $this );
	}


	/**
	 * Gets the order item data.
	 *
	 * @since 3.0.0
	 *
	 * @param string $item_id The item id
	 * @param array|\WC_Order_Item_Product $item The item data
	 * @param \WC_Product $product The product object
	 * @return array
	 */
	abstract protected function get_order_item_data( $item_id, $item, $product );


	/**
	 * Get order item product id
	 *
	 * @since 3.0.5
	 * @param int $item_id Order item id
	 * @return string
	 */
	protected function get_order_item_id_html( $item_id ) {
		return '<span data-item-id="' . esc_attr( $item_id ) . '"></span>';
	}


	/**
	 * Get order item SKU
	 *
	 * @since 3.0.0
	 * @param \WC_Product $product Product corresponding to order item
	 * @param string|array $item Order item (optional, used in filter, defaults to empty string)
	 * @return string
	 */
	protected function get_order_item_sku_html( $product, $item = '' ) {

		$sku = $product instanceof \WC_Product ? $product->get_sku() : '';

		/**
		 * Filter the order item SKU
		 *
		 * @since 3.1.3
		 * @param string $sku The product SKU
		 * @param array $item Order item (optional, might be empty string)
		 * @param string $type The document type
		 * @param \WC_Product $product The product object
		 * @param \WC_Order $order The order object
		 */
		$sku = apply_filters( 'wc_pip_order_item_sku', $sku, $item, $this->type, $product, $this->order );

		return '<span class="sku">' . $sku . '</span>';
	}


	/**
	 * Get product CSS classes
	 *
	 * @since 3.0.5
	 * @param \WC_Product $product Product object
	 * @param string|array $item Order item (optional, used in filter, defaults to empty string)
	 * @return string
	 */
	protected function get_order_item_product_classes( $product, $item = '' ) {

		/**
		 * Filters the order item product classes
		 *
		 * @since 3.0.0
		 * @param string[] $product_classes Array of strings to be used as item classes
		 * @param \WC_Product $product The product object
		 * @param array|string $item Order item (optional, might be an empty string)
		 * @param string $type Document type
		 */
		$product_classes = apply_filters( 'wc_pip_document_table_product_class', array( 'product-' . $product->get_type() ), $product, $item, $this->type );

		return implode( ' ', array_map( 'sanitize_html_class', $product_classes ) );
	}


	/**
	 * Gets an order item name.
	 *
	 * @since 3.0.0
	 *
	 * @param \WC_Product $product product corresponding to order item
	 * @param string|array $item order item (optional, used in filter defaults to empty string)
	 * @return string
	 */
	protected function get_order_item_name_html( $product, $item = '' ) {

		$has_product   = $product instanceof \WC_Product;
		$wrapper_class = 'product product-name';
		$is_visible    = false;

		if ( $has_product ) {

			$product_name  = wp_strip_all_tags( $product->get_title() );
			$wrapper_class = $this->get_order_item_product_classes( $product, $item ) . ' ' . $wrapper_class;
			$is_visible    = $product->is_visible();

			if ( ! $is_visible && current_user_can( 'manage_woocommerce' ) && 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
				$is_visible = true;
			}

			if ( $is_visible ) {
				$product_name = sprintf( '<a href="%1$s" target="_blank">%2$s</a>', get_permalink( $product->get_id() ), $product_name );
			}

		} elseif ( ( $item instanceof WC_Order_Item_Product || is_array( $item ) ) && ! empty( $item['name'] ) ) {

			$product_name = wp_strip_all_tags( $item['name'] );
		}

		if ( isset( $product_name ) ) {

			/**
			 * Filter the order item name.
			 *
			 * @since 3.0.8
			 * @param string $product_name the product name
			 * @param string|array $item the order item
			 * @param bool $is_visible whether the product is visible in the catalog
			 * @param string $type The document type
			 * @param \WC_Product $product The product object
			 * @param \WC_Order $order The order object
			 */
			$product_name = apply_filters( 'wc_pip_order_item_name', $product_name, $item, $is_visible, $this->type, $product, $this->order );

			$product_name_html = '<span class="' . esc_attr( $wrapper_class ) . '">' . $product_name . '</span>';

		} else {

			$product_name_html = '<span class="product">&ndash;</span>';
		}

		return $product_name_html;
	}


	/**
	 * Returns an order item price amount in HTML.
	 *
	 * @since 3.0.0
	 *
	 * @param int|string $item_id item id
	 * @param array|\WC_Order_Item $item order item object or array data
	 * @return string formatted price
	 */
	protected function get_order_item_price_html( $item_id, $item ) {

		if ( ! $this->order instanceof \WC_Order ) {

			$item_price_html = '<span class="price">' . wc_price( 0 ) . '</span>';

		} else {

			if ( $this->show_prices_excluding_tax ) {
				$this->set_item_prices_tax_exclusive();
			}

			$item_price_html = '<span class="price">' . $this->order->get_formatted_line_subtotal( $item )  . '</span>';

			// handle refunds
			if ( $this->may_have_refunds ) {

				$refund_html = $this->get_order_item_refunded_price_html( $item_id, $item );

				if ( '' !== $refund_html ) {
					$item_price_html = $refund_html;
				}
			}

			if ( $this->show_prices_excluding_tax ) {
				$this->set_item_prices_default_tax_handling();
			}
		}

		return $item_price_html;
	}


	/**
	 * Gets an item price refunded amount in HTML.
	 *
	 * @see \WC_PIP_Document::get_order_item_price_html()
	 *
	 * @since 3.5.0
	 *
	 * @param int $item_id item ID
	 * @param \WC_Order_Item_Product $item order item object or array
	 * @return string HTML
	 */
	private function get_order_item_refunded_price_html( $item_id, $item ) {

		$item_refunded_html = '';
		$refund_amount      = abs( $this->order->get_total_refunded_for_item( $item_id ) );

		if ( $refund_amount > 0 ) {

			$item_total    = $this->order->get_line_total( $item );
			$tax_inclusive = 'incl' === get_option( 'woocommerce_tax_display_cart' );

			if ( $tax_inclusive ) {

				/* @type array|\WC_Order_Item_Tax[] $taxes */
				$taxes = $this->order->get_taxes();

				if ( ! empty( $taxes ) ) {

					foreach ( $taxes as $tax ) {

						if ( is_array( $tax ) && isset( $tax['tax_amount'] ) && is_numeric( $tax['tax_amount'] ) ) {
							$tax_total = $tax['tax_amount'];
						} elseif ( $tax instanceof \WC_Order_Item_Tax && is_callable( $tax, 'get_tax_total' ) ) {
							$tax_total = $tax->get_tax_total();
						}

						if ( ! empty( $tax_total ) && is_numeric( $tax_total ) ) {
							$refund_amount -= (float) $tax_total;
						}
					}
				}
			}

			$refund_total = wc_price( max( 0, $item_total - $refund_amount ), [
				'currency'     => $this->order->get_currency(),
				'ex_tax_label' => ! $tax_inclusive,
			] );

			$item_refunded_html = '<span class="price"><del>' . $this->order->get_formatted_line_subtotal( $item ) . '</del></span> <span class="refund-price">' . $refund_total . '</span>';
		}

		return $item_refunded_html;
	}


	/**
	 * Get order item quantity
	 *
	 * @since 3.0.0
	 * @param int|string $item_id Item id
	 * @param array|\WC_Order_Item $item order item object or array
	 * @return string
	 */
	protected function get_order_item_quantity_html( $item_id, $item ) {

		/**
		 * Filters the order item quantity.
		 *
		 * @since 3.5.1
		 *
		 * @param int $item_quantity order item quantity (could be made into a float by some third party plugins)
		 * @param array|\WC_Order_Item $item order item object or array
		 */
		$item_quantity_raw = apply_filters( 'wc_pip_get_order_item_quantity', isset( $item['qty'] ) ? max( 0, (int) $item['qty'] ) : 0, $item );
		$item_quantity     = '<span class="quantity">' . $item_quantity_raw . '</span>';

		// Handle refunds
		if ( $this->may_have_refunds ) {

			$refund_quantity = $item_quantity_raw - absint( $this->order->get_qty_refunded_for_item( $item_id ) );

			// has the quantity changed?
			if ( $refund_quantity !== $item_quantity_raw ) {
				$item_quantity = '<span class="quantity"><del>' . $item_quantity_raw . '</del></span> <span class="refund-quantity">' . $refund_quantity . '</span>';
			}
		}

		return $item_quantity;
	}


	/**
	 * Get order item weight
	 *
	 * @since 3.0.0
	 * @param string|int $item_id Item id
	 * @param array $item WC_Order item
	 * @param \WC_Product $product Corresponding product object
	 * @return string
	 */
	protected function get_order_item_weight_html( $item_id, $item, $product ) {

		$item_weight = 0;

		// we need to use "view" context for a variation to inherit the parent weight in WC 3.0+
		// check for a product object in case this product was deleted
		$product_weight = $product ? $product->get_weight() : 0;

		if ( is_numeric( $product_weight ) && $this->order instanceof \WC_Order ) {

			$item_quantity   = isset( $item['qty'] ) ? max( 0, (int) $item['qty'] ) : 0;
			$refund_quantity = $this->may_have_refunds ? absint( $this->order->get_qty_refunded_for_item( $item_id ) ) : 0;

			/**
			 * Filters the weight of the order item.
			 *
			 * @since 3.0.0
			 * @param float $items_weight Total weight of the item by its quantity
			 * @param string $item_id Item id
			 * @param array $item Item
			 * @param \WC_Product $product WC Product object
			 * @param \WC_Order $order WC Order object
			 */
			$item_weight = apply_filters( 'wc_pip_order_item_weight', max( 0, (float) ( $product_weight * max( 0, $item_quantity - $refund_quantity ) ) ), $item_id, $item, $product, $this->order );
		}

		return '<span class="weight">' . $item_weight . '</span>';
	}


	/**
	 * Get item meta display.
	 *
	 * @since 3.0.0
	 *
	 * @param string|int $item_id order item id
	 * @param array|\WC_Order_Item_Product $item order item data
	 * @param \WC_Product $product a product object
	 * @return bool true if to display flat (single line) or false if multi line (e.g. definition list)
	 */
	protected function get_order_item_meta_display( $item_id, $item, $product ) {

		/**
		 * Filters if item meta should be displayed flat. Defaults to definition list (item meta is displayed on new lines).
		 *
		 * @since 3.0.0
		 *
		 * @param bool $flat display item meta in new lines (flat === false) or a single line (flat === true)
		 * @param \WC_Product $product the product object
		 * @param string $item_id order item id
		 * @param array|\WC_Order_Item_Product $item item data
		 * @param string $type PIP Document type
		 * @param \WC_Order $order the order object
		 */
		return (bool) apply_filters( 'wc_pip_document_table_row_item_meta_flat', false, $product, $item_id, $item, $this->type, $this->order );
	}


	/**
	 * Get meta data for an order item.
	 *
	 * This backwards compatible method ensures a similar output for the order item meta before and after WooCommerce 3.0.
	 *
	 * @since 3.3.2
	 *
	 * @param int $item_id the order item ID
	 * @param array|\WC_Order_Item_Product $item the order item to get meta for
	 * @param \WC_Product|\WC_Product_Variable|\WC_Product_Variation $product a product type
	 * @return string HTML
	 */
	protected function get_order_item_meta( $item_id, $item, $product ) {

		if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.1' ) ) {

			$flat      = $this->get_order_item_meta_display( $item_id, $item, $product );
			$meta_data = $item->get_formatted_meta_data( '_', true );
			$meta_list = array();
			$output    = '';

			foreach ( $meta_data as $meta ) {

				if ( $flat ) {
					$meta_list[] = wp_kses_post( $meta->display_key . ': ' . $meta->display_value );
				} else {
					$meta_list[] = '
						<dt class="variation-' . sanitize_html_class( sanitize_text_field( $meta->key ) ) . '">' . wp_kses_post( $meta->display_key ) . ':</dt>
						<dd class="variation-' . sanitize_html_class( sanitize_text_field( $meta->key ) ) . '">' . wp_kses_post( make_clickable( $meta->display_value ) ) . '</dd>
					';
				}
			}

			if ( ! empty( $meta_list ) ) {

				if ( $flat ) {
					$output .= implode( ", \n", $meta_list );
				} else {
					$output .= '<dl class="variation">' . implode( '', $meta_list ) . '</dl>';
				}
			}

			$item_meta = $output;

		} else {

			$meta_data = new \WC_Order_Item_Meta( $item );
			$item_meta = $meta_data->display( $this->get_order_item_meta_display( $item_id, $item, $product ), true, '_', ', ' );
		}

		/**
		 * Filters an order item list of attached meta data.
		 *
		 * @since 3.6.1
		 *
		 * @param string $item_meta may contain HTML
		 * @param int $item_id order item ID
		 * @param array|\WC_Order_Item order item object or array data
		 * @param \WC_Product $product related product object
		 * @param \WC_PIP_Document $document document object
		 */
		return (string) apply_filters( 'wc_pip_order_item_meta_data_list', $item_meta, $item_id, $item, $product, $this );
	}


	/**
	 * Gets an order item meta.
	 *
	 * @since 3.0.0
	 *
	 * @param string|int $item_id order item id
	 * @param array|\WC_Order_Item_Product $item order item data
	 * @param \WC_Product $product the product the item is related to
	 * @return string HTML
	 */
	protected function get_order_item_meta_html( $item_id, $item, $product ) {

		$has_product   = $product instanceof \WC_Product;
		$wrapper_class = 'product-meta';

		if ( $has_product ) {
			$wrapper_class = $this->get_order_item_product_classes( $product, $item ) . ' ' . $wrapper_class;
		}

		$item_meta_html = '<div class="' . esc_attr( $wrapper_class ) . '">';

		ob_start();

		/**
		 * Fires before order item meta HTML.
		 *
		 * @since 3.0.0
		 *
		 * @param string|int $item_id order item id
		 * @param array $item order item data
		 * @param \WC_Order $order order object
		 */
		do_action( 'wc_pip_order_item_meta_start', $item_id, $item, $this->order );

		$item_meta_html .= ob_get_clean() . $this->get_order_item_meta( $item_id, $item, $product );

		ob_start();

		/**
		 * Fires after order item meta HTML.
		 *
		 * @since 3.0.0
		 *
		 * @param string|int $item_id order item id
		 * @param array $item order item data
		 * @param \WC_Order $order order object
		 */
		do_action( 'wc_pip_order_item_meta_end', $item_id, $item, $this->order );

		$item_meta_html .= ob_get_clean();

		/**
		 * Toggles whether to display a purchase note after item meta.
		 *
		 * @since 3.1.2
		 *
		 * @param bool $show_purchase_note whether to show or not (default true)
		 * @param string $document_type the document type
		 * @param \WC_Product $product the product to show a purchase note for
		 */
		$show_purchase_note = (bool) apply_filters( 'wc_pip_order_item_meta_show_purchase_note', true, $this->type, $product );

		if ( $has_product && true === $show_purchase_note && $this->order->is_paid() ) {

			$purchase_note = $product->get_purchase_note();

			$item_meta_html .= ! empty( $purchase_note ) ? '<br><blockquote>' . wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ) . '</blockquote>' : '';
		}

		$item_meta_html .= '</div>';

		/**
		 * Filters the whole order item meta HTML block.
		 *
		 * @since 3.0.9
		 *
		 * @param string $item_meta_html the item meta HTML
		 * @param int $item_id order item id
		 * @param array $item order item data
		 * @param string $type document type
		 * @param \WC_Order $order order object
		 */
		return apply_filters( 'wc_pip_order_item_meta', $item_meta_html, $item_id, $item, $this->type, $this->order );
	}


	/**
	 * Get table footer
	 *
	 * This method should be overridden by child classes
	 * to output a table footer with column totals and such
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function get_table_footer() {
		return array();
	}


	/**
	 * Returns the HTML tag to be used for the table footer element.
	 *
	 * Normally 'tfoot' should be preferred, except that some browsers may print the footer on each page, breaking design.
	 *
	 * @since 3.4.0
	 *
	 * @return string HTML tag (either 'tbody' or 'tfoot') - default 'tbody' since v3.4.0
	 */
	public function get_table_footer_html_tag() {

		/**
		 * Filters the HTML tag used for the document table footer.
		 *
		 * @since 3.4.0
		 *
		 * @param string $tag either 'tbody' (default since v3.4.0) or 'tfoot'
		 * @param \WC_PIP_Document $document the current document being printed
		 */
		$tag = apply_filters( 'wc_pip_document_table_footer_html_tag', 'tbody', $this );

		// ensures a valid table tag is always returned
		return in_array( $tag, array( 'tbody', 'tfoot' ), true ) ? $tag : 'tbody';
	}


	/**
	 * Get action status
	 *
	 * @since 3.0.0
	 * @param $action
	 * @return bool
	 */
	private function get_document_action_status( $action ) {
		return $action ? ( (int) $this->get_document_action_count( $action ) > 0 ) : false;
	}


	/**
	 * Get document action count
	 *
	 * @since 3.0.0
	 * @param string $action Type of action to get count for
	 * @return int Count
	 */
	private function get_document_action_count( $action ) {

		/**
		 * Filters the document action counters.
		 *
		 * @since 3.0.0
		 * @param array $action_counters
		 */
		if ( ! $this->order instanceof \WC_Order || ! in_array( $action, (array) apply_filters( 'wc_pip_document_action_counters', array( 'print', 'email' ) ), true ) ) {
			return 0;
		}

		// Convert dashes to underscores.
		$document_type = str_replace( '-', '_', $this->type );

		// Get count.
		return max( 0, (int) $this->order->get_meta( "_wc_pip_{$document_type}_{$action}_count" ) );
	}


	/**
	 * Updates the document action count.
	 *
	 * @since 3.0.0
	 *
	 * @param string $action Action count to update
	 * @param string|int $amount If unspecified will bump count by one
	 * @return bool
	 */
	private function update_document_action_count( $action, $amount = '' ) {

		/** This filter is documented in includes/abstract-wc-pip-document.php */
		if ( ! $this->order instanceof \WC_Order || ! in_array( $action, (array) apply_filters( 'wc_pip_document_action_counters', array( 'print', 'email' ) ), true ) ) {
			return false;
		}

		// bump + 1 when $amount is unspecified
		if ( '' === $amount || ! is_numeric( $amount )  ) {
			$amount = max( 0, (int) $this->get_document_action_count( $action ) ) + 1;
		}

		// convert dashes to underscores and get the current count
		$document_type = str_replace( '-', '_', $this->type );

		// update action count (accounts for bulk actions too)
		if ( $this->order_ids && is_array( $this->order_ids ) ) {

			$success = [];

			foreach ( $this->order_ids as $order_id ) {

				if ( $order = wc_get_order( $order_id ) ) {

					$order->update_meta_data( "_wc_pip_{$document_type}_{$action}_count", $amount );
					$order->save_meta_data();

					$success[] = true;
				}
			}

			return in_array( true, $success, true );
		}

		$this->order->update_meta_data( "_wc_pip_{$document_type}_{$action}_count", $amount );
		$this->order->save_meta_data();

		return true;
	}


	/**
	 * Output the template for print
	 *
	 * @since 3.0.0
	 */
	public function print_document() {

		// unhook the admin bar to compensate for crappy plugins
		// which may force it to be rendered on the print window
		remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );

		/**
		 * Fires immediately before the document is output for printing.
		 *
		 * @see \WC_PIP_Document::upon_print() among actions performed here
		 *
		 * @since 3.0.0
		 * @param string $type WC_PIP_Document type
		 * @param int $order_id WC_Order id associated with the document
		 * @param int[] $order_ids Array of WC_Order ids associated with the document
		 */
		do_action( 'wc_pip_print', $this->type, $this->order_id, $this->order_ids );

		// Output the template
		$this->output_template( array( 'action' => 'print' ) );
	}


	/**
	 * Update print count upon print action
	 *
	 * @since 3.0.0
	 * @param string $document_type Document type
	 * @param int $order_id WC_Order id
	 */
	public function upon_print( $document_type, $order_id ) {

		// prevent duplicating count in bulk actions
		if ( $document_type !== $this->type || (int) $order_id !== (int) $this->order_id ) {
			return;
		}

		// Bump print count only when a shop manager or admin is printing from back end
		if ( is_admin() && wc_pip()->get_handler_instance()->current_admin_user_can_manage_documents() ) {

			$this->update_print_count();
		}
	}


	/**
	 * Get document print count
	 *
	 * @since 3.0.0
	 * @return int
	 */
	public function get_print_count() {
		return $this->get_document_action_count( 'print' );
	}


	/**
	 * Get print status
	 *
	 * @since 3.0.0
	 * @return bool
	 */
	public function get_print_status() {
		return $this->get_document_action_status( 'print' );
	}


	/**
	 * Update print count for the document
	 *
	 * @since 3.0.0
	 * @param int|string $amount Optional, if empty will bump the saved value
	 * @return bool True on success, false on failure
	 */
	public function update_print_count( $amount = '' ) {
		return $this->update_document_action_count( 'print', $amount );
	}


	/**
	 * Send document by email
	 *
	 * @since 3.0.0
	 */
	public function send_email() {

		if ( ! is_object( $this->order ) ) {
			return;
		}

		// load the WooCommerce mailer
		WC()->mailer();

		$document_type = str_replace( '-', '_', $this->type );

		/**
		 * Triggers the document email.
		 *
		 * @since 3.0.0
		 * @param string $type PIP Document type
		 * @param \WC_PIP_Document $document PIP Document object
		 * @param \WC_Order $order Order object
		 */
		do_action( "wc_pip_send_email_{$document_type}", $this );
	}


	/**
	 * Update email sent count upon send email action
	 *
	 * @since 3.0.0
	 * @param string $document_type WC_PIP_Document type
	 * @param int $order_id WC_Order id associated to the document
	 */
	public function upon_send_email( $document_type, $order_id ) {

		// prevent duplicating count in bulk actions
		if ( $document_type !== $this->type || (int) $order_id !== (int) $this->order_id ) {
			return;
		}

		$this->update_email_count();
	}


	/**
	 * Get document email count
	 *
	 * @since 3.0.0
	 * @return int
	 */
	public function get_email_count() {
		return $this->get_document_action_count( 'email' );
	}


	/**
	 * Get print status
	 *
	 * @since 3.0.0
	 * @return bool
	 */
	public function get_sent_email_status() {
		return $this->get_document_action_status( 'email' );
	}


	/**
	 * Update email count for the document
	 *
	 * @since 3.0.0
	 * @param int|string $amount Optional, if empty will bump the saved value
	 * @return bool True on success, false on failure
	 */
	public function update_email_count( $amount = '' ) {
		return $this->update_document_action_count( 'email', $amount );
	}


	/**
	 * Determines whether an intangible item should be hidden.
	 *
	 * @since 3.0.0
	 *
	 * @param \WC_Order_Item_Product $item order item object
	 * @return bool default false (do not hide)
	 */
	protected function maybe_hide_virtual_item( $item ) {

		// bail for invoices as all order items must always be listed at all times
		if ( ! is_object( $this->order ) || 'invoice' === $this->type ) {
			return false;
		}

		if ( is_array( $item ) || $item instanceof \WC_Order_Item ) {
			$product = $item->get_product();
		} else {
			$product = $item;
		}

		if ( ! $product instanceof \WC_Product ) {
			return false;
		}

		// although we might be simply bailing out if the product isn't virtual,
		// some third party extensions might use this for products that aren't marked
		// as virtual but de facto are (e.g. Product Bundles, Composites...), so we
		// run the filter anyway, while honouring the admin setting to hide or not
		$hide_virtual_item = $this->hide_virtual_items && $product->is_virtual();

		/**
		 * Filters whether we're hiding a virtual item from packing lists and pick lists.
		 *
		 * @since 3.1.1
		 *
		 * @param bool $hide_virtual_item Whether we're hiding an item or not
		 * @param \WC_Product $product Product object
		 * @param array|\WC_Order_Item_Product $item Order item
		 * @param \WC_Order $order Order object
		 */
		return (bool) apply_filters( "wc_pip_{$this->type}_hide_virtual_item", $hide_virtual_item, $product, $item, $this->order );
	}


	/**
	 * Determines whether a product that has been deleted from the store should be displayed in documents.
	 *
	 * @since 3.6.5
	 *
	 * @param null|false|\WC_Product $product a product object or empty value when not found
	 * @return bool default false (do not hide)
	 */
	protected function maybe_hide_deleted_product( $product ) {

		// bail for invoices as all order items must always be listed at all times
		if ( 'invoice' === $this->type ) {
			return false;
		}

		/**
		 * Filters whether we're hiding a product that has been deleted from the store.
		 *
		 * @since 3.6.5
		 *
		 * @param bool $hide_delete_product default false
		 * @param null|false|\WC_Product $product a product object, if found
		 */
		return (bool) apply_filters( "wc_pip_{$this->type}_hide_deleted_product", false, $product );
	}


	/**
	 * Passes a filter to turn item prices to be tax exclusive.
	 *
	 * Forces item prices to be tax exclusive.
	 *
	 * @since 3.5.0
	 */
	protected function set_item_prices_tax_exclusive() {

		add_filter( 'pre_option_woocommerce_tax_display_cart',  array( $this, 'force_item_prices_tax_exclusive' ), 999 );
		add_filter( 'pre_option_woocommerce_tax_total_display', array( $this, 'force_itemized_tax_total_display' ), 999 );
	}


	/**
	 * Removes a filter to turn item prices to be tax exclusive.
	 *
	 * Restores prices tax handling to WooCommerce saved setting.
	 *
	 * @since 3.5.0
	 */
	protected function set_item_prices_default_tax_handling() {

		remove_filter( 'pre_option_woocommerce_tax_display_cart',  array( $this, 'force_item_prices_tax_exclusive' ), 999 );
		remove_filter( 'pre_option_woocommerce_tax_total_display', array( $this, 'force_itemized_tax_total_display' ), 999 );
	}


	/**
	 * Filters the value of 'woocommerce_tax_display_cart' to set prices to be tax exclusive.
	 *
	 * @see \WC_PIP_Document::set_item_prices_tax_exclusive()
	 * @see \WC_PIP_Document::set_item_prices_default_tax_handling()
	 *
	 * @internal
	 *
	 * @since 3.5.0
	 *
	 * @return string
	 */
	public function force_item_prices_tax_exclusive() {
		return 'excl';
	}


	/**
	 * Filters the value of 'woocommerce_tax_total_display' to ensure the taxes in totals are returned itemized.
	 *
	 * @see \WC_PIP_Document::set_item_prices_tax_exclusive()
	 * @see \WC_PIP_Document::set_item_prices_default_tax_handling()
	 *
	 * @internal
	 *
	 * @since 3.5.0
	 *
	 * @return string
	 */
	public function force_itemized_tax_total_display() {
		return 'itemized';
	}


	/**
	 * Handles deprecated properties.
	 *
	 * @since 3.8.2
	 * @deprecated
	 *
	 * TODO remove deprecated methods by the next major version or 12 months after deprecation {FN 2020-02-19}
	 *
	 * @param string $property property name
	 * @return mixed
	 */
	public function __get( $property ) {

		$deprecated = __CLASS__ . '::$' . $property . ' property';

		switch ( $property ) {

			case 'items' :
				// TODO remove this by version 4.0.0 or February 2021 {FN 2020-02-19}
				wc_deprecated_function( $deprecated, '3.8.2', __CLASS__ . '::$order->get_items() method' );
				return $this->order instanceof \WC_Order ? $this->order->get_items() : [];

			case 'refunds' :
				// TODO remove this by version 4.0.0 or February 2021 {FN 2020-02-19}
				wc_deprecated_function( $deprecated, '3.8.2', __CLASS__ . '::$order->get_refunds() method' );
				return $this->order instanceof \WC_Order ? $this->order->get_refunds() : [];

			case 'has_refunds' :
				// TODO remove this by version 4.0.0 or February 2021 {FN 2020-02-19}
				wc_deprecated_function( $deprecated, '3.8.2', __CLASS__ . '::$may_have_refunds property' );
				return $this->may_have_refunds;

			default :
				return null;
		}
	}


}
