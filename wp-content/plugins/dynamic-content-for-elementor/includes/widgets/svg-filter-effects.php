<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class SvgFilterEffects extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-gsap-lib', 'dce-svgfe'];
    }
    public function get_style_depends()
    {
        return ['dce-svg'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $widgetId = $this->get_id();
        $this->start_controls_section('section_fe_effects', ['label' => $this->get_title()]);
        $this->add_control('svg_trigger', ['label' => __('Trigger', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'options' => ['static' => __('Static', 'dynamic-content-for-elementor'), 'animation' => __('Animation', 'dynamic-content-for-elementor'), 'rollover' => __('Rollover', 'dynamic-content-for-elementor'), 'scroll' => __('Scroll', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'default' => 'static', 'render_type' => 'template']);
        $this->add_control('svg_trigger_options', ['label' => __('Trigger options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['from_val_to_zero' => __('From values to original', 'dynamic-content-for-elementor'), 'from_zero_to_val' => __('From original to values', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'default' => 'from_val_to_zero', 'separator' => 'after', 'condition' => ['svg_trigger!' => 'static']]);
        $this->add_control('link_to', ['label' => __('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => __('None', 'dynamic-content-for-elementor'), 'home' => __('Home URL', 'dynamic-content-for-elementor'), 'custom' => __('Custom URL', 'dynamic-content-for-elementor')], 'condition' => ['svg_trigger' => 'rollover']]);
        $this->add_control('link', ['label' => __('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => __('https://your-link.com', 'dynamic-content-for-elementor'), 'dynamic' => ['active' => \true], 'condition' => ['link_to' => 'custom', 'svg_trigger' => 'rollover'], 'default' => ['url' => ''], 'show_label' => \false]);
        $this->add_control('playpause_control', ['label' => __('Animation Controls', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'default' => 'running', 'toggle' => \false, 'options' => ['running' => ['title' => __('Play', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-play'], 'paused' => ['title' => __('Pause', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-pause']], 'frontend_available' => \true, 'separator' => 'before', 'render_type' => 'ui', 'condition' => ['svg_trigger' => ['animation']]]);
        $this->add_control('animation_heading', ['label' => __('Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['svg_trigger' => ['animation', 'rollover', 'scroll']]]);
        $this->add_control('speed_animation', ['label' => __('Speed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'label_block' => \false, 'default' => ['size' => 3], 'range' => ['px' => ['min' => 0.2, 'max' => 10, 'step' => 0.1]], 'frontend_available' => \true, 'condition' => ['svg_trigger' => ['animation', 'rollover', 'scroll']]]);
        $this->add_control('delay_animation', ['label' => __('Delay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'label_block' => \false, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0.1, 'max' => 10, 'step' => 0.1]], 'frontend_available' => \true, 'condition' => ['svg_trigger' => ['animation', 'rollover', 'scroll']]]);
        $this->add_control('easing_animation', ['label' => __('Easing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor')] + Helper::get_gsap_ease(), 'default' => 'easeInOut', 'frontend_available' => \true, 'label_block' => \false, 'condition' => ['svg_trigger' => ['animation', 'rollover', 'scroll']]]);
        $this->add_control('easing_animation_ease', ['label' => __('Equation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor')] + Helper::get_gsap_timing_functions(), 'default' => 'Power3', 'frontend_available' => \true, 'label_block' => \false, 'condition' => ['svg_trigger' => ['animation', 'rollover', 'scroll']]]);
        $this->add_control('fe_filtereffect', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'separator' => 'after', 'options' => ['' => __('None', 'dynamic-content-for-elementor'), 'duotone' => __('Duotone', 'dynamic-content-for-elementor'), 'broken' => __('Broken', 'dynamic-content-for-elementor'), 'squiggly' => __('Squiggly', 'dynamic-content-for-elementor'), 'sketch' => __('Sketch Frame', 'dynamic-content-for-elementor'), 'glitch' => __('Glitch', 'dynamic-content-for-elementor'), 'x-rays' => __('X-rays', 'dynamic-content-for-elementor'), 'morphology' => __('Morphology', 'dynamic-content-for-elementor'), 'posterize' => __('Posterize', 'dynamic-content-for-elementor'), 'rgbOfset' => __('RGB Offset', 'dynamic-content-for-elementor'), 'pixelate' => __('Pixelate', 'dynamic-content-for-elementor')], 'default' => 'duotone']);
        $this->add_control('options_heading', ['label' => __('Options', 'dynamic-content-for-elementor'), 'description' => __('Shape parameters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'condition' => ['fe_filtereffect' => ['morphology', 'duotone', 'squiggly', 'broken', 'sketch', 'posterize', 'glitch', 'pixelate']]]);
        $this->add_control('size_pixelate', ['label' => __('Pixel size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '5', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 20, 'step' => 1]], 'condition' => ['fe_filtereffect' => 'glitch']]);
        $this->add_control('size_glitch', ['label' => __('Glitch size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '5', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 20, 'step' => 1]], 'condition' => ['fe_filtereffect' => 'glitch']]);
        $this->add_control('sketchStroke', ['label' => __('Sketch Stroke', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '4', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 30, 'step' => 0.1]], 'condition' => ['fe_filtereffect' => 'sketch']]);
        $this->add_control('sketchX', ['label' => __('Sketch X', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0.01', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 0.1, 'step' => 0.01]], 'condition' => ['fe_filtereffect' => 'sketch']]);
        $this->add_control('sketchY', ['label' => __('Sketch Y', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0.02', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 0.1, 'step' => 0.01]], 'condition' => ['fe_filtereffect' => 'sketch']]);
        $this->add_control('sketchScale', ['label' => __('Sketch size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '17', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100, 'step' => 1]], 'condition' => ['fe_filtereffect' => 'sketch']]);
        $this->add_control('sketchColor', ['label' => __('Frame color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#000000', 'alpha' => \false, 'condition' => ['fe_filtereffect' => 'sketch']]);
        $this->add_control('popover_posterize_R', ['label' => __('Red', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::POPOVER_TOGGLE, 'return_value' => 'yes', 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->start_popover();
        $this->add_control('colorposter_R_1', ['label' => __('Color poster 1', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0'], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->add_control('colorposter_R_2', ['label' => __('Color poster 2', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0.25'], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->add_control('colorposter_R_3', ['label' => __('Color poster 3', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0.5'], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->add_control('colorposter_R_4', ['label' => __('Color poster 4', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0.75'], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->add_control('colorposter_R_5', ['label' => __('Color poster 5', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '1'], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->end_popover();
        $this->add_control('popover_posterize_G', ['label' => __('Green', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::POPOVER_TOGGLE, 'return_value' => 'yes', 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->start_popover();
        $this->add_control('colorposter_G_1', ['label' => __('Color poster 1', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0'], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->add_control('colorposter_G_2', ['label' => __('Color poster 2', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0.25'], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->add_control('colorposter_G_3', ['label' => __('Color poster 3', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0.5'], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->add_control('colorposter_G_4', ['label' => __('Color poster 4', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0.75'], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->add_control('colorposter_G_5', ['label' => __('Color poster 5', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '1'], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->end_popover();
        $this->add_control('popover_posterize_B', ['label' => __('Blue', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::POPOVER_TOGGLE, 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->start_popover();
        $this->add_control('colorposter_B_1', ['label' => __('Color poster 1', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0'], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->add_control('colorposter_B_2', ['label' => __('Color poster 2', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0.25'], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->add_control('colorposter_B_3', ['label' => __('Color poster 3', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0.5'], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->add_control('colorposter_B_4', ['label' => __('Color poster 4', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0.75'], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->add_control('colorposter_B_5', ['label' => __('Color poster 5', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '1'], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->end_popover();
        $this->add_control('desatureposter', ['label' => __('Remove colors', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'dilate', 'return_value' => '', 'condition' => ['fe_filtereffect' => 'posterize']]);
        $this->add_control('size_broken', ['label' => __('Broken size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '15', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 100, 'step' => 1]], 'condition' => ['fe_filtereffect' => 'broken']]);
        $this->add_control('operator_dilateerode', ['label' => __('Dilate or Erode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'dilate', 'label_on' => __('Dilate', 'dynamic-content-for-elementor'), 'label_off' => __('Erode', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'condition' => ['fe_filtereffect' => 'morphology']]);
        $this->add_control('radius_dilateerode', ['label' => __('Radius of Morphology', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '3', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 10, 'step' => 1]], 'condition' => ['fe_filtereffect' => 'morphology']]);
        $this->add_control('fe_color1', ['label' => __('Fill Color 1', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#FF0000', 'alpha' => \false, 'condition' => ['fe_filtereffect' => 'duotone']]);
        $this->add_control('fe_color2', ['label' => __('Fill Color 2', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#0000FF', 'alpha' => \false, 'condition' => ['fe_filtereffect' => 'duotone']]);
        $this->add_responsive_control('fe_baseFrequency', ['label' => __('Squiggly baseFrequency', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0.002', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0.001, 'max' => 0.3, 'step' => 0.001]], 'condition' => ['fe_filtereffect' => 'squiggly']]);
        $this->add_responsive_control('fe_numOctaves', ['label' => __('Squiggly numOctaves', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '3', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 10, 'step' => 1]], 'condition' => ['fe_filtereffect' => 'squiggly']]);
        $this->add_control('fe_turbulencetype', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['fractalNoise' => __('FractalNoise', 'dynamic-content-for-elementor'), 'turbulence' => __('Turbulence', 'dynamic-content-for-elementor')], 'default' => 'turbulence', 'condition' => ['fe_filtereffect' => 'squiggly']]);
        $this->add_responsive_control('fe_scale', ['label' => __('Squiggly scale', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '50'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 300, 'step' => 1]], 'condition' => ['fe_filtereffect' => 'squiggly']]);
        $this->add_responsive_control('fe_seed', ['label' => __('Squiggly seed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '3', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 10, 'step' => 1]], 'condition' => ['fe_filtereffect' => 'squiggly']]);
        $this->add_control('base_image', ['label' => __('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'separator' => 'before', 'dynamic' => ['active' => \true], 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()], 'condition' => ['fe_output' => '']]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), [
            'name' => 'image',
            // Actually its `image_size`
            'default' => 'thumbnail',
            'condition' => ['fe_output' => '', 'base_image[id]!' => ''],
        ]);
        $this->add_control('preserveAR', ['label' => __('Aspect Ratio', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'default' => '', 'frontend_available' => \true, 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_viewbox', ['label' => __('Viewbox', 'dynamic-content-for-elementor')]);
        $this->add_control('viewbox_width', ['label' => __('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'label_block' => \false, 'default' => 600, 'min' => 100, 'max' => 2000, 'step' => 1]);
        $this->add_control('viewbox_height', ['label' => __('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'label_block' => \false, 'default' => 600, 'min' => 100, 'max' => 2000, 'step' => 1]);
        $this->add_responsive_control('image_max_width', ['label' => __('Max-Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', '%', 'vw'], 'range' => ['px' => ['min' => 0, 'max' => 1000], '%' => ['min' => 0, 'max' => 100], 'vw' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} svg' => 'max-width: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Style', 'dynamic-content-for-elementor')]);
        $this->add_responsive_control('svg_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'prefix_class' => 'align-', 'default' => 'left', 'selectors' => ['{{WRAPPER}} .dce_fe_effects-wrapper' => 'text-align: {{VALUE}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'condition' => ['svg_trigger' => 'static']]);
        $this->add_control('fe_output', ['label' => __('Output', 'dynamic-content-for-elementor'), 'description' => __('Use the filter effects only for application on other page elements. Activating this option the svg element will not be displayed.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes']);
        $this->add_control('fe_output_direct', ['label' => __('Directly to element', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes']);
        $this->add_control('id_svg_class', ['label' => __('CSS Class', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['fe_output' => 'yes']]);
        $this->add_control('note_idclass', ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'raw' => __('Here you can write the class of the element to trasform with the SVG distortion. Remember to write the class name on your element in advanced tab.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'separator' => 'after', 'condition' => ['fe_output' => 'yes']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $widgetId = $this->get_id();
        $id_svg_class = $settings['id_svg_class'];
        $typeEffect = $settings['fe_filtereffect'];
        $filterId = '';
        if ($typeEffect != '') {
            $filterId = 'filter="url(#fe_effect-' . $widgetId . ')"';
        }
        $image_size = $settings['image_size'];
        $image_url = Group_Control_Image_Size::get_attachment_image_src($settings['base_image']['id'], 'image', $settings);
        $viewBoxW = $settings['viewbox_width'];
        $viewBoxH = $settings['viewbox_height'];
        // BROKEN
        $size_broken = $settings['size_broken']['size'];
        // SQUIGGLY
        $fe_baseFrequency = $settings['fe_baseFrequency']['size'];
        $fe_numOctaves = $settings['fe_numOctaves']['size'];
        $fe_scale = $settings['fe_scale']['size'];
        $fe_seed = $settings['fe_seed']['size'];
        $fe_turbulencetype = $settings['fe_turbulencetype'];
        // DUOTONE
        $duocolor1 = $settings['fe_color1'];
        $duocolor2 = $settings['fe_color2'];
        $hex_to_rgb_color1 = $this->hex2rgb($duocolor1);
        $hex_to_rgb_color2 = $this->hex2rgb($duocolor2);
        $color1_R = $hex_to_rgb_color1['red'] / 255;
        $color1_G = $hex_to_rgb_color1['green'] / 255;
        $color1_B = $hex_to_rgb_color1['blue'] / 255;
        $color2_R = $hex_to_rgb_color2['red'] / 255;
        $color2_G = $hex_to_rgb_color2['green'] / 255;
        $color2_B = $hex_to_rgb_color2['blue'] / 255;
        // GLITCH
        $size_glitch = $settings['size_glitch']['size'];
        // SKETCH
        $turbX_sketch = $settings['sketchX']['size'];
        $turbY_sketch = $settings['sketchY']['size'];
        $scale_sketch = $settings['sketchScale']['size'];
        $stroke_sketch = $settings['sketchStroke']['size'];
        $color_sketch = $settings['sketchColor'];
        // POSTERIZE
        $posterR = '';
        $posterG = '';
        $posterB = '';
        if ($settings['popover_posterize_R']) {
            $poster_R_1 = $settings['colorposter_R_1']['size'] . ' ';
            $poster_R_2 = $settings['colorposter_R_2']['size'] . ' ';
            $poster_R_3 = $settings['colorposter_R_3']['size'] . ' ';
            $poster_R_4 = $settings['colorposter_R_4']['size'] . ' ';
            $poster_R_5 = $settings['colorposter_R_5']['size'];
            $posterR = $poster_R_1 . $poster_R_2 . $poster_R_3 . $poster_R_4 . $poster_R_5;
        } else {
            $posterR = '0 0.25 0.5 0.75 1';
        }
        if ($settings['popover_posterize_G']) {
            $poster_G_1 = $settings['colorposter_G_1']['size'] . ' ';
            $poster_G_2 = $settings['colorposter_G_2']['size'] . ' ';
            $poster_G_3 = $settings['colorposter_G_3']['size'] . ' ';
            $poster_G_4 = $settings['colorposter_G_4']['size'] . ' ';
            $poster_G_5 = $settings['colorposter_G_5']['size'];
            $posterG = $poster_G_1 . $poster_G_2 . $poster_G_3 . $poster_G_4 . $poster_G_5;
        } else {
            $posterG = '0 0.25 0.5 0.75 1';
        }
        if ($settings['popover_posterize_B']) {
            $poster_B_1 = $settings['colorposter_B_1']['size'] . ' ';
            $poster_B_2 = $settings['colorposter_B_2']['size'] . ' ';
            $poster_B_3 = $settings['colorposter_B_3']['size'] . ' ';
            $poster_B_4 = $settings['colorposter_B_4']['size'] . ' ';
            $poster_B_5 = $settings['colorposter_B_5']['size'];
            $posterB = $poster_B_1 . $poster_B_2 . $poster_B_3 . $poster_B_4 . $poster_B_5;
        } else {
            $posterB = '0 0.25 0.5 0.75 1';
        }
        $desatureposter = $settings['desatureposter'];
        // MORPHOLOGY
        $dilateerode = $settings['radius_dilateerode']['size'];
        $this->add_render_attribute('svgfeeffects', ['class' => 'dce_fe_effects', 'data-coef' => 0.5]);
        $preserveAR = '';
        if (!$settings['preserveAR']) {
            $preserveAR = ' preserveAspectRatio="none"';
        }
        $abbondanza = 'x="0%" y="0%" width="100%" height="100%"';
        ?>
		<div class="dce_fe_effects-wrapper">
		  <div <?php 
        echo $this->get_render_attribute_string('svgfeeffects');
        ?>>
			<svg id="dce-svg-<?php 
        echo $this->get_id();
        ?>" class="dce-svg-fe_filtereffect" width="100%" height="100%" viewBox="0 0 <?php 
        echo $viewBoxW;
        ?> <?php 
        echo $viewBoxH;
        ?>" preserveAspectRatio="xMidYMid meet" xml:space="preserve">
			  <defs>
				<filter id="fe_effect-<?php 
        echo $this->get_id();
        ?>">
				<?php 
        if ($settings['fe_filtereffect'] == 'duotone') {
            // DuoTone
            ?>
				   <!-- Grab the SourceGraphic (implicit) and convert it to grayscale -->
					<feColorMatrix type="matrix" values=".33 .33 .33 0 0
						  .33 .33 .33 0 0
						  .33 .33 .33 0 0
						  0 0 0 1 0">
					</feColorMatrix>

					<!-- Map the grayscale result to the gradient map provided in tableValues -->
					<feComponentTransfer color-interpolation-filters="sRGB">
						<feFuncR type="table" tableValues="<?php 
            echo $color1_R . ' ' . $color2_R;
            ?>"></feFuncR>
						<feFuncG type="table" tableValues="<?php 
            echo $color1_G . ' ' . $color2_G;
            ?>"></feFuncG>
						<feFuncB type="table" tableValues="<?php 
            echo $color1_B . ' ' . $color2_B;
            ?>"></feFuncB>
					</feComponentTransfer>
				  <?php 
        } elseif ($settings['fe_filtereffect'] == 'broken') {
            // Broken
            ?>
					  <feTurbulence type="turbulence" baseFrequency="0.002 0.008" numOctaves="2" seed="2" stitchTiles="stitch" result="turbulence"/>
					  <feColorMatrix type="saturate" values="30" in="turbulence" result="colormatrix"/>
					  <feColorMatrix type="matrix" values="1 0 0 0 0
					0 1 0 0 0
					0 0 1 0 0
					0 0 0 150 -15" in="colormatrix" result="colormatrix1"/>
					  <feComposite in="SourceGraphic" in2="colormatrix1" operator="in" result="composite"/>
					  <feDisplacementMap in="SourceGraphic" in2="colormatrix1" scale="<?php 
            echo $size_broken;
            ?>" xChannelSelector="R" yChannelSelector="A" result="displacementMap"/>

				<?php 
        } elseif ($settings['fe_filtereffect'] == 'rgbOfset') {
            ?>
					  <feFlood flood-color="#FF0000" flood-opacity="0.5" result="RED" />
					  <feFlood flood-color="#00FF00" flood-opacity="0.5" result="GREEN" />
					  <feFlood flood-color="#0000FF" flood-opacity="0.5" result="BLUE" />
					  <feComposite operator="in" in="RED" in2="SourceAlpha" result="RED_TEXT"/>
					  <feComposite operator="in" in="GREEN" in2="SourceAlpha" result="GREEN_TEXT"/>
					  <feComposite operator="in" in="BLUE" in2="SourceAlpha" result="BLUE_TEXT"/>
					  <feOffset in="RED_TEXT" dx="-15" dy="0" result="RED_TEXT_OFF"/>
					  <feOffset in="GREEN_TEXT" dx="15" dy="0"  result="GREEN_TEXT_OFF"/>
					  <feOffset in="BLUE_TEXT" dx="0" dy="0"  result="BLUE_TEXT_OFF"/>
					  <feMerge>
							  <feMergeNode in="RED_TEXT_OFF" />
							  <feMergeNode in="GREEN_TEXT_OFF"/>
							  <feMergeNode in="BLUE_TEXT_OFF"/>
					  </feMerge>

				<?php 
        } elseif ($settings['fe_filtereffect'] == 'squiggly') {
            ?>
						   <feTurbulence id="turbulence" baseFrequency="<?php 
            echo $fe_baseFrequency;
            ?>" numOctaves="<?php 
            echo $fe_numOctaves;
            ?>" result="noise" seed="<?php 
            echo $fe_seed;
            ?>" type="<?php 
            echo $fe_turbulencetype;
            ?>" />
						   <feDisplacementMap id="spostamento" in = "SourceGraphic" in2="noise" scale="<?php 
            echo $fe_scale;
            ?>" />
				<?php 
        } elseif ($settings['fe_filtereffect'] == 'sketch') {
            ?>
					<feMorphology operator="dilate" radius="<?php 
            echo $stroke_sketch;
            ?>" in="SourceAlpha" result="morphology"/>
					<feFlood flood-color="<?php 
            echo $color_sketch;
            ?>" flood-opacity="1" result="flood"/>
					<feComposite in="flood" in2="morphology" operator="in" result="composite"/>
					<feComposite in="composite" in2="SourceAlpha" operator="out" result="composite1"/>
					<feTurbulence type="fractalNoise" baseFrequency="<?php 
            echo $turbY_sketch . ' ' . $turbX_sketch;
            ?>" numOctaves="1" seed="0" stitchTiles="stitch" result="turbulence"/>
					<feDisplacementMap in="composite1" in2="turbulence" scale="<?php 
            echo $scale_sketch;
            ?>" xChannelSelector="A" yChannelSelector="A" result="displacementMap"/>

					<feMerge result="merge">
						<feMergeNode in="SourceGraphic" result="mergeNode"/>
						<feMergeNode in="displacementMap" result="mergeNode1"/>
					</feMerge>
				<?php 
        } elseif ($settings['fe_filtereffect'] == 'x-rays') {
            ?>
					<feColorMatrix type="matrix"
								 values="1 0 0 0 0
										1 0 0 0 0
										1 0 0 0 0
										0 0 0 1 0"
								 x="0%" y="0%" width="100%" height="100%" in="SourceGraphic" result="colormatrix"/>
					<feComponentTransfer x="0%" y="0%" width="100%" height="100%" in="colormatrix" result="componentTransfer">
						<feFuncR type="table" tableValues="0.98 0.3 0.25"/>
						<feFuncG type="table" tableValues="1 0.44 0.24"/>
						<feFuncB type="table" tableValues="0.91 0.62 0.39"/>
						<feFuncA type="table" tableValues="0 1"/>
					</feComponentTransfer>
					<feBlend mode="normal" in="componentTransfer" in2="SourceGraphic" result="blend"/>

				<?php 
        } elseif ($settings['fe_filtereffect'] == 'morphology') {
            if ($settings['operator_dilateerode']) {
                ?>
					  <feMorphology operator="dilate" radius="<?php 
                echo $dilateerode;
                ?>"> </feMorphology>
					<?php 
            } else {
                ?>
					  <feMorphology operator="erode" radius="<?php 
                echo $dilateerode;
                ?>"> </feMorphology>
					<?php 
            }
            ?>
				<?php 
        } elseif ($settings['fe_filtereffect'] == 'posterize') {
            ?>

				  <feComponentTransfer result="poster">
					<feFuncR type="discrete" tableValues="<?php 
            echo $posterR;
            ?>" />
					<feFuncG type="discrete" tableValues="<?php 
            echo $posterG;
            ?>" />
					<feFuncB type="discrete" tableValues="<?php 
            echo $posterB;
            ?>" />
				  </feComponentTransfer>

					<?php 
            if ($desatureposter) {
                ?>
					<feColorMatrix type="saturate" values="0" in="poster" result="saturate"/>
				  <?php 
            }
            ?>
				<?php 
        } elseif ($settings['fe_filtereffect'] == 'pixelate') {
            ?>
					<feFlood x="4" y="4" height="2" width="2"/>
					<feComposite width="10" height="10"/>
					<feTile result="a"/>
					<feComposite in="SourceGraphic" in2="a" operator="in"/>
					<feMorphology operator="dilate" radius="5"/>
				<?php 
        } elseif ($settings['fe_filtereffect'] == 'glitch') {
            ?>

					<feColorMatrix in="SourceGraphic" mode="matrix" values="1 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 1 0" result="r" />

					<feOffset in="r" result="r" dx="-<?php 
            echo $size_glitch;
            ?>" dy="0">
					  <animate attributeName="dx" attributeType="XML" values="-1; .5; 3; -2; .4; .5; 2; 1; -.5; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0;" dur=".5s" repeatCount="indefinite"/>
					  <animate attributeName="dy" attributeType="XML" values="2; -1; .4; 2; 1; 3; -.5; 2; 1; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0;" dur="1s" repeatCount="indefinite"/>
					</feOffset>

					<feColorMatrix in="SourceGraphic" mode="matrix" values="0 0 0 0 0  0 1 0 0 0  0 0 0 0 0  0 0 0 1 0" result="g"/>

					<feOffset in="g" result="g" dx="-<?php 
            echo $size_glitch;
            ?>" dy="0">
					  <animate attributeName="dx" attributeType="XML" values="0; 0; 0; 0; 0; 0; 0; 0; 2; -1; .4; 2; 1; 3; -.5; 2; 1; 0; 0; 0; 0; 0; 0; 0; 0;" dur="1.5s" repeatCount="indefinite"/>
					  <animate attributeName="dy" attributeType="XML" values="0; 0; 0; 0; 0; 0; 0; 0; -1; .5; 3; -2; .4; .5; 2; 1; -.5; 0; 0; 0; 0; 0; 0; 0; 0;" dur="1s" repeatCount="indefinite"/>
					</feOffset>

					<feColorMatrix in="SourceGraphic" mode="matrix" values="0 0 0 0 0  0 0 0 0 0  0 0 1 0 0  0 0 0 1 0" result="b"/>

					<feOffset in="b" result="b" dx="<?php 
            echo $size_glitch;
            ?>" dy="0">
					  <animate attributeName="dx" attributeType="XML" values="0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 2; -1; .4; 2; 1; 3; -.5; 2; 1;" dur="0.35s" repeatCount="indefinite"/>
					  <animate attributeName="dy" attributeType="XML" values="0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; -1; .5; 3; -2; .4; .5; 2; 1; -.5;" dur="0.35s" repeatCount="indefinite"/>
					</feOffset>

					<feBlend in="r" in2="g" mode="screen" result="blend" />

					<feBlend in="blend" in2="b" mode="screen" result="blend" />
				<?php 
        } elseif ($settings['fe_filtereffect'] == 'glitch_shadow') {
            ?>

						<feGaussianBlur in="SourceAlpha" stdDeviation="0" result="blur"></feGaussianBlur>
					  <feOffset dx="0" dy="0" result="offsetblur">
						<animate
						  attributeName="dx"
						  from="0"
						  to="0"
						  begin="0s"
						  dur="0.1s"
						  repeatCount="indefinite"
						  values        = "-5;-2;-2;-2;5;0"
						  keyTimes      = "0;0.125;0.275;0.625;0.875;1"
						></animate>
						<animate
						  attributeName="dy"
						  from="0"
						  to="0"
						  begin="0s"
						  dur="0.1s"
						  repeatCount="indefinite"
						  values        = "1;1.5;3;1.7;-1.7;0"
						  keyTimes      = "0;0.125;0.275;0.625;0.875;1"
						></animate>
					  </feOffset>
					  <feOffset dx="60" dy="-12" result="offsetblur2" in="blur">
						<animate
						  attributeName="dx"
						  from="0"
						  to="0"
						  begin="0s"
						  dur="0.1s"
						  repeatCount="indefinite"
						  values        = "0;5;-2;-2;-2;-5"
						  keyTimes      = "0;0.125;0.275;0.625;0.875;1"
						></animate>
						<animate
						  attributeName="dy"
						  from="0"
						  to="0"
						  begin="0s"
						  dur="0.1s"
						  repeatCount="indefinite"
						  values        = "0;-1.7;1.7;-3;1.5;1"
						  keyTimes      = "0;0.125;0.275;0.625;0.875;1"
						></animate>
					  </feOffset>
					  <feComponentTransfer result="shadow1" in="offsetblur">
						<feFuncA type="linear" slope=".8"></feFuncA>
						<feFuncR type="discrete" tableValues="0"></feFuncR>/>
						<feFuncG type="discrete" tableValues="1"></feFuncG>/>
						<feFuncB type="discrete" tableValues="1"></feFuncB>/>
					  </feComponentTransfer>
					  <feComponentTransfer result="shadow2" in="offsetblur2">
						<feFuncA type="linear" slope=".8"></feFuncA>/>
						<feFuncR type="discrete" tableValues="1"></feFuncR>/>
						<feFuncG type="discrete" tableValues="0"></feFuncG>/>
						<feFuncB type="discrete" tableValues="1"></feFuncB>/>
					  </feComponentTransfer>
					  <feMerge>
						<feMergeNode in="shadow1"></feMergeNode>/>
						<feMergeNode in="shadow2"></feMergeNode>/>
						<feMergeNode in="SourceGraphic"></feMergeNode>/>
					  </feMerge>

					<?php 
        }
        ?>
			  </filter>
			</defs>

			<image id="img-distorted"
				<?php 
        echo $abbondanza;
        ?>
				xlink:href="<?php 
        echo $image_url;
        ?>"
				<?php 
        echo $filterId;
        ?> />

			<style>
			  <?php 
        if ($settings['fe_output'] && $settings['id_svg_class'] != '') {
            if (!$settings['fe_output_direct']) {
                ?>
				  .<?php 
                echo $id_svg_class;
                ?> img,
				  .<?php 
                echo $id_svg_class;
                ?> p,
				  .<?php 
                echo $id_svg_class;
                ?> svg > img,
				  .<?php 
                echo $id_svg_class;
                ?> svg path,
				  .<?php 
                echo $id_svg_class;
                ?> svg polyline,
				  .<?php 
                echo $id_svg_class;
                ?> .elementor-heading-title,
				  .<?php 
                echo $id_svg_class;
                ?> .elementor-icon i:before,
				  .<?php 
                echo $id_svg_class;
                ?> .elementor-button
					<?php 
            } else {
                echo '.' . $id_svg_class;
            }
            ?>
				{
				  -webkit-filter: url(#fe_effect-<?php 
            echo $widgetId;
            ?>);
				  filter: url(#fe_effect-<?php 
            echo $widgetId;
            ?>);

				}
				#dce-svg-<?php 
            echo $widgetId;
            ?> {
					position: absolute;
					width: 0;
					height: 0;
				}
			   <?php 
        }
        ?>
			</style>
		</svg>
		  </svg>
		</div>
	  </div>
		<?php 
    }
    protected function content_template()
    {
        ?>
	  <#
	  var idWidget = id;
	  var iFrameDOM = jQuery("iframe#elementor-preview-iframe").contents();
	  var scope = iFrameDOM.find('.elementor-element[data-id='+idWidget+']');
	  var id_svg_class = settings.id_svg_class;

	  var viewBoxW = settings.viewbox_width;
	  var viewBoxH = settings.viewbox_height;
	  var fe_output = settings.fe_output;
	  var fe_output_direct = settings.fe_output_direct;
	  var maxWidth = settings.image_max_width.size;
	  var baseImage = settings.base_image.url;


	  // SQUIGGLY
	  var fe_baseFrequency = settings.fe_baseFrequency.size || 0;
	  var fe_numOctaves = settings.fe_numOctaves.size || 0;
	  var fe_seed = settings.fe_seed.size || 0;
	  var fe_scale = settings.fe_scale.size || 0;
	  var fe_turbulencetype = settings.fe_turbulencetype;

	  // BROKEN
	  var size_broken = settings.size_broken.size;

	  // GLITCH
	  var size_glitch = settings.size_glitch.size;

	  // DUOTONE
	  var duocolor1 = settings.fe_color1;
	  var duocolor2 = settings.fe_color2;

	  var hex_to_rgb_color1 = hexToRgb(duocolor1);
	  var hex_to_rgb_color2 = hexToRgb(duocolor2);

	  var color1_R = hex_to_rgb_color1[0]/255;
	  var color1_G = hex_to_rgb_color1[1]/255;
	  var color1_B = hex_to_rgb_color1[2]/255;

	  var color2_R = hex_to_rgb_color2[0]/255;
	  var color2_G = hex_to_rgb_color2[1]/255;
	  var color2_B = hex_to_rgb_color2[2]/255;


	  // MORPHOLOGY
	  var operator_dilateerode = settings.operator_dilateerode;
	  var radius_dilateerode = settings.radius_dilateerode.size;

	  // SKETCH
	  var turbX_sketch = settings.sketchX.size;
	  var turbY_sketch = settings.sketchY.size;
	  var scale_sketch = settings.sketchScale.size;
	  var stroke_sketch = settings.sketchStroke.size;
	  var color_sketch = settings.sketchColor;

	  // POSTERIZE
	  var posterR = '';
	  var posterG = '';
	  var posterB = '';

	  if(settings.popover_posterize_R){
		var poster_R_1 = settings.colorposter_R_1.size+' ';
		var poster_R_2 = settings.colorposter_R_2.size+' ';
		var poster_R_3 = settings.colorposter_R_3.size+' ';
		var poster_R_4 = settings.colorposter_R_4.size+' ';
		var poster_R_5 = settings.colorposter_R_5.size;

		posterR = poster_R_1+poster_R_2+poster_R_3+poster_R_4+poster_R_5;
	  }else{
		posterR = '0 0.25 0.5 0.75 1';
	  }
	  if(settings.popover_posterize_G){
		var poster_G_1 = settings.colorposter_G_1.size+' ';
		var poster_G_2 = settings.colorposter_G_2.size+' ';
		var poster_G_3 = settings.colorposter_G_3.size+' ';
		var poster_G_4 = settings.colorposter_G_4.size+' ';
		var poster_G_5 = settings.colorposter_G_5.size;

		posterG = poster_G_1+poster_G_2+poster_G_3+poster_G_4+poster_G_5;
	  }else{
		posterG = '0 0.25 0.5 0.75 1';
	  }
	  if(settings.popover_posterize_B){
		var poster_B_1 = settings.colorposter_B_1.size+' ';
		var poster_B_2 = settings.colorposter_B_2.size+' ';
		var poster_B_3 = settings.colorposter_B_3.size+' ';
		var poster_B_4 = settings.colorposter_B_4.size+' ';
		var poster_B_5 = settings.colorposter_B_5.size;

		posterB = poster_B_1+poster_B_2+poster_B_3+poster_B_4+poster_B_5;
	  }else{
		posterB = '0 0.25 0.5 0.75 1';
	  }
	  var desatureposter = settings.desatureposter;
	  // ------------------------------------------------------------
	  var fe_filtereffect = settings.fe_filtereffect;
	  var filterId = '';
	  if( fe_filtereffect != '' ) filterId = 'filter=url(#fe_effect-'+idWidget+')';



	  var image = {
		id: settings.base_image.id,
		url: settings.base_image.url,
		size: settings.image_size,
		dimension: settings.image_custom_dimension,
		model: view.getEditModel()
	  };
	  var image_url = elementor.imagesManager.getImageUrl( image );


	  if ( ! image_url ) {
		return;
	  }

	  view.addRenderAttribute( {
		'svgfeeffects' : {
		  'class' : [
			'dce_fe-effects',
		  ],
		  'data-coef' : [
			0.5,
		  ],
		},
	  });

	  var preserveAR = '';
	  if(!settings.preserveAR) preserveAR = ' preserveAspectRatio=none';

	  var abbondanza = 'x=0 y=0 width=100% height=100%';

	  function hexToRgb(h)
	  {
		  var r = parseInt((cutHex(h)).substring(0,2),16), g = parseInt((cutHex(h)).substring(2,4),16), b = parseInt((cutHex(h)).substring(4,6),16);
		  return [r,g,b];
	  }
	  function cutHex(h) {return (h.charAt(0)=="#") ? h.substring(1,7):h}


	  #>
	  <div class="dce_fe_effects-wrapper">

		<div {{{ view.getRenderAttributeString( 'svgfeeffects') }}}>

		  <svg id="dce-svg-{{idWidget}}" class="dce-svg-fe_filtereffect" width="100%" height="100%" viewBox="0 0 {{viewBoxW}} {{viewBoxH}}" preserveAspectRatio="xMidYMid meet" xml:space="preserve">
			  <defs>
			   <filter id="fe_effect-{{idWidget}}">
				<# if(fe_filtereffect == 'duotone'){ #>

					<!-- Grab the SourceGraphic (implicit) and convert it to grayscale -->
					<feColorMatrix type="matrix" values=".33 .33 .33 0 0
						  .33 .33 .33 0 0
						  .33 .33 .33 0 0
						  0 0 0 1 0">
					</feColorMatrix>

					<!-- Map the grayscale result to the gradient map provided in tableValues -->
					<feComponentTransfer color-interpolation-filters="sRGB">
						<feFuncR type="table" tableValues="{{color1_R}} {{color2_R}}"></feFuncR>
						<feFuncG type="table" tableValues="{{color1_G}} {{color2_G}}"></feFuncG>
						<feFuncB type="table" tableValues="{{color1_B}} {{color2_B}}"></feFuncB>
					</feComponentTransfer>
				  <# } else if(fe_filtereffect == 'broken'){ #>
			<AnimFeTurbulence type="fractalNoise" baseFrequency={freq} numOctaves="1.5" result="TURB" seed="8" />
			<AnimFeDisplacementMap xChannelSelector="R" yChannelSelector="G" in="SourceGraphic" in2="TURB" result="DISP" scale={scale} />

				  <# } else if(fe_filtereffect == 'rgbOfset'){ #>
					  <feFlood flood-color="#FF0000" flood-opacity="0.5" result="RED" />
					  <feFlood flood-color="#00FF00" flood-opacity="0.5" result="GREEN" />
					  <feFlood flood-color="#0000FF" flood-opacity="0.5" result="BLUE" />
					  <feComposite operator="in" in="RED" in2="SourceAlpha" result="RED_TEXT"/>
					  <feComposite operator="in" in="GREEN" in2="SourceAlpha" result="GREEN_TEXT"/>
					  <feComposite operator="in" in="BLUE" in2="SourceAlpha" result="BLUE_TEXT"/>
					  <feOffset in="RED_TEXT" dx="-15" dy="0" result="RED_TEXT_OFF"/>
					  <feOffset in="GREEN_TEXT" dx="15" dy="0"  result="GREEN_TEXT_OFF"/>
					  <feOffset in="BLUE_TEXT" dx="0" dy="0"  result="BLUE_TEXT_OFF"/>
					  <feMerge>
							  <feMergeNode in="RED_TEXT_OFF" />
							  <feMergeNode in="GREEN_TEXT_OFF"/>
							  <feMergeNode in="BLUE_TEXT_OFF"/>
					  </feMerge>
				  <# } else if(fe_filtereffect == 'squiggly'){ #>
						   <feTurbulence id="turbulence" baseFrequency = "{{fe_baseFrequency}}" numOctaves = "{{fe_numOctaves}}" result = "noise" seed="{{fe_seed}}" type="{{fe_turbulencetype}}" />
						   <feDisplacementMap id = "spostamento" in = "SourceGraphic" in2="noise" scale = "{{fe_scale}}" />
				  <# } else if(fe_filtereffect == 'sketch'){ #>

					<feMorphology operator="dilate" radius="{{stroke_sketch}}" in="SourceAlpha" result="morphology"/>
					<feFlood flood-color="{{color_sketch}}" flood-opacity="1" result="flood"/>
					<feComposite in="flood" in2="morphology" operator="in" result="composite"/>
					<feComposite in="composite" in2="SourceAlpha" operator="out" result="composite1"/>
					<feTurbulence type="fractalNoise" baseFrequency="{{turbY_sketch}} {{turbX_sketch}}" numOctaves="1" seed="0" stitchTiles="stitch" result="turbulence"/>
					<feDisplacementMap in="composite1" in2="turbulence" scale="{{scale_sketch}}" xChannelSelector="A" yChannelSelector="A" result="displacementMap"/>
					<feMerge result="merge">
					  <feMergeNode in="SourceGraphic" result="mergeNode"/>
					  <feMergeNode in="displacementMap" result="mergeNode1"/>
					</feMerge>
				  <# } else if(fe_filtereffect == 'x-rays'){ #>
				  <feColorMatrix type="matrix"
				   values="1 0 0 0 0
							1 0 0 0 0
							1 0 0 0 0
							0 0 0 1 0"
					x="0%" y="0%" width="100%" height="100%" in="SourceGraphic" result="colormatrix"/>
				  <feComponentTransfer x="0%" y="0%" width="100%" height="100%" in="colormatrix" result="componentTransfer">
						<feFuncR type="table" tableValues="0.98 0.3 0.25"/>
						<feFuncG type="table" tableValues="1 0.44 0.24"/>
						<feFuncB type="table" tableValues="0.91 0.62 0.39"/>
						<feFuncA type="table" tableValues="0 1"/>
				  </feComponentTransfer>
				  <feBlend mode="normal" in="componentTransfer" in2="SourceGraphic" result="blend"/>

				  <# } else if(fe_filtereffect == 'morphology'){
					  if(operator_dilateerode){
				  #>
					   <feMorphology operator="dilate" radius="{{radius_dilateerode}}"> </feMorphology>
				  <# }else{ #>

					  <feMorphology operator="erode" radius="{{radius_dilateerode}}"> </feMorphology>
				   <#
					}
				  }else if(fe_filtereffect == 'posterize'){ #>
					  <feComponentTransfer result="poster">
						  <!-- <feFuncR type="discrete" tableValues="0 .5 1"/> .25 .4 .5 .75 1-->
						<feFuncR type="discrete" tableValues="{{posterR}}" />
						<feFuncG type="discrete" tableValues="{{posterG}}" />
						<feFuncB type="discrete" tableValues="{{posterB}}" />
					  </feComponentTransfer>
					 <# if( desatureposter ){ #>
					<feColorMatrix type="saturate" values="0" in="poster" result="saturate"/>
					<# } #>
				  <# }else if(fe_filtereffect == 'pixelate'){ #>
					<feFlood x="4" y="4" height="2" width="2"/>
					<feComposite width="10" height="10"/>
					<feTile result="a"/>
					<feComposite in="SourceGraphic" in2="a" operator="in"/>
					<feMorphology operator="dilate" radius="5"/>
				  <# }else if(fe_filtereffect == 'glitch'){ #>

						<feColorMatrix in="SourceGraphic" mode="matrix" values="1 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 1 0" result="r" />

						<feOffset in="r" result="r" dx="-{{size_glitch}}" dy="0">
						  <animate attributeName="dx" attributeType="XML" values="-1; .5; 3; -2; .4; .5; 2; 1; -.5; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0;" dur=".5s" repeatCount="indefinite"/>
						  <animate attributeName="dy" attributeType="XML" values="2; -1; .4; 2; 1; 3; -.5; 2; 1; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0;" dur="1s" repeatCount="indefinite"/>
						</feOffset>

						<feColorMatrix in="SourceGraphic" mode="matrix" values="0 0 0 0 0  0 1 0 0 0  0 0 0 0 0  0 0 0 1 0" result="g"/>

						<feOffset in="g" result="g" dx="-{{size_glitch}}" dy="0">
						  <animate attributeName="dx" attributeType="XML" values="0; 0; 0; 0; 0; 0; 0; 0; 2; -1; .4; 2; 1; 3; -.5; 2; 1; 0; 0; 0; 0; 0; 0; 0; 0;" dur="1.5s" repeatCount="indefinite"/>
						  <animate attributeName="dy" attributeType="XML" values="0; 0; 0; 0; 0; 0; 0; 0; -1; .5; 3; -2; .4; .5; 2; 1; -.5; 0; 0; 0; 0; 0; 0; 0; 0;" dur="1s" repeatCount="indefinite"/>
						</feOffset>

						<feColorMatrix in="SourceGraphic" mode="matrix" values="0 0 0 0 0  0 0 0 0 0  0 0 1 0 0  0 0 0 1 0" result="b"/>

						<feOffset in="b" result="b" dx="{{size_glitch}}" dy="0">
						  <animate attributeName="dx" attributeType="XML" values="0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 2; -1; .4; 2; 1; 3; -.5; 2; 1;" dur="0.35s" repeatCount="indefinite"/>
						  <animate attributeName="dy" attributeType="XML" values="0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; -1; .5; 3; -2; .4; .5; 2; 1; -.5;" dur="0.35s" repeatCount="indefinite"/>
						</feOffset>

						<feBlend in="r" in2="g" mode="screen" result="blend" />

						<feBlend in="blend" in2="b" mode="screen" result="blend" />
				  <# }else if(fe_filtereffect == 'glitch_shadow'){ #>

						<feGaussianBlur in="SourceAlpha" stdDeviation="0" result="blur"></feGaussianBlur>
						  <feOffset dx="0" dy="0" result="offsetblur">
							<animate
							  attributeName="dx"
							  from="0"
							  to="0"
							  begin="0s"
							  dur="0.1s"
							  repeatCount="indefinite"
							  values        = "-5;-2;-2;-2;5;0"
							  keyTimes      = "0;0.125;0.275;0.625;0.875;1"
							></animate>
							<animate
							  attributeName="dy"
							  from="0"
							  to="0"
							  begin="0s"
							  dur="0.1s"
							  repeatCount="indefinite"
							  values        = "1;1.5;3;1.7;-1.7;0"
							  keyTimes      = "0;0.125;0.275;0.625;0.875;1"
							></animate>
						  </feOffset>
						  <feOffset dx="60" dy="-12" result="offsetblur2" in="blur">
							<animate
							  attributeName="dx"
							  from="0"
							  to="0"
							  begin="0s"
							  dur="0.1s"
							  repeatCount="indefinite"
							  values        = "0;5;-2;-2;-2;-5"
							  keyTimes      = "0;0.125;0.275;0.625;0.875;1"
							></animate>
							<animate
							  attributeName="dy"
							  from="0"
							  to="0"
							  begin="0s"
							  dur="0.1s"
							  repeatCount="indefinite"
							  values        = "0;-1.7;1.7;-3;1.5;1"
							  keyTimes      = "0;0.125;0.275;0.625;0.875;1"
							></animate>
						  </feOffset>
						  <feComponentTransfer result="shadow1" in="offsetblur">
							<feFuncA type="linear" slope=".8"></feFuncA>
							<feFuncR type="discrete" tableValues="0"></feFuncR>/>
							<feFuncG type="discrete" tableValues="1"></feFuncG>/>
							<feFuncB type="discrete" tableValues="1"></feFuncB>/>
						  </feComponentTransfer>
						  <feComponentTransfer result="shadow2" in="offsetblur2">
							<feFuncA type="linear" slope=".8"></feFuncA>/>
							<feFuncR type="discrete" tableValues="1"></feFuncR>/>
							<feFuncG type="discrete" tableValues="0"></feFuncG>/>
							<feFuncB type="discrete" tableValues="1"></feFuncB>/>
						  </feComponentTransfer>
						  <feMerge>
							<feMergeNode in="shadow1"></feMergeNode>/>
							<feMergeNode in="shadow2"></feMergeNode>/>
							<feMergeNode in="SourceGraphic"></feMergeNode>/>
						  </feMerge>

				  <# } #>
				  </filter>
			  </defs>
				<image id="img-distorted"
					  {{abbondanza}}
					  xlink:href="{{image_url}}"
					  {{filterId}} />

			 <style>

				<# if( fe_output && id_svg_class != '' ){
					if( fe_output_direct == '' ){
					#>
				.{{id_svg_class}} svg > image,
				.{{id_svg_class}} svg > p,
				.{{id_svg_class}} svg > path,
				.{{id_svg_class}} svg > polyline,
				.{{id_svg_class}} img,
				.{{id_svg_class}} .elementor-heading-title,
				.{{id_svg_class}} .elementor-icon i:before,
				.{{id_svg_class}} .elementor-button
				<# }else{ #>
				.{{id_svg_class}}
				<# } #>
				{
					-webkit-filter: url(#fe_effect-{{idWidget}});
					filter: url(#fe_effect-{{idWidget}});
				}
				#dce-svg-{{idWidget}}{
					position: absolute;
					width: 0;
					height: 0;
				}
				<# } #>
			</style>
		  </svg>

		 </div>
	   </div>
	   <#
	   jQuery(window).on( 'load', function() {

		});

	   #>
		<?php 
    }
    protected function hex2rgb($colour)
    {
        if ($colour[0] == '#') {
            $colour = \substr($colour, 1);
        }
        if (\strlen($colour) == 6) {
            list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
        } elseif (\strlen($colour) == 3) {
            list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
        } else {
            return \false;
        }
        $r = \hexdec($r);
        $g = \hexdec($g);
        $b = \hexdec($b);
        return array('red' => $r, 'green' => $g, 'blue' => $b);
    }
}
