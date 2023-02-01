<?php

class WoocommerceProductFeedsClearGoogleTaxonomyJob extends AbstractWoocommerceProductFeedsJob {

	/**
	 * @var string
	 */
	public $action_hook = 'woocommerce_product_feeds_clear_google_taxonomy';

	/**
	 * @param $locale
	 *
	 * @return bool
	 */
	public function task( $locale ) {
		global $wpdb, $table_prefix;

		$sql = "DELETE FROM {$table_prefix}woocommerce_gpf_google_taxonomy WHERE locale = %s";
		$wpdb->query( $wpdb->prepare( $sql, [ $locale ] ) );

		// Clear the cache expiry timestamp to force refresh.
		delete_option( 'woocommerce_gpf_tax_ts_' . $locale );

		return true;
	}
}
