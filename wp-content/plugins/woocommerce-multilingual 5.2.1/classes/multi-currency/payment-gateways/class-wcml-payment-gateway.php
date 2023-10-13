<?php

/**
 * Class WCML_Payment_Gateway
 */
abstract class WCML_Payment_Gateway {

	const OPTION_KEY = 'wcml_payment_gateway_';

	/**
	 * @var string
	 */
	protected $current_currency;

	/**
	 * @var string
	 */
	protected $default_currency;

	/**
	 * @var array
	 */
	protected $active_currencies;

	/**
	 * @var WC_Payment_Gateway
	 */
	protected $gateway;

	/**
	 * @var array
	 */
	private $settings = [];

	/**
	 * @var woocommerce_wpml
	 */
	protected $woocommerce_wpml;

	/**
	 * @param WC_Payment_Gateway $gateway
	 * @param woocommerce_wpml   $woocommerce_wpml
	 */
	public function __construct( WC_Payment_Gateway $gateway, woocommerce_wpml $woocommerce_wpml ) {
		$this->gateway          = $gateway;
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->settings         = get_option( self::OPTION_KEY . $this->get_id(), [] );
	}

	/**
	 * @param string $current_currency
	 * @param array  $active_currencies
	 *
	 * @return string
	 *
	 * @deprecated since 4.9.0, use React component instead.
	 */
	public function get_settings_output( $current_currency, $active_currencies ) {
		return '';
	}

	/**
	 * @deprecated since 4.9.0, use React component instead.
	 */
	public function show() {
		return '';
	}

	abstract public function get_output_model();

	protected function is_current_currency_default() {
		if ( $this->current_currency === $this->default_currency ) {
			return true;
		}
		return false;
	}
	/**
	 * @return WC_Payment_Gateway
	 */
	public function get_gateway() {
		return $this->gateway;
	}

	/**
	 * @return string
	 */
	public function get_id() {
		return $this->gateway->id;
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return $this->gateway->title;
	}

	/**
	 * @return array
	 */
	public function get_settings() {
		return $this->settings;
	}

	private function save_settings() {
		update_option( self::OPTION_KEY . $this->get_id(), $this->settings );
	}

	/**
	 * @param string $currency
	 *
	 * @return array|null
	 */
	public function get_setting( $currency ) {
		$setting = isset( $this->settings[ $currency ] )
			? $this->settings[ $currency ]
			: null;

		return $this->set_currency( $setting, $currency );
	}

	/**
	 * Make sure settings include the currency key.
	 *
	 * @param array|null $setting
	 * @param string     $currency
	 *
	 * @return array|null
	 */
	private function set_currency( $setting, $currency ) {
		if ( is_array( $setting ) && empty( $setting['currency'] ) ) {
			$setting['currency'] = $currency;
		}

		return $setting;
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 */
	public function save_setting( $key, $value ) {
		$this->settings[ $key ] = $value;
		$this->save_settings();
	}

	public function get_active_currencies() {

		$active_currencies = $this->active_currencies;

		if ( ! in_array( $this->current_currency, array_keys( $active_currencies ), true ) ) {
			$active_currencies[ $this->current_currency ] = [];
		}

		return $active_currencies;
	}

}
