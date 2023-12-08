<?php

namespace ACA\BP\Field\Profile;

use ACA\BP\Editing;
use ACA\BP\Export;
use ACA\BP\Field\Profile;
use ACA\BP\Filtering;
use ACA\BP\Search;
use ACP;

class Checkbox extends Profile
{

    public function get_value($id)
    {
        return ac_helper()->html->small_block($this->column->get_raw_value($id));
    }

    public function editing()
    {
        return new ACP\Editing\Service\Basic(
            new ACP\Editing\View\CheckboxList($this->get_options()),
            new Editing\Storage\Profile\MultiChoices($this->column->get_buddypress_field_id())
        );
    }

    public function sorting()
    {
        return null;
    }

    public function export()
    {
        return new Export\MultipleValues($this->column);
    }

    public function search()
    {
        return new Search\Profile\MultipleChoice($this->column->get_buddypress_field_id(), $this->get_options());
    }

    private function get_options()
    {
        $options = [];
        foreach ($this->column->get_buddypress_field()->get_children() as $option) {
            $options[$option->name] = $option->name;
        }

        return $options;
    }

}