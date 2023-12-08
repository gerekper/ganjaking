<?php

namespace ACP\Editing;

use AC\Helper\Select\Options\Paginated;

interface PaginatedOptions
{

    public function get_paginated_options(string $search, int $page, int $id = null): Paginated;

}