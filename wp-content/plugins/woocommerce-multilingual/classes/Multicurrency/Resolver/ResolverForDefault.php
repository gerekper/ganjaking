<?php

namespace WCML\MultiCurrency\Resolver;

class ResolverForDefault implements Resolver {

	/**
	 * We consider that we should always be able to resolve a currency
	 * for any customer.
	 *
	 * If no currency could be found in the previous steps, we'll
	 * fall back to the default WC currency.
	 *
	 * @inheritDoc
	 */
	public function getClientCurrency() {
		return wcml_get_woocommerce_currency_option();
	}
}
