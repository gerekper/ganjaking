<?php

namespace ACA\BP\Field;

use ACA\BP\Column;
use ACA\BP\Editing;
use ACA\BP\Sorting;
use ACP;
use BP_XProfile_ProfileData;

class Profile
    implements ACP\Search\Searchable
{

    /**
     * @var Column\Profile
     */
    protected $column;

    public function __construct(Column\Profile $column)
    {
        $this->set_column($column);
    }

    public function get_value($id)
    {
        $value = bp_get_profile_field_data([
            'field'   => $this->column->get_buddypress_field_id(),
            'user_id' => $id,
        ]);

        return $this->column->get_formatted_value($value);
    }

    public function get_raw_value($id)
    {
        return maybe_unserialize(
            BP_XProfile_ProfileData::get_value_byid($this->column->get_buddypress_field_id(), $id)
        );
    }

    public function get_dependent_settings()
    {
        return [];
    }

    public function editing()
    {
        return new ACP\Editing\Service\Basic(
            (new ACP\Editing\View\Text())->set_clear_button(true),
            new Editing\Storage\Profile($this->column->get_buddypress_field_id())
        );
    }

    public function sorting()
    {
        return new Sorting\Profile($this->column);
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedRawValue($this->column);
    }

    public function search()
    {
        return false;
    }

    public function get($key)
    {
        return $this->column->get_buddypress_field_option($key);
    }

    private function set_column(Column\Profile $column)
    {
        $this->column = $column;
    }

    public function get_filtering_date_setting(): ?string
    {
        return null;
    }

}