<?php

namespace ACA\WC\Service;

use AC;
use AC\Registerable;
use ACA\WC\ListScreen;

class ListScreens implements Registerable {

	/**
	 * @var bool
	 */
	private $use_product_variations;

	public function __construct( $use_product_variations ) {
		$this->use_product_variations = $use_product_variations;
	}

	public function register() {
		add_action( 'ac/list_screens', [ $this, 'register_list_screens' ] );
	}

	public function register_list_screens() {
		$list_screens = [
			new ListScreen\ShopOrder(),
			new ListScreen\ShopCoupon(),
			new ListScreen\Product(),
			new ListScreen\ProductCategory(),
		];

		if ( $this->use_product_variations ) {
			$list_screens[] = new ListScreen\ProductVariation;
		}

		foreach ( $list_screens as $list_screen ) {
			AC\ListScreenTypes::instance()->register_list_screen( $list_screen );
		}
	}

}