<?php

namespace WCML\Products;

use WPML\FP\Obj;
use WPML\LIB\WP\Hooks as WpHooks;
use function WPML\FP\spreadArgs;

class Hooks implements \IWPML_Frontend_Action, \IWPML_Backend_Action {

	public function add_hooks() {
		WpHooks::onFilter( 'woocommerce_variable_children_args' )
		       ->then( spreadArgs( self::forceProductLanguageInQuery() ) );
	}

	/**
	 * @return \Closure array -> array
	 */
	private static function forceProductLanguageInQuery() {
		return function( $args ) {
			return Obj::assoc(
				'wpml_lang',
				apply_filters( 'wpml_element_language_code', '', [ 'element_id' => Obj::prop( 'post_parent', $args ), 'element_type' => 'product_variation' ] ),
				$args
			);
		};
	}
}
