<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Field\ShopOrder;
use ACA\WC\Settings;
use ACA\WC\Sorting;
use ACP;

class OrderDate extends AC\Column
    implements ACP\Export\Exportable, ACP\Sorting\Sortable, ACP\Search\Searchable,
               ACP\Filtering\FilterableDateSetting
{

    /**
     * @var ShopOrder\OrderDate|null
     */
    private $field;

    public function __construct()
    {
        $this->set_label('Date')
             ->set_type('column-wc-order_date')
             ->set_group('woocommerce');
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\ShopOrder\OrderDate($this));
        $this->add_setting(new ACP\Filtering\Settings\Date($this, ['future_past']));
    }

    public function export()
    {
        $field = $this->get_field();

        if ($field instanceof ACP\Export\Exportable) {
            return $field->export();
        }

        return false;
    }

    public function sorting()
    {
        $field = $this->get_field();

        if ($field instanceof ACP\Sorting\Sortable) {
            return $field->sorting();
        }

        return null;
    }

    public function get_filtering_date_setting(): ?string
    {
        return $this->options['filter_format'] ?? null;
    }

    public function search()
    {
        $field = $this->get_field();

        if ($field instanceof ACP\Search\Searchable) {
            return $field->search();
        }

        return false;
    }

    private function set_field()
    {
        $type = $this->get_setting('date_type')->get_value();

        foreach ($this->get_fields() as $field) {
            if ($field->get_key() === $type) {
                $this->field = $field;
                break;
            }
        }
    }

    public function get_field(): ?ShopOrder\OrderDate
    {
        if (null === $this->field) {
            $this->set_field();
        }

        return $this->field;
    }

    /**
     * @return ShopOrder\OrderDate[]
     */
    public function get_fields(): array
    {
        return [
            new ShopOrder\OrderDate\Completed($this),
            new ShopOrder\OrderDate\Created($this),
            new ShopOrder\OrderDate\Modified($this),
            new ShopOrder\OrderDate\Paid($this),
        ];
    }

}