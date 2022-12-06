<?php

namespace ACA\ACF\Sorting;

use ACP;

interface SortingFactoryAware extends ACP\Sorting\Sortable {

	public function set_sorting_model_factory( SortingModelFactory $factory );

}