<?php

namespace ACP\Filtering\Model\Taxonomy;

use ACP;

class ID extends ACP\Filtering\Model {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
		$this->set_ranged( true );
	}

	public function filter_by_id( $clauses ) {
		global $wpdb;

		$value = $this->get_filter_value();

		if ( $value['min'] ) {
			$clauses['where'] .= $wpdb->prepare( " AND t.term_id >= %s", $value['min'] );
		}

		if ( $value['max'] ) {
			$clauses['where'] .= $wpdb->prepare( " AND t.term_id <= %s", $value['max'] );
		}

		return $clauses;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'terms_clauses', [ $this, 'filter_by_id' ] );

		return $vars;
	}

	public function get_filtering_data() {
		return false;
	}

}