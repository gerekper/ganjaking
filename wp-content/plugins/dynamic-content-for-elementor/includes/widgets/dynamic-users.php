<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Controls\Group_Control_Filters_CSS;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicUsers extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['imagesloaded', 'isotope', 'dce-dynamic_users'];
    }
    public function get_style_depends()
    {
        return ['dce-dynamicUsers'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $user_meta = Helper::get_user_metas();
        $this->start_controls_section('options_users', ['label' => $this->get_title()]);
        $this->add_control('single_autor', ['label' => __('Single Author', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING]);
        $this->add_control('author_user', ['label' => __('Show only the author of the current post', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('results_per_page', ['label' => __('Results per page', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '-1', 'separator' => 'before', 'condition' => ['author_user' => '']]);
        $this->add_control('pagination_enable', ['label' => __('Pagination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['author_user' => '']]);
        $this->add_control('users_orderby', ['label' => __('Order by', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['ID' => __('ID', 'dynamic-content-for-elementor'), 'login' => __('Login', 'dynamic-content-for-elementor'), 'nicename' => __('Nicename', 'dynamic-content-for-elementor'), 'email' => __('Email', 'dynamic-content-for-elementor'), 'url' => __('Url', 'dynamic-content-for-elementor'), 'registered' => __('Registered', 'dynamic-content-for-elementor'), 'display_name' => __('Display Name', 'dynamic-content-for-elementor'), 'post_count' => __('Post Count', 'dynamic-content-for-elementor'), 'meta_value' => __('Meta Value (String)', 'dynamic-content-for-elementor'), 'meta_value_num' => __('Meta Value (Number)', 'dynamic-content-for-elementor')], 'default' => 'ID', 'condition' => ['author_user' => '']]);
        $this->add_control('users_orderby_meta', ['label' => __('Meta value', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Field key or Name', 'dynamic-content-for-elementor'), 'query_type' => 'fields', 'dynamic' => ['active' => \false], 'label_block' => \true, 'object_type' => 'user', 'default' => 'nickname', 'condition' => ['users_orderby' => ['meta_value', 'meta_value_num'], 'author_user' => '']]);
        $this->add_control('users_order', ['label' => __('Order', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['ASC' => __('Ascending', 'dynamic-content-for-elementor'), 'DESC' => __('Descending', 'dynamic-content-for-elementor')], 'toggle' => \false, 'default' => 'ASC', 'condition' => ['author_user' => '']]);
        $this->add_control('filters_heading', ['label' => __('Filters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['author_user' => '']]);
        $this->add_control('roles', ['label' => __('Roles', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => Helper::get_roles(), 'multiple' => \true, 'condition' => ['author_user' => '']]);
        $this->add_control('metaFilter', ['label' => __('User Field Filter', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Field key or Name', 'dynamic-content-for-elementor'), 'query_type' => 'fields', 'dynamic' => ['active' => \false], 'label_block' => \true, 'object_type' => 'user', 'default' => 'none', 'condition' => ['author_user' => '']]);
        $this->add_control('metaValue_filter', ['label' => __('Meta value', 'dynamic-content-for-elementor'), 'description' => __('The value of the filter. Use comma as separator for multiple values', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['metaFilter!' => '', 'author_user' => '']]);
        $this->add_control('exclude_heading', ['label' => __('Exclude', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['author_user' => '']]);
        $this->add_control('exclude_user', ['label' => __('Exclude selected users', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select user', 'dynamic-content-for-elementor'), 'label_block' => \true, 'multiple' => \true, 'query_type' => 'users', 'condition' => ['author_user' => '']]);
        $this->add_control('exclude_author_post', ['label' => __('Exclude users without articles', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['author_user' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_render', ['label' => __('Render', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('users_render', ['label' => __('Render mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['byitems' => ['title' => __('Users', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list'], 'template' => ['title' => __('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'byitems', 'separator' => 'before']);
        $this->add_control('users_render_template', ['label' => __('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'condition' => ['users_render' => 'template']]);
        $this->add_control('layout_position', ['label' => __('Layout', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '', 'tablet_default' => '', 'mobile_default' => '', 'render_type' => 'template', 'options' => ['' => __('None', 'dynamic-content-for-elementor'), 'top' => __('Top', 'dynamic-content-for-elementor'), 'left' => __('Left', 'dynamic-content-for-elementor'), 'right' => __('Right', 'dynamic-content-for-elementor'), 'alternate' => __('Alternate', 'dynamic-content-for-elementor')], 'prefix_class' => 'layout-user-position-', 'condition' => ['users_render' => 'byitems']]);
        $this->add_responsive_control('image_rate', ['label' => __('Rate', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 50, 'unit' => '%'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 600]], 'selectors' => ['{{WRAPPER}} .dce-user_image' => 'width: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-user_content' => 'width: calc( 100% - {{SIZE}}{{UNIT}} );'], 'condition' => ['layout_position' => ['left', 'right', 'alternate']]]);
        $this->add_responsive_control('content_padding', ['label' => __('Content Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-user_content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['layout_position!' => '']]);
        $repeater = new Repeater();
        $repeater->start_controls_tabs('tabs_repeater');
        $repeater->start_controls_tab('tab_content', ['label' => __('Meta', 'dynamic-content-for-elementor')]);
        $repeater->add_control('meta', ['label' => __('Meta', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'userlogin', 'options' => ['userlogin' => __('User Login', 'dynamic-content-for-elementor'), 'email' => __('Email', 'dynamic-content-for-elementor'), 'nickname' => __('Nick Name', 'dynamic-content-for-elementor'), 'displayname' => __('Display Name', 'dynamic-content-for-elementor'), 'lastname' => __('Last Name', 'dynamic-content-for-elementor'), 'firstname' => __('First Name', 'dynamic-content-for-elementor'), 'description' => __('Description', 'dynamic-content-for-elementor'), 'avatar' => __('Avatar', 'dynamic-content-for-elementor'), 'website' => __('WebSite', 'dynamic-content-for-elementor'), 'role' => __('Role', 'dynamic-content-for-elementor'), 'custommeta' => __('Custom Meta', 'dynamic-content-for-elementor'), 'ID' => __('ID', 'dynamic-content-for-elementor'), 'attachments' => __('Attachments', 'dynamic-content-for-elementor'), 'articles' => __('Posts', 'dynamic-content-for-elementor'), 'button' => __('Button', 'dynamic-content-for-elementor')]]);
        $repeater->add_control('text_before', ['label' => __('Text before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $repeater->add_control('text_button', ['label' => __('Text before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Read more', 'dynamic-content-for-elementor'), 'condition' => ['meta' => 'button']]);
        $repeater->add_control('meta_key', ['label' => __('All Meta', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Field key or Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'fields', 'object_type' => 'user', 'default' => 'nickname', 'condition' => ['meta' => 'custommeta']]);
        $repeater->add_control('article_post_type', ['label' => __('Post Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => Helper::get_public_post_types(), 'multiple' => \true, 'label_block' => \true, 'default' => 'post', 'condition' => ['meta' => 'articles']]);
        $repeater->add_control('attachment_url', ['label' => __('Add Link to Attachment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['meta' => 'attachments']]);
        $repeater->add_control('articles_url', ['label' => __('Add Link to Post', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['meta' => 'articles']]);
        $repeater->add_control('link_to_page', ['label' => __('Link to page', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['meta!' => ['attachments', 'articles']]]);
        $repeater->add_control('link_to', ['label' => __('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'user_page', 'options' => ['user_page' => __('User page', 'dynamic-content-for-elementor'), 'other_url' => __('Meta URL', 'dynamic-content-for-elementor'), 'custom' => __('Custom URL', 'dynamic-content-for-elementor')], 'condition' => ['link_to_page' => 'yes', 'meta!' => ['attachments', 'articles']]]);
        $repeater->add_control('custom_link', ['label' => __('Link url', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => __('https://your-link.com', 'dynamic-content-for-elementor'), 'condition' => ['link_to' => 'custom'], 'default' => ['url' => ''], 'show_label' => \false]);
        $user_meta_url = Helper::get_acf_field_urlfile();
        $repeater->add_control('meta_field_url', ['label' => __('Meta Field Url', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $user_meta_url, 'default' => __('Select the field...', 'dynamic-content-for-elementor'), 'condition' => ['link_to_page' => 'yes', 'link_to' => 'other_url', 'meta!' => ['attachments', 'articles']]]);
        $repeater->add_control('meta_field_url_target_blank', ['label' => __('Target blank', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['link_to_page' => 'yes', 'link_to' => 'other_url', 'meta!' => ['attachments', 'articles'], 'meta_field_url!' => '']]);
        $repeater->add_control('inline_item', ['label' => __('Inline', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['meta!' => ['attachments', 'articles']]]);
        $repeater->add_control('hide_item', ['label' => __('Hide item', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['meta!' => ['attachments', 'articles', 'button']]]);
        $repeater->end_controls_tab();
        $repeater->start_controls_tab('tab_style', ['label' => __('Style', 'dynamic-content-for-elementor')]);
        //
        $repeater->add_responsive_control('align_item', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}' => 'text-align: {{VALUE}};']]);
        $repeater->add_control('padding_item', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $repeater->add_control('color_item', ['label' => __('Text color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}.tx-el, {{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}.tx-el a' => 'color: {{VALUE}};'], 'condition' => ['meta!' => ['attachments', 'articles', 'avatar']]]);
        $repeater->add_control('hover_color_item', ['label' => __('Hover color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}.tx-el:hover a' => 'color: {{VALUE}};'], 'condition' => ['meta!' => ['attachments', 'articles', 'avatar'], 'link_to_page' => 'yes']]);
        $repeater->add_control('bgcolor_item', ['label' => __('Background color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}' => 'background-color: {{VALUE}};'], 'condition' => ['meta!' => ['attachments', 'articles', 'avatar']]]);
        $repeater->add_control('hover_bgcolor_item', ['label' => __('Background hover color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}:hover' => 'background-color: {{VALUE}};'], 'condition' => ['meta!' => ['attachments', 'articles', 'avatar']]]);
        $repeater->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_item', 'label' => __('Typography item', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}.tx-el', 'condition' => ['meta!' => ['attachments', 'avatar']]]);
        // --------------------------- Attachments
        $repeater->add_responsive_control('columns_grid_attachments', ['label' => __('Columns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '5', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10'], 'render_type' => 'template', 'selectors' => ['{{WRAPPER}} .dce-item-user .item_attach' => 'flex: 0 1 calc( 100% / {{VALUE}} );'], 'condition' => ['meta' => 'attachments']]);
        $repeater->add_control('flex_grow_attachments', ['label' => __('Flex grow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['1' => ['title' => __('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => __('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => 1, 'selectors' => ['{{WRAPPER}} .dce-item-user .item_attach' => 'flex-grow: {{VALUE}};'], 'condition' => ['meta' => 'attachments']]);
        $repeater->add_responsive_control('flexgrid_mode_attachments', ['label' => __('Alignment grid', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'flex-start', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['flex-start' => __('Flex start', 'dynamic-content-for-elementor'), 'flex-end' => __('Flex end', 'dynamic-content-for-elementor'), 'center' => __('Center', 'dynamic-content-for-elementor'), 'space-between' => __('Space Between', 'dynamic-content-for-elementor'), 'space-around' => __('Space Around', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dce-item-user .grid-attach' => 'justify-content: {{VALUE}};'], 'condition' => ['meta' => 'attachments']]);
        $repeater->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'size_attachment', 'label' => __('Image Size', 'dynamic-content-for-elementor'), 'default' => 'thumbnail', 'condition' => ['meta' => 'attachments']]);
        $repeater->add_responsive_control('columns_grid_articles', ['label' => __('Columns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '5', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10'], 'render_type' => 'template', 'selectors' => ['{{WRAPPER}} .dce-item-user .item_article' => 'flex: 0 1 calc( 100% / {{VALUE}} );'], 'condition' => ['meta' => 'articles']]);
        $repeater->add_control('flex_grow_articles', ['label' => __('Flex grow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['1' => ['title' => __('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => __('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => 1, 'selectors' => ['{{WRAPPER}} .dce-item-user .item_article' => 'flex-grow: {{VALUE}};'], 'condition' => ['meta' => 'articles']]);
        $repeater->add_responsive_control('flexgrid_mode_articles', ['label' => __('Alignment grid', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'flex-start', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['flex-start' => __('Flex start', 'dynamic-content-for-elementor'), 'flex-end' => __('Flex end', 'dynamic-content-for-elementor'), 'center' => __('Center', 'dynamic-content-for-elementor'), 'space-between' => __('Space Between', 'dynamic-content-for-elementor'), 'space-around' => __('Space Around', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dce-item-user .grid-articles' => 'justify-content: {{VALUE}};'], 'condition' => ['meta' => 'articles']]);
        $repeater->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'size_articles', 'label' => __('Image Size', 'dynamic-content-for-elementor'), 'default' => 'thumbnail', 'condition' => ['meta' => 'articles']]);
        $repeater->add_control('txbefore_heading', ['label' => __('Text Before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['text_before!' => '']]);
        $repeater->add_control('color_txbefore', ['label' => __('Text before Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}} .tx-before' => 'color: {{VALUE}};', '{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}} a .tx-before' => 'color: {{VALUE}};'], 'condition' => ['text_before!' => '']]);
        $repeater->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_txbefore', 'label' => 'Typography text before', 'selector' => '{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}} .tx-before', 'popover' => \true, 'condition' => ['text_before!' => '']]);
        $repeater->end_controls_tab();
        $repeater->end_controls_tabs();
        $this->add_control('user_meta_items', ['label' => __('User Meta Items', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'default' => [['meta' => 'avatar'], ['meta' => 'userlogin'], ['meta' => 'email'], ['meta' => 'nickname']], 'fields' => $repeater->get_controls(), 'title_field' => '{{{ meta }}}', 'render_type' => 'template', 'condition' => ['users_render' => 'byitems']]);
        $this->end_controls_section();
        $this->start_controls_section('section_pagination', ['label' => __('Pagination', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['pagination_enable' => 'yes']]);
        $this->add_control('pagination_show_numbers', ['label' => __('Show Numbers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('pagination_range', ['label' => __('Range of numbers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 4, 'condition' => ['pagination_show_numbers' => 'yes']]);
        $this->add_control('pagination_show_prevnext', ['label' => __('Show Prev/Next', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'separator' => 'before']);
        $this->add_control('selected_pagination_icon_prevnext', ['label' => __('Icon Prev/Next', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'fa4compatibility' => 'pagination_icon_prevnext', 'default' => ['value' => 'fas fa-long-arrow-alt-right', 'library' => 'fa-solid'], 'recommended' => ['fa-solid' => ['arrow-right', 'angle-right', 'long-arrow-alt-right', 'arrow-alt-circle-right', 'arrow-circle-right', 'caret-right', 'caret-square-right', 'chevron-circle-right', 'chevron-right', 'hand-point-right']], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prev_label', ['label' => __('Previous Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Previous', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_next_label', ['label' => __('Next Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Next', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_show_firstlast', ['label' => __('Show First/Last', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'separator' => 'before']);
        $this->add_control('selected_pagination_icon_firstlast', ['label' => __('Icon First/Last', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'fa4compatibility' => 'pagination_icon_firstlast', 'default' => ['value' => 'fas fa-long-arrow-alt-right', 'library' => 'fa-solid'], 'recommended' => ['fa-solid' => ['arrow-right', 'angle-right', 'long-arrow-alt-right', 'arrow-alt-circle-right', 'arrow-circle-right', 'caret-right', 'caret-square-right', 'chevron-circle-right', 'chevron-right', 'hand-point-right']], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_first_label', ['label' => __('Previous Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('First', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_last_label', ['label' => __('Next Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Last', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_show_progression', ['label' => __('Show Progression', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_grid', ['label' => __('Skin', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('posts_style', ['label' => __('Skin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'flexgrid', 'frontend_available' => \true, 'render_type' => 'template', 'options' => ['simplegrid' => __('Row', 'dynamic-content-for-elementor'), 'flexgrid' => __('Grid', 'dynamic-content-for-elementor'), 'grid' => __('Masonry', 'dynamic-content-for-elementor')]]);
        $this->add_responsive_control('columns_grid_flex', ['label' => __('Columns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'render_type' => 'template', 'default' => '5', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7'], 'selectors' => ['{{WRAPPER}} .dce-grid-users.flexgrid .dce-item-user' => 'flex: 0 1 calc( 100% / {{VALUE}} );', '{{WRAPPER}} .dce-grid-users.grid .dce-item-user' => 'width: calc( 100% / {{VALUE}} ); display: inline-block;'], 'condition' => ['posts_style' => ['grid', 'flexgrid']]]);
        $this->add_control('flex_grow', ['label' => __('Flex grow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['1' => ['title' => __('1', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => __('0', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => 0, 'selectors' => ['{{WRAPPER}} .dce-item-user' => 'flex-grow: {{VALUE}};'], 'condition' => ['posts_style' => 'flexgrid']]);
        $this->add_responsive_control('flexgrid_mode', ['label' => __('Alignment grid', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'flex-start', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['flex-start' => __('Flex start', 'dynamic-content-for-elementor'), 'flex-end' => __('Flex end', 'dynamic-content-for-elementor'), 'center' => __('Center', 'dynamic-content-for-elementor'), 'space-between' => __('Space Between', 'dynamic-content-for-elementor'), 'space-around' => __('Space Around', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dce-grid-users' => 'justify-content: {{VALUE}};'], 'condition' => ['posts_style' => 'flexgrid', 'flex_grow' => '0']]);
        $this->add_control('filters_enable', ['label' => __('Show Filters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['posts_style' => 'grid']]);
        $this->add_responsive_control('grid_space', ['label' => __('Column Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 15, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', 'rem'], 'range' => ['rem' => ['min' => 0, 'max' => 10], 'px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-item-user' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('row_grid_space', ['label' => __('Row Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 15, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', 'rem'], 'range' => ['rem' => ['min' => 0, 'max' => 10], 'px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-item-user' => 'padding-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('layout_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['none' => ['title' => __('None', 'dynamic-content-for-elementor'), 'icon' => 'eicon-close'], 'left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => 'none', 'toggle' => \false, 'selectors' => ['{{WRAPPER}} .dce-grid-users' => 'text-align: {{VALUE}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_filters', ['label' => __('Filters', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['filters_enable' => 'yes']]);
        $this->add_control('filters_meta', ['label' => __('Filters Meta', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Field key or Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'fields', 'object_type' => 'user', 'default' => 'none']);
        $this->add_control('separator_filter', ['label' => __('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => ' / ', 'condition' => ['filters_enable' => 'yes']]);
        $this->add_responsive_control('filters_align', ['label' => __('Filters Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => 'left', 'selectors' => ['{{WRAPPER}} .dce-users-filters' => 'text-align: {{VALUE}};'], 'condition' => ['filters_enable' => 'yes']]);
        $this->add_control('filters_color', ['label' => __('Filters Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-users-filters .users-filters-item a' => 'color: {{VALUE}};']]);
        $this->add_control('filters_color_hover', ['label' => __('Filters Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-users-filters .users-filters-item a:hover' => 'color: {{VALUE}};']]);
        $this->add_control('filters_color_active', ['label' => __('Filters Color Active', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#990000', 'selectors' => ['{{WRAPPER}} .dce-users-filters .users-filters-item.filter-active a' => 'color: {{VALUE}};']]);
        $this->add_control('filters_color_divisore', ['label' => __('Divider Filters Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-users-filters .filters-divider' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_filters', 'label' => __('Typography Filters', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-users-filters']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_filters_divider', 'label' => __('Typography Divider', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-users-filters .filters-divider']);
        $this->add_responsive_control('filters_padding_items', ['label' => __('Space between filters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 5, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 0, 'max' => 100], 'px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-users-filters .filters-divider' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('filters_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-users-filters' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('filters_move_divider', ['label' => __('Vertical Shift Divider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => -100, 'max' => 100], 'px' => ['min' => -100, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-users-filters .filters-divider' => 'top: {{SIZE}}{{UNIT}}; position: relative;']]);
        $this->end_controls_section();
        $this->start_controls_section('section_avatar', ['label' => __('Avatar', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['users_render' => 'byitems']]);
        $this->add_control('avatar_size', ['label' => __('Avatar Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 80], 'range' => ['px' => ['min' => 10, 'max' => 1200, 'step' => 1]], 'size_units' => ['px', '%']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_avatar', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-item-user .user-avatar img']);
        $this->add_responsive_control('border_radius_avatar', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-item-user .user-avatar, {{WRAPPER}} .dce-item-user .user-avatar img, {{WRAPPER}} .dce-overlay_hover, {{WRAPPER}} .dce-overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('padding_avatar', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-item-user .user-avatar, {{WRAPPER}} .dce-overlay_hover, {{WRAPPER}} .dce-overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'box_shadow_avatar', 'selector' => '{{WRAPPER}} .dce-item-user .user-avatar']);
        $this->add_group_control(Group_Control_Filters_CSS::get_type(), ['name' => 'filters_avatar', 'label' => __('Filters', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-item-user .user-avatar']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_pagination', ['label' => __('Pagination', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['pagination_enable' => 'yes']]);
        $this->add_responsive_control('pagination_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['flex-start' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'flex-end' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => 'center', 'selectors' => ['{{WRAPPER}} .dce-pagination' => 'text-align: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'pagination_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination']);
        $this->add_responsive_control('pagination_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination' => 'padding-top: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('pagination_spacing', ['label' => __('Horizontal Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'margin-right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('pagination_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('pagination_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('pagination_heading_colors', ['label' => __('Colors', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->start_controls_tabs('pagination_colors');
        $this->start_controls_tab('pagination_text_colors', ['label' => __('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('pagination_text_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_background_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pagination_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a']);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_text_colors_hover', ['label' => __('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('pagination_hover_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination a:hover' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_background_hover_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination a:hover' => 'background-color: {{VALUE}};']]);
        $this->add_control('pagination_hover_border_color', ['label' => __('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['pagination_border_border!' => ''], 'selectors' => ['{{WRAPPER}} .dce-pagination a:hover' => 'border-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_text_colors_current', ['label' => __('Current', 'dynamic-content-for-elementor')]);
        $this->add_control('pagination_current_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination span.current' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_background_current_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination span.current' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control('pagination_heading_prevnext', ['label' => __('Prev/Next', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_responsive_control('pagination_spacing_prevnext', ['label' => __('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext' => 'margin-left: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_responsive_control('pagination_icon_spacing_prevnext', ['label' => __('Icon Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev .fa' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext .fa' => 'margin-left: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_responsive_control('pagination_icon_size_prevnext', ['label' => __('Icon Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev .fa' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext .fa' => 'font-size: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->start_controls_tabs('pagination_prevnext_colors');
        $this->start_controls_tab('pagination_prevnext_text_colors', ['label' => __('Normal', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_text_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_background_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pagination_prevnext_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext', 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_prevnext_text_colors_hover', ['label' => __('Hover', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_hover_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev:hover, {{WRAPPER}} .dce-pagination .pagenext:hover' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_background_hover_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev:hover, {{WRAPPER}} .dce-pagination .pagenext:hover' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_hover_border_color', ['label' => __('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev:hover, {{WRAPPER}} .dce-pagination .pagenext:hover' => 'border-color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes', 'pagination_prevnext_border_border!' => '']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control('pagination_heading_firstlast', ['label' => __('First/last', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_responsive_control('pagination_spacing_firstlast', ['label' => __('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagelast' => 'margin-left: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->start_controls_tabs('pagination_firstlast_colors');
        $this->start_controls_tab('pagination_firstlast_text_colors', ['label' => __('Normal', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_text_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_background_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pagination_firstlast_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast', 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_firstlast_text_colors_hover', ['label' => __('Hover', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_hover_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst:hover, {{WRAPPER}} .dce-pagination .pagelast:hover' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_background_hover_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst:hover, {{WRAPPER}} .dce-pagination .pagelast:hover' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_hover_border_color', ['label' => __('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst:hover, {{WRAPPER}} .dce-pagination .pagelast:hover' => 'border-color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes', 'pagination_firstlast_border_border!' => '']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control('pagination_heading_progression', ['label' => __('Progression', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_responsive_control('pagination_spacing_progression', ['label' => __('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'margin-right: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->start_controls_tabs('pagination_progression_colors');
        $this->start_controls_tab('pagination_progression_text_colors', ['label' => __('Normal', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_text_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_background_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pagination_progression_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination .progression', 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_progression_text_colors_hover', ['label' => __('Hover', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_hover_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_background_hover_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_hover_border_color', ['label' => __('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'border-color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes', 'pagination_firstlast_border_border!' => '']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        if ($settings['filters_enable'] && $settings['posts_style'] == 'grid') {
            $tag_filter = 'span';
            $divider = '';
            // Retrieve the list of elements that belong to a given user_meta
            $list_filters = array();
            $users = get_users();
            $list_isotope_filters = [];
            foreach ($users as $user) {
                $valore_meta = get_user_meta($user->ID, $settings['filters_meta'], \true);
                if ($valore_meta != '') {
                    $list_isotope_filters[$valore_meta] = $user->{$settings['filters_meta']};
                }
            }
            echo '<div class="dce-users-filters">';
            $cont_f = 1;
            if (!empty($list_isotope_filters)) {
                echo '<' . $tag_filter . ' class="users-filters-item filter-active">' . wp_kses_post($divider) . '<a href="#" data-filter="*">' . __('All', 'dynamic-content-for-elementor') . '</a></' . $tag_filter . '>';
                foreach ($list_isotope_filters as $filter) {
                    // L'etichetta del filtro
                    $filternome = $filter;
                    // Lo slug del filtro
                    $filterslug = sanitize_title($filter);
                    // poi dovrò convertire in stringa semplificata da trattini e minuscole
                    $divider = '<span class="filters-divider">' . wp_kses_post($settings['separator_filter']) . '</span>';
                    if ($filternome != '') {
                        echo '<' . $tag_filter . ' class="users-filters-item">' . $divider . '<a href="#" data-filter=".' . $filterslug . '">' . $filternome . '</a></' . $tag_filter . '>';
                    }
                    $cont_f++;
                }
            }
            echo '</div>';
        }
        $styleClass = '';
        if ($settings['posts_style'] == 'simplegrid') {
            $styleClass = ' simplegrid';
        } else {
            if ($settings['posts_style'] == 'flexgrid') {
                $styleClass = ' flexgrid';
            } else {
                if ($settings['posts_style'] == 'grid') {
                    $styleClass = ' grid';
                }
            }
        }
        if ($settings['metaFilter'] == 'none') {
            $settings['metaFilter'] = array();
        }
        $userargs = array('blog_id' => get_current_blog_id(), 'number' => $settings['results_per_page']);
        global $paged;
        $paged = $this->get_current_page();
        $userargs['paged'] = $paged;
        if (!empty($settings['roles'])) {
            $userargs['role__in'] = $settings['roles'];
        }
        if (!empty($settings['exclude_user'])) {
            $userargs['exclude'] = $settings['exclude_user'];
        }
        if (!empty($settings['users_orderby_meta'])) {
            $userargs['meta_key'] = $settings['users_orderby_meta'];
        }
        if (!empty($settings['users_orderby'])) {
            $userargs['orderby'] = $settings['users_orderby'];
        }
        if (!empty($settings['users_order'])) {
            $userargs['order'] = $settings['users_order'];
        }
        $array_value = Helper::str_to_array(',', $settings['metaValue_filter']);
        if ($settings['metaFilter'] && !empty($array_value)) {
            if (Helper::is_user_meta($settings['metaFilter'])) {
                $metaFilter = array('relation' => 'OR');
                foreach ($array_value as $key => $value) {
                    $metaFilter[] = array('key' => $settings['metaFilter'], 'value' => $value, 'compare' => '=');
                }
                $userargs['meta_query'] = $metaFilter;
            } else {
                $user_ids = array();
                foreach ($array_value as $key => $value) {
                    $user = get_user_by($settings['metaFilter'], $value);
                    if ($user) {
                        $user_ids[] = $user->ID;
                    }
                }
                $userargs['include'] = $user_ids;
            }
        }
        if ($settings['author_user'] == 'yes') {
            $author = get_the_author_meta('ID');
            $userargs['include'] = array($author);
        }
        $users = get_users($userargs);
        // Calculate the number of users
        $userargs['number'] = -1;
        $number_of_users = \count(get_users($userargs));
        echo '<div class="dce-grid-users' . $styleClass . '">';
        foreach ($users as $user) {
            $user_meta_items = $settings['user_meta_items'];
            $filters_string_class = '';
            if ($settings['filters_enable']) {
                $filters_string_class = $user->{$settings['filters_meta']};
                $filters_string_class = ' ' . sanitize_title($filters_string_class);
            }
            $exclude_author_post = 1;
            if ($settings['exclude_author_post']) {
                $exclude_author_post = $this->have_articles($user->ID);
            }
            if ($exclude_author_post) {
                echo '<div class="dce-item-user' . $filters_string_class . '">';
                if ($settings['users_render'] == 'byitems') {
                    if (!empty($user_meta_items)) {
                        if ($settings['layout_position']) {
                            echo '<div class="dce-user_image">';
                            foreach ($settings['user_meta_items'] as $item) {
                                $classElItem = ' elementor-repeater-item-' . $item['_id'];
                                $openLink = $this->get_link_a($item, $user);
                                $closeLink = $openLink ? '</a>' : '';
                                if ($item['meta'] == 'avatar') {
                                    echo '<div class="user-avatar' . $classElItem . '">' . $openLink . get_avatar($user->user_email, $settings['avatar_size']['size']) . $closeLink . '</div>';
                                }
                            }
                            echo '</div>';
                        }
                        echo '<div class="dce-user_content">';
                        foreach ($settings['user_meta_items'] as $item) {
                            $classElItem = ' elementor-repeater-item-' . $item['_id'];
                            $inlineItem = '';
                            if ($item['inline_item'] == 'yes') {
                                $inlineItem = ' inline-useritem';
                                $classElItem .= $inlineItem;
                            }
                            $openLink = $this->get_link_a($item, $user);
                            $closeLink = $openLink ? '</a>' : '';
                            $show_item = \true;
                            if ($item['hide_item']) {
                                $show_item = \false;
                            }
                            $textBefore = '';
                            if ($item['text_before'] != '' && $show_item) {
                                $textBefore = '<span class="tx-before">' . $item['text_before'] . '</span>';
                            }
                            $user_data_view = '';
                            if ($item['meta'] == 'ID' && $show_item) {
                                $user_data_view = $user->ID;
                            } elseif ($item['meta'] == 'userlogin' && $show_item) {
                                $user_data_view = $user->user_login;
                            } elseif ($item['meta'] == 'nickname' && $show_item) {
                                $user_data_view = $user->nickname;
                            } elseif ($item['meta'] == 'displayname' && $show_item) {
                                $user_data_view = $user->display_name;
                            } elseif ($item['meta'] == 'firstname' && $show_item) {
                                $user_data_view = $user->first_name;
                            } elseif ($item['meta'] == 'lastname' && $show_item) {
                                $user_data_view = $user->last_name;
                            } elseif ($item['meta'] == 'description' && $show_item) {
                                $user_data_view = $user->description;
                            } elseif ($item['meta'] == 'email' && $show_item) {
                                $user_data_view = $user->user_email;
                            } elseif ($item['meta'] == 'website' && $show_item) {
                                $user_data_view = $user->url;
                            } elseif ($item['meta'] == 'avatar' && $show_item) {
                                $user_data_view = get_avatar($user->user_email, $settings['avatar_size']['size']);
                            } elseif ($item['meta'] == 'role' && $show_item) {
                                $user_data_view = $user->roles[0];
                            } elseif ($item['meta'] == 'custommeta' && $show_item) {
                                $user_data_view = $item['meta_key'];
                            } elseif ($item['meta'] == 'button' && $show_item) {
                                $user_data_view = $item['text_button'];
                            }
                            if ($item['meta'] == 'ID') {
                                echo '<div class="user-id tx-el' . $classElItem . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'userlogin') {
                                echo '<div class="user-userlogin tx-el' . $classElItem . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'nickname' && !empty($user_data_view)) {
                                echo '<div class="user-nickname tx-el' . $classElItem . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'displayname' && !empty($user_data_view)) {
                                echo '<div class="user-displayname tx-el' . $classElItem . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'firstname' && !empty($user_data_view)) {
                                echo '<div class="user-firstname tx-el' . $classElItem . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'lastname' && !empty($user_data_view)) {
                                echo '<div class="user-lastname tx-el' . $classElItem . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'description' && !empty($user_data_view)) {
                                echo '<div class="user-description tx-el' . $classElItem . '">' . $openLink . $textBefore . esc_html($user_data_view) . $openLink . '</div>';
                            } elseif ($item['meta'] == 'email') {
                                echo '<div class="user-email tx-el' . $classElItem . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'website' && !empty($user_data_view)) {
                                echo '<div class="user-website tx-el' . $classElItem . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'avatar' && $settings['layout_position'] == '') {
                                echo '<div class="user-avatar' . $classElItem . '">' . $openLink . $textBefore . $user_data_view . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'role') {
                                echo '<div class="user-role tx-el' . $classElItem . '">' . $openLink . $textBefore . esc_html($user_data_view) . ${$closeLink} . '</div>';
                            } elseif ($item['meta'] == 'button') {
                                echo '<div class="user-button tx-el' . $classElItem . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'custommeta') {
                                $metak = $item['meta_key'];
                                if (!empty($metak)) {
                                    echo '<div class="user-custommeta tx-el' . $classElItem . '">' . $openLink . $textBefore;
                                    if (!empty($user_data_view)) {
                                        echo get_user_meta($user->ID, $metak, \true);
                                    }
                                    echo $closeLink . '</div>';
                                }
                            } elseif ($item['meta'] == 'attachments') {
                                $user_ids_att = array();
                                $user_ids_att[] = $user->ID;
                                echo '<div class="user-attachments' . $classElItem . '">';
                                $att = $this->get_attachments($textBefore, $user_ids_att, $item['size_attachment_size'], $item['attachment_url']);
                                echo '</div>';
                            } elseif ($item['meta'] == 'articles') {
                                $user_ids_att = array();
                                $user_ids_att[] = $user->ID;
                                echo '<div class="user-articles' . $classElItem . '">';
                                $att = $this->get_articles($textBefore, $user_ids_att, $item['size_articles_size'], $item['article_post_type'], $item['articles_url']);
                                echo '</div>';
                            }
                        }
                        echo '</div>';
                    }
                } else {
                    if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                        $inlinecss = 'inlinecss="true"';
                    } else {
                        $inlinecss = '';
                    }
                    echo do_shortcode('[dce-elementor-template user_id="' . $user->ID . '" author_id="' . $user->ID . '" id="' . $settings['users_render_template'] . '" ' . $inlinecss . ']');
                }
                echo '</div>';
            }
        }
        echo '</div>';
        if ($settings['pagination_enable'] && $settings['results_per_page'] != '-1') {
            Helper::numeric_query_pagination(\intval(\ceil($number_of_users / $settings['results_per_page'])), $settings);
        }
    }
    protected function get_attachments($tx_before, $users, $size_attach, $is_attachment_url)
    {
        $acfList = [];
        $get_attachments = get_posts(array('author__in' => $users, 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => 'any', 'orderby' => 'title'));
        echo '<div class="grid-attach">';
        if (!empty($get_attachments)) {
            echo $tx_before;
            foreach ($get_attachments as $media) {
                echo '<div class="item_attach">';
                if ($is_attachment_url == 'yes') {
                    echo '<a href="' . get_permalink($media->ID) . '">';
                }
                echo wp_get_attachment_image($media->ID, $size_attach);
                if ($is_attachment_url == 'yes') {
                    echo '</a>';
                }
                echo '</div>';
            }
        }
        echo '</div>';
    }
    protected function have_articles($userId)
    {
        $get_articles = get_posts(array('author__in' => $userId, 'post_type' => 'any', 'numberposts' => -1, 'post_status' => 'publish', 'public' => \true));
        return \count($get_articles);
    }
    protected function get_articles($tx_before, $users, $size_art, $type_art, $is_article_url)
    {
        $acfList = [];
        $get_articles = get_posts(array('author__in' => $users, 'post_type' => \DynamicContentForElementor\Helper::validate_post_type($type_art), 'numberposts' => -1, 'post_status' => 'publish', 'public' => \true, 'orderby' => 'title'));
        echo '<div class="grid-articles">';
        if (!empty($get_articles)) {
            echo $tx_before;
            foreach ($get_articles as $art) {
                $featuredImageID = get_post_thumbnail_id($art->ID);
                echo '<div class="item_article">';
                if ($is_article_url == 'yes') {
                    echo '<a href="' . get_permalink($art->ID) . '">';
                }
                if ($featuredImageID) {
                    echo wp_get_attachment_image($featuredImageID, $size_art);
                } else {
                    echo '<img src="' . \Elementor\Utils::get_placeholder_image_src() . '" />';
                }
                echo '<div class="tit-art tx-el">' . wp_kses_post(get_the_title($art->ID)) . '</div>';
                if ($is_article_url == 'yes') {
                    echo '</a>';
                }
                echo '</div>';
            }
        }
        echo '</div>';
    }
    public function get_link_a($item, $user)
    {
        $urlToPage = '';
        $target = '';
        $openLink = '';
        if ($item['link_to_page']) {
            if ($item['link_to'] == 'other_url' && $item['meta_field_url']) {
                $urlToPage = $user->{$item['meta_field_url']};
                if (isset($item['meta_field_url_target_blank']) && $item['meta_field_url_target_blank']) {
                    $target = 'target="_blank"';
                }
            } elseif ($item['link_to'] == 'user_page') {
                $urlToPage = get_author_posts_url($user->ID);
            } elseif ($item['link_to'] == 'custom') {
                if (!empty($item['custom_link']['url'])) {
                    $urlToPage = esc_url($item['custom_link']['url']);
                } else {
                    $urlToPage = \false;
                }
                $target = !empty($item['custom_link']['is_external']) ? 'target="_blank"' : '';
            }
            if ($item['link_to_page'] == 'yes' && $urlToPage != '') {
                $openLink = '<a data-dnc="layout_position" href="' . $urlToPage . '" ' . $target . '>';
            }
        }
        return $openLink;
    }
    public function get_current_page()
    {
        if ('' === $this->get_settings('pagination_enable')) {
            return 1;
        }
        return \max(1, get_query_var('paged'), get_query_var('page'));
    }
}
