<?php

namespace ACP\Editing;

use AC\Column;
use ACP\Editing\Model\Disabled;

class ServiceFactory {

	/**
	 * @param Column $column
	 *
	 * @return Service|false
	 */
	public static function create( Column $column ) {
		if ( ! $column instanceof Editable ) {
			return false;
		}

		$service = $column->editing();

		// Legacy
		if ( $service instanceof Disabled ) {
			return false;
		}

		return $service;
	}

}