<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use DynamicContentForElementor\Helper;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class DynamicTitle extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * Get Style Depends
     *
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['dce-dynamic-title'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_title', ['label' => $this->get_title()]);
        $this->add_control('html_tag', ['label' => __('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(), 'default' => 'h3', 'separator' => 'before']);
        $this->add_responsive_control('align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};'], 'condition' => ['enable_divider' => '']]);
        $this->add_control('link_to', ['label' => __('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => __('None', 'dynamic-content-for-elementor'), 'home' => __('Home URL', 'dynamic-content-for-elementor'), 'post' => 'Post URL', 'parent' => 'Parent Page', 'custom' => __('Custom URL', 'dynamic-content-for-elementor')]]);
        $this->add_control('link', ['label' => __('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => __('https://your-link.com', 'dynamic-content-for-elementor'), 'condition' => ['link_to' => 'custom'], 'default' => ['url' => ''], 'show_label' => \false]);
        $this->add_control('enable_divider', ['label' => __('Dividers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'label_on' => __('Show', 'dynamic-content-for-elementor'), 'label_off' => __('Hide', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'render_type' => 'template', 'prefix_class' => 'dce-title-divider-']);
        $this->add_control('enable_masking', ['label' => __('Masking', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'label_on' => __('Show', 'dynamic-content-for-elementor'), 'label_off' => __('Hide', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'render_type' => 'template', 'prefix_class' => 'dce-title-mask-']);
        $this->end_controls_section();
        $this->start_controls_section('section_title_dividers', ['label' => __('Dividers', 'dynamic-content-for-elementor'), 'condition' => ['enable_divider' => 'yes']]);
        $this->add_control('style_dividers', ['label' => __('Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['solid' => __('Solid', 'dynamic-content-for-elementor'), 'double' => __('Double', 'dynamic-content-for-elementor'), 'dotted' => __('Dotted', 'dynamic-content-for-elementor'), 'dashed' => __('Dashed', 'dynamic-content-for-elementor')], 'default' => 'solid', 'selectors' => ['{{WRAPPER}} .dce-divider:after, {{WRAPPER}} .dce-divider:before' => 'border-top-style: {{VALUE}};']]);
        $this->add_control('weight_dividers', ['label' => __('Weight', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 1, 'max' => 50]], 'selectors' => ['{{WRAPPER}} .dce-divider:after, {{WRAPPER}} .dce-divider:before' => 'border-top-width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('color_dividers', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-divider:after, {{WRAPPER}} .dce-divider:before' => 'border-top-color: {{VALUE}};']]);
        $this->add_responsive_control('width_dividers', ['label' => __('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['%', 'px'], 'range' => ['px' => ['max' => 600, 'min' => 10, 'step' => 1]], 'default' => ['size' => 25, 'unit' => '%'], 'tablet_default' => ['unit' => '%'], 'mobile_default' => ['unit' => '%'], 'selectors' => ['{{WRAPPER}} .dce-divider:after, {{WRAPPER}} .dce-divider:before' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('gap_dividers', ['label' => __('Gap', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 15], 'range' => ['px' => ['min' => 2, 'max' => 80]], 'selectors' => ['{{WRAPPER}} .dce-title-divider' => 'padding-right: {{SIZE}}{{UNIT}}; padding-left: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('space_dividers', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 15], 'range' => ['px' => ['min' => -100, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-title-divider' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('divider_position', ['label' => __('Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'default' => 'center', 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right'], 'top' => ['title' => __('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], 'bottom' => ['title' => __('Bottom', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom']], 'prefix_class' => 'dce-divider-position%s-']);
        $this->add_responsive_control('divider_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'prefix_class' => 'dce-title-align%s-', 'selectors' => ['{{WRAPPER}} .dce-title-divider' => 'text-align: {{VALUE}};'], 'condition' => ['divider_position' => ['top', 'bottom']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_title_masking', ['label' => __('Masking', 'dynamic-content-for-elementor'), 'condition' => ['enable_masking' => 'yes']]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'background_masking', 'types' => ['classic', 'gradient'], 'fields_options' => ['background' => ['frontend_available' => \true], 'video_link' => ['frontend_available' => \true]], 'selector' => '{{WRAPPER}} span']);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => __('Source', 'dynamic-content-for-elementor')]);
        $this->add_control('data_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'description' => __('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => __('Same', 'dynamic-content-for-elementor'), 'label_off' => __('other', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_control('other_post_source', ['label' => __('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->add_control('other_post_parent', ['label' => __('From post parent', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Title', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-title' => 'color: {{VALUE}};', '{{WRAPPER}} a' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} .dce-title']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'text_shadow', 'selector' => '{{WRAPPER}} .dce-title']);
        $this->add_control('blend_mode', ['label' => __('Blend Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Normal', 'dynamic-content-for-elementor'), 'multiply' => __('Multiply', 'dynamic-content-for-elementor'), 'screen' => __('Screen', 'dynamic-content-for-elementor'), 'overlay' => __('Overlay', 'dynamic-content-for-elementor'), 'darken' => __('Darken', 'dynamic-content-for-elementor'), 'lighten' => __('Lighten', 'dynamic-content-for-elementor'), 'color-dodge' => __('Color Dodge', 'dynamic-content-for-elementor'), 'saturation' => __('Saturation', 'dynamic-content-for-elementor'), 'color' => __('Color', 'dynamic-content-for-elementor'), 'difference' => __('Difference', 'dynamic-content-for-elementor'), 'exclusion' => __('Exclusion', 'dynamic-content-for-elementor'), 'hue' => __('Hue', 'dynamic-content-for-elementor'), 'luminosity' => __('Luminosity', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}}' => 'mix-blend-mode: {{VALUE}}'], 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_rollhover', ['label' => __('Rollover', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['link_to!' => 'none']]);
        $this->add_control('hover_color', ['label' => __('Hover Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a:hover' => 'color: {{VALUE}};'], 'condition' => ['link_to!' => 'none']]);
        $this->add_control('hover_animation', ['label' => __('Hover Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION, 'condition' => ['link_to!' => 'none']]);
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
        $id_page = Helper::get_the_id($settings['other_post_source'], $settings['other_post_parent']);
        if (!empty($settings['other_post_source']) || !empty($settings['other_post_parent'])) {
            $title = wp_kses_post(get_the_title($id_page));
        } else {
            $queried_object = get_queried_object();
            switch (\true) {
                case $queried_object instanceof \WP_Post:
                    $title = wp_kses_post(get_the_title());
                    break;
                case $queried_object instanceof \WP_Term:
                    $title = single_term_title('', \false);
                    break;
                case $queried_object instanceof \WP_Post_Type:
                    $title = post_type_archive_title('', \false);
                    break;
                case $queried_object instanceof \WP_User:
                    $title = $queried_object->display_name;
                    break;
                default:
                    return;
            }
        }
        if (empty($title)) {
            return;
        }
        $this->set_render_attribute('title', 'class', 'dce-title');
        // Link
        switch ($settings['link_to']) {
            case 'custom':
                $link = \false;
                if (!empty($settings['link']['url'])) {
                    $link = esc_url($settings['link']['url']);
                }
                break;
            case 'post':
                $link = get_the_permalink($id_page);
                break;
            case 'parent':
                $link = \false;
                $id_page_parent = wp_get_post_parent_id($id_page);
                if ($id_page_parent) {
                    $link = get_the_permalink($id_page_parent);
                }
                break;
            case 'home':
                $link = get_home_url();
                break;
            case 'none':
            default:
                $link = \false;
                break;
        }
        if ($link) {
            $this->set_render_attribute('link', 'href', $link);
            if (!empty($settings['link']) && $settings['link']['is_external']) {
                $this->set_render_attribute('link', 'target', '_blank');
            }
            if (!empty($settings['link']['nofollow'])) {
                $this->set_render_attribute('link', 'rel', 'nofollow');
            }
        }
        if (!empty($settings['hover_animation'])) {
            $this->add_render_attribute('title', 'class', 'elementor-animation-' . $settings['hover_animation']);
        }
        echo '<' . Helper::validate_html_tag($settings['html_tag']) . ' ' . $this->get_render_attribute_string('title') . '>';
        if (!empty($settings['enable_divider'])) {
            echo '<div class="dce-divider"><span class="dce-title-divider">';
        }
        if ($link) {
            echo '<a ' . $this->get_render_attribute_string('link') . '>';
        }
        echo $title;
        if ($link) {
            echo '</a>';
        }
        if (!empty($settings['enable_divider'])) {
            echo '</span></div>';
        }
        echo '</' . Helper::validate_html_tag($settings['html_tag']) . '>';
    }
}
