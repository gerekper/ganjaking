<?php

namespace ACA\GravityForms;

use AC;
use GF_Entry_List_Table;
use GFAPI;

class ListTable implements AC\ListTable {

	private $listTable;

	public function __construct( GF_Entry_List_Table $listTable ) {
		$this->listTable = $listTable;
	}

	public function get_column_value( $column, $id ) {
		ob_start();

		$entry = GFAPI::get_entry( $id );
		$this->listTable->column_default( $entry, $column );

		return ob_get_clean();
	}

	public function get_total_items() {
		return $this->listTable->get_pagination_arg( 'total_items' );
	}

}