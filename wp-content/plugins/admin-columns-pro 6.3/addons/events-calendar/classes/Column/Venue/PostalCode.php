<?php

namespace ACA\EC\Column\Venue;

use ACA\EC\Column;
use ACP\ConditionalFormat;
use ACP\Search;
use ACP\Search\Searchable;

class PostalCode extends Column\Meta
	implements Searchable, ConditionalFormat\Formattable {

	use ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-ec-venue_postal_code' )
		     ->set_label( __( 'Postal Code', 'codepress-admin-columns' ) );

		parent::__construct();
	}

	public function get_meta_key() {
		return '_VenueZip';
	}

	public function search() {
		return new Search\Comparison\Meta\Text( $this->get_meta_key(), $this->get_meta_type() );
	}

}