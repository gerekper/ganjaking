<?php

namespace ACA\BP\Field\Profile;

use ACA\BP\Editing;
use ACA\BP\Field\Profile;
use ACA\BP\Search;
use ACA\BP\Sorting;
use ACP;
use ACP\Sorting\Type\DataType;

class Datebox extends Profile
{

    public function editing()
    {
        return new ACP\Editing\Service\Date(
            (new ACP\Editing\View\Date())->set_clear_button(true),
            new Editing\Storage\Profile($this->column->get_buddypress_field_id()),
            'Y-m-d 00:00:00'
        );
    }

    public function search()
    {
        return new Search\Profile\Date(
            $this->column->get_buddypress_field_id()
        );
    }

    public function sorting()
    {
        return new Sorting\Profile($this->column, new DataType(DataType::DATETIME));
    }

    public function get_dependent_settings()
    {
        $settings = parent::get_dependent_settings();
        $settings[] = new ACP\Filtering\Settings\Date($this->column);

        return $settings;
    }

    public function get_filtering_date_setting(): ?string
    {
        $setting = $this->column->get_setting('filter');

        if ($setting instanceof ACP\Filtering\Settings\Date) {
            return $setting->get_filter_format() ?? 'range';
        }

        return 'range';
    }

}