<?php

namespace ACA\Pods\Field\Pick;

use AC\Collection;
use AC\Settings;
use AC\Settings\Column\Post;
use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACP;
use ACP\Editing\PaginatedOptions\Posts;
use ACP\Sorting\FormatValue\SerializedSettingFormatter;
use ACP\Sorting\FormatValue\SettingFormatter;
use ACP\Sorting\Model\MetaFormatFactory;
use ACP\Sorting\Model\MetaRelatedPostFactory;

class PostType extends Field\Pick
{

    public function get_value($id)
    {
        return $this->column->get_formatted_value(new Collection($this->get_raw_value($id)));
    }

    public function get_raw_value($id)
    {
        return $this->get_db_value($id);
    }

    public function editing()
    {
        $args = [];
        if ($this->get_option('pick_post_status')) {
            $args['post_status'] = (array)$this->get_option('pick_post_status');
        }

        $paginated = new Posts((array)$this->get('pick_val'), $args);
        $storage = new Editing\Storage\Field(
            $this->get_pod(),
            $this->get_field_name(),
            new Editing\Storage\Read\DbRaw($this->get_meta_key(), $this->get_meta_type())
        );

        return 'multi' === $this->get_option('pick_format_type')
            ? new ACP\Editing\Service\Posts(
                (new ACP\Editing\View\AjaxSelect())->set_clear_button(true)->set_multiple(true),
                $storage,
                $paginated
            )
            : new ACP\Editing\Service\Post(
                (new ACP\Editing\View\AjaxSelect())->set_clear_button(true),
                $storage,
                $paginated
            );
    }

    public function sorting()
    {
        $setting = $this->column->get_setting(Post::NAME);

        if ( ! $this->is_multiple()) {
            $model = (new MetaRelatedPostFactory())->create(
                $this->get_meta_type(),
                $setting->get_value(),
                $this->get_meta_key()
            );

            if ($model) {
                return $model;
            }
        }

        $formatter = $this->is_multiple()
            ? new SerializedSettingFormatter(new SettingFormatter($setting))
            : new SettingFormatter($setting);

        return (new MetaFormatFactory())->create($this->get_meta_type(), $this->get_meta_key(), $formatter, null, [
            'taxonomy'  => $this->column->get_taxonomy(),
            'post_type' => $this->column->get_post_type(),
        ]);
    }

    public function get_dependent_settings()
    {
        return [
            new Settings\Column\Post($this->column),
        ];
    }

}
