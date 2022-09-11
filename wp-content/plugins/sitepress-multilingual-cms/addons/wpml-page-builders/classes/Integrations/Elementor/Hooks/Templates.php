<?php

namespace WPML\PB\Elementor\Hooks;

use WPML\FP\Obj;
use WPML\LIB\WP\Hooks;
use function WPML\FP\spreadArgs;

class Templates implements \IWPML_Frontend_Action, \IWPML_Backend_Action {

	public function add_hooks() {
		Hooks::onFilter( 'elementor/theme/conditions/cache/regenerate/query_args' )
			->then( spreadArgs( Obj::assoc( 'suppress_filters', true ) ) );
	}
}
