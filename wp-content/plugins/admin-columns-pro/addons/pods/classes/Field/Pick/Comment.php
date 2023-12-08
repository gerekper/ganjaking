<?php

namespace ACA\Pods\Field\Pick;

use AC\Settings;
use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACP;

class Comment extends Field\Pick
{

    public function get_value($id)
    {
        $ids = $this->get_raw_value($id);
        $values = [];

        if (empty($ids)) {
            return $this->column->get_empty_char();
        }

        foreach ($ids as $comment_id) {
            $values[] = $this->column->get_formatted_value($comment_id, $comment_id);
        }

        return implode('<br>', $values);
    }

    public function sorting()
    {
        return (new ACP\Sorting\Model\MetaFactory())->create($this->get_meta_type(), $this->get_meta_key());
    }

    public function get_raw_value($id)
    {
        return $this->get_ids_from_array(parent::get_raw_value($id), 'comment_ID');
    }

    public function editing()
    {
        return new Editing\Service\PickComments(
            new Editing\Storage\Field(
                $this->get_pod(),
                $this->get_field_name(),
                new Editing\Storage\Read\DbRaw($this->get_meta_key(), $this->get_meta_type())
            ),
            'multi' === $this->get_option('pick_format_type')
        );
    }

    public function get_dependent_settings()
    {
        return [
            new Settings\Column\Comment($this->column),
        ];
    }

}