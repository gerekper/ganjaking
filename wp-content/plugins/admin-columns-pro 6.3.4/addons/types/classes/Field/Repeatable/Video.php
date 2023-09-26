<?php

namespace ACA\Types\Field\Repeatable;

use AC\MetaType;
use ACA\Types\Editing\Storage;
use ACA\Types\Field;
use ACA\Types\Sorting\DisabledSortingTrait;
use ACP;

class Video extends Field\File {

	use DisabledSortingTrait;

	public function editing() {
		return new ACP\Editing\Service\Basic(
			( new ACP\Editing\View\Video() )->set_multiple( true )->set_clear_button( true ),
			new Storage\RepeatableFile( $this->get_meta_key(), new MetaType( $this->get_meta_type() ) )
		);
	}

}