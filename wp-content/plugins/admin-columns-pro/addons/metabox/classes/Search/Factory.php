<?php

namespace ACA\MetaBox\Search;

use ACA\MetaBox;
use ACA\MetaBox\Column;
use ACP\Search\Comparison;

abstract class Factory extends MetaBox\Factory {

	public function create( Column $column ) {
		switch ( true ) {
			case $this instanceof CloneableFactory && $column->is_clonable():
				$model = $this->create_cloneable( $column );

				break;
			default:
				$model = $this->create_default( $column );
		}

		if ( $column->get_storage() === MetaBox\StorageAware::CUSTOM_TABLE ) {
			if ( $model instanceof Comparison && $this instanceof TableStorageFactory ) {
				$model = $this->create_table_storage( $column, $model );
			} else {
				$model = $this->create_disabled( $column );
			}
		}

		return $model;

	}

	public function create_disabled( Column $column ) {
		return false;
	}

}