<?php

namespace WCML\Reports\Categories;

use WCML\Rest\Functions;

class Query implements \IWPML_REST_Action, \IWPML_Backend_Action {

	var $actionWasRemoved = false;

	/**
	 * Registers hooks.
	 */
	public function add_hooks() {
		if ( Functions::isAnalyticsRestRequest() ) {
			add_filter( 'woocommerce_analytics_categories_select_query', [ $this, 'translateCategoryTitles' ] );
		}

		// Remove before priority 10, so that WC Analytics gets all the terms.
		add_action( 'generate_category_lookup_table', [ $this, 'removeWpmlTermClausesFilter' ], 0 );
		add_action( 'generate_category_lookup_table', [ $this, 'addWpmlTermClausesFilter' ], 20 );
	}

	/**
	 * @param object $results Categories query (note: in March 2021, WC Admin code is showing a PHPDoc returning an array which is not what we get)
	 *
	 * @return object
	 */
	public function translateCategoryTitles( $results ) {
		$results->data = wpml_collect( $results->data )
			->map( function( $row ) {
				if ( $row['extended_info']['name'] ) {
					$row['category_id'] = apply_filters( 'wpml_object_id', $row['category_id'], 'product_cat', true );
					$term = get_term( $row['category_id'] );
					if ( $term ) {
						$row['extended_info']['name'] = $term->name;
					}
				}
				return $row;
			} )->toArray();

		return $results;
	}

	public function removeWpmlTermClausesFilter() {
		global $sitepress;

		$this->actionWasRemoved = remove_filter( 'terms_clauses', [ $sitepress, 'terms_clauses' ] );
	}

	public function addWpmlTermClausesFilter() {
		global $sitepress;

		if ( $this->actionWasRemoved ) {
			$this->actionWasRemoved = false;
			add_filter( 'terms_clauses', [ $sitepress, 'terms_clauses' ], 10, 3 );
		}
	}

}
