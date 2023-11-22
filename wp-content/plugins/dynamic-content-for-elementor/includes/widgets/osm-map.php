<?php

namespace DynamicContentForElementor\Widgets;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;
class OsmMap extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-osm-map'];
    }
    public function get_style_depends()
    {
        return ['dce-osm-map'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_map', ['label' => __('Map', 'dynamic-content-for-elementor')]);
        $default_address = __('Piazza San Marco, Venice, Italy', 'dynamic-content-for-elementor');
        $this->add_control('address', [
            'label' => __('Location', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'frontend_available' => \true,
            'dynamic' => ['active' => \true, 'categories' => [TagsModule::POST_META_CATEGORY]],
            'selectors' => ['' => ''],
            // avoid reinitialization of the widget.
            'placeholder' => $default_address,
            'default' => $default_address,
            'label_block' => \true,
        ]);
        $this->add_control('zoom', [
            'label' => __('Zoom', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SLIDER,
            'frontend_available' => \true,
            'selectors' => ['' => ''],
            // avoid reinitialization of the widget.
            'default' => ['size' => 14],
            'range' => ['px' => ['min' => 1, 'max' => 20]],
            'separator' => 'before',
        ]);
        $this->add_responsive_control('height', ['label' => __('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 40, 'max' => 1440], 'vh' => ['min' => 0, 'max' => 100]], 'default' => ['size' => 500], 'size_units' => ['px', 'vh'], 'selectors' => ['{{WRAPPER}} .dce-osm-wrapper' => 'height: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_map_style', ['label' => __('Map', 'elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->start_controls_tabs('map_filter');
        $this->start_controls_tab('normal', ['label' => __('Normal', 'dynamic-content-for-elementor')]);
        $this->add_group_control(\Elementor\Group_Control_Css_Filter::get_type(), ['name' => 'css_filters', 'selector' => '{{WRAPPER}} .dce-osm-wrapper']);
        $this->end_controls_tab();
        $this->start_controls_tab('hover', ['label' => __('Hover', 'dynamic-content-for-elementor')]);
        $this->add_group_control(\Elementor\Group_Control_Css_Filter::get_type(), ['name' => 'css_filters_hover', 'selector' => '{{WRAPPER}}:hover .dce-osm-wrapper']);
        $this->add_control('hover_transition', ['label' => __('Transition Duration', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 3, 'step' => 0.1]], 'selectors' => ['{{WRAPPER}} .dce-osm-wrapper' => 'transition-duration: {{SIZE}}s']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('div', 'class', 'dce-osm-wrapper');
        echo '<div ' . $this->get_render_attribute_string('div') . '></div>';
    }
    protected function content_template()
    {
        echo '<div class="dce-osm-wrapper"></div>';
    }
}
