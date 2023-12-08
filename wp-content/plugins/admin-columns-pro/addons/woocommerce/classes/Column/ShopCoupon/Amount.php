<?php

namespace ACA\WC\Column\ShopCoupon;

use ACA\WC\Editing;
use ACA\WC\Export;
use ACP;
use ACP\Sorting\Type\DataType;
use WC_Coupon;

class Amount extends ACP\Column\Meta
    implements ACP\Export\Exportable, ACP\Search\Searchable
{

    public function __construct()
    {
        $this->set_type('amount')
             ->set_original(true);
    }

    public function get_meta_key()
    {
        return 'coupon_amount';
    }

    public function get_value($id)
    {
        return null;
    }

    public function get_raw_value($id)
    {
        return (new WC_Coupon($id))->get_amount();
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\Meta($this->get_meta_key(), new DataType(DataType::NUMERIC));
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Decimal($this->get_meta_key());
    }

    public function editing()
    {
        return new Editing\ShopCoupon\Amount();
    }

    public function export()
    {
        return new Export\ShopCoupon\Amount();
    }

}