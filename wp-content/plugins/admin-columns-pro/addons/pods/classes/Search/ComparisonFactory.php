<?php

namespace ACA\Pods\Search;

use AC\Meta\Query;
use AC\Meta\QueryMetaFactory;
use AC\MetaType;
use ACA\Pods\Column;
use ACA\Pods\Field;
use ACA\Pods\Search;
use ACP;

class ComparisonFactory
{

    private function create_query(Column $column): Query
    {
        switch ($column->get_meta_type()) {
            case MetaType::POST:
                return (new QueryMetaFactory())->create_with_post_type(
                    $column->get_meta_key(),
                    $column->get_post_type()
                );
            default:
                return (new QueryMetaFactory())->create($column->get_meta_key(), $column->get_meta_type());
        }
    }

    public function create(Field $field, Column $column): ?ACP\Search\Comparison
    {
        switch (true) {
            case $field instanceof Field\Boolean:
                return new ACP\Search\Comparison\Meta\Checkmark($column->get_meta_key());

            case $field instanceof Field\Code:
            case $field instanceof Field\Password:
            case $field instanceof Field\Paragraph:
            case $field instanceof Field\Phone:
            case $field instanceof Field\Time:
            case $field instanceof Field\Wysiwyg:
                return new ACP\Search\Comparison\Meta\Text(
                    $column->get_meta_key()
                );

            case $field instanceof Field\Color:
            case $field instanceof Field\Email:
            case $field instanceof Field\Text:
            case $field instanceof Field\Website:

                return new ACP\Search\Comparison\Meta\SearchableText(
                    $column->get_meta_key(),
                    $this->create_query($column)
                );

            case $field instanceof Field\Currency:
            case $field instanceof Field\Number:
                return new ACP\Search\Comparison\Meta\Number($column->get_meta_key());

            case $field instanceof Field\Date:
                return new ACP\Search\Comparison\Meta\Date(
                    $column->get_meta_key(), $this->create_query($column)
                );
            case $field instanceof Field\Datetime:
                return new ACP\Search\Comparison\Meta\DateTime\ISO(
                    $column->get_meta_key(), $this->create_query($column)
                );

            case $field instanceof Field\File:
            case $field instanceof Field\Pick\Media:
                return new ACP\Search\Comparison\Meta\Media($column->get_meta_key(), $this->create_query($column));

            case $field instanceof Field\Pick\Capability:
            case $field instanceof Field\Pick\Country:
            case $field instanceof Field\Pick\CustomSimple:
            case $field instanceof Field\Pick\DaysOfWeek:
            case $field instanceof Field\Pick\ImageSize:
            case $field instanceof Field\Pick\MonthsOfYear:
            case $field instanceof Field\Pick\NavMenu:
            case $field instanceof Field\Pick\PostFormat:
            case $field instanceof Field\Pick\PostStatus:
            case $field instanceof Field\Pick\Role:
            case $field instanceof Field\Pick\UsState:
                return new Search\Pick($column->get_meta_key(), $field->get_options());

            case $field instanceof Field\Pick\Comment:
                return new Search\PickComment($column->get_meta_key());

            case $field instanceof Field\Pick\PostType:
                return new Search\PickPost(
                    $column->get_meta_key(),
                    (array)$field->get('pick_val'),
                    $this->create_query($column)
                );

            case $field instanceof Field\Pick\Taxonomy:
                return new Search\PickTaxonomy($column->get_meta_key(), (array)$field->get_taxonomy());

            case $field instanceof Field\Pick\User:
                return new Search\PickUser(
                    $column->get_meta_key(),
                    $field->get_user_roles(),
                    $this->create_query($column)
                );
        }

        return null;
    }
}