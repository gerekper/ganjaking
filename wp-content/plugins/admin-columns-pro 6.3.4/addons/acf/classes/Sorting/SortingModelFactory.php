<?php

namespace ACA\ACF\Sorting;

use ACA\ACF\Column;
use ACA\ACF\Field;
use ACP\Sorting\AbstractModel;

interface SortingModelFactory {

	/**
	 * @param Field  $field
	 * @param string $meta_key
	 * @param Column $column
	 *
	 * @return AbstractModel
	 */
	public function create( Field $field, $meta_key, Column $column );

}