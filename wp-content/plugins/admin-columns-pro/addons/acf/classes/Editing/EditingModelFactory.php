<?php

namespace ACA\ACF\Editing;

use ACA\ACF\Column;
use ACA\ACF\Field;
use ACP;

interface EditingModelFactory {

	/**
	 * @param Field  $field
	 * @param Column $column
	 *
	 * @return ACP\Editing\Service
	 */
	public function create( Field $field, Column $column );

}