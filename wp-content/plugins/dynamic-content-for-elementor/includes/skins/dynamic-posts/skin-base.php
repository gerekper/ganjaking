<?php

namespace DynamicContentForElementor\Includes\Skins;

use DynamicContentForElementor\DynamicQuery;
use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
abstract class Skin_Base extends Elementor_Skin_Base
{
    protected $current_permalink;
    protected $current_id;
    protected $counter = 0;
    protected $depended_scripts;
    protected $depended_styles;
    public function get_script_depends()
    {
        return $this->depended_scripts;
    }
    public function get_style_depends()
    {
        return $this->depended_styles;
    }
    public function register_controls_layout(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        // Block Style
        $this->register_style_controls();
        // Pagination Style
        $this->register_style_pagination_controls();
        // Infinite Scroll Style
        $this->register_style_infinitescroll_controls();
    }
    /**
     * Get Parent or throw an error
     *
     * @return \DynamicContentForElementor\Widgets\DynamicPostsBase
     */
    protected function get_parent()
    {
        /**
         * @var \DynamicContentForElementor\Widgets\DynamicPostsBase|null $parent
         */
        $parent = $this->parent;
        if ($parent === null) {
            throw new \Error('Skin Parent is NULL');
        }
        return $parent;
    }
    protected function register_style_pagination_controls()
    {
        $this->start_controls_section('section_style_pagination', ['label' => __('Pagination', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['_skin!' => 'carousel', 'pagination_enable' => 'yes', 'infiniteScroll_enable' => '']]);
        $this->add_control('pagination_heading_style', ['label' => __('Pagination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('pagination_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['flex-start' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'flex-end' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => 'center', 'selectors' => ['{{WRAPPER}} .dce-pagination' => 'justify-content: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'pagination_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination']);
        $this->add_responsive_control('pagination_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination-top' => 'padding-bottom: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination-bottom' => 'padding-top: {{SIZE}}{{UNIT}};']]);
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
    protected function register_style_infinitescroll_controls()
    {
        $this->start_controls_section('section_style_infiniteScroll', ['label' => __('Infinite Scroll', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['infiniteScroll_enable' => 'yes']]);
        $this->add_responsive_control('infiniteScroll_spacing', ['label' => __('Spacing status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .infiniteScroll' => 'margin-top: {{SIZE}}{{UNIT}};']]);
        $this->add_control('infiniteScroll_heading_button_style', ['label' => __('Button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_responsive_control('infiniteScroll_button_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['flex-start' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'flex-end' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => 'center', 'selectors' => ['{{WRAPPER}} div.infiniteScroll' => 'justify-content: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->start_controls_tabs('infiniteScroll_button_colors');
        $this->start_controls_tab('infiniteScroll_button_text_colors', ['label' => __('Normal', 'dynamic-content-for-elementor'), 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_control('infiniteScroll_button_text_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .infiniteScroll button' => 'color: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_control('infiniteScroll_button_background_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .infiniteScroll button' => 'background-color: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->end_controls_tab();
        $this->start_controls_tab('infiniteScroll_button_text_colors_hover', ['label' => __('Hover', 'dynamic-content-for-elementor'), 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_control('infiniteScroll_button_hover_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .infiniteScroll button:hover' => 'color: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_control('infiniteScroll_button_background_hover_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .infiniteScroll button:hover' => 'background-color: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_control('infiniteScroll_button_hover_border_color', ['label' => __('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .infiniteScroll button:hover' => 'border-color: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control('infiniteScroll_button_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .infiniteScroll button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['infiniteScroll_trigger' => 'button'], 'separator' => 'before']);
        $this->add_control('infiniteScroll_button_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .infiniteScroll button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->end_controls_section();
    }
    protected function register_style_controls()
    {
        // Blocks - Style
        $this->start_controls_section('section_blocks_style', ['label' => __('Blocks', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['style_items!' => ['template']]]);
        $this->add_responsive_control('blocks_align', ['label' => __('Text Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => is_rtl() ? 'right' : 'left', 'render_type' => 'template', 'prefix_class' => 'dce-align%s-', 'selectors' => ['{{WRAPPER}} .dce-post-item' => 'text-align: {{VALUE}};'], 'separator' => 'before']);
        $this->add_responsive_control('blocks_align_v', ['label' => __('Vertical Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex-start' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'flex-end' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom'], 'space-between' => ['title' => __('Space Between', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-stretch'], 'space-around' => ['title' => __('Space Around', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-stretch']], 'separator' => 'after', 'selectors' => ['{{WRAPPER}} .dce-post-block, {{WRAPPER}} .dce-item-area' => 'justify-content: {{VALUE}} !important;'], 'condition' => ['v_pos_postitems' => ['', 'stretch']]]);
        $this->add_control('blocks_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-post-item .dce-post-block' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'blocks_border', 'selector' => '{{WRAPPER}} .dce-post-item .dce-post-block']);
        $this->add_responsive_control('blocks_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-post-item .dce-post-block' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('blocks_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-post-item .dce-post-block' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'blocks_boxshadow', 'selector' => '{{WRAPPER}} .dce-post-item .dce-post-block']);
        // Vertical Alternate
        $this->add_control('dis_alternate', ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'separator' => 'before', 'raw' => '<img src="' . DCE_URL . 'assets/img/skins/alternate.png" />', 'content_classes' => 'dce-skin-dis', 'condition' => ['grid_type' => ['flex']]]);
        $this->add_responsive_control('blocks_alternate', ['label' => __('Vertical Alternate', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}}.dce-col-3 .dce-post-item:nth-child(3n+2) .dce-post-block, {{WRAPPER}}:not(.dce-col-3) .dce-post-item:nth-child(even) .dce-post-block' => 'margin-top: {{SIZE}}{{UNIT}};'], 'condition' => ['grid_type' => ['flex']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_fallback_style', ['label' => __('No Results Behaviour', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['fallback!' => '', 'fallback_type' => 'text']]);
        $this->add_responsive_control('fallback_align', ['label' => __('Text Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => is_rtl() ? 'right' : 'left', 'selectors' => ['{{WRAPPER}} .dce-posts-fallback' => 'text-align: {{VALUE}};'], 'separator' => 'before']);
        $this->add_control('fallback_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-posts-fallback' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fallback_typography', 'selector' => '{{WRAPPER}} .dce-posts-fallback']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'fallback_text_shadow', 'selector' => '{{WRAPPER}} .dce-posts-fallback']);
        $this->end_controls_section();
    }
    // Render main
    public function render()
    {
        $this->get_parent()->query_posts();
        $query = $this->get_parent()->get_query();
        $this->counter = 0;
        // Add WP_Query args to a data attribute so you can retrieve it for debug on the Elementor Editor
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $this->get_parent()->add_render_attribute('container', 'data-dce-debug-query-args', wp_json_encode($this->get_parent()->get_query_args()));
        }
        // RTL
        if ($this->get_parent()->get_settings_for_display('rtl')) {
            $this->get_parent()->add_render_attribute('container', 'class', 'dce-rtl');
        }
        // Add Search and Filter Pro class when used to make it compatible with the plugin
        $this->add_search_filter_class();
        $fallback = $this->get_parent()->get_settings_for_display('fallback');
        if ($this->get_parent()->get_settings('infiniteScroll_enable') && $this->get_parent()->get_settings('pagination_enable') && 'rand' === $this->get_parent()->get_settings('orderby') && current_user_can('edit_posts')) {
            Helper::notice('', __('Infinite Scroll does not work correctly if you set the order randomly. Please choose another sorting type. This notice is not visible to your visitors.', 'dynamic-content-for-elementor'));
        }
        if ($this->get_parent()->get_settings('infiniteScroll_enable') && $this->get_parent()->get_settings('pagination_enable') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            Helper::notice('', __('Infinite Scroll is not displayed correctly in the Elementor editor due to technical limitations but works correctly in the frontend.', 'dynamic-content-for-elementor'));
        }
        if ('masonry' === $this->get_instance_value('grid_type') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            Helper::notice('', __('Masonry is not displayed correctly in the Elementor editor due to technical limitations but works correctly in the frontend.', 'dynamic-content-for-elementor'));
        }
        if ('grid-filters' === $this->get_parent()->get_settings('_skin') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            Helper::notice('', __('Grid with Filters Skin is not displayed correctly in the Elementor editor due to technical limitations but works correctly in the frontend.', 'dynamic-content-for-elementor'));
        }
        if (!empty($this->get_parent()->get_settings_for_display('template_id')) && \Elementor\Plugin::$instance->editor->is_edit_mode() && 'loop' === get_post_meta($this->get_parent()->get_settings_for_display('template_id'), '_elementor_template_type', \true)) {
            Helper::notice(__('Alert', 'dynamic-content-for-elementor'), __('You have used a Loop template, created by Ele Custom Skin specifically for their features. Please use another type of template to avoid incompatibility.', 'dynamic-content-for-elementor'));
        }
        if (empty($query->found_posts)) {
            if (!empty($fallback)) {
                $this->render_fallback();
            } elseif ('search_filter' === $this->get_parent()->get_settings('query_type')) {
                // Show Container when using Search and Filter Pro to avoid incompatibility issues
                $this->render_loop_start();
                $this->render_loop_end();
            }
            return;
        }
        $query_post_before_loop = \false;
        $post_id = get_the_ID();
        if ($post_id !== \false) {
            $query_post_before_loop = get_post($post_id);
        }
        $this->get_parent()->add_render_attribute('container', 'class', 'dce-fix-background-loop');
        $this->render_loop_start();
        if ($query->in_the_loop) {
            $this->current_permalink = get_permalink();
            $this->current_id = get_the_ID();
            $this->render_post();
        } else {
            while ($query->have_posts()) {
                $query->the_post();
                $this->current_permalink = get_permalink();
                $this->current_id = get_the_ID();
                $this->render_post();
            }
        }
        global $wp_query;
        if ($query_post_before_loop) {
            // in case we are already nested inside a loop wp_reset_postdata would
            // reset the post to the one at the very top and not the one we are in:
            $wp_query->post = $query_post_before_loop;
            $wp_query->reset_postdata();
        } else {
            wp_reset_postdata();
        }
        $this->render_loop_end();
    }
    protected function render_post()
    {
        $style_items = $this->get_parent()->get_settings('style_items');
        $this->render_post_start();
        $skins_with_style_items = ['', 'grid', 'grid-filters', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d', 'accordion'];
        $skin = $this->get_parent()->get_settings('_skin');
        if (\in_array($skin, $skins_with_style_items, \true)) {
            if ('template' === $style_items) {
                $this->render_post_template();
            } elseif ('html_tokens' === $style_items) {
                $this->render_post_html_tokens();
            } else {
                $this->render_post_items();
            }
        } else {
            $this->render_post_items();
        }
        $this->render_post_end();
        $this->counter++;
    }
    protected function render_post_template()
    {
        $template_id = $this->get_parent()->get_settings_for_display('template_id');
        $template_id = apply_filters('wpml_object_id', $template_id, 'elementor_library', \true);
        $templatemode_enable_2 = $this->get_parent()->get_settings('templatemode_enable_2');
        $template_2_id = $this->get_parent()->get_settings_for_display('template_2_id');
        $template_2_id = apply_filters('wpml_object_id', $template_2_id, 'elementor_library', \true);
        $native_templatemode_enable = $this->get_parent()->get_settings('native_templatemode_enable') ?? '';
        if ($native_templatemode_enable && \DynamicContentForElementor\Plugin::instance()->template_system->is_active()) {
            $type_of_posts = get_post_type($this->current_id);
            $cptaxonomy = get_post_taxonomies($this->current_id);
            $options = get_option(DCE_TEMPLATE_SYSTEM_OPTION);
            // 2 - Archive
            $templatesystem_template_key = 'dyncontel_field_archive' . $type_of_posts;
            $post_template_id = $options[$templatesystem_template_key];
            if (isset($cptaxonomy) && \count($cptaxonomy) > 0) {
                $key = $cptaxonomy[0];
                $archive_key = 'dyncontel_field_archive_taxonomy_' . $key;
                // 3 - Taxonomy
                if (isset($options[$archive_key])) {
                    $post_template_id_taxo = $options[$archive_key];
                    if (!empty($post_template_id_taxo) && $post_template_id_taxo > 0) {
                        $templatesystem_template_key = $archive_key;
                    }
                }
                $post_template_id = $options[$templatesystem_template_key];
                // 4 - Terms
                $cptaxonomyterm = get_the_terms($this->current_id, $cptaxonomy[0]);
                if (isset($cptaxonomyterm) && $cptaxonomyterm) {
                    foreach ($cptaxonomyterm as $cpterm) {
                        $term_id = $cpterm->term_id;
                        $post_template_id_term = get_term_meta($term_id, 'dynamic_content_block', \true);
                        if (!empty($post_template_id_term)) {
                            $post_template_id = $post_template_id_term;
                        }
                    }
                }
            }
        } elseif ($templatemode_enable_2) {
            if ($this->counter % 2 == 0) {
                // Even
                $post_template_id = $template_id;
            } else {
                // Odd
                $post_template_id = $template_2_id;
            }
        } else {
            $post_template_id = $template_id;
        }
        if ($post_template_id) {
            $this->render_template($post_template_id);
        }
    }
    /**
     * Render Post HTML & Tokens
     *
     * @return void
     */
    protected function render_post_html_tokens()
    {
        $html_tokens = $this->get_parent()->get_settings('html_tokens_editor');
        if (empty($html_tokens)) {
            return;
        }
        echo Helper::get_dynamic_value($html_tokens);
    }
    /**
     * Render Template
     *
     * @param int $template_id
     * @return void
     */
    protected function render_template(int $template_id)
    {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $inlinecss = 'inlinecss="true"';
        } else {
            $inlinecss = '';
        }
        $parent = $this->get_parent();
        echo do_shortcode('[dce-elementor-template id="' . $template_id . '" post_id="' . $this->current_id . '" ' . $inlinecss . ']');
        $this->parent = $parent;
    }
    protected function render_post_items()
    {
        $_skin = $this->get_parent()->get_settings('_skin');
        $style_items = $this->get_parent()->get_settings('style_items');
        $post_items = $this->get_parent()->get_settings('list_items');
        $hover_animation = $this->get_parent()->get_settings('hover_content_animation');
        $animation_class = !empty($hover_animation) && $style_items != 'float' && $_skin != 'gridtofullscreen3d' ? ' elementor-animation-' . $hover_animation : '';
        $hover_effects = $this->get_parent()->get_settings('hover_text_effect');
        $hoverEffects_class = !empty($hover_effects) && $style_items == 'float' && $_skin != 'gridtofullscreen3d' ? ' dce-hover-effect-' . $hover_effects . ' dce-hover-effect-content dce-close' : '';
        $hoverEffects_start = !empty($hover_effects) && $style_items == 'float' && $_skin != 'gridtofullscreen3d' ? '<div class="dce-hover-effect-' . $hover_effects . ' dce-hover-effect-content dce-close">' : '';
        $hoverEffects_end = !empty($hover_effects) && $style_items == 'float' ? '</div>' : '';
        $imagearea_start = '';
        $contentarea_start = '';
        $area_end = '';
        if ($style_items && $style_items != 'default') {
            $imagearea_start = '<div class="dce-image-area dce-item-area">';
            $contentarea_start = '<div class="dce-content-area dce-item-area' . $animation_class . '">';
            $area_end = '</div>';
            echo $imagearea_start;
            foreach ($post_items as $item) {
                $_id = $item['_id'];
                if ($item['item_id'] == 'item_image') {
                    $this->render_repeater_item_start($item['_id'], $item['item_id'], $item);
                    $this->render_featured_image($item);
                    $this->render_repeater_item_end();
                }
            }
            echo $area_end;
        }
        echo $hoverEffects_start . $contentarea_start;
        foreach ($post_items as $key => $item) {
            $_id = $item['item_id'];
            // se ci sono i 2 wrapper (image-area e content-area) escludo dal render immagine
            if ($_id != 'item_image' && $imagearea_start) {
                $this->render_repeater_item_start($item['_id'], $item['item_id'], $item);
            }
            // se il layout è default renderizzo tutto
            if (!$imagearea_start) {
                $this->render_repeater_item_start($item['_id'], $item['item_id'], $item);
            }
            if ($_id == 'item_image' && !$imagearea_start) {
                $this->render_featured_image($item);
            } elseif ($_id == 'item_title') {
                $this->render_title($item);
            } elseif ($_id == 'item_token') {
                echo Helper::get_dynamic_value($item['item_token_code'] ?? '');
            } elseif ($_id == 'item_addtocart') {
                $this->render_add_to_cart($item);
            } elseif ($_id == 'item_productprice') {
                $this->render_product_price($item);
            } elseif ($_id == 'item_sku') {
                $this->render_product_sku($item);
            } elseif ($_id == 'item_date') {
                $this->render_date($item);
            } elseif ($_id == 'item_author') {
                $this->render_author($item);
            } elseif ($_id == 'item_termstaxonomy') {
                $this->render_terms($item);
            } elseif ($_id == 'item_content') {
                $this->render_content($item);
            } elseif ($_id == 'item_custommeta') {
                $this->render_custom_meta($item);
            } elseif ($_id == 'item_jetengine') {
                $this->render_jetengine($item);
            } elseif ($_id == 'item_metabox') {
                $this->render_metabox($item);
            } elseif ($_id == 'item_readmore') {
                $this->render_read_more($item);
            } elseif ($_id == 'item_posttype') {
                $this->render_post_type($item);
            }
            if ($_id != 'item_image' && $imagearea_start) {
                $this->render_repeater_item_end();
            } elseif (!$imagearea_start) {
                $this->render_repeater_item_end();
            }
        }
        echo $area_end . $hoverEffects_end;
    }
    /**
     * Render Repeater Item - Start
     *
     * @param string $id
     * @param string $item_id
     * @param array<string,mixed> $item_settings
     * @return void
     */
    protected function render_repeater_item_start(string $id, string $item_id, array $item_settings)
    {
        $this->get_parent()->set_render_attribute('dynposts_' . $id, ['class' => ['dce-item', 'dce-' . $item_id, 'elementor-repeater-item-' . $id]]);
        $this->render_responsive_settings($id, $item_settings);
        echo '<div ' . $this->get_parent()->get_render_attribute_string('dynposts_' . $id) . '>';
    }
    /**
     * Render Repeater Item - End
     *
     * @return void
     */
    protected function render_repeater_item_end()
    {
        echo '</div>';
    }
    /**
     * Render Responsive Settings
     *
     * @param string $id
     * @param array<string,mixed> $settings
     * @return void
     */
    protected function render_responsive_settings(string $id, array $settings)
    {
        $active_devices = Helper::get_active_devices_list();
        foreach ($active_devices as $breakpoint_key) {
            /**
             * @var \Elementor\Core\Breakpoints\Breakpoint $breakpoint
             */
            $breakpoint = \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints($breakpoint_key);
            $label = 'desktop' === $breakpoint_key ? __('Desktop', 'dynamic-content-for-elementor') : $breakpoint->get_label();
            if (!empty($settings['hide_' . $breakpoint_key])) {
                $this->get_parent()->add_render_attribute('dynposts_' . $id, 'class', 'elementor-hidden-' . $breakpoint_key);
            }
        }
    }
    /**
     * Render Featured Image
     *
     * @param array<mixed> $settings
     * @return void
     */
    protected function render_featured_image(array $settings)
    {
        $setting_key = $settings['thumbnail_size_size'];
        // alt attribute
        if (!empty(get_the_post_thumbnail_caption())) {
            $alt_attribute = get_the_post_thumbnail_caption();
        } else {
            $alt_attribute = get_the_title() ? esc_html(get_the_title()) : get_the_ID();
        }
        $this->get_parent()->set_render_attribute('featured-image', 'class', 'dce-post-image');
        $image_attr = ['class' => $this->get_image_class(), 'alt' => $alt_attribute];
        $thumbnail_html = wp_get_attachment_image(get_post_thumbnail_id(), $setting_key, \false, $image_attr);
        // Fallback
        if (empty($thumbnail_html) && !empty($settings['featured_image_fallback'])) {
            $thumbnail_html = wp_get_attachment_image($settings['featured_image_fallback']['id'], $setting_key, \false, $image_attr);
        }
        if (empty($thumbnail_html)) {
            return;
        }
        if (!empty($settings['ratio_image']['size'])) {
            $this->get_parent()->set_render_attribute('figure-featured-image', 'data-image-ratio', $settings['ratio_image']['size']);
        }
        if ($settings['use_bgimage']) {
            // Use Featured Image as background
            $this->get_parent()->add_render_attribute('featured-image', 'class', 'dce-post-bgimage');
        }
        if ($settings['use_overlay']) {
            // Use Overlay
            $this->get_parent()->add_render_attribute('featured-image', 'class', 'dce-post-overlayimage');
        }
        if ($this->get_parent()->get_settings('use_overlay_hover')) {
            // Use Overlay Hover
            $this->get_parent()->add_render_attribute('featured-image', 'class', 'dce-post-overlayhover');
        }
        $html_tag = 'div';
        if (!empty($settings['use_link'])) {
            $html_tag = 'a';
            $this->get_parent()->set_render_attribute('featured-image', 'href', $this->current_permalink);
            if (!empty($settings['open_target_blank'])) {
                $this->get_parent()->set_render_attribute('featured-image', 'target', '_blank');
            }
        }
        echo '<' . $html_tag . ' ' . $this->get_parent()->get_render_attribute_string('featured-image') . '>';
        if ($settings['use_bgimage']) {
            // Image as Background
            $image_url = \false;
            if (get_post_thumbnail_id()) {
                $image_url = Group_Control_Image_Size::get_attachment_image_src((string) get_post_thumbnail_id(), 'thumbnail_size', $settings);
            }
            if (empty($image_url) && !empty($settings['featured_image_fallback'])) {
                $image_url = Group_Control_Image_Size::get_attachment_image_src($settings['featured_image_fallback']['id'], 'thumbnail_size', $settings);
            }
            if (!empty($image_url)) {
                echo '<figure ' . $this->get_parent()->get_render_attribute_string('figure-featured-image') . ' class="dce-img dce-bgimage" style="background-image: url(' . $image_url . '); background-repeat: no-repeat; background-size: cover; display: block;"></figure>';
            }
        } else {
            echo '<figure ' . $this->get_parent()->get_render_attribute_string('figure-featured-image') . ' class="dce-img">' . $thumbnail_html . '</figure>';
        }
        echo '</' . $html_tag . '>';
    }
    protected function render_title($settings)
    {
        $html_tag = !empty($settings['html_tag']) ? \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']) : 'h3';
        $title_text = get_the_title() ? wp_kses_post(get_the_title()) : get_the_ID();
        $use_link = $settings['use_link'];
        $open_target_blank = $settings['open_target_blank'];
        echo \sprintf('<%1$s class="dce-post-title">', $html_tag);
        echo $this->render_item_link_text($title_text, $use_link, $this->current_permalink, $open_target_blank);
        echo \sprintf('</%s>', $html_tag);
    }
    protected function render_add_to_cart($settings)
    {
        if ('product' !== get_post_type(get_the_ID())) {
            return;
        }
        $product_id = get_the_ID();
        $product = \wc_get_product($product_id);
        $cart_url = wc_get_cart_url();
        $attribute_button = 'button_addtocart_' . $this->counter;
        if ($product->is_type('simple') || $product->is_type('course')) {
            $button_text = wp_kses_post($settings['add_to_cart_text']);
            $this->get_parent()->add_render_attribute($attribute_button, 'class', ['elementor-button-link', 'elementor-button', 'dce-button']);
            $this->get_parent()->add_render_attribute($attribute_button, 'href', $cart_url . '?add-to-cart=' . $product_id);
            $this->get_parent()->add_render_attribute($attribute_button, 'role', 'button');
            if ('ajax' === $settings['add_to_cart_action']) {
                $this->get_parent()->add_render_attribute($attribute_button, 'class', ['add_to_cart_button', 'ajax_add_to_cart']);
                $this->get_parent()->add_render_attribute($attribute_button, 'data-product_id', (string) $product_id);
                $this->get_parent()->add_render_attribute($attribute_button, 'data-quantity', '1');
            }
            ?>

			<div class="dce-post-button">
				<a <?php 
            echo $this->get_parent()->get_render_attribute_string($attribute_button);
            ?>><?php 
            echo $button_text;
            ?></a>
			</div>

			<?php 
        }
    }
    protected function render_product_price($settings)
    {
        if ('product' !== get_post_type(get_the_ID())) {
            return;
        }
        $product = \wc_get_product(get_the_ID());
        if (!$product) {
            return;
        }
        switch ($settings['price_format']) {
            case 'regular':
                echo \wc_price($product->get_regular_price());
                break;
            case 'sale':
                if ($product->is_on_sale()) {
                    echo \wc_price($product->get_sale_price()) . $product->get_price_suffix();
                } else {
                    echo $product->get_price_html();
                }
                break;
            case 'both':
                echo $product->get_price_html();
                break;
        }
    }
    protected function render_product_sku($settings)
    {
        if ('product' !== get_post_type(get_the_ID())) {
            return;
        }
        $product = \wc_get_product(get_the_ID());
        if (!$product) {
            return;
        }
        if ($product->get_sku()) {
            echo esc_html($product->get_sku());
        }
    }
    protected function render_read_more($settings)
    {
        $readmore_text = wp_kses_post($settings['readmore_text']);
        $readmore_size = $settings['readmore_size'];
        $attribute_button = 'button_' . $this->counter;
        $open_target_blank = $settings['open_target_blank'];
        $this->get_parent()->add_render_attribute($attribute_button, 'href', $this->current_permalink);
        $this->get_parent()->add_render_attribute($attribute_button, 'class', ['elementor-button-link', 'elementor-button', 'dce-button']);
        $this->get_parent()->add_render_attribute($attribute_button, 'role', 'button');
        if (!empty($readmore_size)) {
            $this->get_parent()->add_render_attribute($attribute_button, 'class', 'elementor-size-' . $readmore_size);
        }
        ?>
		<div class="dce-post-button">
			<a <?php 
        echo $this->get_parent()->get_render_attribute_string($attribute_button);
        ?> <?php 
        if ($open_target_blank) {
            echo 'target="_blank"';
        }
        ?>>
			<?php 
        echo $readmore_text;
        ?>
			</a>
		</div>
		<?php 
    }
    protected function render_author($settings)
    {
        $avatar_image_size = $settings['author_image_size'];
        $use_link = $settings['use_link'];
        $author_user_key = $settings['author_user_key'];
        $author = [];
        $avatar_args['size'] = $avatar_image_size;
        $user_id = get_the_author_meta('ID');
        $author['avatar'] = get_avatar_url($user_id, $avatar_args);
        $author['posts_url'] = get_author_posts_url($user_id);
        ?>
		<div class="dce-post-author">
			<div class="dce-author-image">
				<?php 
        foreach ($author_user_key as $akey => $author_value) {
            if ($author_value == 'avatar') {
                ?>
						<div class="dce-author-avatar">
							<img class="dce-img" src="<?php 
                echo $author['avatar'];
                ?>" alt="<?php 
                echo get_the_author_meta('display_name');
                ?>" />
						</div>
					<?php 
            }
        }
        ?>
			</div>
			<div class="dce-author-text">
				<?php 
        foreach ($author_user_key as $akey => $author_value) {
            if ($author_value != 'avatar') {
                echo '<div class="dce-author-' . $author_value . '">' . get_the_author_meta($author_value) . '</div>';
            }
        }
        ?>
			</div>
			<?php 
        echo '</div>';
    }
    protected function render_content($settings)
    {
        $content_type = $settings['content_type'];
        $textcontent_limit = $settings['textcontent_limit'];
        $use_link = $settings['use_link'];
        echo '<div class="dce-post-content">';
        if ($content_type === '1') {
            // Content
            if ($textcontent_limit) {
                echo $this->limit_content($textcontent_limit);
            } else {
                echo wpautop(get_the_content());
            }
        } else {
            // Excerpt
            $post = get_post();
            if ($content_type === 'auto-excerpt') {
                echo get_the_excerpt($post);
            } else {
                echo $post->post_excerpt;
            }
        }
        echo '</div>';
    }
    protected function render_post_type($settings)
    {
        $posttype_label = $settings['posttype_label'];
        $type = get_post_type();
        $postTypeObj = get_post_type_object($type);
        switch ($posttype_label) {
            case 'plural':
                $posttype = $postTypeObj->labels->name;
                break;
            case 'singular':
            default:
                $posttype = $postTypeObj->labels->singular_name;
                break;
        }
        echo '<div class="dce-post-ptype">';
        if (isset($postTypeObj->label)) {
            echo $posttype;
        }
        echo '</div>';
    }
    protected function render_date($settings)
    {
        $date_type = $settings['date_type'];
        $date_format = wp_kses_post($settings['date_format']);
        $icon_enable = $settings['icon_enable'];
        $use_link = $settings['use_link'];
        if (!$date_format) {
            $date_format = get_option('date_format');
        }
        $icon = '';
        if ($icon_enable) {
            $icon = '<i class="dce-post-icon fa fa-calendar" aria-hidden="true"></i> ';
        }
        switch ($date_type) {
            case 'modified':
                $date = get_the_modified_date($date_format, '');
                break;
            case 'publish':
            default:
                $date = get_the_date($date_format, '');
                break;
        }
        ?>
		<div class="dce-post-date"><?php 
        echo $icon . $date;
        ?></div><?php 
    }
    protected function render_custom_meta($settings)
    {
        $custommeta_key = $settings['metafield_key'];
        if (!empty($custommeta_key)) {
            $_id = $settings['_id'];
            $metafield_type = $settings['metafield_type'];
            $image_size_key = $settings['image_size_size'];
            $metafield_button_label = wp_kses_post($settings['metafield_button_label']);
            $metafield_button_size = $settings['metafield_button_size'];
            $metafield_date_format_source = $settings['metafield_date_format_source'];
            $metafield_date_format_display = $settings['metafield_date_format_display'];
            $html_tag_item = Helper::validate_html_tag($settings['html_tag_item']);
            $link_to = $settings['link_to'];
            $link = $settings['link'];
            $attribute_a_link = 'a_link_' . $this->counter;
            $attribute_custommeta_item = 'custommeta_item-' . $this->counter;
            $meta_value = get_post_meta($this->current_id, $custommeta_key, \true);
            if (!$meta_value) {
                return;
            }
            echo '<div class="dce-post-custommeta">';
            $meta_html = '';
            switch ($metafield_type) {
                case 'date':
                    if ($metafield_date_format_source) {
                        if ($metafield_date_format_source == 'timestamp') {
                            $timestamp = $meta_value;
                        } else {
                            $d = \DateTime::createFromFormat($metafield_date_format_source, $meta_value);
                            if ($d) {
                                $timestamp = $d->getTimestamp();
                            } else {
                                $timestamp = \strtotime($meta_value);
                            }
                        }
                    } else {
                        $timestamp = \strtotime($meta_value);
                    }
                    $meta_html = date_i18n($metafield_date_format_display, $timestamp);
                    break;
                case 'image':
                    $image_attr = ['class' => 'dce-img'];
                    $image_html = '';
                    if (\is_string($meta_value)) {
                        if (\is_numeric($meta_value)) {
                            $image_html = wp_get_attachment_image($meta_value, $image_size_key, \false, $image_attr);
                        } else {
                            $image_html = '<img src="' . $meta_value . '" />';
                        }
                    } elseif (\is_numeric($meta_value)) {
                        $image_html = wp_get_attachment_image($meta_value, $image_size_key, \false, $image_attr);
                    } elseif (\is_array($meta_value)) {
                        // TODO ... da valutare come gestire il caso di un'array...
                        $imageSrc = wp_get_attachment_image_src($meta_value['ID'], $image_size_key);
                        $imageSrcUrl = $imageSrc[0] ?? '';
                    }
                    $meta_html = $image_html;
                    break;
                case 'button':
                    $this->get_parent()->set_render_attribute($attribute_a_link, 'href', $meta_value);
                    $this->get_parent()->set_render_attribute($attribute_a_link, 'role', 'button');
                    if (!empty($metafield_button_size)) {
                        $this->get_parent()->add_render_attribute($attribute_a_link, 'class', 'elementor-size-' . $metafield_button_size);
                    }
                    $this->get_parent()->add_render_attribute($attribute_a_link, 'class', ['elementor-button-link', 'elementor-button', 'dce-button']);
                    $link_to = $meta_value;
                    $meta_html = $metafield_button_label;
                    break;
                case 'url':
                    $this->get_parent()->set_render_attribute($attribute_a_link, 'href', $meta_value);
                    $link_to = $meta_value;
                    $meta_html = $meta_value;
                    break;
                case 'textarea':
                    // not exists
                    $meta_html = \nl2br($meta_value);
                    break;
                case 'wysiwyg':
                    // not exists
                    $meta_html = wpautop($meta_value);
                    break;
                case 'text':
                    if ($html_tag_item) {
                        $meta_html = '<' . $html_tag_item . '>' . $meta_value . '</' . $html_tag_item . '>';
                    } else {
                        $meta_html = $meta_value;
                    }
                    break;
                default:
                    $meta_html = $meta_value;
            }
            switch ($link_to) {
                case 'home':
                    $this->get_parent()->add_render_attribute($attribute_a_link, 'href', esc_url(get_home_url()));
                    break;
                case 'post':
                    $this->get_parent()->add_render_attribute($attribute_a_link, 'href', $this->current_permalink);
                    break;
                case 'custom':
                    if (!empty($link)) {
                        $this->get_parent()->add_link_attributes($attribute_a_link, $link);
                    }
                    break;
                default:
            }
            $linkOpen = '';
            $linkClose = '';
            if ($link_to) {
                $this->get_parent()->add_render_attribute($attribute_a_link, 'class', ['dce-link']);
                $linkOpen = '<a ' . $this->get_parent()->get_render_attribute_string($attribute_a_link) . '>';
                $linkClose = '</a>';
            }
            if (isset($meta_html)) {
                $this->get_parent()->add_render_attribute($attribute_custommeta_item, ['class' => ['dce-meta-item', 'dce-meta-' . $_id, 'dce-meta-' . $metafield_type, 'elementor-repeater-item-' . $settings['_id']]]);
                echo '<div ' . $this->get_parent()->get_render_attribute_string($attribute_custommeta_item) . '>' . $linkOpen . $meta_html . $linkClose . '</div>';
            }
            echo '</div>';
        }
    }
    /**
     * Render a JetEngine Field
     *
     * @param array $settings
     * @return void
     */
    protected function render_jetengine(array $settings)
    {
        if (!Helper::is_jetengine_active()) {
            return;
        }
        $field = $settings['jetengine_key'];
        if (empty($field)) {
            return;
        }
        $_id = $settings['_id'];
        $metafield_type = $settings['metafield_type'];
        $image_size_key = $settings['image_size_size'];
        $metafield_button_label = wp_kses_post($settings['metafield_button_label']);
        $metafield_button_size = $settings['metafield_button_size'];
        $metafield_date_format_source = $settings['metafield_date_format_source'];
        $metafield_date_format_display = $settings['metafield_date_format_display'];
        $html_tag_item = $settings['html_tag_item'];
        $link_to = $settings['link_to'];
        $link = $settings['link'];
        $attribute_a_link = 'a_link_' . $this->counter;
        $attribute_custommeta_item = 'custommeta_item-' . $this->counter;
        $field_value = jet_engine()->listings->data->get_meta($field);
        if (!$field_value) {
            return;
        }
        echo '<div class="dce-post-jetengine">';
        $meta_html = '';
        switch ($metafield_type) {
            case 'date':
                if ($metafield_date_format_source) {
                    if ($metafield_date_format_source == 'timestamp') {
                        $timestamp = $field_value;
                    } else {
                        $d = \DateTime::createFromFormat($metafield_date_format_source, $field_value);
                        if ($d) {
                            $timestamp = $d->getTimestamp();
                        } else {
                            $timestamp = \strtotime($field_value);
                        }
                    }
                } else {
                    $timestamp = \strtotime($field_value);
                }
                $meta_html = date_i18n($metafield_date_format_display, $timestamp);
                break;
            case 'image':
                $image_attr = ['class' => 'dce-img'];
                if (\is_string($field_value)) {
                    if (\is_numeric($field_value)) {
                        $image_html = wp_get_attachment_image(\intval($field_value), $image_size_key, \false, $image_attr);
                    } else {
                        $image_html = '<img src="' . $field_value . '" />';
                    }
                } elseif (\is_numeric($field_value)) {
                    $image_html = wp_get_attachment_image($field_value, $image_size_key, \false, $image_attr);
                } elseif (\is_array($field_value)) {
                    // TODO ... da valutare come gestire il caso di un'array...
                    $imageSrc = wp_get_attachment_image_src($field_value['ID'], $image_size_key);
                    $imageSrcUrl = $imageSrc[0];
                }
                $meta_html = $image_html;
                break;
            case 'button':
                $this->get_parent()->add_render_attribute($attribute_a_link, 'href', $field_value);
                $this->get_parent()->add_render_attribute($attribute_a_link, 'role', 'button');
                if (!empty($metafield_button_size)) {
                    $this->get_parent()->add_render_attribute($attribute_a_link, 'class', 'elementor-size-' . $metafield_button_size);
                }
                $this->get_parent()->add_render_attribute($attribute_a_link, 'class', ['elementor-button-link', 'elementor-button', 'dce-button']);
                $link_to = $field_value;
                $meta_html = $metafield_button_label;
                break;
            case 'url':
                $this->get_parent()->add_render_attribute($attribute_a_link, 'href', $field_value);
                $link_to = $field_value;
                $meta_html = $field_value;
                break;
            case 'textarea':
                // not exists
                $meta_html = \nl2br($field_value);
                break;
            case 'wysiwyg':
                // not exists
                $meta_html = wpautop($field_value);
                break;
            case 'text':
                if ($html_tag_item) {
                    $meta_html = '<' . $html_tag_item . '>' . $field_value . '</' . $html_tag_item . '>';
                } else {
                    $meta_html = $field_value;
                }
                break;
            default:
                $meta_html = $field_value;
        }
        switch ($link_to) {
            case 'home':
                $this->get_parent()->add_render_attribute($attribute_a_link, 'href', esc_url(get_home_url()));
                break;
            case 'post':
                $this->get_parent()->add_render_attribute($attribute_a_link, 'href', $this->current_permalink);
                break;
            case 'custom':
                if (!empty($link)) {
                    $this->get_parent()->add_link_attributes($attribute_a_link, $link);
                }
                break;
            default:
        }
        $linkOpen = '';
        $linkClose = '';
        if ($link_to) {
            $this->get_parent()->add_render_attribute($attribute_a_link, 'class', ['dce-link']);
            $linkOpen = '<a ' . $this->get_parent()->get_render_attribute_string($attribute_a_link) . '>';
            $linkClose = '</a>';
        }
        if (isset($meta_html)) {
            $this->get_parent()->add_render_attribute($attribute_custommeta_item, ['class' => ['dce-meta-item', 'dce-meta-' . $_id, 'dce-meta-' . $metafield_type, 'elementor-repeater-item-' . $settings['_id']]]);
            echo '<div ' . $this->get_parent()->get_render_attribute_string($attribute_custommeta_item) . '>' . $linkOpen . $meta_html . $linkClose . '</div>';
        }
        echo '</div>';
    }
    /**
     * Render a Metabox Field
     *
     * @param array<string,mixed> $settings
     * @return void
     */
    protected function render_metabox(array $settings)
    {
        if (!Helper::is_metabox_active()) {
            return;
        }
        $field = $settings['metabox_key'];
        if (empty($field)) {
            return;
        }
        $_id = $settings['_id'];
        $metafield_type = $settings['metafield_type'];
        $image_size_key = $settings['image_size_size'];
        $metafield_button_label = wp_kses_post($settings['metafield_button_label']);
        $metafield_button_size = $settings['metafield_button_size'];
        $metafield_date_format_source = $settings['metafield_date_format_source'];
        $metafield_date_format_display = $settings['metafield_date_format_display'];
        $html_tag_item = $settings['html_tag_item'];
        $link_to = $settings['link_to'];
        $link = $settings['link'];
        $attribute_a_link = 'a_link_' . $this->counter;
        $attribute_custommeta_item = 'custommeta_item-' . $this->counter;
        $field_value = rwmb_get_value($field);
        if (!$field_value) {
            return;
        }
        echo '<div class="dce-post-metabox">';
        $meta_html = '';
        switch ($metafield_type) {
            case 'date':
                if ($metafield_date_format_source) {
                    if ($metafield_date_format_source == 'timestamp') {
                        $timestamp = $field_value;
                    } else {
                        $d = \DateTime::createFromFormat($metafield_date_format_source, $field_value);
                        if ($d) {
                            $timestamp = $d->getTimestamp();
                        } else {
                            $timestamp = \strtotime($field_value);
                        }
                    }
                } else {
                    $timestamp = \strtotime($field_value);
                }
                $meta_html = date_i18n($metafield_date_format_display, $timestamp);
                break;
            case 'image':
                $image_attr = ['class' => 'dce-img'];
                if (\is_string($field_value)) {
                    if (\is_numeric($field_value)) {
                        $image_html = wp_get_attachment_image(\intval($field_value), $image_size_key, \false, $image_attr);
                    } else {
                        $image_html = '<img src="' . $field_value . '" />';
                    }
                } elseif (\is_numeric($field_value)) {
                    $image_html = wp_get_attachment_image((int) $field_value, $image_size_key, \false, $image_attr);
                } elseif (\is_array($field_value)) {
                    // TODO ... da valutare come gestire il caso di un'array...
                    $imageSrc = wp_get_attachment_image_src($field_value['ID'], $image_size_key);
                    if (\false !== $imageSrc) {
                        $imageSrcUrl = $imageSrc[0];
                    }
                }
                $meta_html = $image_html ?? '';
                break;
            case 'button':
                $this->get_parent()->add_render_attribute($attribute_a_link, 'href', $field_value);
                $this->get_parent()->add_render_attribute($attribute_a_link, 'role', 'button');
                if (!empty($metafield_button_size)) {
                    $this->get_parent()->add_render_attribute($attribute_a_link, 'class', 'elementor-size-' . $metafield_button_size);
                }
                $this->get_parent()->add_render_attribute($attribute_a_link, 'class', ['elementor-button-link', 'elementor-button', 'dce-button']);
                $link_to = $field_value;
                $meta_html = $metafield_button_label;
                break;
            case 'url':
                $this->get_parent()->add_render_attribute($attribute_a_link, 'href', $field_value);
                $link_to = $field_value;
                $meta_html = $field_value;
                break;
            case 'textarea':
                // not exists
                $meta_html = \nl2br($field_value);
                break;
            case 'wysiwyg':
                // not exists
                $meta_html = wpautop($field_value);
                break;
            case 'text':
                if ($html_tag_item) {
                    $meta_html = '<' . $html_tag_item . '>' . $field_value . '</' . $html_tag_item . '>';
                } else {
                    $meta_html = $field_value;
                }
                break;
            default:
                $meta_html = $field_value;
        }
        switch ($link_to) {
            case 'home':
                $this->get_parent()->add_render_attribute($attribute_a_link, 'href', esc_url(get_home_url()));
                break;
            case 'post':
                $this->get_parent()->add_render_attribute($attribute_a_link, 'href', $this->current_permalink);
                break;
            case 'custom':
                if (!empty($link)) {
                    $this->get_parent()->add_link_attributes($attribute_a_link, $link);
                }
                break;
            default:
        }
        $linkOpen = '';
        $linkClose = '';
        if ($link_to) {
            $this->get_parent()->add_render_attribute($attribute_a_link, 'class', ['dce-link']);
            $linkOpen = '<a ' . $this->get_parent()->get_render_attribute_string($attribute_a_link) . '>';
            $linkClose = '</a>';
        }
        $this->get_parent()->add_render_attribute($attribute_custommeta_item, ['class' => ['dce-meta-item', 'dce-meta-' . $_id, 'dce-meta-' . $metafield_type, 'elementor-repeater-item-' . $settings['_id']]]);
        echo '<div ' . $this->get_parent()->get_render_attribute_string($attribute_custommeta_item) . '>' . $linkOpen . $meta_html . $linkClose . '</div>';
        echo '</div>';
    }
    protected function render_terms($settings)
    {
        $taxonomy_filter = $settings['taxonomy_filter'];
        $separator_chart = wp_kses_post($settings['separator_chart']);
        $only_parent_terms = $settings['only_parent_terms'];
        $block_enable = $settings['block_enable'];
        $icon_enable = $settings['icon_enable'];
        $use_link = $settings['use_link'];
        $open_target_blank = $settings['open_target_blank'];
        $term_list = [];
        $taxonomy = get_post_taxonomies($this->current_id);
        echo '<div class="dce-post-terms">';
        foreach ($taxonomy as $tax) {
            if (isset($taxonomy_filter) && !empty($taxonomy_filter)) {
                if (!\in_array($tax, $taxonomy_filter)) {
                    continue;
                }
            }
            if ($tax != 'post_format') {
                $term_list = Helper::get_the_terms_ordered($this->current_id, $tax);
                if ($term_list && \is_array($term_list) && \count($term_list) > 0) {
                    echo '<ul class="dce-terms-list dce-taxonomy-' . $tax . '">';
                    // Ciclo i termini
                    $cont = 1;
                    $divider = '';
                    foreach ($term_list as $term) {
                        if (!empty($only_parent_terms)) {
                            if ($only_parent_terms == 'yes') {
                                if ($term->parent) {
                                    continue;
                                }
                            }
                            if ($only_parent_terms == 'children') {
                                if (!$term->parent) {
                                    continue;
                                }
                            }
                        }
                        if ($icon_enable && $cont == 1) {
                            $icon = '';
                            if (is_taxonomy_hierarchical($tax)) {
                                $icon = '<i class="dce-post-icon fa fa-folder-open" aria-hidden="true"></i> ';
                            } else {
                                $icon = '<i class="dce-post-icon fa fa-tags" aria-hidden="true"></i> ';
                            }
                            echo $icon;
                        }
                        $term_url = trailingslashit(get_term_link($term));
                        if ($cont > 1 && !$block_enable) {
                            $divider = '<span class="dce-separator">' . $separator_chart . '</span>';
                        }
                        echo '<li class="dce-term-item">';
                        echo $divider . '<span class="dce-term dce-term-' . $term->term_id . '" data-dce-order="' . $term->term_order . '">' . $this->render_item_link_text($term->name, $use_link, $term_url, $open_target_blank) . '</span>';
                        echo '</li>';
                        $cont++;
                    }
                    echo '</ul>';
                }
            }
        }
        echo '</div>';
    }
    protected function render_item_link_text($link_text = '', $use_link = '', $url = '', $open_target_blank = '')
    {
        if (!empty($use_link) && $url && $link_text) {
            $open_target_blank = !empty($open_target_blank) ? ' target="_blank"' : '';
            return '<a href="' . $url . '"' . $open_target_blank . '>' . $link_text . '</a>';
        } else {
            return $link_text ? $link_text : '';
        }
    }
    /**
     * Render Post - Start
     *
     * @return void
     */
    protected function render_post_start()
    {
        $this->get_parent()->set_render_attribute('post', ['class' => get_post_class()]);
        $this->get_parent()->add_render_attribute('post', 'class', 'dce-post');
        $this->get_parent()->add_render_attribute('post', 'class', 'dce-post-item');
        $this->get_parent()->add_render_attribute('post', 'class', $this->get_item_class());
        $this->get_parent()->set_render_attribute('post', 'data-dce-post-id', $this->current_id);
        $this->get_parent()->set_render_attribute('post', 'data-dce-post-index', $this->counter);
        // Template Linkable
        if ($this->get_parent()->get_settings('templatemode_linkable') && !empty(get_permalink($this->current_id))) {
            $this->get_parent()->set_render_attribute('post', 'data-post-link', get_permalink($this->current_id));
        }
        $this->get_parent()->set_render_attribute('post-block', 'class', 'dce-post-block');
        // Hover Animation
        if (!empty($this->get_parent()->get_settings('hover_animation'))) {
            $this->get_parent()->add_render_attribute('post-block', 'class', 'elementor-animation-' . $this->get_parent()->get_settings('hover_animation'));
        }
        // Hover Effect
        if (!empty($this->get_parent()->get_settings('hover_text_effect')) && !empty($this->get_parent()->get_settings('style_items')) && 'float' === $this->get_parent()->get_settings('style_items')) {
            $this->get_parent()->add_render_attribute('post-block', 'class', 'dce-hover-effects');
        }
        ?>

		<article <?php 
        echo $this->get_parent()->get_render_attribute_string('post');
        ?>>
			<div <?php 
        echo $this->get_parent()->get_render_attribute_string('post-block');
        ?>>
		<?php 
    }
    /**
     * Render Post - End
     *
     * @return void
     */
    protected function render_post_end()
    {
        ?>
			</div>
		</article>
		<?php 
    }
    /**
     * Render Fallback
     *
     * @return void
     */
    protected function render_fallback()
    {
        $fallback_type = $this->get_parent()->get_settings_for_display('fallback_type');
        $fallback_text = $this->get_parent()->get_settings_for_display('fallback_text');
        $fallback_template = $this->get_parent()->get_settings_for_display('fallback_template');
        $this->get_parent()->add_render_attribute('container', ['class' => ['dce-posts-container', 'dce-posts', $this->get_scrollreveal_class()]]);
        $this->get_parent()->add_render_attribute('container_wrap', ['class' => ['dce-posts-fallback']]);
        ?>
		<div <?php 
        echo $this->get_parent()->get_render_attribute_string('container');
        ?>>
			<div <?php 
        echo $this->get_parent()->get_render_attribute_string('container_wrap');
        ?>>
			<?php 
        if (isset($fallback_type) && $fallback_type === 'template') {
            $fallback_content = '[dce-elementor-template id="' . $fallback_template . '"]';
        } else {
            $fallback_content = '<p>' . $fallback_text . '</p>';
        }
        echo do_shortcode($fallback_content);
        ?>
		</div>
	</div>
		<?php 
    }
    /**
     * Add Direction
     *
     * @return void
     */
    protected function add_direction(string $attribute = 'container')
    {
        if ($this->get_parent()->get_settings_for_display('rtl')) {
            $this->get_parent()->add_render_attribute($attribute, ['dir' => ['rtl']]);
        }
    }
    /**
     * Render Loop Start
     *
     * @return void
     */
    protected function render_loop_start()
    {
        if (!$this->parent) {
            throw new \Exception('Parent not found');
        }
        $this->get_parent()->add_render_attribute('container', ['class' => ['dce-posts-container', 'dce-posts', 'dce-dynamic-posts-collection', $this->get_scrollreveal_class(), $this->get_container_class()]]);
        $this->get_parent()->add_render_attribute('container_wrap', ['class' => ['dce-posts-wrapper', $this->get_wrapper_class()]]);
        $this->render_pagination_top();
        ?>

		<div <?php 
        echo $this->get_parent()->get_render_attribute_string('container');
        ?>>
			<?php 
        $this->render_posts_before();
        ?>
			<div <?php 
        echo $this->get_parent()->get_render_attribute_string('container_wrap');
        ?>>
			<?php 
        $this->render_posts_wrapper_before();
    }
    /**
     * Render Top Pagination
     *
     * @return void
     */
    protected function render_pagination_top()
    {
        $settings = $this->get_parent()->get_settings_for_display();
        $p_query = $this->get_parent()->get_query();
        $rtl = $this->get_parent()->get_settings_for_display('rtl');
        if ($settings['pagination_enable'] && ('top' === $settings['pagination_position'] || 'both' === $settings['pagination_position'])) {
            Helper::numeric_query_pagination($p_query->max_num_pages, $settings, 'dce-pagination-top', (bool) $rtl);
        }
    }
    /**
     * Render Bottom Pagination
     *
     * @return void
     */
    protected function render_pagination_bottom()
    {
        $settings = $this->get_parent()->get_settings_for_display();
        $p_query = $this->get_parent()->get_query();
        $rtl = $this->get_parent()->get_settings_for_display('rtl');
        if ($settings['pagination_enable'] && ('bottom' === $settings['pagination_position'] || 'both' === $settings['pagination_position'])) {
            Helper::numeric_query_pagination($p_query->max_num_pages, $settings, 'dce-pagination-bottom', (bool) $rtl);
        }
    }
    /**
     * Render Loop End
     *
     * @return void
     */
    protected function render_loop_end()
    {
        $this->render_posts_wrapper_after();
        ?>
			</div>
			<?php 
        $this->render_posts_after();
        ?>
		</div>
		<?php 
        $this->render_pagination_bottom();
        $this->render_infinite_scroll();
    }
    protected function render_posts_before()
    {
    }
    protected function render_posts_after()
    {
    }
    protected function render_posts_wrapper_before()
    {
    }
    protected function render_posts_wrapper_after()
    {
    }
    public function get_container_class()
    {
        return 'dce-skin-' . $this->get_id();
    }
    public function get_wrapper_class()
    {
        return 'dce-wrapper-' . $this->get_id();
    }
    public function get_item_class()
    {
        return 'dce-item-' . $this->get_id();
    }
    public function get_image_class()
    {
    }
    public function get_scrollreveal_class()
    {
        return '';
    }
    public function filter_excerpt_length()
    {
        return $this->get_instance_value('textcontent_limit');
    }
    public function filter_excerpt_more($more)
    {
        return '';
    }
    protected function limit_content($limit)
    {
        $post = get_post();
        $content = $post->post_content;
        $content = \mb_substr(wp_strip_all_tags($content), 0, $limit) . '&hellip;';
        return $content;
    }
    protected function add_search_filter_class()
    {
        $search_filter_id = $this->get_parent()->get_settings_for_display('search_filter_id');
        $search_filter_id = apply_filters('wpml_object_id', $search_filter_id, 'search-filter-widget', \true);
        if ($this->get_parent()->get_settings('query_type') === 'search_filter' && isset($search_filter_id)) {
            $sfid = \intval($search_filter_id);
            $element_class = 'search-filter-results-' . $sfid;
            $args = array('class' => array($element_class));
            $this->get_parent()->add_render_attribute('_wrapper', $args);
        }
    }
    /**
     * Render Infinite Scroll
     *
     * @return void
     */
    protected function render_infinite_scroll()
    {
        $settings = $this->get_parent()->get_settings_for_display();
        $p_query = $this->get_parent()->get_query();
        $postlength = $p_query->post_count;
        $posts_per_page = $p_query->query_vars['posts_per_page'];
        if ($settings['infiniteScroll_enable'] && $settings['query_type'] != 'search_filter' && $postlength >= $settings['num_posts'] && $settings['num_posts'] >= 0 || $settings['infiniteScroll_enable'] && $settings['query_type'] == 'search_filter' && $postlength >= $posts_per_page || \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $preview_mode = '';
            if ($settings['infiniteScroll_enable_status']) {
                ?>
				<nav class="infiniteScroll">
					<div class="page-load-status<?php 
                echo $preview_mode;
                ?>">

						<?php 
                if ($settings['infiniteScroll_loading_type'] == 'text') {
                    ?>
							<div class="infinite-scroll-request status-text"><?php 
                    echo wp_kses_post($settings['infiniteScroll_label_loading']);
                    ?></div>
							<?php 
                } elseif ($settings['infiniteScroll_loading_type'] == 'ellips') {
                    ?>
							<div class="loader-ellips infinite-scroll-request">
								<span class="loader-ellips__dot"></span>
								<span class="loader-ellips__dot"></span>
								<span class="loader-ellips__dot"></span>
								<span class="loader-ellips__dot"></span>
							</div>
							<?php 
                }
                ?>
						<div class="infinite-scroll-last status-text"><?php 
                echo wp_kses_post($settings['infiniteScroll_label_last']);
                ?></div>
						<div class="infinite-scroll-error status-text"><?php 
                echo wp_kses_post($settings['infiniteScroll_label_error']);
                ?></div>

						<div class="pagination" role="navigation">
							<?php 
                if ($settings['query_type'] != 'search_filter') {
                    ?>
							<a class="pagination__next" href="<?php 
                    echo \DynamicContentForElementor\Helper::get_next_pagination();
                    ?>"></a>
							<?php 
                } else {
                    ?>
							<a class="pagination__next" href="<?php 
                    echo \DynamicContentForElementor\Helper::get_next_pagination_sf();
                    ?>"></a>
							<?php 
                }
                ?>
						</div>
					</div>


				</nav>
				<?php 
            }
            // Infinite Scroll - Button
            if ($settings['infiniteScroll_trigger'] == 'button') {
                ?>
				<div class="infiniteScroll">
					<button class="view-more-button"><?php 
                echo $settings['infiniteScroll_label_button'];
                ?></button>
				</div>
				<?php 
            }
        }
    }
}
