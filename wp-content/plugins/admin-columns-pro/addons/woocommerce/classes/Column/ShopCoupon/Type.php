<?php

namespace ACA\WC\Column\ShopCoupon;

use ACA\WC\Editing;
use ACA\WC\Export;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;
use WC_Coupon;

class Type extends ACP\Column\Meta
    implements ACP\Export\Exportable, ACP\Search\Searchable
{

    public function __construct()
    {
        $this->set_type('type');
        $this->set_original(true);
    }

    public function get_value($id)
    {
        return null;
    }

    public function get_meta_key()
    {
        return 'discount_type';
    }

    public function get_raw_value($id)
    {
        return (new WC_Coupon($id))->get_discount_type();
    }

    public function editing()
    {
        return new Editing\ShopCoupon\Type();
    }

    public function sorting()
    {
        return new Sorting\ShopCoupon\Type();
    }

    public function search()
    {
        return new Search\ShopCoupon\Type($this->get_coupon_types());
    }

    public function export()
    {
        return new Export\ShopCoupon\Type();
    }

    public function get_coupon_types()
    {
        return wc_get_coupon_types();
    }

}