<?php

namespace WCML\Rest;

use WCML\Rest\Wrapper\Factory;

class Hooks {

	public static function addHooks() {

		Generic::preventDefaultLangUrlRedirect();

		add_action( 'rest_api_init', [ Language\Set::class, 'fromUrlQueryVar' ] );
		add_filter( 'rest_request_before_callbacks', [ Language\Set::class, 'beforeCallbacks' ], 10, 3 );
		add_action( 'parse_query', [ Generic::class, 'autoAdjustIncludedIds' ] );

		foreach ( [ 'product', 'shop_order', 'product_variation' ] as $type ) {

			$restObject = Factory::create( $type );

			add_filter( "woocommerce_rest_{$type}_query", [ $restObject, 'query' ], 10, 2 );
			add_filter( "woocommerce_rest_{$type}_object_query", [ $restObject, 'query' ], 10, 2 );
			add_action( "woocommerce_rest_prepare_{$type}_object", [ $restObject, 'prepare' ], 10, 3 );
			add_action( "woocommerce_rest_insert_{$type}_object", [ $restObject, 'insert' ], 10, 3 );
		}

		$attributeTaxonomies = wc_get_attribute_taxonomy_names();

		foreach ( array_merge( [ 'product_cat', 'product_tag', 'product_shipping_class' ], $attributeTaxonomies ) as $type ) {

			$restObject = Factory::create( 'term' );

			add_filter( "woocommerce_rest_{$type}_query", [ $restObject, 'query' ], 10, 2 );
			add_action( "woocommerce_rest_prepare_{$type}", [ $restObject, 'prepare' ], 10, 3 );
			add_action( "woocommerce_rest_insert_{$type}", [ $restObject, 'insert' ], 10, 3 );
		}

		add_filter( "woocommerce_rest_prepare_report_top_sellers", [ Factory::create( 'reports_top_seller' ), 'prepare' ], 10, 3 );
		add_filter( "woocommerce_rest_prepare_report_products_count", [ Factory::create( 'reports_products_count' ), 'prepare' ], 10, 3 );
		add_filter( "woocommerce_rest_prepare_report_sales", [ Factory::create( 'reports_products_sales' ), 'prepare' ], 10, 3 );

		self::addHooksSpecificForV1();
	}

	private static function addHooksSpecificForV1() {

		if ( 1 === Functions::getApiRequestVersion() ) {
			add_action( 'woocommerce_rest_prepare_product', [ Factory::create( 'product' ), 'prepare' ], 10, 3 );
			add_action( 'woocommerce_rest_insert_product', [ Factory::create( 'product' ), 'insert' ], 10, 3 );
			add_action( 'woocommerce_rest_update_product', [ Factory::create( 'product' ), 'insert' ], 10, 3 );

			add_action( 'woocommerce_rest_insert_shop_order', [ Factory::create( 'shop_order' ), 'insert' ], 10, 3 );
			add_action( 'woocommerce_rest_prepare_shop_order', [ Factory::create( 'shop_order' ), 'prepare' ], 10, 3 );
		}
	}

}
