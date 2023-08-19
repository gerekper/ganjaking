<?php

namespace ACA\ACF\Value\Formatter;

use AC\Settings\Column\NumberOfItems;
use AC\Settings\Column\Separator;
use ACA\ACF\Field\Choices;
use ACA\ACF\Value\Formatter;

class Select extends Formatter {

	public function format( $value, $id = null ) {
		$labels = $this->field instanceof Choices
			? $this->field->get_choices()
			: [];

		$result = [];
		foreach ( (array) $value as $v ) {
			$result[] = $labels[ $v ] ?? $v;
		}

		if ( empty( $result ) ) {
			return $this->column->get_empty_char();
		}

		$separator = $this->column->get_separator();

		if ( $this->column->get_setting( Separator::NAME ) ) {
			$separator = $this->column->get_setting( Separator::NAME )->get_separator_formatted();
		}

		if ( $this->column->get_setting( NumberOfItems::NAME ) ) {
			return ac_helper()->html->more(
				$result,
				$this->column->get_setting( NumberOfItems::NAME )->get_value(),
				$separator
			);
		}

		return implode( $separator, $result );
	}

}