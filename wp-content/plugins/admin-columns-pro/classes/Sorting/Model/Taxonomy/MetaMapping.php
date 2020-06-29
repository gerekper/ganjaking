<?php

namespace ACP\Sorting\Model\Taxonomy;

class MetaMapping extends Meta {

	/**
	 * @var array
	 */
	protected $sorted_fields;

	public function __construct( $meta_key, $sorted_fields ) {
		parent::__construct( $meta_key );

		$this->sorted_fields = $sorted_fields;
	}

	/**
	 * @return string
	 */
	protected function get_order_by() {
		$fields = implode( "','", array_map( 'esc_sql', $this->sorted_fields ) );

		return sprintf( "ORDER BY FIELD( acsort_termmeta.meta_value, '%s' ) %s, t.term_id", $fields, $this->get_order() );
	}

}