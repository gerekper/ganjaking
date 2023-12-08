<?php

namespace ACA\WC\Settings;

use AC;
use WC_Product_Variation;

class Product extends AC\Settings\Column\Post
{

    public const GROUP_DEFAULT = 'default';
    public const GROUP_PRODUCT = 'product';
    public const GROUP_META = 'custom_field';

    public const TYPE_SKU = 'sku';
    public const TYPE_META = 'custom_field';

    protected function get_post_type()
    {
        return 'product';
    }

    protected function get_display_options()
    {
        $options = parent::get_display_options();

        unset($options[self::PROPERTY_FEATURED_IMAGE]);

        return [
            self::GROUP_DEFAULT => [
                'title'   => __('Post'),
                'options' => $options,
            ],
            self::GROUP_PRODUCT => [
                'title'   => __('Product', 'codepress-admin-columns'),
                'options' => [
                    self::TYPE_SKU                => __('SKU', 'woocommerce'),
                    self::PROPERTY_FEATURED_IMAGE => __('Image', 'woocommerce'),
                ],
            ],
            self::GROUP_META    => [
                'title'   => __('Custom Field', 'codepress-admin-columns'),
                'options' => [
                    self::TYPE_META => __('Custom Field', 'codepress-admin-columns'),
                ],
            ],
        ];
    }

    public function format($post_id, $original_value)
    {
        $value = $this->deferred_format($post_id, $original_value);

        // Check parent properties in case of Variable Products
        if ( ! $value) {
            $product = wc_get_product($original_value);

            if ($product instanceof WC_Product_Variation) {
                return $this->deferred_format($product->get_parent_id(), $product->get_parent_id());
            }
        }

        return $value;
    }

    /**
     * @param int   $id
     * @param mixed $original_value
     *
     * @return string|int
     */
    private function deferred_format($value, $original_value)
    {
        switch ($this->get_post_property_display()) {
            case self::TYPE_SKU :
                return esc_html(get_post_meta($original_value, '_sku', true));

            case self::TYPE_META :
                return get_post_meta($original_value, $this->column->get_setting(ProductMeta::NAME)->get_value(), true);

            default:
                return parent::format($value, $original_value);
        }
    }

    public function get_dependent_settings()
    {
        $settings = parent::get_dependent_settings();

        if (self::TYPE_META === $this->get_post_property_display()) {
            $settings[] = new ProductMeta($this->column);
            $settings[] = new AC\Settings\Column\BeforeAfter($this->column);
        }

        return $settings;
    }

}