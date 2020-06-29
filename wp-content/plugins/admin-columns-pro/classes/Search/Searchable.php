<?php

namespace ACP\Search;

interface Searchable {

	/**
	 * Return the search comparison for this column
	 * @return Comparison|false
	 */
	public function search();

}