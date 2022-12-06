<?php

namespace ACA\EC\Column\Venue;

use ACP\ConditionalFormat;
use ACA\EC\Column\Meta;
use ACP\Search\Comparison\Meta\Text;
use ACP\Search\Searchable;

class City extends Meta
	implements Searchable, ConditionalFormat\Formattable {

	use ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-ec-venue_city' )
		     ->set_label( __( 'City', 'codepress-admin-columns' ) );

		parent::__construct();
	}

	public function get_meta_key() {
		return '_VenueCity';
	}

	public function search() {
		return new Text( $this->get_meta_key(), $this->get_meta_type() );
	}

}