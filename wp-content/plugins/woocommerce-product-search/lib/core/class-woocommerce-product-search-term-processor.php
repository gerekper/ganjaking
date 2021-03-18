<?php
/**
 * class-woocommerce-product-search-term-processor.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 3.6.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Term processor.
 */
class WooCommerce_Product_Search_Term_Processor {

	/**
	 * Index action priority.
	 *
	 * @var int
	 */
	const INDEX_ACTION_PRIORITY = 10000;

	/**
	 * Hooks.
	 */
	public static function init() {

		add_action( 'created_term', array( __CLASS__, 'created_term' ), self::INDEX_ACTION_PRIORITY, 3 );

		add_action( 'edited_term', array( __CLASS__, 'edited_term' ), self::INDEX_ACTION_PRIORITY, 3 );

		add_action( 'delete_term', array( __CLASS__, 'delete_term' ), self::INDEX_ACTION_PRIORITY, 5 );

		add_action( 'edited_term_taxonomies', array( __CLASS__, 'edited_term_taxonomies' ) );

		add_action( 'deleted_term_relationships', array( __CLASS__, 'deleted_term_relationships' ), self::INDEX_ACTION_PRIORITY, 3 );

	}

	/**
	 * Process a new term.
	 *
	 * @param int $term_id the term ID
	 * @param int $term_taxonomy_id the relating ID
	 * @param string $taxonomy the term's taxonomy
	 */
	public static function created_term( $term_id, $term_taxonomy_id, $taxonomy ) {
		self::edited_term( $term_id, $term_taxonomy_id, $taxonomy );
	}

	/**
	 * Process an updated term.
	 *
	 * @since 3.0.0
	 *
	 * @param int $term_id the term ID
	 * @param int $term_taxonomy_id the relating ID
	 * @param string $taxonomy the term's taxonomy
	 */
	public static function edited_term( $term_id, $term_taxonomy_id, $taxonomy ) {

		$product_taxonomies = WooCommerce_Product_Search_Indexer::get_applicable_product_taxonomies();
		if ( in_array( $taxonomy, $product_taxonomies ) ) {
			$indexer = new WooCommerce_Product_Search_Indexer();
			$options = get_option( 'woocommerce-product-search', array() );
			$use_weights = isset( $options[ WooCommerce_Product_Search::USE_WEIGHTS ] ) ? $options[ WooCommerce_Product_Search::USE_WEIGHTS ] : WooCommerce_Product_Search::USE_WEIGHTS_DEFAULT;
			if ( $use_weights ) {

				$indexer->process_term_weights( array( $term_id ) );
			}

			$indexer->preprocess_terms();

			$indexer->process_terms( $term_id );

		}
	}

	/**
	 * Triggered on removal of term.
	 *
	 * @since 3.0.0
	 *
	 * @param int $term term ID
	 * @param int $tt_id term taxonomy ID
	 * @param string $taxonomy taxonomy slug
	 * @param WP_Term|array|WP_Error|null $deleted_term deleted term
	 * @param array $object_ids term object IDs
	 */
	public static function delete_term( $term, $tt_id, $taxonomy, $deleted_term, $object_ids ) {

		$product_taxonomies = WooCommerce_Product_Search_Indexer::get_applicable_product_taxonomies();
		if ( in_array( $taxonomy, $product_taxonomies ) ) {
			$indexer = new WooCommerce_Product_Search_Indexer();
			$indexer->process_terms( intval( $term ) );

			if ( $taxonomy === 'product_cat' ) {
				$indexer->process_term_weights();
			}
		}
	}

	/**
	 * Updated related terms.
	 *
	 * @since 3.0.0
	 *
	 * @param array $edit_tt_ids
	 */
	public static function edited_term_taxonomies( $edit_tt_ids ) {

		global $wpdb;
		if ( is_array( $edit_tt_ids ) && count( $edit_tt_ids ) > 0 ) {

			$taxonomies = WooCommerce_Product_Search_Indexer::get_applicable_product_taxonomies();
			if ( count( $taxonomies ) > 0 ) {
				$edit_tt_ids = array_map( 'intval', $edit_tt_ids );
				$query =
					"SELECT DISTINCT term_id FROM $wpdb->term_taxonomy " .
					'WHERE term_taxonomy_id IN ( ' . implode( ',', $edit_tt_ids ) . ' ) ' .
					'AND ' .
					"taxonomy IN ( '" . implode( "','", esc_sql( $taxonomies ) ) . "' ) ";
				$term_ids = $wpdb->get_col( $query );
				if ( count( $term_ids ) > 0 ) {
					$term_ids = array_unique( array_map( 'intval', $term_ids ) );
					$indexer = new WooCommerce_Product_Search_Indexer();
					$options = get_option( 'woocommerce-product-search', array() );
					$use_weights = isset( $options[ WooCommerce_Product_Search::USE_WEIGHTS ] ) ? $options[ WooCommerce_Product_Search::USE_WEIGHTS ] : WooCommerce_Product_Search::USE_WEIGHTS_DEFAULT;
					if ( $use_weights ) {

						$indexer->process_term_weights( $term_ids );
					}

					$indexer->process_terms( $term_ids );
				}
			}
		}
	}

	/**
	 * Triggered on removal of object-term relationship.
	 *
	 * @since 3.0.0
	 *
	 * @param int $object_id object ID for which the object-term relationship has been deleted
	 * @param array $tt_ids term taxonomy IDs
	 * @param string $taxonomy taxonomy slug
	 */
	public static function deleted_term_relationships( $object_id, $tt_ids, $taxonomy ) {

		WooCommerce_Product_Search_Product_Processor::deleted_post( $object_id );
	}

}
WooCommerce_Product_Search_Term_Processor::init();
