<?php

namespace ACA\WC\Settings\Order;

use AC;
use AC\View;
use ACP;

class Meta extends AC\Settings\Column
{

    public const KEY = 'meta_field';
    public const CACHE_GROUP = 'ac_settings_order_meta';
    public const CACHE_KEY = 'order_meta_fields';

    private $meta_field;

    protected function define_options()
    {
        return [self::KEY];
    }

    public function create_view()
    {
        $view = new View([
            'label'   => __('Field', 'codepress-admin-columns'),
            'setting' => $this->get_settings_field_select(),
        ]);

        return $view;
    }

    private function get_cache()
    {
        return wp_cache_get(self::CACHE_KEY, self::CACHE_GROUP);
    }

    private function set_cache($data, int $expire = 15)
    {
        wp_cache_add(self::CACHE_KEY, $data, self::CACHE_GROUP, $expire);
    }

    private function get_meta_field_options(): array
    {
        global $wpdb;

        $options = $this->get_cache();

        if ( ! empty($options)) {
            return $options;
        }

        $grouped_options = [
            [
                'title'   => __('Public', 'codepress-admin-columns'),
                'options' => [],
            ],
            [
                'title'   => __('Hidden', 'codepress-admin-columns'),
                'options' => [],
            ],
        ];
        $options = $wpdb->get_col("SELECT DISTINCT(meta_key) FROM {$wpdb->prefix}wc_orders_meta ORDER BY meta_key");

        foreach ($options as $option) {
            $group = 0 === strpos($option, '_') ? 1 : 0;

            $grouped_options[$group]['options'][$option] = $option;
        }

        $this->set_cache($grouped_options);

        return $grouped_options;
    }

    private function get_settings_field_select(): AC\Form\Element\Select
    {
        return $this->create_element('select', self::KEY)
                    ->set_options($this->get_meta_field_options());
    }

    public function get_dependent_settings()
    {
        return [new ACP\Settings\Column\CustomFieldType($this->column)];
    }

    public function get_meta_field(): string
    {
        return (string)$this->meta_field;
    }

    public function set_meta_field($meta_field): void
    {
        $this->meta_field = $meta_field;
    }
}