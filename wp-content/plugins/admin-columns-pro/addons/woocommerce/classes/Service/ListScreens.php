<?php

namespace ACA\WC\Service;

use AC\ListScreenFactory;
use AC\Registerable;
use ACA\WC\ListScreenFactory\ProductCategoryFactory;
use ACA\WC\ListScreenFactory\ProductFactory;
use ACA\WC\ListScreenFactory\ProductVariationFactory;
use ACA\WC\ListScreenFactory\ShopCouponFactory;
use ACA\WC\ListScreenFactory\ShopOrderFactory;

class ListScreens implements Registerable {

	private $use_product_variations;

	public function __construct( bool $use_product_variations ) {
		$this->use_product_variations = $use_product_variations;
	}

	public function register() {
		ListScreenFactory::add( new ProductFactory() );
		ListScreenFactory::add( new ShopCouponFactory() );
		ListScreenFactory::add( new ShopOrderFactory() );
		ListScreenFactory::add( new ProductCategoryFactory() );

		if ( $this->use_product_variations ) {
			ListScreenFactory::add( new ProductVariationFactory() );
		}
	}

}