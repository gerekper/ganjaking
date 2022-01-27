<?php


namespace Premmerce\WooCommercePinterest\Model;

use Premmerce\WooCommercePinterest\PinterestException;

class PinterestModelException extends PinterestException {

	public function __toString() {
		return $this->getMessage();
	}
}
