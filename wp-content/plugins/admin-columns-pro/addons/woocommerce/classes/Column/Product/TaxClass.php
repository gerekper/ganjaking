<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACP;
use WC_Tax;

class TaxClass extends AC\Column\Meta
    implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Search\Searchable,
               ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-wc-tax_class')
             ->set_label(__('Tax Class', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function get_meta_key()
    {
        return '_tax_class';
    }

    public function get_value($post_id)
    {
        $value = $this->get_raw_value($post_id);

        $classes = $this->get_tax_classes();

        if (isset($classes[$value])) {
            $value = $classes[$value];
        }

        if ( ! $value) {
            return $this->get_empty_char();
        }

        return $value;
    }

    public function get_raw_value($post_id)
    {
        return wc_get_product($post_id)->get_tax_class();
    }

    public function editing()
    {
        return new Editing\Product\TaxClass();
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\Meta($this->get_meta_key());
    }

    public function search()
    {
        return new Search\Product\TaxClass($this->get_tax_classes());
    }

    public function get_tax_classes()
    {
        $classes = [];

        foreach (WC_Tax::get_tax_classes() as $tax_class) {
            $classes[WC_Tax::format_tax_rate_class($tax_class)] = $tax_class;
        }

        return $classes;
    }

}