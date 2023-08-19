<?php

namespace ACA\MetaBox\Column\MetaBox;

use AC;

class NumberOfFields extends AC\Column\Meta {

	public function __construct() {
		$this->set_type( 'column-mb-number_fields' )
		     ->set_label( __( 'Number of Fields', 'codepress-admin-columns' ) )
		     ->set_group( 'metabox_custom' );
	}

	public function get_meta_key() {
		return 'meta_box';
	}

	public function get_raw_value( $id ) {
		$raw = parent::get_raw_value( $id );

		return $raw && isset( $raw['fields'] )
			? count( $raw['fields'] )
			: false;
	}

}