<?php namespace Premmerce\WooCommercePinterest\Pinterest\Api;

use Premmerce\WooCommercePinterest\PinterestException;

class PinterestApiException extends PinterestException {

	public function __toString() {
		return self::class
			. ' in file '
			. $this->getFile()
			. ' on line '
			. $this->getLine()
			. PHP_EOL
			. 'Message: '
			. $this->getMessage()
			. PHP_EOL
			. 'Code: ' . $this->getCode()
			. PHP_EOL
			. 'Trace: '
			. $this->getTraceAsString()
			. PHP_EOL;
	}

}
