<?php

namespace WCML\Multicurrency\UI;

use WCML\StandAlone\IStandAloneAction;

use WCML\Utilities\AdminPages;
use function WPML\Container\make;

class Factory implements \IWPML_Backend_Action_Loader, \IWPML_Deferred_Action_Loader, IStandAloneAction {

	public function get_load_action() {
		return 'init';
	}

	/**
	 * @return \IWPML_Action|null
	 */
	public function create() {
		/** @var \woocommerce_wpml $woocommerce_wpml */
		global $woocommerce_wpml;

		if ( AdminPages::isMultiCurrency() && make( \WCML_Dependencies::class )->check() ) {
			return make(
				Hooks::class,
				[
					':wcmlSettings' => $woocommerce_wpml->settings,
				]
			);
		}

		return null;
	}
}
