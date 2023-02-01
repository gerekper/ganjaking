<?php

class WoocommerceProductFeedsRefreshGoogleTaxonomyJob extends AbstractWoocommerceProductFeedsJob {

	public $action_hook = 'woocommerce_product_feeds_refresh_google_taxonomy';

	/**
	 * @param $locale
	 *
	 * @return void
	 */
	public function task( $locale ) {
		global $wpdb;

		$request = wp_remote_get( 'http://www.google.com/basepages/producttype/taxonomy.' . $locale . '.txt' );
		if ( is_wp_error( $request ) ||
			 ! isset( $request['response']['code'] ) ||
			 200 !== (int) $request['response']['code']
		) {
			return false;
		}

		$table_name    = $wpdb->prefix . 'woocommerce_gpf_google_taxonomy';
		$taxonomy_data = explode( "\n", $request['body'] );
		// Strip the comment at the top
		array_shift( $taxonomy_data );
		// Strip the extra newline at the end
		array_pop( $taxonomy_data );

		// Remove the old entries for this locale.
		$sql = "DELETE FROM {$table_name} WHERE locale = %s";
		$wpdb->query( $wpdb->prepare( $sql, [ $locale ] ) );

		// Read in the replacements.
		$cnt    = 0;
		$values = [];
		foreach ( $taxonomy_data as $term ) {
			$values[] = $locale;
			$values[] = $term;
			$values[] = strtolower( $term );
			$cnt++;
			if ( 150 === $cnt ) {
				// Bulk insert them all.
				$sql  = "INSERT INTO {$table_name}";
				$sql .= '(locale, taxonomy_term, search_term) VALUES ';
				$sql .= str_repeat( '(%s,%s,%s),', $cnt - 1 ) . '(%s,%s,%s)';
				$wpdb->query( $wpdb->prepare( $sql, $values ) );
				// Prepare for next chunk.
				$cnt    = 0;
				$values = [];
			}
		}
		// Insert the last chunk.
		if ( $cnt ) {
			$sql  = "INSERT INTO {$table_name}";
			$sql .= '(locale, taxonomy_term, search_term) VALUES ';
			$sql .= str_repeat( '(%s,%s,%s),', $cnt - 1 ) . '(%s,%s,%s)';
			$wpdb->query( $wpdb->prepare( $sql, $values ) );
		}

		// Refresh the transient lifetime
		update_option(
			'woocommerce_gpf_tax_ts_' . $locale,
			time() + ( 60 * 60 * 24 * 30 ),
			false
		);

		return true;
	}
}
