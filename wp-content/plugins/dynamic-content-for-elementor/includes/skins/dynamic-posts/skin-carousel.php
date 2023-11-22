<?php

namespace DynamicContentForElementor\Includes\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Skin_Carousel extends \DynamicContentForElementor\Includes\Skins\Skin_Base
{
    /**
     * Register Controls Actions
     *
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/dce-dynamicposts-v2/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/dce-dynamicposts-v2/section_dynamicposts/after_section_end', [$this, 'register_additional_carousel_controls']);
    }
    public $depended_scripts = ['dce-dynamicPosts-carousel', 'dce-jquery-match-height'];
    /**
     * Get Style Depends
     *
     * @return array<string>
     */
    public function get_style_depends()
    {
        if (\Elementor\Plugin::$instance->experiments->is_feature_active('e_swiper_latest')) {
            return ['dce-dynamicPosts-carousel'];
        }
        return ['dce-dynamicPosts-carousel', 'dce-swiper'];
    }
    public function get_id()
    {
        return 'carousel';
    }
    public function get_title()
    {
        return __('Carousel', 'dynamic-content-for-elementor');
    }
    public function register_additional_carousel_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        $this->start_controls_section('section_carousel', ['label' => __('Carousel', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('remove_masking', ['label' => __('Remove Masking', 'dynamic-content-for-elementor'), 'description' => __('Remove the mask on the carousel to allow the display of the elements outside', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'prefix_class' => 'no-masking-', 'frontend_available' => \true, 'default' => '']);
        $this->add_control('speed_slider', ['label' => __('Speed (ms)', 'dynamic-content-for-elementor'), 'description' => __('Duration of transition between slides', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 300, 'min' => 0, 'max' => 30000, 'step' => 10, 'frontend_available' => \true]);
        $this->add_control('effects', ['label' => __('Transition Effect', 'dynamic-content-for-elementor'), 'description' => __('Transition effect between slides', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['slide' => __('Slide', 'dynamic-content-for-elementor'), 'fade' => __('Fade', 'dynamic-content-for-elementor'), 'cube' => __('Cube', 'dynamic-content-for-elementor'), 'coverflow' => __('Coverflow', 'dynamic-content-for-elementor'), 'flip' => __('Flip', 'dynamic-content-for-elementor')], 'default' => 'slide', 'render_type' => 'template', 'frontend_available' => \true, 'prefix_class' => 'dce-carousel-effect-']);
        $this->add_control('effects_options_popover', ['label' => __('Effects options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::POPOVER_TOGGLE, 'label_off' => __('Default', 'dynamic-content-for-elementor'), 'label_on' => __('Custom', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'condition' => [$this->get_control_id('effects!') => ['slide', 'fade']]]);
        $this->get_parent()->start_popover();
        $this->add_control('slideShadows', ['label' => __('Slide Shadows', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => [$this->get_control_id('effects_options_popover') => 'yes', $this->get_control_id('effects') => ['cube', 'flip', 'coverflow']]]);
        $this->add_control('cube_shadow', ['label' => __('Shadow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => [$this->get_control_id('effects_options_popover') => 'yes', $this->get_control_id('effects') => ['cube']]]);
        $this->add_control('coverflow_stretch', ['label' => __('Coverflow Stretch', 'dynamic-content-for-elementor'), 'description' => __('Stretch space between slides (in px)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '0', 'min' => 0, 'max' => 100, 'step' => 1, 'frontend_available' => \true, 'condition' => [$this->get_control_id('effects_options_popover') => 'yes', $this->get_control_id('effects') => ['coverflow']]]);
        // ------- coverflow modifier (1) ------
        $this->add_control('coverflow_modifier', ['label' => __('Coverflow Modifier', 'dynamic-content-for-elementor'), 'description' => __('Effect multipler', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '1', 'min' => 0, 'max' => 2, 'step' => 0.1, 'frontend_available' => \true, 'condition' => [$this->get_control_id('effects_options_popover') => 'yes', $this->get_control_id('effects') => ['coverflow']]]);
        $this->get_parent()->end_popover();
        $this->add_control('direction_slider', ['label' => __('Direction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['horizontal' => __('Horizontal', 'dynamic-content-for-elementor'), 'vertical' => __('Vertical', 'dynamic-content-for-elementor')], 'default' => 'horizontal', 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('autoHeight', ['label' => __('Auto Height', 'dynamic-content-for-elementor'), 'description' => __('Slider wrapper will adopt its height to the height of the currently active slide. This setting doesn\'t support multirow layout', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'separator' => 'before', 'default' => '']);
        $this->add_responsive_control('height_container', ['label' => __('Viewport Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'vh'], 'range' => ['px' => ['min' => 1, 'max' => 800, 'step' => 1], 'vh' => ['min' => 1, 'max' => 100, 'step' => 1]], 'default' => ['size' => '600', 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .dce-skin-carousel' => 'height: {{SIZE}}{{UNIT}};'], 'condition' => [$this->get_control_id('useNavigation') => 'yes', $this->get_control_id('autoHeight') => '']]);
        $this->add_control('match_height', ['label' => __('Match Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->add_responsive_control('initialSlide', ['label' => __('Initial Slide', 'dynamic-content-for-elementor'), 'description' => __('Index number of initial slide', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'tablet_default' => '', 'mobile_default' => '', 'min' => 0, 'step' => 1, 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_responsive_control('slidesPerView', ['label' => __('Slides Per View', 'dynamic-content-for-elementor'), 'description' => __('Slides visible at the same time', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '1', 'tablet_default' => '', 'mobile_default' => '', 'min' => 1, 'max' => 12, 'step' => 1, 'frontend_available' => \true]);
        $this->add_responsive_control('slidesPerGroup', ['label' => __('Slides Per Group', 'dynamic-content-for-elementor'), 'description' => __('Enable group sliding and set numbers of slides per group', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'tablet_default' => '', 'mobile_default' => '', 'min' => 1, 'max' => 12, 'step' => 1, 'frontend_available' => \true, 'condition' => [$this->get_control_id('slidesPerView!') => 1]]);
        $this->add_responsive_control('slidesColumn', ['label' => __('Slides Per Column', 'dynamic-content-for-elementor'), 'description' => __('Number of slides per column, for multirow layout', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '1', 'min' => 1, 'max' => 4, 'step' => 1, 'frontend_available' => \true]);
        $this->add_responsive_control('spaceBetween', ['label' => __('Space Between', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'tablet_default' => '', 'mobile_default' => '', 'min' => 0, 'max' => 100, 'step' => 1, 'frontend_available' => \true]);
        $this->add_control('hr_interface', ['type' => Controls_Manager::DIVIDER, 'style' => 'thick']);
        $this->start_controls_tabs('carousel_interface');
        $this->start_controls_tab('tab_carousel_navigation', ['label' => __('Nav', 'dynamic-content-for-elementor')]);
        $this->add_control('useNavigation', ['label' => __('Navigation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('arrows_heading', ['label' => __('Arrows', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => [$this->get_control_id('useNavigation') => 'yes']]);
        $this->add_control('previous_arrow', ['label' => __('Left', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'condition' => [$this->get_control_id('useNavigation') => 'yes']]);
        $this->add_control('next_arrow', ['label' => __('Right', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'condition' => [$this->get_control_id('useNavigation') => 'yes']]);
        $this->add_responsive_control('navigation_arrow_height', ['label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '80'], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-container-navigation i' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-container-navigation svg' => 'height: {{SIZE}}{{UNIT}}; width: 100%;', '{{WRAPPER}} .dce-carousel-controls .swiper-button-left' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; margin-top: calc({{SIZE}}{{UNIT}} / 2)', '{{WRAPPER}} .dce-carousel-controls .swiper-button-right' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; margin-top: calc({{SIZE}}{{UNIT}} / 2)'], 'condition' => [$this->get_control_id('useNavigation') => 'yes']]);
        $this->add_control('navigation_arrow_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-button-right path, {{WRAPPER}} .swiper-button-left path, {{WRAPPER}} .dce-container-navigation svg, {{WRAPPER}} .dce-container-navigation i' => 'fill: {{VALUE}}; color: {{VALUE}}', '{{WRAPPER}} .swiper-button-right line, {{WRAPPER}} .swiper-button-left line, {{WRAPPER}} .swiper-button-right polyline, {{WRAPPER}} .swiper-button-left polyline' => 'stroke: {{VALUE}};'], 'condition' => [$this->get_control_id('useNavigation') => 'yes']]);
        $this->add_control('navigation_arrow_color_hover', ['label' => __('Hover color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-button-right:hover path, {{WRAPPER}} .swiper-button-left:hover path, {{WRAPPER}} .swiper-button-left:hover svg, {{WRAPPER}} .swiper-button-right:hover svg, {{WRAPPER}} .swiper-button-left:hover i, {{WRAPPER}} .swiper-button-right:hover i' => 'fill: {{VALUE}}; color: {{VALUE}}', '{{WRAPPER}} .swiper-button-right:hover line, {{WRAPPER}} .swiper-button-left:hover line, {{WRAPPER}} .swiper-button-right:hover polyline, {{WRAPPER}} .swiper-button-left:hover polyline' => 'stroke: {{VALUE}};'], 'condition' => [$this->get_control_id('useNavigation') => 'yes']]);
        $this->add_control('navigation_transform_popover', ['label' => __('Transform', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::POPOVER_TOGGLE, 'label_off' => __('Default', 'dynamic-content-for-elementor'), 'label_on' => __('Custom', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'condition' => [$this->get_control_id('useNavigation') => 'yes']]);
        $this->get_parent()->start_popover();
        $this->add_responsive_control('navigation_stroke_1', ['label' => __('Stroke Arrow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-left polyline, {{WRAPPER}} .swiper-button-right polyline' => 'stroke-width: {{SIZE}};'], 'condition' => [$this->get_control_id('useNavigation') => 'yes', $this->get_control_id('navigation_transform_popover') => 'yes']]);
        $this->add_responsive_control('navigation_stroke_2', ['label' => __('Stroke Line', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-right line, {{WRAPPER}} .swiper-button-left line' => 'stroke-width: {{SIZE}};'], 'condition' => [$this->get_control_id('useNavigation') => 'yes', $this->get_control_id('navigation_transform_popover') => 'yes']]);
        $this->add_control('navigation_dash', ['label' => __('Dashed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0'], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-left line, {{WRAPPER}} .swiper-button-right line, {{WRAPPER}} .swiper-button-left polyline, {{WRAPPER}} .swiper-button-right polyline' => 'stroke-dasharray: {{SIZE}},{{SIZE}};'], 'condition' => [$this->get_control_id('useNavigation') => 'yes', $this->get_control_id('navigation_transform_popover') => 'yes']]);
        $this->add_responsive_control('navigation_size', ['label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 2, 'min' => 0.1, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .swiper-button-right, {{WRAPPER}} .swiper-button-left' => 'transform: scale({{SIZE}});'], 'condition' => [$this->get_control_id('useNavigation') => 'yes', $this->get_control_id('navigation_transform_popover') => 'yes']]);
        $this->get_parent()->end_popover();
        $this->add_control('navigation_position_popover', ['label' => __('Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::POPOVER_TOGGLE, 'label_off' => __('Default', 'dynamic-content-for-elementor'), 'label_on' => __('Custom', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'condition' => [$this->get_control_id('useNavigation') => 'yes']]);
        $this->get_parent()->start_popover();
        $this->add_responsive_control('h_navigation_position', ['label' => __('Horizontal position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['left: 0%;' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'transform: translateX(-50%); left: 50%;' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'left: auto; right: 0;' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-carousel-controls .dce-container-navigation' => '{{VALUE}}'], 'condition' => [$this->get_control_id('useNavigation') => 'yes', $this->get_control_id('navigation_position_popover') => 'yes']]);
        $this->add_responsive_control('v_navigation_position', ['label' => __('Vertical position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['0' => ['title' => __('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], '50' => ['title' => __('Middle', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], '100' => ['title' => __('Down', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom']], 'default' => 'center', 'selectors' => ['{{WRAPPER}} .dce-carousel-controls .dce-container-navigation' => 'top: {{VALUE}}%;'], 'condition' => [$this->get_control_id('useNavigation') => 'yes', $this->get_control_id('navigation_position_popover') => 'yes']]);
        $this->add_responsive_control('navigation_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'size_units' => '%', 'range' => ['%' => ['max' => 100, 'min' => 20, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-carousel-controls .dce-container-navigation' => 'width: {{SIZE}}%;'], 'condition' => [$this->get_control_id('useNavigation') => 'yes', $this->get_control_id('navigation_position_popover') => 'yes']]);
        $this->add_responsive_control('horiz_navigation_shift', ['label' => __('Horizontal Shift', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'range' => ['px' => ['max' => 200, 'min' => -200, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-button-left' => 'left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .swiper-button-right' => 'right: {{SIZE}}{{UNIT}};'], 'condition' => [$this->get_control_id('useNavigation') => 'yes', $this->get_control_id('navigation_position_popover') => 'yes']]);
        $this->add_responsive_control('vert_navigation_shift', ['label' => __('Vertical Shift', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'range' => ['px' => ['max' => 200, 'min' => -200, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-button-left, {{WRAPPER}} .swiper-button-right' => 'top: {{SIZE}}{{UNIT}};'], 'condition' => [$this->get_control_id('useNavigation') => 'yes', $this->get_control_id('navigation_position_popover') => 'yes']]);
        $this->get_parent()->end_popover();
        $this->add_control('useNavigation_animationHover', ['label' => __('Use animation in rollover', 'dynamic-content-for-elementor'), 'description' => __('A short animation will take place at the rollover.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'prefix_class' => 'hover-animation-', 'separator' => 'before', 'condition' => [$this->get_control_id('useNavigation') => 'yes']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_carousel_pagination', ['label' => __('Pag', 'dynamic-content-for-elementor')]);
        $this->add_control('usePagination', ['label' => __('Pagination', 'dynamic-content-for-elementor'), 'description' => __('Use the slide progression display system', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('pagination_type', ['label' => __('Pagination Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['bullets' => __('Bullets', 'dynamic-content-for-elementor'), 'fraction' => __('Fraction', 'dynamic-content-for-elementor'), 'progressbar' => __('Progressbar', 'dynamic-content-for-elementor')], 'default' => 'bullets', 'frontend_available' => \true, 'condition' => [$this->get_control_id('usePagination') => 'yes']]);
        $this->add_control('fraction_heading', ['label' => __('Fraction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'fraction']]);
        $this->add_control('fraction_separator', ['label' => __('Fraction text separator', 'dynamic-content-for-elementor'), 'description' => __('The text separating the 2 numbers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'frontend_available' => \true, 'default' => '/', 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'fraction']]);
        $this->add_control('fraction_color', ['label' => __('Numbers color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction > *' => 'color: {{VALUE}};'], 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'fraction']]);
        $this->add_control('fraction_current_color', ['label' => __('current Number Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current' => 'color: {{VALUE}};'], 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'fraction']]);
        $this->add_control('fraction_separator_color', ['label' => __('Separator Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .separator' => 'color: {{VALUE}};'], 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'fraction']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fraction_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-fraction > *', 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'fraction']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fraction_typography_current', 'label' => __('Current Number Typography', 'dynamic-content-for-elementor'), 'default' => '', 'selector' => '{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current', 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'fraction']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => __('fraction_typography_separator', 'dynamic-content-for-elementor'), 'label' => 'Separator Typography', 'default' => '', 'selector' => '{{WRAPPER}} .swiper-pagination-fraction .separator', 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'fraction']]);
        $this->add_responsive_control('fraction_space', ['label' => __('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '4', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => -20, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .separator' => 'margin: 0 {{SIZE}}{{UNIT}};'], 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'fraction']]);
        $this->add_control('bullets_options_heading', ['label' => __('Bullets Options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'bullets']]);
        $this->add_control('dynamicBullets', ['label' => __('Dynamic Bullets', 'dynamic-content-for-elementor'), 'description' => __('Enable it if you use bullets pagination with a lot of slides. So it will keep only few bullets visible at the same time.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => ['bullets', 'custom']]]);
        $this->add_control('bullets_style', ['label' => __('Bullets Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['default' => __('Default', 'dynamic-content-for-elementor'), 'shamso' => __('Dots', 'dynamic-content-for-elementor'), 'timiro' => __('Circles', 'dynamic-content-for-elementor'), 'xusni' => __('Vertical Bars', 'dynamic-content-for-elementor'), 'etefu' => __('Bars', 'dynamic-content-for-elementor'), 'ubax' => __('Square', 'dynamic-content-for-elementor'), 'magool' => __('Lines', 'dynamic-content-for-elementor')], 'default' => 'default', 'frontend_available' => \true, 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'bullets', $this->get_control_id('dynamicBullets') => '']]);
        $this->add_control('bullets_numbers', ['label' => __('Show numbers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true, 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('bullets_style!') => 'default', $this->get_control_id('pagination_type') => 'bullets', $this->get_control_id('dynamicBullets') => '']]);
        $this->add_control('bullets_number_color', ['label' => __('Numbers Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet .swiper-pagination-bullet-title' => 'color: {{VALUE}}'], 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('bullets_style!') => 'default', $this->get_control_id('pagination_type') => 'bullets', $this->get_control_id('dynamicBullets') => '', $this->get_control_id('bullets_numbers') => 'yes']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'bullets_number_typography', 'label' => __('Numbers Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet .swiper-pagination-bullet-title', 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('bullets_style!') => 'default', $this->get_control_id('pagination_type') => 'bullets', $this->get_control_id('dynamicBullets') => '', $this->get_control_id('bullets_numbers') => 'yes']]);
        $this->add_control('bullets_style_heading', ['label' => __('Bullets Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'bullets']]);
        $this->add_control('bullets_color', ['label' => __('Bullets Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets.nav--default .swiper-pagination-bullet, {{WRAPPER}} .swiper-pagination-bullets.nav--ubax .swiper-pagination-bullet:after, {{WRAPPER}} .swiper-pagination-bullets.nav--shamso .swiper-pagination-bullet:before, {{WRAPPER}} .swiper-pagination-bullets.nav--xusni .swiper-pagination-bullet:before, {{WRAPPER}} .swiper-pagination-bullets.nav--etefu .swiper-pagination-bullet, {{WRAPPER}} .swiper-pagination-bullets.nav--timiro .swiper-pagination-bullet, {{WRAPPER}} .swiper-pagination-bullets.nav--magool .swiper-pagination-bullet:after' => 'background-color: {{VALUE}};'], 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'bullets']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_bullet', 'label' => __('Bullets border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet', 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'bullets']]);
        $this->add_control('current_bullet_color', ['label' => __('Active bullet color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets.nav--default .swiper-pagination-bullet-active, {{WRAPPER}} .swiper-pagination-bullets.nav--ubax .swiper-pagination-bullet-active:after, {{WRAPPER}} .swiper-pagination-bullets.nav--shamso .swiper-pagination-bullet:not(.swiper-pagination-bullet-active), {{WRAPPER}} .swiper-pagination-bullets.nav--shamso .swiper-pagination-bullet-active:before, {{WRAPPER}} .swiper-pagination-bullets.nav--xusni .swiper-pagination-bullet-active:before, {{WRAPPER}} .swiper-pagination-bullets.nav--etefu .swiper-pagination-bullet-active:before, {{WRAPPER}} .swiper-pagination-bullets.nav--timiro .swiper-pagination-bullet-active:before, {{WRAPPER}} .swiper-pagination-bullets.nav--magool .swiper-pagination-bullet-active:after' => 'background-color: {{VALUE}};', '{{WRAPPER}} .swiper-pagination-bullets.nav--shamso .swiper-pagination-bullet-active::after' => 'box-shadow: inset 0 0 0 3px {{VALUE}};'], 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'bullets']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_current_bullet', 'label' => __('Active bullet border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active:not(.nav--ubax):not(.nav--magool), {{WRAPPER}} .swiper-pagination-bullets.nav--ubax .swiper-pagination-bullet-active::after', 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'bullets']]);
        // -------------- Transform
        $this->add_control('pagination_transform_popover', ['label' => __('Transform', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::POPOVER_TOGGLE, 'label_off' => __('Default', 'dynamic-content-for-elementor'), 'label_on' => __('Custom', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'bullets']]);
        $this->get_parent()->start_popover();
        $this->add_responsive_control('pagination_bullets_opacity', ['label' => __('Opacity (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 1, 'min' => 0.1, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet:not(.swiper-pagination-bullet-active)' => 'opacity: {{SIZE}};'], 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'bullets', $this->get_control_id('pagination_transform_popover') => 'yes']]);
        $this->add_responsive_control('pagination_bullets_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 50]], 'selectors' => ['{{WRAPPER}} .swiper-container-horizontal > .swiper-pagination-bullets .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}};', '{{WRAPPER}} .swiper-container-vertical > .swiper-pagination-bullets .swiper-pagination-bullet' => 'margin: {{SIZE}}{{UNIT}} 0;'], 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'bullets', $this->get_control_id('pagination_transform_popover') => 'yes']]);
        $this->add_responsive_control('pagination_bullets_dimension', ['label' => __('Bullets Dimension', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .swiper-container-horizontal > .swiper-pagination-bullets.swiper-pagination-bullets-dynamic' => 'height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .swiper-container-vertical > .swiper-pagination-bullets.swiper-pagination-bullets-dynamic' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'bullets', $this->get_control_id('pagination_transform_popover') => 'yes']]);
        $this->get_parent()->end_popover();
        $this->add_control('pagination_position_popover', ['label' => __('Position', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE, 'label_off' => __('Default', 'dynamic-content-for-elementor'), 'label_on' => __('Custom', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'bullets']]);
        $this->get_parent()->start_popover();
        $this->add_responsive_control('h_pagination_position', ['label' => __('Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['text-align: left; left: 0; transform: translate3d(0,0,0);' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'text-align: center; left: 50%; transform: translate3d(-50%,0,0);' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'text-align: right; left: auto; right: 0; transform: translate3d(0,0,0);' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-container-horizontal > .swiper-pagination-bullets' => '{{VALUE}}'], 'condition' => [$this->get_control_id('useNavigation') => 'yes', $this->get_control_id('pagination_position_popover') => 'yes', $this->get_control_id('direction_slider') => 'horizontal']]);
        $this->add_responsive_control('v_pagination_position', ['label' => __('Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['top: 0; transform: translate3d(0,0,0);' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], 'top: 50%; transform: translate3d(0,-50%,0);' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'top: auto; bottom: 0; transform: translate3d(0,0,0);' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom']], 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-container-vertical > .swiper-pagination-bullets' => '{{VALUE}}'], 'condition' => [$this->get_control_id('useNavigation') => 'yes', $this->get_control_id('pagination_position_popover') => 'yes', $this->get_control_id('direction_slider') => 'vertical']]);
        $this->add_responsive_control('pagination_bullets_posy', ['label' => __('Shift', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '20', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => -160, 'max' => 160]], 'selectors' => ['{{WRAPPER}} .swiper-container-horizontal > .swiper-pagination-bullets' => ' bottom: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .swiper-container-vertical > .swiper-pagination-bullets' => ' right: {{SIZE}}{{UNIT}};'], 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'bullets', $this->get_control_id('pagination_position_popover') => 'yes']]);
        $this->get_parent()->end_popover();
        $this->add_control('progress_heading', ['label' => __('Progress', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'progressbar']]);
        $this->add_control('progress_color', ['label' => __('Bar Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-progressbar .swiper-pagination-progressbar-fill' => 'background-color: {{VALUE}};'], 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'progressbar']]);
        $this->add_control('progressbar_bg_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-progressbar' => 'background-color: {{VALUE}};'], 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'progressbar']]);
        $this->add_responsive_control('progressbal_size', ['label' => __('Progressbar Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '4', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 80]], 'selectors' => ['{{WRAPPER}} .swiper-container-horizontal > .swiper-pagination-progressbar' => 'height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .swiper-container-vertical > .swiper-pagination-progressbar' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => [$this->get_control_id('usePagination') => 'yes', $this->get_control_id('pagination_type') => 'progressbar']]);
        $this->end_controls_tab();
        // -----Tab scrollbar
        $this->start_controls_tab('tab_carousel_scrollbar', ['label' => __('Scroll', 'dynamic-content-for-elementor')]);
        $this->add_control('useScrollbar', ['label' => __('Scrollbar', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->add_control('scrollbar_draggable', ['label' => __('Draggable', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => [$this->get_control_id('useScrollbar') => 'yes']]);
        $this->add_control('scrollbar_hide', ['label' => __('Hide', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => [$this->get_control_id('useScrollbar') => 'yes']]);
        $this->add_control('scrollbar_style_popover', ['label' => __('Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::POPOVER_TOGGLE, 'label_off' => __('Default', 'dynamic-content-for-elementor'), 'label_on' => __('Custom', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'condition' => [$this->get_control_id('useScrollbar') => 'yes']]);
        $this->get_parent()->start_popover();
        $this->add_control('scrollbar_color', ['label' => __('Bar Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-scrollbar .swiper-scrollbar-drag' => 'background: {{VALUE}};'], 'condition' => [$this->get_control_id('useScrollbar') => 'yes', $this->get_control_id('scrollbar_style_popover') => 'yes']]);
        $this->add_control('scrollbar_bg_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-scrollbar' => 'background: {{VALUE}};'], 'condition' => [$this->get_control_id('useScrollbar') => 'yes', $this->get_control_id('scrollbar_style_popover') => 'yes']]);
        $this->add_responsive_control('scrollbar_size', ['label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'vh'], 'range' => ['px' => ['min' => 1, 'max' => 100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-container-horizontal > .swiper-scrollbar' => 'height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .swiper-container-vertical > .swiper-scrollbar' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => [$this->get_control_id('useScrollbar') => 'yes', $this->get_control_id('scrollbar_style_popover') => 'yes']]);
        $this->get_parent()->end_popover();
        $this->end_controls_tab();
        $this->start_controls_tab('tab_carousel_autoplay', ['label' => __('Autoplay', 'dynamic-content-for-elementor')]);
        $this->add_control('useAutoplay', ['label' => __('Autoplay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('autoplay', ['label' => __('Autoplay Delay (ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '4000', 'min' => 0, 'max' => 30000, 'step' => 100, 'frontend_available' => \true, 'condition' => [$this->get_control_id('useAutoplay') => 'yes']]);
        $this->add_control('autoplayStopOnLast', ['label' => __('Autoplay stop on last slide', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => [$this->get_control_id('useAutoplay') => 'yes', $this->get_control_id('autoplay!') => '']]);
        $this->add_control('autoplayDisableOnInteraction', ['label' => __('Autoplay Disable on interaction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => [$this->get_control_id('useAutoplay') => 'yes', $this->get_control_id('autoplay!') => '']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_carousel_freemode', ['label' => __('Free Mode', 'dynamic-content-for-elementor')]);
        $this->add_control('freeMode', ['label' => __('Free Mode', 'dynamic-content-for-elementor'), 'description' => __('Slides will not have fixed positions', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('freeModeMomentum', ['label' => __('Free Mode Momentum', 'dynamic-content-for-elementor'), 'description' => __('Slide will keep moving for a while after you release it', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => [$this->get_control_id('freeMode') => 'yes']]);
        $this->add_control('freeModeMomentumRatio', ['label' => __('Free Mode Momentum Ratio', 'dynamic-content-for-elementor'), 'description' => __('Higher value produces larger momentum distance after you release slider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => [$this->get_control_id('freeMode') => 'yes', $this->get_control_id('freeModeMomentum') => 'yes']]);
        $this->add_control('freeModeMomentumVelocityRatio', ['label' => __('Free Mode Momentum Velocity Ratio', 'dynamic-content-for-elementor'), 'description' => __('Higher value produces larger momentum speed after you release slider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => [$this->get_control_id('freeMode') => 'yes', $this->get_control_id('freeModeMomentum') => 'yes']]);
        $this->add_control('freeModeMomentumBounce', ['label' => __('Free Mode Momentum Bounce', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => [$this->get_control_id('freeMode') => 'yes']]);
        $this->add_control('freeModeMomentumBounceRatio', ['label' => __('Free Mode Momentum Bounce Ratio', 'dynamic-content-for-elementor'), 'description' => __('Higher value produces larger momentum bounce effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => [$this->get_control_id('freeMode') => 'yes', $this->get_control_id('freeModeMomentumBounce') => 'yes']]);
        $this->add_control('freeModeMinimumVelocity', ['label' => __('Free Mode Momentum Velocity Ratio', 'dynamic-content-for-elementor'), 'description' => __('Minimum touchmove-velocity required to trigger free mode momentum', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0.02, 'min' => 0, 'max' => 1, 'step' => 0.01, 'frontend_available' => \true, 'condition' => [$this->get_control_id('freeMode') => 'yes']]);
        $this->add_control('freeModeSticky', ['label' => __('Free Mode Sticky', 'dynamic-content-for-elementor'), 'description' => __('Snap to slides positioned in free mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => [$this->get_control_id('freeMode') => 'yes']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_carousel_options', ['label' => __('Other', 'dynamic-content-for-elementor')]);
        $this->add_responsive_control('slidesOffsetBefore', ['label' => __('Slides Offset Before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'min' => 0, 'max' => 100, 'step' => 1, 'frontend_available' => \true]);
        $this->add_responsive_control('slidesOffsetAfter', ['label' => __('Slides Offset After', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'min' => 0, 'max' => 100, 'step' => 1, 'frontend_available' => \true]);
        $this->add_control('slidesPerColumnFill', ['label' => __('Slides per Column Fill', 'dynamic-content-for-elementor'), 'description' => __('Transition effect from the slides.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['row' => __('Row', 'dynamic-content-for-elementor'), 'column' => __('Column', 'dynamic-content-for-elementor')], 'default' => 'row', 'frontend_available' => \true]);
        $this->add_control('loop', ['label' => __('Loop', 'dynamic-content-for-elementor'), 'description' => __('Set to true to enable continuous loop mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('centeredSlides', ['label' => __('Centered Slides', 'dynamic-content-for-elementor'), 'description' => __('Active slide will be centered, not always on the left side', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'separator' => 'before', 'condition' => [$this->get_control_id('effects!') => ['cube', 'flip']]]);
        $this->add_control('centeredSlidesBounds', ['label' => __('Centered Slides Bounds', 'dynamic-content-for-elementor'), 'description' => __('Active slide will be centered without adding gaps at the beginning and end of slider. Required Centered Slides activated. Not intended to be used with loop or pagination.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => [$this->get_control_id('effects!') => ['cube', 'flip'], $this->get_control_id('centeredSlides') => 'yes']]);
        $this->add_control('grabCursor', ['label' => __('Grab Cursor', 'dynamic-content-for-elementor'), 'description' => __('This option may improve desktop usability. The user will see the “grab” cursor when hover on carousel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('keyboardControl', ['label' => __('Keyboard Control', 'dynamic-content-for-elementor'), 'description' => __('Set to true to enable keyboard control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('mousewheelControl', ['label' => __('Mousewheel Control', 'dynamic-content-for-elementor'), 'description' => __('Enables navigation through slides using mouse wheel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }
    protected function render_loop_start()
    {
        $this->add_direction();
        parent::render_loop_start();
    }
    protected function render_loop_end()
    {
        ?>
		</div>
		<?php 
        if ($this->get_instance_value('useScrollbar')) {
            echo '<div class="swiper-scrollbar"></div>';
        }
        ?>
		</div>

		<?php 
        $use_pagination = $this->get_instance_value('usePagination');
        $use_navigation = $this->get_instance_value('useNavigation');
        if ($use_pagination || $use_navigation) {
            ?>
			<div class="dce-carousel-controls">
			<?php 
            if ($use_pagination) {
                $this->render_pagination();
            }
            if ($use_navigation) {
                $this->render_navigation();
            }
            ?>
			</div>
		<?php 
        }
    }
    /**
     * Render Pagination
     *
     * @return void
     */
    protected function render_pagination()
    {
        $bullets_style = $this->get_instance_value('bullets_style');
        $style_pagination = $this->get_instance_value('pagination_type');
        $dynamic_bullets = $this->get_instance_value('dynamicBullets');
        $bullets_class = !empty($bullets_style) && $style_pagination == 'bullets' && !$dynamic_bullets ? ' dce-nav-style nav--' . $bullets_style : ' nav--default';
        $this->add_direction('container-pagination');
        $this->get_parent()->set_render_attribute('container-pagination', 'class', ['dce-container-pagination', 'swiper-container-' . $this->get_instance_value('direction_slider')]);
        $this->get_parent()->set_render_attribute('pagination', 'class', ['swiper-pagination', 'pagination-' . $this->get_parent()->get_id() . $bullets_class]);
        ?>
		<div <?php 
        echo $this->get_parent()->get_render_attribute_string('container-pagination');
        ?>>
			<div <?php 
        echo $this->get_parent()->get_render_attribute_string('pagination');
        ?>>
			</div>
		</div>
		<?php 
    }
    /**
     * Render Navigation
     *
     * @return void
     */
    protected function render_navigation()
    {
        $this->get_parent()->set_render_attribute('container-navigation', 'class', ['dce-container-navigation', 'swiper-container-' . $this->get_instance_value('direction_slider')]);
        $this->get_parent()->set_render_attribute('carousel-left', 'class', ['swiper-button-left', 'left-' . $this->get_parent()->get_id()]);
        $this->get_parent()->set_render_attribute('carousel-right', 'class', ['swiper-button-right', 'right-' . $this->get_parent()->get_id()]);
        // Add Arrows
        $left_arrow = $this->get_instance_value('previous_arrow');
        $right_arrow = $this->get_instance_value('next_arrow');
        if (!empty($left_arrow['value']) || !empty($right_arrow['value'])) {
            ?>
			<div <?php 
            echo $this->get_parent()->get_render_attribute_string('container-navigation');
            ?>>
				<?php 
            // Arrow - Previous
            ?>
				<div <?php 
            echo $this->get_parent()->get_render_attribute_string('carousel-left');
            ?>>
					<?php 
            \Elementor\Icons_Manager::render_icon($this->get_instance_value('previous_arrow'), ['aria-hidden' => 'true']);
            ?>
				</div>
				<?php 
            // Arrow - Next
            ?>
				<div <?php 
            echo $this->get_parent()->get_render_attribute_string('carousel-right');
            ?>>
					<?php 
            \Elementor\Icons_Manager::render_icon($this->get_instance_value('next_arrow'), ['aria-hidden' => 'true']);
            ?>
				</div>
			</div>
			<?php 
        } else {
            ?>
			<div <?php 
            echo $this->get_parent()->get_render_attribute_string('container-navigation');
            ?>>
				<?php 
            // Arrow - Previous
            ?>
				<div <?php 
            echo $this->get_parent()->get_render_attribute_string('carousel-left');
            ?>>
					<svg x="-10px" y="-10px" width="85.039px" height="85.039px" viewBox="378.426 255.12 85.039 85.039" xml:space="preserve">
						<line fill="none" stroke="#000000" stroke-width="1.3845" stroke-dasharray="0,0" stroke-miterlimit="10" x1="382.456" y1="298.077" x2="458.375" y2="298.077"/>
						<polyline fill="none" stroke="#000000" stroke-width="1.3845" stroke-dasharray="0,0" stroke-miterlimit="10" points="416.287,331.909,382.456,298.077,416.287,264.245 "/>
					</svg>
				</div>
				<?php 
            // Arrow - Next
            ?>
				<div <?php 
            echo $this->get_parent()->get_render_attribute_string('carousel-right');
            ?>>
					<svg xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="85.039px" height="85.039px" viewBox="378.426 255.12 85.039 85.039" xml:space="preserve">
						<line fill="none" stroke="#000000" stroke-width="1.3845" stroke-miterlimit="10" x1="458.375" y1="298.077" x2="382.456" y2="298.077"/>
						<polyline fill="none" stroke="#000000" stroke-width="1.3845" stroke-miterlimit="10" points="424.543,264.245,458.375,298.077,424.543,331.909 "/>
					</svg>
				</div>
			</div>
			<?php 
        }
    }
    public function get_container_class()
    {
        if (\Elementor\Plugin::$instance->experiments->is_feature_active('e_swiper_latest')) {
            return 'swiper dce-skin-' . $this->get_id();
        }
        return 'swiper-container dce-skin-' . $this->get_id();
    }
    public function get_wrapper_class()
    {
        return 'swiper-wrapper dce-wrapper-' . $this->get_id();
    }
    public function get_item_class()
    {
        return 'swiper-slide dce-item-' . $this->get_id();
    }
}
