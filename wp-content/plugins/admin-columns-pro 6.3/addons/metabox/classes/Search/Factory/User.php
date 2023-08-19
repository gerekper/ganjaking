<?php

namespace ACA\MetaBox\Search\Factory;

use ACA\MetaBox\Column;
use ACA\MetaBox\Search;
use ACP;
use ACP\Search\Comparison;

final class User extends Search\Factory implements Search\CloneableFactory, Search\TableStorageFactory {

	public function create_table_storage( Column $column, Comparison $default ) {
		if ( $column->is_multiple() ) {
			return new Search\Comparison\Table\Users( $default->get_operators(), $column->get_storage_table(), $column->get_meta_key(), (array) $column->get_field_setting( 'query_args' ), ACP\Search\Value::STRING );
		}

		return new Search\Comparison\Table\User( $default->get_operators(), $column->get_storage_table(), $column->get_meta_key(), (array) $column->get_field_setting( 'query_args' ) );
	}

	public function create_default( Column $column ) {
		return new ACP\Search\Comparison\Meta\User( $column->get_meta_key(), $column->get_meta_type() );
	}

	public function create_cloneable( Column $column ) {
		return false;
	}

}