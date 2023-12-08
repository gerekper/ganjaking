<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use AC\MetaType;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACP;

class IP extends AC\Column
    implements ACP\Search\Searchable, ACP\ConditionalFormat\Formattable, ACP\Editing\Editable, ACP\Sorting\Sortable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-wc-order_ip')
             ->set_label(__('Customer IP address', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_meta_key(): string
    {
        switch ($this->get_setting('ip_property')->get_value()) {
            case 'country':
                return '_customer_ip_country';
            default:
                return '_customer_ip_address';
        }
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\ShopOrder\IP($this));
    }

    public function editing()
    {
        return new ACP\Editing\Service\Basic(
            (new ACP\Editing\View\Text())->set_clear_button(true),
            new ACP\Editing\Storage\Meta($this->get_meta_key(), new MetaType(MetaType::POST))
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

}