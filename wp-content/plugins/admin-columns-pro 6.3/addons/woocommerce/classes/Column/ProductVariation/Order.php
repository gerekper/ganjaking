<?php

namespace ACA\WC\Column\ProductVariation;

use ACP;

class Order extends ACP\Column\Post\Order {

	public function is_valid() {
		return true;
	}

}