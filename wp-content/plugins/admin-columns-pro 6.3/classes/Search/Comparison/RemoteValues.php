<?php

namespace ACP\Search\Comparison;

use AC;

interface RemoteValues {

	/**
	 * @return AC\Helper\Select\Options
	 */
	public function get_values();

}