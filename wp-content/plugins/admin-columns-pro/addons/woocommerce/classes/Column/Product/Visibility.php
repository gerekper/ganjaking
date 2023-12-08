<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACP;

class Visibility extends AC\Column
    implements ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable,
               ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-wc-visibility')
             ->set_label(__('Catalog Visibility', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_taxonomy()
    {
        return 'product_visibility';
    }

    public function get_value($product_id)
    {
        $options = wc_get_product_visibility_options();

        $key = $this->get_raw_value($product_id);

        if ( ! isset($options[$key])) {
            return $this->get_empty_char();
        }

        return $options[$key];
    }

    public function get_raw_value($product_id)
    {
        return wc_get_product($product_id)->get_catalog_visibility();
    }

    public function editing()
    {
        return new Editing\Product\Visibility();
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

    public function search()
    {
        return new Search\Product\Visibility(wc_get_product_visibility_options());
    }

}