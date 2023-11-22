<?php

namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Modules\DynamicTags\Module;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Token extends Tag
{
    protected static $acf_names = [];
    public function get_name()
    {
        return 'dce-token';
    }
    public function get_title()
    {
        return __('Token', 'dynamic-content-for-elementor');
    }
    public function get_group()
    {
        return 'dce';
    }
    public function get_categories()
    {
        return \DynamicContentForElementor\Helper::get_dynamic_tags_categories();
    }
    public function get_docs()
    {
        return 'https://www.dynamic.ooo/widget/dynamic-tag-token/';
    }
    /**
     * @return void
     */
    protected function register_controls()
    {
        if (\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $this->register_controls_settings();
        } else {
            $this->register_controls_non_admin_notice();
        }
    }
    /**
     * @return void
     */
    protected function register_controls_settings()
    {
        $objects = array('post', 'user', 'term');
        $this->add_control('dce_token_wizard', ['label' => __('Wizard mode', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER]);
        $this->add_control('dce_token', ['label' => __('Token', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'label_block' => \true, 'placeholder' => '[post:title|esc_html], [post:meta_key], [user:display_name], [term:name], [wp_query:posts]', 'condition' => ['dce_token_wizard' => '']]);
        $types = ['post' => ['title' => __('Post', 'dynamic-content-for-elementor'), 'icon' => 'eicon-post-content'], 'user' => ['title' => __('User', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-users'], 'term' => ['title' => __('Term', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-tags'], 'option' => ['title' => __('Option', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list'], 'wp_query' => ['title' => __('WP Query', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-search'], 'date' => ['title' => __('Date', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-calendar'], 'system' => ['title' => __('System', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-cogs']];
        // ACF
        if (\DynamicContentForElementor\Helper::is_acf_active()) {
            $types['acf'] = ['title' => __('ACF', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-plug'];
        }
        // Jet Engine
        if (\DynamicContentForElementor\Helper::is_jetengine_active()) {
            $types['jet'] = ['title' => __('JetEngine', 'dynamic-content-for-elementor'), 'icon' => 'icon-dce-jetengine'];
        }
        // Meta Box
        if (\DynamicContentForElementor\Helper::is_metabox_active()) {
            $types['metabox'] = ['title' => __('Meta Box', 'dynamic-content-for-elementor'), 'icon' => 'icon-dce-metabox'];
        }
        $this->add_control('dce_token_object', ['label' => __('Object', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => Controls_Manager::CHOOSE, 'options' => $types, 'default' => 'post', 'toggle' => \false, 'condition' => ['dce_token_wizard!' => '']]);
        $this->add_control('dce_token_field_date', ['label' => __('Date Modificator', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'placeholder' => '+1 week, -2 months, yesterday, timestamp', 'description' => __('A time modificator compatible with strtotime or a timestamp', 'dynamic-content-for-elementor'), 'label_block' => \true, 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => 'date']]);
        $this->add_control('dce_token_field_date_format', ['label' => __('Date Format', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'placeholder' => 'Y-m-d H:i:s', 'label_block' => \true, 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => 'date']]);
        $this->add_control('dce_token_field_system', ['label' => __('Field', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => \true, 'placeholder' => __('_GET, _POST, _SERVER', 'dynamic-content-for-elementor'), 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => 'system']]);
        foreach ($objects as $object) {
            $this->add_control('dce_token_field_' . $object, ['label' => __('Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Meta key or Field Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'fields', 'object_type' => $object, 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => $object]]);
        }
        $this->add_control('dce_token_field_option', ['label' => __('Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Option key', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'options', 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => 'option']]);
        $this->add_control('dce_token_field_acf', ['label' => __('Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => 'acf']]);
        $this->add_control('dce_token_acf_settings', ['label' => __('Get Field Settings', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => 'acf']]);
        $this->add_control('dce_token_field_jet', ['label' => __('Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'jet', 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => 'jet']]);
        $this->add_control('dce_token_field_metabox', ['label' => __('Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metabox', 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => 'metabox']]);
        $this->add_control('dce_token_subfield', ['label' => __('SubField', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => \true, 'placeholder' => __('my_sub:label', 'dynamic-content-for-elementor'), 'condition' => ['dce_token_wizard!' => '', 'dce_token_object!' => 'date']]);
        foreach ($objects as $object) {
            $this->add_control('dce_token_source_' . $object, ['label' => __('Source', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Search', 'dynamic-content-for-elementor') . ' ' . \ucfirst($object), 'label_block' => \true, 'query_type' => $object . 's', 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => $object]]);
        }
        $this->add_control('dce_token_filter', ['label' => __('Filters', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'rows' => 2, 'placeholder' => 'trim', 'label_block' => \true, 'condition' => ['dce_token_wizard!' => '']]);
        $this->add_control('dce_token_code', ['label' => __('Show Token', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'condition' => ['dce_token_wizard!' => '']]);
        $this->add_control('dce_token_data', ['label' => __('Return as Data (deprecated)', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'condition' => ['dce_token_code' => '']]);
        $this->add_control('dce_token_data_wawrning', ['type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => __('Return as Data is deprecated and highly discouraged. It was used for Images and Media. You should replace it with the Dynamic Tag Image Token', 'dynamic-content-for-elementor'), 'condition' => ['dce_token_data' => 'yes']]);
        $this->add_control('dce_token_help', ['type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => '<div id="elementor-panel__editor__help" class="p-0"><a id="elementor-panel__editor__help__link" href="' . $this->get_docs() . '" target="_blank">' . __('Need Help', 'dynamic-content-for-elementor') . ' <i class="eicon-help-o"></i></a></div>', 'separator' => 'before']);
    }
    /**
     * @return void
     */
    protected function register_controls_non_admin_notice()
    {
        $this->add_control('html_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('You will need administrator capabilities to edit this widget.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
    }
    public function render()
    {
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            return '';
        }
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $value = $this->get_token_value($settings);
        echo $value;
    }
    public function get_token_value($settings)
    {
        if (!empty($settings['dce_token_wizard'])) {
            $objects = array('post', 'user', 'term');
            $token = '[';
            $token .= $settings['dce_token_object'];
            foreach ($objects as $object) {
                if ($settings['dce_token_field_' . $object]) {
                    $token .= ':' . $settings['dce_token_field_' . $object];
                }
            }
            if ('acf' === $settings['dce_token_object']) {
                // Advanced Custom Fields
                $acf_field = $settings['dce_token_field_acf'];
                if (\is_numeric($acf_field) && get_post_type($acf_field) === 'acf-field') {
                    // in this case $acf_field is the ID of the ACF field as
                    // saved in the wp_posts table, in the post_excerpt field
                    // the field name is stored. Using WordPress functions
                    // because trying to retrieve the key with ACF functions
                    // was not succesful.
                    $acf_field = get_post_field('post_excerpt', $acf_field);
                }
                if (!empty($acf_field)) {
                    $token .= ':' . $acf_field;
                }
            } elseif ('jet' === $settings['dce_token_object']) {
                // Jet Engine
                $jet_field = $settings['dce_token_field_jet'];
                if (!empty($jet_field)) {
                    $token .= ':' . $jet_field;
                }
            } elseif ('metabox' === $settings['dce_token_object']) {
                // Meta Box
                $metabox_field = $settings['dce_token_field_metabox'];
                if (!empty($metabox_field)) {
                    $token .= ':' . $metabox_field;
                }
            } elseif ('date' === $settings['dce_token_object']) {
                // Date
                if ($settings['dce_token_field_date']) {
                    $token .= ':' . $settings['dce_token_field_date'];
                }
                if ($settings['dce_token_field_date_format']) {
                    $token .= '|' . $settings['dce_token_field_date_format'];
                }
            }
            if ($settings['dce_token_field_system']) {
                $token .= ':' . $settings['dce_token_field_system'];
            }
            if ($settings['dce_token_field_option']) {
                $token .= ':' . $settings['dce_token_field_option'];
            }
            if ($settings['dce_token_subfield']) {
                $token .= ':' . $settings['dce_token_subfield'];
            }
            if ($settings['dce_token_filter']) {
                $filters = \explode(\PHP_EOL, $settings['dce_token_filter']);
                $token .= '|' . \implode('|', $filters);
            }
            foreach ($objects as $object) {
                if ($settings['dce_token_source_' . $object]) {
                    $token .= '|' . $settings['dce_token_source_' . $object];
                }
            }
            $token .= ']';
            if ($settings['dce_token_code']) {
                echo $token;
                return;
            }
        } else {
            $token = $settings['dce_token'];
        }
        $value = \DynamicContentForElementor\Helper::get_dynamic_value($token);
        return $value;
    }
    public function get_value(array $options = [])
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        \DynamicContentForElementor\Tokens::$data = \true;
        $value = $this->get_token_value($settings);
        \DynamicContentForElementor\Tokens::$data = \false;
        // for Media Controls
        if (\filter_var($value, \FILTER_VALIDATE_URL)) {
            $image_data = ['url' => $value];
            $thumbnail_id = \DynamicContentForElementor\Helper::get_image_id($value);
            if ($thumbnail_id) {
                $image_data['id'] = $thumbnail_id;
            }
            return $image_data;
        }
        return $value;
    }
    public function get_content(array $options = [])
    {
        $settings = $this->get_settings();
        $value = \false;
        if (isset($settings['dce_token_data']) && $settings['dce_token_data']) {
            $value = $this->get_value($options);
        } else {
            \ob_start();
            $this->render();
            $value = \ob_get_clean();
            if ($value) {
                // TODO: fix spaces in `before`/`after` if WRAPPED_TAG ( conflicted with .elementor-tag { display: inline-flex; } );
                if (!Utils::is_empty($settings, 'before')) {
                    $value = wp_kses_post($settings['before']) . $value;
                }
                if (!Utils::is_empty($settings, 'after')) {
                    $value .= wp_kses_post($settings['after']);
                }
            } elseif (!Utils::is_empty($settings, 'fallback')) {
                $value = $settings['fallback'];
            }
        }
        if (empty($value) && $this->get_settings('fallback')) {
            $value = $this->get_settings('fallback');
            $value = \DynamicContentForElementor\Helper::get_dynamic_value($value);
        }
        return $value;
    }
}
