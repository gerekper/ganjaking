<?php

namespace ACP\Column\Taxonomy;

use AC;
use ACP\Filtering;
use ACP\Sorting;

/**
 * @since 2.0.0
 */
class ID extends AC\Column
	implements Sorting\Sortable, Filtering\Filterable {

	public function __construct() {
		$this->set_type( 'column-termid' );
		$this->set_label( __( 'ID', 'codepress-admin-columns' ) );
	}

	public function get_value( $term_id ) {
		return $this->get_raw_value( $term_id );
	}

	public function get_raw_value( $term_id ) {
		return $term_id;
	}

	public function sorting() {
		return new Sorting\Model\OrderBy( 'ID' );
	}

	public function filtering() {
		return new Filtering\Model\Taxonomy\ID( $this );
	}

}