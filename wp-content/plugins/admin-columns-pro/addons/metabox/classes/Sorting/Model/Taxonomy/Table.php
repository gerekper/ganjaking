<?php

namespace ACA\MetaBox\Sorting\Model\Taxonomy;

use ACA\MetaBox\Sorting\TableOrderByFactory;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Type\DataType;

class Table extends AbstractModel {

	/**
	 * @var string
	 */
	private $table_name;

	/**
	 * @var string
	 */
	private $meta_key;

	public function __construct( $table_name, $meta_key, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->table_name = (string) $table_name;
		$this->meta_key = (string) $meta_key;
	}

	public function get_sorting_vars() {
		add_filter( 'terms_clauses', [ $this, 'pre_term_query_callback' ] );

		return [];
	}

	public function pre_term_query_callback( $clauses ) {
		remove_filter( 'terms_clauses', [ $this, __FUNCTION__ ] );

		$clauses['join'] .= sprintf( "
			LEFT JOIN %s AS acsort_ct 
				ON acsort_ct.ID = t.term_id
			",
			esc_sql( $this->table_name )
		);
		$clauses['orderby'] = sprintf(
			"ORDER BY %s, t.term_id %s",
			TableOrderByFactory::create( $this->meta_key, $this->data_type, $this->get_order() ),
			esc_sql( $this->get_order() )
		);
		$clauses['order'] = '';

		return $clauses;
	}

}