<?php

namespace ACA\MetaBox\Column\MetaBox;

use AC;

class Fields extends AC\Column\Meta {

	public function __construct() {
		$this->set_type( 'column-mb-fields' )
		     ->set_label( __( 'Field', 'codepress-admin-columns' ) )
		     ->set_group( 'metabox_custom' );
	}

	public function get_meta_key() {
		return 'fields';
	}

	public function get_value( $id ) {
		$raw = $this->get_raw_value( $id );

		if ( empty( $raw ) ) {
			return $this->get_empty_char();
		}

		$fields = [];
		foreach ( $raw as $field ) {
			$fields[] = sprintf( '%s <small style="color: #999">[%s]</small>', $field['name'], $field['type'] );
		}

		return implode( '<br>', $fields );
	}

}