<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

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
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	/**
	 * @param array $clauses
	 *
	 * @return array
	 */
	public function sorting_clauses_callback( $clauses ) {
		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['join'] .= "
            LEFT JOIN (
                SELECT *
                FROM (
                    SELECT DISTINCT acsort_tr.object_id, acsort_t.slug
					FROM $wpdb->term_taxonomy AS acsort_tt
					INNER JOIN $wpdb->term_relationships acsort_tr
						ON acsort_tt.term_taxonomy_id = acsort_tr.term_taxonomy_id
					INNER JOIN $wpdb->terms AS acsort_t
						ON acsort_t.term_id = acsort_tt.term_id
					WHERE taxonomy = '$this->taxonomy'
					ORDER BY acsort_t.slug ASC
				) as acsort_main
				GROUP BY acsort_main.object_id
            ) as acsort_terms ON $wpdb->posts.ID = acsort_terms.object_id
        ";
		$clauses['orderby'] = SqlOrderByFactory::create( "acsort_terms.slug", $this->get_order() );

		return $clauses;
	}

}