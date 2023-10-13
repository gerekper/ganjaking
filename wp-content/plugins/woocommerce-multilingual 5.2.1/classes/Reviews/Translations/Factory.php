<?php

namespace WCML\Reviews\Translations;

use function WPML\Container\make;

class Factory implements \IWPML_Deferred_Action_Loader, \IWPML_Frontend_Action_Loader, \IWPML_AJAX_Action_Loader {
	
	
	public function create() {
		return [
			make( FrontEndHooks::class ),
		];
	}

	/**
	 * These hooks are deferred to allow 3rd party
	 * themes/plugins to disable the feature
	 * with 'wcml_enable_product_review_translation'.
	 *
	 * @return string
	 */
	public function get_load_action() {
		return 'init';
	}
}