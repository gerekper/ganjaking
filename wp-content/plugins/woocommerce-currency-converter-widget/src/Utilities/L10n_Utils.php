<?php
/**
 * Localization utilities.
 *
 * @since 1.9.0
 */

namespace KoiLab\WC_Currency_Converter\Utilities;

/**
 * Class L10n_Utils.
 */
class L10n_Utils {

	/**
	 * Locales info.
	 *
	 * @var array
	 */
	private static $locales;

	/**
	 * Gets the locales' info.
	 *
	 * @since 1.9.0
	 *
	 * @return array
	 */
	public static function get_locales() {
		if ( ! self::$locales ) {
			$filepath = WC_ABSPATH . '/i18n/locale-info.php';
			$locales  = array();

			if ( file_exists( $filepath ) && is_readable( $filepath ) ) {
				$locales = include $filepath;
			}

			self::$locales = $locales;
		}

		return self::$locales;
	}

	/**
	 * Gets the locale info for the specified country.
	 *
	 * @since 1.9.0
	 *
	 * @param string $country_code The country code.
	 * @return array|false The locale info. False if not found.
	 */
	public static function get_locale( $country_code ) {
		$locales = self::get_locales();

		return ( isset( $locales[ $country_code ] ) ? $locales[ $country_code ] : false );
	}
}
