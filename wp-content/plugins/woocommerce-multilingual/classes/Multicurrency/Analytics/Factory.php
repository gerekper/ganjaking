<?php

namespace WCML\Multicurrency\Analytics;

use WCML\COT\Helper as COTHelper;
use WCML\StandAlone\IStandAloneAction;

class Factory implements \IWPML_Backend_Action_Loader, \IWPML_Frontend_Action_Loader, \IWPML_Deferred_Action_Loader, IStandAloneAction {

	/**
	 * @return string
	 */
	public function get_load_action() {
		return 'init';
	}

	/**
	 * @return \IWPML_Action[]
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

		$hooks = [
			COTHelper::isUsageEnabled() ? new ExportHPOS( $wpdb ) : new ExportLegacy( $wpdb ),
		];

		if ( wcml_is_multi_currency_on() ) {
			$hooks[] = new Hooks( $woocommerce_wpml, $wpdb );
		}

		return $hooks;
	}

}
