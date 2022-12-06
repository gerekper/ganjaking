<?php

namespace ACA\ACF\Filtering;

use ACP;

interface FilteringFactoryAware extends ACP\Filtering\Filterable {

	public function set_filtering_model_factory( FilteringModelFactory $factory );

}