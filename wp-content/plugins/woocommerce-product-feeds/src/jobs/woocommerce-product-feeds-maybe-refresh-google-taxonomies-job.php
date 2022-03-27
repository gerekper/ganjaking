<?php

class WoocommerceProductFeedsMaybeRefreshGoogleTaxonomiesJob extends AbstractWoocommerceProductFeedsJob {

	public $action_hook = 'woocommerce_product_feeds_maybe_refresh_google_taxonomies';

	/**
	 * @var WoocommerceGpfCommon
	 */
	private $common;

	/**
	 * @param WoocommerceGpfCommon $common
	 */
	public function __construct( WoocommerceGpfCommon $common ) {
		$this->common = $common;
		parent::__construct();
	}

	/**
	 * @return bool
	 */
	public function task() {
		global $wpdb;

		/**
		 * This task is triggered from the following places:
		 *  - Autocomplete callback if > 24hrs have past since the last check [WoocommerceGpfAdmin::ajax_handler()]
		 *  - WooCommerce settings save if country has changed [WoocommerceGpfAdmin::save_general_settings()]
		 *  - Plugin install [woocommerce_gpf_install()]
		 *  - Plugin upgrade to DB version 15 [WoocommerceProductFeedsDbManager::upgrade_db_to_15()]
		 */
		$required_locales = $this->common->get_google_taxonomy_locales();
		$table_name       = $wpdb->prefix . 'woocommerce_gpf_google_taxonomy';
		$stored_locales   = array_flip( $wpdb->get_col( "SELECT DISTINCT(locale) FROM $table_name" ) );
		foreach ( $required_locales as $locale ) {
			// If we have no data for this locale, trigger a refresh.
			if ( ! isset( $stored_locales[ $locale ] ) ) {
				$this->refresh_google_taxonomy( $locale );
				continue;
			}
			// If we have data, but the refresh timestamp has expired, refresh it.
			$cache_key        = 'woocommerce_gpf_tax_ts_' . $locale;
			$locale_cached_ts = get_option( $cache_key, 0 );
			if ( empty( $locale_cached_ts ) || $locale_cached_ts < time() ) {
				$this->refresh_google_taxonomy( $locale );
			}
		}
		foreach ( array_keys( $stored_locales ) as $cached_locale ) {
			if ( ! in_array( $cached_locale, $required_locales, true ) ) {
				$this->clear_google_taxonomy( $cached_locale );
			}
		}

		return true;
	}

	/**
	 * Clear the Google taxonomy cache for a specific locale.
	 */
	private function clear_google_taxonomy( $locale ) {
		$pending = as_get_scheduled_actions(
			[
				'hook'     => 'woocommerce_product_feeds_clear_google_taxonomy',
				'args'     => [ $locale ],
				'status'   => [ \ActionScheduler_Store::STATUS_PENDING, \ActionScheduler_Store::STATUS_RUNNING ],
				'per_page' => 1,
				'orderby'  => 'none',
			],
			'ids'
		);
		if ( empty( $pending ) ) {
			as_schedule_single_action(
				null,
				'woocommerce_product_feeds_clear_google_taxonomy',
				[ $locale ],
				'woocommerce-product-feeds'
			);
		}
	}

	/**
	 * Retrieve the Google taxonomy for a specific locale and cache it to allow users to choose from it.
	 */
	private function refresh_google_taxonomy( $locale ) {
		$pending = as_get_scheduled_actions(
			[
				'hook'     => 'woocommerce_product_feeds_refresh_google_taxonomy',
				'args'     => [ $locale ],
				'status'   => [ \ActionScheduler_Store::STATUS_PENDING, \ActionScheduler_Store::STATUS_RUNNING ],
				'per_page' => 1,
				'orderby'  => 'none',
			],
			'ids'
		);
		if ( empty( $pending ) ) {
			as_schedule_single_action(
				null,
				'woocommerce_product_feeds_refresh_google_taxonomy',
				[ $locale ],
				'woocommerce-product-feeds'
			);
		}
	}

}
