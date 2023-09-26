<?php

namespace ACA\EC\Column\Event;

use ACA\EC\Column\Meta;
use ACA\EC\Editing;
use ACA\EC\Filtering;
use ACP\Search\Comparison\Meta\Checkmark;
use ACP\Search\Searchable;

class HideFromUpcoming extends Meta
	implements Searchable {

	public function __construct() {
		$this->set_type( 'column-ec-event_hide_from_upcoming' )
		     ->set_label( __( 'Hide from Event Listing', 'codepress-admin-columns' ) );

		parent::__construct();
	}

	public function get_meta_key() {
		return '_EventHideFromUpcoming';
	}

	public function get_value( $id ) {
		$value = $this->get_raw_value( $id );

		return ac_helper()->icon->yes_or_no( 'yes' === $value );
	}

	public function editing() {
		return new Editing\Service\Event\HideFromUpcoming();
	}

	public function filtering() {
		return new Filtering\Event\HiddenFromUpcoming( $this );
	}

	public function search() {
		return new Checkmark( $this->get_meta_key(), $this->get_meta_type() );
	}

}