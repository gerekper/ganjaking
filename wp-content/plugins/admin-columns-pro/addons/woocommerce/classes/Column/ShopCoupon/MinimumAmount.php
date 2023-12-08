<?php

namespace ACA\WC\Column\ShopCoupon;

use ACA\WC\Editing;
use ACP;
use ACP\Sorting\Type\DataType;
use WC_Coupon;

class MinimumAmount extends ACP\Column\Meta
    implements ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\IntegerFormattableTrait;

    public function __construct()
    {
        $this->set_type('column-wc-minimum_amount')
             ->set_label(__('Minimum Amount', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_meta_key()
    {
        return 'minimum_amount';
    }

    public function get_value($id)
    {
        $amount = $this->get_raw_value($id);

        if ( ! $amount) {
            return $this->get_empty_char();
        }

        return wc_price($amount);
    }

    public function editing()
    {
        return new Editing\ShopCoupon\MinimumAmount();
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\Meta($this->get_meta_key(), new DataType(DataType::NUMERIC));
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Number($this->get_meta_key());
    }

    public function get_raw_value($id)
    {
        return (new WC_Coupon($id))->get_minimum_amount();
    }

}