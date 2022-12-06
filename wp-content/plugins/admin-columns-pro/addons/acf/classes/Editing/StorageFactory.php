<?php

namespace ACA\ACF\Editing;

use AC\MetaType;
use ACA\ACF\CloneColumnFactory;
use ACA\ACF\Column;
use ACA\ACF\Editing\Storage;
use ACA\ACF\GroupColumnFactory;

class StorageFactory {

	public function create( Column $column ) {

		if ( $this->is_group( $column->get_type() ) ) {
			return new Storage\Group(
				$this->get_group_key( $column->get_type() ),
				$this->get_sub_key( $column->get_type() ),
				$this->id_prefix( $column ),
				new Storage\Read\Column( $column )
			);
		}

		if ( $this->is_clone( $column->get_type() ) ) {
			return new Storage\CloneField(
				$this->get_clone_hash( $column->get_type() ),
				$this->get_clone_field_hash( $column->get_type() ),
				$this->id_prefix( $column ),
				new Storage\Read\Column( $column )
			);
		}

		return new Storage\Field(
			$column->get_type(),
			$this->id_prefix( $column ),
			new Storage\Read\Column( $column )
		);
	}

	private function get_group_key( $column_type ) {
		$column_type = str_replace( GroupColumnFactory::GROUP_PREFIX, '', $column_type );

		$parts = explode( '-', $column_type );

		return $parts[0];
	}

	private function get_sub_key( $column_type ) {
		$column_type = str_replace( GroupColumnFactory::GROUP_PREFIX, '', $column_type );

		$parts = explode( '-', $column_type );

		return $parts[1];
	}

	private function get_clone_hash( $column_type ) {
		$column_type = str_replace( CloneColumnFactory::CLONE_PREFIX, '', $column_type );

		$key_parts = explode( '_', $column_type );

		return sprintf( '%s_%s', $key_parts[0], $key_parts[1] );
	}

	private function get_clone_field_hash( $column_type ) {
		$column_type = str_replace( CloneColumnFactory::CLONE_PREFIX, '', $column_type );

		$key_parts = explode( '_', $column_type );

		return sprintf( '%s_%s', $key_parts[2], $key_parts[3] );
	}

	/**
	 * @param string $column_type
	 *
	 * @return bool
	 */
	private function is_clone( $column_type ) {
		return 0 === strpos( $column_type, CloneColumnFactory::CLONE_PREFIX );
	}

	/**
	 * @param string $column_type
	 *
	 * @return bool
	 */
	private function is_group( $column_type ) {
		return 0 === strpos( $column_type, GroupColumnFactory::GROUP_PREFIX );
	}

	private function id_prefix( Column $column ) {
		switch ( $column->get_meta_type() ) {
			case MetaType::USER:
				return 'user_';

			case MetaType::COMMENT:
				return 'comment_';

			case MetaType::SITE:
				return 'site_';

			case MetaType::TERM:
				return $column->get_taxonomy() . '_';

			default:
				return '';
		}
	}

}