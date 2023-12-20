<?php
/**
 * @package Polylang-WC
 */

/**
 * Class managing countries, behaves like the WooCommerce one.
 *
 * @since 1.9.2
 */
class PLLWC_Countries extends WC_Countries {
	/**
	 * Cache of countries.
	 *
	 * @since 1.9.2
	 *
	 * @var array
	 */
	private $countries_cache;

	/**
	 * Gets all countries.
	 * Overrides parent method to disable cached country names
	 * so they can be translated correctly when language is set from the content.
	 * Partially copied from `WC_Countries::get_countries()`.
	 *
	 * @since 1.9.2
	 *
	 * @return array
	 */
	public function get_countries() {
		if ( ! PLL()->curlang instanceof PLL_Language ) { // Do not hit the cache if current language is not defined yet.
			return $this->read_countries();
		}

		if ( empty( $this->countries_cache ) ) {
			$this->countries_cache = $this->read_countries();
		}

		return $this->countries_cache;
	}

	/**
	 * Returns the WooCommerce countries list.
	 *
	 * @since 1.9.2
	 *
	 * @return array
	 */
	private function read_countries() {
		/** This filter is documented in woocommerce/include/class-wc-countries.php */
		$countries = apply_filters( 'woocommerce_countries', include WC()->plugin_path() . '/i18n/countries.php' ); // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomFunction
		if ( apply_filters( 'woocommerce_sort_countries', true ) ) {
			wc_asort_by_locale( $countries );
		}

		return $countries;
	}
}
