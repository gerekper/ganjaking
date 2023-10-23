<?php
/**
 * @package Polylang-WC
 */

/**
 * Translates WooCommerce page IDs.
 *
 * @since 0.1
 */
class PLLWC_Filter_WC_Pages {
	/**
	 * Page slugs to translate.
	 *
	 * @var string[]
	 * @phpstan-var non-empty-string[]
	 */
	const TRANSLATED_PAGES = array(
		'myaccount',
		'shop',
		'cart',
		'checkout',
		'terms',
	);

	/**
	 * Adds hooks to translate WC page IDs.
	 *
	 * @since 1.8
	 *
	 * @return void
	 */
	public static function init() {
		foreach ( self::TRANSLATED_PAGES as $page ) {
			// Don't use the filter "woocommerce_get_{$page}_page_id" as some themes (ex: Flatsome) are retrieving directly the option.
			add_filter( 'option_woocommerce_' . $page . '_page_id', 'pll_get_post' );
		}
	}
}
