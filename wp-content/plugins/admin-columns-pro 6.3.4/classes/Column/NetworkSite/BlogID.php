<?php

namespace ACP\Column\NetworkSite;

use ACP\ConditionalFormat;

class BlogID extends Property implements ConditionalFormat\Formattable {

	use ConditionalFormat\IntegerFormattableTrait;

	public function __construct() {
		$this->set_type( 'column-blog_id' )
		     ->set_label( __( 'Blog ID', 'codepress-admin-columns' ) );
	}

	public function get_site_property() {
		return 'blog_id';
	}

}