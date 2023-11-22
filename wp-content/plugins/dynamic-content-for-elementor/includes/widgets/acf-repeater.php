<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use DynamicOOOS\Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class AcfRepeater extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * Return an array with key ACF subfield id and value
     *
     * @param string $repeater_field
     * @return array<string>
     */
    private function get_subfields_values(string $repeater_field, string $post_ID)
    {
        $raw_fields = \get_field($repeater_field, $post_ID);
        $values = [];
        foreach ($raw_fields as $field => $value) {
            $values[$field] = $value;
        }
        return $values;
    }
    public function get_script_depends()
    {
        return ['imagesloaded', 'swiper', 'jquery-masonry', 'dce-wow', 'dce-acf-repeater', 'dce-datatables', 'dce-accordionjs'];
    }
    public function get_style_depends()
    {
        return ['dce-acf-repeater', 'datatables', 'dce-accordionjs'];
    }
    /**
     * Run Once
     *
     * @return void
     */
    public function run_once()
    {
        parent::run_once();
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control($this->get_type(), 'dce_acf_repeater_thead_custom_html');
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_acf_repeater', ['label' => $this->get_title()]);
        $this->add_control('dce_acf_repeater', ['label' => __('ACF Repeater field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'repeater', 'dynamic' => ['active' => \false]]);
        $this->add_control('acf_repeater_from', ['label' => __('Retrieve the field from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'current_post', 'options' => ['current_post' => __('Current Post', 'dynamic-content-for-elementor'), 'current_user' => __('Current User', 'dynamic-content-for-elementor'), 'current_author' => __('Current Author', 'dynamic-content-for-elementor'), 'current_term' => __('Current Term', 'dynamic-content-for-elementor'), 'options_page' => __('Options Page', 'dynamic-content-for-elementor')]]);
        $this->add_control('dce_acf_repeater_mode', ['label' => __('Display mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['subfields' => ['title' => __('Sub fields', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list-ul'], 'html' => ['title' => __('HTML & Tokens', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-code'], 'template' => ['title' => __('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'subfields']);
        $repeater_subfields = new \Elementor\Repeater();
        $repeater_subfields->start_controls_tabs('acfitems_repeater');
        $repeater_subfields->start_controls_tab('tab_content', ['label' => __('Item', 'dynamic-content-for-elementor')]);
        $repeater_subfields->add_control('dce_acf_repeater_field_name', ['type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'dynamic' => ['active' => \false]]);
        $repeater_subfields->add_control('dce_acf_repeater_field_type', ['label' => __('ACF Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Select the type', 'dynamic-content-for-elementor'), 'text' => __('Text', 'dynamic-content-for-elementor'), 'textarea' => __('Textarea', 'dynamic-content-for-elementor'), 'image' => __('Image', 'dynamic-content-for-elementor'), 'wysiwyg' => __('WYSIWYG', 'dynamic-content-for-elementor'), 'date_picker' => __('Date Picker', 'dynamic-content-for-elementor'), 'date_time' => __('DateTime', 'dynamic-content-for-elementor'), 'date_time_picker' => __('DateTime Picker', 'dynamic-content-for-elementor'), 'number' => __('Number', 'dynamic-content-for-elementor'), 'select' => __('Select', 'dynamic-content-for-elementor')]]);
        $repeater_subfields->add_control('dce_acf_repeater_field_tag', ['label' => __('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags([], \true), 'default' => 'span']);
        $repeater_subfields->end_controls_tab();
        $repeater_subfields->start_controls_tab('tab_style', ['label' => __('Style', 'dynamic-content-for-elementor')]);
        $repeater_subfields->add_responsive_control('dce_acf_repeater_field_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}}' => 'text-align: {{VALUE}};'], 'condition' => ['dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_responsive_control('dce_acf_repeater_field_no_tag', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('Please select an HTML Tag to choose the style.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['dce_acf_repeater_field_tag' => '']]);
        $repeater_subfields->add_responsive_control('dce_acf_repeater_field_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}}' => 'margin-bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_control('dce_acf_repeater_field_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'after', 'condition' => ['dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_control('dce_acf_repeater_h_texts', ['label' => __('Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['dce_acf_repeater_field_type' => ['text', 'textarea', 'wysiwyg', 'date_picker', 'date_time', 'date_time_picker', 'number', 'select'], 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_control('dce_acf_repeater_field_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}};', '{{WRAPPER}} {{CURRENT_ITEM}} a' => 'color: {{VALUE}};'], 'condition' => ['dce_acf_repeater_field_type' => ['text', 'textarea', 'wysiwyg', 'date_picker', 'date_time', 'date_time_picker', 'number', 'select'], 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_control('dce_acf_repeater_field_hover_color', ['label' => __('Hover Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} a:hover' => 'color: {{VALUE}};'], 'condition' => ['dce_acf_repeater_field_type' => ['text', 'textarea', 'wysiwyg', 'date_picker', 'date_time', 'date_time_picker', 'number', 'select'], 'dce_acf_repeater_enable_link!' => '', 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_acf_repeater_field_typography', 'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}', 'pop_hover' => \true, 'condition' => ['dce_acf_repeater_field_type' => ['text', 'textarea', 'wysiwyg', 'date_picker', 'date_time', 'date_time_picker', 'number', 'select'], 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_control('dce_acf_repeater_h_image', ['label' => __('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['dce_acf_repeater_field_type' => ['image'], 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_responsive_control('dce_acf_repeater_field_size_image', ['label' => __('Max-Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => '%'], 'size_units' => ['%', 'px'], 'range' => ['%' => ['min' => 1, 'max' => 100, 'step' => 1], 'px' => ['min' => 1, 'max' => 800, 'step' => 1]], 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} img' => 'max-width: {{SIZE}}{{UNIT}};'], 'condition' => ['dce_acf_repeater_field_type' => ['image'], 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_group_control(Group_Control_Border::get_type(), ['name' => 'image_border', 'label' => __('Image Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} img', 'condition' => ['dce_acf_repeater_field_type' => ['image'], 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_control('image_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['dce_acf_repeater_field_type' => ['image'], 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'image_box_shadow', 'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} img', 'condition' => ['dce_acf_repeater_field_type' => ['image'], 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_group_control(Group_Control_Css_Filter::get_type(), ['name' => 'filters_image', 'label' => __('Filters image', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} img', 'condition' => ['dce_acf_repeater_field_type' => ['image'], 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->end_controls_tab();
        $repeater_subfields->start_controls_tab('tab_link', ['label' => __('Link', 'dynamic-content-for-elementor')]);
        $repeater_subfields->add_control('dce_acf_repeater_enable_link', ['label' => __('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $repeater_subfields->add_control('dce_acf_repeater_acfield_link', ['label' => __('URL Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'label_block' => \true, 'query_type' => 'acf', 'object_type' => ['url', 'image', 'file', 'link', 'post_object', 'page_link', 'taxonomy', 'user'], 'dynamic' => ['active' => \false], 'frontend_available' => \true, 'condition' => ['dce_acf_repeater_enable_link!' => '']]);
        $repeater_subfields->add_control('dce_acf_repeater_target_link', ['label' => __('Open in a new window', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_acf_repeater_enable_link!' => '', 'dce_acf_repeater_acfield_link!' => '']]);
        $repeater_subfields->add_control('dce_acf_repeater_nofollow_link', ['label' => __('Add nofollow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_acf_repeater_enable_link!' => '', 'dce_acf_repeater_acfield_link!' => '']]);
        $repeater_subfields->end_controls_tab();
        $repeater_subfields->end_controls_tabs();
        $this->add_control('dce_acf_repeater_subfields', ['label' => __('Sub fields', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater_subfields->get_controls(), 'title_field' => '{{{dce_acf_repeater_field_name}}} ({{{dce_acf_repeater_field_type}}})', 'prevent_empty' => \true, 'item_actions' => ['add' => \true, 'duplicate' => \true, 'remove' => \true, 'sort' => \true], 'condition' => ['dce_acf_repeater_mode' => 'subfields']]);
        $this->add_control('dce_acf_repeater_html', ['label' => __('HTML & Tokens', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'default' => '[ROW]', 'description' => __('Type here your content, you can use HTML and Tokens.', 'dynamic-content-for-elementor'), 'condition' => ['dce_acf_repeater_mode' => 'html']]);
        $this->add_control('dce_acf_repeater_template', ['label' => __('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select Template', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'dynamic' => ['active' => \false], 'object_type' => 'elementor_library', 'description' => __('Use an Elementor Template as content of repeater.', 'dynamic-content-for-elementor'), 'condition' => ['dce_acf_repeater_mode' => 'template']]);
        $this->add_control('dce_acf_repeater_template_2_enable', ['label' => __('Different Template for odd rows', 'dynamic-content-for-elementor'), 'description' => __('Manage the appearance of the odd elements.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'render_type' => 'template', 'condition' => ['dce_acf_repeater_mode' => 'template']]);
        $this->add_control('dce_acf_repeater_template_2_id', ['label' => __('Template for odd rows', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select Template', 'dynamic-content-for-elementor'), 'label_block' => \true, 'show_label' => \false, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'render_type' => 'template', 'condition' => ['dce_acf_repeater_mode' => 'template', 'dce_acf_repeater_template_2_enable!' => '']]);
        $this->add_control('dce_acf_repeater_pagination', ['label' => __('Show only these rows', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => \true, 'placeholder' => 'first, last, 1, 3-4, 2n, even', 'description' => __('Leave empty to print all rows, otherwise write their number. Use “first” and “last” to indicate the first and last element. Insert multiple values with a comma.', 'dynamic-content-for-elementor')]);
        $this->end_controls_section();
        $this->start_controls_section('dce_acf_repeater_h_render', ['label' => __('Skin', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_acf_repeater_format', ['label' => __('Skin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'frontend_available' => \true, 'options' => ['' => __('Text', 'dynamic-content-for-elementor'), 'grid' => __('Grid', 'dynamic-content-for-elementor'), 'masonry' => __('Masonry', 'dynamic-content-for-elementor'), 'slider_carousel' => __('Carousel', 'dynamic-content-for-elementor'), 'table' => __('Table', 'dynamic-content-for-elementor'), 'list' => __('List', 'dynamic-content-for-elementor'), 'accordion' => __('Accordion', 'dynamic-content-for-elementor')], 'default' => 'grid']);
        $this->add_control('dce_acf_repeater_separator', ['label' => __('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => ', ', 'condition' => ['dce_acf_repeater_format' => '']]);
        $this->add_control('dce_acf_repeater_accordion_heading', ['label' => __('Heading Text', 'dynamic-content-for-elementor'), 'default' => '[ROW:id]', 'placeholder' => '[ROW:id]', 'description' => __('Write the [ROW:sub_field_name] here, this text will be used as Accordion Heading', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('selected_icon', ['label' => __('Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'separator' => 'before', 'default' => ['value' => 'fas fa-plus', 'library' => 'fa-solid'], 'skin' => 'inline', 'label_block' => \false, 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('selected_active_icon', ['label' => __('Active Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'default' => ['value' => 'fas fa-minus', 'library' => 'fa-solid'], 'skin' => 'inline', 'label_block' => \false, 'condition' => ['dce_acf_repeater_format' => 'accordion', 'selected_icon[value]!' => '']]);
        $this->add_control('dce_acf_repeater_accordion_heading_size', ['label' => __('Heading HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(), 'default' => 'h4', 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('dce_acf_repeater_accordion_start', ['label' => __('Initially open', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['none' => __('None', 'dynamic-content-for-elementor'), 'first' => __('First', 'dynamic-content-for-elementor'), 'custom' => __('Custom Index', 'dynamic-content-for-elementor'), 'all' => __('All', 'dynamic-content-for-elementor')], 'inline' => \true, 'default' => 'none', 'frontend_available' => \true, 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('accordion_start_custom', ['label' => __('Active Index', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'frontend_available' => \true, 'default' => 1, 'condition' => ['dce_acf_repeater_accordion_start' => 'custom', 'dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('dce_acf_repeater_accordion_close', ['label' => __('Automatically close other tabs', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['dce_acf_repeater_format' => 'accordion', 'dce_acf_repeater_accordion_start' => ['none', 'first']]]);
        $this->add_control('accordion_speed', ['label' => __('Speed (ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 300, 'unit' => 'ms'], 'size_units' => ['ms'], 'range' => ['ms' => ['min' => 0, 'max' => 500, 'step' => 50]], 'render_type' => 'template', 'frontend_available' => \true, 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('dce_acf_repeater_list', ['label' => __('List type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['ul' => ['title' => __('Unordered List', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list-ul'], 'ol' => ['title' => __('Ordered List', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list-ol']], 'toggle' => \false, 'default' => 'ul', 'condition' => ['dce_acf_repeater_format' => 'list']]);
        $this->add_control('selected_icon_ul', ['label' => __('Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'separator' => 'before', 'skin' => 'inline', 'label_block' => \false, 'condition' => ['dce_acf_repeater_format' => 'list', 'dce_acf_repeater_list' => 'ul'], 'selectors' => ['{{WRAPPER}} ul > li > .elementor-icon' => 'float: left; clear: both; font-size: inherit;']]);
        $this->add_control('dce_acf_repeater_list_flex', ['label' => __('Flex list', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_acf_repeater_format' => 'list', 'dce_acf_repeater_list' => 'ul', 'selected_icon_ul[value]!' => ''], 'selectors' => ['{{WRAPPER}} ul > li' => 'display: flex;']]);
        $this->add_responsive_control('dce_acf_repeater_col', ['label' => __('Columns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '5', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7'], 'render_type' => 'template', 'selectors' => ['{{WRAPPER}} .dce-acf-repeater-grid' => 'display: flex; flex-wrap: wrap;', '{{WRAPPER}} .dce-acf-repeater-masonry .dce-acf-repeater-item' => 'width: calc( 100% / {{VALUE}} );', '{{WRAPPER}} .dce-acf-repeater-grid .dce-acf-repeater-item' => 'flex: 0 1 calc( 100% / {{VALUE}} );'], 'condition' => ['dce_acf_repeater_format' => ['grid', 'masonry']]]);
        $this->add_control('flex_grow', ['label' => __('Flex grow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'label_block' => \false, 'options' => ['1' => ['title' => '', 'icon' => 'fa fa-check'], '0' => ['title' => __('0', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => 1, 'selectors' => ['{{WRAPPER}} .dce-acf-repeater-grid .dce-acf-repeater-item' => 'flex-grow: {{VALUE}};'], 'condition' => ['dce_acf_repeater_format' => 'grid']]);
        $this->add_responsive_control('flexgrid_mode', ['label' => __('Alignment grid', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'flex-start', 'tablet_default' => '3', 'mobile_default' => '1', 'label_block' => \true, 'options' => ['flex-start' => 'Flex start', 'flex-end' => 'Flex end', 'center' => 'Center', 'space-between' => 'Space Between', 'space-around' => 'Space Around'], 'selectors' => ['{{WRAPPER}} .dce-acf-repeater-grid' => 'justify-content: {{VALUE}};'], 'condition' => ['dce_acf_repeater_format' => 'grid', 'flex_grow' => '0']]);
        $this->add_responsive_control('v_align_items', ['label' => __('Vertical Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex-start' => ['title' => __('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], 'center' => ['title' => __('Middle', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'flex-end' => ['title' => __('Down', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom'], 'stretch' => ['title' => __('Stretch', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-stretch']], 'default' => 'top', 'selectors' => ['{{WRAPPER}} .dce-acf-repeater-item' => 'align-self: {{VALUE}};'], 'condition' => ['dce_acf_repeater_format' => 'grid', 'flex_grow' => '0']]);
        $this->add_control('dce_acf_repeater_thead', ['label' => __('Table Heading', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_acf_repeater_format' => 'table']]);
        $this->add_control('dce_acf_repeater_thead_custom', ['label' => __('Custom Table Heading', 'dynamic-content-for-elementor'), 'description' => __('Set custom HTML & Tokens for the table heading', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_acf_repeater_thead!' => '', 'dce_acf_repeater_format' => 'table', 'dce_acf_repeater_mode' => 'html']]);
        $this->add_control('dce_acf_repeater_thead_custom_html', ['label' => __('Table Heading with HTML & Tokens', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'description' => \sprintf(__('Remember to use the HTML %1$s element to define a set of rows defining the head of the columns of the table.', 'dynamic-content-for-elementor'), '<strong>thead</strong>'), 'condition' => ['dce_acf_repeater_thead!' => '', 'dce_acf_repeater_format' => 'table', 'dce_acf_repeater_thead_custom!' => '', 'dce_acf_repeater_mode' => 'html']]);
        $this->add_control('dce_acf_repeater_datatables', ['label' => __('Use DataTables', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Add advanced interaction controls to your HTML tables.', 'dynamic-content-for-elementor') . '<br><small>' . __('Read more on ', 'dynamic-content-for-elementor') . ' <a href="https://datatables.net/" target="_blank">DataTables</a></small>', 'condition' => ['dce_acf_repeater_format' => 'table']]);
        $this->end_controls_section();
        $this->start_controls_section('dce_acf_repeater_datatables_section', ['label' => __('DataTables', 'dynamic-content-for-elementor'), 'condition' => ['dce_acf_repeater_format' => 'table', 'dce_acf_repeater_datatables!' => '']]);
        $this->add_control('heading_acf_repeater_datatables_extensions', ['label' => __('DataTables Extensions', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::RAW_HTML, 'raw' => '<br><small>' . __('Read more on ', 'dynamic-content-for-elementor') . ' <a href="https://datatables.net/extensions/index" target="_blank">DataTables Extensions</a></small>', 'separator' => 'before']);
        $this->add_control('dce_acf_repeater_style_table_data_autofill', ['label' => __('Autofill', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Excel-like click and drag copying and filling of data.', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_acf_repeater_style_table_data_buttons', ['label' => __('Buttons', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('A common framework for user interaction buttons. Like Export and Print.', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_acf_repeater_style_table_data_colreorder', ['label' => __('ColReorder', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Click-and-drag column reordering.', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_acf_repeater_style_table_data_fixedcolumns', ['label' => __('FixedColumns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Fix one or more columns to the left or right of a scrolling table.', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_acf_repeater_style_table_data_fixedheader', ['label' => __('FixedHeader', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Sticky header and / or footer for the table.', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_acf_repeater_style_table_data_keytable', ['label' => __('KeyTable', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Keyboard navigation of cells in a table, just like a spreadsheet.', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_acf_repeater_style_table_data_responsive', ['label' => __('Responsive', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Dynamically show and hide columns based on the browser size.', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_acf_repeater_style_table_data_rowgroup', ['label' => __('RowGroup', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Show similar data grouped together by a custom data point.', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_acf_repeater_style_table_data_rowreorder', ['label' => __('RowReorder', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Click-and-drag reordering of rows.', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_acf_repeater_style_table_data_scroller', ['label' => __('Scroller', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Virtual rendering of a scrolling table for large data sets.', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_acf_repeater_style_table_data_scroller_y', ['label' => __('Scroller Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'default' => '200', 'description' => __('Height of virtual scroller.', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_acf_repeater_style_table_data_select', ['label' => __('Select', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Adds row, column and cell selection abilities to a table.', 'dynamic-content-for-elementor')]);
        $this->end_controls_section();
        $this->start_controls_section('section_filter', ['label' => __('Filter', 'dynamic-content-for-elementor')]);
        $this->add_control('filter', ['label' => __('Filter', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $repeater_filter = new \Elementor\Repeater();
        $repeater_filter->add_control('filter_field', ['type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'dynamic' => ['active' => \false]]);
        $repeater_filter->add_control('filter_operator', ['label' => __('Status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'label_block' => \true, 'options' => Helper::compare_options(), 'default' => 'isset', 'toggle' => \false]);
        $repeater_filter->add_control('filter_value', ['type' => Controls_Manager::TEXT, 'label' => __('Value', 'dynamic-content-for-elementor'), 'condition' => ['filter_operator!' => ['not', 'isset']]]);
        $this->add_control('filters', ['label' => __('Conditions', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater_filter->get_controls(), 'title_field' => '{{{filter_field}}}', 'prevent_empty' => \false, 'item_actions' => ['add' => \true, 'duplicate' => \true, 'remove' => \true, 'sort' => \true], 'condition' => ['filter!' => '']]);
        $this->add_control('filters_relationship', ['label' => __('Logical Relationship', 'dynamic-content-for-elementor'), 'description' => __('The logical relationship when there is more than one', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['AND' => 'AND', 'OR' => 'OR'], 'toggle' => \false, 'default' => 'AND', 'condition' => ['filter!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'condition' => ['acf_repeater_from' => 'current_post']]);
        $this->add_control('data_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'description' => __('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => __('Same', 'dynamic-content-for-elementor'), 'label_off' => __('other', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_control('other_post_source', ['label' => __('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
        //
        //////////////////////////////////////////////////////////// [ SECTION Slider & Carousel ]
        //
        // ------------------------------ Base Settings, Slides grid, Grab Cursor
        $this->start_controls_section('section_slidercarousel_mode', ['label' => __('Carousel', 'dynamic-content-for-elementor'), 'condition' => ['dce_acf_repeater_format' => 'slider_carousel']]);
        // -------------------------------- Progressione ------
        // da valutare ....
        $this->add_control('effects', ['label' => __('Transition Effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['slide' => __('Slide', 'dynamic-content-for-elementor'), 'fade' => __('Fade', 'dynamic-content-for-elementor'), 'cube' => __('Cube', 'dynamic-content-for-elementor'), 'coverflow' => __('Coverflow', 'dynamic-content-for-elementor'), 'flip' => __('Flip', 'dynamic-content-for-elementor')], 'default' => 'slide', 'frontend_available' => \true]);
        $this->add_responsive_control('slidesPerView', ['label' => __('Slides Per View', 'dynamic-content-for-elementor'), 'description' => __('Number of slides visible at the same time on slider\'s container).', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '1', 'min' => 1, 'max' => 12, 'step' => 1, 'frontend_available' => \true]);
        $this->add_responsive_control('slidesColumn', ['label' => __('Slides Column', 'dynamic-content-for-elementor'), 'description' => __('Number of slides per column, for multirow layout.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '1', 'min' => 1, 'max' => 4, 'step' => 1, 'frontend_available' => \true]);
        $this->add_responsive_control('slidesPerGroup', ['label' => __('Slides Per Group', 'dynamic-content-for-elementor'), 'description' => __('Set numbers of slides to define and enable group sliding. Useful to use with Slides Per View > 1', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'tablet_default' => '', 'mobile_default' => '', 'min' => 1, 'max' => 12, 'step' => 1, 'frontend_available' => \true]);
        $this->add_control('direction_slider', ['label' => __('Direction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'options' => ['horizontal' => __('Horizontal', 'dynamic-content-for-elementor'), 'vertical' => __('Vertical', 'dynamic-content-for-elementor')], 'default' => 'horizontal', 'frontend_available' => \true]);
        $this->add_control('speed_slider', ['label' => __('Speed', 'dynamic-content-for-elementor'), 'description' => __('Duration of transition between slides (in ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 300, 'min' => 0, 'max' => 3000, 'step' => 10, 'frontend_available' => \true]);
        $this->add_responsive_control('spaceBetween', ['label' => __('Space Between', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'tablet_default' => '', 'mobile_default' => '', 'min' => 0, 'max' => 100, 'step' => 1, 'frontend_available' => \true]);
        $this->add_control(
            // Added a 2 because we did not want the previous broken setting
            // value to influence the result:
            'centeredSlides2',
            ['label' => __('Centered Slides', 'dynamic-content-for-elementor'), 'description' => __('If true, then active slide will be centered, not always on the left side.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]
        );
        $this->add_control('centerInsufficientSlides', ['label' => __('Center Insufficient Slides', 'dynamic-content-for-elementor'), 'description' => __('When enabled it center slides if the amount of slides less than slidesPerView', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        // -------------------------------- Free Mode ------
        $this->add_control('freemode_options', ['label' => __('Free Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('freeMode', ['label' => __('Free Mode', 'dynamic-content-for-elementor'), 'description' => __('If true then slides will not have fixed positions', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('freeModeMomentum', ['label' => __('Free Mode Momentum', 'dynamic-content-for-elementor'), 'description' => __('If true, then slide will keep moving for a while after you release it', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->add_control('freeModeMomentumRatio', ['label' => __('Free Mode Momentum Ratio', 'dynamic-content-for-elementor'), 'description' => __('Higher value produces larger momentum distance after you release slider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentum' => 'yes']]);
        $this->add_control('freeModeMomentumVelocityRatio', ['label' => __('Free Mode Momentum Velocity Ratio', 'dynamic-content-for-elementor'), 'description' => __('Higher value produces larger momentum speed after you release slider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentum' => 'yes']]);
        $this->add_control('freeModeMomentumBounce', ['label' => __('Free Mode Momentum Bounce', 'dynamic-content-for-elementor'), 'description' => __('Set to false if you want to disable momentum bounce in free mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->add_control('freeModeMomentumBounceRatio', ['label' => __('Free Mode Momentum Bounce Ratio', 'dynamic-content-for-elementor'), 'description' => __('Higher value produces larger momentum bounce effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentumBounce' => 'yes']]);
        $this->add_control('freeModeMinimumVelocity', ['label' => __('Free Mode Momentum Velocity Ratio', 'dynamic-content-for-elementor'), 'description' => __('Minimum touchmove-velocity required to trigger free mode momentum', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0.02, 'min' => 0, 'max' => 1, 'step' => 0.01, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->add_control('freeModeSticky', ['label' => __('Free Mode Sticky', 'dynamic-content-for-elementor'), 'description' => __('Set \'yes\' to enable snap to slides positioned in free mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        // -------------------------------- Navigation options ------
        $this->add_control('navigation_options', ['label' => __('Navigation options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('useNavigation', ['label' => __('Use Navigation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        // ------------------------------------------------- Navigations Arrow Options
        $this->add_control('navigation_arrow_color', ['label' => __('Arrows color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-button-next path, {{WRAPPER}} .swiper-button-prev path, ' => 'fill: {{VALUE}};', '{{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev line, {{WRAPPER}} .swiper-button-next polyline, {{WRAPPER}} .swiper-button-prev polyline' => 'stroke: {{VALUE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('useNavigation_animationHover', ['label' => __('Use animation in rollover', 'dynamic-content-for-elementor'), 'description' => __('A short animation will take place at the rollover.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'prefix_class' => 'hoveranim-', 'separator' => 'before', 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('navigation_arrow_color_hover', ['label' => __('Hover color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-button-next:hover path, {{WRAPPER}} .swiper-button-prev:hover path, ' => 'fill: {{VALUE}};', '{{WRAPPER}} .swiper-button-next:hover line, {{WRAPPER}} .swiper-button-prev:hover line, {{WRAPPER}} .swiper-button-next:hover polyline, {{WRAPPER}} .swiper-button-prev:hover polyline' => 'stroke: {{VALUE}};'], 'condition' => ['useNavigation' => 'yes'], 'separator' => 'after']);
        $this->add_responsive_control('pagination_stroke_1', ['label' => __('Stroke Arrow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev polyline, {{WRAPPER}} .swiper-button-next polyline' => 'stroke-width: {{SIZE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('pagination_stroke_2', ['label' => __('Stroke Line', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev line' => 'stroke-width: {{SIZE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('pagination_tratteggio', ['label' => __('Dashed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0'], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev line, {{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev polyline, {{WRAPPER}} .swiper-button-next polyline' => 'stroke-dasharray: {{SIZE}},{{SIZE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('pagination_scale', ['label' => __('Scale', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 2, 'min' => 0.1, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'transform: scale({{SIZE}});'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('pagination_position', ['label' => __('Horizontal Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'size_units' => ['px'], 'range' => ['px' => ['max' => 100, 'min' => -100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .swiper-button-next' => 'right: {{SIZE}}{{UNIT}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('pagination_position_v', ['label' => __('Vertical Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 50, 'unit' => '%'], 'size_units' => ['%', 'px'], 'range' => ['%' => ['max' => 120, 'min' => -20, 'step' => 1], 'px' => ['max' => 200, 'min' => -200, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'top: {{SIZE}}{{UNIT}};'], 'condition' => ['useNavigation' => 'yes']]);
        // --------------------------------------------------- Pagination options ------
        $this->add_control('pagination_options', ['label' => __('Pagination options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('usePagination', ['label' => __('Pagination', 'dynamic-content-for-elementor'), 'description' => __('Use the slide progression display system ("bullets", "fraction", "progress").', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('pagination_type', ['label' => __('Pagination Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['bullets' => __('Bullets', 'dynamic-content-for-elementor'), 'fraction' => __('Fraction', 'dynamic-content-for-elementor'), 'progress' => __('Progress', 'dynamic-content-for-elementor')], 'default' => 'bullets', 'frontend_available' => \true, 'condition' => ['usePagination' => 'yes']]);
        // ------------------------------------------------- Pagination Fraction Options
        $this->add_control('fraction_separator', ['label' => __('Fraction text separator', 'dynamic-content-for-elementor'), 'description' => __('The text separating the 2 numbers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'frontend_available' => \true, 'default' => '/', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_responsive_control('fraction_space', ['label' => __('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '4', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => -20, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .separator' => 'margin: 0 {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_control('fraction_color', ['label' => __('Numbers color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction > *' => 'color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fraction_typography', 'label' => __('Numbers Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-fraction > *', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_control('fraction_current_color', ['label' => __('Current number color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current' => 'color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fraction_typography_current', 'label' => __('Current number typography', 'dynamic-content-for-elementor'), 'default' => '', 'selector' => '{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_control('fraction_separator_color', ['label' => __('Separator color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .separator' => 'color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fraction_typography_separator', 'label' => __('Typography separator', 'dynamic-content-for-elementor'), 'default' => '', 'selector' => '{{WRAPPER}} .swiper-pagination-fraction .separator', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        // ------------------------------------------------- Pagination Bullets Options
        $this->add_responsive_control('bullets_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '5', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_responsive_control('pagination_bullets', ['label' => __('Bullets size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '8', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_responsive_control('pagination_posy', ['label' => __('Shift', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '10', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => -160, 'max' => 160]], 'selectors' => ['{{WRAPPER}} .swiper-pagination' => ' bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => ['bullets', 'fraction']]]);
        $this->add_responsive_control('pagination_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '10', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 60]], 'selectors' => ['{{WRAPPER}} .swiper-pagination' => 'padding-right: {{SIZE}}{{UNIT}}; padding-left: {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => ['bullets', 'fraction']]]);
        $this->add_control('bullets_color', ['label' => __('Bullets Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'background-color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_bullet', 'label' => __('Bullets border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_responsive_control('current_bullet', ['label' => __('Active bullet size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .swiper-pagination.swiper-pagination-bullets' => 'height: {{SIZE}}{{UNIT}}'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_control('current_bullet_color', ['label' => __('Active bullet color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_current_bullet', 'label' => __('Active bullet border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_control('progress_color', ['label' => __('Progress color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-progress' => 'background-color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'progress']]);
        $this->add_control('progressbar_color', ['label' => __('Progressbar color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-progress .swiper-pagination-progressbar' => 'background-color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'progress']]);
        $this->add_responsive_control('h_align_pagination', ['label' => __('Horizontal Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'selectors' => ['{{WRAPPER}} .swiper-pagination' => 'text-align: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => ['bullets', 'fraction']]]);
        // -------------------------------- Autoplay ------
        $this->add_control('autoplay_options', ['label' => __('Autoplay options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('useAutoplay', ['label' => __('Use Autoplay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('autoplay', ['label' => __('Autoplay Delay (ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 3000, 'min' => 0, 'max' => 7000, 'step' => 100, 'frontend_available' => \true, 'condition' => ['useAutoplay' => 'yes']]);
        $this->add_control('autoplayStopOnHover', ['label' => __('Autoplay stop on hover', 'dynamic-content-for-elementor'), 'description' => __('Enable this parameter and autoplay will be stopped on hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['useAutoplay' => 'yes']]);
        $this->add_control('autoplayStopOnLast', ['label' => __('Autoplay stop on last slide', 'dynamic-content-for-elementor'), 'description' => __('Enable this parameter and autoplay will be stopped when it reaches the last slide (has no effect in loop mode)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['autoplay!' => '']]);
        $this->add_control('autoplayDisableOnInteraction', ['label' => __('Autoplay Disable on interaction', 'dynamic-content-for-elementor'), 'description' => __('Set to "false" and autoplay will not be disabled after user interactions (swipes), it will be restarted every time after interaction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['autoplay!' => '']]);
        // -------------------------------- Keyboard ------
        $this->add_control('keyboard_options', ['label' => __('Keyboard / Mousewheel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('keyboardControl', ['label' => __('Keyboard Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('mousewheelControl', ['label' => __('Mousewheel Control', 'dynamic-content-for-elementor'), 'description' => __('Enables navigation through slides using mouse wheel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        // -------------------------------- Ciclo ------
        $this->add_control('cicleloop_options', ['label' => __('Cicle / Loop', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('loop', ['label' => __('Loop', 'dynamic-content-for-elementor'), 'description' => __('Set it to true to enable continuous loop mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        // -------------------------------- Special options ---------
        $this->add_control('special_options', ['label' => __('Specials options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('autoHeight', ['label' => __('Auto Height', 'dynamic-content-for-elementor'), 'description' => __('Set to true and slider wrapper will adopt its height to the height of the currently active slide', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('grabCursor', ['label' => __('Grab Cursor', 'dynamic-content-for-elementor'), 'description' => __('This option may improve desktop usability. If true, user will see the “grab” cursor when hover on Swiper', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->end_controls_section();
        // ACCORDION
        $this->start_controls_section('section_toggle_style', ['label' => $this->get_title(), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} ']);
        $this->add_control('color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}}' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'imgsize', 'label' => __('Image Size', 'dynamic-content-for-elementor'), 'default' => 'large', 'render_type' => 'template', 'condition' => ['dce_acf_repeater_mode' => 'subfields']]);
        $this->add_responsive_control('alignment', ['label' => __('Global Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};'], 'condition' => ['dce_acf_repeater_mode!' => 'template']]);
        $this->add_responsive_control('th_alignment', ['label' => __('Table - Heading Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} th' => 'text-align: {{VALUE}};'], 'condition' => ['dce_acf_repeater_format' => 'table']]);
        $this->add_responsive_control('td_alignment', ['label' => __('Table - Rows Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} td' => 'text-align: {{VALUE}};'], 'condition' => ['dce_acf_repeater_format' => 'table']]);
        $this->add_responsive_control('dce_acf_repeater_field_col', ['label' => __('Col space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .dce-acf-repeater-item' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('dce_acf_repeater_field_row', ['label' => __('Row space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .dce-acf-repeater-item' => 'padding-bottom: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_title_style', ['label' => __('Accordion', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('section_toggle_style_title', ['label' => __('Heading', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('title_background', ['label' => __('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_head' => 'background-color: {{VALUE}};']]);
        $this->add_control('title_active_background', ['label' => __('Active Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_active .acc_head' => 'background-color: {{VALUE}};']]);
        $this->add_control('title_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_head *' => 'color: {{VALUE}};']]);
        $this->add_control('tab_active_color', ['label' => __('Active Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_active .acc_head *' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'title_typography', 'selector' => '{{WRAPPER}} .acc_head']);
        $this->add_responsive_control('title_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .acc_head' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('tab_heading', ['label' => __('Tab', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_width', 'label' => __('Border Width', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .acc_section']);
        $this->add_responsive_control('tab_space', ['label' => __('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 100]], 'default' => ['unit' => 'px', 'size' => 0], 'selectors' => ['{{WRAPPER}} .acc_section:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_control('section_toggle_style_icon', ['label' => __('Icons', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('icon_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Start', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'right' => ['title' => __('End', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => is_rtl() ? 'right' : 'left', 'toggle' => \false]);
        $this->add_control('icon_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_head i:before' => 'color: {{VALUE}};', '{{WRAPPER}} .acc_head svg' => 'fill: {{VALUE}};']]);
        $this->add_control('icon_active_color', ['label' => __('Active Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_active .acc_head i:before' => 'color: {{VALUE}};', '{{WRAPPER}} .acc_active .acc_head svg' => 'fill: {{VALUE}};']]);
        $this->add_responsive_control('icon_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .acc_head .icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', '{{WRAPPER}} .acc_head .icon-active' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('icon_space', ['label' => __('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .acc_head .icon' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .acc_head .icon-active' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: {{SIZE}}{{UNIT}};']]);
        $this->add_control('section_toggle_style_content', ['label' => __('Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('content_background_color', ['label' => __('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_content' => 'background-color: {{VALUE}};']]);
        $this->add_control('content_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_content' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'content_typography', 'selector' => '{{WRAPPER}} .acc_content']);
        $this->add_responsive_control('content_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .acc_content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('content_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .acc_content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'content_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .acc_content']);
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
        global $repeater_counter;
        if (empty($repeater_counter)) {
            $repeater_counter = 1;
        } else {
            $repeater_counter++;
        }
        $sub_fields_tokens = Helper::get_acf_repeater_fields($settings['dce_acf_repeater']);
        if ($settings['dce_acf_repeater']) {
            $id_page = null;
            switch ($settings['acf_repeater_from']) {
                case 'current_post':
                    $id_page = Helper::get_the_id($settings['other_post_source']);
                    break;
                case 'current_user':
                    $user_id = get_current_user_id();
                    $id_page = 'user_' . $user_id;
                    break;
                case 'current_author':
                    $user_id = get_the_author_meta('ID');
                    $id_page = 'user_' . $user_id;
                    break;
                case 'current_term':
                    $queried_object = get_queried_object();
                    if (!empty($queried_object) && \is_object($queried_object) && \get_class($queried_object) == 'WP_Term') {
                        $taxonomy = $queried_object->taxonomy;
                        $term_id = $queried_object->term_id;
                        $id_page = $taxonomy . '_' . $term_id;
                    }
                    break;
                case 'options_page':
                    $id_page = 'options';
                    break;
            }
            // Sub-fields
            $sub_fields = Helper::get_acf_repeater_fields($settings['dce_acf_repeater']);
            // Inizialize layout
            $this->add_render_attribute('container', 'class', 'dce-acf-repeater');
            echo '<div ' . $this->get_render_attribute_string('container') . '>';
            if ($settings['dce_acf_repeater_mode'] == 'subfields' && empty($settings['dce_acf_repeater_subfields']) && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
                Helper::notice('', __('Select at least one sub field', 'dynamic-content-for-elementor'));
            }
            $this->add_render_attribute('wrapper', 'class', 'dce-acf-repeater-' . $settings['dce_acf_repeater_format']);
            if ($settings['dce_acf_repeater_format'] == 'slider_carousel') {
                $swiper_class = \Elementor\Plugin::$instance->experiments->is_feature_active('e_swiper_latest') ? 'swiper' : 'swiper-container';
                $this->add_render_attribute('wrapper', 'class', $swiper_class);
                $this->add_render_attribute('wrapper', 'class', 'swiper-container-' . $settings['direction_slider']);
                $this->add_render_attribute('wrapper', 'counter-id', $repeater_counter);
            }
            if ($settings['dce_acf_repeater_format'] == 'list' && $settings['dce_acf_repeater_list'] == 'ul' && !empty($settings['selected_icon_ul']['value'])) {
                $this->add_render_attribute('wrapper', 'class', 'dce-no-list');
            }
            if ($settings['dce_acf_repeater_format'] == 'table' && !empty($settings['dce_acf_repeater_datatables'])) {
                $this->add_render_attribute('wrapper', 'class', 'dce-datatable');
            }
            switch ($settings['dce_acf_repeater_format']) {
                case 'list':
                    echo '<' . $settings['dce_acf_repeater_list'] . ' ' . $this->get_render_attribute_string('wrapper') . '>';
                    break;
                case 'grid':
                case 'masonry':
                    echo '<div ' . $this->get_render_attribute_string('wrapper') . '>';
                    break;
                case 'slider_carousel':
                    echo '<div ' . $this->get_render_attribute_string('wrapper') . '><div class="swiper-wrapper">';
                    break;
                case 'table':
                    echo '<table ' . $this->get_render_attribute_string('wrapper') . '>';
                    if ($settings['dce_acf_repeater_thead'] && !empty($sub_fields)) {
                        echo '<thead>';
                        if ($settings['dce_acf_repeater_mode'] !== 'subfields' && empty($settings['dce_acf_repeater_thead_custom'])) {
                            foreach ($sub_fields as $key => $acfitem) {
                                echo '<th>' . $acfitem['title'] . '</th>';
                            }
                        } elseif ($settings['dce_acf_repeater_mode'] !== 'subfields' && !empty($settings['dce_acf_repeater_thead_custom'])) {
                            echo do_shortcode(Helper::get_dynamic_value($settings['dce_acf_repeater_thead_custom_html']));
                        } else {
                            while (have_rows($settings['dce_acf_repeater'], $id_page)) {
                                the_row();
                                if (get_row_index() === 1) {
                                    foreach ($settings['dce_acf_repeater_subfields'] as $key => $value) {
                                        $subfield_object = get_sub_field_object($value['dce_acf_repeater_field_name'], $id_page);
                                        echo '<th>' . $subfield_object['label'] . '</th>';
                                    }
                                } else {
                                    break;
                                }
                            }
                            reset_rows();
                        }
                        echo '</thead>';
                    }
                    break;
                case 'accordion':
                    ?>
					<ul class="accordion">
					<?php 
                    break;
            }
            $my_fields = get_field_object($settings['dce_acf_repeater'], $id_page);
            // If my_fields is empty probably we are on a nested repeater, so get the object from the subfield
            if (empty($my_fields)) {
                $my_fields = get_sub_field_object($settings['dce_acf_repeater'], $id_page);
            }
            $repeater_count = 0;
            if (isset($my_fields['value']) && (\is_array($my_fields['value']) || \is_object($my_fields['value']))) {
                $repeater_count = \count($my_fields['value']);
            }
            $paginations = $this->get_pagination($settings['dce_acf_repeater_pagination'], $repeater_count);
            // Iterate all rows
            while (have_rows($settings['dce_acf_repeater'], $id_page)) {
                the_row();
                // Filter
                $sub_fields_to_filter = $my_fields['value'][get_row_index() - 1] ?? '';
                if (!empty($settings['filter']) && \is_array($sub_fields_to_filter) && !$this->is_filter_satisfied($sub_fields_to_filter)) {
                    continue;
                }
                // Render a single sub-field
                if (empty($paginations) || \in_array(get_row_index(), $paginations)) {
                    switch ($settings['dce_acf_repeater_format']) {
                        case 'list':
                            echo '<li class="dce-acf-repeater-item">';
                            if ($settings['dce_acf_repeater_list'] == 'ul') {
                                Icons_Manager::render_icon($settings['selected_icon_ul'], ['aria-hidden' => 'true']);
                            }
                            break;
                        case 'grid':
                            echo '<div class="dce-acf-repeater-item">';
                            break;
                        case 'masonry':
                            echo '<div class="dce-acf-repeater-item">';
                            break;
                        case 'slider_carousel':
                            echo '<div class="dce-acf-repeater-item swiper-slide">';
                            break;
                        case 'table':
                            echo '<tr>';
                            break;
                        case 'accordion':
                            ?>
							<li class="item">
								<div class="heading"><?php 
                            $this->render_accordion_heading();
                            ?></div>
								<div>
							<?php 
                    }
                    // HTML & Tokens
                    if ($settings['dce_acf_repeater_mode'] == 'html') {
                        foreach ($sub_fields_tokens as $key => $value) {
                            $text = $settings['dce_acf_repeater_html'];
                            $value = get_sub_field($key);
                            $sub_fields_tokens_row[get_row_index()][$key] = $value;
                        }
                        echo do_shortcode(Tokens::replace_var_tokens($text, 'ROW', $sub_fields_tokens_row[get_row_index()]));
                    } elseif ($settings['dce_acf_repeater_mode'] == 'template') {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $inlinecss = 'inlinecss="true"';
                        } else {
                            $inlinecss = '';
                        }
                        $template_id = $settings['dce_acf_repeater_template'];
                        if ($settings['dce_acf_repeater_template_2_enable'] && get_row_index() % 2) {
                            $template_id = $settings['dce_acf_repeater_template_2_id'];
                        }
                        echo do_shortcode('[dce-elementor-template id="' . $template_id . '" ' . $inlinecss . ']');
                    } else {
                        foreach ($settings['dce_acf_repeater_subfields'] as $key => $value) {
                            $sub_field = get_sub_field($value['dce_acf_repeater_field_name']);
                            $sub_field_settings = Helper::get_acf_field_settings($value['dce_acf_repeater_field_name']);
                            $sub_field_link = '';
                            if ($value['dce_acf_repeater_acfield_link']) {
                                // ACF Subfield
                                $sub_field_link = get_sub_field($value['dce_acf_repeater_acfield_link']);
                                // ACF Field
                                if (!$sub_field_link) {
                                    $sub_field_link = \get_field($value['dce_acf_repeater_acfield_link']);
                                }
                            }
                            if (!empty($sub_field) || $sub_field === '0' || $sub_field === 0) {
                                $field_type = '';
                                if (\is_array($sub_field_settings)) {
                                    $field_type = $sub_field_settings['type'];
                                }
                                switch ($field_type) {
                                    case 'wysiwyg':
                                        $subfield_value = wpautop($sub_field);
                                        break;
                                    case 'image':
                                        $imageAlt = '';
                                        if (\is_string($sub_field)) {
                                            $imageSrc = $sub_field;
                                        } elseif (\is_numeric($sub_field)) {
                                            $imageSrc = Group_Control_Image_Size::get_attachment_image_src($sub_field, 'imgsize', $settings);
                                            $imageAlt = get_post_meta($sub_field, '_wp_attachment_image_alt', \true);
                                        } elseif (\is_array($sub_field)) {
                                            $imageSrc = Group_Control_Image_Size::get_attachment_image_src($sub_field['ID'], 'imgsize', $settings);
                                            $imageAlt = $sub_field['alt'];
                                        }
                                        $subfield_value = '<img src="' . $imageSrc . '" alt="' . $imageAlt . '" />';
                                        break;
                                    case 'date_time_picker':
                                    case 'date_picker':
                                    case 'url':
                                    case 'text':
                                    case 'textarea':
                                    default:
                                        $subfield_value = Helper::to_string($sub_field, \true);
                                }
                                if (!empty($sub_field_link) && !empty($value['dce_acf_repeater_enable_link'])) {
                                    $targetLink = '';
                                    if (!empty($value['dce_acf_repeater_target_link'])) {
                                        $targetLink .= ' target="_blank"';
                                    }
                                    if (!empty($value['dce_acf_repeater_nofollow_link'])) {
                                        $targetLink .= ' rel="nofollow"';
                                    }
                                    if (!$sub_field_link instanceof \WP_Term) {
                                        $subfield_value = '<a href="' . $sub_field_link . '"' . $targetLink . '>' . $subfield_value . '</a>';
                                    }
                                }
                                $subfield_value = Helper::to_string($subfield_value);
                                if ($value['dce_acf_repeater_field_tag']) {
                                    $subfield_value = '<' . \DynamicContentForElementor\Helper::validate_html_tag($value['dce_acf_repeater_field_tag']) . ' class="repeater-item elementor-repeater-item-' . $value['_id'] . '">' . $subfield_value . '</' . \DynamicContentForElementor\Helper::validate_html_tag($value['dce_acf_repeater_field_tag']) . '>';
                                }
                                if ($settings['dce_acf_repeater_format'] == 'table') {
                                    echo '<td>';
                                }
                                echo $subfield_value;
                                if ($settings['dce_acf_repeater_format'] == 'table') {
                                    echo '</td>';
                                }
                            } elseif (empty($sub_field) && $settings['dce_acf_repeater_format'] == 'table') {
                                echo '<td></td>';
                            }
                        }
                    }
                    switch ($settings['dce_acf_repeater_format']) {
                        case '':
                            if (get_row_index() < $repeater_count) {
                                echo wp_kses_post($settings['dce_acf_repeater_separator']);
                            }
                            break;
                        case 'list':
                            echo '</li>';
                            break;
                        case 'grid':
                        case 'masonry':
                        case 'slider_carousel':
                            echo '</div>';
                            break;
                        case 'accordion':
                            echo '</div></li>';
                            break;
                        case 'table':
                            echo '</tr>';
                    }
                }
            }
            switch ($settings['dce_acf_repeater_format']) {
                case 'list':
                    echo '</' . $settings['dce_acf_repeater_list'] . '>';
                    break;
                case 'grid':
                case 'masonry':
                    echo '</div>';
                    break;
                case 'accordion':
                    echo '</ul>';
                    break;
                case 'slider_carousel':
                    echo '</div></div>';
                    break;
                case 'table':
                    echo '</table>';
                    if ($settings['dce_acf_repeater_datatables']) {
                        ?>
					<script type="text/javascript">
							jQuery(function () {
							jQuery('.elementor-element-<?php 
                        echo $this->get_id();
                        ?> table.dce-datatable').DataTable({
							language: {
						url: '<?php 
                        echo DCE_URL . 'assets/lib/datatables/i18n/' . Helper::get_datatables_language() . '.json';
                        ?>'
					},
							order: [],
							<?php 
                        if ($settings['dce_acf_repeater_style_table_data_autofill']) {
                            ?>autoFill: true,<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_acf_repeater_style_table_data_autofill']) {
                            ?>autoFill: true,<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_acf_repeater_style_table_data_buttons']) {
                            ?>dom: 'Bfrtip',
											buttons: [
													'copyHtml5',
													'excelHtml5',
													'csvHtml5',
													'pdfHtml5'
											],<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_acf_repeater_style_table_data_colreorder']) {
                            ?>colReorder: true,<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_acf_repeater_style_table_data_fixedcolumns']) {
                            ?>fixedColumns: true,<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_acf_repeater_style_table_data_fixedheader']) {
                            ?>fixedHeader: true,<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_acf_repeater_style_table_data_keytable']) {
                            ?>keys: true,<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_acf_repeater_style_table_data_responsive']) {
                            ?>responsive: true,<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_acf_repeater_style_table_data_rowgroup']) {
                            ?>rowGroup: {
									dataSrc: 'group'
									},<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_acf_repeater_style_table_data_rowreorder']) {
                            ?>rowReorder: true,<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_acf_repeater_style_table_data_scroller']) {
                            ?>scroller: true,
											scrollX: true,
								<?php 
                            if (!empty($settings['dce_acf_repeater_style_table_data_scroller_y'])) {
                                ?>scrollY: 200,<?php 
                            }
                            ?>
										paging: true,
												deferRender: true,<?php 
                        } else {
                            ?>
										paging: false,
							<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_acf_repeater_style_table_data_select']) {
                            ?>select: true,<?php 
                        }
                        ?>

									ordering: true,
									});
									});</script>
									<?php 
                    }
            }
            // NOTE the pagination and navigation for the swiper is outside its container so I can move the elements around as I please, since the container is in overflow: hidden, and if they were inside (as by default) they would hide outside the area.
            if ($settings['usePagination']) {
                // Add Pagination
                echo '<div class="swiper-container-' . $settings['direction_slider'] . '"><div class="swiper-pagination pagination-' . $this->get_id() . ' pagination-' . $repeater_counter . '"></div></div>';
            }
            if ($settings['useNavigation']) {
                // Add Arrows
                echo '<div class="swiper-button-prev prev-' . $this->get_id() . ' prev-' . $repeater_counter . '"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
											width="85.039px" height="85.039px" viewBox="378.426 255.12 85.039 85.039" enable-background="new 378.426 255.12 85.039 85.039"
											xml:space="preserve">
											<line fill="none" stroke="#000000" stroke-width="1.3845" stroke-dasharray="0,0" stroke-miterlimit="10" x1="382.456" y1="298.077" x2="458.375" y2="298.077"/>
											<polyline fill="none" stroke="#000000" stroke-width="1.3845" stroke-dasharray="0,0" stroke-miterlimit="10" points="416.287,331.909,382.456,298.077
											416.287,264.245 "/>
											</svg></div>';
                echo '<div class="swiper-button-next next-' . $this->get_id() . ' next-' . $repeater_counter . '"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
											width="85.039px" height="85.039px" viewBox="378.426 255.12 85.039 85.039" enable-background="new 378.426 255.12 85.039 85.039"
											xml:space="preserve">
											<line fill="none" stroke="#000000" stroke-width="1.3845" stroke-miterlimit="10" x1="458.375" y1="298.077" x2="382.456" y2="298.077"/>
											<polyline fill="none" stroke="#000000" stroke-width="1.3845" stroke-miterlimit="10" points="424.543,264.245,458.375,298.077
											424.543,331.909 "/>
											</svg></div>';
            }
            echo '</div>';
        } elseif (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            Helper::notice('', __('Select an ACF Repeater field', 'dynamic-content-for-elementor'));
        }
    }
    public function get_pagination($pages, $count = 0)
    {
        $pages = Helper::str_to_array(',', $pages);
        $ret = array();
        if (!empty($pages)) {
            foreach ($pages as $key => $value) {
                switch ($value) {
                    case 'first':
                        $ret[] = 1;
                        break;
                    case 'last':
                        $ret[] = $count;
                        break;
                    case 'odd':
                        for ($i = 1; $i <= $count; $i++) {
                            if ($i % 2) {
                                $ret[] = $i;
                            }
                        }
                        break;
                    case 'even':
                        for ($i = 1; $i <= $count; $i++) {
                            if (!($i % 2)) {
                                $ret[] = $i;
                            }
                        }
                        break;
                    default:
                        if (\preg_match('/[1-9][n]/', $value)) {
                            for ($i = 1; $i <= $count; $i++) {
                                if (!($i % \intval($value))) {
                                    $ret[] = $i;
                                }
                            }
                        } else {
                            $range = \explode('-', $value, 2);
                            if (\count($range) == 2) {
                                $start = \intval(\reset($range));
                                $end = \intval(\end($range));
                                if ($start <= $end) {
                                    while ($start <= $end) {
                                        $ret[] = $start;
                                        $start++;
                                    }
                                }
                            } else {
                                $ret[] = \intval($value);
                            }
                        }
                }
            }
        }
        return $ret;
    }
    /**
     * Render Accordion Heading
     *
     * @return void
     */
    protected function render_accordion_heading()
    {
        $settings = $this->get_settings_for_display();
        $sub_fields_tokens = Helper::get_acf_repeater_fields($settings['dce_acf_repeater']);
        foreach ($sub_fields_tokens as $key => $value) {
            $text = $settings['dce_acf_repeater_html'];
            $value = get_sub_field($key);
            $sub_fields_tokens_row[get_row_index()][$key] = $value;
        }
        if ('[ROW:id]' === $settings['dce_acf_repeater_accordion_heading']) {
            $title = get_row_index();
        } else {
            $title = Tokens::replace_var_tokens($settings['dce_acf_repeater_accordion_heading'], 'ROW', $sub_fields_tokens_row[get_row_index()]);
        }
        $html_tag = !empty($settings['dce_acf_repeater_accordion_heading_size']) ? Helper::validate_html_tag($settings['dce_acf_repeater_accordion_heading_size']) : 'h4';
        echo \sprintf('<%1$s>', $html_tag);
        $this->render_heading_icon();
        $this->add_render_attribute('accordion_title', ['class' => 'accordion-title']);
        ?>

		<span <?php 
        echo $this->get_render_attribute_string('accordion_title');
        ?>>
			<?php 
        echo $title;
        ?>
		</span>
		<?php 
        echo \sprintf('</%s>', $html_tag);
    }
    /**
     * Render Heading Icon
     *
     * @return void
     */
    protected function render_heading_icon()
    {
        $settings = $this->get_settings_for_display();
        $icon = $settings['selected_icon'];
        $icon_active = $settings['selected_active_icon'];
        $icon_align = $settings['icon_align'] ?? 'left';
        if (!empty($icon)) {
            echo "<span class='icon dce-accordion-icon accordion-icon-{$icon_align}'>";
            Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']);
            echo '</span>';
        }
        if (!empty($icon_active)) {
            echo "<span class='icon-active dce-accordion-icon accordion-icon-{$icon_align}'>";
            Icons_Manager::render_icon($icon_active, ['aria-hidden' => 'true']);
            echo '</span>';
        }
    }
    /**
     * Is Filter Satisfied
     *
     * @param array<string,mixed> $sub_fields
     * @return boolean
     */
    protected function is_filter_satisfied(array $sub_fields)
    {
        $settings = $this->get_settings_for_display();
        foreach ($settings['filters'] as $key => $filter) {
            if (!isset($sub_fields[$filter['filter_field']])) {
                Helper::notice('', \sprintf(__('Filter Error: the subfield %1$s doesn\'t exist', 'dynamic-content-for-elementor'), '<strong>' . $filter['filter_field'] . '</strong>'));
            }
            $field = $sub_fields[$filter['filter_field']] ?? '';
            $condition_satisfied = Helper::is_condition_satisfied($field, $filter['filter_operator'], $filter['filter_value'] ?? '');
            if ('AND' === $settings['filters_relationship'] && !$condition_satisfied) {
                return \false;
            }
            if ('OR' === $settings['filters_relationship'] && $condition_satisfied) {
                return \true;
            }
        }
        return 'AND' === $settings['filters_relationship'];
    }
}
