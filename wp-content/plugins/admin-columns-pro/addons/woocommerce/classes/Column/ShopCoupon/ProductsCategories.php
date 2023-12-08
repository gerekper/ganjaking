<?php

namespace ACA\WC\Column\ShopCoupon;

use ACA\WC\Column;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACP;

/**
 * @since 3.0
 */
class ProductsCategories extends Column\CouponProductCategories
    implements ACP\Editing\Editable, ACP\Search\Searchable
{

    public function __construct()
    {
        parent::__construct();

        $this->set_type('column-wc-coupon_product_categories')
             ->set_label(__('Product Categories', 'woocommerce'));
    }

    public function get_meta_key()
    {
        return 'product_categories';
    }

    public function editing()
    {
        return new Editing\ShopCoupon\ProductCategories();
    }

    public function search()
    {
        return new Search\ShopCoupon\Categories($this->get_meta_key());
    }

}