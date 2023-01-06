<?php

namespace WCML\Compatibility\Aurum;

class MulticurrencyHooks implements \IWPML_Action {

	public function add_hooks() {
		add_filter( 'wcml_multi_currency_ajax_actions', [ $this, 'add_ajax_action' ] );
	}

	/**
	 * @param array $actions
	 *
	 * @return array
	 */
	public function add_ajax_action( $actions ) {
		$actions[] = 'lab_wc_add_to_cart';

		return $actions;
	}
}
