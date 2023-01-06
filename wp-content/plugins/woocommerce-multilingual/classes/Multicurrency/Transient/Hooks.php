<?php

namespace WCML\Multicurrency\Transient;

use WCML\MultiCurrency\Settings as McSettings;
use WPML\FP\Fns;
use WPML\FP\Str;
use WPML\LIB\WP\Hooks as WpHooks;
use function WCML\functions\getClientCurrency;
use function WPML\FP\spreadArgs;

class Hooks {

	/**
	 * @param string $key
	 */
	public static function addHooks( $key ) {
		$getKeyWithCurrency       = Str::concat( $key . '_' );
		$getKeyWithClientCurrency = function() use ( $getKeyWithCurrency ) {
			return $getKeyWithCurrency( getClientCurrency() );
		};

		$getTransient = function() use ( $getKeyWithClientCurrency ) {
			return get_transient( $getKeyWithClientCurrency() );
		};

		$setTransient = function( $value ) use ( $key, $getKeyWithClientCurrency ) {
			delete_transient( $key );
			return set_transient( $getKeyWithClientCurrency(), $value );
		};

		$deleteTransient = function() use ( $getKeyWithCurrency ) {
			foreach ( McSettings::getActiveCurrencyCodes() as $code ) {
				delete_transient( $getKeyWithCurrency( $code ) );
			}
		};

		$withLock = Fns::withNamedLock( __CLASS__ . "_$key", Fns::identity() );

		WpHooks::onFilter( 'pre_transient_' . $key )
			->then( $getTransient );

		WpHooks::onAction( 'set_transient_' . $key )
			->then( spreadArgs( $withLock( $setTransient ) ) );

		WpHooks::onAction( 'delete_transient_' . $key )
			->then( $withLock( $deleteTransient ) );
	}

}
