<?php

namespace ACP\Column\NetworkSite;

class Path extends Property {

	public function __construct() {
		$this->set_type( 'column-msite_path' );
		$this->set_label( __( 'Path', 'codepress-admin-columns' ) );
	}

	public function get_site_property() {
		return 'path';
	}

}