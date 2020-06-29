<?php

namespace ACP\Sorting\Model\Taxonomy;

use ACP\Sorting\AbstractModel;

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

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$clauses['fields'] .= ", COUNT( acsort_termmeta.meta_key ) AS acsort_termcount";
		$clauses['join'] .= $wpdb->prepare( "
			{$join_type} JOIN {$wpdb->termmeta} AS acsort_termmeta 
				ON t.term_id = acsort_termmeta.term_id
				AND acsort_termmeta.meta_key = %s
		", $this->meta_key );

		if ( ! $this->show_empty ) {
			$clauses['join'] .= " AND acsort_termmeta.meta_value <> ''";
		}

		$order = esc_sql( $this->get_order() );

		$clauses['orderby'] = "
			GROUP BY t.term_id 
			ORDER BY acsort_termcount $order
		";
		$clauses['order'] = '';

		remove_action( 'terms_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}