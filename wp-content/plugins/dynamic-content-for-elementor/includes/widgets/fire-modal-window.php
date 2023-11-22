<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class FireModalWindow extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['velocity', 'dce-modalwindow'];
    }
    public function get_style_depends()
    {
        return ['dce-modalWindow'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_modalwindow', ['label' => __('Fire Modal Window', 'dynamic-content-for-elementor')]);
        $this->add_control('text_btn', ['label' => __('Text Button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Open', 'dynamic-content-for-elementor')]);
        $this->add_control('icon', ['label' => __('Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'fa4compatibility' => 'icon_fmw', 'skin' => 'inline', 'label_block' => \false]);
        $this->add_control('icon_fmw_align', ['label' => __('Icon Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => ['left' => __('Before', 'dynamic-content-for-elementor'), 'right' => __('After', 'dynamic-content-for-elementor')], 'condition' => ['icon!' => '']]);
        $this->add_control('space_icon_fmw', ['label' => __('Icon spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-modalwindow-section .icon-left' => 'padding-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-modalwindow-section .icon-right' => 'padding-left: {{SIZE}}{{UNIT}};'], 'condition' => ['icon!' => '']]);
        $this->add_control('template', ['label' => __('Select Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => 'Button', 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_btn', 'selector' => '{{WRAPPER}} .dce-modalwindow-section .cd-modal-action .btn']);
        $this->start_controls_tabs('fmw_btn');
        $this->start_controls_tab('fmw_btn_colors', ['label' => __('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('color_txbtn', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .cd-modal-action .btn' => 'color: {{VALUE}};']]);
        $this->add_control('color_bgbtn', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-modalwindow-section .cd-modal-action .btn, {{WRAPPER}} .dce-modalwindow-section .cd-modal-action .cd-modal-bg' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('fmw_btn_hover', ['label' => __('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('color_txbtn_hover', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-modalwindow-section .cd-modal-action .btn:hover' => 'color: {{VALUE}};']]);
        $this->add_control('color_bgbtn_hover', ['label' => __('Background color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-modalwindow-section .cd-modal-action .btn:hover' => 'background-color: {{VALUE}};']]);
        $this->add_control('hover_animation', ['label' => __('Hover Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_responsive_control('fmw_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'separator' => 'before', 'default' => ['top' => 10, 'right' => 20, 'bottom' => 10, 'left' => 20], 'frontend_available' => \true, 'selectors' => ['{{WRAPPER}} .cd-modal-action .btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('borderradius_btn', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 50, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-modalwindow-section .cd-modal-action .btn' => 'border-radius: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'btn_border', 'label' => __('Button Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .cd-modal-action .btn']);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['label' => 'Button shadow', 'name' => 'btn_box_shadow', 'selector' => '{{WRAPPER}} .cd-modal-action .btn']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_modal', ['label' => 'Modal Window', 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('fmw_modal', ['label' => __('Modal', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'background', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .cd-modal-action .cd-modal-bg.is-visible']);
        $this->add_responsive_control('fmw_modal_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .cd-modal .cd-modal-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('fmw_closebutton', ['label' => __('Close Tutton', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->start_controls_tabs('fmw_button_colors');
        $this->start_controls_tab('fmw_button_text_colors', ['label' => __('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('color_closemodal', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .cd-modal-close .dce-quit-ics:after, {{WRAPPER}} .cd-modal-close .dce-quit-ics:before' => 'background-color: {{VALUE}};']]);
        $this->add_control('fmw_button_background_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .cd-modal-close .dce-quit-ics' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('fmw_button_text_colors_hover', ['label' => __('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('fmw_button_hover_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .cd-modal-close:hover .dce-quit-ics:after, {{WRAPPER}} .cd-modal-close:hover .dce-quit-ics:before' => 'background-color: {{VALUE}};']]);
        $this->add_control('fmw_button_background_hover_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .cd-modal-close .dce-quit-ics:hover' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_responsive_control('buttonsize_closemodal', ['label' => __('Button Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 50, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 20, 'max' => 100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .cd-modal-close .dce-quit-ics' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('weight_closemodal', ['label' => __('Close Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 20, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .cd-modal-close .dce-quit-ics:after, {{WRAPPER}} .cd-modal-close .dce-quit-ics:before' => 'height: {{SIZE}}{{UNIT}}; top: calc(50% - ({{SIZE}}{{UNIT}}/2));']]);
        $this->add_control('size_closemodal', ['label' => __('Close Size (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 60, 'unit' => '%'], 'size_units' => ['%'], 'range' => ['%' => ['min' => 20, 'max' => 200, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .cd-modal-close .dce-quit-ics:after, {{WRAPPER}} .cd-modal-close .dce-quit-ics:before' => 'width: {{SIZE}}{{UNIT}}; left: calc(50% - ({{SIZE}}{{UNIT}}/2));']]);
        $this->add_responsive_control('vertical_close', ['label' => __('Y Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 20, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .cd-modal-close .dce-quit-ics' => 'top: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('horizontal_close', ['label' => __('X Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 20, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .cd-modal-close .dce-quit-ics' => 'right: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        $template = $settings['template'];
        $animation_class = !empty($settings['hover_animation']) ? 'elementor-animation-' . $settings['hover_animation'] : '';
        ?>
		<section class="dce-modalwindow-section">

			<div class="cd-modal-action">
				<a href="#" class="btn <?php 
        echo $animation_class;
        ?>" data-type="modal-trigger">
					<?php 
        if ($settings['icon'] && $settings['icon_fmw_align'] == 'left') {
            Icons_Manager::render_icon($settings['icon'], ['class' => 'icon-' . sanitize_text_field($settings['icon_fmw_align'])]);
        }
        echo $settings['text_btn'];
        if ($settings['icon'] && $settings['icon_fmw_align'] == 'right') {
            Icons_Manager::render_icon($settings['icon'], ['class' => 'icon-' . sanitize_text_field($settings['icon_fmw_align'])]);
        }
        ?>

					</a>
				<span class="cd-modal-bg"></span>
			</div>

			<div class="cd-modal">
				<div class="cd-modal-content">
					<?php 
        if (!empty($template)) {
            echo do_shortcode('[dce-elementor-template id="' . $template . '"]');
        }
        ?>
				</div>
			</div>
			<a href="#" class="cd-modal-close">
				<span class="dce-quit-ics"></span>
			</a>
		</section>
		<?php 
    }
}
