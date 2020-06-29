<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Payment {

	private $invoice_id = '';
	private $code = '';
	private $date = '';
	private $currency_rate = '';
	private $amount = 0;
	private $order = null;

	/**
	 * @return string
	 */
	public function get_invoice_id() {
		return apply_filters( 'woocommerce_xero_payment_invoice_id', $this->invoice_id, $this );
	}

	/**
	 * @param string $invoice_id
	 */
	public function set_invoice_id( $invoice_id ) {
		$this->invoice_id = $invoice_id;
	}

	/**
	 * @return string
	 */
	public function get_code() {
		return apply_filters( 'woocommerce_xero_payment_code', $this->code, $this );
	}

	/**
	 * @param string $code
	 */
	public function set_code( $code ) {
		$this->code = $code;
	}

	/**
	 * @return string
	 */
	public function get_date() {
		return apply_filters( 'woocommerce_xero_payment_date', $this->date, $this );
	}

	/**
	 * @param string $date
	 */
	public function set_date( $date ) {
		$this->date = $date;
	}

	/**
	 * @return string
	 */
	public function get_currency_rate() {
		return apply_filters( 'woocommerce_xero_payment_currency_rate', $this->currency_rate, $this );
	}

	/**
	 * @param string $currency_rate
	 */
	public function set_currency_rate( $currency_rate ) {
		$this->currency_rate = $currency_rate;
	}

	/**
	 * @return int
	 */
	public function get_amount() {
		return apply_filters( 'woocommerce_xero_payment_amount', $this->amount, $this );
	}

	/**
	 * @param int $amount
	 */
	public function set_amount( $amount ) {
		$this->amount = floatval( $amount );
	}

	/**
	 * @return WC_Order
	 */
	public function get_order() {
		return $this->order;
	}

	/**
	 * @param WC_Order $order
	 */
	public function set_order( $order ) {
		$this->order = $order;
	}

	/**
	 * Return XML of Payment
	 *
	 * @return string
	 */
	public function to_xml() {

		$xml = '<Payment>';

		// Invoice ID
		$xml .= '<Invoice><InvoiceID>' . $this->get_invoice_id() . '</InvoiceID></Invoice>';

		// Account Code
		$xml .= '<Account><Code>' . $this->get_code() . '</Code></Account>';

		// Date
		$xml .= '<Date>' . $this->get_date() . '</Date>';

		// Currency Rate
		$xml .= '<CurrencyRate>' . $this->get_currency_rate() . '</CurrencyRate>';

		// Amount
		$xml .= '<Amount>' . $this->get_amount() . '</Amount>';

		$xml .= '</Payment>';

		return $xml;
	}

}
