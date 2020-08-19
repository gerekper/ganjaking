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

		$join_type = $this->show_empty ? 'LEFT JOIN' : 'INNER JOIN';

		$clauses['join'] .= "
            {$join_type}(
                SELECT *
                FROM (
                    SELECT DISTINCT acsort_tr.object_id, acsort_t.slug
					FROM {$wpdb->term_taxonomy} AS acsort_tt
					INNER JOIN {$wpdb->term_relationships} acsort_tr
						ON acsort_tt.term_taxonomy_id = acsort_tr.term_taxonomy_id
					INNER JOIN {$wpdb->terms} AS acsort_t
						ON acsort_t.term_id = acsort_tt.term_id
					WHERE taxonomy = '{$this->taxonomy}'
					ORDER BY acsort_t.slug ASC
				) as acsort_main
				GROUP BY acsort_main.object_id
            ) as acsort_terms ON {$wpdb->posts}.ID = acsort_terms.object_id
        ";
		$clauses['orderby'] = "acsort_terms.slug " . $query->query_vars['order'];

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}