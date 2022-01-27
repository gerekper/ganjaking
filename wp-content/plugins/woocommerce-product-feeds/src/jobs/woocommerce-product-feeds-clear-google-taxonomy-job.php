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

		$sql = "DELETE FROM ${table_prefix}woocommerce_gpf_google_taxonomy WHERE locale = %s";
		$wpdb->query( $wpdb->prepare( $sql, [ $locale ] ) );

		// Clear the cache expiry transient.
		delete_transient( 'woocommerce_gpf_tax_' . $locale );

		return true;
	}
}
