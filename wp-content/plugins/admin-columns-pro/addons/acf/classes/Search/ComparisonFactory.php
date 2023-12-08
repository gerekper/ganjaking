<?php

namespace ACA\ACF\Search;

use AC\Meta\Query;
use AC\Meta\QueryMetaFactory;
use ACA\ACF\Column;
use ACA\ACF\Field;
use ACA\ACF\FieldType;
use ACA\ACF\Search;
use ACP;
use ACP\Search\Comparison\Meta\Post;
use ACP\Search\Comparison\Meta\Posts;

class ComparisonFactory implements SearchComparisonFactory
{

    protected function create_query(Column $column): Query
    {
        return (new QueryMetaFactory())->create_by_meta_column($column);
    }

    public function create(Field $field, string $meta_key, string $meta_type, Column $column): ?ACP\Search\Comparison
    {
        switch ($field->get_type()) {
            case FieldType::TYPE_BOOLEAN:
                return new ACP\Search\Comparison\Meta\Checkmark($meta_key);

            case FieldType::TYPE_BUTTON_GROUP:
                return new Search\Comparison\Select(
                    $meta_key,
                    $field instanceof Field\Choices ? $field->get_choices() : []
                );

            case FieldType::TYPE_CHECKBOX:
                return new Search\Comparison\MultiSelect(
                    $meta_key,
                    $field instanceof Field\Choices ? $field->get_choices() : []
                );

            case FieldType::TYPE_DATE_PICKER:
                return new Search\Comparison\DatePicker($meta_key, $this->create_query($column));

            case FieldType::TYPE_DATE_TIME_PICKER:
                return new ACP\Search\Comparison\Meta\DateTime\ISO($meta_key, $this->create_query($column));

            case FieldType::TYPE_TEXT:
            case FieldType::TYPE_EMAIL;
            case FieldType::TYPE_COLOR_PICKER:
            case FieldType::TYPE_URL:
                return new ACP\Search\Comparison\Meta\SearchableText($meta_key, $this->create_query($column));

            case FieldType::TYPE_PASSWORD:
            case FieldType::TYPE_TEXTAREA:
            case FieldType::TYPE_WYSIWYG:
            case FieldType::TYPE_TIME_PICKER:
            case FieldType::TYPE_OEMBED:
                return new ACP\Search\Comparison\Meta\Text($meta_key);

            case FieldType::TYPE_IMAGE:
                $query_factory = new QueryMetaFactory();

                if ($field instanceof Field\PostTypeFilterable) {
                    $query = $query_factory->create_with_post_types($meta_key, $field->get_post_types());
                } else {
                    $query = $query_factory->create($meta_key, $meta_type);
                }
      
                return new ACP\Search\Comparison\Meta\Image($meta_key, $query);

            case FieldType::TYPE_GALLERY:
                return new ACP\Search\Comparison\Meta\EmptyNotEmpty($meta_key);

            case FieldType::TYPE_NUMBER:
            case FieldType::TYPE_RANGE:
                return new ACP\Search\Comparison\Meta\Decimal($meta_key);

            case FieldType::TYPE_SELECT:
            case FieldType::TYPE_RADIO:
                $choices = $field instanceof Field\Choices ? $field->get_choices() : [];

                return $field instanceof Field\Multiple && $field->is_multiple()
                    ? new Search\Comparison\MultiSelect($meta_key, $choices)
                    : new Search\Comparison\Select($meta_key, $choices);

            case FieldType::TYPE_FILE:
                $query_factory = new QueryMetaFactory();

                if ($field instanceof Field\PostTypeFilterable) {
                    $query = $query_factory->create_with_post_type($meta_key, $field->get_post_types());
                } else {
                    $query = $query_factory->create($meta_key, $meta_type);
                }

                return new ACP\Search\Comparison\Meta\Media($meta_key, $query);

            case FieldType::TYPE_RELATIONSHIP:
            case FieldType::TYPE_POST:
            case FieldType::TYPE_PAGE_LINK:
                $post_types = $field instanceof Field\PostTypeFilterable
                    ? $field->get_post_types()
                    : [];

                $terms = $field instanceof Field\TaxonomyFilterable
                    ? $field->get_taxonomies()
                    : [];

                return $field instanceof Field\Multiple && $field->is_multiple()
                    ? new Posts(
                        $meta_key,
                        $post_types,
                        $terms,
                        $this->create_query($column)
                    )
                    : new Post(
                        $meta_key,
                        $post_types,
                        $terms,
                        null,
                        $this->create_query($column)
                    );

            case FieldType::TYPE_TAXONOMY:
                if ( ! $field instanceof Field\Type\Taxonomy) {
                    return null;
                }

                if ($field->uses_native_term_relation()) {
                    return new ACP\Search\Comparison\Post\Taxonomy($field->get_taxonomy());
                }

                return $field->is_multiple()
                    ? new Search\Comparison\Taxonomies($meta_key, $field->get_taxonomy())
                    : new Search\Comparison\Taxonomy($meta_key, $field->get_taxonomy());

            case FieldType::TYPE_LINK:
                return new Search\Comparison\Link($meta_key);

            case FieldType::TYPE_USER:
                return $field instanceof Field\Multiple && $field->is_multiple()
                    ? new Search\Comparison\Users($meta_key, $this->create_query($column))
                    : new Search\Comparison\User($meta_key, $this->create_query($column));

            default:
                return null;
        }
    }

}