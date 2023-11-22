<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class EnhancedMultiStep extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    private $is_common = \false;
    public $has_action = \false;
    public function get_name()
    {
        return 'dce_form_step';
    }
    public function get_label()
    {
        return __('Form Steps', 'dynamic-content-for-elementor');
    }
    protected function add_actions()
    {
        add_action('elementor/frontend/widget/before_render', array($this, 'start_element'));
        add_action('elementor/widget/render_content', array($this, '_render_form'), 10, 2);
        add_action('elementor/frontend/widget/after_render', array($this, 'end_element'));
        add_action('elementor/element/form/section_steps_settings/before_section_end', [$this, 'add_control_section_to_steps'], 10, 2);
        add_action('elementor/element/form/section_form_style/after_section_end', [$this, 'add_control_section_to_form'], 10, 2);
        add_action('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
    }
    public function start_element($widget = \false)
    {
        if ('form' === $widget->get_name()) {
            $settings = $widget->get_settings_for_display();
            if ('none' === $settings['step_type']) {
                \ob_start();
            }
        }
    }
    public function end_element($widget = \false)
    {
        if ('form' === $widget->get_name()) {
            $settings = $widget->get_settings_for_display();
            if ('none' === $settings['step_type']) {
                $content = \ob_get_clean();
                // Fix Indicators
                $content = \str_replace('"step_type":"none"', '"step_type":"text"', $content);
                $content = \str_replace('&quot;step_type&quot;:&quot;none&quot;', '&quot;step_type&quot;:&quot;text&quot;', $content);
                $content .= '<style>.elementor .elementor-element.elementor-element-' . $widget->get_id() . ' .e-form__indicators { display: none !important; }</style>';
                echo $content;
            }
        }
    }
    public function add_control_section_to_steps($element, $args)
    {
        $element->add_control('dce_step_legend', ['label' => '<span class="color-dce icon-dyn-logo-dce"></span> ' . __('Use Label as Legend', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER]);
        $element->add_control('dce_step_show', ['label' => '<span class="color-dce icon-dyn-logo-dce"></span> ' . __('Show All steps', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER]);
        $element->add_control('dce_step_scroll', ['label' => '<span class="color-dce icon-dyn-logo-dce"></span> ' . __('Scroll to Top on Step change', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'condition' => ['dce_step_show' => '']]);
        $element->add_control('dce_step_summary', ['label' => '<span class="color-dce icon-dyn-logo-dce"></span> ' . __('Step Summary', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['dce_step_show' => '']]);
        $element->add_control('dce_step_summary_submit_btn_text', ['label' => __('Summary Submit Button', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('Submit Form', 'dynamic-content-for-elementor'), 'condition' => ['dce_step_show' => '', 'dce_step_summary!' => '']]);
    }
    public function add_control_section_to_form($element, $args)
    {
        $element->start_controls_section('dce_step_section_style', ['label' => '<span class="color-dce icon-dyn-logo-dce pull-right ml-1"></span> ' . __('Steps Legend', 'dynamic-content-for-elementor'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE, 'condition' => ['dce_step_legend!' => '']]);
        $element->add_responsive_control('dce_step_title_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} .dce-form-step legend' => 'display: block; text-align: {{VALUE}};']]);
        $element->add_control('dce_step_title_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-form-step legend' => 'color: {{VALUE}};']]);
        $element->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_step_title_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-form-step legend']);
        $element->add_control('dce_step_title_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['min' => -50, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-form-step legend' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);
        $element->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'dce_step_text_shadow', 'selector' => '{{WRAPPER}} .dce-form-step legend']);
        $element->end_controls_section();
        // SUMMARY
        $element->start_controls_section('dce_step_section_summary', ['label' => '<span class="color-dce icon-dyn-logo-dce pull-right ml-1"></span> ' . __('Steps Summary', 'dynamic-content-for-elementor'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE, 'condition' => ['dce_step_summary!' => '', 'dce_step_show' => '']]);
        $element->add_control('dce_step_summary_title', ['label' => __('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING]);
        $element->add_responsive_control('dce_step_summary_title_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} .dce-step-summary-title' => 'text-align: {{VALUE}};']]);
        $element->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_step_summary_title_typography', 'selector' => '{{WRAPPER}} .dce-step-summary-title']);
        $element->add_control('dce_step_summary_title_color', ['label' => __('Title Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-step-summary-title' => 'color: {{VALUE}};']]);
        $element->add_responsive_control('dce_step_summary_title_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-step-summary-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $element->add_control('dce_step_summary_step', ['label' => __('Step', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $element->add_responsive_control('dce_step_summary_step_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .dce-form-step-summary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $element->add_responsive_control('dce_step_summary_step_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'default' => ['top' => '0', 'right' => '0', 'bottom' => '15', 'left' => '0', 'unit' => 'px', 'isLinked' => \false], 'selectors' => ['{{WRAPPER}} .dce-form-step-summary' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $element->add_control('dce_step_summary_step_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-form-step-summary' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $element->start_controls_tabs('dce_step_summary_step_style');
        $element->start_controls_tab('dce_step_summary_step_normal', ['label' => __('Normal', 'dynamic-content-for-elementor')]);
        $element->add_control('dce_step_summary_step_background_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-form-step-summary' => 'background-color: {{VALUE}};']]);
        $element->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_step_summary_step_border', 'selector' => '{{WRAPPER}} .dce-form-step-summary']);
        $element->end_controls_tab();
        $element->start_controls_tab('dce_step_summary_step_filled', ['label' => __('Filled', 'dynamic-content-for-elementor')]);
        $element->add_control('dce_step_summary_step_background_color_filled', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-form-step-summary.dce-step-filled-summary' => 'background-color: {{VALUE}};']]);
        $element->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_step_summary_step_border_filled', 'selector' => '{{WRAPPER}} .dce-form-step-summary.dce-step-filled-summary']);
        $element->end_controls_tab();
        $element->start_controls_tab('dce_step_summary_step_active', ['label' => __('Active', 'dynamic-content-for-elementor')]);
        $element->add_control('dce_step_summary_step_background_color_active', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-form-step-summary.dce-step-active-summary' => 'background-color: {{VALUE}};']]);
        $element->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_step_summary_step_border_active', 'selector' => '{{WRAPPER}} .dce-form-step-summary.dce-step-active-summary']);
        $element->end_controls_tab();
        $element->end_controls_tabs();
        $element->add_control('dce_step_summary_step_text_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'separator' => 'before', 'selectors' => ['{{WRAPPER}} .dce-form-step-summary' => 'color: {{VALUE}};']]);
        $element->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_step_summary_step_text_typography', 'selector' => '{{WRAPPER}} .dce-form-step-summary']);
        $element->add_control('dce_step_summary_step_label_color', ['label' => __('Label Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'separator' => 'before', 'selectors' => ['{{WRAPPER}} .dce-form-summary-field-label' => 'color: {{VALUE}};']]);
        $element->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_step_summary_step_label_typography', 'selector' => '{{WRAPPER}} .dce-form-summary-field-label']);
        $element->add_control('dce_step_summary_step_title_color', ['label' => __('Title Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'separator' => 'before', 'selectors' => ['{{WRAPPER}} .dce-form-summary-step-title' => 'color: {{VALUE}};']]);
        $element->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_step_summary_step_title_typography', 'selector' => '{{WRAPPER}} .dce-form-summary-step-title']);
        $element->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_step_summary_step_title_border', 'selector' => '{{WRAPPER}} .dce-form-summary-step-title']);
        $element->add_responsive_control('dce_step_summary_step_title_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .dce-form-summary-step-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $element->add_responsive_control('dce_step_summary_step_title_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .dce-form-summary-step-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $element->add_control('dce_step_summary_submit_btn', ['label' => __('Submit Button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $element->add_responsive_control('dce_step_summary_submit_btn_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify']], 'default' => '']);
        $element->add_control('dce_step_summary_submit_btn_size', ['label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'sm', 'options' => Helper::get_button_sizes(), 'style_transfer' => \true]);
        $element->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_step_summary_submit_btn_typography', 'selector' => '{{WRAPPER}} .elementor-button.elementor-button-submit']);
        $element->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'dce_step_summary_submit_btn_text_shadow', 'selector' => '{{WRAPPER}} .elementor-button.elementor-button-submit']);
        $element->start_controls_tabs('dce_step_summary_submit_btn_tabs_button_style');
        $element->start_controls_tab('dce_step_summary_submit_btn_tab_button_normal', ['label' => __('Normal', 'dynamic-content-for-elementor')]);
        $element->add_control('dce_step_summary_submit_btn_text_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-submit' => 'fill: {{VALUE}}; color: {{VALUE}};']]);
        $element->add_control('dce_step_summary_submit_btn_background_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-submit' => 'background-color: {{VALUE}};']]);
        $element->end_controls_tab();
        $element->start_controls_tab('dce_step_summary_submit_btn_tab_button_hover', ['label' => __('Hover', 'dynamic-content-for-elementor')]);
        $element->add_control('dce_step_summary_submit_btn_hover_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-submit:hover, {{WRAPPER}} .elementor-button.elementor-button-submit:focus' => 'color: {{VALUE}};', '{{WRAPPER}} .elementor-button.elementor-button-submit:hover svg, {{WRAPPER}} .elementor-button.elementor-button-submit:focus svg' => 'fill: {{VALUE}};']]);
        $element->add_control('dce_step_summary_submit_btn_background_hover_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-submit:hover, {{WRAPPER}} .elementor-button.elementor-button-submit:focus' => 'background-color: {{VALUE}};']]);
        $element->add_control('dce_step_summary_submit_btn_hover_border_color', ['label' => __('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['dce_step_summary_submit_btn_border_border!' => ''], 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-submit:hover, {{WRAPPER}} .elementor-button.elementor-button-submit:focus' => 'border-color: {{VALUE}};']]);
        $element->end_controls_tab();
        $element->end_controls_tabs();
        $element->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_step_summary_submit_btn_border', 'selector' => '{{WRAPPER}} .elementor-button.elementor-button-submit', 'separator' => 'before']);
        $element->add_control('dce_step_summary_submit_btn_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $element->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'dce_step_summary_submit_btn_button_box_shadow', 'selector' => '{{WRAPPER}} .elementor-button.elementor-button-submit']);
        $element->add_responsive_control('dce_step_summary_submit_btn_text_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $element->add_responsive_control('dce_step_summary_submit_btn_text_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $element->end_controls_section();
    }
    public function update_style_controls($widget)
    {
        Helper::update_elementor_control($widget, 'column_gap', function ($control_data) {
            if (isset($control_data['selectors']['{{WRAPPER}} .elementor-field-group'])) {
                $control_data['selectors']['{{WRAPPER}} .elementor-form-steps legend'] = $control_data['selectors']['{{WRAPPER}} .elementor-field-group'];
            }
            $control_data['selectors']['{{WRAPPER}} .elementor-form-steps .elementor-form-fields-wrapper'] = 'margin: 0;';
            return $control_data;
        });
    }
    public function _summary($widget)
    {
        $settings = $widget->get_settings_for_display();
        if (empty($settings['dce_step_summary'])) {
            return '';
        }
        // FIELDS
        $steps = array();
        if (!empty($settings['form_fields'])) {
            foreach ($settings['form_fields'] as $key => $afield) {
                if ($afield['field_type'] == 'step') {
                    $steps[] = $afield;
                }
            }
        }
        $bar = '';
        if (!empty($steps)) {
            $bar .= '<div class="dce-form-summary-wrapper">';
            $bar .= '<h3 class="dce-step-summary-title">' . $settings['form_name'] . '</h3>';
            $bar .= '<ul class="dce-form-summary dce-no-list">';
            foreach ($settings['form_fields'] as $key => $afield) {
                $field_name = $afield['field_label'];
                if (!$field_name) {
                    if (!empty($afield['placeholder'])) {
                        $field_name = $afield['placeholder'];
                    }
                }
                if (!$field_name) {
                    $field_name = $afield['custom_id'];
                }
                if ($afield['field_type'] === 'step') {
                    if ($key) {
                        $bar .= '</ul></li>';
                    }
                    $bar .= '<li id="dce-form-step-' . $afield['custom_id'] . '-summary" class="dce-form-step-summary' . (!$key ? ' dce-step-filled-summary dce-step-active-summary' : '') . '">' . '<h4 class="dce-form-summary-step-title">' . $field_name . '</h4>' . '<ul>';
                } else {
                    if (\in_array($afield['field_type'], array('text', 'textarea', 'select', 'upload', 'radio', 'checkbox', 'email', 'url', 'tel', 'acceptance', 'number', 'date', 'time', 'amount'))) {
                        $bar .= '<li id="dce-summary-form-field-' . $afield['custom_id'] . '" class="dce-form-step-field-summary"><label class="dce-form-summary-field-label">' . $field_name . ':</label> <span class="dce-form-summary-field-value" id="dce-summary-value-form-field-' . $afield['custom_id'] . '-' . $widget->get_id() . '">' . $afield['field_value'] . '</span></li>';
                    }
                }
            }
            $bar .= '</ul></li>';
            $bar .= '</ul>';
            if (!empty($settings['dce_step_summary_submit_btn_text'])) {
                $bar .= '<div class="elementor-button-wrapper' . (!empty($settings['dce_step_summary_submit_btn_align']) ? ' elementor-align-' . $settings['dce_step_summary_submit_btn_align'] : '') . '">';
                $bar .= '<button class="elementor-button elementor-button-submit elementor-size-' . $settings['dce_step_summary_submit_btn_size'] . '">' . $settings['dce_step_summary_submit_btn_text'] . '</button>';
                $bar .= '</div>';
            }
            $bar .= '</div>';
            wp_enqueue_script('dce-form-summary', plugins_url('/assets/js/dce-form-summary.js', DCE__FILE__), [], DCE_VERSION);
        }
        return $bar;
    }
    public function _render_form($content, $widget)
    {
        $new_content = $content;
        if ($widget->get_name() === 'form') {
            $settings = $widget->get_settings_for_display();
            // FIELDS
            $steps = array();
            if (!empty($settings['form_fields'])) {
                foreach ($settings['form_fields'] as $key => $afield) {
                    if (!$key && $afield['field_type'] !== 'step') {
                        break;
                    }
                    if ($afield['field_type'] === 'step') {
                        $steps[] = $afield;
                    }
                }
            }
            if (!empty($steps)) {
                if (!$settings['dce_step_show']) {
                    $content = \str_replace('class="elementor-form"', 'class="elementor-form elementor-form-steps"', $content);
                }
                $jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_steps';
                $filtered_fields = \array_map(function ($field) {
                    $allowed = ["field_label", "custom_id", "field_type"];
                    $filtered = [];
                    foreach ($field as $key => $value) {
                        if (\in_array($key, $allowed, \true)) {
                            $field[$key] = $value;
                        }
                    }
                    return $field;
                }, $settings['form_fields']);
                $filtered_settings = ['dce_step_scroll' => $settings['dce_step_scroll'], 'form_fields' => $filtered_fields];
                // add custom js
                \ob_start();
                ?>
				<script id="<?php 
                echo $jkey;
                ?>">
					(function ($) {
				<?php 
                if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    ?>
							var <?php 
                    echo $jkey;
                    ?> = function ($scope, $) {
								if ($scope.hasClass("elementor-element-<?php 
                    echo $widget->get_id();
                    ?>")) {
				<?php 
                }
                ?>

								var form_id = '<?php 
                echo $widget->get_id();
                ?>';
								var settings = "<?php 
                echo \addslashes(wp_json_encode($filtered_settings) ?: '');
                ?>";
								settings = JSON.parse(settings);
								var step_last = false;
								window.$form = $('.elementor-element-' + form_id);
								if ( settings['dce_step_scroll'] === 'yes' ) {
									// Scroll to Top on change
									setTimeout(function(){
										let nextButtons = $form.find('.elementor-field-type-next');
										let prevButtons = $form.find('.elementor-field-type-previous');
										let stepButtons = nextButtons.add(prevButtons);
										let formOffset = $form.offset().top - 70;
										stepButtons.on('click', function() {
											$("html, body").animate({ scrollTop: formOffset });
										} );
									}, 200);
								}
								if (settings['form_fields'].length) {

									jQuery(settings.form_fields).each(function (index, afield) {
											if (afield.field_type == 'step') {
												var field = jQuery('.elementor-element-' + form_id + ' .elementor-field-group-' + afield.custom_id);
												field.addClass("dce-form-step");
												field.addClass("dce-form-step" + afield.custom_id);
												field.attr('data-custom_id', afield.custom_id);
												field.attr('id', 'dce-form-step-' + afield.custom_id);

												<?php 
                if ($settings['dce_step_legend']) {
                    ?>
													// Legend
													field.prepend('<legend class="elementor-step-legend elementor-column elementor-col-100">' + afield.field_label + '</legend>');
												<?php 
                }
                ?>
											}
									});
								}
								<?php 
                if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    ?>
								}
							};
							$(window).on('elementor/frontend/init', function () {
								elementorFrontend.hooks.addAction('frontend/element_ready/form.default', <?php 
                    echo $jkey;
                    ?>);
							});
								<?php 
                }
                ?>
					})(jQuery, window);
				</script>
				<?php 
                $js = \ob_get_clean();
                $js = \DynamicContentForElementor\Assets::dce_enqueue_script($jkey, $js, $widget->get_id());
                $content .= $js;
                if ($settings['dce_step_show']) {
                    $css = '<style>.elementor-element-' . $widget->get_id() . ' .e-form__buttons__wrapper.elementor-field-type-next, .elementor-element-' . $widget->get_id() . ' .e-form__buttons__wrapper.elementor-field-type-previous { display: none; }
					.elementor-widget-form .dce-form-step { flex-wrap: wrap; max-width: 100%; display: flex; align-content: flex-start; }</style>';
                    $content .= $css;
                }
                $new_content = $content . $this->_summary($widget);
            }
        }
        return $new_content;
    }
}
