<?php

namespace ACP\Sorting\Model\Taxonomy;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;

class MetaCount extends AbstractModel {

	/**
	 * @var string
	 */
	protected $meta_key;

	public function __construct( $meta_key ) {
		parent::__construct();

		$this->meta_key = $meta_key;
	}

	public function get_sorting_vars() {
		add_action( 'terms_clauses', [ $this, 'pre_term_query_callback' ] );

		return [];
	}

	public function pre_term_query_callback( $clauses ) {
		global $wpdb;

		remove_action( 'terms_clauses', [ $this, __FUNCTION__ ] );

		if ( 'COUNT(*)' === $clauses['fields'] ) {
			return $clauses;
		}

		$clauses['join'] .= $wpdb->prepare( "
			LEFT JOIN {$wpdb->termmeta} AS acsort_termmeta 
				ON t.term_id = acsort_termmeta.term_id
				AND acsort_termmeta.meta_key = %s
		", $this->meta_key );

		$clauses['orderby'] = sprintf( "
			GROUP BY t.term_id 
			ORDER BY %s
		", SqlOrderByFactory::create_with_computation( new ComputationType( ComputationType::COUNT ), 'acsort_termmeta.meta_key', $this->get_order(), true ) );
		$clauses['order'] = '';

		return $clauses;
	}

}