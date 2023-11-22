<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class TextEditorWithTokens extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_tokens', ['label' => __('Text Editor with Tokens', 'dynamic-content-for-elementor')]);
        $this->add_control('text_w_tokens', ['label' => '', 'type' => Controls_Manager::WYSIWYG, 'default' => __('Hello', 'dynamic-content-for-elementor') . ' [user:nicename], ' . __('you are using Elementor', 'dynamic-content-for-elementor') . ' [option:elementor_version]', 'description' => esc_html__('If you add custom html tags, you can use the class `dce-force-style` to apply the style as selected in the Style tab. ', 'dynamic-content-for-elementor'), 'dynamic' => ['active' => \true]]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Text Editor', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'selectors' => ['{{WRAPPER}} .dce-tokens' => 'text-align: {{VALUE}};']]);
        $this->add_control('text_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-tokens' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'typography',
            // With custom HTML you can use the class dce-force-style to
            // try to make this style win over other rules.
            'selector' => '{{WRAPPER}} .dce-tokens .dce-force-style, {{WRAPPER}} .dce-tokens',
        ]);
        $text_columns = \range(1, 10);
        $text_columns = \array_combine($text_columns, $text_columns);
        $text_columns[''] = __('Default', 'dynamic-content-for-elementor');
        $this->add_responsive_control('text_columns', ['label' => __('Columns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'separator' => 'before', 'options' => $text_columns, 'selectors' => ['{{WRAPPER}} .dce-tokens' => 'columns: {{VALUE}};']]);
        $this->add_responsive_control('column_gap', ['label' => __('Columns Gap', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', '%', 'em', 'vw'], 'range' => ['px' => ['max' => 100], '%' => ['max' => 10, 'step' => 0.1], 'vw' => ['max' => 10, 'step' => 0.1], 'em' => ['max' => 10, 'step' => 0.1]], 'selectors' => ['{{WRAPPER}} .dce-tokens' => 'column-gap: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_drop_cap', ['label' => __('Drop Cap', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['drop_cap' => 'yes']]);
        $this->add_control('drop_cap_view', ['label' => __('View', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['default' => __('Default', 'dynamic-content-for-elementor'), 'stacked' => __('Stacked', 'dynamic-content-for-elementor'), 'framed' => __('Framed', 'dynamic-content-for-elementor')], 'default' => 'default', 'prefix_class' => 'elementor-drop-cap-view-', 'condition' => ['drop_cap' => 'yes']]);
        $this->add_control('drop_cap_primary_color', ['label' => __('Primary Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}}.elementor-drop-cap-view-stacked .elementor-drop-cap' => 'background-color: {{VALUE}};', '{{WRAPPER}}.elementor-drop-cap-view-framed .elementor-drop-cap, {{WRAPPER}}.elementor-drop-cap-view-default .elementor-drop-cap' => 'color: {{VALUE}}; border-color: {{VALUE}};'], 'condition' => ['drop_cap' => 'yes']]);
        $this->add_control('drop_cap_secondary_color', ['label' => __('Secondary Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}}.elementor-drop-cap-view-framed .elementor-drop-cap' => 'background-color: {{VALUE}};', '{{WRAPPER}}.elementor-drop-cap-view-stacked .elementor-drop-cap' => 'color: {{VALUE}};'], 'condition' => ['drop_cap_view!' => 'default']]);
        $this->add_control('drop_cap_size', ['label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 5], 'range' => ['px' => ['max' => 30]], 'selectors' => ['{{WRAPPER}} .elementor-drop-cap' => 'padding: {{SIZE}}{{UNIT}};'], 'condition' => ['drop_cap_view!' => 'default']]);
        $this->add_control('drop_cap_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['max' => 50]], 'selectors' => ['body:not(.rtl) {{WRAPPER}} .elementor-drop-cap' => 'margin-right: {{SIZE}}{{UNIT}};', 'body.rtl {{WRAPPER}} .elementor-drop-cap' => 'margin-left: {{SIZE}}{{UNIT}};']]);
        $this->add_control('drop_cap_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['%', 'px'], 'default' => ['unit' => '%'], 'range' => ['%' => ['max' => 50]], 'selectors' => ['{{WRAPPER}} .elementor-drop-cap' => 'border-radius: {{SIZE}}{{UNIT}};']]);
        $this->add_control('drop_cap_border_width', ['label' => __('Border Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .elementor-drop-cap' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['drop_cap_view' => 'framed']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'drop_cap_typography', 'selector' => '{{WRAPPER}} .elementor-drop-cap-letter', 'exclude' => ['letter_spacing'], 'condition' => ['drop_cap' => 'yes']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (\Elementor\Plugin::$instance->editor->is_edit_mode() && empty($settings['text_w_tokens'])) {
            Helper::notice('', __('Add text to the widget using Tokens', 'dynamic-content-for-elementor'));
            return;
        }
        $this->add_render_attribute('tokens', 'class', 'dce-tokens');
        ?>
		<div <?php 
        echo $this->get_render_attribute_string('tokens');
        ?>>
		<?php 
        echo Helper::get_dynamic_value($settings['text_w_tokens']);
        ?>
		</div>
		<?php 
    }
}
