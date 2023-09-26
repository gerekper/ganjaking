<?php

declare(strict_types=1);

namespace ACA\MLA\ListScreen;

use AC;
use ACA\MLA\Column;
use ACA\MLA\Editing;
use ACA\MLA\Export;
use ACP;
use MLACore;

class MediaLibrary extends AC\ThirdParty\MediaLibraryAssistant\ListScreen\MediaLibrary implements ACP\Export\ListScreen,
                                                                                                  ACP\Editing\ListScreen
{

    public function export()
    {
        return new Export\Strategy($this);
    }

    public function editing()
    {
        return new Editing\Strategy(get_post_type_object('attachment'));
    }

    public function register_column_types(): void
    {
        parent::register_column_types();

        $columns = [
            Column\AltText::class,
            Column\AttachedTo::class,
            Column\Author::class,
            Column\BaseFile::class,
            Column\Caption::class,
            Column\CustomField::class,
            Column\Date::class,
            Column\Description::class,
            Column\Features::class,
            Column\FileUrl::class,
            Column\Galleries::class,
            Column\GalleriesMla::class,
            Column\IdParent::class,
            Column\Inserts::class,
            Column\MenuOrder::class,
            Column\MimeType::class,
            Column\Modified::class,
            Column\Name::class,
            Column\PostParent::class,
            Column\Taxonomy::class,
            Column\Title::class,
            Column\TitleName::class,
        ];

        $this->register_column_types_from_list($columns);

        // Custom Fields
        foreach (MLACore::mla_custom_field_support('custom_columns') as $type => $label) {
            $column = new Column\CustomField();
            $column->set_type($type)
                   ->set_label($label);

            $this->register_column_type($column);
        }

        // Taxonomies
        foreach (get_taxonomies(['show_ui' => true], 'objects') as $taxonomy) {
            if (MLACore::mla_taxonomy_support($taxonomy->name)) {
                $column = new Column\Taxonomy();
                $column->set_type('t_' . $taxonomy->name)
                       ->set_label($taxonomy->label);

                $this->register_column_type($column);
            }
        }
    }

}