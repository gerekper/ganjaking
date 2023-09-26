<?php

namespace ACA\ACF\Search;

use ACP;

interface SearchFactoryAware extends ACP\Search\Searchable {

	public function set_search_comparison_factory( SearchComparisonFactory $factory );

}