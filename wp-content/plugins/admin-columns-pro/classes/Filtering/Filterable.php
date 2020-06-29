<?php

namespace ACP\Filtering;

interface Filterable {

	/**
	 * Return the filtering model for this column
	 * @return Model
	 */
	public function filtering();

}