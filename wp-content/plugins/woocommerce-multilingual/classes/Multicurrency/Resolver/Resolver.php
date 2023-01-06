<?php

namespace WCML\MultiCurrency\Resolver;

interface Resolver {

	/**
	 * @return string|null
	 */
	public function getClientCurrency();
}
