<?php

namespace ACA\BP\Field\Profile;

use AC;
use ACA\BP\Editing;
use ACA\BP\Field\Profile;
use ACA\BP\Search;
use ACP;
use ACP\Editing\Service\Basic;

class Textarea extends Profile {

	public function get_dependent_settings() {
		return [ new AC\Settings\Column\WordLimit( $this->column ) ];
	}

	public function editing() {
		return new Basic(
			( new ACP\Editing\View\TextArea() )->set_clear_button( true ),
			new Editing\Storage\Profile( $this->column->get_buddypress_field_id() )
		);
	}

	public function search() {
		return new Search\Profile\Text( $this->column->get_buddypress_field_id() );
	}

}