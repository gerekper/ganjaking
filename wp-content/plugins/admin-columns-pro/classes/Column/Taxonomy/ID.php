<?php

namespace ACP\Column\Taxonomy;

use AC;
use ACP\ConditionalFormat;
use ACP\Filtering;
use ACP\Sorting;

/**
 * @since 2.0.0
 */
class ID extends AC\Column
	implements Sorting\Sortable, Filtering\Filterable, ConditionalFormat\Formattable {

	use ConditionalFormat\IntegerFormattableTrait;

	public function __construct() {
		$this->set_type( 'column-termid' )
		     ->set_label( __( 'ID', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		return (int) $id;
	}

	public function get_raw_value( $id ) {
		return (int) $id;
	}

	public function sorting() {
		return new Sorting\Model\OrderBy( 'ID' );
	}

	public function filtering() {
		return new Filtering\Model\Taxonomy\ID( $this );
	}

}