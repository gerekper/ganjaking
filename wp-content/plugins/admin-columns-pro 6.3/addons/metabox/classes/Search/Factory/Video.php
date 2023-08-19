<?php

namespace ACA\MetaBox\Search\Factory;

use ACA\MetaBox\Column;
use ACA\MetaBox\Search;
use ACP\Search\Comparison;

final class Video extends Search\Factory implements Search\CloneableFactory, Search\TableStorageFactory {

	public function create_table_storage( Column $column, Comparison $default ) {
		return new Search\Comparison\Table\Media( $default->get_operators(), $column->get_storage_table(), $column->get_meta_key(), 'video', $default->get_value_type() );
	}

	public function create_default( Column $column ) {
		return new Search\Comparison\Video( $column->get_meta_key(), $column->get_meta_type() );
	}

	public function create_cloneable( Column $column ) {
		return false;
	}

}