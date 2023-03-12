<?php
declare( strict_types=1 );

namespace ACA\MLA\ListTable;

use AC\ListTable;
use MLA_List_Table;
use MLAData;

class MediaLibraryTable implements ListTable {

	private $table;

	public function __construct( MLA_List_Table $table ) {
		$this->table = $table;
	}

	public function get_total_items(): int {
		return $this->table->get_pagination_arg( 'total_items' );
	}

	public function get_column_value( $column, $id ) {
		$item = (object) MLAData::mla_get_attachment_by_id( $id );

		if ( ! $item ) {
			return null;
		}

		$method = 'column_' . $column;

		if ( method_exists( $this->table, $method ) ) {
			return call_user_func( [ $this->table, $method ], $item );
		}

		return $this->table->column_default( $item, $column );
	}

}