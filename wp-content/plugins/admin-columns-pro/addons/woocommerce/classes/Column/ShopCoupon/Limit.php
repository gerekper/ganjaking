<?php

namespace ACA\WC\Column\ShopCoupon;

use AC;
use ACA\WC\Settings;
use ACP;
use ACP\Sorting\Type\DataType;

class Limit extends AC\Column\Meta
    implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\IntegerFormattableTrait;

    public function __construct()
    {
        $this->set_type('column-shop-coupon_limit')
             ->set_label(__('Coupon limit', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_meta_key()
    {
        return $this->get_setting('coupon_limit')->get_value();
    }

    protected function register_settings()
    {
        $this->add_setting(new Settings\ShopCoupon\Limit($this));
    }

    public function editing()
    {
        return new ACP\Editing\Service\Basic(
            (new ACP\Editing\View\Number())->set_min(0)->set_step(1),
            new ACP\Editing\Storage\Post\Meta($this->get_meta_key())
        );
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\Meta($this->get_meta_key(), new DataType(DataType::NUMERIC));
    }

}