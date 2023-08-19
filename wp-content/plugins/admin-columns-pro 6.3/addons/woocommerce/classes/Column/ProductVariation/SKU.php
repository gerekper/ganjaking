<?php

namespace ACA\WC\Column\ProductVariation;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;
use WC_Product_Variation;

/**
 * @since 3.0
 */
class SKU extends AC\Column\Meta
    implements ACP\Editing\Editable, ACP\Search\Searchable, ACP\Export\Exportable, ACP\Sorting\Sortable
{

    public function __construct()
    {
        $this->set_type('variation_sku')
             ->set_label(__('SKU', 'woocommerce'))
             ->set_original(true);
    }

    public function get_value($id)
    {
        $variation = new WC_Product_Variation($id);

        $sku = $variation->get_sku();

        if (empty($sku)) {
            return $this->get_empty_char();
        }

        $data = $variation->get_data();

        if (empty($data['sku'])) {
            $sku .= ac_helper()->html->tooltip(
                '<span class="woocommerce-help-tip"></span>',
                __('SKU from product', 'codepress-admin-columns')
            );
        }

        return $sku;
    }

    public function get_meta_key()
    {
        return '_sku';
    }

    public function editing()
    {
        $view = (new ACP\Editing\View\Text())->set_clear_button(true);

        return new ACP\Editing\Service\Basic(
            $view,
            new Editing\Storage\Product\Sku()
        );
    }

    public function search()
    {
        return new Search\ProductVariation\SKU();
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

    public function sorting()
    {
        // The class `Sorting\ProductVariation\SKU` will take up less memory but the sorting results are not naturally sorted.
        return new Sorting\ProductVariation\SkuNaturalSort();
    }

}