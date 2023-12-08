<?php

namespace ACA\Types\Field;

use AC\Helper\Select\Option;
use AC\MetaType;
use AC\Type\ToggleOptions;
use ACA\Types\Field;
use ACA\Types\Search;
use ACP;
use ACP\Editing\Service\Basic;
use ACP\Sorting;

class Checkbox extends Field
{

    public function get_value($id)
    {
        $value = parent::get_value($id);

        return ac_helper()->icon->yes_or_no($value !== '') . ' ' . $value;
    }

    public function editing()
    {
        return new Basic(
            new ACP\Editing\View\Toggle(
                new ToggleOptions(
                    new Option($this->get('save_empty') === 'yes' ? '0' : ''),
                    new Option($this->get('set_value'))
                )
            ),
            new ACP\Editing\Storage\Meta($this->get_meta_key(), new MetaType($this->get_meta_type()))
        );
    }

    public function sorting()
    {
        return (new Sorting\Model\MetaFactory())->create($this->get_meta_type(), $this->get_meta_key());
    }

    public function search()
    {
        return new Search\Checkbox($this->column->get_meta_key());
    }

}
