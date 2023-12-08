<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA;
use ACA\WC\Editing;
use ACP;

class TaxStatus extends AC\Column\Meta
    implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Export\Exportable,
               ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-wc-tax_status')
             ->set_label(__('Tax Status', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function get_meta_key()
    {
        return '_tax_status';
    }

    public function get_value($post_id)
    {
        $value = $this->get_raw_value($post_id);
        $status = $this->get_tax_status();

        if (isset($status[$value])) {
            $value = $status[$value];
        }

        if ( ! $value) {
            return $this->get_empty_char();
        }

        return $value;
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\Meta($this->get_meta_key());
    }

    public function editing()
    {
        return new Editing\Product\TaxStatus($this->get_tax_status());
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

    public function search()
    {
        return new ACA\WC\Search\Product\TaxStatus($this->get_tax_status());
    }

    public function get_tax_status()
    {
        return [
            'taxable'  => __('Taxable', 'woocommerce'),
            'shipping' => __('Shipping only', 'woocommerce'),
            'none'     => _x('None', 'Tax status', 'woocommerce'),
        ];
    }

}