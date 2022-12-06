<?php

namespace ACA\WC\Sorting\User\OrderDate;

use ACA\WC\Sorting\User\OrderDate;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;

class FirstOrder extends OrderDate {

	protected function get_order_by(): string {
		return SqlOrderByFactory::create_with_computation( new ComputationType( ComputationType::MIN ), 'acsort_order_postmeta.meta_value', $this->get_order() );
	}

}