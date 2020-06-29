<?php

namespace ACP\Column\NetworkSite;

class BlogID extends Property {

	public function __construct() {
		$this->set_type( 'column-blog_id' )
		     ->set_label( __( 'Blog ID', 'codepress-admin-columns' ) );
	}

	public function get_site_property() {
		return 'blog_id';
	}

}