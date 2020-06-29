<?php

namespace ACP\Sorting\Model;

use ACP\Sorting\AbstractModel;

class Disabled extends AbstractModel {

	public function get_sorting_vars() {
		return [];
	}

}