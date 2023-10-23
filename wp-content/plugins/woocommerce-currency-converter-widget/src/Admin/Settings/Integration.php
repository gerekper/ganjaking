<?php
/**
 * Integration for handling the plugin settings.
 *
 * @since 1.8.0
 */

namespace KoiLab\WC_Currency_Converter\Admin\Settings;

use KoiLab\WC_Currency_Converter\Internal\Admin\Settings\Abstracts\Integration as Integration_Base;

/**
 * Integration class.
 */
class Integration extends Integration_Base {

	/**
	 * Integration ID.
	 *
	 * @var string
	 */
	public $id = 'open_exchange_rates';

	/**
	 * Constructor.
	 *
	 * @since 1.8.0
	 */
	public function __construct() {
		parent::__construct();

		$this->method_title = _x( 'Open Exchange Rates', 'settings page title', 'woocommerce-currency-converter-widget' );
	}

	/**
	 * Initializes the settings API.
	 *
	 * @since 1.8.0
	 */
	public function init_settings_api() {
		$this->settings_api = new General();
	}
}

class_alias( Integration::class, 'Themesquad\WC_Currency_Converter\Admin\Settings\Integration' );
