<?php

namespace ACP\Sorting\Table\Filter;

use AC\Column;
use AC\ColumnRepository;
use ACP\Sorting;

class DisabledOriginalColumns implements ColumnRepository\Filter {

	public function filter( $columns ) {
		return array_filter( $columns, [ $this, 'is_disabled' ] );
	}

	private function is_disabled( Column $column ) {
		if ( ! $column->is_original() ) {
			return false;
		}

		$setting = $column->get_setting( 'sort' );

		return $setting instanceof Sorting\Settings && ! $setting->is_active();
	}
}