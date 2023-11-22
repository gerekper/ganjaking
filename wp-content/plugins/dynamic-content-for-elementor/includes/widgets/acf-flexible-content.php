<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use DynamicContentForElementor\Helper;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class AcfFlexibleContent extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return [];
    }
    public function get_style_depends()
    {
        return [];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => $this->get_title()]);
        $this->add_control('flexible_field', ['label' => __('Select ACF Flexible Content Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'flexible_content', 'dynamic' => ['active' => \false]]);
        $this->add_control('flexible_field_from', ['label' => __('Retrieve the field from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'current_post', 'options' => ['current_post' => __('Current Post', 'dynamic-content-for-elementor'), 'current_user' => __('Current User', 'dynamic-content-for-elementor'), 'current_author' => __('Current Author', 'dynamic-content-for-elementor'), 'current_term' => __('Current Term', 'dynamic-content-for-elementor'), 'options_page' => __('Options Page', 'dynamic-content-for-elementor')]]);
        $repeater_layout = new \Elementor\Repeater();
        $repeater_layout->start_controls_tabs('layout_repeater');
        $repeater_layout->add_control('layout', ['type' => 'ooo_query', 'label' => __('Layout', 'dynamic-content-for-elementor'), 'placeholder' => __('Select the layout', 'dynamic-content-for-elementor'), 'query_type' => 'acf_flexible_content_layouts', 'label_block' => \true]);
        $repeater_layout->add_control('display_mode', ['label' => __('Display mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['html' => ['title' => __('HTML & Tokens', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-code'], 'template' => ['title' => __('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'template']);
        $repeater_layout->add_control('html', ['label' => __('HTML & Tokens', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'default' => '[ROW]', 'description' => __('Type here your content, you can use HTML and Tokens.', 'dynamic-content-for-elementor'), 'condition' => ['display_mode' => 'html']]);
        $repeater_layout->add_control('template_id', ['label' => __('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select Template', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'dynamic' => ['active' => \false], 'object_type' => 'elementor_library', 'condition' => ['display_mode' => 'template']]);
        $this->add_control('layouts', ['label' => __('Show these layouts', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater_layout->get_controls(), 'title_field' => '{{{layout}}}', 'prevent_empty' => \false, 'item_actions' => ['add' => \true, 'duplicate' => \true, 'remove' => \true, 'sort' => \false]]);
        $this->end_controls_section();
        $this->start_controls_section('section_toggle_style', ['label' => $this->get_title(), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} ']);
        $this->add_control('color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}}' => 'color: {{VALUE}};']]);
        $this->add_responsive_control('alignment', ['label' => __('Global Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings) || empty($settings['layouts'])) {
            return;
        }
        switch ($settings['flexible_field_from']) {
            case 'current_post':
                $id = get_the_ID();
                break;
            case 'current_user':
                $user_id = get_current_user_id();
                $id = 'user_' . $user_id;
                break;
            case 'current_author':
                $user_id = get_the_author_meta('ID');
                $id = 'user_' . $user_id;
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
                $id = 'options';
                break;
        }
        $defined_layouts = \array_column($settings['layouts'], 'layout');
        if (have_rows($settings['flexible_field'], $id)) {
            while (have_rows($settings['flexible_field'], $id)) {
                the_row();
                if (\in_array(get_row_layout(), $defined_layouts)) {
                    $key_layout = \array_search(get_row_layout(), $defined_layouts);
                    if ('html' === $settings['layouts'][$key_layout]['display_mode']) {
                        $sub_fields_tokens = Helper::get_acf_flexible_content_sub_fields_by_row($settings['flexible_field'], get_row_index());
                        $html = $settings['layouts'][$key_layout]['html'];
                        $html = Helper::get_dynamic_value($html);
                        echo '<div>';
                        echo \DynamicContentForElementor\Tokens::replace_var_tokens($html, 'ROW', $sub_fields_tokens);
                        echo '</div>';
                    } elseif ('template' === $settings['layouts'][$key_layout]['display_mode']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $inlinecss = 'inlinecss="true"';
                        } else {
                            $inlinecss = '';
                        }
                        $template_id = $settings['layouts'][$key_layout]['template_id'];
                        echo do_shortcode('[dce-elementor-template id="' . $template_id . '" ' . $inlinecss . ']');
                    }
                }
            }
        }
    }
}
