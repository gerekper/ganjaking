<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class AddressAutocomplete extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    private $is_common = \false;
    public $has_action = \false;
    public $depended_scripts = ['dce-form-address-autocomplete'];
    public $depended_styles = [];
    public function get_name()
    {
        return 'dce_form_address_autocomplete';
    }
    protected function add_actions()
    {
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'update_fields_controls']);
        add_action('elementor-pro/forms/pre_render', [$this, 'add_assets_depends'], 10, 2);
    }
    public function add_assets_depends($instance, $form)
    {
        $autocomplete_fields = [];
        // fetch all the settings data we need to pass to the JavaScript code:
        foreach ($instance['form_fields'] as $field) {
            if ($field['field_type'] == 'text' && !empty($field['field_address'])) {
                $new_field = ['id' => $field['custom_id']];
                if (!empty($field['field_address_restrict_country'])) {
                    $new_field['country'] = $field['field_address_restrict_country'];
                }
                $autocomplete_fields[] = $new_field;
            }
        }
        if (!empty($autocomplete_fields)) {
            $form->add_render_attribute('wrapper', 'data-autocomplete-fields', wp_json_encode($autocomplete_fields));
            global $wp_scripts;
            if (!empty($wp_scripts->registered['dce-google-maps-api'])) {
                $wp_scripts->registered['dce-google-maps-api']->src .= '&libraries=places';
            }
            foreach ($this->depended_scripts as $script) {
                $form->add_script_depends($script);
            }
            foreach ($this->depended_styles as $style) {
                $form->add_style_depends($style);
            }
        }
    }
    public function update_fields_controls($widget)
    {
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            return;
        }
        $elementor = \ElementorPro\Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = [];
        if (!get_option('dce_google_maps_api')) {
            $field_controls['field_address_api_notice'] = ['name' => 'field_address_api_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => __('In order to use Address Autocomplete you should set Google Maps API, with Geocoding API enabled, on Integrations section', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'conditions' => ['terms' => [['name' => 'field_type', 'value' => 'text']]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted'];
        }
        $field_controls += ['field_address' => ['name' => 'field_address', 'label' => __('Address Autocomplete', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'true', 'default' => '', 'conditions' => ['terms' => [['name' => 'field_type', 'value' => 'text']]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted'], 'field_address_restrict_country' => ['name' => 'field_address_restrict_country', 'label' => __('Restrict Country', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => Helper::get_iso_3166_1_alpha_2(), 'multiple' => \true, 'conditions' => ['terms' => [['name' => 'field_type', 'value' => 'text'], ['name' => 'field_address', 'operator' => '!=', 'value' => '']]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted']];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
}
