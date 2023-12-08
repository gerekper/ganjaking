<?php

namespace ACA\WC\Column\ShopOrder;

use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

class ProductTags extends ProductTaxonomy
{

    use FilteredHtmlFormatTrait;

    public function __construct()
    {
        parent::__construct();

        $this->set_type('column-wc-product_tags')
             ->set_label(__('Product Tags', 'codepress-admin-columns'));
    }

    public function get_taxonomy()
    {
        return 'product_tag';
    }

}