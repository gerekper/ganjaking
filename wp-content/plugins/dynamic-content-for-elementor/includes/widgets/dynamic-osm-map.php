<?php

namespace DynamicContentForElementor\Widgets;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
class DynamicOsmMap extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * Get Script Depends
     *
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['dce-dynamic-osm-map'];
    }
    /**
     * Get Style Depends
     *
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['dce-osm-map', 'dce-osm-map-marker-cluster'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $default_address = __('Piazza San Marco, Venice, Italy', 'dynamic-content-for-elementor');
        $this->start_controls_section('section_map', ['label' => __('Map', 'dynamic-content-for-elementor')]);
        $this->add_control('map_data_type', ['label' => __('Data Type', 'dynamic-content-for-elementor'), 'type' => 'images_selector', 'toggle' => \false, 'type_selector' => 'icon', 'columns_grid' => 5, 'default' => 'address', 'options' => ['address' => ['title' => __('Address', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-map-marker-alt'], 'latlon' => ['title' => __('Latitude and longitude', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-globe-europe']], 'frontend_available' => \true]);
        $this->add_control('map_type', ['label' => __('Map Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'frontend_available' => \true, 'default' => 'osm', 'options' => ['osm' => __('Osm', 'dynamic-content-for-elementor'), 'hot' => __('Hot', 'dynamic-content-for-elementor'), 'cycle' => __('Cycle', 'dynamic-content-for-elementor')]]);
        $repeaters_address = new \Elementor\Repeater();
        // Address
        $repeaters_address->add_control('rep_address', ['label' => esc_html__('Address', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'default' => esc_html__('Rome', 'dynamic-content-for-elementor'), 'label_block' => \true]);
        $repeaters_address->add_control('custom_marker_address', ['label' => __('Custom Marker', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'type' => Controls_Manager::SWITCHER]);
        $repeaters_address->add_control('image_marker_address', ['label' => __('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => ''], 'frontend_available' => \true, 'condition' => ['custom_marker_address' => 'yes']]);
        $repeaters_address->add_control('width_marker_address', ['label' => __('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 25], 'range' => ['px' => ['min' => 1, 'max' => 100]], 'separator' => 'before', 'condition' => ['custom_marker_address' => 'yes']]);
        $repeaters_address->add_control('height_marker_address', ['label' => __('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 40], 'range' => ['px' => ['min' => 1, 'max' => 100]], 'separator' => 'before', 'condition' => ['custom_marker_address' => 'yes']]);
        $repeaters_address->add_control('custom_infowindow_address', ['label' => __('Custom Info Window', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'type' => Controls_Manager::SWITCHER]);
        $repeaters_address->add_control('text_infowindow_address', ['label' => esc_html__('Info Window', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'default' => esc_html__('Text me', 'dynamic-content-for-elementor'), 'label_block' => \true, 'condition' => ['custom_infowindow_address' => 'yes']]);
        $this->add_control('addresses_list', ['label' => esc_html__('Addresses List', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeaters_address->get_controls(), 'default' => [['rep_address' => $default_address]], 'title_field' => '{{{ rep_address }}}', 'condition' => ['map_data_type' => 'address']]);
        $repeaters_latlon = new \Elementor\Repeater();
        // latlon
        $repeaters_latlon->add_control('rep_lat', ['label' => esc_html__('Latitude', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => esc_html__('45.43444', 'dynamic-content-for-elementor'), 'label_block' => \true]);
        $repeaters_latlon->add_control('rep_lon', ['label' => esc_html__('Longitude', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => esc_html__('12.33808', 'dynamic-content-for-elementor'), 'label_block' => \true]);
        $repeaters_latlon->add_control('custom_marker_latlon', ['label' => __('Custom Marker', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'type' => Controls_Manager::SWITCHER]);
        $repeaters_latlon->add_control('image_marker_latlon', ['label' => __('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => ''], 'frontend_available' => \true, 'condition' => ['custom_marker_latlon' => 'yes']]);
        $repeaters_latlon->add_control('width_marker_latlon', ['label' => __('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 25], 'range' => ['px' => ['min' => 1, 'max' => 100]], 'separator' => 'before', 'condition' => ['custom_marker_latlon' => 'yes']]);
        $repeaters_latlon->add_control('height_marker_latlon', ['label' => __('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 40], 'range' => ['px' => ['min' => 1, 'max' => 100]], 'separator' => 'before', 'condition' => ['custom_marker_latlon' => 'yes']]);
        $repeaters_latlon->add_control('custom_infowindow_latlon', ['label' => __('Custom Info Window', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'type' => Controls_Manager::SWITCHER]);
        $repeaters_latlon->add_control('text_infowindow_latlon', ['label' => esc_html__('Info Window', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'default' => esc_html__('Text me', 'dynamic-content-for-elementor'), 'label_block' => \true, 'condition' => ['custom_infowindow_latlon' => 'yes']]);
        $this->add_control('latlon_list', ['label' => esc_html__('Coordinates List', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeaters_latlon->get_controls(), 'default' => [['rep_lat' => esc_html__('45.43444', 'dynamic-content-for-elementor'), 'rep_lon' => esc_html__('12.33808', 'dynamic-content-for-elementor')]], 'title_field' => 'Lat: {{{ rep_lat }}}, Lon: {{{ rep_lon }}}', 'condition' => ['map_data_type' => 'latlon']]);
        $this->end_controls_section();
        $this->start_controls_section('section_controls', ['label' => __('Controlling', 'dynamic-content-for-elementor')]);
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
        $this->add_control('zoom_control', ['label' => __('Zoom Control', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'default' => 'yes', 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('box_zoom', ['label' => __('Box Zoom Control', 'dynamic-content-for-elementor'), 'description' => __('Whether the map can be zoomed to a rectangular area specified by dragging the mouse while pressing the shift key.', 'dynamic-content-for-elementor'), 'default' => 'yes', 'frontend_available' => \true, 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('double_click_zoom', ['label' => __('double Click Zoom', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'default' => 'yes', 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('dragging', ['label' => __('dragging on Map', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'default' => 'yes', 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('enable_layers_group', ['label' => __('Layers Panel', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'default' => 'yes', 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('enable_map_scale', ['label' => __('Map Scale Info', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('scale_panel_width', ['label' => __('Scale Panel Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 100], 'range' => ['px' => ['min' => 100, 'max' => 500, 'step' => 1]], 'separator' => 'before', 'condition' => ['enable_map_scale' => 'yes']]);
        $this->add_control('scale_panel_metric', ['label' => __('Show the metric scale line (m/km)', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['enable_map_scale' => 'yes']]);
        $this->add_control('scale_panel_imperial', ['label' => __('Show the imperial scale line (mi/ft)', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'type' => Controls_Manager::SWITCHER, 'condition' => ['enable_map_scale' => 'yes']]);
        $this->add_control('enable_marker_cluster_group', ['label' => __('Cluster', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'type' => Controls_Manager::SWITCHER]);
        $this->end_controls_section();
        $this->start_controls_section('section_circles', ['label' => __('Circles', 'dynamic-content-for-elementor')]);
        $this->add_control('enable_circles', ['label' => __('Circles', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('radius_circles', ['label' => __('Radius Circles (m)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 100000], 'range' => ['px' => ['min' => 10, 'max' => 2000000]], 'separator' => 'before', 'condition' => ['enable_circles' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_map_style', ['label' => __('Map', 'elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('height', ['label' => __('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 40, 'max' => 1440], 'vh' => ['min' => 0, 'max' => 100]], 'default' => ['size' => 500], 'size_units' => ['px', 'vh'], 'selectors' => ['{{WRAPPER}} .dce-osm-wrapper' => 'height: {{SIZE}}{{UNIT}};']]);
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
        $this->start_controls_section('section_infowindow_style', ['label' => __('Info Window', 'elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('infowindow_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .leaflet-popup-content-wrapper,{{WRAPPER}} .leaflet-popup-content-wrapper, {{WRAPPER}} .leaflet-popup-tip' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'infowindow_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .leaflet-popup-content-wrapper']);
        $this->add_control('infowindow_textcolor', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .leaflet-popup-content, {{WRAPPER}} .leaflet-popup-close-button' => 'color: {{VALUE}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_circles_style', ['label' => __('Circles', 'elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['enable_circles' => 'yes']]);
        $this->add_control('color_circles', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#3388FF', 'frontend_available' => \true, 'condition' => ['enable_circles' => 'yes']]);
        $this->add_control('opacity_circles', ['label' => __('Opacity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 1.0], 'range' => ['px' => ['min' => 0.1, 'max' => 1.0, 'step' => 0.1]], 'separator' => 'before', 'condition' => ['enable_circles' => 'yes']]);
        $this->add_control('weight_circles', ['label' => __('Weight', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 1, 'max' => 10, 'step' => 1]], 'separator' => 'before', 'condition' => ['enable_circles' => 'yes']]);
        $this->end_controls_section();
    }
    /**
     * Safe Render
     *
     * @return void
     */
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $data_repeater = [];
        if ('latlon' === $settings['map_data_type']) {
            $data_repeater = $settings['latlon_list'] ?? [];
        } elseif ('address' === $settings['map_data_type']) {
            $data_repeater = $settings['addresses_list'] ?? [];
        }
        $this->add_render_attribute('div', ['data_repeater' => [wp_json_encode($data_repeater)], 'class' => ['dce-osm-wrapper']]);
        echo '<div ' . $this->get_render_attribute_string('div') . '></div>';
    }
}
