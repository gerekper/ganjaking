<?php
/**
 * @package Polylang-WC
 */

/**
 * Setups an object language model when the managed object is a custom post type.
 *
 * @since 1.0
 */
abstract class PLLWC_Object_Language_CPT {

	/**
	 * Get the language taxonomy name.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_tax_language() {
		return 'language';
	}

	/**
	 * Stores the object language in the database.
	 *
	 * @since 1.0
	 *
	 * @param int    $id   Object id.
	 * @param string $lang Language code.
	 * @return void
	 */
	public function set_language( $id, $lang ) {
		pll_set_post_language( $id, $lang );
	}

	/**
	 * Returns the language of an object.
	 *
	 * @since 1.0
	 *
	 * @param int    $id    Object id.
	 * @param string $field Optional, the language field to return ( see PLL_Language ), defaults to 'slug'.
	 * @return string|false Language code, false if no language is associated to this order.
	 */
	public function get_language( $id, $field = 'slug' ) {
		return pll_get_post_language( $id, $field );
	}

	/**
	 * Returns a join clause to add to sql queries when filtering by language is needed directly in query.
	 *
	 * @since 1.0
	 *
	 * @param string $alias Alias for $wpdb->posts table.
	 * @return string Join clause.
	 */
	public function join_clause( $alias = '' ) {
		return PLL()->model->post->join_clause( $alias );
	}

	/**
	 * Returns a where clause to add to sql queries when filtering by language is needed directly in query.
	 *
	 * @since 1.0
	 *
	 * @param PLL_Language|string|string[] $lang A PLL_Language object or a comma separated list of language slug or an array of language slugs.
	 * @return string Where clause.
	 */
	public function where_clause( $lang ) {
		return PLL()->model->post->where_clause( $lang );
	}
}
