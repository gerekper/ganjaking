<?php
/**
 * Exchange utilities.
 *
 * @since 2.1.0
 */

namespace KoiLab\WC_Currency_Converter\Utilities;

use KoiLab\WC_Currency_Converter\Exchange\Providers\Exchange_Provider;
use KoiLab\WC_Currency_Converter\Exchange\Providers\Koilab_Exchange_Provider;
use KoiLab\WC_Currency_Converter\Exchange\Providers\Open_Exchange_Provider;

/**
 * Class Exchange_Utils.
 */
class Exchange_Utils {

	/**
	 * Gets the exchange provider based on the plugin configuration.
	 *
	 * @since 2.1.0
	 *
	 * @return Exchange_Provider
	 */
	public static function get_provider(): Exchange_Provider {
		$app_id = get_option( 'wc_currency_converter_app_id' );

		if ( $app_id ) {
			$provider = new Open_Exchange_Provider( $app_id );
		} else {
			$provider = new Koilab_Exchange_Provider();
		}

		return $provider;
	}
}
