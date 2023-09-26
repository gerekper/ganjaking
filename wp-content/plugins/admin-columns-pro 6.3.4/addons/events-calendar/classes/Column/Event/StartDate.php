<?php

namespace ACA\EC\Column\Event;

use AC;
use ACA\EC\Editing;
use ACA\EC\Filtering;
use ACP;
use ACP\Search;

class StartDate extends AC\Column\Meta
	implements ACP\Filtering\Filterable, ACP\Editing\Editable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'start-date' )
		     ->set_original( true );
	}

	public function get_meta_key() {
		return '_EventStartDate';
	}

	public function get_value( $id ) {
		return '';
	}

	public function filtering() {
		return new Filtering\Event\Date( $this );
	}

	public function editing() {
		return new ACP\Editing\Service\Basic(
			new ACP\Editing\View\DateTime(),
			new Editing\Storage\Event\StartDate()
		);
	}

	public function search() {
		return new Search\Comparison\Meta\DateTime\ISO( $this->get_meta_key(), $this->get_meta_type() );
	}

}