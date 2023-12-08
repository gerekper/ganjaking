<?php

namespace ACA\BP\Field\Profile;

use ACA\BP\Editing;
use ACA\BP\Field\Profile;
use ACA\BP\Filtering;
use ACA\BP\Search;
use ACP\Editing\Service\Basic;
use ACP\Editing\View\Select;

class Radio extends Profile
{

    public function editing()
    {
        return new Basic(
            (new Select($this->get_options()))->set_clear_button(true),
            new Editing\Storage\Profile($this->column->get_buddypress_field_id())
        );
    }

    public function search()
    {
        return new Search\Profile\Choice($this->column->get_buddypress_field_id(), $this->get_options());
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