<?php

namespace ACP\Search\Comparison;

use AC;

interface Values {

	/**
	 * @return AC\Helper\Select\Options
	 */
	public function get_values();

}