<?php

namespace ACA\Types\Field\Repeatable;

use AC\MetaType;
use ACA\Types\Editing\Storage;
use ACA\Types\Field;
use ACA\Types\Sorting\DisabledSortingTrait;
use ACP;

class Phone extends Field\Phone {

	use DisabledSortingTrait;

	public function editing() {
		return new ACP\Editing\Service\Basic(
			( new ACP\Editing\View\MultiInput() )->set_clear_button( true ),
			new Storage\Repeater( $this->get_meta_key(), new MetaType( $this->get_meta_type() ) )
		);
	}

}