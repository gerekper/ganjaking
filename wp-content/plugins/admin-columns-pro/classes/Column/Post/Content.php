<?php

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Editing\Settings\EditableType;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class Content extends AC\Column\Post\Content
    implements Editing\Editable, Sorting\Sortable, Export\Exportable, Search\Searchable, ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function register_settings()
    {
        parent::register_settings();

        $this->add_setting(
            (new Editing\Settings\Factory\EditableType(
                $this, Editing\Settings\Factory\EditableType::TYPE_CONTENT
            ))->create()
        );
    }

    public function editing()
    {
        return EditableType\Content::TYPE_WYSIWYG === $this->get_inline_editable_type()
            ? new Editing\Service\Post\ContentWysiwyg()
            : new Editing\Service\Post\Content();
    }

    public function sorting()
    {
        return new Sorting\Model\Post\PostContent();
    }

    public function export()
    {
        return new Export\Model\RawValue($this);
    }

    public function search()
    {
        return new Search\Comparison\Post\Content();
    }

    private function get_inline_editable_type()
    {
        $setting = $this->get_setting(Editing\Settings::NAME);

        if ( ! $setting instanceof Editing\Settings) {
            return null;
        }

        $section = $setting->get_section(EditableType\Content::NAME);

        return $section instanceof EditableType\Content
            ? $section->get_editable_type()
            : null;
    }

}