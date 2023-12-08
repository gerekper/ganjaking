<?php

namespace ACA\EC\Column\Event;

use ACA\EC\Column\Meta;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter\FloatFormatter;
use ACP\ConditionalFormat\Formatter\SanitizedFormatter;
use ACP\Sorting\Type\DataType;

class Costs extends Meta
    implements ACP\ConditionalFormat\Formattable
{

    public function __construct()
    {
        $this->set_type('column-ec-event_costs')
             ->set_label(__('Costs', 'codepress-admin-columns'));

        parent::__construct();
    }

    public function get_meta_key()
    {
        return '_EventCost';
    }

    public function get_value($id)
    {
        $value = tribe_get_formatted_cost($id);

        if ( ! $value) {
            return $this->get_empty_char();
        }

        return $value;
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\Meta($this->get_meta_key(), new DataType(DataType::NUMERIC));
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Number($this->get_meta_key());
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(SanitizedFormatter::from_ignore_strings(new FloatFormatter()));
    }

}