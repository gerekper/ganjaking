<?php

namespace WCML\Rest\Store;

use WCML\Rest\Functions;

class HooksFactory implements \IWPML_REST_Action_Loader {

	/**
	 * @return \IWPML_Action[]
	 */
	public function create() {
		global $woocommerce_wpml;

		$hooks = [];

		if ( Functions::isStoreAPIRequest() ) {
			$hooks[] = new Hooks();

			if ( wcml_is_multi_currency_on() ) {
				$hooks[] = new PriceRangeHooks( $woocommerce_wpml );
			}
		}

		return $hooks;
	}

}
