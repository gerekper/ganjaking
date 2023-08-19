<?php

namespace ACA\MetaBox\Column\MetaBox;

use AC;

class CustomTable extends AC\Column\Meta {

	public function __construct() {
		$this->set_type( 'column-mb-custom_table' )
		     ->set_label( __( 'Custom Table', 'codepress-admin-columns' ) )
		     ->set_group( 'metabox_custom' );
	}

	public function get_meta_key() {
		return 'settings';
	}

	public function get_value( $id ) {
		$raw_value = $this->get_raw_value( $id );

		return isset( $raw_value['enable'] ) && $raw_value['enable']
			? ac_helper()->icon->yes() . ' ' . $raw_value['name']
			: $this->get_empty_char();
	}

	public function get_raw_value( $id ) {
		$raw = parent::get_raw_value( $id );

		return $raw['custom_table'] ?? false;
	}

}