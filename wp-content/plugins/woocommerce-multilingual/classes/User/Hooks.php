<?php

namespace WCML\User;

use IWPML_Frontend_Action;
use WPML\LIB\WP\Hooks as WpHooks;
use function WPML\FP\spreadArgs;

class Hooks implements IWPML_Frontend_Action {

	public function add_hooks() {
		WpHooks::onAction( 'woocommerce_created_customer', 10, 1 )
			->then( spreadArgs( [ $this, 'setCustomerProfileLanguage' ] ) );
	}

	/**
	 * Set user's language to current language
	 *
	 * @param int $userId
	 */
	public function setCustomerProfileLanguage( $userId ) {
		return wp_update_user( [
			'ID'     => $userId,
			'locale' => wpml_get_current_language(),
		] );
	}

}

