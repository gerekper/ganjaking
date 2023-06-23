<?php

namespace WCML\COT;

use WPML\LIB\WP\Hooks as WPHooks;
use Automattic\WooCommerce\Utilities\FeaturesUtil as FeaturesUtil;
use WCML\StandAlone\IStandAloneAction;

class Hooks implements \IWPML_Frontend_Action, \IWPML_Backend_Action, IStandAloneAction {

	const FEATURE = 'custom_order_tables';

	public function add_hooks() {
		WPHooks::onAction( 'before_woocommerce_init' )
			->then( function() {
				if ( class_exists( FeaturesUtil::class ) ) {
					FeaturesUtil::declare_compatibility( self::FEATURE, WCML_PLUGIN_PATH . '/wpml-woocommerce.php', true );
				}
			} );
	}

}
