<?php
/**
 * Currency utilities.
 *
 * @since 1.9.0
 */

namespace KoiLab\WC_Currency_Converter\Utilities;

/**
 * Class Currency_Utils.
 */
class Currency_Utils {

	/**
	 * Gets the currency by country.
	 *
	 * @since 1.9.0
	 *
	 * @param string $country_code The country code.
	 * @return string|false The currency code. False if not found.
	 */
	public static function get_by_country( $country_code ) {
		$locale_info = L10n_Utils::get_locale( $country_code );

		return ( $locale_info ? $locale_info['currency_code'] : false );
	}
}
