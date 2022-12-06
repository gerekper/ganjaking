<?php

namespace ACA\MetaBox\Column;

use AC\Settings\Column\NumberOfItems;
use ACA\MetaBox\Column;
use ACP\ConditionalFormat\ConditionalFormatTrait;
use ACP\ConditionalFormat\Formattable;

class TextList extends Column implements Formattable {

	use ConditionalFormatTrait;

	public function format_single_value( $value, $id = null ) {
		if ( ! $value ) {
			return $this->get_empty_char();
		}

		$setting_limit = $this->get_setting( 'number_of_items' );

		return ac_helper()->html->more( $value, $setting_limit ? $setting_limit->get_value() : false );
	}

	protected function register_settings() {
		$this->add_setting( new NumberOfItems( $this ) );
	}

}