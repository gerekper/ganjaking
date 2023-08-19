<?php

namespace ACA\Types\Field\Repeatable;

use AC\MetaType;
use ACA\Types\Editing\Storage;
use ACA\Types\Field;
use ACA\Types\Sorting\DisabledSortingTrait;
use ACP;

class Colorpicker extends Field\Colorpicker {

	use DisabledSortingTrait;

	public function get_value( $id ) {
		$values = [];

		foreach ( (array) $this->get_raw_value( $id ) as $color ) {
			$values[] = ac_helper()->string->get_color_block( $color );
		}

		return implode( $values );
	}

	public function editing() {
		return new ACP\Editing\Service\Basic(
			( new ACP\Editing\View\MultiInput() )->set_clear_button( true )->set_sub_type( 'color' ),
			new Storage\Repeater( $this->get_meta_key(), new MetaType( $this->get_meta_type() ) )
		);
	}

}