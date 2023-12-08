<?php

namespace ACA\WC\Column\ProductVariation;

use AC;
use AC\Column;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACP;

class ParentProduct extends Column implements ACP\Search\Searchable
{

    public function __construct()
    {
        $this->set_label(__('Parent Product', 'codepress-admin-columns'))
             ->set_type('column-parent_products')
             ->set_group('woocommerce');
    }

    protected function register_settings()
    {
        parent::register_settings();

        $this->add_setting(new Settings\Product($this));
    }

    private function get_display_property(): ?string
    {
        $setting = $this->get_setting('post');

        return $setting instanceof Settings\Product
            ? $setting->get_value()
            : null;
    }

    public function get_value($id)
    {
        $value = $this->get_formatted_value(
            $this->get_raw_value($id),
            $this->get_raw_value($id)
        ) ?: $this->get_empty_char();

        if ($value instanceof AC\Collection) {
            $value = $value->filter()->implode($this->get_separator());
        }

        if ( ! $this->is_original() && ac_helper()->string->is_empty($value)) {
            $value = $this->get_empty_char();
        }

        return (string)$value;
    }

    public function get_raw_value($id)
    {
        return get_post($id)->post_parent;
    }

    public function search()
    {
        switch ($this->get_display_property()) {
            case Settings\Product::PROPERTY_ID:
                return new Search\ProductVariation\Parent\Id();

            case Settings\Product::PROPERTY_TITLE:
                return new Search\ProductVariation\Parent\Title();

            case Settings\Product::PROPERTY_STATUS:
                return new Search\ProductVariation\Parent\Status();

            case Settings\Product::TYPE_SKU:
                return new Search\ProductVariation\Parent\MetaText('_sku');

            case Settings\Product::TYPE_META:
                return $this->create_meta_comparison();
            default:
                return null;
        }
    }

    private function create_meta_comparison()
    {
        $setting = $this->get_setting('field_type');
        $setting_field = $this->get_setting('custom_field');

        if ( ! $setting instanceof AC\Settings\Column\CustomFieldType || ! $setting_field instanceof AC\Settings\Column\CustomField) {
            return null;
        }

        $type = $setting->get_field_type();

        switch ($type) {
            case AC\Settings\Column\CustomFieldType::TYPE_TEXT:
            case AC\Settings\Column\CustomFieldType::TYPE_COLOR:
            case AC\Settings\Column\CustomFieldType::TYPE_URL:
                return new Search\ProductVariation\Parent\MetaText($setting_field->get_field());
            case AC\Settings\Column\CustomFieldType::TYPE_NUMERIC:
                return new Search\ProductVariation\Parent\MetaNumber($setting_field->get_field());
            case AC\Settings\Column\CustomFieldType::TYPE_USER:
                return new Search\ProductVariation\Parent\MetaUser($setting_field->get_field());
            case AC\Settings\Column\CustomFieldType::TYPE_POST:
                return new Search\ProductVariation\Parent\MetaPost($setting_field->get_field());
            case AC\Settings\Column\CustomFieldType::TYPE_MEDIA:
            case AC\Settings\Column\CustomFieldType::TYPE_IMAGE:
                return new Search\ProductVariation\Parent\MetaPost($setting_field->get_field(), ['attachment']);
            default:
                return null;
        }
    }
}