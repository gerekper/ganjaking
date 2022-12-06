<?php

namespace ACP\Sorting\Model\Taxonomy;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;

class Meta extends AbstractModel {

	/**
	 * @var string
	 */
	protected $meta_key;

	public function __construct( string $meta_key, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->meta_key = $meta_key;
	}

	public function get_sorting_vars() {
		add_filter( 'terms_clauses', [ $this, 'pre_term_query_callback' ] );

		return [];
	}

	public function pre_term_query_callback( $clauses ) {
		remove_filter( 'terms_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		if ( 'COUNT(*)' === $clauses['fields'] ) {
			return $clauses;
		}

		$clauses['join'] .= $wpdb->prepare( "
			LEFT JOIN $wpdb->termmeta AS acsort_termmeta ON t.term_id = acsort_termmeta.term_id
				AND acsort_termmeta.meta_key = %s
		", $this->meta_key );

		if ( 't.term_id' === $clauses['fields'] ) {
			$clauses['orderby'] = "GROUP BY t.term_id";
			$clauses['orderby'] .= "\nORDER BY " . $this->get_order_by();
			$clauses['order'] = '';
		}

		return $clauses;
	}

	protected function get_order_by(): string {
		return SqlOrderByFactory::create( "acsort_termmeta.`meta_value`", $this->get_order(), [ 'cast_type' => (string) CastType::create_from_data_type( $this->data_type ) ] );
	}

}