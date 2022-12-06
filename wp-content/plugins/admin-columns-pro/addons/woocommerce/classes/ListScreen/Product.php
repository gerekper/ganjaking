<?php

namespace ACA\WC\ListScreen;

use ACA\WC\Column;
use ACA\WC\Editing;
use ACP;

class Product extends ACP\ListScreen\Post {

	public function __construct() {
		parent::__construct( 'product' );

		$this->set_group( 'woocommerce' );
	}

	public function editing() {
		return new Editing\Strategy\Product( $this->get_post_type() );
	}

	protected function register_column_types() {
		parent::register_column_types();

		$this->register_column_types_from_list( [
			Column\Product\Attributes::class,
			Column\Product\AvgOrderInterval::class,
			Column\Product\BackordersAllowed::class,
			Column\Product\Comments::class,
			Column\Product\Coupons::class,
			Column\Product\Crosssells::class,
			Column\Product\Customers::class,
			Column\Product\Date::class,
			Column\Product\DefaultFormValues::class,
			Column\Product\Dimensions::class,
			Column\Product\Downloads::class,
			Column\Product\Featured::class,
			Column\Product\Gallery::class,
			Column\Product\GroupedProducts::class,
			Column\Product\LowOnStock::class,
			Column\Product\MenuOrder::class,
			Column\Product\Name::class,
			Column\Product\OrderCount::class,
			Column\Product\OrderTotal::class,
			Column\Product\Price::class,
			Column\Product\ProductCat::class,
			Column\Product\ProductParent::class,
			Column\Product\ProductTag::class,
			Column\Product\ProductType::class,
			Column\Product\PurchaseNote::class,
			Column\Product\Rating::class,
			Column\Product\Reviews::class,
			Column\Product\ReviewsEnabled::class,
			Column\Product\Sale::class,
			Column\Product\Sales::class,
			Column\Product\ShippingClass::class,
			Column\Product\ShortDescription::class,
			Column\Product\SKU::class,
			Column\Product\SoldIndividually::class,
			Column\Product\Stock::class,
			Column\Product\StockStatus::class,
			Column\Product\TaxClass::class,
			Column\Product\TaxStatus::class,
			Column\Product\Thumb::class,
			Column\Product\Type::class,
			Column\Product\Upsells::class,
			Column\Product\Variation::class,
			Column\Product\Visibility::class,
			Column\Product\Weight::class,
		] );
	}

}