<?php

namespace ACA\ACF\Filtering;

use ACA\ACF\Column;
use ACA\ACF\Field;
use ACP\Filtering\Model;

interface FilteringModelFactory {

	/**
	 * @param Field  $field
	 * @param Column $column
	 *
	 * @return Model
	 */
	public function create( Field $field, Column $column );

}