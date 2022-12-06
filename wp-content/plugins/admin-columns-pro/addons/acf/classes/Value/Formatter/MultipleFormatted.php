<?php

namespace ACA\ACF\Value\Formatter;

use AC\Settings\Column\NumberOfItems;
use AC\Settings\Column\Separator;
use ACA\ACF\Value\Formatter;

class MultipleFormatted extends Formatter {

	public function format( $value, $id = null ) {
		if ( ! is_array( $value ) || empty( $value ) ) {
			return $this->column->get_empty_char();
		}

		$items = [];

		foreach ( $value as $item_id ) {
			$items[] = $this->column->get_formatted_value( $item_id );
		}

		$items = array_filter( $items, [ $this, 'is_not_empty' ] );

		$values = ac_helper()->html->more(
			$items,
			$this->get_limit(),
			$this->get_separator()
		);

		return $values ?: $this->column->get_empty_char();
	}

	/**
	 * @param mixed $value
	 *
	 * @return bool
	 */
	private function is_not_empty( $value ) {
		return ! in_array( $value, [ null, false, '' ], true );
	}

	/**
	 * @return string
	 */
	private function get_separator() {
		$setting = $this->column->get_setting( Separator::NAME );

		return $setting instanceof Separator
			? $setting->get_separator_formatted()
			: $this->column->get_separator();
	}

	/**
	 * @return int
	 */
	private function get_limit() {
		$setting_limit = $this->column->get_setting( NumberOfItems::NAME );

		return $setting_limit instanceof NumberOfItems
			? $setting_limit->get_number_of_items()
			: 0;
	}

}