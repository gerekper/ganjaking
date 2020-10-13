<?php

namespace WPML\Ajax;

use WPML\Collect\Support\Collection;
use WPML\FP\Either;

interface IHandler {
	/**
	 * @param Collection $data
	 *
	 * @return Either
	 */
	public function run( Collection $data );
}
