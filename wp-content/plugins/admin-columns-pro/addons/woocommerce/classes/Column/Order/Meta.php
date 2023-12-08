<?php

namespace ACA\WC\Column\Order;

use AC;
use AC\Settings\Column\DateFormat;
use ACA\WC;
use ACA\WC\Editing;
use ACA\WC\Settings;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter\DateFormatter\FormatFormatter;
use ACP\Export;
use ACP\Search;
use ACP\Settings\Column\CustomFieldType;
use ACP\Sorting\Type\DataType;

class Meta extends AC\Column implements Search\Searchable, ACP\Editing\Editable, ACP\Export\Exportable,
                                        ACP\Sorting\Sortable, ACP\ConditionalFormat\Formattable
{

    public function __construct()
    {
        $this->set_type('column-order_meta')
             ->set_label(__('Order Meta', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_raw_value($id)
    {
        $order = wc_get_order($id);

        return $order ? $order->get_meta($this->get_meta_key()) : false;
    }

    public function get_meta_key(): string
    {
        $setting = $this->get_setting(Settings\Order\Meta::KEY);

        return $setting instanceof Settings\Order\Meta
            ? $setting->get_meta_field()
            : '';
    }

    public function get_field_type(): string
    {
        $setting = $this->get_setting(CustomFieldType::NAME);

        return $setting instanceof CustomFieldType
            ? (string)$setting->get_field_type()
            : '';
    }

    protected function register_settings()
    {
        parent::register_settings();

        $this->add_setting(new Settings\Order\Meta($this));
    }

    public function get_date_save_format(): string
    {
        $setting = $this->get_setting('date_save_format');

        return $setting instanceof DateFormat
            ? $setting->get_date_save_format()
            : DateFormat::FORMAT_DATE;
    }

    public function editing()
    {
        return (new Editing\Order\OrderMetaFactory())->create($this);
    }

    public function search()
    {
        switch ($this->get_field_type()) {
            case CustomFieldType::TYPE_BOOLEAN:
                return new Search\Comparison\Meta\Checkmark($this->get_meta_key());

            case CustomFieldType::TYPE_NON_EMPTY :
                return new Search\Comparison\Meta\EmptyNotEmpty($this->get_meta_key());

            case CustomFieldType::TYPE_IMAGE :
            case CustomFieldType::TYPE_MEDIA :
                return new Search\Comparison\Meta\Post($this->get_meta_key(), ['attachment']);

            case CustomFieldType::TYPE_NUMERIC :
                return new Search\Comparison\Meta\Number($this->get_meta_key());

            case CustomFieldType::TYPE_USER :
                return new Search\Comparison\Meta\User($this->get_meta_key());

            case CustomFieldType::TYPE_POST :
            case CustomFieldType::TYPE_COLOR :
            case CustomFieldType::TYPE_TEXT :
            case CustomFieldType::TYPE_URL :
                return new Search\Comparison\Meta\Text($this->get_meta_key());
            case CustomFieldType::TYPE_DATE :
                switch ($this->get_date_save_format()) {
                    case 'Y-m-d H:i:s':
                    case 'Y-m-d':
                        return new WC\Search\OrderMeta\IsoDate($this->get_meta_key());
                    case 'U':
                        return new WC\Search\OrderMeta\Timestamp($this->get_meta_key());
                    default:
                        return null;
                }
            case CustomFieldType::TYPE_ARRAY:
            case CustomFieldType::TYPE_COUNT:
                return null;
        }

        return new Search\Comparison\Meta\Text($this->get_meta_key());
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
            default :
                return new Export\Model\RawValue($this);
        }
    }

    public function sorting()
    {
        switch ($this->get_field_type()) {
            case CustomFieldType::TYPE_POST :
            case CustomFieldType::TYPE_USER :
            case CustomFieldType::TYPE_COUNT :
                return null;
            case CustomFieldType::TYPE_NUMERIC :
                return new WC\Sorting\Order\OrderMeta($this->get_meta_key(), new DataType(DataType::NUMERIC));
            case CustomFieldType::TYPE_DATE :
                switch ($this->get_date_save_format()) {
                    case DateFormat::FORMAT_UNIX_TIMESTAMP:
                        return new WC\Sorting\Order\OrderMeta($this->get_meta_key(), new DataType(DataType::NUMERIC));
                    default:
                        return new WC\Sorting\Order\OrderMeta($this->get_meta_key(), new DataType(DataType::DATE));
                }

            default :
                return new WC\Sorting\Order\OrderMeta($this->get_meta_key());
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
            case CustomFieldType::TYPE_COUNT :
            case CustomFieldType::TYPE_NUMERIC :
                return new FormattableConfig(new ACP\ConditionalFormat\Formatter\IntegerFormatter());
            case CustomFieldType::TYPE_POST :
            case CustomFieldType::TYPE_USER :

            default:
                return new FormattableConfig();
        }
    }
}