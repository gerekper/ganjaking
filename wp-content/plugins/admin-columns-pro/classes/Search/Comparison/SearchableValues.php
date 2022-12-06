<?php

namespace ACP\Search\Comparison;

use ACP\Helper\Select\Options;

interface SearchableValues {

	/**
	 * @param string $search
	 * @param int    $page
	 *
	 * @return Options\Paginated
	 */
	public function get_values( $search, $page );

}