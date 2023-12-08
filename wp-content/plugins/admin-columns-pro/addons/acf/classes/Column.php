<?php

namespace ACA\ACF;

use AC;
use AC\MetaType;
use ACA\ACF;
use ACP;
use ACP\Export\Exportable;
use InvalidArgumentException;

class Column extends AC\Column\Meta
    implements Exportable, ACF\Search\SearchFactoryAware, ACF\Sorting\SortingFactoryAware,
               ACF\Editing\EditingFactoryAware, ACF\ConditionalFormatting\FormattableFactoryAware,
               ACP\Filtering\FilterableDateSetting
{

    use ACF\Search\SearchableTrait;
    use ACF\Sorting\SortableTrait;
    use ACF\Editing\EditableTrait;
    use ACF\ConditionalFormatting\FormattableTrait;

    /**
     * @var string
     */
    protected $field_hash;

    /**
     * @var string
     */
    protected $meta_key;

    /**
     * @var Field
     */
    protected $field;

    /**
     * @var string
     */
    private $field_type;

    public function __construct()
    {
        $this->set_label('ACF Column')
             ->set_group(ColumnGroup::SLUG);
    }

    public function set_config(array $config): void
    {
        $this->field_hash = $config[Configurable::FIELD_HASH];
        $this->meta_key = $config[Configurable::META_KEY];
        $this->field = $config[Configurable::FIELD];
        $this->field_type = $config[Configurable::FIELD_TYPE];

        $this->set_label($this->field->get_settings()['label'] ?: $this->field->get_settings()['name']);

        $this->validate();
    }

    private function validate(): void
    {
        if ( ! is_string($this->field_hash)) {
            throw new InvalidArgumentException('Invalid field hash');
        }
        if ( ! is_string($this->meta_key)) {
            throw new InvalidArgumentException('Invalid meta key');
        }
        if ( ! $this->field instanceof Field) {
            throw new InvalidArgumentException('Invalid field');
        }
        if ( ! is_string($this->field_type)) {
            throw new InvalidArgumentException('Invalid field type');
        }
    }

    public function get_value($id)
    {
        $factory = new Value\FormatterFactory();

        return $factory->create($this, $this->get_field())->format(
            $this->get_raw_value($id),
            $id
        );
    }

    public function get_raw_value($id)
    {
        return get_field($this->meta_key, $this->get_formatted_id($id), false);
    }

    public function get_formatted_id($id)
    {
        switch ($this->get_meta_type()) {
            case MetaType::USER:
                $prefix = 'user_';
                break;
            case MetaType::COMMENT:
                $prefix = 'comment_';
                break;
            case MetaType::SITE:
                $prefix = 'site_';
                break;
            case MetaType::TERM:
                $prefix = $this->get_taxonomy() . '_';
                break;
            default:
                $prefix = '';
        }

        return $prefix . $id;
    }

    protected function register_settings()
    {
        $setting_factory = new ACF\Settings\SettingFactory();

        $settings = $setting_factory->create(
            $this->get_field(),
            $this
        );

        array_map([$this, 'add_setting'], $settings);
    }

    public function export()
    {
        return (new Export\ModelFactory())->create($this->get_field_type(), $this);
    }

    public function get_meta_key()
    {
        return $this->meta_key;
    }

    public function get_field_hash()
    {
        return $this->field_hash;
    }

    public function get_field_type()
    {
        return $this->field_type;
    }

    /**
     * @return Field
     */
    public function get_field()
    {
        return $this->field;
    }

    /**
     * @return array
     * @deprecated 6.0
     */
    public function get_acf_field()
    {
        _deprecated_function(__METHOD__, '6.0');

        return $this->field->get_settings();
    }

    /**
     * @param string $name
     *
     * @return string
     * @deprecated 6.0
     */
    public function get_acf_field_option($name)
    {
        _deprecated_function(__METHOD__, '6.0');

        $settings = $this->field->get_settings();

        return array_key_exists($name, $settings) ? $settings[$name] : '';
    }

    public function get_filtering_date_setting(): ?string
    {
        return $this->options['filter_format'] ?? null;
    }

}