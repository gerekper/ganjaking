<?php

namespace ACA\Types\Field;

use AC\MetaType;
use ACA\Types\Editing\Storage;
use ACP\Editing;

class Audio extends File {

	public function editing() {
		return new Editing\Service\Basic(
			( new Editing\View\Audio() )->set_clear_button( true ),
			new Storage\File( $this->get_meta_key(), new MetaType( $this->get_meta_type() ) )
		);
	}

}