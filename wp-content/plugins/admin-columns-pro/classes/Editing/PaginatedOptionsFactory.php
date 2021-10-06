<?php

namespace ACP\Editing;

use AC;

interface PaginatedOptionsFactory {

	/**
	 * @param string   $search
	 * @param int      $page
	 * @param int|null $id
	 *
	 * @return AC\Helper\Select\Options\Paginated
	 */
	public function create( $search, $page, $id = null );

}