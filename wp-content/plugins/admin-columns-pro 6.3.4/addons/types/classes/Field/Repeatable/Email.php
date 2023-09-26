<?php

namespace ACA\Types\Field\Repeatable;

use AC\MetaType;
use ACA\Types\Editing\Storage;
use ACA\Types\Field;
use ACA\Types\Sorting\DisabledSortingTrait;
use ACP;

class Email extends Field\Email {

	use DisabledSortingTrait;

	public function get_value( $id ) {
		return $this->get_repeatable_value( $id );
	}

	public function editing() {
		return new ACP\Editing\Service\Basic(
			( new ACP\Editing\View\MultiInput() )->set_clear_button( true )->set_sub_type( 'email' ),
			new Storage\Repeater( $this->get_meta_key(), new MetaType( $this->get_meta_type() ) )
		);
	}

}