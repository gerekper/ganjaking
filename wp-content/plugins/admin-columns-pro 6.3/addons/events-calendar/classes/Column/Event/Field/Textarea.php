<?php

namespace ACA\EC\Column\Event\Field;

use ACA\EC\Column\Event;
use ACP\Editing;

/**
 * @since 1.1.2
 */
class Textarea extends Event\Field {

	public function editing() {
		return new Editing\Service\Basic(
			( new Editing\View\TextArea() )->set_clear_button( true ),
			new Editing\Storage\Post\Meta( $this->get_meta_key() )
		);
	}

}