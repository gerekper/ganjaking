<?php

namespace ACA\Pods\Sorting;

use ACP;

trait DefaultSortingTrait {

	public function sorting() {
		return ( new ACP\Sorting\Model\MetaFactory() )->create( $this->get_meta_type(), $this->get_meta_key() );
	}

}