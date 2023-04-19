<?php

namespace WCML\Rest\Store;

use WPML\FP\Fns;
use function WCML\functions\getSetting;

class Hooks implements \IWPML_Action {

	const BEFORE_REST_API_LOADED = 0;

	public function add_hooks() {
		if ( wcml_is_multi_currency_on() ) {
			add_action( 'init', [ $this, 'initializeSession' ], self::BEFORE_REST_API_LOADED );
		}

		if ( getSetting( 'reviews_in_all_languages', false ) ) {
			add_action( 'wpml_is_comment_query_filtered', Fns::always( false ) );
		}
	}

	public function initializeSession() {
		if ( ! isset( WC()->session ) ) {
			WC()->initialize_session();
		}
	}

}
