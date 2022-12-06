<?php

namespace ACA\ACF\Search;

use ACA\ACF\Field;

interface SearchComparisonFactory {

	public function create( Field $field, $meta_key, $meta_type );

}