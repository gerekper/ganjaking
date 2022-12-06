<?php

namespace ACA\JetEngine\Sorting;

use ACA\JetEngine\Sorting;
use ACP;

trait SortableTrait {

	/**
	 * @return ACP\Sorting\AbstractModel
	 */
	public function sorting() {
		return ( new Sorting\ModelFactory() )->create( $this->field, $this->get_meta_type() );
	}

}