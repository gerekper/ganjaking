<?php

namespace ACA\WC\Column\ShopCoupon;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACP;
use WC_Coupon;

/**
 * @since 1.0
 */
class FreeShipping extends AC\Column\Meta
    implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Search\Searchable
{

    public function __construct()
    {
        $this->set_type('column-wc-free_shipping')
             ->set_label(__('Free Shipping', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_meta_key()
    {
        return 'free_shipping';
    }

    public function get_value($post_id)
    {
        $free_shipping = $this->get_raw_value($post_id);

        if ($free_shipping) {
            return ac_helper()->icon->yes(
                __(
                    'The free shipping method must be enabled with the &quot;must use coupon&quot; setting.',
                    'codepress-admin-columns'
                )
            );
        }

        return ac_helper()->icon->no($free_shipping);
    }

    public function get_raw_value($id)
    {
        return (new WC_Coupon($id))->get_free_shipping();
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\Meta($this->get_meta_key());
    }

    public function editing()
    {
        return new Editing\ShopCoupon\FreeShipping();
    }

    public function search()
    {
        return new Search\ShopCoupon\FreeShipping();
    }

}