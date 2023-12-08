<?php

namespace ACA\Pods\Field;

use AC;
use AC\Collection;
use AC\Meta\QueryMetaFactory;
use AC\MetaType;
use ACA\Pods\Editing;
use ACA\Pods\Export;
use ACA\Pods\Field;

class File extends Field
{

    public function get_value($id)
    {
        return $this->column->get_formatted_value(new Collection($this->get_raw_value($id)));
    }

    public function get_raw_value($id)
    {
        return (array)$this->get_db_value($id);
    }

    public function get_separator()
    {
        return ' ';
    }

    public function editing()
    {
        return new Editing\Service\FieldStorage(
            new Editing\Storage\File($this->get_pod(), $this->get_field_name(), $this->get_meta_type()),
            (new Editing\ViewFactory())->create_by_field($this)
        );
    }

    public function export()
    {
        return new Export\File($this->column);
    }

    private function create_query(): AC\Meta\Query
    {
        switch ($this->get_meta_type()) {
            case MetaType::POST:
                return (new QueryMetaFactory())->create_with_post_type(
                    $this->get_meta_key(),
                    $this->column()->get_post_type()
                );
            default:
                return (new QueryMetaFactory())->create($this->get_meta_key(), $this->get_meta_type());
        }
    }

    public function get_dependent_settings()
    {
        $settings = [];

        switch ($this->get_option('file_type')) {
            case 'images' :
            case 'images-any' :
            case 'any' :
                $settings[] = new AC\Settings\Column\Image($this->column);

                break;
        }

        return $settings;
    }

}