<?php

namespace ACA\MetaBox\Sorting;

use ACA\MetaBox;
use ACA\MetaBox\Column;
use ACP;

abstract class Factory {

	/**
	 * @return ACP\Sorting\AbstractModel
	 */
	public function create( Column $column ) {
		if ( $column->is_clonable() ) {
			return new ACP\Sorting\Model\Disabled;
		}

		if ( $column->get_storage() === MetaBox\StorageAware::CUSTOM_TABLE ) {
			return $this instanceof TableStorageFactory
				? $this->create_table_storage( $column )
				: new ACP\Sorting\Model\Disabled();
		}

		return $this->create_default( $column );
	}

	/**
	 * @return ACP\Sorting\AbstractModel
	 */
	abstract protected function create_default( Column $column );

}