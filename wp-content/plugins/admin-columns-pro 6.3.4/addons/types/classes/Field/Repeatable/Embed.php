<?php

namespace ACA\Types\Field\Repeatable;

use AC\MetaType;
use ACA\Types\Editing\Storage;
use ACA\Types\Field;
use ACA\Types\Sorting\DisabledSortingTrait;
use ACP;

class Embed extends Field\Embed {

	use DisabledSortingTrait;

	public function get_value( $id ) {
		$values = [];

		foreach ( (array) $this->get_raw_value( $id ) as $url ) {
			$values[] = $this->column->get_formatted_value( $url );
		}

		return ac_helper()->html->small_block( $values );
	}

	public function editing() {
		return new ACP\Editing\Service\Basic(
			( new ACP\Editing\View\MultiInput() )->set_clear_button( true )->set_sub_type( 'url' ),
			new Storage\Repeater( $this->get_meta_key(), new MetaType( $this->get_meta_type() ) )
		);
	}

}