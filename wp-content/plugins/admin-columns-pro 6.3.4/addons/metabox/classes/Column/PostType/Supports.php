<?php

namespace ACA\MetaBox\Column\PostType;

use AC;

class Supports extends AC\Column {

	public function __construct() {
		$this->set_type( 'column-mb-pt_supports' )
		     ->set_label( _x( 'Supports', 'post_type', 'codepress-admin-columns' ) )
		     ->set_group( 'metabox_custom' );
	}

	public function get_raw_value( $id ) {
		$data = json_decode( get_post_field( 'post_content', $id ), true );

		return implode( ', ', $data['supports'] );
	}
}