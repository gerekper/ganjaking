<?php

namespace ACA\Types\Field\Repeatable;

use AC\MetaType;
use ACA\Types\Editing\Storage;
use ACA\Types\Field;
use ACA\Types\Sorting\DisabledSortingTrait;
use ACP;

class Textarea extends Field\Textarea {

	use DisabledSortingTrait;

	public function get_value( $id ) {
		$values = [];

		foreach ( (array) $this->get_raw_value( $id ) as $string ) {
			$values[] = $this->column->get_formatted_value( $string );
		}

		return ac_helper()->html->small_block( $values );
	}

	public function editing() {
		return new ACP\Editing\Service\Basic(
			( new ACP\Editing\View\MultiInput() )->set_clear_button( true )->set_sub_type( 'textarea' ),
			new Storage\Repeater( $this->get_meta_key(), new MetaType( $this->get_meta_type() ) )
		);
	}

}