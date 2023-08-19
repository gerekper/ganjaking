<?php

namespace ACA\MetaBox\Editing;

use AC\MetaType;
use ACA\MetaBox\Column;
use ACA\MetaBox\Editing\Storage\CustomTable;
use ACA\MetaBox\Editing\Storage\Field;
use ACA\MetaBox\Editing\Storage\TermField;
use ACA\MetaBox\StorageAware;
use ACP;

final class StorageFactory {

	public function create( Column $column, $single = true ): ACP\Editing\Storage {
		switch ( $column->get_storage() ) {
			case StorageAware::CUSTOM_TABLE:
				return $this->create_table_storage( $column );
			case StorageAware::META_BOX:
			default:
				return $this->create_field_storage( $column, $single );
		}
	}

	public function create_table_storage( Column $column ): ACP\Editing\Storage {
		return new CustomTable( $column->get_field_setting( 'storage' ), $column->get_storage_table(), $column->get_meta_key() );
	}

	private function create_field_storage( Column $column, $single ) {
		switch ( true ) {
			case get_class( $column ) === Column\Taxonomies::class:
			case get_class( $column ) === Column\Taxonomy::class:
				return new TermField( $column->get_meta_key(), new MetaType( $column->get_meta_type() ), $column->get_field_settings(), $single );
			default:
				return new Field( $column->get_meta_key(), new MetaType( $column->get_meta_type() ), $column->get_field_settings(), $single );
		}
	}

}