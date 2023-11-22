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
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class AcfRepeaterOldVersion extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['imagesloaded', 'swiper', 'jquery-masonry', 'dce-wow', 'dce-acf-repeater-old', 'dce-datatables'];
    }
    public function get_style_depends()
    {
        return ['dce-acf-repeater-old', 'datatables'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_dynamictemplate', ['label' => $this->get_title()]);
        $this->add_control('deprecated', ['raw' => __('This widget is deprecated. You can continue to use it but we recommend that you use ACF Repeater widget new version instead, it\'s more fast.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::RAW_HTML, 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
        $repeaters = Helper::get_acf_fields('repeater');
        $this->add_control('dce_acf_repeater', ['label' => __('ACF Repeater', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'label_block' => \true, 'options' => $repeaters]);
        $this->add_control('dce_acf_repeater_mode', ['label' => __('Display mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['repeater' => ['title' => __('Repeater', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list-ul'], 'html' => ['title' => __('HTML & Tokens', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-code'], 'template' => ['title' => __('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'repeater']);
        foreach ($repeaters as $arepeater => $arepeater_title) {
            $arepeater_fields = Helper::get_acf_repeater_fields($arepeater);
            $default = [];
            foreach ($arepeater_fields as $key => $acfitem) {
                $default[] = ['dce_acf_repeater_field_name' => $key, 'dce_acf_repeater_field_type' => $acfitem['type'], 'dce_acf_repeater_field_show' => 'yes', 'dce_views_select_field_label' => $acfitem['title']];
            }
            $repeater_fields = new \Elementor\Repeater();
            $repeater_fields->start_controls_tabs('acfitems_repeater');
            $repeater_fields->start_controls_tab('tab_content', ['label' => __('Item', 'dynamic-content-for-elementor')]);
            $repeater_fields->add_control('dce_acf_repeater_field_name', ['label' => __('Name', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN]);
            $repeater_fields->add_control('dce_acf_repeater_field_type', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN]);
            $repeater_fields->add_control('dce_views_select_field_label', ['label' => __('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
            $repeater_fields->add_control('dce_acf_repeater_field_show', ['label' => __('Show', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
            $repeater_fields->add_control('dce_acf_repeater_field_tag', ['label' => __('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags([], \true), 'default' => 'div']);
            $repeater_fields->add_control('dce_acf_repeater_label_tag', ['label' => __('HTML Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => 'None', 'label' => 'label', 'div' => 'div', 'span' => 'span', 'p' => 'p'], 'default' => 'label', 'condition' => ['dce_views_select_field_label!' => '']]);
            $repeater_fields->end_controls_tab();
            $repeater_fields->start_controls_tab('tab_style', ['label' => __('Style', 'dynamic-content-for-elementor')]);
            $repeater_fields->add_responsive_control('dce_acf_repeater_field_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}}' => 'text-align: {{VALUE}};']]);
            $repeater_fields->add_responsive_control('dce_acf_repeater_field_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}}  {{CURRENT_ITEM}}' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);
            $repeater_fields->add_control('dce_acf_repeater_field_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'after']);
            // ---------- Texts
            $repeater_fields->add_control('dce_acf_repeater_h_texts', ['label' => __('Texts', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'condition' => ['dce_acf_repeater_field_type' => ['text', 'textarea', 'wysiwyg', 'date_picker', 'date_time', 'date_time_picker', 'number', 'select']]]);
            $repeater_fields->add_control('dce_acf_repeater_field_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}};', '{{WRAPPER}} {{CURRENT_ITEM}} a' => 'color: {{VALUE}};'], 'condition' => ['dce_acf_repeater_field_type' => ['text', 'textarea', 'wysiwyg', 'date_picker', 'date_time', 'date_time_picker', 'number', 'select']]]);
            $repeater_fields->add_control('dce_acf_repeater_field_hover_color', ['label' => __('Hover Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} a:hover' => 'color: {{VALUE}};'], 'condition' => ['dce_acf_repeater_field_type' => ['text', 'textarea', 'wysiwyg', 'date_picker', 'date_time', 'date_time_picker', 'number', 'select'], 'dce_acf_repeater_enable_link!' => '']]);
            $repeater_fields->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_acf_repeater_field_typography', 'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}', 'pop_hover' => \true, 'condition' => ['dce_acf_repeater_field_type' => ['text', 'textarea', 'wysiwyg', 'date_picker', 'date_time', 'date_time_picker', 'number', 'select']]]);
            // ---------- Image
            $repeater_fields->add_control('dce_acf_repeater_h_image', ['label' => __('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['dce_acf_repeater_field_type' => ['image']]]);
            $repeater_fields->add_responsive_control('dce_acf_repeater_field_size_image', ['label' => __('Max-Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => '%'], 'size_units' => ['%', 'px'], 'range' => ['%' => ['min' => 1, 'max' => 100, 'step' => 1], 'px' => ['min' => 1, 'max' => 800, 'step' => 1]], 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} img' => 'max-width: {{SIZE}}{{UNIT}};'], 'condition' => ['dce_acf_repeater_field_type' => ['image']]]);
            $repeater_fields->add_group_control(Group_Control_Border::get_type(), ['name' => 'image_border', 'label' => __('Image Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} img', 'condition' => ['dce_acf_repeater_field_type' => ['image']]]);
            $repeater_fields->add_control('image_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['dce_acf_repeater_field_type' => ['image']]]);
            //  ------------------------------------- [SHADOW]
            $repeater_fields->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'image_box_shadow', 'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} img', 'condition' => ['dce_acf_repeater_field_type' => ['image']]]);
            //  ------------------------------------- [FILTERS]
            $repeater_fields->add_group_control(Group_Control_Css_Filter::get_type(), ['name' => 'filters_image', 'label' => __('Filters image', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} img', 'condition' => ['dce_acf_repeater_field_type' => ['image']]]);
            $repeater_fields->end_controls_tab();
            $repeater_fields->start_controls_tab('tab_link', ['label' => __('Link', 'dynamic-content-for-elementor')]);
            $repeater_fields->add_control('dce_acf_repeater_enable_link', ['label' => __('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
            $link_fields = Helper::get_acf_fields(array('url', 'image', 'file', 'link', 'post_object', 'page_link', 'taxonomy', 'user'));
            $repeater_fields->add_control('dce_acf_repeater_acfield_link', ['label' => __('URL Field', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'label_block' => \true, 'groups' => $link_fields, 'default' => 0, 'frontend_available' => \true, 'condition' => ['dce_acf_repeater_enable_link!' => '']]);
            $repeater_fields->add_control('dce_acf_repeater_target_link', ['label' => __('Open in new window', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_acf_repeater_enable_link!' => '', 'dce_acf_repeater_acfield_link!' => '']]);
            $repeater_fields->add_control('dce_acf_repeater_nofollow_link', ['label' => __('Add nofollow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_acf_repeater_enable_link!' => '', 'dce_acf_repeater_acfield_link!' => '']]);
            $repeater_fields->end_controls_tab();
            $repeater_fields->end_controls_tabs();
            $this->add_control('dce_acf_repeater_fields_' . $arepeater, ['label' => __('Repeater fields', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater_fields->get_controls(), 'title_field' => '{{{ dce_views_select_field_label }}} [{{{dce_acf_repeater_field_name}}}] ({{{dce_acf_repeater_field_type}}})', 'default' => $default, 'item_actions' => ['add' => \false, 'duplicate' => \false, 'remove' => \false, 'sort' => \true], 'condition' => ['dce_acf_repeater' => $arepeater, 'dce_acf_repeater_mode' => 'repeater']]);
        }
        $this->add_control('dce_acf_repeater_html', ['label' => __('Custom HTML', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'default' => '[ROW]', 'description' => __('Type here some content, you can use HTML and TOKENS.', 'dynamic-content-for-elementor'), 'condition' => ['dce_acf_repeater_mode' => 'html']]);
        $this->add_control('dce_acf_repeater_template', ['label' => __('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select Template', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'description' => __('Use an Elementor Template as content of the repeater', 'dynamic-content-for-elementor'), 'condition' => ['dce_acf_repeater_mode' => 'template']]);
        $this->add_control('dce_acf_repeater_pagination', ['label' => __('Show row item', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'first, last, 1, 3-4', 'description' => __('Leave empty to print all rows, otherwise write the number of them. Use "first" and "last" to indicate the first and last element.', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_acf_repeater_inverted', ['label' => __('Invert row order', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->end_controls_section();
        $this->start_controls_section('dce_acf_repeater_options', ['label' => __('Options', 'dynamic-content-for-elementor')]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'imgsize', 'label' => __('Image Size', 'dynamic-content-for-elementor'), 'default' => 'large', 'render_type' => 'template', 'condition' => ['dce_acf_repeater_mode' => 'repeater']]);
        $this->add_responsive_control('alignment', ['label' => __('Global Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'separator' => 'before', 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};'], 'condition' => ['dce_acf_repeater_mode!' => 'template']]);
        $this->add_responsive_control('dce_acf_repeater_field_col', ['label' => __('Col space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .dce-acf-repeater-item' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('dce_acf_repeater_field_row', ['label' => __('Row space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .dce-acf-repeater-item' => 'padding-bottom: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        $this->start_controls_section('dce_acf_repeater_h_render', ['label' => __('Render', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_acf_repeater_format', ['label' => __('Render as', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'frontend_available' => \true, 'options' => ['' => __('Natural', 'dynamic-content-for-elementor'), 'grid' => __('Grid', 'dynamic-content-for-elementor'), 'masonry' => __('Masonry', 'dynamic-content-for-elementor'), 'slider_carousel' => __('Slider/Carousel', 'dynamic-content-for-elementor'), 'table' => __('Table (No Template)', 'dynamic-content-for-elementor'), 'list' => __('List', 'dynamic-content-for-elementor'), 'accordion' => __('Accordion', 'dynamic-content-for-elementor')], 'default' => 'grid']);
        // -------------------------------- SHOW LABEL ON GRID
        $this->add_control('dce_acf_repeater_grid_label', ['label' => __('Show label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_acf_repeater_format' => 'grid']]);
        $this->add_control('dce_acf_repeater_separator', ['label' => __('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => ', ', 'condition' => ['dce_acf_repeater_format' => '']]);
        // ---------------------------------- ACCORDION
        $this->add_control('dce_acf_repeater_accordion_heading', ['label' => __('Heading text', 'dynamic-content-for-elementor'), 'default' => '[ROW:id]', 'placeholder' => '[ROW:id]', 'description' => __('Write the [ROW:sub_field_name] here, this text will be used as Accordion Heading', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('selected_icon', ['label' => __('Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'separator' => 'before', 'fa4compatibility' => 'icon', 'default' => ['value' => 'fas fa-plus', 'library' => 'fa-solid'], 'skin' => 'inline', 'label_block' => \false, 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('selected_active_icon', ['label' => __('Active Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'fa4compatibility' => 'icon_active', 'default' => ['value' => 'fas fa-minus', 'library' => 'fa-solid'], 'skin' => 'inline', 'label_block' => \false, 'condition' => ['dce_acf_repeater_format' => 'accordion', 'selected_icon[value]!' => '']]);
        $this->add_control('dce_acf_repeater_accordion_heading_size', ['label' => __('Heading HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(), 'default' => 'h4', 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('dce_acf_repeater_accordion_start', ['label' => __('Initially open', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['none' => ['title' => __('None', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban'], 'first' => ['title' => __('First', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-right'], 'all' => ['title' => __('All', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-double-right']], 'inline' => \true, 'toggle' => \false, 'default' => 'none', 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('dce_acf_repeater_accordion_close', ['label' => __('Automatically close other Tabs', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        // ---------------------------------- LIST
        $this->add_control('dce_acf_repeater_list', ['label' => __('List type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['ul' => ['title' => __('Unordered List', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list-ul'], 'ol' => ['title' => __('Ordered List', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list-ol']], 'toggle' => \false, 'default' => 'ul', 'condition' => ['dce_acf_repeater_format' => 'list']]);
        $this->add_control('selected_icon_ul', ['label' => __('Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'separator' => 'before', 'fa4compatibility' => 'icon', 'skin' => 'inline', 'label_block' => \false, 'condition' => ['dce_acf_repeater_format' => 'list', 'dce_acf_repeater_list' => 'ul'], 'render_type' => 'template', 'selectors' => ['{{WRAPPER}} ul > li > .elementor-icon' => 'float: left; clear: both; font-size: inherit;']]);
        $this->add_control('dce_acf_repeater_list_flex', ['label' => __('Flex list', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_acf_repeater_format' => 'list', 'dce_acf_repeater_list' => 'ul', 'selected_icon_ul[value]!' => ''], 'selectors' => ['{{WRAPPER}} ul > li' => 'display: flex;']]);
        $this->add_responsive_control('dce_acf_repeater_col', ['label' => __('Columns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '5', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7'], 'render_type' => 'template', 'selectors' => ['{{WRAPPER}} .dce-acf-repeater-grid' => 'display: flex; flex-wrap: wrap;', '{{WRAPPER}} .dce-acf-repeater-masonry .dce-acf-repeater-item' => 'width: calc( 100% / {{VALUE}} );', '{{WRAPPER}} .dce-acf-repeater-grid .dce-acf-repeater-item' => 'flex: 0 1 calc( 100% / {{VALUE}} );'], 'condition' => ['dce_acf_repeater_format' => ['grid', 'masonry']]]);
        $this->add_control('flex_grow', ['label' => __('Flex grow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'label_block' => \false, 'options' => ['1' => ['title' => __('1', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => __('0', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => 1, 'selectors' => ['{{WRAPPER}} .dce-post-item' => 'flex-grow: {{VALUE}};'], 'condition' => ['dce_acf_repeater_format' => 'grid']]);
        $this->add_responsive_control('flexgrid_mode', ['label' => __('Alignment grid', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'flex-start', 'tablet_default' => '3', 'mobile_default' => '1', 'label_block' => \true, 'options' => ['flex-start' => 'Flex start', 'flex-end' => 'Flex end', 'center' => 'Center', 'space-between' => 'Space Between', 'space-around' => 'Space Around'], 'selectors' => ['{{WRAPPER}} .dce-acf-repeater-grid' => 'justify-content: {{VALUE}};'], 'condition' => ['dce_acf_repeater_format' => 'grid', 'flex_grow' => '0']]);
        $this->add_responsive_control('v_align_items', ['label' => __('Vertical Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex-start' => ['title' => __('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], 'center' => ['title' => __('Middle', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'flex-end' => ['title' => __('Down', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom'], 'stretch' => ['title' => __('Stretch', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-stretch']], 'default' => 'top', 'selectors' => ['{{WRAPPER}} .dce-acf-repeater-item' => 'align-self: {{VALUE}};'], 'condition' => ['dce_acf_repeater_format' => 'grid', 'flex_grow' => '0']]);
        // -------------------------------- TABLE
        $this->add_control('dce_acf_repeater_thead', ['label' => __('Table Head', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_acf_repeater_format' => 'table']]);
        $this->add_control('dce_acf_repeater_datatables', ['label' => __('Add DataTable', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_acf_repeater_format' => 'table']]);
        $this->end_controls_section();
        $this->start_controls_section('section_slidercarousel_mode', ['label' => __('Slider Carousel', 'dynamic-content-for-elementor'), 'condition' => ['dce_acf_repeater_format' => 'slider_carousel']]);
        // -------------------------------- Progressione ------
        // da valutare ....
        $this->add_control('effects', ['label' => __('Effect of transition', 'dynamic-content-for-elementor'), 'description' => __('Tranisition effect from the slides.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['slide' => __('Slide', 'dynamic-content-for-elementor'), 'fade' => __('Fade', 'dynamic-content-for-elementor'), 'cube' => __('Cube', 'dynamic-content-for-elementor'), 'coverflow' => __('Coverflow', 'dynamic-content-for-elementor'), 'flip' => __('Flip', 'dynamic-content-for-elementor')], 'default' => 'slide', 'frontend_available' => \true]);
        $this->add_responsive_control('slidesPerView', ['label' => __('Slides Per View', 'dynamic-content-for-elementor'), 'description' => __('Number of slides per view (slides visible at the same time on slider\'s container). If you use it with "auto" value and along with loop: true then you need to specify loopedSlides parameter with amount of slides to loop (duplicate). SlidesPerView: \'auto\' is currently not compatible with multirow mode, when slidesPerColumn > 1', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '1', 'min' => 1, 'max' => 12, 'step' => 1, 'frontend_available' => \true]);
        $this->add_responsive_control('slidesColumn', ['label' => __('Slides Column', 'dynamic-content-for-elementor'), 'description' => __('Number of slides per column, for multirow layout.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '1', 'min' => 1, 'max' => 4, 'step' => 1, 'frontend_available' => \true]);
        $this->add_responsive_control('slidesPerGroup', ['label' => __('Slides Per Group', 'dynamic-content-for-elementor'), 'description' => __('Set numbers of slides to define and enable group sliding. Useful to use with slidesPerView > 1', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'tablet_default' => '', 'mobile_default' => '', 'min' => 1, 'max' => 12, 'step' => 1, 'frontend_available' => \true]);
        $this->add_control('direction_slider', ['label' => __('Direction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'options' => ['horizontal' => __('Horizontal', 'dynamic-content-for-elementor'), 'vertical' => __('Vertical', 'dynamic-content-for-elementor')], 'default' => 'horizontal', 'frontend_available' => \true]);
        $this->add_control('speed_slider', ['label' => __('Speed', 'dynamic-content-for-elementor'), 'description' => __('Duration of transition between slides (in ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 300, 'min' => 0, 'max' => 3000, 'step' => 10, 'frontend_available' => \true]);
        $this->add_responsive_control('spaceBetween', ['label' => __('Space Between', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'tablet_default' => '', 'mobile_default' => '', 'min' => 0, 'max' => 100, 'step' => 1, 'frontend_available' => \true]);
        $this->add_control('centeredSlides', ['label' => __('Centered Slides', 'dynamic-content-for-elementor'), 'description' => __('If true, then active slide will be centered, not always on the left side.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        // -------------------------------- Free Mode ------
        $this->add_control('freemode_options', ['label' => __('Free Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('freeMode', ['label' => __('Free Mode', 'dynamic-content-for-elementor'), 'description' => __('If true then slides will not have fixed positions', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('freeModeMomentum', ['label' => __('Free Mode Momentum', 'dynamic-content-for-elementor'), 'description' => __('If true, then slide will keep moving for a while after you release it', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->add_control('freeModeMomentumRatio', ['label' => __('Free Mode Momentum Ratio', 'dynamic-content-for-elementor'), 'description' => __('Higher value produces larger momentum distance after you release slider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentum' => 'yes']]);
        $this->add_control('freeModeMomentumVelocityRatio', ['label' => __('Free Mode Momentum Velocity Ratio', 'dynamic-content-for-elementor'), 'description' => __('Higher value produces larger momentum velocity after you release slider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentum' => 'yes']]);
        $this->add_control('freeModeMomentumBounce', ['label' => __('Free Mode Momentum Bounce', 'dynamic-content-for-elementor'), 'description' => __('Set to false if you want to disable momentum bounce in free mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->add_control('freeModeMomentumBounceRatio', ['label' => __('Free Mode Momentum Bounce Ratio', 'dynamic-content-for-elementor'), 'description' => __('Higher value produces larger momentum bounce effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentumBounce' => 'yes']]);
        $this->add_control('freeModeMinimumVelocity', ['label' => __('Free Mode Momentum Velocity Ratio', 'dynamic-content-for-elementor'), 'description' => __('Minimum touchmove-velocity required to trigger free mode momentum', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0.02, 'min' => 0, 'max' => 1, 'step' => 0.01, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->add_control('freeModeSticky', ['label' => __('Free Mode Sticky', 'dynamic-content-for-elementor'), 'description' => __('Set \'yes\' to enable snap to slides positions in free mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        // -------------------------------- Navigation options ------
        $this->add_control('navigation_options', ['label' => __('Navigation options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('useNavigation', ['label' => __('Use Navigation', 'dynamic-content-for-elementor'), 'description' => __('Set "yes", you will use the navigation arrows.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        // ------------------------------------------------- Navigations Arrow Options
        $this->add_control('navigation_arrow_color', ['label' => __('Arrows color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-button-next path, {{WRAPPER}} .swiper-button-prev path, ' => 'fill: {{VALUE}};', '{{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev line, {{WRAPPER}} .swiper-button-next polyline, {{WRAPPER}} .swiper-button-prev polyline' => 'stroke: {{VALUE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('useNavigation_animationHover', ['label' => __('Use animation in rollover', 'dynamic-content-for-elementor'), 'description' => __('A short animation will take place at the rollover.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'prefix_class' => 'hoveranim-', 'separator' => 'before', 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('navigation_arrow_color_hover', ['label' => __('Hover color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-button-next:hover path, {{WRAPPER}} .swiper-button-prev:hover path, ' => 'fill: {{VALUE}};', '{{WRAPPER}} .swiper-button-next:hover line, {{WRAPPER}} .swiper-button-prev:hover line, {{WRAPPER}} .swiper-button-next:hover polyline, {{WRAPPER}} .swiper-button-prev:hover polyline' => 'stroke: {{VALUE}};'], 'condition' => ['useNavigation' => 'yes'], 'separator' => 'after']);
        $this->add_responsive_control('pagination_stroke_1', ['label' => __('Stroke Arrow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev polyline, {{WRAPPER}} .swiper-button-next polyline' => 'stroke-width: {{SIZE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('pagination_stroke_2', ['label' => __('Stroke Line', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev line' => 'stroke-width: {{SIZE}};'], 'condition' => ['useNavigation' => 'yes']]);
        ////////
        $this->add_control('pagination_tratteggio', ['label' => __('Dashed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0'], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev line, {{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev polyline, {{WRAPPER}} .swiper-button-next polyline' => 'stroke-dasharray: {{SIZE}},{{SIZE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('pagination_scale', ['label' => __('Scale', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 2, 'min' => 0.1, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'transform: scale({{SIZE}});'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('pagination_position', ['label' => __('Horizontal Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'size_units' => ['px'], 'range' => ['px' => ['max' => 100, 'min' => -100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .swiper-button-next' => 'right: {{SIZE}}{{UNIT}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('pagination_position_v', ['label' => __('Vertical Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 50, 'unit' => '%'], 'size_units' => ['%', 'px'], 'range' => ['%' => ['max' => 120, 'min' => -20, 'step' => 1], 'px' => ['max' => 200, 'min' => -200, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'top: {{SIZE}}{{UNIT}};'], 'condition' => ['useNavigation' => 'yes']]);
        // --------------------------------------------------- Pagination options ------
        $this->add_control('pagination_options', ['label' => __('Pagination options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('usePagination', ['label' => __('Use Pagination', 'dynamic-content-for-elementor'), 'description' => __('Use the slide progression display system ("bullets", "fraction", "progress").', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('pagination_type', ['label' => __('Pagination Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['bullets' => __('Bullets', 'dynamic-content-for-elementor'), 'fraction' => __('Fraction', 'dynamic-content-for-elementor'), 'progress' => __('Progress', 'dynamic-content-for-elementor')], 'default' => 'bullets', 'frontend_available' => \true, 'condition' => ['usePagination' => 'yes']]);
        // ------------------------------------------------- Pagination Fraction Options
        $this->add_control('fraction_separator', ['label' => __('Fraction text separator', 'dynamic-content-for-elementor'), 'description' => __('The text separating the 2 numbers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'frontend_available' => \true, 'default' => '/', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_responsive_control('fraction_space', ['label' => __('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '4', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => -20, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .separator' => 'margin: 0 {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_control('fraction_color', ['label' => __('Numbers color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction > *' => 'color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fraction_typography', 'label' => __('Typography numbers', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-fraction > *', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_control('fraction_current_color', ['label' => __('Color of current number', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current' => 'color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fraction_typography_current', 'label' => __('Current number typography', 'dynamic-content-for-elementor'), 'default' => '', 'selector' => '{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_control('fraction_separator_color', ['label' => __('The color of the separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .separator' => 'color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => __('fraction_typography_separator', 'dynamic-content-for-elementor'), 'label' => 'Typography separator', 'default' => '', 'selector' => '{{WRAPPER}} .swiper-pagination-fraction .separator', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        // ------------------------------------------------- Pagination Bullets Options
        $this->add_responsive_control('bullets_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '5', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_responsive_control('pagination_bullets', ['label' => __('Bullets dimension', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '8', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_responsive_control('pagination_posy', ['label' => __('Shift', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '10', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => -160, 'max' => 160]], 'selectors' => ['{{WRAPPER}} .swiper-pagination' => ' bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => ['bullets', 'fraction']]]);
        $this->add_responsive_control('pagination_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '10', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 60]], 'selectors' => ['{{WRAPPER}} .swiper-pagination' => 'padding-right: {{SIZE}}{{UNIT}}; padding-left: {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => ['bullets', 'fraction']]]);
        $this->add_control('bullets_color', ['label' => __('Bullets Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'background-color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_bullet', 'label' => __('Dullets border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_responsive_control('current_bullet', ['label' => __('Dimension of active bullet', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .swiper-pagination.swiper-pagination-bullets' => 'height: {{SIZE}}{{UNIT}}'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_control('current_bullet_color', ['label' => __('Active bullet color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_current_bullet', 'label' => __('Active bullet border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_control('progress_color', ['label' => __('Progress color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-progress' => 'background-color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'progress']]);
        $this->add_control('progressbar_color', ['label' => __('Progressbar color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-progress .swiper-pagination-progressbar' => 'background-color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'progress']]);
        $this->add_responsive_control('h_align_pagination', ['label' => __('Horizontal Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'selectors' => ['{{WRAPPER}} .swiper-pagination' => 'text-align: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => ['bullets', 'fraction']]]);
        $this->add_control('autoplay_options', ['label' => __('Autoplay options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('useAutoplay', ['label' => __('Use Autoplay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('autoplay', ['label' => __('Autoplay Delay (ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 3000, 'min' => 0, 'max' => 7000, 'step' => 100, 'frontend_available' => \true, 'condition' => ['useAutoplay' => 'yes']]);
        $this->add_control('autoplayStopOnHover', ['label' => __('Autoplay stop on hover', 'dynamic-content-for-elementor'), 'description' => __('Enable this parameter and autoplay will be stopped on hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['useAutoplay' => 'yes']]);
        $this->add_control('autoplayStopOnLast', ['label' => __('Autoplay stop on last slide', 'dynamic-content-for-elementor'), 'description' => __('Enable this parameter and autoplay will be stopped when it reaches last slide (has no effect in loop mode)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['autoplay!' => '']]);
        $this->add_control('autoplayDisableOnInteraction', ['label' => __('Autoplay Disable on interaction', 'dynamic-content-for-elementor'), 'description' => __('Set to "false" and autoplay will not be disabled after user interactions (swipes), it will be restarted every time after interaction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['autoplay!' => '']]);
        $this->add_control('keyboard_options', ['label' => __('Keyboard / Mousewheel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('keyboardControl', ['label' => __('Keyboard Control', 'dynamic-content-for-elementor'), 'description' => __('Set to true to enable keyboard control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('mousewheelControl', ['label' => __('Mousewheel Control', 'dynamic-content-for-elementor'), 'description' => __('Enables navigation through slides using mouse wheel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('cicleloop_options', ['label' => __('Cicle / Loop', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('loop', ['label' => __('Loop', 'dynamic-content-for-elementor'), 'description' => __('Set to true to enable continuous loop mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('special_options', ['label' => __('Specials options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('autoHeight', ['label' => __('Auto Height', 'dynamic-content-for-elementor'), 'description' => __('Set to true and slider wrapper will adopt its height to the height of the currently active slide', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('grabCursor', ['label' => __('Grab Cursor', 'dynamic-content-for-elementor'), 'description' => __('This option may a little improve desktop usability. If true, user will see the "grab" cursor when hover on Swiper', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('data_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'description' => __('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => __('Same', 'dynamic-content-for-elementor'), 'label_off' => __('other', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_control('other_post_source', ['label' => __('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_title_style', ['label' => __('Accordion', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('border_width', ['label' => __('Border Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 10]], 'selectors' => ['{{WRAPPER}} .elementor-accordion .elementor-accordion-item' => 'border-width: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-accordion .elementor-accordion-item .elementor-tab-content' => 'border-width: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-accordion .elementor-accordion-item .elementor-tab-title.elementor-active' => 'border-width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('border_color', ['label' => __('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-accordion .elementor-accordion-item' => 'border-color: {{VALUE}};', '{{WRAPPER}} .elementor-accordion .elementor-accordion-item .elementor-tab-content' => 'border-top-color: {{VALUE}};', '{{WRAPPER}} .elementor-accordion .elementor-accordion-item .elementor-tab-title.elementor-active' => 'border-bottom-color: {{VALUE}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_toggle_style_title', ['label' => __('Title', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('title_background', ['label' => __('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-accordion .elementor-tab-title' => 'background-color: {{VALUE}};']]);
        $this->add_control('title_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-accordion-icon, {{WRAPPER}} .elementor-accordion-title' => 'color: {{VALUE}};']]);
        $this->add_control('tab_active_color', ['label' => __('Active Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-active .elementor-accordion-icon, {{WRAPPER}} .elementor-active .elementor-accordion-title' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'title_typography', 'selector' => '{{WRAPPER}} .elementor-accordion .elementor-accordion-title']);
        $this->add_responsive_control('title_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .elementor-accordion .elementor-tab-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_toggle_style_icon', ['label' => __('Icon', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_acf_repeater_format' => ['accordion', 'list']]]);
        $this->add_control('icon_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Start', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'right' => ['title' => __('End', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => is_rtl() ? 'right' : 'left', 'toggle' => \false, 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('icon_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-icon i:before' => 'color: {{VALUE}};', '{{WRAPPER}} .elementor-icon svg' => 'fill: {{VALUE}};', '{{WRAPPER}} .elementor-accordion .elementor-tab-title .elementor-accordion-icon i:before' => 'color: {{VALUE}};', '{{WRAPPER}} .elementor-accordion .elementor-tab-title .elementor-accordion-icon svg' => 'fill: {{VALUE}};']]);
        $this->add_control('icon_active_color', ['label' => __('Active Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-accordion .elementor-tab-title.elementor-active .elementor-accordion-icon i:before' => 'color: {{VALUE}};', '{{WRAPPER}} .elementor-accordion .elementor-tab-title.elementor-active .elementor-accordion-icon svg' => 'fill: {{VALUE}};'], 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_responsive_control('icon_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .elementor-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['dce_acf_repeater_format' => 'list']]);
        $this->add_responsive_control('icon_space', ['label' => __('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .elementor-accordion .elementor-accordion-icon.elementor-accordion-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-accordion .elementor-accordion-icon.elementor-accordion-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};'], 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'icon_typography', 'selector' => '{{WRAPPER}} .elementor-accordion .elementor-accordion-icon, {{WRAPPER}} .elementor-icon i']);
        $this->end_controls_section();
        $this->start_controls_section('section_toggle_style_content', ['label' => __('Content', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('content_background_color', ['label' => __('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-accordion .elementor-tab-content' => 'background-color: {{VALUE}};']]);
        $this->add_control('content_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-accordion .elementor-tab-content' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'content_typography', 'selector' => '{{WRAPPER}} .elementor-accordion .elementor-tab-content']);
        $this->add_responsive_control('content_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .elementor-accordion .elementor-tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('content_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .elementor-accordion .elementor-tab-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'content_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .elementor-accordion .elementor-tab-content']);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id($settings['other_post_source']);
        global $repeater_counter;
        if (empty($repeater_counter)) {
            $repeater_counter = 1;
        } else {
            $repeater_counter++;
        }
        if ($settings['dce_acf_repeater']) {
            $values = array();
            $locations = Helper::get_acf_field_locations($settings['dce_acf_repeater']);
            if (\in_array('options', $locations)) {
                $id_page = 'options';
            }
            if (\in_array('taxonomy', $locations)) {
                $queried_object = get_queried_object();
                if (!empty($queried_object) && \is_object($queried_object) && \get_class($queried_object) == 'WP_Term') {
                    $taxonomy = $queried_object->taxonomy;
                    $term_id = $queried_object->term_id;
                    $id_page = $taxonomy . '_' . $term_id;
                }
            }
            if (\in_array('user', $locations)) {
                $user_id = get_current_user_id();
                $id_page = 'user_' . $user_id;
            }
            $rows = \get_field($settings['dce_acf_repeater'], $id_page);
            if (have_rows($settings['dce_acf_repeater'], $id_page)) {
                echo '<div class="dce-acf-repater-' . ($settings['dce_acf_repeater_format'] == 'table' && !empty($settings['dce_acf_repeater_datatables']) ? 'data' : '') . $settings['dce_acf_repeater_format'] . '">';
                $row_id = 0;
                $sub_fields = Helper::get_acf_repeater_fields($settings['dce_acf_repeater']);
                if (!empty($sub_fields)) {
                    while (have_rows($settings['dce_acf_repeater'], $id_page)) {
                        the_row();
                        $row_id++;
                        if ($settings['dce_acf_repeater_mode'] == 'template') {
                            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                                $inlinecss = 'inlinecss="true"';
                            } else {
                                $inlinecss = '';
                            }
                            $idtemplate = $settings['dce_acf_repeater_template'];
                            $values[$row_id]['template'] = do_shortcode('[dce-elementor-template id="' . $idtemplate . '" ' . $inlinecss . ']');
                        }
                        foreach ($sub_fields as $key => $acfitem) {
                            $value = get_sub_field($key);
                            $values[$row_id][$key] = $value;
                        }
                        $values[$row_id]['id'] = $row_id;
                    }
                    if (!empty($rows) && $row_id < \count($rows)) {
                        foreach ($rows as $row_id => $arow) {
                            foreach ($sub_fields as $key => $acfitem) {
                                $value = $arow[$key];
                                $values[$row_id][$key] = $value;
                            }
                            $values[$row_id]['id'] = $row_id;
                        }
                    }
                }
                if ($settings['dce_acf_repeater_inverted']) {
                    $values = \array_reverse($values);
                }
                $repeater_count = $row_id;
                $swiper_class = \Elementor\Plugin::$instance->experiments->is_feature_active('e_swiper_latest') ? 'swiper' : 'swiper-container';
                $class = '  class="dce-acf-repeater dce-acf-repeater-' . $settings['dce_acf_repeater_format'] . ($settings['dce_acf_repeater_format'] == 'slider_carousel' ? ' ' . $swiper_class . ' swiper-container-' . $settings['direction_slider'] : '') . ($settings['dce_acf_repeater_format'] == 'list' && $settings['dce_acf_repeater_list'] == 'ul' && !empty($settings['selected_icon_ul']['value']) ? ' dce-no-list' : '') . ($settings['dce_acf_repeater_format'] == 'table' && !empty($settings['dce_acf_repeater_datatables']) ? ' dce-datatable' : '') . '"';
                switch ($settings['dce_acf_repeater_format']) {
                    case 'list':
                        echo '<' . $settings['dce_acf_repeater_list'] . $class . '>';
                        break;
                    case 'grid':
                    case 'masonry':
                        echo '<div' . $class . '>';
                        break;
                    case 'slider_carousel':
                        echo '<div' . $class . '><div class="swiper-wrapper">';
                        break;
                    case 'table':
                        echo '<table' . $class . '>';
                        echo '<thead>';
                        if ($settings['dce_acf_repeater_thead']) {
                            if ($settings['dce_acf_repeater_mode'] == 'repeater') {
                                $fields = $settings['dce_acf_repeater_fields_' . $settings['dce_acf_repeater']];
                                if (!empty($fields)) {
                                    foreach ($fields as $key => $acfitem) {
                                        if (!$acfitem['dce_acf_repeater_field_show']) {
                                            continue;
                                        }
                                        echo '<th>';
                                        if ($acfitem['dce_acf_repeater_label_tag']) {
                                            echo '<' . $acfitem['dce_acf_repeater_label_tag'] . '>';
                                        }
                                        echo $acfitem['dce_views_select_field_label'];
                                        if ($acfitem['dce_acf_repeater_label_tag']) {
                                            echo '</' . $acfitem['dce_acf_repeater_label_tag'] . '>';
                                        }
                                        echo '</th>';
                                    }
                                }
                            } else {
                                if (!empty($sub_fields)) {
                                    foreach ($sub_fields as $key => $acfitem) {
                                        echo '<th>' . $acfitem['title'] . '</th>';
                                    }
                                }
                            }
                        }
                        echo '</thead>';
                        break;
                    case 'accordion':
                        echo '<div class="elementor-widget-accordion"><div class="elementor-accordion" role="tablist">';
                        break;
                }
                $paginations = $this->get_pagination($settings['dce_acf_repeater_pagination'], $repeater_count);
                foreach ($values as $row_id => $row_fields) {
                    if (empty($paginations) || \in_array($row_id, $paginations)) {
                        switch ($settings['dce_acf_repeater_format']) {
                            case 'list':
                                echo '<li class="dce-acf-repeater-item">';
                                if ($settings['dce_acf_repeater_list'] == 'ul') {
                                    echo '<span class="elementor-icon">' . Helper::get_icon($settings['selected_icon_ul']) . '</span>';
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
                                $element_active = '';
                                switch ($settings['dce_acf_repeater_accordion_start']) {
                                    case 'first':
                                        if ($row_id == 1) {
                                            $element_active = ' elementor-active';
                                        }
                                        break;
                                    case 'all':
                                        $element_active = ' elementor-active';
                                        break;
                                }
                                echo '<div class="dce-acf-repeater-item elementor-accordion-item"><' . $settings['dce_acf_repeater_accordion_heading_size'] . ' class="elementor-tab-title' . $element_active . '" data-tab="' . $this->get_id() . '-' . $row_id . '" role="tab" aria-controls="elementor-tab-content-' . $this->get_id() . '-' . $row_id . '"><span class="elementor-accordion-icon elementor-accordion-icon-' . esc_attr($settings['icon_align']) . '" aria-hidden="true"><span class="elementor-accordion-icon-closed">' . Helper::get_icon($settings['selected_icon']) . '</span><span class="elementor-accordion-icon-opened">' . Helper::get_icon($settings['selected_active_icon']) . '</span></span><a class="elementor-accordion-title" href="#" onclick="return false;"> ';
                                echo \DynamicContentForElementor\Tokens::replace_var_tokens($settings['dce_acf_repeater_accordion_heading'], 'ROW', $row_fields);
                                echo '</a></' . $settings['dce_acf_repeater_accordion_heading_size'] . '>';
                                echo '<div id="elementor-tab-content-' . $this->get_id() . '-' . $row_id . '" class="elementor-tab-content elementor-clearfix' . $element_active . '" data-tab="' . $this->get_id() . '-' . $row_id . '" role="tabpanel" aria-labelledby="elementor-tab-title-' . $this->get_id() . '-' . $row_id . '"' . ($element_active ? ' style="display:block;"' : '') . '>';
                                break;
                        }
                        switch ($settings['dce_acf_repeater_mode']) {
                            case 'repeater':
                                $fields = $settings['dce_acf_repeater_fields_' . $settings['dce_acf_repeater']];
                                if (!empty($fields)) {
                                    foreach ($fields as $key => $acfitem) {
                                        if ($acfitem['dce_acf_repeater_field_show']) {
                                            $value = $row_fields[$acfitem['dce_acf_repeater_field_name']];
                                            if (!empty($value)) {
                                                $field_settings = Helper::get_acf_field_settings($acfitem['dce_acf_repeater_field_name']);
                                                $field_type = $field_settings['type'];
                                                //i base al tipo elaboro il dato per generare il giusto tag
                                                switch ($field_type) {
                                                    case 'wysiwyg':
                                                        $value = wpautop($value);
                                                        break;
                                                    case 'image':
                                                        $imageAlt = '';
                                                        if (\is_string($value)) {
                                                            $imageSrc = $value;
                                                        } elseif (\is_numeric($value)) {
                                                            $imageSrc = Group_Control_Image_Size::get_attachment_image_src($value, 'imgsize', $settings);
                                                            $imageAlt = get_post_meta($value, '_wp_attachment_image_alt', \true);
                                                        } elseif (\is_array($value)) {
                                                            $imageSrc = Group_Control_Image_Size::get_attachment_image_src($value['ID'], 'imgsize', $settings);
                                                            $imageAlt = $value['alt'];
                                                        }
                                                        $value = '<img src="' . $imageSrc . '" alt="' . $imageAlt . '" />';
                                                        break;
                                                    case 'date_time_picker':
                                                    case 'date_picker':
                                                    case 'url':
                                                    case 'text':
                                                    case 'textarea':
                                                    default:
                                                        $value = Helper::to_string($value, \true);
                                                }
                                            }
                                            if (!empty($row_fields[$acfitem['dce_acf_repeater_acfield_link']]) && !empty($acfitem['dce_acf_repeater_enable_link'])) {
                                                $targetLink = '';
                                                if (!empty($acfitem['dce_acf_repeater_target_link'])) {
                                                    $targetLink .= ' target="_blank"';
                                                }
                                                if (!empty($acfitem['dce_acf_repeater_nofollow_link'])) {
                                                    $targetLink .= ' rel="nofollow"';
                                                }
                                                $link = $row_fields[$acfitem['dce_acf_repeater_acfield_link']];
                                                $link = Helper::get_permalink($link, '#');
                                                $value = '<a href="' . $link . '"' . $targetLink . '>' . $value . '</a>';
                                            }
                                            switch ($settings['dce_acf_repeater_format']) {
                                                case 'grid':
                                                    if ($settings['dce_acf_repeater_grid_label'] && $acfitem['dce_views_select_field_label'] && $value) {
                                                        $label = '';
                                                        if ($acfitem['dce_acf_repeater_label_tag']) {
                                                            $label .= '<' . $acfitem['dce_acf_repeater_label_tag'] . '>';
                                                        }
                                                        $label .= $acfitem['dce_views_select_field_label'];
                                                        if ($acfitem['dce_acf_repeater_label_tag']) {
                                                            $label .= '</' . $acfitem['dce_acf_repeater_label_tag'] . '>';
                                                        }
                                                        $value = $label . $value;
                                                    }
                                                    break;
                                            }
                                            $value = Helper::to_string($value);
                                            if ($acfitem['dce_acf_repeater_field_tag']) {
                                                $value = '<' . \DynamicContentForElementor\Helper::validate_html_tag($acfitem['dce_acf_repeater_field_tag']) . ' class="repeater-item elementor-repeater-item-' . $acfitem['_id'] . '">' . $value . '</' . \DynamicContentForElementor\Helper::validate_html_tag($acfitem['dce_acf_repeater_field_tag']) . '>';
                                            }
                                            switch ($settings['dce_acf_repeater_format']) {
                                                case 'table':
                                                    echo '<td>';
                                                    break;
                                            }
                                            echo $value;
                                            switch ($settings['dce_acf_repeater_format']) {
                                                case 'table':
                                                    echo '</td>';
                                            }
                                        }
                                    }
                                }
                                break;
                            case 'html':
                                $text = $settings['dce_acf_repeater_html'];
                                echo \DynamicContentForElementor\Tokens::replace_var_tokens($text, 'ROW', $row_fields);
                                break;
                            case 'template':
                                echo $row_fields['template'];
                                break;
                        }
                        switch ($settings['dce_acf_repeater_format']) {
                            case '':
                                if ($row_id < $repeater_count) {
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
                                echo '</div></div>';
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
                    case 'slider_carousel':
                        echo '</div></div>';
                        break;
                    case 'table':
                        echo '</table>';
                        if ($settings['dce_acf_repeater_datatables']) {
                            wp_enqueue_style('datatables');
                            wp_enqueue_script('dce-datatables');
                            ?>
							<script type="text/javascript">
								jQuery(function () {
									jQuery('.elementor-element-<?php 
                            echo $this->get_id();
                            ?> table.dce-datatable').DataTable();
								});
							</script>
							<?php 
                        }
                }
                if ($settings['dce_acf_repeater_format'] == 'slider_carousel') {
                    // NOTA: la paginazione e la navigazione per lo swiper è fuori dal suo contenitore per poter spostare gli elementi a mio piacimento, visto che il contenitore è in overflow: hidden, e se fossero all'interno (come di default) si nasconderebbero fuori dall'area.
                    if ($settings['usePagination']) {
                        // Add Pagination
                        echo '<div class="swiper-container-' . $settings['direction_slider'] . '"><div class="swiper-pagination pagination-' . $this->get_id() . '"></div></div>';
                    }
                    if ($settings['useNavigation']) {
                        // Add Arrows
                        echo '<div class="swiper-button-prev prev-' . $this->get_id() . ' prev-' . $repeater_counter . '"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                        width="85.039px" height="85.039px" viewBox="378.426 255.12 85.039 85.039" enable-background="new 378.426 255.12 85.039 85.039"
                        xml:space="preserve">
                        <line fill="none" stroke="#000000" stroke-width="1.3845" stroke-dasharray="0,0" stroke-miterlimit="10" x1="382.456" y1="298.077" x2="458.375" y2="298.077"/>
                        <polyline fill="none" stroke="#000000" stroke-width="1.3845" stroke-dasharray="0,0" stroke-miterlimit="10" points="416.287,331.909,382.456,298.077,416.287,264.245 "/>
                        </svg></div>';
                        echo '<div class="swiper-button-next next-' . $this->get_id() . ' next-' . $repeater_counter . '"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                        width="85.039px" height="85.039px" viewBox="378.426 255.12 85.039 85.039" enable-background="new 378.426 255.12 85.039 85.039"
                        xml:space="preserve">
                        <line fill="none" stroke="#000000" stroke-width="1.3845" stroke-miterlimit="10" x1="458.375" y1="298.077" x2="382.456" y2="298.077"/>
                        <polyline fill="none" stroke="#000000" stroke-width="1.3845" stroke-miterlimit="10" points="424.543,264.245,458.375,298.077
                        424.543,331.909 "/>
                        </svg></div>';
                    }
                }
                // end if slider_carousel
                echo '</div>';
            }
        } else {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                Helper::notice('', __('Select an ACF Repeater field', 'dynamic-content-for-elementor'));
            }
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
}
