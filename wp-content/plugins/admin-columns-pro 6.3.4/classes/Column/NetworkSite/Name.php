<?php

namespace ACP\Column\NetworkSite;

class Name extends Option {

	public function __construct() {
		$this->set_type( 'column-msite_name' )
		     ->set_label( __( 'Name', 'codepress-admin-columns' ) );
	}

	public function get_option_name() {
		return 'blogname';
	}

}