<?php

namespace ACP\ListScreen;

use AC;
use ACP\Column;
use ACP\Editing;
use ACP\Editing\BulkDelete\Deletable;
use ACP\Export;
use ACP\Sorting;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Strategy;

class Media extends AC\ListScreen\Media
    implements Sorting\ListScreen, Editing\ListScreen, Export\ListScreen,
               Editing\BulkDelete\ListScreen
{

    public function sorting(AbstractModel $model): Strategy
    {
        return new Sorting\Strategy\Media($model);
    }

    public function deletable(): Deletable
    {
        return new Deletable\Post(get_post_type_object($this->get_post_type()));
    }

    public function editing()
    {
        return new Editing\Strategy\Post(get_post_type_object($this->get_post_type()));
    }

    public function export()
    {
        return new Export\Strategy\Post($this);
    }

    protected function register_column_types(): void
    {
        parent::register_column_types();

        $this->register_column_types_from_list([
            Column\CustomField::class,
            Column\Actions::class,
            Column\Post\TitleRaw::class,
            Column\Post\Modified::class,
            Column\Post\LastModifiedAuthor::class,
            Column\Media\Album::class,
            Column\Media\AlternateText::class,
            Column\Media\Artist::class,
            Column\Media\AspectRatio::class,
            Column\Media\Author::class,
            Column\Media\AuthorName::class,
            Column\Media\AvailableSizes::class,
            Column\Media\Caption::class,
            Column\Media\Comments::class,
            Column\Media\Date::class,
            Column\Media\Description::class,
            Column\Media\Dimensions::class,
            Column\Media\ExifData::class,
            Column\Media\FileMetaAudio::class,
            Column\Media\FileMetaVideo::class,
            Column\Media\FileName::class,
            Column\Media\FileSize::class,
            Column\Media\Height::class,
            Column\Media\ID::class,
            Column\Media\Orientation::class,
            Column\Media\MediaParent::class,
            Column\Media\Menu::class,
            Column\Media\MimeType::class,
            Column\Media\Permalink::class,
            Column\Media\PostType::class,
            Column\Media\Taxonomy::class,
            Column\Media\Title::class,
            Column\Media\UsedAsFeaturedImage::class,
            Column\Media\Width::class,
        ]);
    }

}