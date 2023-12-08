<?php

namespace ACA\WC\Column\ShopCoupon;

use AC;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACP;
use ACP\Sorting\Type\DataType;

/**
 * @since 1.0
 */
class Usage extends AC\Column\Meta
    implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable
{

    public function __construct()
    {
        $this->set_type('usage')
             ->set_original(true);
    }

    public function get_meta_key()
    {
        return 'usage_count';
    }

    public function get_value($id)
    {
        return '';
    }

    public function editing()
    {
        return new Editing\ShopCoupon\Usage();
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\Meta($this->get_meta_key(), new DataType(DataType::NUMERIC));
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Number($this->get_meta_key());
    }

    public function export()
    {
        return new Export\ShopCoupon\Usage();
    }

}