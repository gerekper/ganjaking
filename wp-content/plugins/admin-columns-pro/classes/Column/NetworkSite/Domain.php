<?php

namespace ACP\Column\NetworkSite;

class Domain extends Property {

	public function __construct() {
		$this->set_type( 'column-msite_domain' )
		     ->set_label( __( 'Domain', 'codepress-admin-columns' ) );
	}

	public function get_site_property() {
		return 'domain';
	}

}