<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Settings;
use ACA\WC\Sorting;
use ACP;

class PurchaseNote extends AC\Column\Meta
    implements ACP\Export\Exportable, ACP\Editing\Editable, ACP\Sorting\Sortable,
               ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-wc-product_purchase_note')
             ->set_label(__('Purchase Note', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function get_meta_key()
    {
        return '_purchase_note';
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedRawValue($this);
    }

    public function editing()
    {
        return new ACP\Editing\Service\Basic(
            (new ACP\Editing\View\TextArea())->set_clear_button(true),
            new ACP\Editing\Storage\Post\Meta($this->get_meta_key())
        );
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\Meta($this->get_meta_key());
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Text($this->get_meta_key());
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\Product\UseIcon($this));
    }

}