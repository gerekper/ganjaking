<?php
/**
 * @package Polylang-WC
 */

/**
 * Filter order queries by language when HPOS is enabled.
 *
 * @since 1.9
 *
 * @phpstan-type QueryClauses array{
 *     fields: non-falsy-string,
 *     join: string,
 *     where: non-falsy-string,
 *     groupby: string,
 *     orderby: non-falsy-string,
 *     limits: non-falsy-string
 * }
 */
class PLLWC_HPOS_Orders_Query {

	/**
	 * Launch hooks.
	 *
	 * @since 1.9
	 *
	 * @return self
	 */
	public function init() {
		add_filter( 'woocommerce_orders_table_query_clauses', array( $this, 'maybe_filter_query_clauses_by_lang' ), 10, 2 );
		return $this;
	}

	/**
	 * Maybe filters the query clauses by language by adding JOIN and WHERE clauses.
	 * Requires WC 7.9.0.
	 *
	 * @since 1.9
	 *
	 * @param string[] $clauses {
	 *     Associative array of the clauses for the query.
	 *
	 *     @type string $fields  The SELECT clause of the query.
	 *     @type string $join    The JOIN clause of the query.
	 *     @type string $where   The WHERE clause of the query.
	 *     @type string $groupby The GROUP BY clause of the query.
	 *     @type string $orderby The ORDER BY clause of the query.
	 *     @type string $limits  The LIMIT clause of the query.
	 * }
	 * @param object   $query   A `Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableQuery` instance.
	 * @return string[]
	 *
	 * @phpstan-param QueryClauses $clauses
	 * @phpstan-return QueryClauses
	 */
	public function maybe_filter_query_clauses_by_lang( $clauses, $query ) {
		global $wpdb;

		if ( ! is_callable( array( $query, 'get_core_mapping_alias' ) ) || ! is_callable( array( $query, 'get' ) ) ) {
			/*
			 * We can't make sure it's Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableQuery because
			 * it's a non-documented class.
			 */
			return $clauses;
		}

		/** @var PLLWC_Order_Language_CPT */
		$store = PLLWC_Data_Store::load( 'order_language' );

		if ( ! $this->are_translated_types( (array) $query->get( 'type' ), $store ) ) {
			return $clauses;
		}

		$languages = $this->get_languages( $query->get( 'lang' ) );

		if ( empty( $languages ) ) {
			return $clauses;
		}

		$tt_ids = array();
		$alias  = $query->get_core_mapping_alias( 'orders' );

		foreach ( $languages as $language ) {
			$tt_ids[] = (int) $language->get_tax_prop( $store->get_tax_language(), 'term_taxonomy_id' );
		}

		$clauses['join']  .= " INNER JOIN {$wpdb->term_relationships} AS pll_tr ON pll_tr.object_id = {$alias}.id";
		$clauses['where'] .= ' AND pll_tr.term_taxonomy_id IN ( ' . implode( ',', $tt_ids ) . ' )';

		return $clauses;
	}

	/**
	 * Returns the list of languages passed to the given query.
	 * Falls back to an array containing the current language if no languages are found in the query.
	 *
	 * @since 1.9
	 *
	 * @param string[]|string|null $languages An array of language codes, a comma-separated list of language codes, or `null`.
	 * @return PLL_Language[] A list of `PLL_Language` objects.
	 */
	private function get_languages( $languages ): array {
		if ( ! isset( $languages ) && ! empty( PLL()->curlang ) ) {
			// `lang` is not set at all: return the current language.
			return array( PLL()->curlang );
		}

		if ( empty( $languages ) ) {
			// `lang` is set to an empty string: don't filter by language.
			return array();
		}

		if ( is_string( $languages ) ) {
			$languages = explode( ',', $languages );
		} elseif ( ! is_array( $languages ) ) {
			return array();
		}

		$languages = array_map( 'trim', $languages );
		$languages = array_map( array( PLL()->model, 'get_language' ), $languages );

		return array_filter( $languages );
	}

	/**
	 * Tells if the order types from the given query are all translated by PLLWC.
	 *
	 * @since 1.9
	 *
	 * @param string[]                 $types Order types.
	 * @param PLLWC_Order_Language_CPT $store The order language store.
	 * @return bool
	 */
	private function are_translated_types( array $types, PLLWC_Order_Language_CPT $store ) {
		if ( empty( $types ) ) {
			// All types are queried: some types, like `shop_order_refund` are not translated.
			return false;
		}

		$translated = $store->translated_post_types( array(), false );

		return empty( array_diff( $types, $translated ) );
	}
}
