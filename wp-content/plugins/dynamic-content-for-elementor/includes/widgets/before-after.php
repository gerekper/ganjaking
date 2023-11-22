<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class BeforeAfter extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-jqueryeventmove-lib', 'dce-twentytwenty-lib', 'dce-before-after'];
    }
    public function get_style_depends()
    {
        return ['dce-before-after'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_5050base', ['label' => $this->get_title()]);
        $this->add_control('before_image', ['label' => __('Before Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'dynamic' => ['active' => \true], 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]]);
        $this->add_control('after_image', ['label' => __('After Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'dynamic' => ['active' => \true], 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]]);
        $this->add_control('orientation', ['label' => __('Orientation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'default' => 'horizontal', 'options' => ['vertical' => ['title' => __('Vertical', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'horizontal' => ['title' => __('Horizontal', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center']], 'frontend_available' => \true]);
        $this->add_control('offset_pict', ['label' => __('Offset', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 50], 'range' => ['px' => ['max' => 100, 'min' => 10, 'step' => 1]], 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_5050settings', ['label' => __('Settings', 'dynamic-content-for-elementor')]);
        $this->add_control('move_slider_on_hover', ['label' => __('Move slider on mouse hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('move_with_handle_only', ['label' => __('Move with handle only', 'dynamic-content-for-elementor'), 'description' => __('Allow the user to swipe anywhere on the image to control slider movement', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('click_to_move', ['label' => __('Click to move', 'dynamic-content-for-elementor'), 'description' => __('Allow the user to click (or tap) anywhere on the image to move the slider to that location', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => __('Yes', 'dynamic-content-for-elementor'), 'label_off' => __('Other', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_5050overlay', ['label' => __('Overlay and Labels', 'dynamic-content-for-elementor')]);
        $this->add_control('no_overlay', ['label' => __('No overlay', 'dynamic-content-for-elementor'), 'description' => __('Don\'t show the overlay with before and after', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('before_label', ['label' => __('Before', 'dynamic-content-for-elementor'), 'description' => __('Set a custom before label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Before', 'dynamic-content-for-elementor'), 'dynamic' => ['active' => \true], 'frontend_available' => \true, 'condition' => ['no_overlay' => '']]);
        $this->add_control('after_label', ['label' => __('After', 'dynamic-content-for-elementor'), 'description' => __('Set a custom after label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('After', 'dynamic-content-for-elementor'), 'dynamic' => ['active' => \true], 'frontend_available' => \true, 'condition' => ['no_overlay' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_handlestyle', ['label' => __('Handle Style', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('handle_border_color', ['label' => __('Handle Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#fff', 'selectors' => ['{{WRAPPER}} .twentytwenty-horizontal #container-afterbefore .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-horizontal #container-afterbefore .twentytwenty-handle:after, {{WRAPPER}} .twentytwenty-vertical #container-afterbefore .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-vertical #container-afterbefore .twentytwenty-handle:after' => 'background-color: {{VALUE}};', '{{WRAPPER}} #container-afterbefore .twentytwenty-handle' => 'border-color: {{VALUE}}']]);
        $this->add_control('handle_fill_color', ['label' => __('Handle Fill Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-handle' => 'background-color: {{VALUE}}']]);
        $this->add_responsive_control('handle_stroke', ['label' => __('Stroke', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 3], 'range' => ['px' => ['max' => 30, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .twentytwenty-horizontal #container-afterbefore .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-horizontal #container-afterbefore .twentytwenty-handle:after' => 'width: {{SIZE}}{{UNIT}}; margin-left: calc(-{{SIZE}}{{UNIT}} / 2);', '{{WRAPPER}} .twentytwenty-vertical #container-afterbefore .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-vertical #container-afterbefore .twentytwenty-handle:after' => 'height: {{SIZE}}{{UNIT}}; margin-top: calc(-{{SIZE}}{{UNIT}} / 2);', '{{WRAPPER}} #container-afterbefore .twentytwenty-handle' => 'border-width: {{SIZE}}{{UNIT}}']]);
        $this->add_responsive_control('circle_stroke', ['label' => __('Circle Stroke', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 30, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-handle' => 'border-width: {{SIZE}}{{UNIT}}']]);
        $this->add_responsive_control('handle_circlewidth', ['label' => __('Circle Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 38], 'range' => ['px' => ['max' => 100, 'min' => 15, 'step' => 1]], 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-handle' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .twentytwenty-vertical #container-afterbefore .twentytwenty-handle::before' => 'margin-left: calc({{SIZE}}{{UNIT}} / 2 - 1px);', '{{WRAPPER}} .twentytwenty-vertical #container-afterbefore .twentytwenty-handle::after' => 'margin-right: calc({{SIZE}}{{UNIT}} / 2 - 1px);', '{{WRAPPER}} .twentytwenty-horizontal #container-afterbefore .twentytwenty-handle:before' => 'margin-bottom: calc({{SIZE}}{{UNIT}} / 2 - 1px);', '{{WRAPPER}} .twentytwenty-horizontal #container-afterbefore .twentytwenty-handle:after' => 'margin-top: calc({{SIZE}}{{UNIT}} / 2 - 1px);']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'handle_boxshadow', 'selector' => '{{WRAPPER}} #container-afterbefore .twentytwenty-handle']);
        $this->add_control('handle_trianglecolor', ['label' => __('Triangle Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#fff', 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-left-arrow' => 'border-right-color: {{VALUE}}', '{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-right-arrow' => 'border-left-color: {{VALUE}}', '{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-up-arrow' => 'border-bottom-color: {{VALUE}}', '{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-down-arrow' => 'border-top-color: {{VALUE}}']]);
        $this->add_responsive_control('handle_trianglesize', ['label' => __('Triangle Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 6], 'range' => ['px' => ['max' => 30, 'min' => 3, 'step' => 1]], 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-left-arrow, {{WRAPPER}}  #container-afterbefore .twentytwenty-handle .twentytwenty-right-arrow, {{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-up-arrow, {{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-down-arrow' => 'border-width: {{SIZE}}{{UNIT}}']]);
        $this->add_responsive_control('handle_triangleposition', ['label' => __('Triangle Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => -15], 'range' => ['px' => ['max' => 50, 'min' => -50, 'step' => 1]], 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-left-arrow' => 'margin-left: {{SIZE}}{{UNIT}}', '{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-right-arrow' => 'margin-right: {{SIZE}}{{UNIT}}', '{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-up-arrow' => 'margin-top: {{SIZE}}{{UNIT}}', '{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-down-arrow' => 'margin-bottom: {{SIZE}}{{UNIT}}']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_overlayandlabels', ['label' => __('Overlay and Labels', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['no_overlay' => '']]);
        $this->add_control('overlay_bg', ['label' => __('Overlay Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(0, 0, 0, 0.5)', 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-overlay:hover' => 'background-color: {{VALUE}};']]);
        $this->add_control('label_color', ['label' => __('Labels Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#fff', 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-before-label:before, {{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-after-label:before' => 'color: {{VALUE}};']]);
        $this->add_control('overlay_label_bg', ['label' => __('Overlay Label Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(#fff, .2)', 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-before-label:before, {{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-after-label:before' => 'background-color: {{VALUE}};']]);
        $this->add_control('overlay_label_padding', ['label' => __('Overlay Label Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-before-label:before, {{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-after-label:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('label_radius', ['label' => __('Labels Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 2], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-before-label:before, {{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-after-label:before' => 'border-radius: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'labels_typography', 'label' => __('Labels Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-before-label:before, {{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-after-label:before']);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $this->add_render_attribute('container', 'id', 'container-afterbefore');
        // Set class twentytwenty-container to avoid Flash of unstyled content (FOUC)
        $this->add_render_attribute('container', 'class', 'twentytwenty-container');
        // Before
        $this->add_render_attribute('before', 'src', $settings['before_image']['url']);
        if (!empty($settings['before_image']['id'])) {
            $this->add_render_attribute('before', 'alt', Helper::get_image_alt($settings['before_image']['id']));
        }
        // After
        $this->add_render_attribute('after', 'src', $settings['after_image']['url']);
        if (!empty($settings['after_image']['id'])) {
            $this->add_render_attribute('after', 'alt', Helper::get_image_alt($settings['after_image']['id']));
        }
        ?>

		<div <?php 
        echo $this->get_render_attribute_string('container');
        ?>>
			<img <?php 
        echo $this->get_render_attribute_string('before');
        ?> />
			<img <?php 
        echo $this->get_render_attribute_string('after');
        ?> />
		</div>
		<?php 
    }
}
