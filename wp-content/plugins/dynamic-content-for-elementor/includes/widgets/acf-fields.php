<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Controls\Group_Control_Filters_CSS;
use DynamicContentForElementor\Controls\Group_Control_Transform_Element;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class AcfFields extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['elementor-dialog', 'dce-acf'];
    }
    public function get_style_depends()
    {
        return ['dce-acf'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => $this->get_title()]);
        $this->add_control('acf_field_list', ['label' => __('Select ACF Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'dynamic' => ['active' => \false], 'frontend_available' => \true]);
        $this->add_control('acf_type', ['label' => __('ACF Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['empty' => __('Empty', 'dynamic-content-for-elementor'), 'text' => __('Text', 'dynamic-content-for-elementor'), 'display' => __('ACF Display', 'dynamic-content-for-elementor'), 'wysiwyg' => __('Wysiwyg Editor', 'dynamic-content-for-elementor'), 'textarea' => __('TextArea', 'dynamic-content-for-elementor'), 'date' => __('Date', 'dynamic-content-for-elementor'), 'number' => __('Number', 'dynamic-content-for-elementor'), 'email' => __('Email', 'dynamic-content-for-elementor'), 'url' => __('Url', 'dynamic-content-for-elementor'), 'select' => __('Select', 'dynamic-content-for-elementor'), 'list' => __('List (Radio or Checkbox)', 'dynamic-content-for-elementor'), 'image' => __('Image', 'dynamic-content-for-elementor'), 'video' => __('Video oembed', 'dynamic-content-for-elementor')], 'default' => 'text', 'frontend_available' => \true]);
        $this->add_control('acf_dynamic', ['label' => __('Apply Shortcodes', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['acf_type' => ['text', 'textarea', 'wysiwyg']]]);
        $this->add_control('acf_currency_mode', ['label' => __('Currency Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['acf_type' => 'number']]);
        $this->add_control('acf_currency_type', ['label' => __('Currency type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::number_format_currency(), 'default' => 'en-US', 'frontend_available' => \true, 'condition' => ['acf_currency_mode!' => '', 'acf_type' => 'number']]);
        $this->add_control('acf_settoDecimal', ['label' => __('Decimal Place', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['acf_type' => 'number']]);
        $this->add_control('acf_integerDecimalOpt', ['label' => __('Set upto decimal integer', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 15, 'default' => 2, 'frontend_available' => \true, 'condition' => ['acf_settoDecimal!' => '', 'acf_type' => 'number']]);
        $this->add_control('acf_text_before', ['label' => __('Text before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $this->add_responsive_control('acf_text_before_block', ['label' => __('Before - Inline or Block', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => __('Inline', 'dynamic-content-for-elementor'), 'label_on' => __('Block', 'dynamic-content-for-elementor'), 'return_value' => 'block', 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf span.tx-before' => 'display: {{VALUE}};'], 'condition' => ['acf_text_before!' => '']]);
        $this->add_control('acf_text_after', ['label' => __('Text after', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $this->add_responsive_control('acf_text_after_block', ['label' => __('After - List or Block', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => __('List', 'dynamic-content-for-elementor'), 'label_on' => __('Block', 'dynamic-content-for-elementor'), 'return_value' => 'block', 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf span.tx-after' => 'display: {{VALUE}};'], 'condition' => ['acf_text_after!' => '']]);
        $this->add_control('drop_cap', ['label' => __('Drop Cap', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => __('Off', 'dynamic-content-for-elementor'), 'label_on' => __('On', 'dynamic-content-for-elementor'), 'separator' => 'before', 'render_type' => 'template', 'prefix_class' => 'elementor-drop-cap-', 'frontend_available' => \true, 'condition' => ['acf_type' => ['text', 'textarea']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_settings', ['label' => __('Settings', 'dynamic-content-for-elementor'), 'condition' => ['acf_type' => ['text', 'wysiwyg', 'textarea', 'date', 'image', 'empty', 'select', 'list']]]);
        $this->add_control('list_array_return', ['label' => __('List Return Value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Label: Value', 'dynamic-content-for-elementor'), 'label' => __('Label', 'dynamic-content-for-elementor'), 'value' => __('Value', 'dynamic-content-for-elementor')], 'default' => '', 'condition' => ['acf_type' => 'list']]);
        $this->add_control('list_style', ['label' => __('List Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['inline-block' => __('Horizontal', 'dynamic-content-for-elementor'), 'block' => __('Vertical', 'dynamic-content-for-elementor')], 'default' => 'inline-block', 'separator' => 'after', 'render_type' => 'template', 'condition' => ['acf_type' => 'list'], 'selectors' => ['{{WRAPPER}} .dce-acf-list' => 'display: {{VALUE}};']]);
        $this->add_control('list_separator', ['label' => __('Text before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => ' / ', 'condition' => ['list_style' => 'inline-block']]);
        $this->add_control('html_tag', ['label' => __('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(['code']), 'default' => 'div', 'condition' => ['acf_type!' => 'empty']]);
        $this->add_control('link_to', ['label' => __('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => __('None', 'dynamic-content-for-elementor'), 'home' => __('Home URL', 'dynamic-content-for-elementor'), 'post_url' => __('Post URL', 'dynamic-content-for-elementor'), 'acf_url' => __('ACF URL', 'dynamic-content-for-elementor'), 'custom' => __('Custom URL', 'dynamic-content-for-elementor')]]);
        $acf_url_groups = [];
        if (\Elementor\Plugin::$instance->editor->is_edit_mode() || ($_REQUEST['action'] ?? '') === 'elementor_ajax') {
            $acf_url_groups = Helper::get_acf_field_urlfile(\true);
        }
        $this->add_control('acf_field_url', ['label' => __('ACF Field URL', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => $acf_url_groups, 'default' => __('Select the field...', 'dynamic-content-for-elementor'), 'condition' => ['link_to' => 'acf_url']]);
        $this->add_control('acf_field_url_target', ['label' => __('Blank', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['link_to' => 'acf_url']]);
        $this->add_control('acf_field_url_nofollow', ['label' => __('NoFollow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['link_to' => 'acf_url']]);
        $this->add_control('link', ['label' => __('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => __('https://your-link.com', 'dynamic-content-for-elementor'), 'default' => ['url' => ''], 'show_label' => \false, 'condition' => ['link_to' => 'custom']]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'size', 'label' => __('Image Size', 'dynamic-content-for-elementor'), 'default' => 'large', 'condition' => ['acf_type' => 'image']]);
        $this->add_control('use_bg', ['label' => __('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['1' => ['title' => __('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => __('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'render_type' => 'template', 'default' => '0', 'separator' => 'before', 'prefix_class' => 'use-bg', 'condition' => ['acf_type' => 'image']]);
        $this->add_control('bg_position', ['label' => __('Background position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'top center', 'options' => ['' => __('Default', 'dynamic-content-for-elementor'), 'top left' => __('Top Left', 'dynamic-content-for-elementor'), 'top center' => __('Top Center', 'dynamic-content-for-elementor'), 'top right' => __('Top Right', 'dynamic-content-for-elementor'), 'center left' => __('Center Left', 'dynamic-content-for-elementor'), 'center center' => __('Center Center', 'dynamic-content-for-elementor'), 'center right' => __('Center Right', 'dynamic-content-for-elementor'), 'bottom left' => __('Bottom Left', 'dynamic-content-for-elementor'), 'bottom center' => __('Bottom Center', 'dynamic-content-for-elementor'), 'bottom right' => __('Bottom Right', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acfimage-bg' => 'background-position: {{VALUE}};'], 'condition' => ['acf_type' => ['image'], 'use_bg' => '1']]);
        $this->add_responsive_control('height', ['label' => __('Background Minimum Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 200, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%', 'vh'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vh' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acfimage-bg' => 'min-height: {{SIZE}}{{UNIT}};'], 'condition' => ['acf_type' => ['image'], 'use_bg' => '1']]);
        $this->add_responsive_control('bg_width', ['label' => __('Background Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%', 'vw'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vh' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acfimage-bg' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['acf_type' => ['image'], 'use_bg' => '1']]);
        $this->add_control('bg_extend', ['label' => __('Extend Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'label_on' => __('Show', 'dynamic-content-for-elementor'), 'label_off' => __('Hide', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'prefix_class' => 'extendbg-', 'condition' => ['acf_type' => ['image'], 'use_bg' => '1']]);
        $this->end_controls_section();
        $this->start_controls_section('section_overlay', ['label' => __('Overlay Image', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['acf_type' => ['image']]]);
        $this->add_control('use_overlay', ['label' => __('Overlay Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'label_on' => __('Show', 'dynamic-content-for-elementor'), 'label_off' => __('Hide', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'background_overlay', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-overlay', 'condition' => ['use_overlay' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_datetime', ['label' => __('Date Time', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['acf_type' => 'date']]);
        $this->add_control('date_format', ['label' => __('Date Format', 'dynamic-content-for-elementor'), 'description' => '<a target="_blank" href="https://www.php.net/manual/en/function.date.php">' . __('Use standard PHP format character', 'dynamic-content-for-elementor') . '</a>', 'type' => Controls_Manager::TEXT, 'default' => 'F j, Y, g:i a', 'label_block' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_filters', ['label' => __('Filters Image', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['acf_type' => ['image']]]);
        $this->add_group_control(Group_Control_Filters_CSS::get_type(), ['name' => 'filters_image', 'label' => 'Filters image', 'selector' => '{{WRAPPER}} .wrap-filters']);
        $this->add_control('blend_mode', ['label' => __('Blend Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Normal', 'dynamic-content-for-elementor'), 'multiply' => __('Multiply', 'dynamic-content-for-elementor'), 'screen' => __('Screen', 'dynamic-content-for-elementor'), 'overlay' => __('Overlay', 'dynamic-content-for-elementor'), 'darken' => __('Darken', 'dynamic-content-for-elementor'), 'lighten' => __('Lighten', 'dynamic-content-for-elementor'), 'color-dodge' => __('Color Dodge', 'dynamic-content-for-elementor'), 'saturation' => __('Saturation', 'dynamic-content-for-elementor'), 'color' => __('Color', 'dynamic-content-for-elementor'), 'difference' => __('Difference', 'dynamic-content-for-elementor'), 'exclusion' => __('Exclusion', 'dynamic-content-for-elementor'), 'hue' => __('Hue', 'dynamic-content-for-elementor'), 'luminosity' => __('Luminosity', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .acf-image' => 'mix-blend-mode: {{VALUE}}'], 'separator' => 'none']);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('data_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'description' => __('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => __('Same', 'dynamic-content-for-elementor'), 'label_off' => __('other', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_control('other_post_source', ['label' => __('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_fallback', ['label' => __('Fallback', 'dynamic-content-for-elementor')]);
        $this->add_control('fallback', ['label' => __('Fallback Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('If you want to show something when the field is empty', 'dynamic-content-for-elementor')]);
        $this->add_control('fallback_text', ['label' => __('Text Fallback', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'default' => __('Empty field', 'dynamic-content-for-elementor'), 'condition' => ['fallback!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Style', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'default' => '', 'prefix_class' => 'align-dce-', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->add_control('hyphens', ['label' => __('Hyphens', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'prefix_class' => 'hyphens-', 'condition' => ['align' => 'justify']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_text', ['label' => __('Text', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['acf_type' => ['text', 'date', 'textarea', 'select', 'list', 'wysiwyg', 'number', 'empty']]]);
        $this->add_control('color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf' => 'color: {{VALUE}};'], 'condition' => ['acf_type' => ['text', 'date', 'textarea', 'select', 'list', 'wysiwyg', 'number', 'empty']]]);
        $this->add_control('bg_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf' => 'background-color: {{VALUE}};'], 'condition' => ['acf_type' => ['text', 'date', 'textarea', 'select', 'list', 'wysiwyg', 'number', 'empty']]]);
        $this->add_responsive_control('acf_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('acf_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('acf_shift', ['label' => __('Shift', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 180, 'min' => -180, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf' => 'left: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_tx', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-acf', 'condition' => ['acf_type' => ['text', 'date', 'textarea', 'select', 'list', 'wysiwyg', 'number', 'empty']]]);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'text_shadow', 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-acf']);
        $this->add_control('List_heading', ['label' => __('List', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['acf_type' => ['list']]]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_list_label', 'label' => __('Label Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-acf-list-label', 'condition' => ['acf_type' => ['list'], 'list_array_return' => 'label']]);
        $this->add_responsive_control('list_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-acf-list-inline-block .dce-acf-list:not(:first-child)' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-acf-list-inline-block .dce-acf-list:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-acf-list-block .dce-acf-list:not(:forst-child)' => 'margin-top: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-acf-list-block .dce-acf-list:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['acf_type' => ['list']]]);
        $this->add_responsive_control('list_width', ['label' => __('Label width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-acf-list-label' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['acf_type' => ['list'], 'list_array_return' => 'label']]);
        $this->add_control('txbefore_heading', ['label' => __('Text before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['acf_text_before!' => '']]);
        $this->add_control('tx_before_color', ['label' => __('Text Before Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf span.tx-before' => 'color: {{VALUE}};', '{{WRAPPER}} .dynamic-content-for-elementor-acf a span.tx-before' => 'color: {{VALUE}};'], 'condition' => ['acf_text_before!' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_tx_before', 'label' => __('Font Before', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-acf span.tx-before', 'condition' => ['acf_text_before!' => '']]);
        $this->add_control('txafter_heading', ['label' => __('Text after', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['acf_text_after!' => '']]);
        $this->add_control('tx_after_color', ['label' => __('Text After Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf span.tx-after' => 'color: {{VALUE}};', '{{WRAPPER}} .dynamic-content-for-elementor-acf a span.tx-after' => 'color: {{VALUE}};'], 'condition' => ['acf_text_after!' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_tx_after', 'label' => __('Font After', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-acf span.tx-after', 'condition' => ['acf_text_after!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_image', ['label' => __('Image', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['acf_type' => ['image']]]);
        $this->add_responsive_control('space', ['label' => __('Size (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => '%'], 'size_units' => ['%', 'px', 'vw'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 800], 'vw' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .acf-image' => 'max-width: {{SIZE}}{{UNIT}};'], 'condition' => ['acf_type' => ['image'], 'bg_extend' => '']]);
        $this->add_control('force_width', ['label' => __('Force Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'prefix_class' => 'forcewidth-', 'condition' => ['acf_type' => ['image'], 'bg_extend' => '']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'image_border', 'label' => __('Image Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .acf-image', 'condition' => ['acf_type' => ['image']]]);
        $this->add_control('image_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .acf-image, {{WRAPPER}} .acf-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['acf_type' => ['image']]]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'image_box_shadow', 'selector' => '{{WRAPPER}} .acf-image', 'condition' => ['acf_type' => ['image']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_hover_style', ['label' => 'Rollover', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['link_to!' => 'none']]);
        $this->add_control('acf_color_hover', ['label' => __('Text Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf a:hover' => 'color: {{VALUE}};'], 'condition' => ['link_to!' => 'none']]);
        $this->add_control('tx_before_color_hover', ['label' => __('Text Before Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf span.tx-before' => 'color: {{VALUE}};', '{{WRAPPER}} .dynamic-content-for-elementor-acf a:hover span.tx-before' => 'color: {{VALUE}};'], 'condition' => ['acf_text_before!' => '']]);
        $this->add_control('tx_after_color_hover', ['label' => __('Text After Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf span.tx-after' => 'color: {{VALUE}};', '{{WRAPPER}} .dynamic-content-for-elementor-acf a:hover span.tx-after' => 'color: {{VALUE}};'], 'condition' => ['acf_text_after!' => '']]);
        $this->add_control('acf_bgcolor_hover', ['label' => __('Background Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf:hover' => 'background-color: {{VALUE}};'], 'condition' => ['link_to!' => 'none']]);
        $this->add_control('acf_bgcolor_overlay_hover', ['label' => __('Background Overlay Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'overlay_hover_color', 'label' => __('Background', 'dynamic-content-for-elementor'), 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-overlay_hover', 'separator' => 'after', 'condition' => ['link_to!' => 'none', 'acf_type' => ['image']]]);
        $this->add_control('hover_animation', ['label' => __('Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION, 'condition' => ['link_to!' => 'none', 'acf_type' => ['image']]]);
        $this->add_group_control(Group_Control_Filters_CSS::get_type(), ['name' => 'filters_image_hover', 'label' => __('Filters', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} a:hover .wrap-filters', 'condition' => ['link_to!' => 'none', 'acf_type' => ['image']]]);
        $this->add_control('hover_effects', ['label' => __('Effects', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('None', 'dynamic-content-for-elementor'), 'zoom' => __('Zoom', 'dynamic-content-for-elementor'), 'slow-zoom' => __('Slow Zoom', 'dynamic-content-for-elementor')], 'separator' => 'before', 'prefix_class' => 'hovereffect-', 'condition' => ['link_to!' => 'none']]);
        $this->end_controls_section();
        $this->start_controls_section('section_settings_media', ['label' => __('Media Settings', 'dynamic-content-for-elementor'), 'condition' => ['acf_type' => ['video', 'audio']]]);
        $this->add_control('video_type', ['label' => __('Video Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'youtube', 'options' => ['youtube' => __('YouTube', 'dynamic-content-for-elementor'), 'vimeo' => __('Vimeo', 'dynamic-content-for-elementor')]]);
        $this->add_control('hosted_link', ['label' => __('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => __('Enter your video link', 'dynamic-content-for-elementor'), 'default' => '', 'label_block' => \true, 'condition' => ['video_type' => 'hosted']]);
        $this->add_control('aspect_ratio', ['label' => __('Aspect Ratio', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'frontend_available' => \true, 'options' => ['169' => '16:9', '43' => '4:3', '32' => '3:2', 'customheight' => 'Custom Height'], 'default' => '169', 'prefix_class' => 'elementor-aspect-ratio-']);
        $this->add_responsive_control('custom_height', ['label' => __('Custom Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 300, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 0, 'max' => 100], 'px' => ['min' => 0, 'max' => 600]], 'selectors' => ['{{WRAPPER}} iframe' => 'height: {{SIZE}}{{UNIT}};'], 'condition' => ['aspect_ratio' => 'customheight']]);
        $this->add_control('heading_youtube', ['label' => __('Video Options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('yt_autoplay', ['label' => __('Autoplay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['video_type' => 'youtube']]);
        $this->add_control('yt_loop', ['label' => __('Loop', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['video_type' => 'youtube']]);
        $this->add_control('yt_mute', ['label' => __('Mute', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['video_type' => 'youtube']]);
        $this->add_control('yt_rel', ['label' => __('Suggested Videos', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => __('Hide', 'dynamic-content-for-elementor'), 'label_on' => __('Show', 'dynamic-content-for-elementor'), 'condition' => ['video_type' => 'youtube']]);
        $this->add_control('yt_controls', ['label' => __('Player Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => __('Hide', 'dynamic-content-for-elementor'), 'label_on' => __('Show', 'dynamic-content-for-elementor'), 'default' => 'yes', 'condition' => ['video_type' => 'youtube']]);
        $this->add_control('yt_showinfo', ['label' => __('Player Title & Actions', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => __('Hide', 'dynamic-content-for-elementor'), 'label_on' => __('Show', 'dynamic-content-for-elementor'), 'default' => 'yes', 'condition' => ['video_type' => 'youtube']]);
        $this->add_control('vimeo_autoplay', ['label' => __('Autoplay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['video_type' => 'vimeo']]);
        $this->add_control('vimeo_loop', ['label' => __('Loop', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['video_type' => 'vimeo']]);
        $this->add_control('vimeo_title', ['label' => __('Intro Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => __('Hide', 'dynamic-content-for-elementor'), 'label_on' => __('Show', 'dynamic-content-for-elementor'), 'default' => 'yes', 'condition' => ['video_type' => 'vimeo']]);
        $this->add_control('vimeo_portrait', ['label' => __('Intro Portrait', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => __('Hide', 'dynamic-content-for-elementor'), 'label_on' => __('Show', 'dynamic-content-for-elementor'), 'default' => 'yes', 'condition' => ['video_type' => 'vimeo']]);
        $this->add_control('vimeo_byline', ['label' => __('Intro Byline', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => __('Hide', 'dynamic-content-for-elementor'), 'label_on' => __('Show', 'dynamic-content-for-elementor'), 'default' => 'yes', 'condition' => ['video_type' => 'vimeo']]);
        $this->add_control('vimeo_color', ['label' => __('Controls Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'condition' => ['video_type' => 'vimeo']]);
        $this->add_control('view', ['label' => __('View', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'default' => 'youtube']);
        $this->end_controls_section();
        $this->start_controls_section('section_image_overlay', ['label' => __('Image Overlay', 'dynamic-content-for-elementor'), 'condition' => ['acf_type' => 'video'], 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('show_image_overlay', ['label' => __('Enable Image Overlay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => __('Hide', 'dynamic-content-for-elementor'), 'label_on' => __('Show', 'dynamic-content-for-elementor')]);
        $this->add_control('image_overlay_type', ['label' => __('Image Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'custom', 'options' => ['custom' => __('Custom', 'dynamic-content-for-elementor'), 'acf' => __('ACF', 'dynamic-content-for-elementor')], 'condition' => ['show_image_overlay' => 'yes']]);
        $this->add_control('image_overlay_acf', ['label' => __('Field Image', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'image', 'condition' => ['image_overlay_type' => 'acf']]);
        $this->add_control('image_overlay', ['label' => __('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => Utils::get_placeholder_image_src()], 'condition' => ['show_image_overlay' => 'yes', 'image_overlay_type' => 'custom']]);
        $this->add_control('show_play_icon', ['label' => __('Play Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'yes', 'options' => ['yes' => __('Yes', 'dynamic-content-for-elementor'), 'no' => __('No', 'dynamic-content-for-elementor')], 'condition' => ['show_image_overlay' => 'yes', 'image_overlay[url]!' => '']]);
        $this->add_control('lightbox', ['label' => __('Lightbox', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['show_image_overlay' => 'yes', 'image_overlay[url]!' => ''], 'separator' => 'before']);
        $this->add_control('lightbox_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['#elementor-video-modal-{{ID}}' => 'background-color: {{VALUE}};'], 'condition' => ['show_image_overlay' => 'yes', 'image_overlay[url]!' => '', 'lightbox' => 'yes']]);
        $this->add_control('lightbox_content_width', ['label' => __('Content Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'units' => ['%'], 'default' => ['unit' => '%'], 'range' => ['%' => ['min' => 50]], 'selectors' => ['#elementor-video-modal-{{ID}} .dialog-widget-content' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['show_image_overlay' => 'yes', 'image_overlay[url]!' => '', 'lightbox' => 'yes']]);
        $this->add_control('lightbox_content_position', ['label' => __('Content Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'center center', 'frontend_available' => \true, 'options' => ['center center' => __('Center', 'dynamic-content-for-elementor'), 'center top' => __('Top', 'dynamic-content-for-elementor')], 'condition' => ['show_image_overlay' => 'yes', 'image_overlay[url]!' => '', 'lightbox' => 'yes'], 'render_type' => 'none']);
        $this->add_control('lightbox_content_animation', ['label' => __('Entrance Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ANIMATION, 'default' => '', 'frontend_available' => \true, 'label_block' => \true, 'condition' => ['show_image_overlay' => 'yes', 'image_overlay[url]!' => '', 'lightbox' => 'yes'], 'render_type' => 'none']);
        $this->end_controls_section();
        $this->start_controls_section('section_drop_cap', ['label' => __('Drop Cap', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['drop_cap' => 'yes', 'acf_type' => ['text', 'textarea']]]);
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
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id($settings['other_post_source']);
        $type_page = get_post_type($id_page);
        $acfResult = '';
        $idFields = $settings['acf_field_list'];
        $typeField = $settings['acf_type'];
        $fieldSettings = Helper::get_acf_field_settings($idFields);
        $image_size = $settings['size_size'];
        $use_bg = $settings['use_bg'];
        $wrap_effect_start = '<div class="mask"><div class="wrap-filters">';
        $wrap_effect_end = '</div></div>';
        $overlay_block = '';
        if ($settings['use_overlay'] == 'yes') {
            $overlay_block = '<div class="dce-overlay"></div>';
        }
        $overlay_hover_block = '<div class="dce-overlay_hover"></div>';
        $acf_field_value = Helper::get_acf_field_value($idFields, $id_page);
        if (\in_array($typeField, ['image', 'image_url'], \true)) {
            $imageField = $acf_field_value;
            if (\is_string($imageField)) {
                $typeField = 'image_url';
            } elseif (\is_numeric($imageField)) {
                $typeField = 'image';
            } elseif (\is_array($imageField)) {
                $typeField = 'image_array';
            }
        }
        if ('list' === $typeField) {
            $field_wrapper = 'div';
            if (empty($settings['acf_text_before_block'])) {
                $field_wrapper = 'span';
            }
            $acfResult = '<' . $field_wrapper . ' class="dce-acf-list-' . $settings['list_style'] . '">';
            // l'elemento Item Wrap
            // Radio or Checkbox list
            switch ($fieldSettings['type']) {
                case 'radio':
                    if (\is_array($acf_field_value)) {
                        if ($settings['list_array_return'] == '') {
                            $acfResult .= '<span class="dce-acf-list"><span class="dce-acf-list-label">' . $acf_field_value['label'] . ': </span><span class="dce-acf-list-value">' . $acf_field_value['value'] . '</span></span>';
                        } elseif ($settings['list_array_return'] == 'label') {
                            $acfResult .= '<span class="dce-acf-list dce-acf-list-label">' . $acf_field_value['label'] . '</span>';
                        } elseif ($settings['list_array_return'] == 'value') {
                            $acfResult .= '<span class="dce-acf-list dce-acf-list-value">' . $acf_field_value['value'] . '</span>';
                        }
                    } else {
                        $acfResult .= $acf_field_value;
                    }
                    break;
                case 'checkbox':
                    if (!empty($acf_field_value)) {
                        foreach ($acf_field_value as $akey => $field) {
                            if ($akey > 0) {
                                $acfResult .= '<span class="dce-acf-list-separator">' . wp_kses_post($settings['list_separator']) . '</span>';
                            }
                            if (\is_array($field)) {
                                if ($settings['list_array_return'] == '') {
                                    $acfResult .= '<span class="dce-acf-list"><span class="dce-acf-list-label">' . $field['label'] . ': </span><span class="dce-acf-list-value">' . $field['value'] . '</span></span>';
                                } elseif ($settings['list_array_return'] == 'label') {
                                    $acfResult .= '<span class="dce-acf-list dce-acf-list-label">' . $field['label'] . '</span>';
                                } elseif ($settings['list_array_return'] == 'value') {
                                    $acfResult .= '<span class="dce-acf-list dce-acf-list-value">' . $field['value'] . '</span>';
                                }
                            } else {
                                $acfResult .= '<span class="dce-acf-list dce-acf-list-value">' . $field . '</span>';
                            }
                        }
                    }
                    break;
                case 'taxonomy':
                default:
                    if (!empty($acf_field_value)) {
                        if (!\is_array($acf_field_value)) {
                            $acf_field_value = array($acf_field_value);
                        }
                        foreach ($acf_field_value as $akey => $field) {
                            $label = Helper::to_string($field);
                            if ($akey > 0) {
                                $acfResult .= '<span class="dce-acf-list-separator">' . wp_kses_post($settings['list_separator']) . '</span>';
                            }
                            $acfResult .= '<span class="dce-acf-list dce-acf-list-value">' . $label . '</span>';
                        }
                    }
            }
            $acfResult .= '</' . $field_wrapper . '>';
            // end wrap
        } elseif (\in_array($typeField, ['text', 'textarea', 'select', 'wysiwyg', 'number'], \true)) {
            $acf_field_value = Helper::to_string($acf_field_value);
            if ($typeField == 'select') {
                $acfResult = $acf_field_value;
            } elseif ($typeField == 'wysiwyg') {
                $acfResult = wpautop($acf_field_value);
            } else {
                // text and textarea
                $acfResult = $acf_field_value;
            }
        } elseif (\in_array($typeField, ['url', 'file'], \true)) {
            $acfResult = $acf_field_value;
            if ('elementor_library' === $type_page && $acfResult == '') {
                $acfResult = '#';
            }
        } elseif ($typeField === 'display') {
            $acfResult = \get_field($idFields, $id_page ? $id_page : null);
        } elseif ($typeField == 'email') {
            $acfResult = $acf_field_value;
            $acfResult = '<a href="mailto:' . $acfResult . '">' . $acfResult . '</a>';
            if ($type_page == 'elementor_library' && $acfResult == '') {
                $acfResult = 'your-email@my-domain.com';
            }
        } elseif ($typeField == 'date') {
            $acfResult = $acf_field_value;
            if ($acfResult == '') {
                $acfResult = '1970/01/01 00:00:00';
            } else {
                $dataDate = get_field_object($idFields);
                $dataDate = Helper::get_acf_field_settings($idFields);
                $format_display = $settings['date_format'];
                if (!$format_display) {
                    $format_display = $dataDate['return_format'];
                }
                $d = \DateTime::createFromFormat($dataDate['return_format'], $acfResult);
                if ($d) {
                    $locale = get_locale();
                    if (!\strpos($locale, '.')) {
                        // avoid months being printed in other encodings
                        $locale = "{$locale}.UTF-8";
                    }
                    \setlocale(\LC_TIME, $locale);
                    $date_format = Helper::date_format_to_strftime_format($format_display);
                    $acfResult = \strftime($date_format, \intval($d->format('U')));
                } else {
                    $timestamp = \strtotime($acfResult);
                    $acfResult = date_i18n($format_display, $timestamp);
                }
            }
        } elseif ('image' === $typeField) {
            $settings['html_tag'] = 'div';
            $imageSrc = Group_Control_Image_Size::get_attachment_image_src($imageField, 'size', $settings);
            $imageSrcUrl = $imageSrc;
            if ($type_page == 'elementor_library' && $imageSrcUrl == '') {
                $imageSrcUrl = \Elementor\Utils::get_placeholder_image_src();
            }
            if (empty($imageSrcUrl)) {
                return;
            }
            if ($use_bg == 0) {
                $acfResult = '<div class="acf-image">' . $wrap_effect_start . '<img src="' . $imageSrcUrl . '" />' . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
            } else {
                $bg_featured_image = '<div class="acf-image acf-bg-image">' . $wrap_effect_start . '<figure class="dynamic-content-for-elementor-acfimage-bg" style="background-image: url(' . $imageSrcUrl . '); background-repeat: no-repeat; background-size: cover;"></figure>' . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
                $acfResult = $bg_featured_image;
            }
        } elseif ('image_url' === $typeField) {
            if ($type_page == 'elementor_library' && $imageField == '') {
                $imageSrcUrl = \Elementor\Utils::get_placeholder_image_src();
            }
            if (empty($imageField)) {
                return;
            }
            $settings['html_tag'] = 'div';
            if (\is_numeric($imageField)) {
                $imageField = Group_Control_Image_Size::get_attachment_image_src($imageField, 'size', $settings);
            }
            if (!$use_bg) {
                $acfResult = '<div class="acf-image">' . $wrap_effect_start . '<img src="' . $imageField . '" />' . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
            } else {
                $bg_featured_image = '<div class="acf-image acf-bg-image">' . $wrap_effect_start . '<figure class="dynamic-content-for-elementor-acfimage-bg" style="background-image: url(' . $imageField . '); background-repeat: no-repeat; background-size: cover;"></figure>' . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
                $acfResult = $bg_featured_image;
            }
        } elseif ('image_array' === $typeField) {
            $settings['html_tag'] = 'div';
            $imageAlt = $imageField['alt'];
            $imageDesc = $imageField['description'];
            $imageCapt = $imageField['caption'];
            $imageSrc = Group_Control_Image_Size::get_attachment_image_src($imageField['ID'], 'size', $settings);
            $imageAttach = '<img src="' . $imageSrc . '" alt="' . $imageAlt . '" />';
            $imageSrcUrl = $imageSrc;
            if ($type_page == 'elementor_library' && $imageSrcUrl == '') {
                $imageAttach = '<img src="' . \Elementor\Utils::get_placeholder_image_src() . '" />';
            }
            if (empty($imageSrcUrl)) {
                return;
            }
            if (!$use_bg) {
                $acfResult = '<div class="acf-image">' . $wrap_effect_start . $imageAttach . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
            } else {
                $bg_featured_image = '<div class="acf-image acf-bg-image">' . $wrap_effect_start . '<figure class="dynamic-content-for-elementor-acfimage-bg" style="background-image: url(' . $imageSrcUrl . '); background-repeat: no-repeat; background-size: cover;"></figure>' . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
                $acfResult = $bg_featured_image;
            }
        } elseif ('video' === $typeField) {
            $video_field = $acf_field_value;
            if ($type_page == 'elementor_library' && $video_field == '') {
                $video_field = 'https://www.youtube.com/watch?v=9uOETcuFjbE';
            }
            $params = [];
            if (!\is_string($video_field) || $video_field === '') {
                return;
            }
            add_filter('oembed_result', [$this, 'filter_oembed_result'], 50);
            $video_html = wp_oembed_get($video_field, wp_embed_defaults());
            remove_filter('oembed_result', [$this, 'filter_oembed_result'], 50);
            if (!$video_html) {
                echo $video_field;
                return;
            }
            $this->add_render_attribute('video-wrapper', 'class', 'elementor-wrapper');
            if (!$settings['lightbox']) {
                $this->add_render_attribute('video-wrapper', 'class', 'elementor-video-wrapper');
            }
            $this->add_render_attribute('video-wrapper', 'class', 'elementor-open-' . (!empty($settings['lightbox']) ? 'lightbox' : 'inline'));
            ?>
			<div <?php 
            echo $this->get_render_attribute_string('video-wrapper');
            ?>>
				<?php 
            if (!$settings['lightbox']) {
                echo $video_html;
            }
            if ($this->has_image_overlay()) {
                $this->add_render_attribute('image-overlay', 'class', 'elementor-custom-embed-image-overlay');
                if (!$settings['lightbox']) {
                    if ($settings['image_overlay_type'] == 'custom') {
                        $this->add_render_attribute('image-overlay', 'style', 'background-image: url(' . $settings['image_overlay']['url'] . ');');
                    } elseif ($settings['image_overlay_type'] == 'acf') {
                        $immagine_acf_overlay = Helper::get_acf_field_value($settings['image_overlay_acf'], $id_page);
                        if (\is_string($immagine_acf_overlay)) {
                            $immagine_acf_overlay = $immagine_acf_overlay;
                        } elseif (\is_numeric($immagine_acf_overlay)) {
                            $imageSrc = wp_get_attachment_image_src($immagine_acf_overlay, 'full');
                            $imageSrcUrl = $imageSrc[0];
                            $immagine_acf_overlay = $imageSrcUrl;
                        } elseif (\is_array($immagine_acf_overlay)) {
                            $imageSrc = wp_get_attachment_image_src($immagine_acf_overlay['ID'], 'full');
                            $imageSrcUrl = $imageSrc[0];
                            $immagine_acf_overlay = $imageSrcUrl;
                        }
                        if ($immagine_acf_overlay == '') {
                            $immagine_acf_overlay = \Elementor\Utils::get_placeholder_image_src();
                        }
                        $this->add_render_attribute('image-overlay', 'style', 'background-image: url(' . $immagine_acf_overlay . ');');
                    }
                }
                ?>
					<div <?php 
                echo $this->get_render_attribute_string('image-overlay');
                ?>>

						<?php 
                if ($settings['lightbox']) {
                    ?>
							<img src="<?php 
                    echo $settings['image_overlay']['url'];
                    ?>">
						<?php 
                }
                ?>
						<?php 
                if ('yes' === $settings['show_play_icon']) {
                    ?>
							<div class="elementor-custom-embed-play">
								<i class="fa fa-play-circle"></i>
							</div>
						<?php 
                }
                ?>
					</div>
					<?php 
            }
            ?>
			</div>

			<?php 
        } elseif ('empty' === $typeField) {
            $acfResult = '';
        }
        switch ($settings['link_to']) {
            case 'custom':
                if (!empty($settings['link']['url'])) {
                    $link = esc_url($settings['link']['url']);
                    $target = !empty($settings['link']['is_external']) ? ' target="_blank"' : '';
                } else {
                    $link = \false;
                }
                break;
            case 'acf_url':
                if (!empty($settings['acf_field_url'])) {
                    $link = Helper::get_acf_field_value($settings['acf_field_url'], $id_page);
                    $link = esc_url(\is_string($link) ? $link : '');
                    $target = !empty($settings['acf_field_url_target']) ? ' target="_blank"' : '';
                    $target .= !empty($settings['acf_field_url_nofollow']) ? ' rel="nofollow"' : '';
                } else {
                    $link = \false;
                }
                break;
            case 'post_url':
                $link = esc_url(get_permalink($id_page));
                $target = '';
                break;
            case 'home':
                $link = esc_url(get_home_url());
                $target = '';
                break;
            case 'none':
            default:
                $link = \false;
                $target = '';
                break;
        }
        $html = '';
        $animation_class = !empty($settings['hover_animation']) ? 'elementor-animation-' . $settings['hover_animation'] : '';
        if ($acfResult != '' || $typeField == 'empty') {
            if ($settings['acf_dynamic']) {
                $acfResult = do_shortcode($acfResult);
            }
            if ($settings['acf_currency_mode'] || $settings['acf_settoDecimal']) {
                $acfResult = '<div id=' . $settings['acf_field_list'] . '>' . $acfResult . '</div>';
            }
            $html_tag = 'div';
            if ($settings['html_tag']) {
                $html_tag = \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']);
            }
            $html = '<' . $html_tag . ' class="dynamic-content-for-elementor-acf ' . $animation_class . '">';
            if ($settings['acf_text_before'] != '') {
                $acfResult = '<span class="tx-before">' . wp_kses_post($settings['acf_text_before']) . '</span>' . $acfResult;
            }
            if ($settings['acf_text_after'] != '') {
                $acfResult = $acfResult . '<span class="tx-after">' . wp_kses_post($settings['acf_text_after']) . '</span>';
            }
            if ($link) {
                $html .= '<a href="' . $link . '"' . $target . '>' . $acfResult . '</a>';
            } else {
                $html .= $acfResult;
            }
            $html .= '</' . $html_tag . '>';
        }
        if ('' !== $acf_field_value) {
            // Don't show if is '', show if it's 0
            echo $html;
        } elseif (!empty($this->get_settings_for_display('fallback'))) {
            $this->render_fallback();
        }
    }
    public function filter_oembed_result($html)
    {
        $settings = $this->get_settings_for_display();
        $params = [];
        if ('youtube' === $settings['video_type']) {
            $youtube_options = ['autoplay', 'loop', 'mute', 'rel', 'controls', 'showinfo'];
            foreach ($youtube_options as $option) {
                if ('autoplay' === $option && $this->has_image_overlay()) {
                    continue;
                }
                $value = 'yes' === $settings['yt_' . $option] ? '1' : '0';
                if ($settings['yt_loop'] == 'yes') {
                    $ytID = $this->youtube_id_from_url($html);
                    $params['playlist'] = $ytID;
                }
                $params[$option] = $value;
            }
            $params['wmode'] = 'opaque';
        }
        if ('vimeo' === $settings['video_type']) {
            $vimeo_options = ['autoplay', 'loop', 'title', 'portrait', 'byline'];
            foreach ($vimeo_options as $option) {
                if ('autoplay' === $option && $this->has_image_overlay()) {
                    continue;
                }
                $value = 'yes' === $settings['vimeo_' . $option] ? '1' : '0';
                $params[$option] = $value;
            }
            $params['color'] = \str_replace('#', '', $settings['vimeo_color']);
        }
        if (!empty($params)) {
            \preg_match('/<iframe.*src=\\"(.*)\\".*><\\/iframe>/isU', $html, $matches);
            $url = esc_url(add_query_arg($params, $matches[1]));
            $html = \str_replace($matches[1], $url, $html);
        }
        return $html;
    }
    protected function youtube_id_from_url($html)
    {
        \preg_match('/<iframe.*src=\\"(.*)\\".*><\\/iframe>/isU', $html, $matches);
        $url = esc_url($matches[1]);
        $pattern = "/^(?:http(?:s)?:\\/\\/)?(?:www\\.)?(?:m\\.)?(?:youtu\\.be\\/|youtube\\.com\\/(?:(?:watch)?\\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\\/))([^\\?&\"'>]+)/";
        $result = \preg_match($pattern, $url, $matches);
        if ($result) {
            return $matches[1];
        }
        return \false;
    }
    protected function get_hosted_params()
    {
        $settings = $this->get_settings_for_display();
        $params = [];
        $params['src'] = $settings['hosted_link'];
        $hosted_options = ['autoplay', 'loop'];
        foreach ($hosted_options as $key => $option) {
            $value = 'yes' === $settings['hosted_' . $option] ? '1' : '0';
            $params[$option] = $value;
        }
        if (!empty($settings['hosted_width'])) {
            $params['width'] = $settings['hosted_width'];
        }
        if (!empty($settings['hosted_height'])) {
            $params['height'] = $settings['hosted_height'];
        }
        return $params;
    }
    protected function has_image_overlay()
    {
        $settings = $this->get_settings_for_display();
        return !empty($settings['image_overlay']['url']) && 'yes' === $settings['show_image_overlay'];
    }
    protected function render_fallback()
    {
        $html_tag = $this->get_settings_for_display('html_tag') ?? 'div';
        $this->set_render_attribute('fallback', 'class', ['dynamic-content-for-elementor-acf', 'dce-fallback']);
        if (!empty($this->get_settings_for_display('hover_animation'))) {
            $this->add_render_attribute('fallback', 'class', 'elementor-animation-' . $this->get_settings_for_display('hover_animation'));
        }
        echo '<' . Helper::validate_html_tag($html_tag) . ' ' . $this->get_render_attribute_string('fallback') . '>';
        echo $this->get_settings_for_display('fallback_text');
        echo '</' . Helper::validate_html_tag($html_tag) . '>';
    }
}
