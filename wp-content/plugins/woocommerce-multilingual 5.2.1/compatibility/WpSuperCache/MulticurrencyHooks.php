<?php

namespace WCML\Compatibility\WpSuperCache;

use WPML\FP\Lst;
use WPML\FP\Fns;

class MulticurrencyHooks implements \IWPML_Action {

	public function add_hooks() {
		add_filter( 'wcml_multicurrency_supported_cache_plugins', Lst::append( 'WP Super Cache' ) );

		add_filter( 'wcml_user_store_strategy', Fns::always( 'cookie' ) );

		do_action( 'wpsc_add_cookie', 'wcml_client_currency' );
		do_action( 'wpsc_add_cookie', 'client_currency_switched' );
	}

}
