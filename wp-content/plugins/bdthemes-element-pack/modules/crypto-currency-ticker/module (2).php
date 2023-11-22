<?php
namespace ElementPack\Modules\CryptoCurrencyTicker;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {


	public function __construct() {
		parent::__construct();

		add_action("wp_ajax_ep_crypto", "ep_crypto");
		add_action("wp_ajax_nopriv_ep_crypto", "ep_crypto");

		add_action("wp_ajax_ep_crypto_data", "ep_crypto_data");
		add_action("wp_ajax_nopriv_ep_crypto_data", "ep_crypto_data");
	}

	public function get_name() {
		return 'crypto-currency-ticker';
	}

	public function get_widgets() {
		$widgets = [
			'Crypto_Currency_Ticker',
		];

		return $widgets;
	}
}
