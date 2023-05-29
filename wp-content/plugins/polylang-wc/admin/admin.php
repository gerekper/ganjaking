<?php
/**
 * @package Polylang-WC
 */

/**
 * Helper methods used on admin.
 *
 * @since 0.6
 */
class PLLWC_Admin {

	/**
	 * Get the preferred language for filters.
	 *
	 * @since 0.1
	 *
	 * @return string|false Language slug.
	 *
	 * @phpstan-return non-empty-string|false
	 */
	public static function get_preferred_language() {
		// We rely on the admin language filter.
		if ( ! empty( PLL()->curlang ) ) {
			return PLL()->curlang->slug;
		}

		// Or the current locale (admin language).
		$curlang = PLL()->model->get_language( get_user_locale() );

		if ( ! empty( $curlang ) ) {
			return $curlang->slug;
		}

		// Or the default language.
		/** @var non-empty-string|false */
		return pll_default_language();
	}
}
