<?php

namespace ACP\Column\NetworkSite;

use ACP\ConditionalFormat;

class SiteID extends Property implements ConditionalFormat\Formattable {

	use ConditionalFormat\IntegerFormattableTrait;

	public function __construct() {
		$this->set_type( 'column-msite_id' );
		$this->set_label( __( 'Site ID', 'codepress-admin-columns' ) );
	}

	public function get_site_property() {
		return 'site_id';
	}

}