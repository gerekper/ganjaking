<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use WP_Query;

class Taxonomy extends AbstractModel {

	/**
	 * @var string
	 */
	private $taxonomy;

	public function __construct( $taxonomy ) {
		parent::__construct();

		$this->taxonomy = (string) $taxonomy;
	}

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ], 10, 2 );

		return [
			'suppress_filters' => false,
		];
	}

	protected function get_where_clause() {
		global $wpdb;

		$conditions[] = $wpdb->prepare( 'taxonomy = %s', $this->taxonomy );
		$conditions[] = $this->show_empty ? ' OR taxonomy IS NULL' : '';

		return vsprintf( ' AND (%s%s)', $conditions );
	}

	/**
	 * Setup clauses to sort by taxonomies
	 *
	 * @param array    $clauses array
	 * @param WP_Query $query
	 *
	 * @return array
	 * @since 3.4
	 */
	public function sorting_clauses_callback( $clauses, $query ) {
		global $wpdb;

		$clauses['join'] .= "
            LEFT OUTER JOIN {$wpdb->term_relationships} AS acsort_termrelation
                ON {$wpdb->posts}.ID = acsort_termrelation.object_id
            LEFT OUTER JOIN {$wpdb->term_taxonomy} AS acsort_term_tax
                ON acsort_termrelation.term_taxonomy_id = acsort_term_tax.term_taxonomy_id
            LEFT OUTER JOIN {$wpdb->terms} AS acsort_terms
                ON acsort_term_tax.term_id = acsort_terms.term_id
        ";
		$clauses['where'] .= $this->get_where_clause();
		$clauses['orderby'] = "acsort_terms.name " . $query->query_vars['order'];
		$clauses['groupby'] = "{$wpdb->posts}.ID";

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}