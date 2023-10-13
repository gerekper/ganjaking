<?php

namespace WCML\Rest\Wrapper;

use WCML\Rest\ProductSaveActions;

use WCML\Rest\Wrapper\Orders\Languages as OrdersLanguages;
use WCML\Rest\Wrapper\Orders\Prices as OrdersPrices;
use WCML\Rest\Wrapper\Products\Products;
use WCML\Rest\Wrapper\Reports\ProductsCount;
use WCML\Rest\Wrapper\Reports\ProductsSales;
use WCML\Rest\Wrapper\Reports\TopSeller;

use function WCML\functions\isStandAlone;

class Factory {

	/**
	 * @param string $objectType
	 *
	 * @return Handler
	 */
	public static function create( $objectType ) {
		/**
		 * @var \woocommerce_wpml      $woocommerce_wpml
		 * @var \WPML_Post_Translation $wpml_post_translations
		 * @var \WPML_Term_Translation $wpml_term_translations
		 * @var \SitePress             $sitepress
		 * @var \WPML_Query_Filter     $wpml_query_filter
		 * @var \wpdb                  $wpdb
		 */
		global $woocommerce_wpml, $wpml_post_translations, $wpml_term_translations, $sitepress, $wpml_query_filter, $wpdb;

		$isMultiCurrencyOn = wcml_is_multi_currency_on();

		switch ( $objectType ) {
			case 'shop_order':
				$objects[] = new OrdersLanguages();
				if ( $isMultiCurrencyOn ) {
					$objects[] = new OrdersPrices( $woocommerce_wpml->multi_currency->orders );
				}

				return new Composite( $objects );
			case 'product_variation':
			case 'product':
				if ( ! isStandAlone() ) {
					return new Products(
						$sitepress,
						$wpml_post_translations,
						$wpml_query_filter,
						new ProductSaveActions( $sitepress->get_settings(), $wpdb, $sitepress, $woocommerce_wpml->sync_product_data )
					);
				}
				break;
			case 'term':
				if ( ! isStandAlone() ) {
					return new ProductTerms( $sitepress, $wpml_term_translations, $woocommerce_wpml->terms );
				}
				break;
			case 'reports_top_seller':
				if ( ! isStandAlone() ) {
					return new TopSeller( $sitepress );
				}
				break;
			case 'reports_products_count':
				if ( ! isStandAlone() ) {
					return new ProductsCount( $sitepress, $wpdb );
				}
				break;
			case 'reports_products_sales':
				if ( $isMultiCurrencyOn ) {
					return new ProductsSales();
				}
				break;
		}

		return new Handler();
	}

}
