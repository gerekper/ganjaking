<?php

namespace ACP\Column\NetworkSite;

use ACP\ConditionalFormat;

class Domain extends Property implements ConditionalFormat\Formattable {

	use ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-msite_domain' )
		     ->set_label( __( 'Domain', 'codepress-admin-columns' ) );
	}

	public function get_site_property() {
		return 'domain';
	}

}