<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicTemplate extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function run_once()
    {
        parent::run_once();
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control($this->get_type(), 'other_post_source');
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_dynamictemplate', ['label' => $this->get_title()]);
        $this->add_control('dynamic_template', ['label' => __('Select Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library']);
        $this->add_control('data_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'description' => __('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => __('Same', 'dynamic-content-for-elementor'), 'label_off' => __('other', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'separator' => 'before']);
        $this->add_control('other_post_source', ['label' => __('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings) || empty($settings['dynamic_template'])) {
            return;
        }
        $template_id = $settings['dynamic_template'];
        $template_id = apply_filters('wpml_object_id', $template_id, 'elementor_library', \true);
        $atts = ['id' => $template_id, 'inlinecss' => \Elementor\Plugin::$instance->editor->is_edit_mode()];
        if (empty($settings['data_source']) && $settings['other_post_source']) {
            $atts['post_id'] = $settings['other_post_source'];
        }
        $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
        echo $template_system->build_elementor_template_special($atts);
    }
}
