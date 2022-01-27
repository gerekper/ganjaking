<?php

class WoocommerceProductFeedsRefreshGoogleTaxonomyJob extends AbstractWoocommerceProductFeedsJob {

	public $action_hook = 'woocommerce_product_feeds_refresh_google_taxonomy';

	/**
	 * @param $locale
	 *
	 * @return void
	 */
	public function task( $locale ) {
		global $wpdb, $table_prefix;

		$request = wp_remote_get( 'http://www.google.com/basepages/producttype/taxonomy.' . $locale . '.txt' );
		if ( is_wp_error( $request ) ||
			 ! isset( $request['response']['code'] ) ||
			 200 !== (int) $request['response']['code']
		) {
			return false;
		}

		$taxonomy_data = explode( "\n", $request['body'] );
		// Strip the comment at the top
		array_shift( $taxonomy_data );
		// Strip the extra newline at the end
		array_pop( $taxonomy_data );

		$cnt    = 0;
		$values = [];
		foreach ( $taxonomy_data as $term ) {
			$values[] = $locale;
			$values[] = $term;
			$values[] = strtolower( $term );
			$cnt++;
			if ( 150 === $cnt ) {
				// Bulk insert them all.
				$sql  = "INSERT INTO ${table_prefix}woocommerce_gpf_google_taxonomy";
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
			$sql  = "INSERT INTO ${table_prefix}woocommerce_gpf_google_taxonomy";
			$sql .= '(locale, taxonomy_term, search_term) VALUES ';
			$sql .= str_repeat( '(%s,%s,%s),', $cnt - 1 ) . '(%s,%s,%s)';
			$wpdb->query( $wpdb->prepare( $sql, $values ) );
		}

		// Refresh the transient lifetime
		set_transient(
			'woocommerce_gpf_tax_' . $locale,
			true,
			time() + ( 60 * 60 * 24 * 30 )
		);

		return true;
	}
}
