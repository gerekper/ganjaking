<?php

namespace ACA\MetaBox\Column\MetaBox;

use AC;

class Position extends AC\Column\Meta {

	public function __construct() {
		$this->set_type( 'column-mb-position' )
		     ->set_label( __( 'Position', 'meta-box-builder' ) )
		     ->set_group( 'metabox_custom' );
	}

	public function get_meta_key() {
		return 'settings';
	}

	public function get_value( $id ) {
		$raw = $this->get_raw_value( $id );

		switch ( $raw ) {
			case 'side':
				return __( 'Side', 'meta-box-builder' );
			default:
				return __( 'After content', 'meta-box-builder' );
		}
	}

	public function get_raw_value( $id ) {
		$raw = parent::get_raw_value( $id );

		return $raw['context'] ?? false;
	}

}