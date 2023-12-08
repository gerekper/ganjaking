<?php

namespace ACA\JetEngine\Search;

use ACA\JetEngine\Search;
use ACP;

trait SearchableTrait {

	/**
	 * @return ACP\Search\Comparison|false
	 */
	public function search() {
		return ( new Search\ComparisonFactory() )->create( $this->field, $this->get_meta_type(), $this );
	}

}