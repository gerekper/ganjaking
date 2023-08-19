<?php

namespace ACA\MetaBox\Column\PostType;

use AC;

class Description extends AC\Column {

	public function __construct() {
		$this->set_type( 'column-mb-pt_description' )
		     ->set_label( __( 'Description', 'codepress-admin-columns' ) )
		     ->set_group( 'metabox_custom' );
	}

	public function get_raw_value( $id ) {
		$data = json_decode( get_post_field( 'post_content', $id ), true );

		return $data['description'] ?? $this->get_empty_char();
	}
}