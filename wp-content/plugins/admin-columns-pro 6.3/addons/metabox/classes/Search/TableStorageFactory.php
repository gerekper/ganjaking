<?php

namespace ACA\MetaBox\Search;

use ACA\MetaBox\Column;
use ACP\Search\Comparison;

interface TableStorageFactory {

	public function create_table_storage( Column $column, Comparison $default );

}