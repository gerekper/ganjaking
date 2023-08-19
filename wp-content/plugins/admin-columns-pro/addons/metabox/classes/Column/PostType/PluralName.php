<?php

namespace ACA\MetaBox\Column\PostType;

use AC;

class PluralName extends AC\Column {

	public function __construct() {
		$this->set_type( 'column-mb-pt_plural_name' )
		     ->set_label( __( 'Label', 'codepress-admin-columns' ) )
		     ->set_group( 'metabox_custom' );
	}

	public function get_raw_value( $id ) {
		$data = json_decode( get_post_field( 'post_content', $id ), true );

		return $data['label'] ?? $this->get_empty_char();
	}
}