<?php

// phpcs:ignoreFile
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class Views extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public $cpts;
    public $taxonomies;
    public $taxonomies_terms;
    public $wp_obj_type = ['post', 'user', 'term'];
    public $obj__in = [];
    public $the_query;
    public function get_style_depends()
    {
        return ['datatables', 'dce-views'];
    }
    public function get_script_depends()
    {
        return ['dce-infinitescroll', 'dce-datatables', 'dce-views'];
    }
    protected function get_objects()
    {
        return ['post' => ['title' => __('Posts', 'dynamic-content-for-elementor'), 'icon' => 'eicon-post-content'], 'user' => ['title' => __('Users', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-users'], 'term' => ['title' => __('Terms', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-tags']];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $cpts = $templates = $taxonomies = $taxonomies_terms = $post_fields = $post_status = array();
        $cpts = Helper::get_public_post_types(\false);
        $taxonomies = Helper::get_taxonomies();
        $post_status = get_post_statuses();
        $roles = Helper::get_roles();
        $this->taxonomies = $taxonomies;
        $sql_operators = Helper::get_sql_operators();
        //* OBJECT *//
        $this->start_controls_section('section_object', ['label' => __('Object', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_object', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => $this->get_objects(), 'default' => 'post', 'toggle' => \false]);
        $this->end_controls_section();
        // SKIN //
        $this->start_controls_section('section_skin', ['label' => __('Skin', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_style_format', ['label' => __('Skin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['grid' => __('Grid', 'dynamic-content-for-elementor'), 'table' => __('Table', 'dynamic-content-for-elementor'), 'list' => __('List', 'dynamic-content-for-elementor'), 'slideshow' => __('Carousel', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'default' => 'grid']);
        // SLIDESHOW
        $this->add_control('section_slider_options', ['label' => __('Carousel Options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['dce_views_style_format' => 'slideshow']]);
        $slides_to_show = \range(1, 10);
        $slides_to_show = \array_combine($slides_to_show, $slides_to_show);
        $this->add_responsive_control('slides_to_show', ['label' => __('Slides per View', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor')] + $slides_to_show, 'frontend_available' => \true, 'condition' => ['dce_views_style_format' => 'slideshow']]);
        $this->add_responsive_control('slides_to_scroll', ['label' => __('Slides per Group', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor')] + $slides_to_show, 'condition' => ['slides_to_show!' => '1', 'dce_views_style_format' => 'slideshow'], 'frontend_available' => \true]);
        $this->add_responsive_control('spaceBetween', ['label' => __('Space Between', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'tablet_default' => '', 'mobile_default' => '', 'min' => 0, 'max' => 100, 'step' => 1, 'frontend_available' => \true, 'selectors' => ['{{WRAPPER}} div.dce-view-results div.elementor-row.dce-view-grid div.item-page.dce-view-col.dce-view-single-wrapper.dce-view-grid-element' => 'padding: {{VALUE}}px;']]);
        $this->add_control('navigation', ['label' => __('Navigation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['both' => __('Arrows and Dots', 'dynamic-content-for-elementor'), 'arrows' => __('Arrows', 'dynamic-content-for-elementor'), 'dots' => __('Dots', 'dynamic-content-for-elementor'), 'none' => __('None', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'condition' => ['dce_views_style_format' => 'slideshow']]);
        $this->add_control('autoplay', ['label' => __('Autoplay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['dce_views_style_format' => 'slideshow']]);
        $this->add_control('pause_on_hover', ['label' => __('Pause on Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['dce_views_style_format' => 'slideshow', 'autoplay!' => '']]);
        $this->add_control('pause_on_interaction', ['label' => __('Pause on Interaction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['dce_views_style_format' => 'slideshow', 'autoplay!' => '']]);
        $this->add_control('autoplay_speed', ['label' => __('Autoplay Speed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 5000, 'condition' => ['dce_views_style_format' => 'slideshow', 'autoplay' => 'yes'], 'selectors' => ['{{WRAPPER}} .swiper-slide' => 'transition-duration: calc({{VALUE}}ms*1.2)'], 'frontend_available' => \true]);
        $this->add_control('infinite', ['label' => __('Infinite Loop', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['dce_views_style_format' => 'slideshow']]);
        $this->add_control('effect', ['label' => __('Effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'slide', 'options' => ['slide' => __('Slide', 'dynamic-content-for-elementor'), 'fade' => __('Fade', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'condition' => ['dce_views_style_format' => 'slideshow', 'slide_to_show' => 1]]);
        $this->add_control('transition_speed', ['label' => __('Animation Speed', 'dynamic-content-for-elementor') . ' (ms)', 'type' => Controls_Manager::NUMBER, 'default' => 500, 'frontend_available' => \true, 'condition' => ['dce_views_style_format' => 'slideshow']]);
        $this->add_control('section_style_navigation', ['label' => __('Carousel Navigation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['dce_views_style_format' => 'slideshow', 'navigation' => ['arrows', 'dots', 'both']]]);
        $this->add_control('heading_style_arrows', ['label' => __('Arrows', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'condition' => ['dce_views_style_format' => 'slideshow', 'navigation' => ['arrows', 'both']]]);
        $this->add_control('arrows_position', ['label' => __('Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'inside', 'options' => ['inside' => __('Inside', 'dynamic-content-for-elementor'), 'outside' => __('Outside', 'dynamic-content-for-elementor')], 'prefix_class' => 'elementor-arrows-position-', 'condition' => ['dce_views_style_format' => 'slideshow', 'navigation' => ['arrows', 'both']]]);
        $this->add_control('arrows_size', ['label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 20, 'max' => 60]], 'selectors' => ['{{WRAPPER}} .elementor-swiper-button.elementor-swiper-button-prev, {{WRAPPER}} .elementor-swiper-button.elementor-swiper-button-next' => 'font-size: {{SIZE}}{{UNIT}};'], 'condition' => ['dce_views_style_format' => 'slideshow', 'navigation' => ['arrows', 'both']]]);
        $this->add_control('arrows_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-swiper-button.elementor-swiper-button-prev, {{WRAPPER}} .elementor-swiper-button.elementor-swiper-button-next' => 'color: {{VALUE}};'], 'condition' => ['dce_views_style_format' => 'slideshow', 'navigation' => ['arrows', 'both']]]);
        $this->add_control('heading_style_dots', ['label' => __('Dots', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'condition' => ['dce_views_style_format' => 'slideshow', 'navigation' => ['dots', 'both']]]);
        $this->add_control('dots_position', ['label' => __('Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'outside', 'options' => ['outside' => __('Outside', 'dynamic-content-for-elementor'), 'inside' => __('Inside', 'dynamic-content-for-elementor')], 'prefix_class' => 'elementor-pagination-position-', 'condition' => ['dce_views_style_format' => 'slideshow', 'navigation' => ['dots', 'both']]]);
        $this->add_control('dots_size', ['label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 5, 'max' => 10]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'], 'condition' => ['dce_views_style_format' => 'slideshow', 'navigation' => ['dots', 'both']]]);
        $this->add_control('dots_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullet' => 'background: {{VALUE}};'], 'condition' => ['dce_views_style_format' => 'slideshow', 'navigation' => ['dots', 'both']]]);
        $this->add_control('dce_views_style_table_data', ['label' => __('Use DataTables', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __("Add advanced interaction controls to your HTML tables.", 'dynamic-content-for-elementor') . '<br><small>' . __('Read more on ', 'dynamic-content-for-elementor') . ' <a href="https://datatables.net/" target="_blank">DataTables</a></small>', 'condition' => ['dce_views_style_format' => 'table']]);
        $this->add_control('heading_views_datatables_extensions', ['label' => __('DataTables Extensions', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::RAW_HTML, 'raw' => '<br><small>' . __('Read more on ', 'dynamic-content-for-elementor') . ' <a href="https://datatables.net/extensions/index" target="_blank">DataTables Extensions</a></small>', 'separator' => 'before', 'condition' => ['dce_views_style_format' => 'table', 'dce_views_style_table_data!' => '']]);
        $this->add_control('dce_views_style_table_data_autofill', ['label' => __('Autofill', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Excel-like click and drag copying and filling of data.', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_style_format' => 'table', 'dce_views_style_table_data!' => '']]);
        $this->add_control('dce_views_style_table_data_buttons', ['label' => __('Buttons', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('A common framework for user interaction buttons. Like Export and Print.', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_style_format' => 'table', 'dce_views_style_table_data!' => '']]);
        $this->add_control('dce_views_style_table_data_colreorder', ['label' => __('ColReorder', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Click-and-drag column reordering.', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_style_format' => 'table', 'dce_views_style_table_data!' => '']]);
        $this->add_control('dce_views_style_table_data_fixedcolumns', ['label' => __('FixedColumns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Fix one or more columns to the left or right of a scrolling table.', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_style_format' => 'table', 'dce_views_style_table_data!' => '']]);
        $this->add_control('dce_views_style_table_data_fixedheader', ['label' => __('FixedHeader', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Sticky header and / or footer for the table.', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_style_format' => 'table', 'dce_views_style_table_data!' => '']]);
        $this->add_control('dce_views_style_table_data_keytable', ['label' => __('KeyTable', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Keyboard navigation of cells in a table, just like a spreadsheet.', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_style_format' => 'table', 'dce_views_style_table_data!' => '']]);
        $this->add_control('dce_views_style_table_data_responsive', ['label' => __('Responsive', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Dynamically show and hide columns based on the browser size.', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_style_format' => 'table', 'dce_views_style_table_data!' => '']]);
        $this->add_control('dce_views_style_table_data_rowgroup', ['label' => __('RowGroup', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Show similar data grouped together by a custom data point.', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_style_format' => 'table', 'dce_views_style_table_data!' => '']]);
        $this->add_control('dce_views_style_table_data_rowreorder', ['label' => __('RowReorder', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Click-and-drag reordering of rows.', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_style_format' => 'table', 'dce_views_style_table_data!' => '']]);
        $this->add_control('dce_views_style_table_data_scroller', ['label' => __('Scroller', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Virtual rendering of a scrolling table for large data sets.', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_style_format' => 'table', 'dce_views_style_table_data!' => '']]);
        $this->add_control('dce_views_style_table_data_scroller_y', ['label' => __('Scroller Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'default' => '200', 'description' => __('Height of virtual scroller.', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_style_format' => 'table', 'dce_views_style_table_data!' => '', 'dce_views_style_table_data_scroller!' => '']]);
        $this->add_control('dce_views_style_table_data_select', ['label' => __('Select', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Adds row, column and cell selection abilities to a table.', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_style_format' => 'table', 'dce_views_style_table_data!' => '']]);
        $this->add_control('dce_views_style_list', ['label' => __('List type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['ul' => ['title' => __('Unordered List', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list-ul'], 'ol' => ['title' => __('Ordered List', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list-ol']], 'toggle' => \false, 'default' => 'ul', 'condition' => ['dce_views_style_format' => 'list']]);
        $this->add_responsive_control('dce_views_style_col_width', ['label' => __('Column Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor'), '100' => '100%', '90' => '90%', '83' => '83%', '80' => '80%', '75' => '75%', '70' => '70%', '66' => '66%', '60' => '60%', '50' => '50%', '40' => '40%', '33' => '33%', '30' => '30%', '25' => '25%', '20' => '20%', '16' => '16%', '14' => '14%', '12' => '12%', '11' => '11%', '10' => '10%'], 'default' => '100', 'condition' => ['dce_views_style_format' => 'grid']]);
        $this->add_control('dce_views_style_col_grow', ['label' => __('Flex grow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 1, 'selectors' => ['{{WRAPPER}} .dce-view-col' => 'flex-grow: {{VALUE}};'], 'condition' => ['dce_views_style_format' => 'grid']]);
        $this->add_control('dce_views_select_class_heading', ['label' => __('Custom classes', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('dce_views_style_wrapper_class', ['label' => __('Wrapper', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'row']);
        $this->add_control('dce_views_style_element_class', ['label' => __('Single Element', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'col col-md-4 col-sm-2']);
        $this->end_controls_section();
        // SELECT
        $this->start_controls_section('section_select', ['label' => __('Select', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_select_type', ['label' => __('Content type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['fields' => ['title' => __('Fields', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list'], 'text' => ['title' => __('Text', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'template' => ['title' => __('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'text']);
        $this->add_control('dce_views_select_template', ['label' => __('Render Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'condition' => ['dce_views_select_type' => 'template']]);
        $this->add_control('dce_views_select_template_ajax', ['label' => __('Lazy Load', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_views_select_type' => 'template']]);
        $this->add_control('dce_views_select_template_ajax_progressive', ['label' => __('Progressive Load', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_views_select_type' => 'template', 'dce_views_select_template_ajax!' => '']]);
        $this->add_control('dce_views_select_template_ajax_placeholder', ['label' => __('Progressive Load', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['image' => ['title' => __('Image', 'dynamic-content-for-elementor'), 'icon' => 'eicon-image'], 'text' => ['title' => __('Text', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-font'], 'clone' => ['title' => __('Clone', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-clone'], 'fadein' => ['title' => __('FadeIn', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-low-vision']], 'toggle' => \false, 'default' => 'fadein', 'condition' => ['dce_views_select_type' => 'template', 'dce_views_select_template_ajax!' => '']]);
        $this->add_control('dce_views_select_template_ajax_placeholder_text', ['label' => __('Placeholder Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>', 'condition' => ['dce_views_select_type' => 'template', 'dce_views_select_template_ajax!' => '', 'dce_views_select_template_ajax_placeholder' => 'text']]);
        $this->add_control('dce_views_select_template_ajax_placeholder_image', ['label' => __('Placeholder Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()], 'condition' => ['dce_views_select_type' => 'template', 'dce_views_select_template_ajax!' => '', 'dce_views_select_template_ajax_placeholder' => 'image']]);
        $this->add_control('dce_views_select_text', ['label' => __('HTML & Tokens', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'default' => '[post:thumb]<h4>[post:title|esc_html]</h4><p>[post:excerpt]</p><a class="btn btn-primary" href="[post:permalink]">' . __("Read more", 'dynamic-content-for-elementor') . '</a>', 'condition' => ['dce_views_select_type' => 'text']]);
        $repeater_fields = new \Elementor\Repeater();
        $repeater_fields->add_control('dce_views_select_field', ['label' => __('Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Meta key or field name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'fields', 'object_type' => 'any', 'dynamic' => ['active' => \false]]);
        $repeater_fields->add_control('dce_views_select_label', ['label' => __('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $repeater_fields->add_control('dce_views_select_label_inline', ['label' => __('Inline label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_views_select_label!' => '']]);
        $repeater_fields->add_control('dce_views_select_render', ['label' => __('Render', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['auto' => ['title' => __('Auto', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-rocket'], 'rewrite' => ['title' => __('Text', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-edit'], 'image' => ['title' => __('Image', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-image']], 'toggle' => \false, 'default' => 'auto']);
        $repeater_fields->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'dce_views_select_image', 'label' => __('Image Format', 'dynamic-content-for-elementor'), 'default' => 'large', 'condition' => ['dce_views_select_render' => 'image']]);
        $repeater_fields->add_control('dce_views_select_tag', ['label' => __('HTML Tag', 'dynamic-content-for-elementor'), 'description' => __('Wrap the output of this field in this HTML Tag.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags([], \true), 'condition' => ['dce_views_select_render' => 'auto']]);
        $repeater_fields->add_control('dce_views_select_rewrite', ['label' => __('Rewrite field', 'dynamic-content-for-elementor'), 'description' => __('Override the output of this field with custom text. You may include HTML and Tokens.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '[field]', 'placeholder' => '[field]', 'condition' => ['dce_views_select_render' => 'rewrite']]);
        $repeater_fields->add_control('dce_views_select_link', ['label' => __('Link to Object', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $repeater_fields->add_control('dce_views_select_description', ['label' => __('Description', 'dynamic-content-for-elementor'), 'rows' => 1, 'type' => Controls_Manager::TEXTAREA]);
        $repeater_fields->add_control('dce_views_select_no_results', ['label' => __('Fallback', 'dynamic-content-for-elementor'), 'description' => __('Provide a fallback text to display if this field contains an empty result. You may include HTML and Tokens.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $repeater_fields->add_control('custom_classes_heading', ['label' => __('Custom classes', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $repeater_fields->add_control('dce_views_select_class_wrapper', ['label' => __('Wrapper', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $repeater_fields->add_control('dce_views_select_class_label', ['label' => __('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $repeater_fields->add_control('dce_views_select_class_value', ['label' => __('Value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $this->add_control('dce_views_select_fields', ['label' => __('Show these fields', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater_fields->get_controls(), 'title_field' => '{{{ dce_views_select_field }}}', 'default' => ['dce_views_select_field' => 'post_title'], 'condition' => ['dce_views_select_type' => 'fields']]);
        $this->end_controls_section();
        //* COUNT *//
        $this->start_controls_section('section_count', ['label' => __('Count', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_count', ['label' => __('Display Count', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('dce_views_count_text', ['label' => __('Count Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'default' => 'Page [QUERY:page] | [QUERY:start]-[QUERY:end] of [QUERY:total]', 'condition' => ['dce_views_count!' => '']]);
        $this->add_control('dce_views_count_position', ['label' => __('Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['top' => ['title' => __('Top', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-arrow-up'], 'bottom' => ['title' => __('Bottom', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-arrow-down'], 'both' => ['title' => __('Both', 'dynamic-content-for-elementor'), 'icon' => 'fas fa-portrait']], 'toggle' => \false, 'default' => 'bottom', 'condition' => ['dce_views_count!' => '']]);
        $this->end_controls_section();
        //* FROM *//
        $this->start_controls_section('section_from', ['label' => __('From', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_from_dynamic', ['label' => __('Dynamic', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Automatic fetch results from global WP_Query, change by current page', 'dynamic-content-for-elementor'), 'separator' => 'after', 'condition' => ['dce_views_object' => 'post']]);
        unset($cpts['elementor_library']);
        $this->add_control('dce_views_cpt', ['label' => __('Post Type', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => Controls_Manager::SELECT2, 'options' => $cpts + array('nav_menu_item' => __('Navigation menu item', 'dynamic-content-for-elementor'), 'custom' => __('Custom', 'dynamic-content-for-elementor'), 'any' => __('Any', 'dynamic-content-for-elementor')), 'default' => ['post'], 'multiple' => \true, 'condition' => ['dce_views_from_dynamic' => '', 'dce_views_object' => 'post']]);
        $this->add_control('dce_views_attachment_mime_type', ['label' => __('Mime Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'application/pdf,image/jpeg,image/png', 'condition' => ['dce_views_cpt' => 'attachment', 'dce_views_from_dynamic' => '', 'dce_views_object' => 'post']]);
        $this->add_control('dce_views_cpt_custom', ['label' => __('CPT name', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'my_cpt_name', 'condition' => ['dce_views_cpt' => 'custom', 'dce_views_from_dynamic' => '', 'dce_views_object' => 'post']]);
        $this->add_control('dce_views_status', ['label' => __('Status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $post_status + array('any' => __('Any', 'dynamic-content-for-elementor')), 'multiple' => \true, 'default' => ['publish'], 'condition' => ['dce_views_from_dynamic' => '', 'dce_views_object' => 'post']]);
        $this->add_control('dce_views_from_ignore_sticky_posts', ['label' => __('Ignore Sticky Posts', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Ignores that a post is sticky and shows the posts in the normal order. Your sticky posts will appear in the loop, however they will not be placed on the top', 'dynamic-content-for-elementor'), 'separator' => 'after', 'condition' => ['dce_views_from_dynamic' => '', 'dce_views_object' => 'post']]);
        $this->add_control('taxonomy_heading', ['label' => __('Taxonomy', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'condition' => ['dce_views_from_dynamic' => '', 'dce_views_object!' => 'user']]);
        $this->add_control('dce_views_tax', ['label' => __('Taxonomy', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $taxonomies, 'multiple' => \true, 'condition' => ['dce_views_object' => 'term']]);
        $this->add_control('dce_views_empty', ['label' => __('Hide Empty', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['dce_views_object' => 'term']]);
        $this->add_control('dce_views_term_count', ['label' => __('Count', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_views_object' => 'term']]);
        $this->add_control('dce_views_term_parent', ['label' => __('Term Parents', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_views_object' => 'term']]);
        $this->add_control('dce_views_term_parent_dynamic', ['label' => __('Dynamic', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_views_object' => 'term', 'dce_views_term_parent!' => '']]);
        $this->add_control('dce_views_term_parent_dynamic_id', ['label' => __('Set Term Parent ID', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'placeholder' => __('[term:term_id]', 'dynamic-content-for-elementor'), 'label_block' => \true, 'condition' => ['dce_views_object' => 'term', 'dce_views_term_parent!' => '', 'dce_views_term_parent_dynamic!' => '']]);
        $this->add_control('dce_views_term_parent_id', ['label' => __('Set Term Parent', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Empty for all root Terms', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'terms', 'condition' => ['dce_views_object' => 'term', 'dce_views_term_parent!' => '', 'dce_views_term_parent_dynamic' => '']]);
        $this->add_control('dce_views_role', ['label' => __('Roles', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $roles, 'multiple' => \true, 'condition' => ['dce_views_object' => 'user']]);
        $this->add_control('dce_views_role_exclude', ['label' => __('Exclude Roles', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $roles, 'multiple' => \true, 'condition' => ['dce_views_object' => 'user']]);
        $this->add_control('dce_views_term_custom', ['label' => __('Dynamic Terms', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => __('Term IDs', 'dynamic-content-for-elementor'), 'label_block' => \true, 'description' => __('Type Terms IDs or Slugs separated by comma', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_from_dynamic' => '', 'dce_views_object' => 'post']]);
        unset($taxonomies['elementor_library_type']);
        foreach ($taxonomies as $tkey => $atax) {
            if ($tkey) {
                $this->add_control('dce_views_term_' . $tkey, ['label' => $atax, 'type' => 'ooo_query', 'placeholder' => __('Term Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'terms', 'object_type' => $tkey, 'multiple' => \true, 'condition' => ['dce_views_from_dynamic' => '', 'dce_views_object' => 'post']]);
            }
        }
        $this->add_control('dce_views_tax_relation', ['label' => __('Tax Relation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['AND' => 'AND', 'OR' => 'OR'], 'toggle' => \false, 'default' => 'OR', 'condition' => ['dce_views_from_dynamic' => '', 'dce_views_object' => 'post']]);
        $this->add_control('dce_views_term_not', ['label' => __('Not in Terms', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'query_type' => 'terms', 'placeholder' => __('Select Terms', 'dynamic-content-for-elementor'), 'label_block' => \true, 'multiple' => \true, 'separator' => 'before', 'description' => __('Exclude posts related to this terms', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_from_dynamic' => '', 'dce_views_object' => 'post']]);
        $this->add_control('dce_views_ignore_ids', ['label' => __('Ignore IDs', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'separator' => 'before']);
        $this->end_controls_section();
        // WHERE - Conditions
        $this->start_controls_section('section_where', ['label' => __('Where - Filter criteria', 'dynamic-content-for-elementor')]);
        $repeater_where = new \Elementor\Repeater();
        $repeater_where->add_control('dce_views_where_field', ['label' => __('Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Meta key or Field Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'fields', 'object_type' => 'any', 'dynamic' => ['active' => \false]]);
        $repeater_where->add_control('dce_views_where_field_is_sub', ['label' => __('Has Sub Fields', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('For data stored Serialized or in JSON format', 'dynamic-content-for-elementor')]);
        $repeater_where->add_control('dce_views_where_field_sub', ['label' => __('Sub Field', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '[field]', 'description' => __('Use Token notation to access to sub field value. Example: [field:sub_field], [field:sub_array:sub_sub_field], [field:0:sub_field]', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_where_field_is_sub!' => '']]);
        $repeater_where->add_control('dce_views_where_operator', ['label' => __('Operator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $sql_operators, 'default' => '=']);
        $repeater_where->add_control('dce_views_where_value', ['label' => __('Value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $repeater_where->add_control('dce_views_where_rule', ['label' => __('Combination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['AND' => 'AND', 'OR' => 'OR'], 'toggle' => \false, 'default' => 'OR']);
        $this->add_control('dce_views_where', ['label' => __('Filter by these conditions', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater_where->get_controls(), 'prevent_empty' => \false, 'title_field' => '{{{ dce_views_where_field }}}']);
        $this->end_controls_section();
        //* WHERE - Exposed form
        $this->start_controls_section('section_form', ['label' => __('Where - Exposed form', 'dynamic-content-for-elementor')]);
        $repeater_form = new \Elementor\Repeater();
        $repeater_form->start_controls_tabs('dce_views_where_form_fields_tabs');
        $repeater_form->start_controls_tab('dce_views_where_form_fields_content_tab', ['label' => __('Content', 'dynamic-content-for-elementor')]);
        $repeater_form->add_control('dce_views_where_form_field', ['label' => __('Filter', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Meta key or field name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'taxonomies_fields', 'object_type' => 'any']);
        $repeater_form->add_control('dce_views_where_form_field_is_sub', ['label' => __('Has Sub Fields', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('For data stored Serialized or in JSON format', 'dynamic-content-for-elementor')]);
        $repeater_form->add_control('dce_views_where_form_field_orderby', ['label' => __('Order by', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['name' => __('Name', 'dynamic-content-for-elementor'), 'parent' => __('Parent', 'dynamic-content-for-elementor'), 'count' => __('Count (number of associated posts)', 'dynamic-content-for-elementor'), 'term_order' => __('Order', 'dynamic-content-for-elementor'), 'slug' => __('Slug', 'dynamic-content-for-elementor'), 'term_group' => __('Group', 'dynamic-content-for-elementor'), 'term_id' => 'ID'], 'default' => 'name', 'condition' => ['dce_views_where_form_type!' => 'text']]);
        $repeater_form->add_control('dce_views_where_form_field_order', ['label' => __('Sorting', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['ASC' => ['title' => __('ASC', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-sort-up'], 'DESC' => ['title' => __('DESC', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-sort-down']], 'inline' => \true, 'toggle' => \false, 'default' => 'ASC', 'condition' => ['dce_views_where_form_type!' => 'text']]);
        $repeater_form->add_control('dce_views_where_form_field_sub', ['label' => __('Sub Field', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '[field]', 'description' => __('Use Token notation to access to sub field value. Example:', 'dynamic-content-for-elementor') . '[field:sub_field], [field:sub_array:sub_sub_field], [field:0:sub_field]', 'condition' => ['dce_views_where_form_field_is_sub!' => '']]);
        $repeater_form->add_control('dce_views_where_form_label', ['label' => __('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $repeater_form->add_control('dce_views_where_form_type', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => array('text' => 'Text', 'select' => 'Select', 'radio' => 'Radio', 'checkbox' => 'Checkbox', 'auto' => 'AUTO'), 'default' => 'auto']);
        $repeater_form->add_control('dce_views_where_form_field_multiple', ['label' => __('Multiple', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_views_where_form_type' => ['select']]]);
        $repeater_form->add_control('dce_views_where_form_operator', ['label' => __('Operator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $sql_operators, 'default' => 'LIKE']);
        $repeater_form->add_control('dce_views_where_form_value', ['label' => __('Value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'description' => __('If select/checkbox/radio use one line for option, use | to separate value and name (e.g.:"my_value|Name of the option").', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_where_form_type' => ['radio', 'checkbox', 'select']]]);
        $repeater_form->add_control('dce_views_where_form_value_format', ['label' => __('Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['reset' => __('value|Label', 'dynamic-content-for-elementor'), 'end' => __('Label|value', 'dynamic-content-for-elementor')], 'toggle' => \false, 'default' => 'reset', 'condition' => ['dce_views_where_form_type' => ['radio', 'checkbox', 'select']]]);
        $repeater_form->add_control('dce_views_where_form_rule', ['label' => __('Combination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['AND' => 'AND', 'OR' => 'OR'], 'toggle' => \false, 'default' => 'AND']);
        $repeater_form->add_control('dce_views_where_form_required', ['label' => __('Required', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $repeater_form->end_controls_tab();
        $repeater_form->start_controls_tab('dce_views_where_form_fields_advanced_tab', ['label' => __('Advanced', 'dynamic-content-for-elementor')]);
        $repeater_form->add_control('dce_views_where_form_preselect', ['label' => __('Default Value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'description' => __('Insert default value.', 'dynamic-content-for-elementor')]);
        $repeater_form->add_control('dce_views_where_form_required_empty_label', ['label' => __('Empty option label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Select a value', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_where_form_required' => '']]);
        $repeater_form->add_control('dce_views_where_form_hint', ['label' => __('Hint', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'description' => __('A short description of the field', 'dynamic-content-for-elementor')]);
        $repeater_form->add_control('dce_views_where_form_placeholder', ['label' => __('Placeholder', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $repeater_form->end_controls_tab();
        $repeater_form->start_controls_tab('dce_views_where_form_fields_style_tab', ['label' => __('Style', 'dynamic-content-for-elementor')]);
        $repeater_form->add_control('dce_views_where_form_required_label', ['label' => __('Post Field Status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['none' => ['title' => __('None', 'dynamic-content-for-elementor'), 'icon' => 'eicon-close'], 'asterisk' => ['title' => __('*', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-asterisk'], 'text' => ['title' => __('Text', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-i-cursor']], 'default' => 'asterisk', 'toggle' => \false, 'condition' => ['dce_views_where_form_required!' => '']]);
        $repeater_form->add_control('dce_views_where_form_required_label_text', ['label' => __('Required marker text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('required', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_where_form_required!' => '', 'dce_views_where_form_required_label' => 'text']]);
        $repeater_form->add_control('dce_views_where_form_field_inline', ['label' => __('Inline', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_views_where_form_type' => ['radio', 'checkbox']]]);
        $repeater_form->add_responsive_control('dce_views_where_form_width', ['label' => __('Column Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor'), '100' => '100%', '80' => '80%', '75' => '75%', '66' => '66%', '60' => '60%', '50' => '50%', '40' => '40%', '33' => '33%', '25' => '25%', '20' => '20%'], 'default' => '100']);
        $repeater_form->add_control('dce_views_where_form_custom_classes_heading', ['label' => __('Custom classes', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $repeater_form->add_control('dce_views_where_form_class_wrapper', ['label' => __('Wrapper', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $repeater_form->add_control('dce_views_where_form_class_label', ['label' => __('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $repeater_form->add_control('dce_views_where_form_class_input', ['label' => __('Input', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $repeater_form->end_controls_tab();
        $repeater_form->end_controls_tabs();
        $this->add_control('dce_views_where_form', ['label' => __('Exposed Fields', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater_form->get_controls(), 'prevent_empty' => \false, 'title_field' => '{{{ dce_views_where_form_field }}}', 'separator' => 'before']);
        $this->add_control('dce_views_input_size', ['label' => __('Input Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::bootstrap_button_sizes(), 'default' => 'sm', 'separator' => 'before']);
        $this->add_control('dca_views_style_form_show_labels', ['label' => __('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['inline' => ['title' => __('Inline', 'dynamic-content-for-elementor'), 'icon' => 'fas fa-tv'], 'block' => ['title' => __('Block', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-stop'], 'none' => ['title' => __('None', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-eye-slash']], 'default' => 'inline', 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form label.dce-view-input-label' => 'display: {{VALUE}};']]);
        $this->add_control('dce_views_style_form_text', ['label' => __('Form Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['dce_views_where_form!' => ['', []]], 'separator' => 'before']);
        $this->add_control('dce_views_style_form_text_size', ['label' => __('Title HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(), 'default' => 'h4', 'condition' => ['dce_views_where_form!' => ['', []], 'dce_views_style_form_text!' => '']]);
        $this->add_control('dce_views_where_form_result', ['label' => __('Show result', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'description' => __("Show results from first time, also before user interact with form, using preselected value.", 'dynamic-content-for-elementor'), 'condition' => ['dce_views_where_form!' => ['', []]]]);
        $this->add_control('dce_views_where_form_ajax', ['label' => __('Use Ajax', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_views_where_form!' => ['', []]]]);
        $this->add_control('dce_views_where_form_ajax_transition', ['label' => __('Transition', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => array('' => 'Toggle', 'fade' => 'Fade', 'slide' => 'Slide'), 'condition' => ['dce_views_where_form!' => ['', []], 'dce_views_where_form_ajax!' => '']]);
        $this->add_control('dce_views_where_form_ajax_onchange', ['label' => __('Submit on change', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_views_where_form!' => ['', []], 'dce_views_where_form_ajax!' => '']]);
        $this->add_control('dce_views_where_form_ajax_nobutton', ['label' => __('Remove submit button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form input.dce-button.find' => 'display: none;'], 'render_type' => 'template', 'condition' => ['dce_views_where_form!' => ['', []], 'dce_views_where_form_ajax!' => '', 'dce_views_where_form_ajax_onchange!' => '']]);
        $this->add_control('dce_views_where_form_reset', ['label' => __('Show Reset', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_views_where_form!' => ['', []]]]);
        $this->add_control('dce_views_where_form_filters_heading', ['label' => __('Active Filters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['dce_views_where_form!' => ['', []]]]);
        $this->add_control('dce_views_where_form_active_filters', ['label' => __('Show Active Filters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_views_where_form!' => ['', []]]]);
        $this->add_control('dce_views_where_form_active_filters_remove', ['label' => __('Remove Filters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['dce_views_where_form!' => ['', []], 'dce_views_where_form_active_filters!' => '']]);
        $this->add_control('dce_views_where_form_active_filters_no_message', ['label' => __('No Filter Message', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['dce_views_where_form!' => ['', []], 'dce_views_where_form_active_filters!' => '']]);
        $this->add_control('dce_views_where_form_action_heading', ['label' => __('Form Action', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['dce_views_where_form!' => ['', []]]]);
        $this->add_control('dce_views_style_form_submit_text', ['label' => __('Submit Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Search', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_where_form!' => ['', []], 'dce_views_where_form_ajax_nobutton' => '']]);
        $this->add_control('dce_views_style_form_reset_text', ['label' => __('Reset Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Reset', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_where_form!' => ['', []], 'dce_views_where_form_reset!' => '']]);
        $this->add_responsive_control('dce_views_where_form_action_width', ['label' => __('Column Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor'), '100' => '100%', '80' => '80%', '75' => '75%', '66' => '66%', '60' => '60%', '50' => '50%', '40' => '40%', '33' => '33%', '25' => '25%', '20' => '20%'], 'default' => '100', 'condition' => ['dce_views_where_form!' => ['', []]]]);
        $this->add_control('dce_views_where_form_class_heading', ['label' => __('Custom classes', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['dce_views_where_form!' => ['', []]]]);
        $this->add_control('dce_views_where_form_class', ['label' => __('Form', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['dce_views_where_form!' => ['', []]]]);
        $this->add_control('dce_views_where_form_class_wrapper', ['label' => __('Wrapper', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'dce-basic-form', 'condition' => ['dce_views_where_form!' => ['', []]]]);
        $this->add_control('dce_views_where_form_class_filter', ['label' => __('Filters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'form-group', 'condition' => ['dce_views_where_form!' => ['', []]]]);
        $this->add_control('dce_views_where_form_class_buttons', ['label' => __('Buttons wrapper', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'form-action', 'condition' => ['dce_views_where_form!' => ['', []]]]);
        $this->add_control('dce_views_where_form_class_button', ['label' => __('Button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'btn-primary', 'condition' => ['dce_views_where_form!' => ['', []]]]);
        $this->end_controls_section();
        //* GROUP BY
        $this->start_controls_section('section_group_by', ['label' => __('Group By', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_group_by_field', ['label' => __('Grouping field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Meta key or field name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'dynamic' => ['active' => \false], 'query_type' => 'taxonomies_fields', 'object_type' => 'any']);
        $this->add_control('dce_views_group_by_field_orderby', ['label' => __('Order by', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['name' => __('Name', 'dynamic-content-for-elementor'), 'parent' => __('Parent', 'dynamic-content-for-elementor'), 'count' => __('Count (number of associated posts)', 'dynamic-content-for-elementor'), 'term_order' => __('Order', 'dynamic-content-for-elementor'), 'menu_order' => __('Menu Order', 'dynamic-content-for-elementor'), 'slug' => __('Slug', 'dynamic-content-for-elementor'), 'term_group' => __('Group', 'dynamic-content-for-elementor'), 'term_id' => 'ID'], 'default' => 'name']);
        $this->add_control('dce_views_group_by_field_order', ['label' => __('Sorting', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['ASC' => ['title' => __('ASC', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-sort-up'], 'DESC' => ['title' => __('DESC', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-sort-down']], 'inline' => \true, 'toggle' => \false, 'default' => 'ASC', 'separator' => 'after']);
        $this->add_control('dce_views_group_by_field_heading_show', ['label' => __('Show Heading', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('dce_views_group_by_field_heading', ['label' => __('Heading text', 'dynamic-content-for-elementor'), 'default' => '[TITLE]', 'placeholder' => '[TITLE]', 'description' => __('Group heading text. You may include HTML and Tokens.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['dce_views_group_by_field_heading_show!' => '']]);
        $this->add_control('dce_views_group_by_heading_size', ['label' => __('Heading HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags([], \true), 'default' => 'h4', 'condition' => ['dce_views_group_by_field_heading_show!' => '']]);
        $this->add_control('dce_views_group_by_classes_heading', ['label' => __('Custom classes', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('dce_views_group_by_class_wrapper', ['label' => __('Wrapper', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $this->add_control('dce_views_group_by_class_heading', ['label' => __('Heading', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['dce_views_group_by_field_heading_show!' => '']]);
        $this->add_control('dce_views_group_by_accordion', ['label' => __('Accordion', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_views_group_by_field_heading_show!' => '', 'dce_views_style_format' => 'grid'], 'separator' => 'before']);
        $this->add_control('dce_views_group_by_accordion_display', ['label' => __('Display', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'options' => ['block' => __('Block', 'dynamic-content-for-elementor'), 'flex' => __('Flex', 'dynamic-content-for-elementor')], 'default' => 'block', 'condition' => ['dce_views_group_by_field_heading_show!' => '', 'dce_views_style_format' => 'grid', 'dce_views_group_by_accordion!' => '']]);
        $this->add_control('dce_views_group_by_accordion_start', ['label' => __('Initially open', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['none' => ['title' => __('None', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban'], 'first' => ['title' => __('First', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-right'], 'all' => ['title' => __('All', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-double-right']], 'inline' => \true, 'toggle' => \false, 'default' => 'none', 'condition' => ['dce_views_group_by_field_heading_show!' => '', 'dce_views_group_by_accordion!' => '', 'dce_views_style_format' => 'grid']]);
        $this->end_controls_section();
        // ORDER BY
        $repeater_order = new \Elementor\Repeater();
        $repeater_order->add_control('dce_views_order_field', ['label' => __('Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Meta key or Field Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'fields', 'dynamic' => ['active' => \false], 'object_type' => 'any']);
        $repeater_order->add_control('dce_views_order_field_type', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor'), '_num' => __('Number', 'dynamic-content-for-elementor'), '_date' => __('Date', 'dynamic-content-for-elementor')]]);
        $repeater_order->add_control('dce_views_order_field_sort', ['label' => __('Sorting', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['ASC' => ['title' => __('ASC', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-sort-up'], 'DESC' => ['title' => __('DESC', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-sort-down']], 'inline' => \true, 'toggle' => \false, 'default' => 'ASC']);
        $repeater_order->add_control('dce_views_order_field_sort_exposed', ['label' => __('Exposed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Expose this sort to visitors, to allow them to change it', 'dynamic-content-for-elementor')]);
        $this->start_controls_section('section_order', ['label' => __('Order By - Sort criteria', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_order_random', ['label' => __('Random', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('dce_views_order_by', ['label' => __('Sorting by fields', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater_order->get_controls(), 'prevent_empty' => \false, 'title_field' => '{{{ dce_views_order_field }}}', 'condition' => ['dce_views_order_random' => '']]);
        $this->add_control('dce_views_order_by_no_nulls', ['label' => __('Exclude Posts where the sorting fields are not set (simpler, faster query).', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_block' => \true, 'condition' => ['dce_views_order_random' => '']]);
        $this->add_control('exposed_sort_heading', ['label' => __('Exposed Sort', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING]);
        $this->add_responsive_control('dce_views_order_form_width', ['label' => __('Form Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor'), '100' => '100%', '90' => '90%', '83' => '83%', '80' => '80%', '75' => '75%', '70' => '70%', '66' => '66%', '60' => '60%', '50' => '50%', '40' => '40%', '33' => '33%', '30' => '30%', '25' => '25%', '20' => '20%', '16' => '16%', '14' => '14%', '12' => '12%', '11' => '11%', '10' => '10%'], 'default' => '100', 'condition' => ['dce_views_order_by!' => []]]);
        $this->add_responsive_control('dce_views_order_form_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex-start' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'flex-end' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} .dce-view-exposed-sort-row' => 'justify-content: {{VALUE}};', '{{WRAPPER}} .dce-view-exposed-sort-row .elementor-column > *' => 'width: 100%;']]);
        $this->add_control('dce_views_order_label', ['label' => __('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Sort by', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_order_by!' => []]]);
        $this->add_control('dce_views_order_by_default', ['label' => __('Default Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Default', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_order_by!' => []]]);
        $this->add_control('dce_views_order_class', ['label' => __('Custom class', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'dce-basic-sort', 'condition' => ['dce_views_order_by!' => []]]);
        $this->end_controls_section();
        // LIMIT
        $this->start_controls_section('section_limit', ['label' => __('Limit - Pager', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_limit_offset', ['label' => __('Start from - Offset', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'min' => 0, 'description' => __("Number of items to skip. For example, set this to 3 and the first 3 items will not be displayed. Set 0 to show from the first result.", 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_limit_to', ['label' => __('Max allowed results displayed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'min' => 0, 'description' => __("Set 0 if you do not want to limit results", 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_pagination', ['label' => __('Pagination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('dce_views_post_per_page', ['label' => __('Results per page', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 10, 'min' => 0, 'description' => __("Set 0 for default site global limit.", 'dynamic-content-for-elementor'), 'condition' => ['dce_views_pagination!' => '']]);
        $this->add_control('dce_views_limit_offset_pagination', ['label' => __('Remove Offset from display', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_views_pagination!' => '', 'dce_views_limit_offset!' => '']]);
        $this->add_control('dce_views_pagination_type', ['label' => __('Pagination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'numbers_and_prev_next', 'options' => ['infinite_scroll' => __('Infinite Scroll', 'dynamic-content-for-elementor'), 'numbers' => __('Numbers', 'dynamic-content-for-elementor'), 'prev_next' => __('Previous/Next', 'dynamic-content-for-elementor'), 'numbers_and_prev_next' => __('Numbers', 'dynamic-content-for-elementor') . ' + ' . __('Previous/Next', 'dynamic-content-for-elementor')], 'condition' => ['dce_views_pagination!' => '']]);
        $this->add_control('dce_views_pagination_ajax', ['label' => __('Ajax', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_views_pagination!' => '', 'dce_views_pagination_type' => ['numbers', 'numbers_and_prev_next']]]);
        $this->add_control('dce_views_pagination_page_limit', ['label' => __('Page Limit', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'min' => 0, 'condition' => ['dce_views_pagination!' => '', 'dce_views_pagination_type' => ['numbers', 'numbers_and_prev_next']]]);
        $this->add_control('dce_views_pagination_numbers_shorten', ['label' => __('Shorten', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['dce_views_pagination!' => '', 'dce_views_pagination_type' => ['numbers', 'numbers_and_prev_next']]]);
        $this->add_control('dce_views_pagination_prev_label', ['label' => __('Previous Label', 'dynamic-content-for-elementor'), 'default' => __('&laquo; Previous', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_pagination!' => '', 'dce_views_pagination_type' => ['prev_next', 'numbers_and_prev_next']]]);
        $this->add_control('dce_views_pagination_next_label', ['label' => __('Next Label', 'dynamic-content-for-elementor'), 'default' => __('Next &raquo;', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_pagination!' => '', 'dce_views_pagination_type' => ['prev_next', 'numbers_and_prev_next']]]);
        $this->add_control('dce_views_limit_scroll_loading', ['label' => __('Label Loading', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Loading...', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_pagination!' => '', 'dce_views_pagination_type' => ['infinite_scroll']]]);
        $this->add_control('dce_views_limit_scroll_last', ['label' => __('Label Last', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('END', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_pagination!' => '', 'dce_views_pagination_type' => ['infinite_scroll']]]);
        $this->add_control('dce_views_limit_scroll_button', ['label' => __('Load on Click', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_views_pagination!' => '', 'dce_views_pagination_type' => ['infinite_scroll']]]);
        $this->add_control('dce_views_limit_scroll_button_label', ['label' => __('Button Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Load more', 'dynamic-content-for-elementor'), 'condition' => ['dce_views_pagination!' => '', 'dce_views_pagination_type' => ['infinite_scroll'], 'dce_views_limit_scroll_button!' => '']]);
        $this->end_controls_section();
        // FALLBACK for NO RESULTS
        $this->start_controls_section('section_fallback', ['label' => __('No results behaviour', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_fallback', ['label' => __('Fallback Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __("If you want to show something when no elements are found.", 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_fallback_type', ['label' => __('Content type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['text' => ['title' => __('Text', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'template' => ['title' => __('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'text', 'condition' => ['dce_views_fallback!' => '']]);
        $this->add_control('dce_views_fallback_template', ['label' => __('Render Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'condition' => ['dce_views_fallback!' => '', 'dce_views_fallback_type' => 'template']]);
        $this->add_control('dce_views_fallback_text', ['label' => __('Text Fallback', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'default' => __('No results found.', 'dynamic-content-for-elementor'), 'description' => __("Type here your content, you can use HTML and Tokens.", 'dynamic-content-for-elementor'), 'condition' => ['dce_views_fallback!' => '', 'dce_views_fallback_type' => 'text']]);
        $this->end_controls_section();
        //* STYLE *//
        // TABLE STYLE
        $this->start_controls_section('section_style_table', ['label' => __('Table', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_views_style_format' => 'table']]);
        // TR:hover, TR:odd, TR:even
        $this->start_controls_tabs('dce_views_style_table_row');
        $this->start_controls_tab('dce_views_style_table_cell_tr', ['label' => __('TR', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_style_table_cell_tr_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} table tbody > tr, {{WRAPPER}} table tbody > tr > td' => 'background: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('dce_views_style_table_cell_tr_head', ['label' => __('TR:head', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_style_table_cell_tr_head_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} table thead > tr, {{WRAPPER}} table thead > tr > th' => 'background: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('dce_views_style_table_cell_tr_even', ['label' => __('TR:even', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_style_table_cell_tr_even_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} table tbody > tr:nth-child(even), {{WRAPPER}} table tbody > tr:nth-child(even) > td' => 'background: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('dce_views_style_table_cell_tr_odd', ['label' => __('TR:odd', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_style_table_cell_tr_odd_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} table tbody > tr:nth-child(odd), {{WRAPPER}} table tbody > tr:nth-child(odd) > td' => 'background: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('dce_views_style_table_cell_tr_hover', ['label' => __('TR:hover', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_style_table_cell_tr_hover_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} table tbody > tr:hover, {{WRAPPER}} table tbody > tr:hover > td' => 'background: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        // TH, TD, TD-first, TD-last,
        $this->start_controls_tabs('dce_views_style_table_cell');
        $this->start_controls_tab('dce_views_style_table_cell_th', ['label' => __('TH', 'dynamic-content-for-elementor')]);
        $this->add_responsive_control('dce_views_style_table_cell_th_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} table thead > tr > th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_views_style_table_cell_th_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} table thead > tr > th']);
        $this->add_responsive_control('dce_views_style_table_cell_th_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify']], 'selectors' => ['{{WRAPPER}} table thead > tr > th' => 'text-align: {{VALUE}};']]);
        $this->add_control('dce_views_style_table_cell_th_vertical_alignment', ['label' => __('Vertical Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['bottom' => ['title' => __('Bottom', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom'], 'middle' => ['title' => __('Middle', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'top' => ['title' => __('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top']], 'selectors' => ['{{WRAPPER}} table thead > tr > th' => 'vertical-align: {{VALUE}};']]);
        $this->add_control('dce_views_style_table_cell_th_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} table thead > tr > th' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_views_style_table_cell_th_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} table thead > tr > th']);
        $this->end_controls_tab();
        $this->start_controls_tab('dce_views_style_table_cell_td', ['label' => __('TD', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_style_table_cell_td_vertical_alignment', ['label' => __('Vertical Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['bottom' => ['title' => __('Bottom', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom'], 'middle' => ['title' => __('Middle', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'top' => ['title' => __('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top']], 'selectors' => ['{{WRAPPER}} table tbody > tr > td' => 'vertical-align: {{VALUE}};']]);
        $this->add_control('dce_views_style_table_cell_td_more', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('You will find many other specific style settings in the "Single Result" section here below', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
        $this->end_controls_tab();
        $this->start_controls_tab('dce_views_style_table_cell_td_first', ['label' => __('TD:first', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_style_table_cell_td_first_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} table tbody > tr > td:first-child' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_views_style_table_cell_td_first', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} table tbody > tr > td:first-child']);
        $this->add_responsive_control('dce_views_style_table_cell_td_first_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify']], 'selectors' => ['{{WRAPPER}} table tbody > tr > td:first-child' => 'text-align: {{VALUE}};']]);
        $this->add_control('dce_views_style_table_cell_td_first_txt_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} table tbody > tr > td:first-child, {{WRAPPER}} table tbody > tr > td:first-child a' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_views_style_table_cell_td_first_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} table tbody > tr > td:first-child']);
        $this->end_controls_tab();
        $this->start_controls_tab('dce_views_style_table_cell_td_last', ['label' => __('TD:last', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_style_table_cell_td_last_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} table tbody > tr > td:last-child' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_views_style_table_cell_td_last', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} table tbody > tr > td:last-child']);
        $this->add_responsive_control('dce_views_style_table_cell_td_last_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify']], 'selectors' => ['{{WRAPPER}} table tbody > tr > td:last-child' => 'text-align: {{VALUE}};']]);
        $this->add_control('dce_views_style_table_cell_td_last_txt_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} table tbody > tr > td:last-child, {{WRAPPER}} table tbody > tr > td:last-child a' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_views_style_table_cell_td_last_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} table tbody > tr > td:last-child']);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_responsive_control('dce_views_style_table_cell_spacing', ['label' => __('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em'], 'selectors' => ['{{WRAPPER}} table' => 'border-collapse: separate; border-spacing: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}};'], 'separator' => 'before']);
        $this->end_controls_section();
        // COUNT
        $this->start_controls_section('section_style_count', ['label' => __('Count', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_views_count!' => '']]);
        $this->add_responsive_control('dce_views_style_count_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-view-count' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('dce_views_style_count_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} .dce-view-count' => 'text-align: {{VALUE}};']]);
        $this->add_control('dce_views_style_count_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-count' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_views_style_count_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-count']);
        $this->end_controls_section();
        // RESULTS WRAPPER
        $this->start_controls_section('section_style_results', ['label' => __('Results Container', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('dce_views_style_results_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-view-results' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('dce_views_style_results_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-view-results' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('dce_views_style_results_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} .dce-view-results' => 'text-align: {{VALUE}};']]);
        // Border
        $this->add_control('heading_views_results_border', ['label' => __('Border', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_views_style_results_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-results']);
        $this->add_control('dce_views_style_results_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-view-results' => 'overflow: hidden; border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', '{{WRAPPER}} .dce-view-results > table' => 'margin-bottom: 0;']]);
        // Background
        $this->add_control('heading_views_results_background', ['label' => __('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'dce_views_style_results_background', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-view-results']);
        $this->end_controls_section();
        // SINGLE RESULT
        $this->start_controls_section('section_style_result', ['label' => __('Single Result', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('dce_views_style_result_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-view-single, {{WRAPPER}} table tbody > tr > td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('dce_views_style_result_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-view-single' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('dce_views_style_result_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'selectors' => ['{{WRAPPER}} .dce-view-single, {{WRAPPER}} table tbody > tr > td' => 'text-align: {{VALUE}};']]);
        // Border
        $this->add_control('heading_views_result_border', ['label' => __('Border', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_views_style_result_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-single, {{WRAPPER}} table tbody > tr > td']);
        $this->add_control('dce_views_style_result_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-view-single, {{WRAPPER}} table tbody > tr > td' => 'overflow: hidden; border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        // Background ----------------
        $this->add_control('heading_views_result_background', ['label' => __('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'dce_views_style_result_background', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-view-single, {{WRAPPER}} table tbody > tr > td']);
        $this->add_control('heading_views_field_typo', ['label' => __('Field value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['dce_views_select_type!' => 'template']]);
        $this->add_control('dce_views_style_field_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-single' => 'color: {{VALUE}};'], 'condition' => ['dce_views_select_type!' => 'template']]);
        $this->add_control('dce_views_style_field_color_a', ['label' => __('Link Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-single a' => 'color: {{VALUE}};'], 'condition' => ['dce_views_select_type!' => 'template']]);
        $this->add_control('dce_views_style_field_color_a_hover', ['label' => __('Link Hover Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-single a:hover' => 'color: {{VALUE}};'], 'condition' => ['dce_views_select_type!' => 'template']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_views_style_field_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-single', 'condition' => ['dce_views_select_type!' => 'template']]);
        // Placeholder ----------------
        $this->add_control('heading_views_result_placeholder', ['label' => __('Placeholder', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['dce_views_select_type' => 'template', 'dce_views_select_template_ajax!' => '']]);
        $this->add_responsive_control('dce_views_style_field_placeholder_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-elementor-template-placeholder' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['dce_views_select_type' => 'template', 'dce_views_select_template_ajax!' => '']]);
        $this->add_responsive_control('dce_views_style_field_placeholder_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'selectors' => ['{{WRAPPER}} .dce-elementor-template-placeholder' => 'text-align: {{VALUE}};'], 'condition' => ['dce_views_select_type' => 'template', 'dce_views_select_template_ajax!' => '']]);
        $this->add_control('dce_views_style_field_placeholder_clone_img', ['label' => __('Obscure image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'selectors' => ['{{WRAPPER}} .dce-elementor-template-placeholder.dce-elementor-template-placeholder-clone img' => 'filter: blur(8px) grayscale(100%) !important;'], 'default' => 'yes', 'condition' => ['dce_views_select_type' => 'template', 'dce_views_select_template_ajax!' => '', 'dce_views_select_template_ajax_placeholder' => 'clone']]);
        $this->add_control('dce_views_style_field_placeholder_clone_txt', ['label' => __('Obscure text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'selectors' => ['{{WRAPPER}} .dce-elementor-template-placeholder.dce-elementor-template-placeholder-clone h1, {{WRAPPER}} .dce-elementor-template-placeholder.dce-elementor-template-placeholder-clone .elementor-widget-container, {{WRAPPER}} .dce-elementor-template-placeholder.dce-elementor-template-placeholder-clone h2, {{WRAPPER}} .dce-elementor-template-placeholder.dce-elementor-template-placeholder-clone h3, {{WRAPPER}} .dce-elementor-template-placeholder.dce-elementor-template-placeholder-clone h4, {{WRAPPER}} .dce-elementor-template-placeholder.dce-elementor-template-placeholder-clone h5, {{WRAPPER}} .dce-elementor-template-placeholder.dce-elementor-template-placeholder-clone h6, {{WRAPPER}} .dce-elementor-template-placeholder.dce-elementor-template-placeholder-clone p, {{WRAPPER}} .dce-elementor-template-placeholder.dce-elementor-template-placeholder-clone span' => 'color: transparent !important; text-shadow: 0 0 5px black !important;'], 'default' => 'yes', 'condition' => ['dce_views_select_type' => 'template', 'dce_views_select_template_ajax!' => '', 'dce_views_select_template_ajax_placeholder' => 'clone']]);
        $this->end_controls_section();
        // EXPOSED FORM
        $this->start_controls_section('section_style_form', ['label' => __('Exposed Form', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_views_where_form!' => ['', []]]]);
        $this->add_responsive_control('dce_views_style_form_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-view-form-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('dce_views_style_form_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-view-form-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('dce_views_style_form_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form' => 'text-align: {{VALUE}};']]);
        // Border ----------------
        $this->add_control('heading_views_border', ['label' => __('Border', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_views_style_form_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-form-wrapper']);
        $this->add_control('dce_views_style_form_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-view-form-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        // Background ----------------
        $this->add_control('heading_views_background', ['label' => __('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'background_search', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-view-exposed-form']);
        // Title ----------------
        $this->add_control('heading_views_title', ['label' => __('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['dce_views_style_form_text!' => '']]);
        $this->add_responsive_control('dce_views_style_form_title_align', ['label' => __('Title Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form .dce-views-form-title' => 'text-align: {{VALUE}};'], 'condition' => ['dce_views_style_form_text!' => '']]);
        $this->add_control('dce_views_style_form_title_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form .dce-views-form-title' => 'color: {{VALUE}};'], 'condition' => ['dce_views_style_form_text!' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_views_style_form_title_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-exposed-form .dce-views-form-title', 'condition' => ['dce_views_style_form_text!' => '']]);
        $this->add_control('dce_views_style_form_title_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['min' => -50, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form .dce-views-form-title' => 'margin-bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['dce_views_style_form_text!' => '']]);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'dce_views_style_form_title_text_shadow', 'selector' => '{{WRAPPER}} .dce-view-exposed-form .dce-views-form-title', 'condition' => ['dce_views_style_form_text!' => '']]);
        // Filters ----------------
        $this->add_control('heading_views_filters', ['label' => __('Filters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('dce_views_style_form_filters_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form .dce-view-field-filter' => 'padding: {{TOP}}{{UNIT}} calc( {{RIGHT}}{{UNIT}}/2 ) {{BOTTOM}}{{UNIT}} calc( {{LEFT}}{{UNIT}}/2 );', '{{WRAPPER}} .dce-view-exposed-form .elementor-field-type-submit' => 'padding: {{TOP}}{{UNIT}} calc( {{RIGHT}}{{UNIT}}/2 ) {{BOTTOM}}{{UNIT}} calc( {{LEFT}}{{UNIT}}/2 );', '{{WRAPPER}} .dce-view-exposed-form .dce-view-fields-wrapper' => 'margin-left: calc( -{{LEFT}}{{UNIT}}/2 ); margin-right: calc( -{{RIGHT}}{{UNIT}}/2 );']]);
        $this->add_responsive_control('dce_views_style_form_filters_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-view-form-wrapper .dce-view-field-filter' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('dce_views_style_form_filters_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form .dce-view-field-filter' => 'text-align: {{VALUE}};']]);
        // Field ----------------
        $this->add_control('heading_views_field', ['label' => __('Input Text & Select', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('dce_views_style_form_field_txcolor', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form .dce-view-input > input[type=text]' => 'color: {{VALUE}};', '{{WRAPPER}} .dce-view-exposed-form .dce-view-input > select' => 'color: {{VALUE}};']]);
        $this->add_control('dce_views_style_form_field_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form .dce-view-input > input[type=text]' => 'background-color: {{VALUE}};', '{{WRAPPER}} .dce-view-exposed-form .dce-view-input > select' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_views_style_form_field_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-exposed-form .dce-view-input > input[type=text], {{WRAPPER}} .dce-view-exposed-form .dce-view-input > select']);
        $this->add_responsive_control('dce_views_style_form_field_padding', ['label' => __('Input Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form .dce-view-input > input[type=text]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', '{{WRAPPER}} .dce-view-exposed-form .dce-view-input > select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('dce_views_style_form_field_margin', ['label' => __('Input Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-view-form-wrapper .dce-view-input' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_views_style_form_field_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-exposed-form .button']);
        $this->add_control('dce_views_style_form_field_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0, 'max' => 50]], 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form .dce-view-input > input[type=text]' => 'border-radius: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-view-exposed-form .dce-view-input > select' => 'border-radius: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'dce_views_style_form_field_box_shadow', 'selector' => '{{WRAPPER}} .dce-view-exposed-form .dce-view-input']);
        // Label ----------------
        $this->add_control('heading_label_field', ['label' => __('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('dce_views_style_form_label_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form label.dce-view-input-label' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_views_style_form_label_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-exposed-form label.dce-view-input-label']);
        $this->add_control('dce_views_style_form_label_block', ['label' => __('Block', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form label.dce-view-input-label' => 'display: block;']]);
        // Buttons ----------------
        $this->add_control('heading_views_buttons', ['label' => __('Buttons', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('buttons_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify']], 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form .dce-view-exposed-form-buttons' => 'text-align: {{VALUE}};'], 'render_type' => 'template']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'buttons_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-exposed-form .button']);
        $this->add_control('buttons_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0, 'max' => 50]], 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form .button' => 'border-radius: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('buttons_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('buttons_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form .button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'buttons_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-exposed-form .button']);
        $this->add_control('buttons_v_space', ['label' => __('Vertical Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['min' => -100, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form .button' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_control('buttons_h_space', ['label' => __('Horizontal Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form .button' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};']]);
        // Button Reset ----------------
        $this->add_control('heading_views_buttonReset', ['label' => __('Button Reset', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('buttonreset_txcolor', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form input.reset' => 'color: {{VALUE}};']]);
        $this->add_control('buttonreset_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form input.reset' => 'background-color: {{VALUE}};']]);
        $this->add_control('buttonreset_border_color', ['label' => __('Border color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form input.reset' => 'border-color: {{VALUE}};']]);
        $this->add_control('buttonreset_txcolor_hover', ['label' => __('Hover Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form input.reset:hover' => 'color: {{VALUE}};']]);
        $this->add_control('buttonreset_bgcolor_hover', ['label' => __('Hover Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form input.reset:hover' => 'background-color: {{VALUE}};']]);
        $this->add_control('buttonreset_border_color_hover', ['label' => __('Hover Border color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form input.reset:hover' => 'border-color: {{VALUE}};']]);
        // Button Find ----------------
        $this->add_control('heading_views_buttonFind', ['label' => __('Button Find', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('buttonfind_txcolor', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form input.find' => 'color: {{VALUE}};']]);
        $this->add_control('buttonfind_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form input.find' => 'background-color: {{VALUE}};']]);
        $this->add_control('buttonfind_border_color', ['label' => __('Border color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form input.find' => 'border-color: {{VALUE}};']]);
        $this->add_control('buttonfind_txcolor_hover', ['label' => __('Hover Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form input.find:hover' => 'color: {{VALUE}};']]);
        $this->add_control('buttonfind_bgcolor_hover', ['label' => __('Hover Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form input.find:hover' => 'background-color: {{VALUE}};']]);
        $this->add_control('buttonfind_border_color_hover', ['label' => __('Hover Border color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form input.reset:hover' => 'border-color: {{VALUE}};']]);
        $this->add_control('form_col_inner_width', ['label' => __('Col inner width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'default' => '100', 'selectors' => ['{{WRAPPER}} .dce-view-exposed-form .dce-view-form-col-inner' => 'width: {{VALUE}}%;']]);
        $this->end_controls_section();
        // EXPOSED FORM - ACTIVE FILTERS
        $this->start_controls_section('section_style_form_filters', ['label' => __('Exposed Form - Active Filters', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_views_where_form!' => ['', []], 'dce_views_where_form_active_filters!' => '']]);
        $this->add_control('dce_views_style_form_filter_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-view-form-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('dce_views_style_form_filter_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-view-form-filter' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('dce_views_style_form_filter_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify']], 'selectors' => ['{{WRAPPER}} .dce-view-form-filters-row' => 'text-align: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_views_style_form_filter_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-form-filter']);
        $this->add_control('dce_views_style_form_filter_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-form-filter' => 'color: {{VALUE}};']]);
        $this->add_control('dce_views_style_form_filter_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-form-filter' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_views_style_form_filter_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-form-filter']);
        $this->add_control('dce_views_style_form_no_filter_heading', ['label' => __('No Filters Message', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_views_style_form_no_filter_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-form-no-filter', 'condition' => ['dce_views_where_form_active_filters_no_message!' => '']]);
        $this->add_control('dce_views_style_form_no_filter_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-form-no-filter' => 'color: {{VALUE}};'], 'condition' => ['dce_views_where_form_active_filters_no_message!' => '']]);
        $this->end_controls_section();
        // GROUP BY
        $this->start_controls_section('section_style_group_by', ['label' => __('Group By', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_views_group_by_field!' => '']]);
        $this->add_control('heading_views_group_by_title', ['label' => __('Heading', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING]);
        $this->add_control('dce_views_style_group_by_title_width', ['label' => __('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 100, 'unit' => '%'], 'size_units' => ['%'], 'range' => ['%' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-views-group-title, {{WRAPPER}} .elementor-widget-accordion' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('dce_views_style_group_by_title_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} .dce-views-group-title' => 'text-align: {{VALUE}};', '{{WRAPPER}} .elementor-tab-title a' => 'display: block; text-align: {{VALUE}};']]);
        $this->add_control('dce_views_style_group_by_title_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-views-group-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('dce_views_style_group_by_title_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-views-group-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_views_style_group_by_title_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-views-group-title']);
        $this->add_control('dce_views_style_group_by_title_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-views-group-title' => 'color: {{VALUE}};', '{{WRAPPER}} .elementor-tab-title a' => 'color: {{VALUE}};', '{{WRAPPER}} .elementor-accordion-icon' => 'color: {{VALUE}};']]);
        $this->add_control('dce_views_style_group_by_title_active_color', ['label' => __('Active Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-tab-title.elementor-active a' => 'color: {{VALUE}};', '{{WRAPPER}} .elementor-tab-title.elementor-active .elementor-accordion-icon' => 'color: {{VALUE}};'], 'condition' => ['dce_views_group_by_accordion!' => '']]);
        $this->add_control('dce_views_style_group_by_title_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-views-group-title' => 'background-color: {{VALUE}};', '{{WRAPPER}} .elementor-tab-title' => 'background-color: {{VALUE}};']]);
        $this->add_control('dce_views_style_group_by_title_active_bgcolor', ['label' => __('Active Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-tab-title.elementor-active' => 'background-color: {{VALUE}};'], 'condition' => ['dce_views_group_by_accordion!' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_views_style_group_by_title_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-views-group-title']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'dce_views_style_group_by_title_text_shadow', 'selector' => '{{WRAPPER}} .dce-views-group-title']);
        $this->add_control('heading_views_group_by_content', ['label' => __('Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('dce_views_style_group_by_content_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .elementor-tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('dce_views_style_group_by_content_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-tab-content' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_views_style_group_by_content_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .elementor-tab-content']);
        $this->end_controls_section();
        // EXPOSED SORT
        $this->start_controls_section('section_style_sort', ['label' => __('Exposed Sort', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('dce_views_style_sort_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-view-exposed-sort' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('dce_views_style_sort_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-sort' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('dce_views_style_sort_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} .dce-view-exposed-sort' => 'text-align: {{VALUE}};', '{{WRAPPER}} .dce-view-exposed-sort select' => 'text-align: {{VALUE}};']]);
        // Border ----------------
        $this->add_control('heading_views_sort_border', ['label' => __('Border', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_views_style_sort_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-exposed-sort']);
        $this->add_control('dce_views_style_sort_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-view-exposed-sort' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        // Background ----------------
        $this->add_control('dce_views_h_style_sort_bg', ['label' => __('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'dce_views_style_sort_bg', 'selector' => '{{WRAPPER}} .dce-view-exposed-sort']);
        // Label ----------------
        $this->add_control('heading_sort_label_field', ['label' => __('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('dce_views_style_sort_label_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-sort label' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_views_style_sort_label_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-exposed-sort label']);
        $this->add_control('dce_views_style_sort_label_display', ['label' => __('Display', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['inline' => __('Inline', 'dynamic-content-for-elementor'), 'block' => __('Block', 'dynamic-content-for-elementor')], 'default' => 'inline', 'selectors' => ['{{WRAPPER}} .dce-view-exposed-sort label' => 'display: {{VALUE}};']]);
        $this->add_control('heading_views_sort_field', ['label' => __('Select', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('dce_views_style_sort_field_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-view-exposed-sort .dce-input-sort' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('dce_views_style_sort_field_txcolor', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-sort .dce-input-sort' => 'color: {{VALUE}};']]);
        $this->add_control('dce_views_style_sort_field_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-exposed-sort .dce-input-sort' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_views_style_sort_field_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-exposed-sort .dce-input-sort']);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_views_style_sort_field_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-exposed-sort .dce-input-sort']);
        $this->add_control('dce_views_style_sort_field_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0, 'max' => 50]], 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-view-exposed-sort .dce-input-sort' => 'border-radius: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        // PAGINATION
        $this->start_controls_section('section_style_pagination', ['label' => __('Pagination', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_views_pagination!' => '']]);
        $this->add_control('pagination_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => 'center', 'selectors' => ['{{WRAPPER}} .elementor-pagination' => 'text-align: {{VALUE}};', '{{WRAPPER}} .elementor-pagination ul' => 'padding-left: 0;']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'pagination_typography', 'selector' => '{{WRAPPER}} .elementor-pagination']);
        $this->add_control('pagination_color_heading', ['label' => __('Colors', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->start_controls_tabs('pagination_colors');
        $this->start_controls_tab('pagination_color_normal', ['label' => __('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('pagination_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-pagination .page-numbers:not(.dots)' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-pagination .page-numbers:not(.dots)' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_color_hover', ['label' => __('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('pagination_hover_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-pagination a.page-numbers:hover' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_hover_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-pagination a.page-numbers:hover' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_color_active', ['label' => __('Active', 'dynamic-content-for-elementor')]);
        $this->add_control('pagination_active_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-pagination .page-numbers.current' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_active_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-pagination .page-numbers.current' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_responsive_control('pagination_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .elementor-pagination .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('pagination_spacing', ['label' => __('Space Between', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'separator' => 'before', 'default' => ['size' => 10], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['body:not(.rtl) {{WRAPPER}} .elementor-pagination li .page-numbers' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 ); margin-right: calc( {{SIZE}}{{UNIT}}/2 );', 'body.rtl {{WRAPPER}} .elementor-pagination li .page-numbers' => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 ); margin-left: calc( {{SIZE}}{{UNIT}}/2 );']]);
        $this->end_controls_section();
        $this->start_controls_section('dce_views_style_scroll_btn_heading', ['label' => __('Load more Button', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_views_pagination!' => '', 'dce_views_pagination_type' => ['infinite_scroll'], 'dce_views_limit_scroll_button!' => '']]);
        $this->add_responsive_control('dce_views_style_scroll_btn_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify']], 'default' => '']);
        $this->add_control('dce_views_style_scroll_btn_size', ['label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'sm', 'options' => Helper::get_button_sizes(), 'style_transfer' => \true]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_views_style_scroll_btn_typography', 'selector' => '{{WRAPPER}} .elementor-button.view-more-button']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'dce_views_style_scroll_btn_text_shadow', 'selector' => '{{WRAPPER}} .elementor-button.view-more-button']);
        $this->start_controls_tabs('dce_views_style_scroll_btn_tabs_button_style');
        $this->start_controls_tab('dce_views_style_scroll_btn_tab_button_normal', ['label' => __('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_style_scroll_btn_text_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .elementor-button.view-more-button' => 'fill: {{VALUE}}; color: {{VALUE}};']]);
        $this->add_control('dce_views_style_scroll_btn_background_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button.view-more-button' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('dce_views_style_scroll_btn_tab_button_hover', ['label' => __('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_views_style_scroll_btn_hover_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button.view-more-button:hover, {{WRAPPER}} .elementor-button.view-more-button:focus' => 'color: {{VALUE}};', '{{WRAPPER}} .elementor-button.view-more-button:hover svg, {{WRAPPER}} .elementor-button.view-more-button:focus svg' => 'fill: {{VALUE}};']]);
        $this->add_control('dce_views_style_scroll_btn_background_hover_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button.view-more-button:hover, {{WRAPPER}} .elementor-button.view-more-button:focus' => 'background-color: {{VALUE}};']]);
        $this->add_control('dce_views_style_scroll_btn_hover_border_color', ['label' => __('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['dce_views_style_scroll_btn_border_border!' => ''], 'selectors' => ['{{WRAPPER}} .elementor-button.view-more-button:hover, {{WRAPPER}} .elementor-button.view-more-button:focus' => 'border-color: {{VALUE}};']]);
        $this->add_control('dce_views_style_scroll_btn_hover_animation', ['label' => __('Hover Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_views_style_scroll_btn_border', 'selector' => '{{WRAPPER}} .elementor-button.view-more-button', 'separator' => 'before']);
        $this->add_control('dce_views_style_scroll_btn_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .elementor-button.view-more-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'dce_views_style_scroll_btn_button_box_shadow', 'selector' => '{{WRAPPER}} .elementor-button.view-more-button']);
        $this->add_responsive_control('dce_views_style_scroll_btn_text_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .elementor-button.view-more-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'before']);
        $this->add_responsive_control('dce_views_style_scroll_btn_text_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .elementor-button.view-more-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();
        // ADVANCED
        $this->start_controls_section('section_style_advanced', ['label' => __('Special effects', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('dce_views_style_entrance_animation', ['label' => __('Entrance Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ANIMATION, 'prefix_class' => 'animated ']);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings) || empty($settings['dce_views_object'])) {
            return;
        }
        $this->_exposed_form();
        $this->_exposed_sort();
        if ($settings['dce_views_where_form_result'] || empty($settings['dce_views_where_form']) || isset($_GET['eid'])) {
            $this->_loop();
        } elseif ($settings['dce_views_where_form_ajax']) {
            echo '<div class="dce-view-results-wrapper"><div class="dce-view-results dce-view-results-ajax"></div></div>';
        }
        $this->_ajax();
    }
    public function _loop($settings = null)
    {
        if (!$settings) {
            $settings = $this->get_settings_for_display();
            // not parsed because token need to be valued on loop
        }
        global $wpdb, $post, $user, $authordata, $current_user, $wp_query, $term;
        global $dce_obj;
        $original_post = $post;
        $original_user = $user;
        $original_current_user = $current_user;
        $original_authordata = $authordata;
        $original_queried_object = $wp_query->queried_object;
        $original_queried_object_id = $wp_query->queried_object_id;
        $original_current_post = $wp_query->current_post;
        $original_current_index = $wp_query->index;
        $original_loop = $wp_query->in_the_loop;
        $wrapper_class = 'dce-view-' . $settings['dce_views_style_format'] . ' ' . $settings['dce_views_style_wrapper_class'];
        $element_class = 'dce-view-' . $settings['dce_views_style_format'] . '-element ' . (!empty($settings['dce_views_style_entrance_animation']) ? 'animated-' . $settings['dce_views_style_entrance_animation'] : '') . ' ' . $settings['dce_views_style_element_class'];
        $args = $this->get_wp_query_args();
        $args = $this->set_exposed_form_args($args);
        $this->add_filters();
        $args = apply_filters("elementor/query/before/" . $this->get_id(), $args);
        // The Query
        switch ($settings['dce_views_object']) {
            case 'post':
                add_action('pre_get_posts', [$this, 'pre_get_posts_query_filter']);
                $this->the_query = $the_query = new \WP_Query($args);
                remove_action('pre_get_posts', [$this, 'pre_get_posts_query_filter']);
                $objects = $the_query->query($args);
                break;
            case 'user':
                $this->the_query = $the_query = $user_query = new \WP_User_Query($args);
                $objects = $the_query->get_results();
                break;
            case 'term':
                $this->the_query = $the_query = $term_query = new \WP_Term_Query($args);
                $objects = $the_query->get_terms();
                break;
        }
        if (!empty($the_query)) {
            do_action("elementor/query/after/" . $this->get_id(), $the_query);
        }
        $this->remove_filters();
        // TOTAL
        $total_objects = 0;
        if (!empty($objects) && !empty($the_query)) {
            switch ($settings['dce_views_object']) {
                case 'post':
                    $total_objects = $the_query->found_posts;
                    break;
                case 'user':
                    $total_objects = $the_query->get_total();
                    break;
                case 'term':
                    $total_objects = \count($objects);
                    if (isset($args['number'])) {
                        unset($args['number']);
                        $term_query_totals = new \WP_Term_Query($args);
                        $total_objects = \count($term_query_totals->get_terms());
                    }
                    break;
            }
        }
        $the_query->total = $total_objects;
        // The Loop
        if (!empty($objects)) {
            // Now wipe it out completely
            $wp_query->in_the_loop = $settings['dce_views_object'] != 'term';
            echo '<div class="dce-view-results-wrapper"><div class="dce-view-results">';
            $this->_count($the_query, $settings, 'top');
            switch ($settings['dce_views_style_format']) {
                case 'table':
                    echo '<table class="' . $wrapper_class . (isset($settings['dce_views_style_table_data']) && !empty($settings['dce_views_style_table_data']) ? ' dce-datatable' : '') . '">';
                    if ($settings['dce_views_select_type'] == 'fields') {
                        echo '<thead><tr>';
                        foreach ($settings['dce_views_select_fields'] as $key => $afield) {
                            echo '<th class="dce-view-field-th ' . $afield['dce_views_select_class_label'] . '">';
                            if ($afield['dce_views_select_description']) {
                                echo '<abbr title="' . $afield['dce_views_select_description'] . '">';
                            }
                            echo !empty($afield['dce_views_select_label']) ? $afield['dce_views_select_label'] : $afield['dce_views_select_field'];
                            if ($afield['dce_views_select_description']) {
                                echo '</abbr>';
                            }
                            echo '</th>';
                        }
                        echo '</tr></thead>';
                    }
                    echo '<tbody class="dce-view-archive">';
                    break;
                case 'list':
                    echo '<' . $settings['dce_views_style_list'] . ' class="' . $wrapper_class . ' dce-view-archive">';
                    break;
                case 'slideshow':
                    $prev = 'left';
                    $next = 'right';
                    $direction = 'ltr';
                    if (is_rtl()) {
                        $prev = 'right';
                        $next = 'left';
                        $direction = 'rtl';
                    }
                    $swiper_class = \Elementor\Plugin::$instance->experiments->is_feature_active('e_swiper_latest') ? 'swiper' : 'swiper-container';
                    echo '<div class="elementor-swiper ' . $wrapper_class . '">';
                    echo '<div class="elementor-slides-wrapper elementor-main-swiper ' . $swiper_class . ' swiper-container-autoheight" dir="' . $direction . '">';
                    echo '<div class="swiper-wrapper elementor-slides dce-view-archive">';
                    break;
                case 'grid':
                default:
                    echo '<div class="dce-view-row elementor-row ' . $wrapper_class . ' dce-flex dce-view-archive">';
            }
            $dce_views_limit_to = $settings['dce_views_limit_to'] ? $settings['dce_views_limit_to'] : 0;
            if ($settings['dce_views_limit_offset'] && $settings['dce_views_limit_offset'] > 0) {
                $dce_views_limit_to += $settings['dce_views_limit_offset'];
            }
            if (empty($settings['dce_views_group_by_field'])) {
                if (!empty($settings['dce_views_order_random'])) {
                    switch ($settings['dce_views_object']) {
                        case 'post':
                            // random by WP_Query
                            break;
                        case 'user':
                        case 'term':
                            \shuffle($objects);
                            break;
                    }
                }
            }
            $k = 0;
            $group_value_prev = \false;
            $prev_dce_obj_id = 0;
            $term_id = -1;
            if (!empty($settings['dce_views_group_by_field'])) {
                $taxonomy = \false;
                if (\substr($settings['dce_views_group_by_field'], 0, 9) == 'taxonomy_') {
                    $taxonomy = \substr($settings['dce_views_group_by_field'], 9);
                }
                if ($taxonomy) {
                    $objects_by_terms = array();
                    if ($settings['dce_views_object'] == 'post') {
                        $taxonomies_terms = Helper::get_taxonomy_terms($taxonomy, \false, '', \true, $settings['dce_views_group_by_field_orderby'], $settings['dce_views_group_by_field_order']);
                        $taxonomies_terms_ids = array();
                        $objects_terms_ids = array();
                        foreach ($objects as $key => $dce_obj) {
                            $obj_terms = get_the_terms($dce_obj->ID, $taxonomy);
                            $term_ids = array();
                            if (!empty($obj_terms)) {
                                if (\is_object($obj_terms)) {
                                    $obj_terms = array();
                                } else {
                                    foreach ($obj_terms as $aterm) {
                                        if (\is_object($aterm) && \get_class($aterm) == 'WP_Term') {
                                            $term_ids[$aterm->term_id] = $aterm->term_id;
                                        }
                                    }
                                }
                            }
                            $objects_terms_ids[$dce_obj->ID] = $term_ids;
                        }
                        foreach ($taxonomies_terms as $akey => $avalue) {
                            if ($akey) {
                                foreach ($objects as $key => $dce_obj) {
                                    if (\in_array($akey, $objects_terms_ids[$dce_obj->ID])) {
                                        if (empty($objects_by_terms[$akey]) || !\in_array($dce_obj, $objects_by_terms[$akey])) {
                                            $objects_by_terms[$akey]['post_' . $dce_obj->ID] = $dce_obj;
                                        }
                                        if (!\in_array($akey, $taxonomies_terms_ids)) {
                                            $taxonomies_terms_ids[] = $akey;
                                        }
                                    }
                                }
                            }
                        }
                        $objects = array();
                        if (!empty($objects_by_terms)) {
                            foreach ($objects_by_terms as $aterm) {
                                if (!empty($aterm)) {
                                    foreach ($aterm as $dce_obj) {
                                        $objects[] = $dce_obj;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($settings['dce_views_group_by_accordion']) {
                    echo '<div class="elementor-widget-accordion"><div class="elementor-accordion" role="tablist">';
                }
            }
            $obj_count = $obj_display = 0;
            // Group by - Wrapper
            if (!empty($settings['dce_views_group_by_class_wrapper'])) {
                $this->add_render_attribute('group-by-wrapper', 'class', ['group-by-wrapper', $settings['dce_views_group_by_class_wrapper']]);
            } else {
                $this->add_render_attribute('group-by-wrapper', 'class', ['group-by-wrapper']);
            }
            foreach ($objects as $key => $dce_obj) {
                $obj_count++;
                if ($settings['dce_views_limit_to'] && $k >= $dce_views_limit_to) {
                    break;
                }
                if ($settings['dce_views_limit_offset'] > $k && !$settings['dce_views_pagination']) {
                    $k++;
                    continue;
                }
                if ($settings['dce_views_limit_offset'] > $k && $settings['dce_views_pagination'] && $this->get_current_page() == 1 && $settings['dce_views_limit_offset_pagination']) {
                    $k++;
                    continue;
                }
                $obj_display++;
                $page = $this->get_current_page();
                $obj_index = $obj_display + ($page - 1) * \intval($settings['dce_views_post_per_page']);
                switch ($settings['dce_views_object']) {
                    case 'post':
                        $object_id = $dce_obj;
                        if (empty($settings['dce_views_select_template_ajax'])) {
                            $post = $wp_query->queried_object = $dce_obj;
                            //get_post();
                            $object_id = $wp_query->queried_object_id = $post->ID;
                            $authordata = get_userdata($post->post_author);
                        } else {
                            $object_id = $dce_obj;
                        }
                        break;
                    case 'user':
                        $current_user = $user = $authordata = $wp_query->queried_object = $dce_obj;
                        $object_id = $wp_query->queried_object_id = $authordata->ID;
                        break;
                    case 'term':
                        $term = $wp_query->queried_object = $dce_obj;
                        $object_id = $wp_query->queried_object_id = $dce_obj->term_id;
                        break;
                }
                $wp_query->current_post = $obj_display;
                $wp_query->index = $obj_index;
                if (!empty($settings['dce_views_group_by_field'])) {
                    $term_permalink = \false;
                    if ($settings['dce_views_object'] == 'post' && $taxonomy) {
                        if (!\in_array($term_id, $objects_terms_ids[$object_id]) || empty($objects_by_terms[$term_id])) {
                            $term_id = \array_shift($taxonomies_terms_ids);
                        }
                        $group_value = $term_tmp = get_term_by('term_id', $term_id, $taxonomy);
                        unset($objects_by_terms[$term_id]['post_' . $object_id]);
                        $term_permalink = get_term_link($group_value);
                        if (is_wp_error($term_permalink)) {
                            $term_permalink = \false;
                        }
                    } else {
                        $get_meta = 'get_' . $settings['dce_views_object'] . '_value';
                        echo '<pre>';
                        echo '</pre>';
                        $group_value = Helper::$get_meta($object_id, $settings['dce_views_group_by_field'], \true);
                    }
                    $group_value = Tokens::replace_var_tokens($settings['dce_views_group_by_field_heading'], 'TITLE', $group_value);
                    if ($term_permalink) {
                        $group_value = \str_replace('[PERMALINK]', $term_permalink, $group_value);
                    }
                    if (!empty($settings['dce_views_group_by_field_heading_show'])) {
                        if ($group_value != $group_value_prev) {
                            if ($settings['dce_views_group_by_accordion']) {
                                $element_active = '';
                                switch ($settings['dce_views_group_by_accordion_start']) {
                                    case 'first':
                                        if (!$group_value_prev) {
                                            $element_active = ' elementor-active';
                                        }
                                        break;
                                    case 'all':
                                        $element_active = ' elementor-active';
                                        break;
                                }
                                if ($group_value_prev) {
                                    echo '</div></div>';
                                }
                                echo '<div class="elementor-accordion-item ' . $settings['dce_views_group_by_class_wrapper'] . '"><' . $settings['dce_views_group_by_heading_size'] . ' id="elementor-tab-title-' . $object_id . ' ' . $settings['dce_views_group_by_class_heading'] . '" class="dce-views-group-title elementor-tab-title' . $element_active . '" data-tab="' . $object_id . '" role="tab" aria-controls="elementor-tab-content-' . $object_id . '"  onclick="jQuery(this).toggleClass(\'elementor-active\'); jQuery(this).next().slideToggle().toggleClass(\'elementor-active\'); return false;"><span class="elementor-accordion-icon elementor-accordion-icon-left" aria-hidden="true"><span class="elementor-accordion-icon-closed"><i class="fas fa-plus"></i></span><span class="elementor-accordion-icon-opened"><i class="fas fa-minus"></i></span></span><a class="elementor-accordion-title" href="#" onclick="return false;"> ';
                            } else {
                                if ($group_value_prev) {
                                    echo '</div>';
                                }
                                ?>
								<div <?php 
                                echo $this->get_render_attribute_string('group-by-wrapper');
                                ?> > <?php 
                                echo '<' . $settings['dce_views_group_by_heading_size'] . ' class="dce-views-group-title ' . $settings['dce_views_group_by_class_heading'] . '">';
                            }
                            echo $group_value;
                            if ($settings['dce_views_group_by_accordion']) {
                                switch ($settings['dce_views_group_by_accordion_display']) {
                                    case 'flex':
                                        $accordion_style = 'display: flex !important; flex-wrap: wrap !important;';
                                        break;
                                    default:
                                        $accordion_style = 'display: block;';
                                        break;
                                }
                                echo '</a></' . $settings['dce_views_group_by_heading_size'] . '><div id="elementor-tab-content-' . $object_id . '" class="elementor-tab-content elementor-clearfix' . $element_active . '" data-tab="' . $object_id . '" role="tabpanel" aria-labelledby="elementor-tab-title-' . $object_id . '"' . ($element_active ? ' style="' . $accordion_style . '"' : '') . '>';
                            } else {
                                echo '</' . $settings['dce_views_group_by_heading_size'] . '>';
                            }
                        }
                    }
                    $group_value_prev = $group_value;
                }
                $prev_dce_obj_id = $object_id;
                $element_class_obj = ' dce-view-element dce-view-element-' . $object_id;
                $responsive_cols = ' elementor-column elementor-col-' . $settings['dce_views_style_col_width'];
                if (!empty($settings['dce_views_style_col_width_tablet'])) {
                    $responsive_cols .= ' elementor-md-' . $settings['dce_views_style_col_width_tablet'];
                }
                if (!empty($settings['dce_views_style_col_width_mobile'])) {
                    $responsive_cols .= ' elementor-sm-' . $settings['dce_views_style_col_width_mobile'];
                }
                $row_html = '';
                switch ($settings['dce_views_select_type']) {
                    case 'fields':
                        switch ($settings['dce_views_style_format']) {
                            case 'table':
                                echo '<tr class="' . $element_class . $element_class_obj . ' dce-view-single-wrapper dce-view-single">';
                                foreach ($settings['dce_views_select_fields'] as $key => $afield) {
                                    echo '<td class="dce-view-field-' . $afield['dce_views_select_field'] . ' ' . $afield['dce_views_select_class_wrapper'] . '"><div class="dce-view-field-value ' . $afield['dce_views_select_class_value'] . '">' . $this->get_field_value($dce_obj, $object_id, $afield, $settings) . '</div></td>';
                                }
                                echo '</tr>';
                                break;
                            case 'list':
                                echo '<li class="' . $element_class . $element_class_obj . ' dce-view-single-wrapper dce-view-single">';
                                $this->_fields($settings, $dce_obj, $object_id);
                                echo '</li>';
                                break;
                            case 'slideshow':
                                echo '<div class="elementor-repeater-item-' . $object_id . ' swiper-slide dce-view-single-wrapper dce-view-single"><div class="elementor-swiper-slide-inner dce-swiper-slide-inner ' . $element_class . $element_class_obj . '"><div class="swiper-slide-contents">';
                                $this->_fields($settings, $dce_obj, $object_id);
                                echo '</div></div></div>';
                                break;
                            case 'grid':
                            default:
                                echo '<div class="dce-view-col dce-view-single-wrapper ' . $element_class . $element_class_obj . $responsive_cols . '"><div class="elementor-column-wrap elementor-element-populated dce-view-single"><div class="elementor-widget-wrap dce-block">';
                                foreach ($settings['dce_views_select_fields'] as $key => $afield) {
                                    echo '<div class="dce-view-field-' . $afield['dce_views_select_field'] . ' ' . $afield['dce_views_select_class_wrapper'] . '">';
                                    if ($afield['dce_views_select_label']) {
                                        if ($afield['dce_views_select_label_inline']) {
                                            echo '<label';
                                        } else {
                                            echo '<div';
                                        }
                                        echo ' class="dce-view-field-label ' . $afield['dce_views_select_class_label'] . '">' . $afield['dce_views_select_label'];
                                        if ($afield['dce_views_select_label_inline']) {
                                            echo '</label>';
                                        } else {
                                            echo '</div>';
                                        }
                                    }
                                    if ($afield['dce_views_select_label'] && $afield['dce_views_select_label_inline']) {
                                        echo '<span';
                                    } else {
                                        echo '<div';
                                    }
                                    echo ' class="dce-view-field-value ' . $afield['dce_views_select_class_value'] . '">' . $this->get_field_value($dce_obj, $object_id, $afield, $settings) . '</div>';
                                    if ($afield['dce_views_select_label'] && $afield['dce_views_select_label_inline']) {
                                        echo '</span>';
                                    } else {
                                        echo '</div>';
                                    }
                                }
                                echo '</div></div></div>';
                        }
                        break;
                    case 'template':
                        $tmpl_opt = '';
                        switch ($settings['dce_views_object']) {
                            case 'post':
                                $tmpl_opt = ' post_id="' . $object_id . '"';
                                break;
                            case 'user':
                                $tmpl_opt = ' author_id="' . $object_id . '" user_id="' . $object_id . '"';
                                break;
                            case 'term':
                                $tmpl_opt = ' term_id="' . $object_id . '"';
                                break;
                        }
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $inlinecss = ' inlinecss="true"';
                        } else {
                            $inlinecss = '';
                        }
                        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            if ($settings['dce_views_select_template_ajax'] && $obj_display > 1) {
                                $tmpl_opt .= ' loading="lazy"';
                            }
                        }
                        $template_shortcode = '[dce-elementor-template id="' . $settings['dce_views_select_template'] . '"' . $tmpl_opt . $inlinecss . ']';
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode() && $settings['dce_views_select_template_ajax'] && $obj_display > 1) {
                            if ($obj_display == 2) {
                                $row_html = $this->_get_placeholder($row_html, $settings, \false);
                                if ($settings['dce_views_select_template_ajax_placeholder'] == 'clone') {
                                    $row_html = '<div class="dce-elementor-template-placeholder dce-elementor-template-placeholder-clone">' . $row_html . '</div>';
                                }
                                if ($settings['dce_views_select_template_ajax_placeholder'] == 'fadein') {
                                    $row_html = '<div class="dce-elementor-template-placeholder style="opacity: 0.1;">' . $row_html . '</div>';
                                }
                                if ($settings['dce_views_select_template_ajax_placeholder'] == 'image' || $settings['dce_views_select_template_ajax_placeholder'] == 'text') {
                                    $row_html = '<div class="dce-elementor-template-placeholder">' . $row_html . '</div>';
                                }
                            }
                        } else {
                            $row_html = do_shortcode($template_shortcode);
                        }
                        break;
                    case 'text':
                        $field_value = $settings['dce_views_select_text'];
                        if ($settings['dce_views_object'] == 'user') {
                            $field_value = Tokens::user_to_author($field_value);
                        }
                        $row_html = Helper::get_dynamic_value($field_value);
                        break;
                }
                $row_html = apply_filters('dynamicooo/views/row-html', $row_html);
                if (\in_array($settings['dce_views_select_type'], array('text', 'template'))) {
                    switch ($settings['dce_views_style_format']) {
                        case 'grid':
                            echo '<div class="item-page dce-view-col dce-view-single-wrapper ' . $element_class . $element_class_obj . $responsive_cols . '"><div class="elementor-column-wrap elementor-element-populated dce-view-single"><div class="elementor-widget-wrap dce-block">';
                            echo $row_html;
                            echo '</div></div></div>';
                            break;
                        case 'slideshow':
                            echo '<div class="elementor-repeater-item-' . $object_id . ' swiper-slide dce-view-single-wrapper dce-view-single"><div class="elementor-swiper-slide-inner dce-swiper-slide-inner ' . $element_class . $element_class_obj . '"><div class="swiper-slide-contents">';
                            echo $row_html;
                            echo '</div></div></div>';
                            break;
                        case 'list':
                            echo '<li class="dce-views-render-' . $settings['dce_views_style_format'] . ' ' . $element_class . $element_class_obj . ' dce-view-single-wrapper dce-view-single">';
                            echo $row_html;
                            echo '</li>';
                            break;
                        case 'table':
                            echo '<tr class="dce-view-single-wrapper dce-views-render-' . $settings['dce_views_style_format'] . ' ' . $element_class . $element_class_obj . '"><td class="dce-view-single">';
                            echo $row_html;
                            echo '</td></tr>';
                            break;
                        default:
                            echo '<div class="dce-views-render-' . $settings['dce_views_style_format'] . ' ' . $element_class . $element_class_obj . ' dce-view-single-wrapper dce-view-single">';
                            echo $row_html;
                            echo '</div>';
                    }
                }
                $k++;
            }
            if (!empty($settings['dce_views_group_by_field']) && $settings['dce_views_group_by_accordion']) {
                echo '</div></div></div></div>';
            }
            switch ($settings['dce_views_style_format']) {
                case 'table':
                    echo '</tbody></table>';
                    if (isset($settings['dce_views_style_table_data']) && $settings['dce_views_style_table_data']) {
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
                        if ($settings['dce_views_style_table_data_autofill']) {
                            ?>autoFill: true,<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_views_style_table_data_autofill']) {
                            ?>autoFill: true,<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_views_style_table_data_buttons']) {
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
                        if ($settings['dce_views_style_table_data_colreorder']) {
                            ?>colReorder: true,<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_views_style_table_data_fixedcolumns']) {
                            ?>fixedColumns: true,<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_views_style_table_data_fixedheader']) {
                            ?>fixedHeader: true,<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_views_style_table_data_keytable']) {
                            ?>keys: true,<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_views_style_table_data_responsive']) {
                            ?>responsive: true,<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_views_style_table_data_rowgroup']) {
                            ?>rowGroup: {
									dataSrc: 'group'
									},<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_views_style_table_data_rowreorder']) {
                            ?>rowReorder: true,<?php 
                        }
                        ?>
							<?php 
                        if ($settings['dce_views_style_table_data_scroller']) {
                            ?>scroller: true,
											scrollX: true,
								<?php 
                            if (!empty($settings['dce_views_style_table_data_scroller_y'])) {
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
                        if ($settings['dce_views_style_table_data_select']) {
                            ?>select: true,<?php 
                        }
                        ?>

									ordering: true,
									});
									});</script>
							<?php 
                    }
                    break;
                case 'list':
                    echo '</ul>';
                    break;
                case 'slideshow':
                    echo '</div>';
                    if ($obj_display > 1) {
                        if (\in_array($settings['navigation'], ['dots', 'both'])) {
                            echo '<div class="swiper-pagination"></div>';
                        }
                        if (\in_array($settings['navigation'], ['arrows', 'both'])) {
                            ?>
							<div class="elementor-swiper-button elementor-swiper-button-prev">
								<i class="eicon-chevron-<?php 
                            echo $prev;
                            ?>" aria-hidden="true"></i>
								<span class="elementor-screen-only"><?php 
                            _e('Previous', 'dynamic-content-for-elementor');
                            ?></span>
							</div>
							<div class="elementor-swiper-button elementor-swiper-button-next">
								<i class="eicon-chevron-<?php 
                            echo $next;
                            ?>" aria-hidden="true"></i>
								<span class="elementor-screen-only"><?php 
                            _e('Next', 'dynamic-content-for-elementor');
                            ?></span>
							</div>
							<?php 
                        }
                    }
                    echo '</div></div>';
                    break;
                case 'grid':
                default:
                    echo '</div>';
            }
            $this->_count($the_query, $settings, 'bottom');
            echo '</div></div>';
            if (!empty($settings['dce_views_pagination'])) {
                $this->_nav($the_query, $settings, $total_objects);
            }
            $wp_query->in_the_loop = $original_loop;
            $wp_query->queried_object = $original_queried_object;
            $wp_query->queried_object_id = $original_queried_object_id;
            $wp_query->current_post = $original_current_post;
            $wp_query->index = $original_current_index;
            $post = $original_post;
            $user = $original_user;
            $current_user = $original_current_user;
            $authordata = $original_authordata;
            $term = null;
            if ($settings['dce_views_object'] == 'post') {
                /* Restore original Post Data */
                wp_reset_postdata();
            }
        } else {
            // no posts found
            if (isset($settings['dce_views_fallback']) && $settings['dce_views_fallback']) {
                echo '<div class="dce-view-results-wrapper"><div class="dce-view-results dce-view-results-fallback dce-views-no-results">';
                if (isset($settings['dce_views_fallback_type']) && $settings['dce_views_fallback_type'] == 'template') {
                    $fallback_content = '[dce-elementor-template id="' . $settings['dce_views_fallback_template'] . '"]';
                } else {
                    $fallback_content = $settings['dce_views_fallback_text'];
                }
                $fallback_content = Helper::get_dynamic_value($fallback_content);
                echo $fallback_content;
                echo '</div></div>';
            } else {
                if ($settings['dce_views_where_form_ajax']) {
                    echo '<div class="dce-view-results-wrapper"><div class="dce-view-results dce-view-results-ajax"></div></div>';
                }
            }
        }
        return \true;
    }
    public function _count($the_query, $settings, $position)
    {
        if (!empty($settings['dce_views_count']) && $settings['dce_views_count_position'] == $position || $settings['dce_views_count_position'] == 'both') {
            $query = (array) $the_query;
            $start = $end = 0;
            if ($query['total']) {
                $start = 1;
                $end = $query['total'];
            }
            $page = $this->get_current_page();
            if (!empty($settings['dce_views_post_per_page'])) {
                $start = $settings['dce_views_post_per_page'] * ($page - 1) + 1;
                $end = $start + $settings['dce_views_post_per_page'] - 1;
                if ($end > $query['total']) {
                    $end = $query['total'];
                }
            }
            $query['page'] = $page;
            $query['start'] = $start;
            $query['end'] = $end;
            $count = Helper::get_dynamic_value($settings['dce_views_count_text'], $query, 'QUERY');
            echo '<div class="dce-view-count">' . $count . '</div>';
        }
    }
    public function _exposed_sort($settings = null)
    {
        if (!$settings) {
            $settings = $this->get_settings_for_display();
        }
        $exposed = \false;
        if (!$settings['dce_views_order_random']) {
            if (isset($settings['dce_views_order_by']) && !empty($settings['dce_views_order_by'])) {
                $options = '';
                if (!empty($settings['dce_views_order_by_default'])) {
                    $options = '<option value="">' . $settings['dce_views_order_by_default'] . '</option>';
                }
                $i = 0;
                foreach ($settings['dce_views_order_by'] as $key => $asort) {
                    if (!$i) {
                        $order_field = $asort['dce_views_order_field'];
                        $order_sort = $asort['dce_views_order_field_sort'];
                        if (!empty($_GET['orderby'])) {
                            list($order_sort, $order_field) = \explode('__', sanitize_text_field($_GET['orderby']), 2);
                        }
                    }
                    if ($asort['dce_views_order_field_sort_exposed']) {
                        $exposed = \true;
                        $options .= '<option value="ASC__' . $asort['dce_views_order_field'] . '"' . ($asort['dce_views_order_field'] == $order_field && $order_sort == 'ASC' ? ' selected' : '') . '>' . $this->get_field_label($asort['dce_views_order_field'], $settings) . ' ASC</option>';
                        $options .= '<option value="DESC__' . $asort['dce_views_order_field'] . '"' . ($asort['dce_views_order_field'] == $order_field && $order_sort == 'DESC' ? ' selected' : '') . '>' . $this->get_field_label($asort['dce_views_order_field'], $settings) . ' DESC</option>';
                    }
                }
                if ($options && $exposed) {
                    $form_action = '';
                    if (isset($_GET['page_id'])) {
                        $form_action = '?page_id=' . sanitize_text_field($_GET['page_id']);
                    }
                    if (isset($_GET['p'])) {
                        $form_action = '?p=' . sanitize_text_field($_GET['p']);
                    }
                    ?>
					<div class="dce-view-exposed-sort-wrapper">
						<div class="dce-view-exposed-sort-row elementor-row">
							<div class="elementor-column elementor-col-<?php 
                    echo $settings['dce_views_order_form_width'];
                    ?>">
								<form action="<?php 
                    echo $form_action;
                    ?>" method="get" class="dce-view-exposed-sort <?php 
                    echo $settings['dce_views_order_class'];
                    ?>">
									<?php 
                    if (isset($_GET['page_id'])) {
                        ?>
										<input type="hidden" name="page_id" value="<?php 
                        echo sanitize_text_field($_GET['page_id']);
                        ?>">
									<?php 
                    }
                    ?>
									<?php 
                    if (isset($_GET['p'])) {
                        ?>
										<input type="hidden" name="p" value="<?php 
                        echo sanitize_text_field($_GET['p']);
                        ?>">
									<?php 
                    }
                    ?>
									<?php 
                    if ($settings['dce_views_order_label']) {
                        ?>
										<label for="order_<?php 
                        echo $this->get_id();
                        ?>">
											<?php 
                        echo $settings['dce_views_order_label'];
                        ?>
										</label>
									<?php 
                    }
                    ?>
									<select class="dce-input-sort" id="order_<?php 
                    echo $this->get_id();
                    ?>" name="orderby" onchange="jQuery(this).closest('form').submit();">
										<?php 
                    echo $options;
                    ?>
									</select>
									<?php 
                    if (!empty($_GET) && isset($_GET['eid']) && $_GET['eid'] == $this->get_id()) {
                        foreach ($_GET as $gkey => $gval) {
                            if ($gkey != 'eid' && $gkey != 'orderby') {
                                if (\is_array($gval)) {
                                    foreach ($gval as $agval) {
                                        echo '<input type="hidden" name="' . esc_attr($gkey) . '[]" value="' . esc_attr($agval) . '">';
                                    }
                                } else {
                                    echo '<input type="hidden" name="' . esc_attr($gkey) . '" value="' . esc_attr($gval) . '">';
                                }
                            }
                        }
                    }
                    ?>
									<input type="hidden" name="eid" value="<?php 
                    echo $this->get_id();
                    ?>">
									<?php 
                    if (isset($_GET['page_id'])) {
                        ?>
										<input type="hidden" name="page_id" value="<?php 
                        echo sanitize_text_field($_GET['page_id']);
                        ?>">
									<?php 
                    }
                    ?>
									<?php 
                    if (isset($_GET['p'])) {
                        ?>
										<input type="hidden" name="p" value="<?php 
                        echo sanitize_text_field($_GET['p']);
                        ?>">
									<?php 
                    }
                    ?>
								</form>
							</div>
						</div>
					</div>
					<?php 
                }
            }
        }
    }
    public function _exposed_form($settings = null)
    {
        if (!$settings) {
            $settings = $this->get_settings_for_display();
        }
        if (empty($this->taxonomies)) {
            $this->taxonomies = Helper::get_taxonomies();
        }
        if (isset($settings['dce_views_where_form']) && !empty($settings['dce_views_where_form'])) {
            $form_action = '';
            if (isset($_GET['page_id'])) {
                $form_action = '?page_id=' . sanitize_text_field($_GET['page_id']);
            }
            if (isset($_GET['p'])) {
                $form_action = '?p=' . sanitize_text_field($_GET['p']);
            }
            ?>
			<div class="dce-view-form-wrapper dce-view-exposed-form elementor-button-align-<?php 
            echo $settings['buttons_align'] == 'justify' ? 'stretch' : 'start';
            ?> <?php 
            echo $settings['dce_views_where_form_class_wrapper'];
            ?>">
				<?php 
            if ($settings['dce_views_style_form_text']) {
                ?>
					<<?php 
                echo $settings['dce_views_style_form_text_size'];
                ?> class="dce-views-form-title"><?php 
                echo $settings['dce_views_style_form_text'];
                ?></<?php 
                echo $settings['dce_views_style_form_text_size'];
                ?>>
				<?php 
            }
            ?>
				<form id="dce-view-form-<?php 
            echo $this->get_id();
            ?>" method="get" action="<?php 
            echo $form_action;
            ?>" class="elementor-view-fields-wrapper dce-view-form <?php 
            echo $settings['dce_views_where_form_class'];
            ?>">
					<div class="elementor-row elementor-form-fields-wrapper">
						<?php 
            foreach ($settings['dce_views_where_form'] as $key => $afield) {
                if (!$afield['dce_views_where_form_field']) {
                    continue;
                }
                $taxonomy = \false;
                if (\substr($afield['dce_views_where_form_field'], 0, 9) == 'taxonomy_') {
                    $taxonomy = \substr($afield['dce_views_where_form_field'], 9);
                }
                $auto_label = $taxonomy && !empty($this->taxonomies[$taxonomy]) ? $this->taxonomies[$taxonomy] : Helper::get_post_meta_name($afield['dce_views_where_form_field']);
                $filter_class = 'elementor-field-group elementor-field-group-' . $afield['dce_views_where_form_field'] . ' elementor-column elementor-col-' . $afield['dce_views_where_form_width'];
                if (!empty($afield['dce_views_where_form_width_tablet'])) {
                    $filter_class .= ' elementor-md-' . $afield['dce_views_where_form_width_tablet'];
                }
                if (!empty($afield['dce_views_where_form_width_mobile'])) {
                    $filter_class .= ' elementor-sm-' . $afield['dce_views_where_form_width_mobile'];
                }
                ?>
							<div class="dce-view-field-wrapper <?php 
                echo $filter_class;
                ?> <?php 
                echo $settings['dce_views_where_form_class_filter'];
                ?>">
								<div class="dce-view-field-filter dce-view-form-col-inner">
									<label class="elementor-field-label dce-view-input-label <?php 
                echo $afield['dce_views_where_form_class_label'];
                ?>" for="dce_view_<?php 
                echo $this->get_id() . '_' . $afield['dce_views_where_form_field'];
                ?>">
										<?php 
                echo isset($afield['dce_views_where_form_label']) && $afield['dce_views_where_form_label'] ? $afield['dce_views_where_form_label'] : $auto_label;
                ?>
										<?php 
                if ($afield['dce_views_where_form_required'] && $afield['dce_views_where_form_required_label'] != 'none') {
                    ?>
											<span class="dce-form-required">
												<?php 
                    if ($afield['dce_views_where_form_required_label'] == 'asterisk') {
                        ?>*<?php 
                    }
                    ?>
												<?php 
                    if ($afield['dce_views_where_form_required_label'] == 'text') {
                        echo $afield['dce_views_where_form_required_label_text'];
                    }
                    ?>
											</span>
										<?php 
                }
                ?>
									</label>
									<?php 
                $input_values = array();
                $presel = Helper::str_to_array(',', $afield['dce_views_where_form_preselect']);
                $dce_views_where_form_type = $this->get_field_type($afield['dce_views_where_form_field'], $afield['dce_views_where_form_type']);
                $input_values = $this->get_field_options($afield['dce_views_where_form_field'], $afield['dce_views_where_form_value'], $afield['dce_views_where_form_preselect'], $afield['dce_views_where_form_value_format'], $afield['dce_views_where_form_field_orderby'], $afield['dce_views_where_form_field_order']);
                if (!$afield['dce_views_where_form_required']) {
                    if ($dce_views_where_form_type == 'select') {
                        \array_unshift($input_values, array('key' => '', 'value' => $afield['dce_views_where_form_required_empty_label'], 'selected' => \false));
                    }
                }
                if (empty($input_values)) {
                    // TEXT FALLBACK
                    if ($afield['dce_views_where_form_type'] == 'auto') {
                        $afield['dce_views_where_form_type'] = 'text';
                    }
                }
                switch ($dce_views_where_form_type) {
                    case 'select':
                        ?>
											<span class="dce-view-input dce-view-select <?php 
                        echo $afield['dce_views_where_form_class_input'];
                        ?>">
												<select class="elementor-field elementor-field-textual elementor-size-<?php 
                        echo $settings['dce_views_input_size'];
                        ?>" name="<?php 
                        echo $afield['dce_views_where_form_field'];
                        if ($afield['dce_views_where_form_field_multiple']) {
                            ?>[]<?php 
                        }
                        ?>" id="dce_view_<?php 
                        echo $this->get_id() . '_' . $afield['dce_views_where_form_field'];
                        ?>"<?php 
                        if ($afield['dce_views_where_form_required']) {
                            ?> required<?php 
                        }
                        if ($afield['dce_views_where_form_field_multiple']) {
                            ?> multiple<?php 
                        }
                        ?>>
													<?php 
                        foreach ($input_values as $aopt) {
                            ?>
														<option value="<?php 
                            echo $aopt['key'];
                            ?>"<?php 
                            echo !empty($aopt['selected']) ? ' selected' : '';
                            ?>>
															<?php 
                            echo $aopt['value'];
                            ?>
														</option>
														<?php 
                        }
                        ?>
												</select>
											</span>
											<?php 
                        break;
                    case 'radio':
                        $html_tag = !empty($afield['dce_views_where_form_field_inline']) ? 'span' : 'div';
                        foreach ($input_values as $okey => $aopt) {
                            $checked = !empty($aopt['selected']) ? ' checked' : '';
                            ?>
												<<?php 
                            echo $html_tag;
                            ?> class="dce-view-input dce-view-radio <?php 
                            echo $afield['dce_views_where_form_class_input'];
                            ?>">
												<input type="radio" value="<?php 
                            echo $aopt['key'];
                            ?>" name="<?php 
                            echo $afield['dce_views_where_form_field'];
                            ?>" id="dce_view_<?php 
                            echo $this->get_id() . '_' . $afield['dce_views_where_form_field'] . '_' . $okey;
                            ?>"<?php 
                            echo $checked;
                            if ($afield['dce_views_where_form_required']) {
                                ?> required<?php 
                            }
                            ?>>
												<label for="dce_view_<?php 
                            echo $this->get_id() . '_' . $afield['dce_views_where_form_field'] . '_' . $okey;
                            ?>"><?php 
                            echo $aopt['value'];
                            ?></label>
												</<?php 
                            echo $html_tag;
                            ?>>
												<?php 
                        }
                        break;
                    case 'checkbox':
                        $html_tag = !empty($afield['dce_views_where_form_field_inline']) ? 'span' : 'div';
                        foreach ($input_values as $okey => $aopt) {
                            $checked = !empty($aopt['selected']) ? ' checked' : '';
                            ?>
												<<?php 
                            echo $html_tag;
                            ?> class="dce-view-input dce-view-checkbox <?php 
                            echo $afield['dce_views_where_form_class_input'];
                            ?>">
												<input type="checkbox" value="<?php 
                            echo $aopt['key'];
                            ?>" name="<?php 
                            echo $afield['dce_views_where_form_field'];
                            ?>[]" id="dce_view_<?php 
                            echo $this->get_id() . '_' . $afield['dce_views_where_form_field'] . '_' . $okey;
                            ?>"<?php 
                            echo $checked;
                            if ($afield['dce_views_where_form_required']) {
                                ?> required<?php 
                            }
                            ?>>
												<label for="dce_view_<?php 
                            echo $this->get_id() . '_' . $afield['dce_views_where_form_field'] . '_' . $okey;
                            ?>"><?php 
                            echo $aopt['value'];
                            ?></label>
												</<?php 
                            echo $html_tag;
                            ?>>
												<?php 
                        }
                        break;
                    case 'text':
                    default:
                        ?>
											<span class="dce-view-input dce-view-text <?php 
                        echo $afield['dce_views_where_form_class_input'];
                        ?>">
												<input class="elementor-field elementor-field-textual elementor-size-<?php 
                        echo $settings['dce_views_input_size'];
                        ?>" type="text" placeholder="<?php 
                        echo $afield['dce_views_where_form_placeholder'];
                        ?>" value="<?php 
                        echo isset($_GET[$afield['dce_views_where_form_field']]) ? sanitize_text_field($_GET[$afield['dce_views_where_form_field']]) : $afield['dce_views_where_form_preselect'];
                        ?>" name="<?php 
                        echo $afield['dce_views_where_form_field'];
                        ?>" id="dce_view_<?php 
                        echo $this->get_id() . '_' . $afield['dce_views_where_form_field'];
                        ?>"<?php 
                        if ($afield['dce_views_where_form_required']) {
                            ?> required<?php 
                        }
                        ?>>
											</span>
											<?php 
                }
                ?>
									<?php 
                if ($afield['dce_views_where_form_hint']) {
                    ?>
										<small class="dce-view-input-hint"><i class="fa fa-info" aria-hidden="true"></i> <?php 
                    echo $afield['dce_views_where_form_hint'];
                    ?></small>
									<?php 
                }
                ?>
								</div>
							</div>
							<?php 
            }
            if (!$settings['dce_views_where_form_ajax_nobutton'] || $settings['dce_views_where_form_reset']) {
                $action_class = 'elementor-field-group elementor-column elementor-col-' . $settings['dce_views_where_form_action_width'];
                if (!empty($settings['dce_views_where_form_action_width_tablet'])) {
                    $action_class .= ' elementor-md-' . $settings['dce_views_where_form_action_width_tablet'];
                }
                if (!empty($settings['dce_views_where_form_action_width_mobile'])) {
                    $action_class .= ' elementor-sm-' . $settings['dce_views_where_form_action_width_mobile'];
                }
                ?>
							<div class="dce-view-exposed-form-action <?php 
                echo $action_class;
                ?> <?php 
                echo $settings['dce_views_where_form_class_buttons'];
                ?>">
								<div class="dce-view-exposed-form-buttons elementor-field-type-submit dce-view-form-col-inner">
									<?php 
                if (!$settings['dce_views_where_form_ajax_nobutton']) {
                    ?><button class="button dce-button elementor-button elementor-size-<?php 
                    echo $settings['dce_views_input_size'];
                    ?> find <?php 
                    echo $settings['buttons_align'] == 'justify' ? 'dce-block' : '';
                    ?> <?php 
                    echo $settings['dce_views_where_form_class_button'];
                    ?>" type="submit"><span class="elementor-button-text"><?php 
                    echo $settings['dce_views_style_form_submit_text'];
                    ?></span></button><?php 
                }
                ?>
									<?php 
                if ($settings['dce_views_where_form_reset']) {
                    ?><input class="button dce-button elementor-button elementor-size-<?php 
                    echo $settings['dce_views_input_size'];
                    ?> reset <?php 
                    echo $settings['dce_views_where_form_class_button'];
                    ?>" type="reset" value="<?php 
                    echo $settings['dce_views_style_form_reset_text'];
                    ?>"><?php 
                }
                ?>
								</div>
							</div>
							<?php 
            }
            ?>

						<?php 
            if (isset($_GET['page_id'])) {
                ?>
							<input type="hidden" name="page_id" value="<?php 
                echo sanitize_text_field($_GET['page_id']);
                ?>">
						<?php 
            }
            ?>
						<?php 
            if (isset($_GET['p'])) {
                ?>
							<input type="hidden" name="p" value="<?php 
                echo sanitize_text_field($_GET['p']);
                ?>">
						<?php 
            }
            ?>
						<input type="hidden" name="eid" value="<?php 
            echo $this->get_id();
            ?>">
					</div>
				</form>
			</div>
			<?php 
        }
    }
    public function is_value_selected($akey, $value, $field_conf)
    {
        $asel = \false;
        if (isset($_GET['eid']) && $_GET['eid'] == $this->get_id() && !empty($_GET[$akey])) {
            $values = sanitize_text_field($_GET[$akey]);
        } else {
            $values = $field_conf;
        }
        if (!empty($values)) {
            if (\is_array($values)) {
                if (\in_array($value, $values)) {
                    $asel = \true;
                }
            } else {
                if ($value == $values) {
                    $asel = \true;
                }
            }
        }
        return $asel;
    }
    public function _get_placeholder($placeholder, $settings, $slash = \true)
    {
        if ($settings['dce_views_select_template_ajax_placeholder'] == 'image') {
            $img_url = empty($settings['dce_views_select_template_ajax_placeholder_image']['url']) ? '' : $settings['dce_views_select_template_ajax_placeholder_image']['url'];
            // TODO
            if (!$img_url) {
                $img_url = \Elementor\Utils::get_placeholder_image_src();
            }
            return '<img src="' . $img_url . '">';
        }
        if ($settings['dce_views_select_template_ajax_placeholder'] == 'text') {
            $placeholder = $settings['dce_views_select_template_ajax_placeholder_text'];
            // TODO
            return $placeholder;
        }
        return $placeholder;
    }
    public function _ajax($settings = null)
    {
        if (!$settings) {
            $settings = $this->get_settings_for_display();
        }
        if (!empty($settings['dce_views_select_template_ajax'])) {
            $lazy = $jkey = 'dce_' . $this->get_type() . '_view_' . $this->get_id() . '_ajax_lazy';
            if ($settings['dce_views_select_template_ajax_placeholder'] == 'text') {
                echo '<div class="dce-view-single-placeholder" style="display: none;">' . $settings['dce_views_select_template_ajax_placeholder_text'] . '</div>';
            }
            \ob_start();
            ?>
			<script id="<?php 
            echo $jkey;
            ?>">
				var dceAjaxPath = {"ajaxurl": "<?php 
            echo admin_url('admin-ajax.php');
            ?>"};
				function dce_views_load_template_<?php 
            echo $this->get_id();
            ?>($scope) {
				$scope.find('.dce-elementor-template-placeholder').each(function () {

				<?php 
            $placeholder = "\$scope.find('.dce-view-single-wrapper:first-child .elementor').parent().html()";
            $placeholder = $this->_get_placeholder($placeholder, $settings);
            if ($settings['dce_views_select_template_ajax_placeholder'] == 'text') {
                $placeholder = "\$scope.find('.dce-view-single-placeholder').html()";
            }
            if ($settings['dce_views_select_template_ajax_placeholder'] == 'image') {
                $placeholder = "'" . wp_slash($placeholder) . "'";
            }
            ?>
				var first_html = <?php 
            echo $placeholder;
            ?>;
				jQuery(this).html(first_html);
				<?php 
            if ($settings['dce_views_select_template_ajax_placeholder'] == 'fadein') {
                ?>
				jQuery(this).addClass('dce-elementor-template-placeholder-clone');
				<?php 
            }
            ?>
				<?php 
            if ($settings['dce_views_select_template_ajax_placeholder'] == 'fadein') {
                ?>
				jQuery(this).css('opacity', 0);
				<?php 
            }
            ?>

				<?php 
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ?>
				return true;
				<?php 
            }
            ?>

				<?php 
            if (!empty($settings['dce_views_select_template_ajax_progressive'])) {
                ?>
				var dce_load_template = function(dir){
				if (dir == 'down'){
				<?php 
            }
            ?>

				var template_id = jQuery(this).data('id');
				var dce_data = {
				'action': 'dce_elementor_template',
						'template_id': template_id,
				};
				var obj = 'post';
				if (jQuery(this).data('post')) {
				dce_data['post_id'] = jQuery(this).data('post');
				}
				if (jQuery(this).data('user')) {
				dce_data['user_id'] = jQuery(this).data('user');
				var obj = 'user';
				}
				if (jQuery(this).data('term')) {
				dce_data['term_id'] = jQuery(this).data('term');
				var obj = 'term';
				}
				if (jQuery(this).data('author')) {
				dce_data['author_id'] = jQuery(this).data('author');
				var obj = 'user';
				}

				dce_data['object'] = obj;
				var ele_id = dce_data['obj_id'];
				jQuery.ajax({
				url: dceAjaxPath.ajaxurl,
						dataType: "html",
						context: jQuery(this),
						type: 'POST',
						data: dce_data,
						error: function () {
						console.log('error');
						},
						success: function (data, status, xhr) {
						jQuery(this).html(data);
						jQuery(this).children('.elementor').addClass('dce-elementor-template-loaded').unwrap()<?php 
            if ($settings['dce_views_select_template_ajax_placeholder'] == 'fadein') {
                ?>.hide().fadeIn("slow")<?php 
            }
            ?>;
						$scope.trigger('dynamicooo/views/ajax_success');
						},
				});

				<?php 
            if (!empty($settings['dce_views_select_template_ajax_progressive'])) {
                ?>
				}
				}
				var waypointOptions = {
				offset: '100%',
						triggerOnce: true
				};
				elementorFrontend.waypoint(jQuery(this), dce_load_template, waypointOptions);
				<?php 
            }
            ?>
				});
				}

				(function ($) {
				var <?php 
            echo $jkey;
            ?> = function ($scope, $) {
				if ($scope.hasClass("elementor-element-<?php 
            echo $this->get_id();
            ?>")) {

				if (typeof dce_views_load_template_<?php 
            echo $this->get_id();
            ?> === "function") {
				dce_views_load_template_<?php 
            echo $this->get_id();
            ?>($scope);
				}

				}
				};
				$(window).on("elementor/frontend/init", function () {
				elementorFrontend.hooks.addAction("frontend/element_ready/<?php 
            echo $this->get_name();
            ?>.default", <?php 
            echo $jkey;
            ?>);
				});
				})(jQuery, window);</script>
			<?php 
            $add_js = \ob_get_clean();
            echo $add_js = \DynamicContentForElementor\Assets::dce_enqueue_script($jkey, $add_js);
        }
        if (!empty($settings['dce_views_where_form_ajax'])) {
            $jkey = 'dce_' . $this->get_type() . '_view_' . $this->get_id() . '_ajax_exposed';
            \ob_start();
            ?>
			<script id="<?php 
            echo $jkey;
            ?>">
				// Re init layout after ajax request on Exposed Form
				(function ( $ ) {
					"use strict";
					$(function () {
						$(document).on("dce/views/exposedform/after", function( e, data ) {
							if ( elementorFrontend) {
								if ( elementorFrontend.elementsHandler.runReadyTrigger ) {
									var widgets = $('.elementor-widget-dce-views').find('.elementor-widget');
									widgets.each(function (i) {
										elementorFrontend.elementsHandler.runReadyTrigger(jQuery(this));
									});
								}
							}
						});
					});
				}(jQuery));

				(function ($) {
				var <?php 
            echo $jkey;
            ?> = function ($scope, $) {
				if ($scope.hasClass("elementor-element-<?php 
            echo $this->get_id();
            ?>")) {

				function dce_views_update_result_<?php 
            echo $this->get_id();
            ?>() {
				var result_container = '.elementor-element-<?php 
            echo $this->get_id();
            ?> .dce-view-results-wrapper';
				var sort_container = '.elementor-element-<?php 
            echo $this->get_id();
            ?> .dce-view-exposed-sort-wrapper';
				var pagination_container = '.elementor-element-<?php 
            echo $this->get_id();
            ?> .dce-view-posts-pagination-wrapper';
				var filters_container = '.elementor-element-<?php 
            echo $this->get_id();
            ?> .dce-view-form-filters-wrapper';
				jQuery(result_container).html('<div class="dce-preloader" style="text-align: center;"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span></div>');
				var results = jQuery.get('?' + jQuery('#dce-view-form-<?php 
            echo $this->get_id();
            ?>').serialize(), function (data) {
				jQuery(result_container).html(jQuery(data).find(result_container).html());
				jQuery(sort_container).html(jQuery(data).find(sort_container).html());
				if (jQuery(data).find(pagination_container).length) {
				jQuery(pagination_container).html(jQuery(data).find(pagination_container).html());
				} else {
				jQuery(pagination_container).html('');
				}
				jQuery(filters_container).html(jQuery(data).find(filters_container).html());
				if (typeof dce_views_load_template_<?php 
            echo $this->get_id();
            ?> === "function") {
				dce_views_load_template_<?php 
            echo $this->get_id();
            ?>($scope);
				}

				jQuery(document).trigger( 'dce/views/exposedform/after' );


				});
				}

				$scope.find('.dce-view-form').on('submit', function () {
				dce_views_update_result_<?php 
            echo $this->get_id();
            ?>();
				return false;
				});

			<?php 
            if ($settings['dce_views_where_form_ajax_onchange']) {
                ?>
					$scope.find('.dce-view-form input, .dce-view-form select, .dce-view-form input[type=text]').on('input', function () {
					dce_views_update_result_<?php 
                echo $this->get_id();
                ?>();
					return false;
					});
					$scope.find('.dce-view-form input[type=reset]').on('click', function () {
					setTimeout(dce_views_update_result_<?php 
                echo $this->get_id();
                ?>, 100);
					});
			<?php 
            }
            ?>

				}
				};
				$(window).on("elementor/frontend/init", function () {
				elementorFrontend.hooks.addAction("frontend/element_ready/<?php 
            echo $this->get_name();
            ?>.default", <?php 
            echo $jkey;
            ?>);
				});
				})(jQuery, window);</script>
			<?php 
            $add_js = \ob_get_clean();
            $add_js = \DynamicContentForElementor\Assets::dce_enqueue_script($jkey, $add_js);
        }
        if (!empty($settings['dce_views_pagination_ajax'])) {
            $jkey = 'dce_' . $this->get_type() . '_view_' . $this->get_id() . '_ajax_nav';
            \ob_start();
            ?>
			<script id="<?php 
            echo $jkey;
            ?>">
				(function ($) {
				var <?php 
            echo $jkey;
            ?> = function ($scope, $) {
				if ($scope.hasClass("elementor-element-<?php 
            echo $this->get_id();
            ?>")) {

				$scope.on('click', '.dce-posts-pagination a', function() {
				var widget_container = '.elementor-element-<?php 
            echo $this->get_id();
            ?> .dce-view-results-wrapper';
				var pagination_container = '.elementor-element-<?php 
            echo $this->get_id();
            ?> .dce-view-posts-pagination-wrapper';
				var results = jQuery.get(jQuery(this).attr('href'), function(data) {
				if (jQuery(data).find(widget_container).length) {
				jQuery(widget_container).html(jQuery(data).find(widget_container).html());
				if (typeof dce_views_load_template_<?php 
            echo $this->get_id();
            ?> === "function") {
				dce_views_load_template_<?php 
            echo $this->get_id();
            ?>($scope);
				}
				}
				if (jQuery(data).find(pagination_container).length) {
				jQuery(pagination_container).html(jQuery(data).find(pagination_container).html());
				}
				});
				return false;
				});
				}
				};
				$(window).on("elementor/frontend/init", function () {
				elementorFrontend.hooks.addAction("frontend/element_ready/<?php 
            echo $this->get_name();
            ?>.default", <?php 
            echo $jkey;
            ?>);
				});
				})(jQuery, window);</script>
			<?php 
            $add_js = \ob_get_clean();
            $add_js = \DynamicContentForElementor\Assets::dce_enqueue_script($jkey, $add_js);
        }
        return \true;
    }
    public function _nav($the_query = null, $settings = array(), $total_objects = 0)
    {
        if (empty($settings)) {
            $settings = $this->get_settings_for_display();
        }
        if (empty($settings)) {
            return \false;
        }
        $posts_per_page = \intval($settings['dce_views_post_per_page']);
        $posts_per_page = $posts_per_page <= 0 ? 10 : $posts_per_page;
        switch ($settings['dce_views_object']) {
            case 'post':
                $max = \intval($the_query->max_num_pages);
                break;
            case 'user':
                $max = $settings['dce_views_post_per_page'] ? \ceil(\intval($total_objects) / $posts_per_page) : 0;
                break;
            case 'term':
                $max = $settings['dce_views_post_per_page'] ? \ceil(\intval($total_objects) / $posts_per_page) : 0;
                break;
        }
        $dce_views_pagination_page_limit = \intval($settings['dce_views_pagination_page_limit']);
        if ($settings['dce_views_limit_to']) {
            $dce_views_pagination_page_to = \ceil($settings['dce_views_limit_to'] / $posts_per_page);
            if (!$dce_views_pagination_page_limit || $dce_views_pagination_page_to < $dce_views_pagination_page_limit) {
                $dce_views_pagination_page_limit = $dce_views_pagination_page_to;
            }
        }
        if ($dce_views_pagination_page_limit && $dce_views_pagination_page_limit < $max) {
            $max = $dce_views_pagination_page_limit;
        }
        if ($max <= 1) {
            return;
        }
        $paged = $this->get_current_page();
        /** Add current page to the array */
        if ($paged >= 1) {
            $links[] = $paged;
        }
        /** Add the pages around the current page to the array */
        if ($paged >= 3) {
            $links[] = $paged - 1;
            $links[] = $paged - 2;
        }
        if ($paged + 2 <= $max) {
            $links[] = $paged + 2;
            $links[] = $paged + 1;
        }
        ?>
		<div class="dce-view-posts-pagination-wrapper">
			<nav class="navigation posts-navigation dce-posts-navigation dce-view-posts-pagination dce-posts-pagination elementor-pagination" role="navigation" arial-label="<?php 
        _e('Pagination');
        ?>" <?php 
        if ($settings['dce_views_pagination_type'] == 'infinite_scroll') {
            echo ' style="display: none;"';
        }
        ?>>
				<ul class="dce-page-numbers">
					<?php 
        if (empty($settings['dce_views_pagination_type']) || $settings['dce_views_pagination_type'] == 'prev_next' || $settings['dce_views_pagination_type'] == 'numbers_and_prev_next') {
            /** Previous Post Link */
            if ($paged > 1) {
                echo '<li><a class="page-numbers pagination__prev" href="' . $this->get_posts_link('prev') . '">';
                if (empty($settings['dce_views_pagination_prev_label'])) {
                    echo '&lt;';
                } else {
                    echo $settings['dce_views_pagination_prev_label'];
                }
                echo '</a></li> ';
            }
        }
        if (empty($settings['dce_views_pagination_type']) || $settings['dce_views_pagination_type'] == 'numbers' || $settings['dce_views_pagination_type'] == 'numbers_and_prev_next') {
            if ($settings['dce_views_pagination_numbers_shorten']) {
                /** Link to first page, plus ellipses if necessary */
                if (!\in_array(1, $links)) {
                    $class = 1 == $paged ? ' current' : '';
                    \printf('<li><a class="page-numbers%s" href="%s">%s</a></li>', $class, esc_url($this->get_posts_link('first')), '1');
                    if (!\in_array(2, $links)) {
                        echo '<li class="dots">…</li>';
                    }
                }
                /** Link to current page, plus 2 pages in either direction if necessary */
                \sort($links);
                foreach ((array) $links as $link) {
                    $class = $paged == $link ? ' current' : '';
                    \printf('<li><a class="page-numbers%s" href="%s">%s</a></li>', $class, esc_url($this->get_posts_link('current', $link)), $link);
                }
                /** Link to last page, plus ellipses if necessary */
                if (!\in_array($max, $links)) {
                    if (!\in_array($max - 1, $links)) {
                        echo '<li class="dots">…</li>' . "\n";
                    }
                    $class = $paged == $max ? ' current' : '';
                    \printf('<li><a class="page-numbers%s" href="%s">%s</a></li>', $class, esc_url($this->get_posts_link('last', $max)), $max);
                }
            } else {
                for ($p = 1; $p <= $max; $p++) {
                    $class = $paged == $p ? ' current' : '';
                    \printf('<li><a class="page-numbers%s" href="%s">%s</a></li>', $class, esc_url($this->get_posts_link('', $p)), $p);
                }
            }
        }
        if (empty($settings['dce_views_pagination_type']) || $settings['dce_views_pagination_type'] == 'prev_next' || $settings['dce_views_pagination_type'] == 'numbers_and_prev_next' || $settings['dce_views_pagination_type'] == 'infinite_scroll') {
            /** Next Post Link */
            if ($paged < $max) {
                echo '<li><a class="page-numbers pagination__next" href="' . $this->get_posts_link() . '">';
                if (empty($settings['dce_views_pagination_next_label'])) {
                    echo '&gt;';
                } else {
                    echo $settings['dce_views_pagination_next_label'];
                }
                echo '</a></li>';
            }
        }
        ?>
				</ul>
			</nav>
		</div>
		<?php 
        $this->_infinite($settings);
    }
    public function _infinite($settings = array())
    {
        if ($settings['dce_views_pagination']) {
            if ($settings['dce_views_pagination_type'] == 'infinite_scroll') {
                ?>
				<?php 
                if ($settings['dce_views_limit_scroll_button']) {
                    ?>
					<div class="elementor-button-wrapper<?php 
                    echo !empty($settings['dce_views_style_scroll_btn_align']) ? ' elementor-align-' . $settings['dce_views_style_scroll_btn_align'] : '';
                    ?>">
						<button class="elementor-button elementor-size-<?php 
                    echo $settings['dce_views_style_scroll_btn_size'];
                    ?> view-more-button">
							<span class="elementor-button-content-wrapper">
								<span class="elementor-button-text"><?php 
                    echo $settings['dce_views_limit_scroll_button_label'];
                    ?></span>
							</span>
						</button>
					</div>
				<?php 
                }
                ?>
				<!-- status elements -->
				<div class="scroller-status">
					<div class="infinite-scroll-request"<?php 
                if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    ?> style="display: none;"<?php 
                }
                ?>><?php 
                echo $settings['dce_views_limit_scroll_loading'];
                ?></div>
					<div class="infinite-scroll-last"<?php 
                if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    ?> style="display: none;"<?php 
                }
                ?>><?php 
                echo $settings['dce_views_limit_scroll_last'];
                ?></div>
					<p class="infinite-scroll-error" style="display: none;"><a class="infinite__next" href="<?php 
                echo $this->get_posts_link();
                ?>"><i class="fa fa-angle-double-down" aria-hidden="true"></i></a></p>
				</div>
				<?php 
                $jkey = 'dce_' . $this->get_type() . '_view_' . $this->get_id() . '_infinite';
                if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    \ob_start();
                    ?>
					<script id="<?php 
                    echo $jkey;
                    ?>">
						(function ($) {
						var <?php 
                    echo $jkey;
                    ?> = function ($scope, $) {
						if ($scope.hasClass("elementor-element-<?php 
                    echo $this->get_id();
                    ?>")) {
						$scope.find('.dce-view-archive').infiniteScroll({
						// options
						path: '.elementor-element-<?php 
                    echo $this->get_id();
                    ?> .pagination__next',
								append: '.elementor-element-<?php 
                    echo $this->get_id();
                    ?> .dce-view-archive .dce-view-single-wrapper',
								hideNav: '.elementor-element-<?php 
                    echo $this->get_id();
                    ?> .dce-posts-navigation',
								status: '.elementor-element-<?php 
                    echo $this->get_id();
                    ?> .scroller-status',
								debug: true,
					<?php 
                    if ($settings['dce_views_limit_scroll_button']) {
                        ?>
							button: '.elementor-element-<?php 
                        echo $this->get_id();
                        ?> .view-more-button',
									scrollThreshold: false,
					<?php 
                    }
                    ?>
						});
						// because script handle only p.infinite-scroll-last not a div
						$scope.find('.dce-view-archive').on('last.infiniteScroll', function(event, response, path) {
						jQuery(this).find('.infinite-scroll-last').fadeIn();
						}).on('request.infiniteScroll', function(event, path) {
						jQuery(this).find('.infinite-scroll-request').show();
						}).on('append.infiniteScroll', function(event, response, path, items) {
						jQuery(this).find('.infinite-scroll-request').hide();
						});
						}
						};
						$(window).on("elementor/frontend/init", function () {
						elementorFrontend.hooks.addAction("frontend/element_ready/<?php 
                    echo $this->get_name();
                    ?>.default", <?php 
                    echo $jkey;
                    ?>);
						});
						})(jQuery, window);</script>
					<?php 
                    $add_js = \ob_get_clean();
                    $add_js = \DynamicContentForElementor\Assets::dce_enqueue_script($jkey, $add_js);
                }
                return \true;
            }
        }
        return \false;
    }
    public function get_current_page()
    {
        return isset($_GET['pag']) && isset($_GET['eid']) && $_GET['eid'] == $this->get_id() ? absint($_GET['pag']) : 1;
    }
    public function get_posts_link($verso = 'next', $page = 1)
    {
        global $wp;
        $current_url = home_url(add_query_arg(array(), $wp->request));
        $paged = $this->get_current_page();
        switch ($verso) {
            case 'next':
                $page = $paged + 1;
                break;
            case 'prev':
                $page = $paged - 1;
                break;
            case 'current':
            case 'first':
            case 'last':
        }
        $ret = $current_url . '/?';
        if (!empty($_GET) && isset($_GET['eid']) && $_GET['eid'] == $this->get_id()) {
            foreach ($_GET as $gkey => $gval) {
                $gkey = esc_attr($gkey);
                if ($gkey != 'pag' && $gkey != 'eid') {
                    if (\is_array($gval)) {
                        foreach ($gval as $sgval) {
                            $ret .= '&' . $gkey . '[]=' . esc_attr($sgval);
                        }
                    } else {
                        $ret .= '&' . $gkey . '=' . esc_attr($gval);
                    }
                }
            }
        }
        if ($ret != $current_url . '/?') {
            $ret .= '&';
        }
        $ret .= 'eid=' . $this->get_id();
        $ret .= '&pag=' . $page;
        if (isset($_GET['page_id'])) {
            $ret .= '&page_id=' . sanitize_text_field($_GET['page_id']);
        }
        if (isset($_GET['p'])) {
            $ret .= '&p=' . sanitize_text_field($_GET['p']);
        }
        if (isset($_GET['s'])) {
            $ret .= '&s=' . sanitize_text_field($_GET['s']);
        }
        return $ret;
    }
    public function set_exposed_form_args($args = array(), $settings = null)
    {
        if (!$settings) {
            $settings = $this->get_settings_for_display();
        }
        if (isset($_GET['eid']) && $_GET['eid'] == $this->get_id()) {
            if ($settings['dce_views_object'] == 'post') {
                $args_keys = Helper::get_wp_query_args();
            }
            foreach ($_GET as $gkey => $aget) {
                if ($gkey === 'p') {
                    continue;
                }
                $gkey = esc_attr($gkey);
                $aget = esc_attr($aget);
                if ($settings['dce_views_object'] == 'post') {
                    if (!\in_array($gkey, $args_keys)) {
                        continue;
                    }
                }
                if (!isset($args[$gkey])) {
                    $args[$gkey] = $aget;
                }
            }
        }
        return $args;
    }
    public function get_wp_query_args($settings = null)
    {
        if (!$settings) {
            $settings = $this->get_settings_for_display();
        }
        if (empty($this->taxonomies)) {
            $this->taxonomies = $taxonomies = Helper::get_taxonomies();
        }
        $args = array();
        // FROM
        if ($settings['dce_views_object'] == 'post') {
            if (isset($settings['dce_views_from_dynamic']) && $settings['dce_views_from_dynamic']) {
                global $wp_query;
                if (is_archive() || is_search() || is_home()) {
                    $query_vars = $wp_query->query_vars;
                    if (!empty($query_vars)) {
                        if ($settings['dce_views_object'] == 'post') {
                            $args_keys = Helper::get_wp_query_args();
                            $taxonomies = Helper::get_taxonomies();
                        }
                        foreach ($query_vars as $gkey => $aget) {
                            if ($settings['dce_views_object'] == 'post') {
                                if (!\in_array($gkey, $args_keys)) {
                                    continue;
                                }
                            }
                            $args[$gkey] = $aget;
                        }
                        if ($settings['dce_views_object'] == 'post') {
                            $queried_object = get_queried_object();
                            $term_id = 0;
                            if ($queried_object && \is_object($queried_object) && \get_class($queried_object) == 'WP_Term') {
                                $term_id = get_queried_object_id();
                            }
                            foreach ($query_vars as $gkey => $aget) {
                                if (isset($taxonomies[$gkey])) {
                                    if ($term_id && $queried_object->slug == $aget) {
                                        $args['tax_query'][$gkey] = array('taxonomy' => $gkey, 'field' => 'term_id', 'terms' => $term_id);
                                    } else {
                                        $args['tax_query'][$gkey] = array('taxonomy' => $gkey, 'field' => 'name', 'terms' => $aget);
                                    }
                                }
                            }
                            if (isset($query_vars['taxonomy']) && isset($query_vars['term'])) {
                                if ($term_id && $queried_object->slug == $query_vars['term']) {
                                    $args['tax_query'][$query_vars['taxonomy']] = array('taxonomy' => $query_vars['taxonomy'], 'field' => 'term_id', 'terms' => $term_id);
                                } else {
                                    $args['tax_query'][$query_vars['taxonomy']] = array('taxonomy' => $query_vars['taxonomy'], 'field' => 'name', 'terms' => $query_vars['term']);
                                }
                            }
                        }
                    }
                } else {
                    $post_id = $wp_query->query_vars['p'];
                    $taxonomies = get_post_taxonomies($post_id);
                    $terms = array();
                    foreach ($taxonomies as $atax) {
                        $terms = $terms + wp_get_post_terms($post_id, $atax);
                    }
                    if (empty($terms)) {
                        // same type
                        $cpt = get_post_type();
                        $args['post_type'] = $cpt;
                    } else {
                        // same taxonomy terms associated
                        foreach ($terms as $akey => $aterm) {
                            $tkey = $aterm->taxonomy;
                            switch ($tkey) {
                                case 'category':
                                    if (isset($args['tag__in'])) {
                                        $args['category__in'] = \array_merge($args['category__in'] ?? [], array($aterm->term_id));
                                    } else {
                                        $args['category__in'] = array($aterm->term_id);
                                    }
                                    break;
                                case 'post_tag':
                                    if (isset($args['tag__in'])) {
                                        $args['tag__in'] = \array_merge($args['tag__in'], array($aterm->term_id));
                                    } else {
                                        $args['tag__in'] = array($aterm->term_id);
                                    }
                                    break;
                                default:
                                    if (isset($args['tax_query'][$tkey])) {
                                        $args['tax_query'][$tkey] = array('taxonomy' => $tkey, 'field' => 'term_id', 'terms' => \array_merge($args['tax_query'][$tkey]['terms'], array($aterm->term_id)), 'operator' => 'IN');
                                    } else {
                                        $args['tax_query'][$tkey] = array('taxonomy' => $tkey, 'field' => 'term_id', 'terms' => array($aterm->term_id), 'operator' => 'IN');
                                    }
                            }
                        }
                    }
                    // exclude himself
                    $args['post__not_in'] = array($post_id);
                }
            } else {
                if (!empty($settings['dce_views_from_ignore_sticky_posts'])) {
                    $args['ignore_sticky_posts'] = 1;
                }
                if (!empty($settings['dce_views_cpt'])) {
                    if (\count($settings['dce_views_cpt']) > 1) {
                        $args['post_type'] = $settings['dce_views_cpt'];
                        if (\in_array('custom', $settings['dce_views_cpt'])) {
                            $args['post_type'] = \array_merge($settings['dce_views_cpt'], Helper::str_to_array(',', $settings['dce_views_cpt_custom']));
                        }
                    } else {
                        if (\is_array($settings['dce_views_cpt'])) {
                            $args['post_type'] = \reset($settings['dce_views_cpt']);
                        } else {
                            $args['post_type'] = $settings['dce_views_cpt'];
                        }
                        if ($args['post_type'] == 'custom') {
                            $args['post_type'] = $settings['dce_views_cpt_custom'];
                        }
                    }
                }
                if (!empty($settings['dce_views_status'])) {
                    if (\count($settings['dce_views_status']) > 1) {
                        $args['post_status'] = $settings['dce_views_status'];
                    } else {
                        $args['post_status'] = \reset($settings['dce_views_status']);
                        if ($settings['dce_views_cpt'] == array('attachment')) {
                            $args['post_status'] = 'any';
                        }
                    }
                } elseif ($settings['dce_views_cpt'] == array('attachment')) {
                    $args['post_status'] = 'any';
                }
                // FROM - filter by taxonomy term
                foreach ($this->taxonomies as $tkey => $atax) {
                    if (!empty($settings['dce_views_term_' . $tkey])) {
                        switch ($tkey) {
                            case 'category':
                                $args['category__in'] = \array_map('intval', $settings['dce_views_term_' . $tkey]);
                                break;
                            case 'post_tag':
                                $args['tag__in'] = \array_map('intval', $settings['dce_views_term_' . $tkey]);
                                break;
                            default:
                                if ($tkey) {
                                    $args['tax_query'][] = array('taxonomy' => $tkey, 'field' => 'term_id', 'terms' => \array_map('intval', $settings['dce_views_term_' . $tkey]), 'operator' => 'IN');
                                }
                        }
                    }
                }
                if (!empty($settings['dce_views_term_custom'])) {
                    $terms = Helper::str_to_array(',', $settings['dce_views_term_custom']);
                    if (!empty($terms)) {
                        foreach ($terms as $aterm) {
                            $term = Helper::get_term($aterm);
                            if ($term) {
                                switch ($term->taxonomy) {
                                    case 'category':
                                        if (empty($args['category__in'])) {
                                            $args['category__in'] = array($term->term_id);
                                        } else {
                                            $args['category__in'][] = $term->term_id;
                                        }
                                        break;
                                    case 'post_tag':
                                        if (empty($args['tag__in'])) {
                                            $args['tag__in'] = array($term->term_id);
                                        } else {
                                            $args['tag__in'][] = $term->term_id;
                                        }
                                        break;
                                    default:
                                        $tax_query_found = \false;
                                        if (!empty($args['tax_query'])) {
                                            foreach ($args['tax_query'] as $tkey => $tax_query) {
                                                if ($tax_query['taxonomy'] == $term->taxonomy) {
                                                    $args['tax_query'][$tkey]['terms'][] = $term->term_id;
                                                    $tax_query_found = \true;
                                                }
                                            }
                                        }
                                        if (empty($args['tax_query']) || !$tax_query_found) {
                                            $args['tax_query'][] = array('taxonomy' => $term->taxonomy, 'field' => 'term_id', 'terms' => array($term->term_id), 'operator' => 'IN');
                                        }
                                }
                            }
                        }
                    }
                }
                if (!empty($settings['dce_views_term_not'])) {
                    $terms = Helper::str_to_array(',', $settings['dce_views_term_not']);
                    if (!empty($terms)) {
                        foreach ($terms as $aterm) {
                            $term = Helper::get_term($aterm);
                            if ($term) {
                                switch ($term->taxonomy) {
                                    case 'category':
                                        if (empty($args['category__not_in'])) {
                                            $args['category__not_in'] = array($term->term_id);
                                        } else {
                                            $args['category__not_in'][] = $term->term_id;
                                        }
                                        break;
                                    case 'post_tag':
                                        if (empty($args['tag__not_in'])) {
                                            $args['tag__not_in'] = array($term->term_id);
                                        } else {
                                            $args['tag__not_in'][] = $term->term_id;
                                        }
                                        break;
                                    default:
                                        $tax_query_found = \false;
                                        if (!empty($args['tax_query'])) {
                                            foreach ($args['tax_query'] as $tkey => $tax_query) {
                                                if ($tax_query['taxonomy'] == $term->taxonomy) {
                                                    $args['tax_query'][$tkey]['terms'][] = $term->term_id;
                                                    $tax_query_found = \true;
                                                }
                                            }
                                        }
                                        if (empty($args['tax_query']) || !$tax_query_found) {
                                            $args['tax_query'][] = array('taxonomy' => $term->taxonomy, 'field' => 'term_id', 'terms' => array($term->term_id), 'operator' => 'NOT IN');
                                        }
                                }
                            }
                        }
                    }
                }
                if (isset($args['tax_query'])) {
                    $args['tax_query']['relation'] = $settings['dce_views_tax_relation'];
                }
            }
            if (!empty($settings['dce_views_select_template_ajax'])) {
                $args['fields'] = 'ids';
            }
        }
        // PAGINATION
        if ($settings['dce_views_object'] == 'post') {
            if ($settings['dce_views_pagination']) {
                if ($settings['dce_views_post_per_page'] > 0) {
                    $args['posts_per_page'] = $settings['dce_views_post_per_page'];
                }
            } else {
                $args['nopaging'] = \true;
                $args['posts_per_page'] = -1;
            }
        } else {
            if ($settings['dce_views_pagination']) {
                if ($settings['dce_views_post_per_page'] > 0) {
                    $args['number'] = $settings['dce_views_post_per_page'];
                }
                if ($settings['dce_views_object'] == 'term') {
                    if ($this->get_current_page()) {
                        if (!$settings['dce_views_pagination'] || $settings['dce_views_pagination'] && $this->get_current_page() == 1 && !$settings['dce_views_limit_offset_pagination']) {
                            $args['offset'] = $settings['dce_views_limit_offset'] + $settings['dce_views_post_per_page'] * ($this->get_current_page() - 1);
                        } else {
                            $args['offset'] = $settings['dce_views_post_per_page'] * ($this->get_current_page() - 1);
                        }
                    }
                }
            } else {
                $args['number'] = 0;
            }
        }
        if ($settings['dce_views_object'] == 'user') {
            if (!empty($settings['dce_views_role'])) {
                $args['role__in'] = $settings['dce_views_role'];
            }
            if (!empty($settings['dce_views_role_exclude'])) {
                $args['role__not_in'] = $settings['dce_views_role_exclude'];
            }
        }
        if ($settings['dce_views_object'] == 'term') {
            if (!empty($settings['dce_views_tax'])) {
                $args['taxonomy'] = $settings['dce_views_tax'];
            }
            $args['hide_empty'] = (bool) $settings['dce_views_empty'];
            $args['count'] = (bool) $settings['dce_views_term_count'];
            if ($settings['dce_views_term_parent_dynamic']) {
                if ($settings['dce_views_term_parent_dynamic_id']) {
                    $args['parent'] = (int) $settings['dce_views_term_parent_dynamic_id'];
                }
            } else {
                if ($settings['dce_views_term_parent']) {
                    $args['parent'] = (int) $settings['dce_views_term_parent_id'];
                }
            }
        }
        // COLLECT ALL WHERE CONDITIONS
        $where_fields = $settings['dce_views_where'];
        $filters = array();
        if (!empty($settings['dce_views_where_form'])) {
            foreach ($settings['dce_views_where_form'] as $afield) {
                if (isset($_GET[$afield['dce_views_where_form_field']]) || $settings['dce_views_where_form_result']) {
                    $default_value = $afield['dce_views_where_form_preselect'];
                    $afield_value = $default_value;
                    if (!empty($_GET['eid']) && $_GET['eid'] == $this->get_id() && isset($_GET[$afield['dce_views_where_form_field']])) {
                        $afield_value = $_GET[$afield['dce_views_where_form_field']];
                    }
                    if ($settings['dce_views_where_form_active_filters']) {
                        if ($afield_value || \Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $filters[$afield['dce_views_where_form_field']] = $afield_value;
                        }
                    }
                    $taxonomy = \false;
                    if (\substr($afield['dce_views_where_form_field'], 0, 9) == 'taxonomy_') {
                        $taxonomy = \substr($afield['dce_views_where_form_field'], 9);
                    }
                    if ($taxonomy) {
                        if (\is_array($afield_value)) {
                            $tax_ids = \array_map('intval', $afield_value);
                        } else {
                            $tax_ids = array(\intval($afield_value));
                        }
                        if (!empty($tax_ids) && $tax_ids[0]) {
                            switch ($taxonomy) {
                                case 'category':
                                    $args['category__in'] = $tax_ids;
                                    break;
                                case 'post_tag':
                                    $args['tag__in'] = $tax_ids;
                                    break;
                                default:
                                    if ($taxonomy) {
                                        $args['tax_query'][] = array('taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => $tax_ids, 'operator' => 'IN');
                                    }
                            }
                        }
                    } else {
                        if (empty($afield_value)) {
                            continue;
                        }
                        $where_fields[] = array('dce_views_where_field' => $afield['dce_views_where_form_field'], 'dce_views_where_field_is_sub' => $afield['dce_views_where_form_field_is_sub'], 'dce_views_where_field_sub' => $afield['dce_views_where_form_field_sub'], 'dce_views_where_value' => $afield_value, 'dce_views_where_operator' => $afield['dce_views_where_form_operator'], 'dce_views_where_rule' => $afield['dce_views_where_form_rule'], 'dce_views_where_form_type' => $afield['dce_views_where_form_type']);
                    }
                }
            }
        }
        $this->_form_active_filters($filters, $settings);
        if ($settings['dce_views_pagination'] && $settings['dce_views_pagination_page_limit']) {
            $args['max_num_pages'] = \intval($settings['dce_views_pagination_page_limit']);
        }
        $obj__in = array();
        $first = \true;
        $is_meta_fnc = 'is_' . $settings['dce_views_object'] . '_meta';
        // WHERE - NATIVE
        if (!empty($where_fields)) {
            foreach ($where_fields as $awhere) {
                if ($awhere['dce_views_where_field'] && $awhere['dce_views_where_operator'] && !Helper::$is_meta_fnc($awhere['dce_views_where_field'])) {
                    // need some raw query because wp_query has limitations
                    $obj_ids = $this->get_obj_ids($awhere);
                    $this->obj__in[$awhere['dce_views_where_field']] = $obj_ids;
                    if ($awhere['dce_views_where_rule'] == 'AND') {
                        if (!$first) {
                            $obj__in = \array_intersect($obj__in, $obj_ids);
                        } else {
                            $obj__in = $obj_ids;
                        }
                    } else {
                        $obj__in = \array_merge($obj__in, $obj_ids);
                    }
                    $first = \false;
                }
            }
        }
        if ($settings['dce_views_attachment_mime_type']) {
            $types = Helper::str_to_array(',', $settings['dce_views_attachment_mime_type']);
            if (\count($types) > 1) {
                $args['post_mime_type'] = $types;
            } else {
                $args['post_mime_type'] = $settings['dce_views_attachment_mime_type'];
            }
        }
        // WHERE - META
        if (!empty($where_fields)) {
            foreach ($where_fields as $awhere) {
                if ($awhere['dce_views_where_field'] && $awhere['dce_views_where_operator'] && Helper::$is_meta_fnc($awhere['dce_views_where_field'])) {
                    $obj_ids = $this->get_obj_ids($awhere);
                    if (!\is_bool($obj_ids)) {
                        $this->obj__in[$awhere['dce_views_where_field']] = $obj_ids;
                        if ($awhere['dce_views_where_rule'] == 'AND') {
                            if (!$first) {
                                $obj__in = \array_intersect($obj__in, $obj_ids);
                            } else {
                                $obj__in = $obj_ids;
                            }
                        } else {
                            $obj__in = \array_merge($obj__in, $obj_ids);
                        }
                        $first = \false;
                    }
                }
            }
        }
        $obj_not_in = array();
        if (isset($settings['dce_views_ignore_ids']) && $settings['dce_views_ignore_ids']) {
            $obj_not_in = $settings['dce_views_ignore_ids'];
            $obj_not_in = Helper::str_to_array(',', $obj_not_in, 'intval');
        }
        switch ($settings['dce_views_object']) {
            case 'post':
                if (!empty($obj_not_in)) {
                    $args['post__not_in'] = $obj_not_in;
                }
                if (!$first) {
                    if (!empty($obj__in)) {
                        $args['post__in'] = $obj__in;
                    } else {
                        // NO RESULTS
                        $args['post__in'] = array(0);
                    }
                }
                if (isset($args['post__in']) && isset($args['post__not_in'])) {
                    $args['post__in'] = \array_diff($args['post__in'], $args['post__not_in']);
                }
                break;
            case 'user':
                if (!empty($obj_not_in)) {
                    $args['exclude'] = $obj_not_in;
                }
                if (!$first) {
                    if (!empty($obj__in)) {
                        $args['include'] = $obj__in;
                    } else {
                        // NO RESULTS
                        $args['include'] = array(0);
                    }
                }
                if (isset($args['include']) && isset($args['exclude'])) {
                    $args['include'] = \array_diff($args['include'], $args['exclude']);
                }
                break;
            case 'term':
                if (!empty($obj_not_in)) {
                    $args['exclude'] = $obj_not_in;
                    $args['exclude_tree'] = $obj_not_in;
                }
                if (!$first) {
                    if (!empty($obj__in)) {
                        $args['include'] = $obj__in;
                    } else {
                        // NO RESULTS
                        $args['include'] = array(0);
                    }
                }
                if (isset($args['include']) && isset($args['exclude'])) {
                    $args['include'] = \array_diff($args['include'], $args['exclude']);
                }
                break;
        }
        // ORDER BY
        if ($settings['dce_views_order_random']) {
            $args['orderby'] = 'rand';
        } else {
            if (!empty($_GET['orderby']) && isset($_GET['eid']) && $_GET['eid'] == $this->get_id()) {
                list($order_sort, $order_field) = \explode('__', sanitize_text_field($_GET['orderby']), 2);
                $order_sort = esc_attr($order_sort);
                $order_field = esc_attr($order_field);
                $order_tmp = array();
                foreach ($settings['dce_views_order_by'] as $key => $asort) {
                    if ($asort['dce_views_order_field_sort_exposed'] && $order_field == $asort['dce_views_order_field']) {
                        $order_tmp['dce_views_order_field'] = $order_field;
                        $order_tmp['dce_views_order_field_sort'] = $order_sort;
                        break;
                    }
                }
                if (!empty($order_tmp)) {
                    $settings['dce_views_order_by'][] = $order_tmp;
                }
            }
            if (!empty($settings['dce_views_group_by_field'])) {
                $taxonomy = \false;
                if (\substr($settings['dce_views_group_by_field'], 0, 9) == 'taxonomy_') {
                    $taxonomy = \substr($settings['dce_views_group_by_field'], 9);
                } else {
                    \array_unshift($settings['dce_views_order_by'], array('dce_views_order_field' => $settings['dce_views_group_by_field'], 'dce_views_order_field_sort' => $settings['dce_views_group_by_field_order']));
                }
            }
            if (!empty($settings['dce_views_order_by'])) {
                $orderby_clauses = ['relation' => 'AND'];
                $orderby_not_exists = ['relation' => 'OR'];
                $orderby_metaquery_used = \false;
                foreach ($settings['dce_views_order_by'] as $key => $asort) {
                    if ($asort['dce_views_order_field']) {
                        $is_meta = 'is_' . $settings['dce_views_object'] . '_meta';
                        $is_meta = Helper::$is_meta($asort['dce_views_order_field']);
                        if ($settings['dce_views_object'] == 'term' && \in_array($asort['dce_views_order_field'], array('parent', 'description', 'taxonomy', 'count'))) {
                            $is_meta = \false;
                        }
                        if ($is_meta) {
                            $dce_views_order_field = 'meta_value';
                            if (!empty($asort['dce_views_order_field_type'])) {
                                $dce_views_order_field .= $asort['dce_views_order_field_type'];
                            }
                        } else {
                            $dce_views_order_field = $asort['dce_views_order_field'];
                        }
                        $field_name = $asort['dce_views_order_field'];
                        $meta_type = 'CHAR';
                        if (!empty($asort['dce_views_order_field_type'])) {
                            switch ($asort['dce_views_order_field_type']) {
                                case '':
                                    $meta_type = 'CHAR';
                                    break;
                                case '_num':
                                    $meta_type = 'NUMERIC';
                                    break;
                                case '_date':
                                    $meta_type = 'DATE';
                                    break;
                            }
                        }
                        // Don't insert on meta_query the orderby fields
                        $default_fields = ['post_content' => 'post_content'];
                        $default_fields = \array_merge($default_fields, Helper::get_post_orderby_options());
                        if (!\array_key_exists($field_name, $default_fields) && !\array_key_exists(\str_replace('post_', '', $field_name), $default_fields)) {
                            $orderby_metaquery_used = \true;
                            $orderby_clause = ['key' => $field_name, 'type' => $meta_type, 'compare' => 'EXISTS'];
                            $orderby_clause_name = $field_name . '_clause';
                            $orderby_clauses[$orderby_clause_name] = $orderby_clause;
                            $args['orderby'][$orderby_clause_name] = $asort['dce_views_order_field_sort'];
                            // NOT EXISTS
                            $orderby_not_exists[] = ['key' => $field_name, 'type' => $meta_type, 'compare' => 'NOT EXISTS'];
                        } else {
                            if (\is_array($args['orderby']) && \is_array($asort)) {
                                $args['orderby'][$field_name] = $asort['dce_views_order_field_sort'];
                            }
                        }
                    }
                }
                if ($orderby_metaquery_used) {
                    if ('yes' === $settings['dce_views_order_by_no_nulls']) {
                        $args['meta_query'] = $orderby_clauses;
                    } else {
                        $args['meta_query'] = ['relation' => 'OR', $orderby_clauses, $orderby_not_exists];
                    }
                }
                if ($settings['dce_views_object'] == 'term' && \is_array($args['orderby'])) {
                    $order_by_fields = \array_keys($args['orderby']);
                    $order_by_first = \reset($args['orderby']);
                    $args['order'] = $order_by_first;
                    $args['orderby'] = \reset($order_by_fields);
                }
            }
        }
        // OFFSET
        if ($settings['dce_views_object'] != 'term' || !$settings['dce_views_pagination']) {
            if ($settings['dce_views_limit_offset']) {
                if (!$settings['dce_views_pagination'] || $settings['dce_views_pagination'] && $this->get_current_page() == 1 && !$settings['dce_views_limit_offset_pagination']) {
                    $args['offset'] = $settings['dce_views_limit_offset'];
                }
            }
        }
        // PAGE
        if (isset($_GET['pag']) && $_GET['pag'] > 0 && isset($_GET['eid']) && $_GET['eid'] == $this->get_id()) {
            $args['paged'] = $this->get_current_page();
        }
        if ($settings['dce_views_object'] == 'post' && !isset($args['post_status'])) {
            $args['post_status'] = 'publish';
        }
        if ($settings['dce_views_object'] == 'user' && empty($args)) {
            $args['exclude'] = array(0);
        }
        return $args;
    }
    public function get_obj_ids($awhere, $retry = null)
    {
        global $wpdb;
        $settings = $this->get_settings_for_display();
        $obj_ids = array();
        if (!$retry) {
            if (isset($_GET[$awhere['dce_views_where_field']]) && $_GET['eid'] == $this->get_id()) {
                $search_value = sanitize_text_field($_GET[$awhere['dce_views_where_field']]);
            } else {
                $search_value = $awhere['dce_views_where_value'];
            }
        } else {
            $search_value = $retry;
        }
        if (!empty($settings['dce_views_where_form'])) {
            foreach ($settings['dce_views_where_form'] as $afield) {
                if ($afield['dce_views_where_form_field'] == $awhere['dce_views_where_field'] && empty($search_value)) {
                    return \false;
                }
            }
        }
        if ($awhere['dce_views_where_operator'] == 'IN' || $awhere['dce_views_where_operator'] == 'NOT IN') {
            $search_values = Helper::str_to_array(',', $search_value, 'esc_sql');
            if (!empty($search_values)) {
                $is_string = \false;
                foreach ($search_values as $asrc) {
                    if (\is_string($asrc)) {
                        $is_string = \true;
                        break;
                    }
                }
                if ($is_string) {
                    $search_value = '("' . \implode('","', $search_values) . '")';
                } else {
                    $search_value = '(' . \implode(',', $search_values) . ')';
                }
            } else {
                $search_value = '(0)';
            }
        }
        if ($awhere['dce_views_where_operator'] == 'BETWEEN' || $awhere['dce_views_where_operator'] == 'NOT BETWEEN') {
            $search_value = \implode('" AND "', Helper::str_to_array(',', $search_value));
        }
        $acf_repeater_block_fields = Helper::get_acf_fields(array('repeater', 'block'));
        $is_repeater = Helper::is_acf_active() && \array_key_exists($awhere['dce_views_where_field'], $acf_repeater_block_fields);
        $is_meta_fnc = 'is_' . $settings['dce_views_object'] . '_meta';
        $is_meta = Helper::$is_meta_fnc($awhere['dce_views_where_field']);
        $obj_first = \substr($settings['dce_views_object'], 0, 1);
        $field_id = $settings['dce_views_object'] == 'term' ? $settings['dce_views_object'] . '_id' : 'ID';
        $post_fields = $settings['dce_views_object'] == 'post' ? ', p.post_type, p.post_parent' : '';
        $table = $wpdb->prefix . $this->validate_object($settings['dce_views_object']) . 's';
        $table_meta = $wpdb->prefix . $this->validate_object($settings['dce_views_object']) . 'meta';
        if ($settings['dce_views_object'] == 'user') {
            if (\defined('CUSTOM_USER_TABLE')) {
                $table = CUSTOM_USER_TABLE;
            }
            if (\defined('CUSTOM_USER_META_TABLE')) {
                $table_meta = CUSTOM_USER_META_TABLE;
            }
        }
        if ($settings['dce_views_object'] == 'term') {
            // TODO: support for all operator in term wp_term_taxonomy table
            if (\in_array($awhere['dce_views_where_field'], array('parent', 'description', 'taxonomy', 'count'))) {
                $is_meta = \false;
                $table = $wpdb->prefix . $this->validate_object($settings['dce_views_object']) . '_taxonomy ';
            }
        }
        if ($is_meta) {
            if (!\in_array($awhere['dce_views_where_operator'], array('EXISTS', 'NOT EXISTS'))) {
                $search_query = 'SELECT ' . esc_sql($obj_first) . 'm.' . esc_sql($settings['dce_views_object']) . '_id AS "ID"' . esc_sql($post_fields) . ' FROM ';
                $search_query .= esc_sql($table_meta) . ' ' . esc_sql($obj_first) . 'm, ';
            } else {
                $search_query = 'SELECT ' . esc_sql($field_id) . ' AS "ID" FROM ';
            }
            $search_query .= esc_sql($table) . ' ' . esc_sql($obj_first);
            if (\in_array($awhere['dce_views_where_operator'], array('EXISTS', 'NOT EXISTS'))) {
                $search_query .= ' WHERE ' . esc_sql($awhere['dce_views_where_operator']) . ' ( SELECT ' . esc_sql($settings['dce_views_object']) . '_id AS "ID" FROM ' . esc_sql($table_meta) . ' ' . esc_sql($obj_first) . 'm';
            }
            $search_query .= ' WHERE ' . esc_sql($obj_first) . '.' . esc_sql($field_id) . ' = ' . esc_sql($obj_first) . 'm.' . esc_sql($settings['dce_views_object']) . '_id AND ' . esc_sql($obj_first) . 'm.meta_key LIKE "' . esc_sql($awhere['dce_views_where_field']) . ($is_repeater ? '_%" ' : '" ');
            if (\in_array($awhere['dce_views_where_operator'], array('EXISTS', 'NOT EXISTS'))) {
                $search_query .= ' )';
            } else {
                if (empty($awhere['dce_views_where_field_is_sub'])) {
                    $search_query .= 'AND ( ';
                }
            }
        } else {
            $search_query = 'SELECT ' . esc_sql($field_id) . ' AS "ID" FROM ' . esc_sql($table);
            $search_query .= ' WHERE ';
        }
        if (!\is_array($search_value)) {
            $search_values = array($search_value);
        } else {
            $search_values = $search_value;
        }
        if (!empty($awhere['dce_views_where_field_is_sub'])) {
            $results = $wpdb->get_results($search_query);
            foreach ($results as $key => $aobj) {
                if ($settings['dce_views_object'] == 'post' && $aobj->post_type == 'revision') {
                    continue;
                }
                $pid = \intval($aobj->ID);
                if ($is_meta) {
                    $fnc = 'get_' . $settings['dce_views_object'] . '_meta';
                    $value = \call_user_func($fnc, $pid, $awhere['dce_views_where_field'], \true);
                } else {
                    $fnc = 'get_' . $settings['dce_views_object'];
                    if ($settings['dce_views_object'] == 'user') {
                        $aobj = get_user_by('ID', $pid);
                    } else {
                        $aobj = \call_user_func($fnc, $pid);
                    }
                    $value = $aobj->{$awhere['dce_views_where_field']};
                }
                $sub_value = Tokens::replace_var_tokens($awhere['dce_views_where_field_sub'], 'field', $value);
                $arr_satisfy = -1;
                foreach ($search_values as $akey => $search_value) {
                    $satisfy = \false;
                    switch ($awhere['dce_views_where_operator']) {
                        case ">":
                            $satisfy = $sub_value > $search_value;
                            break;
                        case ">=":
                            $satisfy = $sub_value >= $search_value;
                            break;
                        case "<":
                            $satisfy = $sub_value < $search_value;
                            break;
                        case "<=":
                            $satisfy = $sub_value <= $search_value;
                            break;
                        case "LIKE":
                        case "RLIKE":
                        case "=":
                            $satisfy = $sub_value == $search_value;
                            break;
                        case "NOT LIKE":
                        case "!=":
                            $satisfy = $sub_value != $search_value;
                            break;
                        case "IN":
                            if (\is_array($sub_value)) {
                                $satisfy = \in_array($search_value, $sub_value);
                            }
                            break;
                        case "NOT IN":
                            if (\is_array($sub_value)) {
                                $satisfy = !\in_array($search_value, $sub_value);
                            }
                            break;
                        case "BETWEEN":
                            if (\is_array($search_value)) {
                                $satisfy = $sub_value > \reset($search_value) && $sub_value < \end($search_value);
                            }
                            break;
                        case "NOT BETWEEN":
                            if (\is_array($search_value)) {
                                $satisfy = $sub_value < \reset($search_value) || $sub_value > \end($search_value);
                            }
                            break;
                        case "EXISTS":
                        case "NOT EXISTS":
                            $satisfy = $sub_value == '';
                            break;
                        //"REGEXP" => "REGEXP",
                        //"NOT REGEXP" => "NOT REGEXP",
                        default:
                            $satisfy = \false;
                    }
                    if ($satisfy) {
                        if ($arr_satisfy < 0 && !$key || $awhere['dce_views_where_rule'] == 'OR') {
                            $arr_satisfy = 1;
                        } else {
                            $arr_satisfy = 0;
                        }
                    }
                }
                if ($arr_satisfy && !\in_array($pid, $obj_ids)) {
                    $obj_ids[] = $pid;
                }
            }
        } else {
            if (!\in_array($awhere['dce_views_where_operator'], array('EXISTS', 'NOT EXISTS'))) {
                if (!\in_array($awhere['dce_views_where_operator'], array('IS NULL', 'IS NOT NULL'))) {
                    foreach ($search_values as $akey => $search_value) {
                        if ($akey) {
                            $search_query .= ' ' . $awhere['dce_views_where_rule'] . ' ';
                        }
                        $search_query .= ' ( ';
                        if ($is_meta) {
                            $search_query .= $obj_first . 'm.meta_value ';
                        } else {
                            $search_query .= $awhere['dce_views_where_field'] . ' ';
                        }
                        $search_query .= $awhere['dce_views_where_operator'] . ' ';
                        if ($awhere['dce_views_where_operator'] != 'IN' && $awhere['dce_views_where_operator'] != 'NOT IN') {
                            $search_query .= '"';
                        }
                        if ($awhere['dce_views_where_operator'] == 'LIKE' || $awhere['dce_views_where_operator'] == 'NOT LIKE') {
                            $search_query .= '%';
                            $search_value = $wpdb->esc_like($search_value);
                        }
                        if ($awhere['dce_views_where_operator'] == 'IN' || $awhere['dce_views_where_operator'] == 'NOT IN') {
                            $search_query .= $search_value;
                        } else {
                            $search_query .= esc_sql($search_value);
                        }
                        if ($awhere['dce_views_where_operator'] == 'LIKE' || $awhere['dce_views_where_operator'] == 'NOT LIKE') {
                            $search_query .= '%';
                        }
                        if ($awhere['dce_views_where_operator'] != 'IN' && $awhere['dce_views_where_operator'] != 'NOT IN') {
                            $search_query .= '"';
                        }
                        if ($is_meta) {
                            if ($awhere['dce_views_where_operator'] == 'IN' || $awhere['dce_views_where_operator'] == 'NOT IN') {
                                foreach ($search_values as $avalue) {
                                    $avalue = \str_replace('"', '', $avalue);
                                    $search_query .= " OR " . $obj_first . "m.meta_value " . \trim(\str_replace('IN', '', $awhere['dce_views_where_operator'])) . " LIKE '%s:" . \strlen($avalue) . ":\"" . $avalue . "\"%'";
                                    // serialized data s:1:"5";
                                    $search_query .= " OR " . $obj_first . "m.meta_value " . \trim(\str_replace('IN', '', $awhere['dce_views_where_operator'])) . " LIKE '%i:" . $avalue . ";%'";
                                    // serialized integer i:123;;
                                }
                            }
                        }
                        $search_query .= ' )';
                    }
                    if ($is_meta) {
                        $search_query .= ' )';
                    }
                }
            }
            $results = $wpdb->get_results($search_query);
            if (!empty($results)) {
                foreach ($results as $key => $aobj) {
                    $pid = \intval($aobj->ID);
                    if ($is_meta) {
                        if ($settings['dce_views_object'] == 'post' && $aobj->post_type == 'revision') {
                            if (!\in_array(\intval($aobj->post_parent), $obj_ids)) {
                                $obj_ids[] = \intval($aobj->post_parent);
                            }
                        } else {
                            if (!\in_array($pid, $obj_ids)) {
                                $obj_ids[] = $pid;
                            }
                        }
                    } else {
                        if (!\in_array($pid, $obj_ids)) {
                            $obj_ids[] = $pid;
                        }
                    }
                }
            }
        }
        if (empty($obj_ids) && !$retry && isset($awhere['dce_views_where_form_type']) && $awhere['dce_views_where_form_type'] == 'text') {
            $words = \explode(' ', $search_value);
            if (\count($words) > 2) {
                foreach ($words as $key => $value) {
                    if (\strlen($value) > 3) {
                        $obj_ids = \array_merge($obj_ids, $this->get_obj_ids($awhere, $value));
                    }
                }
            }
        }
        return $obj_ids;
    }
    public function get_field_value($dce_obj, $object_id, $afield, $settings = null)
    {
        if (!$settings) {
            $settings = $this->get_settings_for_display();
        }
        $get_value = 'get_' . $settings['dce_views_object'] . '_value';
        $field_value = Helper::$get_value($object_id, $afield['dce_views_select_field']);
        if ($afield['dce_views_select_render'] == 'image') {
            $field_value = wp_get_attachment_image($field_value, $afield['dce_views_select_image_size']);
        } elseif ($afield['dce_views_select_render'] == 'rewrite' && $afield['dce_views_select_rewrite']) {
            $field_value_rewrite = $afield['dce_views_select_rewrite'];
            $field_value_rewrite = Tokens::replace_var_tokens($field_value_rewrite, 'field', $field_value);
            $field_value_rewrite = Tokens::replace_var_tokens($field_value_rewrite, $settings['dce_views_object'], $dce_obj);
            $field_value_rewrite = Tokens::replace_var_tokens($field_value_rewrite, 'object', $dce_obj);
            $field_value_rewrite = Helper::get_dynamic_value($field_value_rewrite);
            $field_value = $field_value_rewrite;
        }
        if ($field_value) {
            if (!empty($afield['dce_views_select_link'])) {
                switch ($settings['dce_views_object']) {
                    case 'post':
                        $link = get_permalink($object_id);
                        break;
                    case 'user':
                        $author_id = get_the_author_meta('ID');
                        $link = get_author_posts_url($object_id ?? $author_id);
                        break;
                    case 'term':
                        $link = get_term_link($object_id);
                        break;
                }
                $field_value = '<a href="' . $link . '">' . $field_value . '</a>';
            }
            if ($afield['dce_views_select_render'] == 'auto' && $afield['dce_views_select_tag']) {
                $field_value = '<' . $afield['dce_views_select_tag'] . '>' . $field_value . '</' . $afield['dce_views_select_tag'] . '>';
            }
        } else {
            if ($afield['dce_views_select_no_results']) {
                $field_value = $afield['dce_views_select_no_results'];
                if ($settings['dce_views_object'] == 'user') {
                    $field_value = Tokens::user_to_author($field_value);
                }
                $field_value = Helper::get_dynamic_value($field_value);
            }
        }
        return $field_value;
    }
    public function _form_active_filters($filters = array(), $settings = array())
    {
        if (!empty($settings['dce_views_where_form_active_filters'])) {
            if (!empty($settings['dce_views_where_form'])) {
                if (!empty($filters) || $settings['dce_views_where_form_active_filters_no_message'] || $settings['dce_views_where_form_ajax']) {
                    echo '<div class="dce-view-form-filters-wrapper">';
                    if (!empty($filters) || $settings['dce_views_where_form_active_filters_no_message']) {
                        echo '<div class="dce-view-form-filters-row">';
                        if (!empty($filters)) {
                            foreach ($filters as $fkey => $afilter) {
                                $value = $afilter;
                                $remove_filter_url = '';
                                if ($settings['dce_views_where_form_active_filters_remove']) {
                                    $current_url = add_query_arg('removed', 'views');
                                    $fget_a = $fkey . (\is_array($value) ? '%5B1%5D' : '') . '=';
                                    $fget_b = $fkey . (\is_array($value) ? '%5B0%5D' : '') . '=';
                                    $fget = $fkey . (\is_array($value) ? '[]' : '') . '=';
                                    if (\strpos($current_url, $fget) !== \false || \strpos($current_url, $fget_a) !== \false || \strpos($current_url, $fget_b) !== \false) {
                                        $remove_filter_url = remove_query_arg($fkey);
                                    }
                                }
                                $label = $this->get_field_label($fkey, $settings);
                                $value = $this->get_option_label($fkey, $value, $settings);
                                $has_remove = $remove_filter_url || \Elementor\Plugin::$instance->editor->is_edit_mode();
                                echo '<span class="dce-view-form-filter">' . $label . ': ' . $value . ($has_remove ? ' |<a href="' . $remove_filter_url . '" data-field="' . $fkey . '">&nbsp;×&nbsp;</a>' : '') . '</span>';
                            }
                        } else {
                            if ($settings['dce_views_where_form_active_filters_no_message']) {
                                echo '<div class="dce-view-form-no-filter">' . $settings['dce_views_where_form_active_filters_no_message'] . '</div>';
                            }
                        }
                        echo '</div>';
                    }
                    echo '</div>';
                }
            }
            if (!empty($settings['dce_views_where_form_ajax'])) {
                $jkey = 'dce_' . $this->get_type() . '_view_' . $this->get_id() . '_ajax_filter';
                \ob_start();
                ?>
				<script id="<?php 
                echo $jkey;
                ?>">
					(function ($) {
					var <?php 
                echo $jkey;
                ?> = function ($scope, $) {
					if ($scope.hasClass("elementor-element-<?php 
                echo $this->get_id();
                ?>")) {
					$scope.on('click', '.dce-view-form-filter a', function() {
					var field_container = '.elementor-element-<?php 
                echo $this->get_id();
                ?> .dce-view-form-wrapper .elementor-field-group-' + jQuery(this).data('field');
					jQuery(field_container).find('input').val('');
					jQuery(field_container).find('select').val('');
					jQuery(field_container).find('input:checked').prop('checked', false);
					jQuery(field_container).find('input,select').trigger('change');
					return false;
					});
					}
					};
					$(window).on("elementor/frontend/init", function () {
					elementorFrontend.hooks.addAction("frontend/element_ready/<?php 
                echo $this->get_name();
                ?>.default", <?php 
                echo $jkey;
                ?>);
					});
					})(jQuery, window);</script>
				<?php 
                $add_js = \ob_get_clean();
                $add_js = \DynamicContentForElementor\Assets::dce_enqueue_script($jkey, $add_js);
            }
        }
    }
    public function get_field_label($fkey, $settings = array())
    {
        if (\substr($fkey, 0, \strlen('taxonomy_')) == 'taxonomy_') {
            $label = \substr($fkey, \strlen('taxonomy_'));
            $tax = get_taxonomy($label);
            if ($tax && \is_object($tax) && \get_class($tax) == 'WP_Taxonomy') {
                $label = $tax->label;
            }
            return $label;
        }
        if (!empty($settings['dce_views_where_form'])) {
            foreach ($settings['dce_views_where_form'] as $afield) {
                if ($afield['dce_views_where_form_field'] == $fkey && $afield['dce_views_where_form_label']) {
                    return $afield['dce_views_where_form_label'];
                }
            }
        }
        $get_field = 'get_' . $settings['dce_views_object'] . '_fields';
        $fields = Helper::$get_field($fkey, \false, \false);
        if (!empty($fields)) {
            foreach ($fields as $akey => $field) {
                if ($akey == $fkey) {
                    return $field;
                }
            }
        }
        return $fkey;
    }
    public function get_option_label($fkey, $value, $settings = array())
    {
        if (\substr($fkey, 0, \strlen('taxonomy_')) == 'taxonomy_') {
            $label = \substr($fkey, \strlen('taxonomy_'));
            $terms_id = $value;
            if (!\is_array($value)) {
                $terms_id = array($value);
            }
            $tmp = array();
            foreach ($terms_id as $aterm) {
                $term = get_term($aterm);
                if ($term && \is_object($term) && \get_class($term) == 'WP_Term') {
                    $tmp[] = $term->name;
                }
            }
            $value = Helper::to_string($tmp);
        }
        if (!\is_array($value)) {
            $value = array($value);
        }
        foreach ($value as $akey => $avalue) {
            if (!empty($settings['dce_views_where_form'])) {
                foreach ($settings['dce_views_where_form'] as $afield) {
                    if ($afield['dce_views_where_form_field'] == $fkey) {
                        $options = $this->get_field_options($fkey, $afield['dce_views_where_form_value'], $afield['dce_views_where_form_preselect'], $afield['dce_views_where_form_value_format']);
                        if (!empty($options[$avalue])) {
                            $value[$akey] = $options[$avalue]['value'];
                        }
                    }
                }
            }
        }
        $value = Helper::to_string($value);
        return $value;
    }
    public function get_field_type($field, $type)
    {
        $dce_views_where_form_type = $type;
        if (\substr($field, 0, \strlen('taxonomy_')) == 'taxonomy_') {
            if ($type == 'auto') {
                $dce_views_where_form_type = 'select';
            }
        } elseif (Helper::is_acf_active() && Helper::is_acf($field)) {
            $field_conf = Helper::get_acf_field_settings($field);
            if ('auto' === $type) {
                $dce_views_where_form_type = $field_conf['type'];
                if ($field_conf['type'] == 'true_false') {
                    $dce_views_where_form_type = 'checkbox';
                }
                if ($field_conf['type'] == 'button_group') {
                    $dce_views_where_form_type = 'radio';
                }
            }
        }
        return $dce_views_where_form_type;
    }
    public function get_field_options($field, $value, $presel = '', $format = 'reset', $orderby = 'name', $order = 'ASC')
    {
        $input_values = array();
        $options = \explode(\PHP_EOL, $value);
        $options = \array_filter($options);
        if (!empty($options)) {
            foreach ($options as $akey => $aopt) {
                $aopt = \trim($aopt);
                $option = \explode('|', $aopt, 2);
                if ($format == 'end') {
                    $akey = \trim(\end($option));
                    $avalue = \reset($option);
                } else {
                    $akey = \trim(\reset($option));
                    $avalue = \end($option);
                }
                $asel = $this->is_value_selected($field, $akey, $presel);
                $input_values[$akey] = array('key' => $akey, 'value' => $avalue, 'selected' => $asel);
            }
        }
        if (empty($input_values)) {
            if (\substr($field, 0, \strlen('taxonomy_')) == 'taxonomy_') {
                // TAXONOMY
                if (empty(\trim($value))) {
                    $taxonomy = \substr($field, \strlen('taxonomy_'));
                    $taxonomies_terms = Helper::get_taxonomy_terms($taxonomy, \false, '', \false, $orderby, $order);
                    foreach ($taxonomies_terms as $akey => $term_title) {
                        if ($akey) {
                            $asel = \false;
                            $asel = $this->is_value_selected('taxonomy_' . $taxonomy, $akey, $presel);
                            $input_values[$akey] = array('key' => $akey, 'value' => $term_title, 'selected' => $asel);
                        }
                    }
                }
            } else {
                // ACF
                if (Helper::is_acf_active() && Helper::is_acf($field)) {
                    $field_conf = Helper::get_acf_field_settings($field);
                    if ($field_conf && isset($field_conf['choices']) && !empty($field_conf['choices'])) {
                        foreach ($field_conf['choices'] as $akey => $avalue) {
                            $asel = $this->is_value_selected($field, $akey, $field_conf['default_value']);
                            $input_values[$akey] = array('key' => $akey, 'value' => $avalue, 'selected' => $asel);
                        }
                    }
                }
            }
        }
        return $input_values;
    }
    public function _fields($settings, $dce_obj, $object_id)
    {
        foreach ($settings['dce_views_select_fields'] as $key => $afield) {
            echo '<div class="dce-view-field-' . $afield['dce_views_select_field'] . ' ' . $afield['dce_views_select_class_wrapper'] . '">';
            if ($afield['dce_views_select_label']) {
                if ($afield['dce_views_select_label_inline']) {
                    echo '<label';
                } else {
                    echo '<div';
                }
                echo ' class="dce-view-field-label">' . $afield['dce_views_select_label'];
                if ($afield['dce_views_select_label_inline']) {
                    echo '</label>';
                } else {
                    echo '</div>';
                }
            }
            if ($afield['dce_views_select_label'] && $afield['dce_views_select_label_inline']) {
                echo '<span';
            } else {
                echo '<div';
            }
            echo ' class="dce-view-field-value ' . $afield['dce_views_select_class_value'] . '">' . $this->get_field_value($dce_obj, $object_id, $afield, $settings) . '</div>';
            if ($afield['dce_views_select_label'] && $afield['dce_views_select_label_inline']) {
                echo '</span>';
            } else {
                echo '</div>';
            }
        }
    }
    public function pre_get_posts_query_filter($wp_query)
    {
        do_action("elementor/query/{$this->get_id()}", $wp_query, $this);
    }
    public function add_filters()
    {
        $settings = $this->get_settings_for_display();
        if (!empty($settings['dce_views_order_by'])) {
            foreach ($settings['dce_views_order_by'] as $aorder) {
                if ($aorder['dce_views_order_field'] == 'post_status') {
                    $filter = function () use($aorder) {
                        global $wpdb;
                        return $wpdb->prefix . 'posts.' . $aorder['dce_views_order_field'] . ' ' . $aorder['dce_views_order_field_sort'];
                    };
                    add_filter('posts_orderby', $filter);
                }
            }
        }
    }
    /**
     * Validate Object
     *
     * @param string $object
     * @return string
     */
    protected function validate_object($object)
    {
        if (\in_array($object, \array_keys($this->get_objects()), \true)) {
            return $object;
        }
        return '';
    }
    public function remove_filters()
    {
        $settings = $this->get_settings_for_display();
        if (!empty($settings['dce_views_order_by'])) {
            foreach ($settings['dce_views_order_by'] as $aorder) {
                if ($aorder['dce_views_order_field'] == 'post_status') {
                    $filter = function () use($aorder) {
                        return $aorder['dce_views_order_field'] . ' ' . $aorder['dce_views_order_field_sort'];
                    };
                    remove_filter('posts_orderby', $filter);
                }
            }
        }
    }
}
