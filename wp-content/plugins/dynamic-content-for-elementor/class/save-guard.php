<?php

namespace DynamicContentForElementor;

use Elementor\Core\Base\Document;
use Elementor\Core\Settings\Page\Manager as PageManager;
if (!\defined('ABSPATH')) {
    exit;
}
class SaveGuard
{
    /**
     * @var array<string>
     */
    private $unsafe_widgets = [];
    /**
     * @var array<string,mixed>
     */
    private $unsafe_controls = [];
    /**
     * @var array<string>
     */
    private $unsafe_dynamic_tags = ['dce-dynamic-tag-php', 'dce-dynamic-tag-image-token', 'dce-token'];
    /**
     * @var array<string,mixed>
     */
    private $saved_data;
    /**
     * @param string $type
     *
     * @return void
     */
    public function register_unsafe_widget($type)
    {
        $this->unsafe_widgets[] = $type;
    }
    /**
     * @param string $widget_type
     * @param string $control_path
     *
     * @return void
     */
    public function register_unsafe_control($widget_type, $control_path)
    {
        if (!isset($this->unsafe_controls[$widget_type])) {
            $this->unsafe_controls[$widget_type] = [];
        }
        $this->unsafe_controls[$widget_type][$control_path] = \true;
    }
    /**
     * @return never
     */
    private function denied()
    {
        $msg = DCE_PRODUCT_NAME . ' ' . esc_html__('Only administrators can edit this Elementor Page', 'dynamic-content-for-elementor');
        throw new \Exception($msg);
    }
    /**
     * @param array<mixed> $elements
     * @param string $_id
     *
     * @return array<mixed>|false
     */
    private function find_element_by__id($elements, $_id)
    {
        $res = \array_filter($elements, function ($e) use($_id) {
            return $e['_id'] === $_id;
        });
        return \current($res);
    }
    /**
     * @param string $id
     *
     * @return array<string,mixed>|false
     */
    private function find_saved_element($id)
    {
        return \DynamicContentForElementor\Helper::find_element_recursive($this->saved_data['elements'], $id);
    }
    /**
     * @param string $element_id
     * @param string $repeater_name
     * @param string $field__id
     *
     * @return array<string,mixed>|false
     */
    private function find_saved_repeater_field($element_id, $repeater_name, $field__id)
    {
        $sel = $this->find_saved_element($element_id);
        if (!$sel) {
            return \false;
        }
        return $this->find_element_by__id($sel['settings'][$repeater_name], $field__id);
    }
    /**
     * @param string $id
     * @return array<string,mixed>|never
     */
    private function find_saved_element_or_deny($id)
    {
        if ($id === '') {
            return $this->saved_data;
        }
        $el = $this->find_saved_element($id);
        if (!$el) {
            $this->denied();
        }
        return $el;
    }
    /**
     * @param array<string,mixed> $settings
     * @param Callable $saved_settings_callback
     *
     * @return array<string,mixed>
     */
    private function filter_dynamic_tags_flat($settings, $saved_settings_callback)
    {
        foreach ($settings as $key => $val) {
            if ('__dynamic__' === $key) {
                foreach ($val as $dt_key => $dt_value) {
                    foreach ($this->unsafe_dynamic_tags as $unsafe_dt) {
                        if (\strpos($dt_value, $unsafe_dt)) {
                            $saved_settings = $saved_settings_callback();
                            if (isset($saved_settings['__dynamic__'][$dt_key])) {
                                $settings['__dynamic__'][$dt_key] = $saved_settings['__dynamic__'][$dt_key];
                            } else {
                                unset($settings['__dynamic__'][$dt_key]);
                            }
                        }
                    }
                }
            }
        }
        return $settings;
    }
    /**
     * @param string $element_id
     * @param array<string,mixed> $settings
     *
     * @return array<string,mixed>
     */
    private function filter_dynamic_tags($element_id, $settings)
    {
        $settings = $this->filter_dynamic_tags_flat($settings, function () use($element_id) {
            $sel = $this->find_saved_element($element_id);
            return $sel ? $sel['settings'] : \false;
        });
        foreach ($settings as $key => $val) {
            if (\is_array($val)) {
                // it's a repeater
                foreach ($val as $index => $field) {
                    if (!\is_array($field) || !isset($field['_id'])) {
                        // it's not a field.
                        continue;
                    }
                    $field__id = $field['_id'];
                    $settings[$key][$index] = $this->filter_dynamic_tags_flat($settings[$key][$index], function () use($element_id, $key, $field__id) {
                        return $this->find_saved_repeater_field($element_id, $key, $field__id);
                    });
                }
            }
        }
        return $settings;
    }
    /**
     * @param string $element_id
     * @param array<string,mixed> $settings
     * @param string $widget_type
     *
     * @return array<string,mixed>
     */
    private function filter_unsafe_controls($element_id, $settings, $widget_type)
    {
        $controls = $this->unsafe_controls[$widget_type] ?? [];
        $controls += $this->unsafe_controls['any'] ?? [];
        foreach (\array_keys($controls) as $key) {
            // if the control is inside a repeater:
            if (\strpos($key, '::')) {
                list($repeater, $subkey) = \explode('::', $key);
                // look through all the repeater fields:
                foreach ($settings[$repeater] ?? [] as $index => $field) {
                    if (isset($field[$subkey])) {
                        $saved_field = $this->find_saved_repeater_field($element_id, $repeater, $field['_id']);
                        if ($saved_field && isset($saved_field[$subkey])) {
                            $settings[$repeater][$index][$subkey] = $saved_field[$subkey];
                        } else {
                            unset($settings[$repeater][$index][$subkey]);
                        }
                    }
                }
            }
            if (isset($settings[$key])) {
                $sel = $this->find_saved_element($element_id);
                if ($sel && isset($sel['settings'][$key])) {
                    $settings[$key] = $sel['settings'][$key];
                } else {
                    unset($settings[$key]);
                }
            }
        }
        return $settings;
    }
    /**
     * @param string $element_id
     * @param array<string,mixed> $settings
     * @param string $widget_type
     *
     * @return array<string,mixed>
     */
    private function filter_settings($element_id, $settings, $widget_type)
    {
        $settings = $this->filter_dynamic_tags($element_id, $settings);
        return $this->filter_unsafe_controls($element_id, $settings, $widget_type);
    }
    /**
     * @param array<string,mixed> $element
     *
     * @return array<string,mixed>
     */
    private function filter_element($element)
    {
        $type = $element['widgetType'] ?? \false;
        if ($type && \in_array($type, $this->unsafe_widgets, \true)) {
            $saved_element = $this->find_saved_element_or_deny($element['id']);
            return $saved_element;
        }
        if (isset($element['settings'])) {
            $element['settings'] = $this->filter_settings($element['id'] ?? '', $element['settings'], $type);
        }
        foreach ($element['elements'] as $index => $el) {
            $element['elements'][$index] = $this->filter_element($el);
        }
        return $element;
    }
    /**
     * @param Document $document
     *
     * @return array<string,mixed>
     */
    public function get_saved_data($document)
    {
        $elements = $document->get_elements_raw_data();
        $page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers('page');
        if (\is_array($page_settings_manager)) {
            throw new \Error();
        }
        $model = $page_settings_manager->get_model($document->get_post()->ID);
        $settings = $model->get_settings();
        return ['elements' => $elements, 'settings' => $settings];
    }
    /**
     * @param array<string,mixed> $data
     * @param Document $document
     *
     * @return array<string,mixed>
     */
    public function filter_save_data($data, $document)
    {
        if (empty($data)) {
            // needed to avoid infinite recursion when getting saved data of a new elementor post.
            return $data;
        }
        if (current_user_can('administrator')) {
            return $data;
        }
        $this->saved_data = $this->get_saved_data($document);
        return $this->filter_element($data);
    }
    public function __construct()
    {
        add_filter('elementor/document/save/data', [$this, 'filter_save_data'], 10, 2);
    }
}
