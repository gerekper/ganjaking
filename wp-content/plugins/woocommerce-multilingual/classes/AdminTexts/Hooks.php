<?php

namespace WCML\AdminTexts;

use WPML\FP\Lst;
use WPML\LIB\WP\Hooks as WPHooks;

use function WPML\FP\spreadArgs;

class Hooks implements \IWPML_Frontend_Action, \IWPML_Backend_Action {

	public function add_hooks() {
		WPHooks::onFilter( 'wpml_st_blacklisted_options' )
			->then( spreadArgs( Lst::append( 'woocommerce_permalinks' ) ) );
	}

}
