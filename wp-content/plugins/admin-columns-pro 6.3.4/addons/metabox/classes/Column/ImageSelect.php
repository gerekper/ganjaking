<?php

namespace ACA\MetaBox\Column;

use ACA\MetaBox\Editing;
use ACP\Editing\Service\Basic;
use ACP\Editing\View;

class ImageSelect extends Select {

	public function format_single_value( $value, $id = null ) {
		if ( ! $value ) {
			return $this->get_empty_char();
		}

		if ( $this->is_multiple() ) {
			return implode( ', ', (array) $value );
		}

		return $value;
	}

	public function editing() {
		if ( $this->is_clonable() ) {
			return false;
		}

		return new Basic(
			( new View\AdvancedSelect( $this->get_field_options() ) )->set_multiple( $this->get_field_setting( 'multiple' ) ),
			( new Editing\StorageFactory() )->create( $this )
		);
	}

	public function get_field_options() {
		$options = $this->get_field_setting( 'options' );

		if ( empty( $options ) || ! is_array( $options ) ) {
			return [];
		}

		$option_keys = array_keys( $options );

		return array_combine( $option_keys, $option_keys );
	}

}