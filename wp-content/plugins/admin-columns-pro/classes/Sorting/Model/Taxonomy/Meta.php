<?php

namespace ACP\Sorting\Model\Taxonomy;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;

class Meta extends AbstractModel {

	/**
	 * @var string
	 */
	protected $meta_key;

	public function __construct( $meta_key, DataType $data_type = null ) {
		parent::__construct( $data_type );

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

		$from = $wpdb->prepare( "
			{$join_type} JOIN {$wpdb->termmeta} AS acsort_termmeta 
				ON t.term_id = acsort_termmeta.term_id
				AND acsort_termmeta.meta_key = %s
		", $this->meta_key );

		if ( ! $this->show_empty ) {
			$from .= " AND acsort_termmeta.meta_value <> ''";
		}

		$clauses['join'] .= $from;
		$clauses['orderby'] = "GROUP BY t.term_ID " . $this->get_order_by();
		$clauses['order'] = '';

		remove_action( 'terms_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

	/**
	 * @return string
	 */
	protected function get_order_by() {
		$order = esc_sql( $this->get_order() );
		$cast_type = CastType::create_from_data_type( $this->data_type )->get_value();

		return "ORDER BY CAST( acsort_termmeta.meta_value AS {$cast_type} ) $order";
	}

}