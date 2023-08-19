<?php

namespace ACA\MetaBox\Column;

use ACA\MetaBox\Column;
use ACA\MetaBox\Editing;
use ACA\MetaBox\Search;
use ACA\MetaBox\Sorting;
use ACP;

class Select extends Column implements ACP\Search\Searchable, ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function get_raw_value( $id ) {
		return get_metadata( $this->get_meta_type(), $id, $this->get_meta_key(), ! $this->is_multiple() );
	}

	public function format_single_value( $value, $id = null ) {
		if ( ! $value ) {
			return $this->get_empty_char();
		}

		if ( $this->is_multiple() ) {
			$value = array_map( [ $this, 'get_label_for_option' ], array_filter( $value ) );

			return implode( ', ', (array) $value );
		}

		return $this->get_label_for_option( $value );
	}

	protected function get_label_for_option( $key ) {
		$options = $this->get_field_options();

		return isset( $options[ $key ] ) && is_scalar( $options[ $key ] )
			? $options[ $key ]
			: $key;
	}

	public function get_field_options() {
		return (array) $this->get_field_setting( 'options' );
	}

	public function editing() {
		if ( $this->is_clonable() ) {
			return false;
		}

		return new ACP\Editing\Service\Basic(
			( new ACP\Editing\View\AdvancedSelect( $this->get_field_options() ) )->set_clear_button( true )->set_multiple( $this->is_multiple() ),
			( new Editing\StorageFactory() )->create( $this )
		);
	}

	public function search() {
		return ( new Search\Factory\Select )->create( $this );
	}

	public function sorting() {
		return ( new Sorting\Factory\Select )->create( $this );
	}

}