<?php

namespace WCML\Compatibility\YithWcQuickView;

class MulticurrencyHooks implements \IWPML_Action {

	public function add_hooks() {
		add_filter( 'wcml_multi_currency_ajax_actions', [ $this, 'ajax_action_needs_multi_currency' ] );
	}

	/**
	 * @param array $actions
	 *
	 * @return array
	 */
	public function ajax_action_needs_multi_currency( $actions ) {
		$actions[] = 'yith_load_product_quick_view';

		return $actions;
	}
}
