<?php

namespace WCML\Orders;

use WPML\FP\Fns;
use WPML\FP\Logic;
use WPML\FP\Maybe;
use WPML\FP\Relation;
use WPML\LIB\WP\Cache;
use function WPML\FP\invoke;
use function WPML\FP\pipe;
use WCML\COT\Helper as COTHelper;
use WCML\Orders\Legacy\Helper as LegacyHelper;

class Helper {

	const CACHE_GROUP         = 'wcml_order_currency';
	const KEY_LEGACY_CURRENCY = '_order_currency';

	/**
	 * @param int  $orderId
	 * @param bool $useDB
	 *
	 * @return string|null
	 */
	public static function getCurrency( $orderId, $useDB = false ) {
		$useDB = $useDB || ! did_action( 'woocommerce_after_register_post_type' );

		if ( $useDB ) {
			return self::getCurrencyFromDB( $orderId );
		} else {
			return self::getCurrencyFromOrderObject( $orderId );
		}
	}

	/**
	 * @param int $orderId
	 *
	 * @return string|null
	 */
	private static function getCurrencyFromDB( $orderId ) {
		/** @var callable(int):(string|null) $getCurrency */
		$getCurrency = Cache::memorize( self::CACHE_GROUP, MINUTE_IN_SECONDS, function( $orderId ) {
			/** @var \wpdb $wpdb */
			global $wpdb;

			if ( \WCML\COT\Helper::isUsageEnabled() ) {
				$orderTable = \WCML\COT\Helper::getTableName();

				$currency = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT currency FROM {$orderTable} WHERE id = %d",
						$orderId
					)
				);
			} else {
				$currency = get_post_meta( $orderId, self::KEY_LEGACY_CURRENCY, true );
			}

			return $currency ?: null;
		} );

		return $getCurrency( $orderId );
	}

	/**
	 * @param int $orderId
	 *
	 * @return string|null
	 */
	private static function getCurrencyFromOrderObject( $orderId ) {
		$isNotAutoDraft = pipe(
			invoke( 'get_status' ),
			Relation::equals( 'auto-draft' ),
			Logic::not()
		);

		return Maybe::fromNullable( wc_get_order( $orderId ) )
			->filter( $isNotAutoDraft )
			->map( invoke( 'get_currency' ) )
			->getOrElse( null );
	}

	/**
	 * @param int    $orderId
	 * @param string $currency
	 *
	 * @return void
	 */
	public static function setCurrency( $orderId, $currency ) {
		Maybe::fromNullable( wc_get_order( $orderId ) )
			->map( Fns::tap( invoke( 'set_currency' )->with( $currency ) ) )
			->map( invoke( 'save' ) );
	}

	/**
	 * Checks if the current screen is an admin screen for WooCommerce New Order (Legacy or HPOS).
	 *
	 * @return bool
	 */
	public static function isOrderCreateAdminScreen(): bool {
		return COTHelper::isOrderCreateAdminScreen() || LegacyHelper::isOrderCreateAdminScreen();
	}

	/**
	 * Checks if the current screen is an admin screen for list of WooCommerce orders (Legacy or HPOS).
	 *
	 * @return bool
	 */
	public static function isOrderListAdminScreen(): bool {
		return COTHelper::isOrderListAdminScreen() || LegacyHelper::isOrderListAdminScreen();
	}

	/**
	 * Checks if the current screen is an admin screen for WooCommerce Edit Order (Legacy or HPOS).
	 *
	 * @return bool
	 */
	public static function isOrderEditAdminScreen(): bool {
		return COTHelper::isOrderEditAdminScreen() || LegacyHelper::isOrderEditAdminScreen();
	}

}
