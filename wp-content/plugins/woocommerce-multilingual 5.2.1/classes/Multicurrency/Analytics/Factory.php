<?php

namespace WCML\Multicurrency\Analytics;

use WCML\StandAlone\IStandAloneAction;

class Factory implements \IWPML_Backend_Action_Loader, \IWPML_REST_Action_Loader, \IWPML_Deferred_Action_Loader, IStandAloneAction {

	/**
	 * @return string
	 */
	public function get_load_action() {
		return 'init';
	}

	/**
	 * @return \IWPML_Action|null
	 */
	public function create() {
		/**
		 * @global \woocommerce_wpml $GLOBALS['woocommerce_wpml']
		 * @name $woocommerce_wpml
		 */
		global $woocommerce_wpml;

		/**
		 * @global \wpdb $GLOBALS['wpdb']
		 * @name $wpdb
		 */
		global $wpdb;

		if ( wcml_is_multi_currency_on() ) {
			return new Hooks( $woocommerce_wpml, $wpdb );
		}

		return null;
	}

}
