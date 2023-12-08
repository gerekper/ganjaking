<?php

namespace ACP\Editing;

use AC\Helper\Select\Options\Paginated;

interface PaginatedOptionsFactory {

	public function create( string $search, int $page, int $id = null ): Paginated;

}