<?php

namespace ACA\EC\Export\Model\Event;

use ACP;

/**
 * Export Model for AllDayEvent column
 * @since 1.0.2
 */
class AllDayEvent extends ACP\Export\Model {

	public function get_value( $id ) {
		$value = $this->column->get_raw_value( $id );

		return $value ? 1 : '';
	}

}