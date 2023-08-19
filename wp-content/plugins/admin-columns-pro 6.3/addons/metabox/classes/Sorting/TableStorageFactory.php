<?php

namespace ACA\MetaBox\Sorting;

use ACA\MetaBox\Column;

interface TableStorageFactory {

	public function create_table_storage( Column $column );

}