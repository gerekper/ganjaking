<?php

namespace ACA\JetEngine\Column\Meta;

use AC\Settings;
use ACA\JetEngine\Column;
use ACA\JetEngine\Editing\EditableTrait;
use ACA\JetEngine\Field;
use ACA\JetEngine\Search\SearchableTrait;
use ACA\JetEngine\Sorting;
use ACA\JetEngine\Value\DefaultValueFormatterTrait;
use ACP;

/**
 * @property Field\Type\Posts $field
 */
class Post extends Column\Meta
    implements ACP\Search\Searchable, ACP\Editing\Editable, ACP\Sorting\Sortable, ACP\ConditionalFormat\Formattable
{

    use SearchableTrait,
        EditableTrait,
        DefaultValueFormatterTrait,
        ACP\ConditionalFormat\FilteredHtmlFormatTrait;

    protected function register_settings()
    {
        $this->add_setting(new Settings\Column\Post($this));
    }

    /**
     * @return Settings\Column\Post
     */
    private function get_post_setting()
    {
        $setting = $this->get_setting(Settings\Column\Post::NAME);

        return $setting instanceof Settings\Column\Post
            ? $setting
            : null;
    }

    public function sorting()
    {
        return (new Sorting\ModelFactory\Post())->create(
            $this->get_meta_type(),
            $this->get_meta_key(),
            $this->field->is_multiple(),
            $this->get_post_setting(),
            [
                'taxonomy' => $this->get_taxonomy(),
                'post_type' => $this->get_post_type(),
            ]
        );
    }

}