<?php

namespace ACA\Pods\Field\Pick;

use AC;
use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACP\Sorting\FormatValue\SerializedSettingFormatter;
use ACP\Sorting\FormatValue\SettingFormatter;
use ACP\Sorting\Model\MetaFormatFactory;

class Taxonomy extends Field\Pick
{

    public function get_value($id)
    {
        $values = [];

        foreach ($this->get_db_value($id) as $term_id) {
            $value = $this->column->get_formatted_value($term_id, $term_id);

            if ($value) {
                $values[] = $value;
            }
        }

        return implode(', ', $values);
    }

    public function sorting()
    {
        $setting = $this->column->get_setting(AC\Settings\Column\Term::NAME);

        $formatter = $this->is_multiple()
            ? new SerializedSettingFormatter(new SettingFormatter($setting))
            : new SettingFormatter($setting);

        return (new MetaFormatFactory())->create(
            $this->get_meta_type(),
            $this->get_meta_key(),
            $formatter,
            null,
            [
                'taxonomy' => $this->column->get_taxonomy(),
                'post_type' => $this->column->get_post_type(),
            ]
        );
    }

    public function get_raw_value($id)
    {
        return $this->get_ids_from_array(parent::get_raw_value($id), 'term_id');
    }

    public function editing()
    {
        return new Editing\Service\PickTaxonomy(
            new Editing\Storage\Field(
                $this->get_pod(),
                $this->get_field_name(),
                new Editing\Storage\Read\DbRaw($this->get_meta_key(), $this->get_meta_type())
            ),
            'multi' === $this->get_option('pick_format_type'),
            $this->get_taxonomy()
        );
    }

    public function get_taxonomy()
    {
        return $this->get('pick_val');
    }

    public function get_dependent_settings()
    {
        return [
            new AC\Settings\Column\Term($this->column),
        ];
    }

}