<?php

namespace ACP\Column\NetworkSite;

use AC;
use ACP\Settings;

class PostCount extends AC\Column {

	public function __construct() {
		$this->set_type( 'column-msite_postcount' );
		$this->set_label( __( 'Post Count', 'codepress-admin-columns' ) );
	}

	public function get_raw_value( $blog_id ) {
		return $blog_id;
	}

	public function register_settings() {
		$this->add_setting( new Settings\Column\NetworkSite\PostCount( $this ) );
	}

}