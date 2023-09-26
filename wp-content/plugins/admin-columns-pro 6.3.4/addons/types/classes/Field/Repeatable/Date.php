<?php

namespace ACA\Types\Field\Repeatable;

use AC\MetaType;
use ACA\Types\Editing\Storage;
use ACA\Types\Field;
use ACA\Types\Sorting\DisabledSortingTrait;
use ACP;

class Date extends Field\Date {

	use DisabledSortingTrait;

	public function get_value( $id ) {
		$raw_value = array_filter( (array) $this->get_raw_value( $id ) );

		return ! empty( $raw_value )
			? ac_helper()->html->small_block( array_map( [ $this, 'format_single_value' ], $raw_value ) )
			: false;
	}

	private function format_single_value( $value ) {
		return $this->column->get_formatted_value( date( 'c', $value ) );
	}

	public function editing() {
		return new ACP\Editing\Service\Basic(
			( new ACP\Editing\View\MultiInput() )->set_clear_button( true )->set_sub_type( 'date' ),
			new Storage\RepeatableDate( $this->get_meta_key(), new MetaType( $this->get_meta_type() ) )
		);
	}

}