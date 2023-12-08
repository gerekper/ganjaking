<?php

namespace ACA\Types\Field;

use AC\MetaType;
use ACA\Types\Field;
use ACA\Types\Search;
use ACP;
use ACP\Editing;
use ACP\Sorting;

class Select extends Field
{

    public function editing()
    {
        return new Editing\Service\Basic(
            (new Editing\View\Select($this->get_field_options()))->set_clear_button(true),
            new Editing\Storage\Meta($this->get_meta_key(), new MetaType($this->get_meta_type()))
        );
    }

    public function sorting()
    {
        $options = $this->get_field_options();
        natcasesort($options);

        return (new Sorting\Model\MetaMappingFactory())->create(
            $this->get_meta_type(),
            $this->get_meta_key(),
            array_keys($options)
        );
    }

    public function search()
    {
        return new Search\Select(
            $this->column->get_meta_key(),
            $this->get_field_options()
        );
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this->column);
    }

    /**
     * @return array
     */
    private function get_field_options()
    {
        $result = [];
        $options = (array)$this->get('options');

        foreach ($options as $option) {
            if ( ! is_array($option)) {
                continue;
            }

            $result[$option['value']] = $option['title'];
        }

        return $result;
    }

}