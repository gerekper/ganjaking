<?php

namespace ACP\Helper\Select;

use AC;

/**
 * @deprecated 4.7
 */
interface Value extends AC\Helper\Select\Value {

	/**
	 * @param $entity
	 *
	 * @return string
	 */
	public function get_value( $entity );

}