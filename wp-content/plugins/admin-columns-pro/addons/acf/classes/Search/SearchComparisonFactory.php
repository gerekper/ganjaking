<?php

namespace ACA\ACF\Search;

use ACA\ACF\Column;
use ACA\ACF\Field;

interface SearchComparisonFactory {

	public function create( Field $field, string $meta_key, string $meta_type, Column $column );

}