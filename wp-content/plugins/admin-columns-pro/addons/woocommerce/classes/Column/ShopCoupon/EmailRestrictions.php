<?php

namespace ACA\WC\Column\ShopCoupon;

use AC;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACA\WC\Search\ShopCoupon\EmailRestriction;
use ACP;
use WC_Coupon;

class EmailRestrictions extends AC\Column
    implements ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable,
               ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-wc-email-restrictions')
             ->set_label(__('Email Restrictions', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $emails = $this->get_raw_value($id);

        if (empty($emails)) {
            return $this->get_empty_char();
        }

        return implode(', ', $emails);
    }

    public function editing()
    {
        return new Editing\ShopCoupon\EmailRestrictions();
    }

    public function export()
    {
        return new Export\ShopCoupon\EmailRestrictions($this);
    }

    public function search()
    {
        return new EmailRestriction();
    }

    public function get_raw_value($id)
    {
        return (new WC_Coupon($id))->get_email_restrictions();
    }

}