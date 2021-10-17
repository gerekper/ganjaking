<?php

namespace ACP\Search\Comparison;

use ACP\Helper\Select\Options;

interface SearchableValues {

	/**
	 * @param $search
	 * @param $page
	 *
	 * @return Options\Paginated
	 */
	public function get_values( $search, $page );

}