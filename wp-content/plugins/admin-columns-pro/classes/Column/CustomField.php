<?php

namespace ACP\Column;

use AC;
use AC\Meta\QueryMetaFactory;
use AC\MetaType;
use AC\Settings\Column\CustomFieldType;
use AC\Settings\Column\DateFormat;
use AC\Settings\Column\Post;
use ACP\ApplyFilter\CustomField\StoredDateFormat;
use ACP\ConditionalFormat;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter\DateFormatter\FormatFormatter;
use ACP\ConditionalFormat\Formatter\IntegerFormatter;
use ACP\Editing;
use ACP\Editing\Settings\EditableType;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;
use ACP\Search\Comparison\Meta\DateFactory;
use ACP\Settings;
use ACP\Settings\Column\User;
use ACP\Sorting;
use ACP\Sorting\Model\MetaCountFactory;
use ACP\Sorting\Model\MetaFactory;
use ACP\Sorting\Model\MetaFormatFactory;
use ACP\Sorting\Model\MetaRelatedPostFactory;
use ACP\Sorting\Model\MetaRelatedUserFactory;
use ACP\Sorting\Type\DataType;

class CustomField extends AC\Column\CustomField
    implements Sorting\Sortable, Editing\Editable, Filtering\FilterableDateSetting,
               Export\Exportable, Search\Searchable,
               ConditionalFormat\Formattable
{

    use Filtering\FilteringDateSettingTrait;

    public function sorting()
    {
        $type = $this->get_field_type();

        if ($type === null) {
            return null;
        }

        switch ($type) {
            case CustomFieldType::TYPE_ARRAY :
                return null;
            case CustomFieldType::TYPE_BOOLEAN :
                return (new MetaFactory())->create(
                    $this->get_meta_type(),
                    $this->get_meta_key(),
                    new DataType(DataType::NUMERIC)
                );
            case CustomFieldType::TYPE_NUMERIC :
                // $numeric_type can be `numeric` or `decimal`
                $numeric_type = apply_filters('acp/sorting/custom_field/numeric_type', DataType::NUMERIC, $this);

                return (new MetaFactory())->create(
                    $this->get_meta_type(),
                    $this->get_meta_key(),
                    new DataType($numeric_type)
                );
            case CustomFieldType::TYPE_DATE :
                switch ($this->get_date_save_format()) {
                    case DateFormat::FORMAT_DATE :
                        $data_type = DataType::DATE;
                        break;
                    case DateFormat::FORMAT_UNIX_TIMESTAMP :
                        $data_type = DataType::NUMERIC;
                        break;
                    default :
                        $data_type = DataType::DATETIME;
                }

                // $date_type can be `string`, `numeric`, `date` or `datetime`
                $data_type = apply_filters('acp/sorting/custom_field/date_type', $data_type, $this);

                return (new MetaFactory())->create(
                    $this->get_meta_type(),
                    $this->get_meta_key(),
                    new DataType($data_type)
                );
            case CustomFieldType::TYPE_POST :
                // only works on single post ID's
                $model = (new MetaRelatedPostFactory())->create(
                    $this->get_meta_type(),
                    $this->get_setting(Post::NAME)->get_value(),
                    $this->get_meta_key()
                );

                if ( ! $model) {
                    $model = (new MetaFormatFactory())->create(
                        $this->get_meta_type(),
                        $this->get_meta_key(),
                        new Sorting\FormatValue\SettingFormatter($this->get_setting(Post::NAME)),
                        null,
                        [
                            'taxonomy' => $this->get_taxonomy(),
                        ]
                    );
                }

                return $model;
            case CustomFieldType::TYPE_USER :
                // only works on single user ID's
                $model = (new MetaRelatedUserFactory())->create(
                    $this->get_meta_type(),
                    $this->get_setting(User::NAME)->get_value(),
                    $this->get_meta_key()
                );

                if ( ! $model) {
                    $model = (new MetaFormatFactory())->create(
                        $this->get_meta_type(),
                        $this->get_meta_key(),
                        new Sorting\FormatValue\SettingFormatter($this->get_setting(User::NAME)),
                        null,
                        [
                            'taxonomy' => $this->get_taxonomy(),
                        ]
                    );
                }

                return $model;
            case CustomFieldType::TYPE_COUNT :
                return (new MetaCountFactory())->create($this->get_meta_type(), $this->get_meta_key());
            case CustomFieldType::TYPE_TEXT :
            case CustomFieldType::TYPE_NON_EMPTY :
            case CustomFieldType::TYPE_IMAGE :
            case CustomFieldType::TYPE_MEDIA :
            case CustomFieldType::TYPE_URL :
            case CustomFieldType::TYPE_COLOR :
            default :
                return (new MetaFactory())->create($this->get_meta_type(), $this->get_meta_key());
        }
    }

    public function editing()
    {
        return (new Editing\Service\CustomFieldServiceFactory())->create($this) ?: false;
    }

    private function create_query(): AC\Meta\Query
    {
        switch ($this->get_meta_type()) {
            case MetaType::POST:
                return (new QueryMetaFactory())->create_with_post_type($this->get_meta_key(), $this->get_post_type());
            default:
                return (new QueryMetaFactory())->create($this->get_meta_key(), $this->get_meta_type());
        }
    }

    private function get_date_save_format(): string
    {
        $setting = $this->get_setting('date_save_format');

        if ( ! $setting instanceof DateFormat) {
            return 'Y-m-d';
        }

        return (new StoredDateFormat($this))->apply_filters(
            $setting->get_date_save_format()
        );
    }

    public function get_select_options(): array
    {
        $setting = $this->get_setting('select_options');

        return $setting instanceof Settings\Column\SelectOptions
            ? $setting->get_options()
            : [];
    }

    public function search()
    {
        switch ($this->get_field_type()) {
            case CustomFieldType::TYPE_ARRAY :
                return new Search\Comparison\Meta\Serialized($this->get_meta_key());
            case CustomFieldType::TYPE_BOOLEAN :
                return new Search\Comparison\Meta\Checkmark($this->get_meta_key());
            case CustomFieldType::TYPE_COUNT :
                return null;
            case CustomFieldType::TYPE_DATE :
                $query = $this->create_query();

                return DateFactory::create(
                    $this->get_date_save_format(),
                    $this->get_meta_key(),
                    $query
                );
            case CustomFieldType::TYPE_NON_EMPTY :
                return new Search\Comparison\Meta\EmptyNotEmpty($this->get_meta_key());
            case CustomFieldType::TYPE_IMAGE :
            case CustomFieldType::TYPE_MEDIA :
                return new Search\Comparison\Meta\Media($this->get_meta_key(), $this->create_query());
            case CustomFieldType::TYPE_NUMERIC :
                return new Search\Comparison\Meta\Number($this->get_meta_key());
            case CustomFieldType::TYPE_POST :
                return new Search\Comparison\Meta\Posts(
                    $this->get_meta_key(),
                    [],
                    [],
                    $this->create_query(),
                    Search\Value::INT
                );
            case Settings\Column\CustomFieldType::TYPE_SELECT:
                return new Search\Comparison\Meta\Select($this->get_meta_key(), $this->get_select_options());
            case CustomFieldType::TYPE_USER :
                return new Search\Comparison\Meta\User($this->get_meta_key());
            case CustomFieldType::TYPE_COLOR :
            case CustomFieldType::TYPE_TEXT :
            case CustomFieldType::TYPE_URL :
            default :
                return new Search\Comparison\Meta\SearchableText($this->get_meta_key(), $this->create_query());
        }
    }

    public function conditional_format(): ?FormattableConfig
    {
        switch ($this->get_field_type()) {
            // Unsupported fields
            case CustomFieldType::TYPE_NON_EMPTY:
            case CustomFieldType::TYPE_BOOLEAN:
            case CustomFieldType::TYPE_MEDIA:
            case CustomFieldType::TYPE_COLOR:
            case CustomFieldType::TYPE_IMAGE:
                return null;

            case CustomFieldType::TYPE_DATE :
                return new FormattableConfig(
                    new FormatFormatter(
                        $this->get_date_save_format()
                    )
                );

            case CustomFieldType::TYPE_NUMERIC :
            case CustomFieldType::TYPE_COUNT :
                return new FormattableConfig(new IntegerFormatter());

            default :
                return new FormattableConfig();
        }
    }

    public function export()
    {
        switch ($this->get_field_type()) {
            case CustomFieldType::TYPE_ARRAY :
            case CustomFieldType::TYPE_COUNT :
            case CustomFieldType::TYPE_NON_EMPTY :
                return new Export\Model\Value($this);
            case CustomFieldType::TYPE_DATE :
                return new Export\Model\CustomField\Date($this);
            case CustomFieldType::TYPE_IMAGE :
            case CustomFieldType::TYPE_MEDIA :
                return new Export\Model\CustomField\Image($this);
            case CustomFieldType::TYPE_POST :
            case CustomFieldType::TYPE_USER :
                return new Export\Model\StrippedValue($this);
            case CustomFieldType::TYPE_BOOLEAN :
            case CustomFieldType::TYPE_COLOR :
            case CustomFieldType::TYPE_TEXT :
            case CustomFieldType::TYPE_URL :
            case CustomFieldType::TYPE_NUMERIC :
            default :
                return new Export\Model\RawValue($this);
        }
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\Column\CustomField($this))
             ->add_setting(new AC\Settings\Column\BeforeAfter($this));

        $this->register_editing_setting();
        $this->register_filtering_setting();
    }

    private function register_filtering_setting(): void
    {
        switch ($this->get_field_type()) {
            case CustomFieldType::TYPE_DATE :
                $this->add_setting(new Filtering\Settings\Date($this));
                break;
        }
    }

    private function register_editing_setting(): void
    {
        $unsupported_field_types = (new Editing\Service\CustomFieldServiceFactory())->unsupported_field_types();

        if (in_array($this->get_field_type(), $unsupported_field_types, true)) {
            return;
        }

        $setting = new Editing\Settings\CustomField($this);

        if (in_array($this->get_field_type(), [CustomFieldType::TYPE_DEFAULT, CustomFieldType::TYPE_TEXT], true)) {
            $section = new EditableType\Text($this, EditableType\Text::TYPE_TEXT);
            $section->set_values($this->get_options());

            $setting->add_section($section);
        }

        $this->add_setting($setting);
    }

}