<?php

namespace ACP\Sorting;

use AC\Column;
use ACP\Sorting\Model\Disabled;

class ModelFactory {

	/**
	 * @param Column $column
	 *
	 * @return AbstractModel|null
	 */
	public function create( Column $column ) {
		if ( ! $column instanceof Sortable ) {
			return null;
		}

		$model = apply_filters( 'acp/sorting/model', $column->sorting(), $column );

		if ( $model instanceof Disabled ) {
			return null;
		}

		return $model;
	}

}