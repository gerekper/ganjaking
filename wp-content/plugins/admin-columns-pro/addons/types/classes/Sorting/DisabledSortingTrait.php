<?php

namespace ACA\Types\Sorting;

use ACP;

trait DisabledSortingTrait {

	public function sorting() {
		return new ACP\Sorting\Model\Disabled;
	}

}