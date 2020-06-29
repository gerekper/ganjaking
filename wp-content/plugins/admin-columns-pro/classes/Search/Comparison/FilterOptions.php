<?php

namespace ACP\Search\Comparison;

use AC;

interface FilterOptions {

	/**
	 * @return AC\Helper\Select\Options
	 */
	public function get_options();

}