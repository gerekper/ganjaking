<?php

namespace ACP\Editing;

use AC;

interface PaginatedOptions {

	/**
	 * @param string $search
	 * @param int    $page
	 * @param int    null
	 *
	 * @return AC\Helper\Select\Options\Paginated
	 */
	public function get_paginated_options( $search, $page, $id = null );

}