<?php

namespace WCML\OrderNotes;

use WPML\LIB\WP\Hooks as WPHooks;

class Hooks implements \IWPML_AJAX_Action {

	public function add_hooks() {
		WPHooks::onAction( 'woocommerce_after_order_object_save' )
			->then( function() {
				do_action( 'wpml_switch_language', apply_filters( 'wpml_default_language', null ) );
			} );
	}

}
