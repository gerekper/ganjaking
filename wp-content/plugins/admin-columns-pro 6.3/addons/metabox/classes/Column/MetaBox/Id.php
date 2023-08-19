<?php

namespace ACA\MetaBox\Column\MetaBox;

use AC;

class Id extends AC\Column\Meta {

	public function __construct() {
		$this->set_type( 'column-mb-id' )
		     ->set_label( __( 'ID', 'codepress-admin-columns' ) )
		     ->set_group( 'metabox_custom' );
	}

	public function get_meta_key() {
		return 'meta_box';
	}

	public function get_raw_value( $id ) {
		$raw = parent::get_raw_value( $id );

		return $raw['id'] ?? false;
	}

}