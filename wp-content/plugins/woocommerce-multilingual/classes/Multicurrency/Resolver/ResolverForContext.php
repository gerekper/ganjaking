<?php

namespace WCML\MultiCurrency\Resolver;

use WCML\COT\Helper as COTHelper;
use WCML\MultiCurrency\Settings;
use WCML\Orders\Helper as OrdersHelper;
use WPML\FP\Logic;
use WPML\FP\Obj;
use WPML\FP\Relation;

class ResolverForContext implements Resolver {

	/** @var callable $getOriginalProductLanguage */
	private $getOriginalProductLanguage;

	public function __construct( callable $getOriginalProductLanguage ) {
		$this->getOriginalProductLanguage = $getOriginalProductLanguage;
	}

	/**
	 * @inheritDoc
	 */
	public function getClientCurrency() {
		$getOnWoocommerceQuickEdit = function() {
			if ( ! empty( $_REQUEST['woocommerce_quick_edit'] ) ) {
				return wcml_get_woocommerce_currency_option();
			}

			return null;
		};

		$getIfMissingCustomPrice = function() {
			if ( Settings::isDisplayOnlyCustomPrices() && is_product() ) {
				$product                 = wc_get_product();
				$originalProductLanguage = call_user_func( $this->getOriginalProductLanguage, $product->get_id() );

				// $isMissingCustomPrice :: int -> bool
				$isMissingCustomPrice = function( $productOrVariationId ) use ( $originalProductLanguage ) {
					return ! get_post_meta(
						apply_filters( 'wpml_object_id', $productOrVariationId, get_post_type( $productOrVariationId ), true, $originalProductLanguage ),
						'_wcml_custom_prices_status',
						true
					);
				};

				if ( $product->get_type() === 'variable' ) {
					foreach ( $product->get_children() as $child ) {
						if ( $isMissingCustomPrice( $child ) ) {
							return wcml_get_woocommerce_currency_option();
						}
					}
				} elseif ( $isMissingCustomPrice( $product->get_id() ) ) {
					return wcml_get_woocommerce_currency_option();
				}
			}

			return null;
		};

		$getOnPayForOrder = function() {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			if ( isset( $_GET['pay_for_order'], $_GET['key'] ) && $_GET['pay_for_order'] ) {
				$cacheGroup = 'wcml_client_currency';
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				$cacheKey      = 'order' . sanitize_text_field( $_GET['key'] );
				$orderCurrency = wp_cache_get( $cacheKey, $cacheGroup );

				if ( $orderCurrency ) {
					return $orderCurrency;
				} else {
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$orderId = wc_get_order_id_by_order_key( wc_clean( wp_unslash( $_GET['key'] ) ) );
					if ( $orderId ) {
						$clientCurrency = OrdersHelper::getCurrency( $orderId, true );
						wp_cache_set( $cacheKey, $clientCurrency, $cacheGroup );
						return $clientCurrency;
					}
				}
			}

			return null;
		};

		$getOnSearchProductsFromOrderCurrencyCookie = function() {
			if ( Relation::propEq( 'action', 'woocommerce_json_search_products_and_variations', $_GET ) ) {
				return Obj::prop( '_wcml_order_currency', $_COOKIE );
			}

			return null;
		};

		$getFromHttpRefererShopOrderCurrency = function() {

			$refererUrl = Obj::prop( 'HTTP_REFERER', $_SERVER );

			if ( $refererUrl ) {
				$query = parse_url( $refererUrl, PHP_URL_QUERY );

				if ( $query ) {
					parse_str( $query, $queryArgs );

					if ( COTHelper::isUsageEnabled() ) {
						$id = (int) Obj::prop( 'id', $queryArgs );
						if ( $id && COTHelper::isOrder( $id ) ) {
							return OrdersHelper::getCurrency( $id, true );
						}
					} else {
						$postId = (int) Obj::prop( 'post', $queryArgs );
						if ( $postId && get_post_type( $postId ) === 'shop_order' ) {
							return OrdersHelper::getCurrency( $postId, true );
						}
					}

				}
			}

			return null;
		};

		$resolve = Logic::firstSatisfying(
			Logic::isTruthy(),
			[
				$getOnWoocommerceQuickEdit,
				$getIfMissingCustomPrice,
				$getOnPayForOrder,
				$getOnSearchProductsFromOrderCurrencyCookie,
				$getFromHttpRefererShopOrderCurrency,
			]
		);

		return $resolve( null );
	}
}
