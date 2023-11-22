<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Controls\Group_Control_Filters_CSS;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class AcfSlider extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['photoswipe', 'photoswipe-ui', 'imagesloaded', 'dce-acfslider-js'];
    }
    public function get_style_depends()
    {
        return ['dce-photoSwipe_default', 'dce-photoSwipe_skin', 'dce-acfslider'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $swiper_class = \Elementor\Plugin::$instance->experiments->is_feature_active('e_swiper_latest') ? 'swiper' : 'swiper-container';
        $this->start_controls_section('section_content', ['label' => __('ACF Slider', 'dynamic-content-for-elementor')]);
        $this->add_control('acf_field_list', ['label' => __('ACF Gallery Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'gallery']);
        $this->add_control('acf_gallery_from', ['label' => __('Retrieve the field from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'current_post', 'options' => ['current_post' => __('Current Post', 'dynamic-content-for-elementor'), 'current_user' => __('Current User', 'dynamic-content-for-elementor'), 'current_author' => __('Current Author', 'dynamic-content-for-elementor'), 'current_term' => __('Current Term', 'dynamic-content-for-elementor'), 'options_page' => __('Options Page', 'dynamic-content-for-elementor')]]);
        $this->add_responsive_control('align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'prefix_class' => 'align-', 'selectors' => ['{{WRAPPER}} .dynamic_acfslider' => 'text-align: {{VALUE}};']]);
        $this->add_control('mode_heading', ['label' => __('Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('force_width', ['label' => __('Force Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'render_type' => 'template', 'condition' => ['force_height' => '', 'use_bg_image' => '']]);
        $this->add_responsive_control('size_img', ['label' => __('Size (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['%'], 'default' => ['unit' => '%', 'size' => 100], 'tablet_default' => ['unit' => '%'], 'mobile_default' => ['unit' => '%'], 'range' => ['%' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .wrap-item-acfslider' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['force_width' => 'yes']]);
        $this->add_control('force_height', ['label' => __('Force Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'prefix_class' => 'forceheignt-', 'render_type' => 'template', 'condition' => ['force_width' => '', 'use_bg_image' => '']]);
        $this->add_responsive_control('height', ['label' => __('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'description' => __('If the value is empty the height is automatic.', 'dynamic-content-for-elementor'), 'default' => ['size' => ''], 'size_units' => ['px', 'rem', 'vh'], 'range' => ['rem' => ['min' => 0, 'max' => 100], 'px' => ['min' => 0, 'max' => 1200], 'vw' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dyncontel-swiper .' . $swiper_class => 'height: {{SIZE}}{{UNIT}};'], 'frontend_available' => \true, 'condition' => ['force_height' => 'yes']]);
        $this->add_control('use_bg_image', ['label' => __('Use as a background image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'condition' => ['force_width' => '', 'force_height' => '']]);
        $this->add_control('bg_position', ['label' => __('Background position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'center center', 'options' => ['' => __('Default', 'dynamic-content-for-elementor'), 'top left' => __('Top Left', 'dynamic-content-for-elementor'), 'top center' => __('Top Center', 'dynamic-content-for-elementor'), 'top right' => __('Top Right', 'dynamic-content-for-elementor'), 'center left' => __('Center Left', 'dynamic-content-for-elementor'), 'center center' => __('Center Center', 'dynamic-content-for-elementor'), 'center right' => __('Center Right', 'dynamic-content-for-elementor'), 'bottom left' => __('Bottom Left', 'dynamic-content-for-elementor'), 'bottom center' => __('Bottom Center', 'dynamic-content-for-elementor'), 'bottom right' => __('Bottom Right', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .acfslider-bg-image' => 'background-position: {{VALUE}};'], 'condition' => ['use_bg_image' => 'yes']]);
        $this->add_responsive_control('height_bg_img', ['label' => __('Background Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'vh'], 'default' => ['unit' => 'px', 'size' => 400], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'range' => ['px' => ['min' => 80, 'max' => 800, 'step' => 1], 'vh' => ['min' => 0, 'max' => 100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-slide' => 'height: {{SIZE}}{{UNIT}};'], 'condition' => ['use_bg_image' => 'yes']]);
        $this->add_control('space_heading', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('spaceV', ['label' => __('Vertical space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', 'em', 'vh'], 'range' => ['em' => ['min' => 0, 'max' => 30], 'px' => ['min' => 0, 'max' => 150], 'vw' => ['min' => 0, 'max' => 50]], 'selectors' => ['{{WRAPPER}} .dyncontel-swiper .' . $swiper_class => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};'], 'frontend_available' => \true]);
        $this->add_responsive_control('spaceH', ['label' => __('Horizontal space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', 'em', 'vh'], 'range' => ['em' => ['min' => 0, 'max' => 30], 'px' => ['min' => 0, 'max' => 150], 'vw' => ['min' => 0, 'max' => 50]], 'selectors' => ['{{WRAPPER}} .dyncontel-swiper .' . $swiper_class => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};'], 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_settings', ['label' => __('Image Settings', 'dynamic-content-for-elementor')]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'size', 'label' => __('Image Size', 'dynamic-content-for-elementor'), 'default' => 'large', 'condition' => []]);
        $this->add_control('use_desc', ['label' => __('Description', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('None', 'dynamic-content-for-elementor'), 'caption' => __('Caption', 'dynamic-content-for-elementor'), 'description' => __('Description', 'dynamic-content-for-elementor')], 'default' => '']);
        $this->add_control('style_heading', ['label' => __('Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('enable_image_style', ['label' => __('Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'image_border', 'label' => __('Image Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .wrap-item-acfslider img', 'condition' => ['enable_image_style' => 'yes']]);
        $this->add_control('image_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .wrap-item-acfslider img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['enable_image_style' => 'yes']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'image_box_shadow', 'selector' => '{{WRAPPER}} .acfslider-item img']);
        $this->add_group_control(Group_Control_Filters_CSS::get_type(), ['name' => 'filters_image', 'label' => __('Filters image', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .acfslider-item img']);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_settings', ['label' => __('Slider Settings', 'dynamic-content-for-elementor')]);
        $this->add_control('effects', ['label' => __('Transition effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['slide' => __('Slide', 'dynamic-content-for-elementor'), 'fade' => __('Fade', 'dynamic-content-for-elementor'), 'cube' => __('Cube', 'dynamic-content-for-elementor'), 'coverflow' => __('Coverflow', 'dynamic-content-for-elementor'), 'flip' => __('Flip', 'dynamic-content-for-elementor')], 'default' => 'slide', 'frontend_available' => \true]);
        $this->add_control('directionSlide', ['label' => __('Direction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'options' => ['horizontal' => __('Horizontal', 'dynamic-content-for-elementor'), 'vertical' => __('Vertical', 'dynamic-content-for-elementor')], 'default' => 'horizontal', 'frontend_available' => \true]);
        $this->add_control('speedSlide', ['label' => __('Speed', 'dynamic-content-for-elementor'), 'description' => __('Duration of transition between slides (in ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 300, 'min' => 0, 'max' => 3000, 'step' => 10, 'frontend_available' => \true]);
        $this->add_control('centeredSlides', ['label' => __('Centered Slides', 'dynamic-content-for-elementor'), 'description' => __('If true, then active slide will be centered, not always on the left side.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_responsive_control('spaceBetween', ['label' => __('Space Between', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100, 'step' => 1]], 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('more_options', ['label' => __('Slides Grid', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('slidesPerView', ['label' => __('Slides Per View', 'dynamic-content-for-elementor'), 'description' => __('Number of slides per view (slides visible at the same time on slider\'s container). If you use it with "auto" value and along with loop: true then you need to specify loopedSlides parameter with amount of slides to loop (duplicate). SlidesPerView: \'auto\' is currently not compatible with multirow mode, when slidesPerColumn greater than one', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '1', 'min' => 1, 'max' => 12, 'step' => 1, 'frontend_available' => \true]);
        $this->add_responsive_control('slidesColumn', ['label' => __('Slides Column', 'dynamic-content-for-elementor'), 'description' => __('Number of slides per column, for multirow layout.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '1', 'min' => 1, 'max' => 4, 'step' => 1, 'frontend_available' => \true]);
        $this->add_responsive_control('slidesPerGroup', ['label' => __('Slides Per Group', 'dynamic-content-for-elementor'), 'description' => __('Set numbers of slides to define and enable group sliding. Useful to use with slidesPerView > 1', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'tablet_default' => '', 'mobile_default' => '', 'min' => 1, 'max' => 12, 'step' => 1, 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_navigation', ['label' => __('Navigation', 'dynamic-content-for-elementor')]);
        $this->add_control('useNavigation', ['label' => __('Navigation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_responsive_control('navigation_size', ['label' => __('Navigation size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '48', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dynamic_acfslider .swiper-button-prev, {{WRAPPER}} .dynamic_acfslider .swiper-button-next' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; margin-top: calc(-{{SIZE}}{{UNIT}} / 2);'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('navigation_scale', ['label' => __('Arrows scale', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '1', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'selectors' => ['{{WRAPPER}} .dynamic_acfslider .swiper-button-prev svg, {{WRAPPER}} .dynamic_acfslider .swiper-button-next svg' => '-webkit-transform: scale({{SIZE}}); -ms-transform: scale({{SIZE}}); transform: scale({{SIZE}});'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('navigation_position', ['label' => __('Horizontal position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '10', 'unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['px' => ['max' => 100, 'min' => -100, 'step' => 1], '%' => ['max' => 100, 'min' => -100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .swiper-button-next' => 'right: {{SIZE}}{{UNIT}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('vertical_navigation_position', ['label' => __('Vertical position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 50, 'unit' => '%'], 'size_units' => ['px', '%'], 'range' => ['px' => ['max' => 200, 'min' => -200, 'step' => 1], '%' => ['max' => 150, 'min' => -150, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'top: {{SIZE}}{{UNIT}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('navigation_arrow_color', ['label' => __('Arrows color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#000000', 'selectors' => ['{{WRAPPER}} .swiper-button-next path, {{WRAPPER}} .swiper-button-prev path' => 'fill: {{VALUE}};', '{{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev line, {{WRAPPER}} .swiper-button-next polyline, {{WRAPPER}} .swiper-button-prev polyline' => 'stroke: {{VALUE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('navigation_arrow_color_hover', ['label' => __('Arrow color hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#007aff', 'selectors' => ['{{WRAPPER}} .swiper-button-next:hover path, {{WRAPPER}} .swiper-button-prev:hover path' => 'fill: {{VALUE}};', '{{WRAPPER}} .swiper-button-next:hover line, {{WRAPPER}} .swiper-button-prev:hover line, {{WRAPPER}} .swiper-button-next:hover polyline, {{WRAPPER}} .swiper-button-prev:hover polyline' => 'stroke: {{VALUE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('navigation_stroke_1', ['label' => __('Stroke Arrow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev polyline, {{WRAPPER}} .swiper-button-next polyline' => 'stroke-width: {{SIZE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('navigation_stroke_2', ['label' => __('Stroke Line', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev line' => 'stroke-width: {{SIZE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('navigation_tratteggio', ['label' => __('Dashed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0'], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev line, {{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev polyline, {{WRAPPER}} .swiper-button-next polyline' => 'stroke-dasharray: {{SIZE}},{{SIZE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_pagination', ['label' => __('Pagination', 'dynamic-content-for-elementor')]);
        $this->add_control('usePagination', ['label' => __('Pagination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('pagination_type', ['label' => __('Pagination Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['bullets' => __('Bullets', 'dynamic-content-for-elementor'), 'fraction' => __('Fraction', 'dynamic-content-for-elementor'), 'progress' => __('Progress', 'dynamic-content-for-elementor')], 'default' => 'bullets', 'frontend_available' => \true, 'condition' => ['usePagination' => 'yes']]);
        $this->add_control('fraction_separator', ['label' => __('Fraction text separator', 'dynamic-content-for-elementor'), 'description' => __('The text separating the 2 numbers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'frontend_available' => \true, 'default' => '/', 'condition' => ['pagination_type' => 'fraction', 'usePagination' => 'yes']]);
        $this->add_responsive_control('fraction_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '4', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => -20, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .separator' => 'margin: 0 {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_type' => 'fraction', 'usePagination' => 'yes']]);
        $this->add_control('fraction_color', ['label' => __('Numbers color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction > *' => 'color: {{VALUE}};'], 'condition' => ['pagination_type' => 'fraction', 'usePagination' => 'yes']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fraction_typography', 'label' => __('Numbers Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-fraction > *', 'condition' => ['pagination_type' => 'fraction', 'usePagination' => 'yes']]);
        $this->add_control('fraction_current_color', ['label' => __('The color of the current number', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current' => 'color: {{VALUE}};'], 'condition' => ['pagination_type' => 'fraction', 'usePagination' => 'yes']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fraction_typography_current', 'label' => __('Typography current number', 'dynamic-content-for-elementor'), 'default' => '', 'selector' => '{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current', 'condition' => ['pagination_type' => 'fraction', 'usePagination' => 'yes']]);
        $this->add_control('fraction_separator_color', ['label' => __('Separator color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .separator' => 'color: {{VALUE}};'], 'condition' => ['pagination_type' => 'fraction', 'usePagination' => 'yes']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => __('fraction_typography_separator', 'dynamic-content-for-elementor'), 'label' => __('Separator Typography', 'dynamic-content-for-elementor'), 'default' => '', 'selector' => '{{WRAPPER}} .swiper-pagination-fraction .separator', 'condition' => ['pagination_type' => 'fraction', 'usePagination' => 'yes']]);
        $this->add_responsive_control('bullets_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '5', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_type' => 'bullets', 'usePagination' => 'yes']]);
        $this->add_responsive_control('vertical_pagination_position', ['label' => __('Vertical position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0, 'unit' => '%'], 'size_units' => ['px', '%'], 'range' => ['px' => ['max' => 200, 'min' => -200, 'step' => 1], '%' => ['max' => 150, 'min' => -150, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-pagination' => 'bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_type' => ['bullets', 'fraction'], 'usePagination' => 'yes']]);
        $this->add_responsive_control('pagination_bullets', ['label' => __('Bullets size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '8', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_type' => 'bullets', 'usePagination' => 'yes']]);
        $this->add_control('bullets_color', ['label' => __('Bullets Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_type' => 'bullets', 'usePagination' => 'yes']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_bullet', 'label' => __('Bullets border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet', 'condition' => ['pagination_type' => 'bullets', 'usePagination' => 'yes']]);
        $this->add_control('current_bullet_color', ['label' => __('Active bullet color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_type' => 'bullets', 'usePagination' => 'yes']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_current_bullet', 'label' => __('Active bullet border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active', 'condition' => ['pagination_type' => 'bullets', 'usePagination' => 'yes']]);
        $this->add_control('progress_color', ['label' => __('Progress color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-progress' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_type' => 'progress', 'usePagination' => 'yes']]);
        $this->add_control('progressbar_color', ['label' => __('Progressbar color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-progress .swiper-pagination-progressbar' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_type' => 'progress', 'usePagination' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_scrollbar', ['label' => __('Scrollbar', 'dynamic-content-for-elementor')]);
        $this->add_control('useScrollbar', ['label' => __('Scrollbar', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_autoplay', ['label' => __('Autoplay', 'dynamic-content-for-elementor')]);
        $this->add_control('useAutoplay', ['label' => __('Autoplay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->add_control('autoplay', ['label' => __('Autoplay Delay (ms)', 'dynamic-content-for-elementor'), 'description' => __('Delay between transitions (in ms). If this parameter is not specified (by default), autoplay will be disabled', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '', 'min' => 0, 'max' => 15000, 'step' => 100, 'frontend_available' => \true, 'condition' => ['useAutoplay' => 'yes']]);
        $this->add_control('autoplayStopOnHover', ['label' => __('Autoplay stop on hover', 'dynamic-content-for-elementor'), 'description' => __('Enable this parameter and autoplay will be stopped on hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['useAutoplay' => 'yes']]);
        $this->add_control('autoplayStopOnLast', ['label' => __('Autoplay stop on last slide', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true, 'condition' => ['useAutoplay' => 'yes']]);
        $this->add_control('autoplayDisableOnInteraction', ['label' => __('Disable Autoplay on interaction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['useAutoplay' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_loop', ['label' => __('Loop', 'dynamic-content-for-elementor')]);
        $this->add_control('loop', ['label' => __('Loop', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_progress', ['label' => __('Progress', 'dynamic-content-for-elementor')]);
        $this->add_control('watchSlidesProgress', ['label' => __('Watch Slides Progress', 'dynamic-content-for-elementor'), 'description' => __('Enable this feature to calculate each slides progress', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('watchSlidesVisibility', ['label' => __('Watch Slides Visibility', 'dynamic-content-for-elementor'), 'description' => __('WatchSlidesProgress should be enabled. Enable this option and slides that are in viewport will have additional visible classes', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true, 'condition' => ['watchSlidesProgress' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_freemode', ['label' => __('Freemode', 'dynamic-content-for-elementor')]);
        $this->add_control('freeMode', ['label' => __('Free Mode', 'dynamic-content-for-elementor'), 'description' => __('The slides will not have fixed positions', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('freeModeMinimumVelocity', ['label' => __('Free Mode Momentum Velocity Ratio', 'dynamic-content-for-elementor'), 'description' => __('Higher value produces larger momentum bounce effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0.02, 'min' => 0, 'max' => 1, 'step' => 0.01, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->add_control('freeModeMomentum', ['label' => __('Free Mode Momentum', 'dynamic-content-for-elementor'), 'description' => __('Slides will keep moving for a while after you release it', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->add_control('freeModeMomentumRatio', ['label' => __('Free Mode Momentum Ratio', 'dynamic-content-for-elementor'), 'description' => __('Higher value produces larger momentum distance after you release slider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentum' => 'yes']]);
        $this->add_control('freeModeMomentumVelocityRatio', ['label' => __('Free Mode Momentum Velocity Ratio', 'dynamic-content-for-elementor'), 'description' => __('Higher value produces larger momentum speed after you release slider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentum' => 'yes']]);
        $this->add_control('freeModeMomentumBounce', ['label' => __('Free Mode Momentum Bounce', 'dynamic-content-for-elementor'), 'description' => __('Set to false if you want to disable momentum bounce in free mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentum' => 'yes']]);
        $this->add_control('freeModeMomentumBounceRatio', ['label' => __('Free Mode Momentum Bounce Ratio', 'dynamic-content-for-elementor'), 'description' => __('Higher value produces bigger rebound effect of the moment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentumBounce' => 'yes']]);
        $this->add_control('freeModeSticky', ['label' => __('Free Mode Sticky', 'dynamic-content-for-elementor'), 'description' => __('Minimum touchmove-velocity required to trigger free mode momentum', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_keyboardMousewheel', ['label' => __('Keyboard / Mousewheel', 'dynamic-content-for-elementor')]);
        $this->add_control('keyboardControl', ['label' => __('Keyboard Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('mousewheelControl', ['label' => __('Mousewheel Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_special', ['label' => __('Other Options', 'dynamic-content-for-elementor')]);
        $this->add_control('setWrapperSize', ['label' => __('Set Wrapper Size', 'dynamic-content-for-elementor'), 'description' => __('Enable this option and plugin will set width/height on swiper wrapper equal to total size of all slides. Mostly should be used as compatibility fallback option for browser that don\'t support flexbox layout well', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('virtualTranslate', ['label' => __('Virtual Translate', 'dynamic-content-for-elementor'), 'description' => __('Enable this option and swiper will be operated as usual except it will not move, real translate values on wrapper will not be set. Useful when you may need to create custom slide transition', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->add_control('autoHeight', ['label' => __('Auto Height', 'dynamic-content-for-elementor'), 'description' => __('Set to true and slider wrapper will adopt its height to the height of the currently active slide', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->add_control('roundLengths', ['label' => __('Round Lengths', 'dynamic-content-for-elementor'), 'description' => __('Set to true to round values of slides width and height to prevent blurry texts on usual resolution screens (if you have such)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->add_control('nested', ['label' => __('Nested', 'dynamic-content-for-elementor'), 'description' => __('Set to true on nested Swiper for correct touch events interception. Use only on nested swipers that use same direction as the parent one', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->add_control('grabCursor', ['label' => __('Grab Cursor', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_lightbox_effects', ['label' => 'Lightbox Settings', 'dynamic-content-for-elementor']);
        $this->add_control('enable_lightbox', ['label' => __('LightBox', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('lightbox_type', ['label' => __('Lightbox Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor'), 'photoswipe' => 'Photoswipe'], 'default' => '', 'condition' => ['enable_lightbox' => 'yes']]);
        $this->add_control('enable_overlay_hover', ['label' => __('Overlay Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'condition' => ['enable_lightbox' => 'yes']]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'background', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .acfslider-overlay_hover', 'popover' => \true, 'condition' => ['enable_overlay_hover' => 'yes']]);
        $this->add_control('hover_effects', ['label' => __('Hover Effects', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('None', 'dynamic-content-for-elementor'), 'zoom' => __('Zoom', 'dynamic-content-for-elementor')], 'default' => '', 'prefix_class' => 'hovereffect-', 'condition' => ['enable_lightbox' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'condition' => ['acf_gallery_from' => 'current_post']]);
        $this->add_control('data_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'description' => __('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => __('Same', 'dynamic-content-for-elementor'), 'label_off' => __('other', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_control('other_post_source', ['label' => __('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = '';
        switch ($settings['acf_gallery_from']) {
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
        $acf_gallery = Helper::get_acf_field_value($settings['acf_field_list'], $id_page);
        if (!$acf_gallery) {
            return;
        }
        $enable_lightbox = '';
        $lightbox_type = '';
        $elementor_lightbox = '';
        $data_elementor_open_lightbox_base = '';
        $data_elementor_slideshow = '';
        if ($settings['enable_lightbox'] != '') {
            $enable_lightbox = ' is-lightbox';
        }
        if ($settings['lightbox_type'] == 'photoswipe') {
            $lightbox_type = ' ' . $settings['lightbox_type'];
            $data_elementor_open_lightbox_base = 'data-elementor-open-lightbox="no"';
        } else {
            global $acfslider_counter;
            if (!isset($acfslider_counter)) {
                $acfslider_counter = 1;
            } else {
                $acfslider_counter++;
            }
            $lightbox_type = ' gallery';
            $data_elementor_slideshow = ' data-elementor-lightbox-slideshow="' . $this->get_id() . '_' . $acfslider_counter . '"';
            $elementor_lightbox = ' gallery-lightbox';
            $data_elementor_open_lightbox_base = 'data-elementor-open-lightbox="yes"';
        }
        $overlay_hover_block = '';
        $overlay_hover_class = '';
        if ('yes' == $settings['enable_overlay_hover']) {
            $overlay_hover_block = '<span class="acfslider-overlay_hover"></span>';
            $overlay_hover_class = ' is-overlay ';
        }
        ?>

		<div class="dynamic_acfslider<?php 
        echo $enable_lightbox . $lightbox_type . $elementor_lightbox . $overlay_hover_class;
        ?>" itemscope itemtype="http://schema.org/ImageGallery">
			<?php 
        $effect = ' dce-' . $settings['effects'];
        $direction = ' dce-direction-' . $settings['directionSlide'];
        $swiper_class = \Elementor\Plugin::$instance->experiments->is_feature_active('e_swiper_latest') ? 'swiper' : 'swiper-container';
        echo '<div class="dyncontel-swiper' . $effect . $direction . '">';
        echo '  <div class="' . $swiper_class . '">';
        echo '    <div class="swiper-wrapper">';
        $counter = 0;
        foreach ($acf_gallery as $image) {
            if (!isset($image['id'])) {
                $img_id = $image;
                $image = $this->get_attachment($img_id);
            }
            $img_id = $image['id'];
            $img_url = $image['url'];
            $img_alt = $image['alt'];
            $img_width = $image['width'];
            $img_height = $image['height'];
            $img_desc = \false;
            $data_elementor_open_lightbox = $data_elementor_open_lightbox_base;
            if ($settings['use_desc']) {
                $use_desc = $settings['use_desc'];
                $img_desc = $image[$use_desc];
                $data_elementor_open_lightbox .= ' data-elementor-lightbox-description="' . \htmlspecialchars($img_desc) . '"';
            }
            $image_url = Group_Control_Image_Size::get_attachment_image_src($img_id, 'size', $settings);
            $bg_image_style = '';
            $bg_image_class = '';
            if ($settings['use_bg_image'] != '') {
                $bg_image_style = ' style="background-image: url(' . $image_url . '); background-repeat: no-repeat; background-size: cover;"';
                $bg_image_class = ' acfslider-bg-image';
            }
            echo '<div class="swiper-slide">';
            echo '<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject"  class="acfslider-item grid-item' . $bg_image_class . '"' . $bg_image_style . '>';
            if ($enable_lightbox != '' && $settings['use_bg_image'] != '') {
                echo '<a class="' . $enable_lightbox . $elementor_lightbox . '" href="' . $img_url . '" itemprop="contentUrl" data-size="' . $img_width . 'x' . $img_height . '"' . $data_elementor_open_lightbox . $data_elementor_slideshow . '>';
            }
            if ($settings['use_bg_image'] != '') {
                echo '<div class="wrap-item-acfslider" style="height: 100%; " >';
            } else {
                echo '<div class="wrap-item-acfslider">';
            }
            if ($enable_lightbox != '' && $settings['use_bg_image'] == '') {
                echo '<a class="' . $enable_lightbox . $elementor_lightbox . '" href="' . $img_url . '" itemprop="contentUrl" data-size="' . $img_width . 'x' . $img_height . '"' . $data_elementor_open_lightbox . $data_elementor_slideshow . '>';
            }
            if ($settings['use_bg_image'] == '') {
                echo '<img src="' . $image_url . '" itemprop="thumbnail" alt="' . $img_alt . '" />';
                echo $overlay_hover_block;
            }
            if ($img_desc) {
                echo '<figcaption itemprop="caption description">' . $img_desc . '</figcaption>';
            }
            if ($enable_lightbox && $settings['use_bg_image'] == '') {
                echo '</a>';
            }
            echo '</div>';
            echo '</figure>';
            if ($enable_lightbox != '' && $settings['use_bg_image'] != '') {
                echo '</a>';
            }
            echo '</div>';
            $counter++;
        }
        echo '   </div>';
        if ($settings['useScrollbar'] != '' && \count($acf_gallery) > 1) {
            // If we need scrollbar
            echo '<div class="swiper-scrollbar"></div>';
        }
        echo '</div>';
        if ($settings['useNavigation'] != '' && \count($acf_gallery) > 1) {
            // Add Arrows
            echo '<div class="swiper-button swiper-button-next next-' . $this->get_id() . '"><svg version="1.1" id="Livello_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
width="85.039px" height="85.039px" viewBox="378.426 255.12 85.039 85.039" enable-background="new 378.426 255.12 85.039 85.039"
xml:space="preserve">
<line fill="none" stroke="#C81517" stroke-width="1.3845" stroke-miterlimit="10" x1="458.375" y1="298.077" x2="382.456" y2="298.077"/>
<polyline fill="none" stroke="#C81517" stroke-width="1.3845" stroke-miterlimit="10" points="424.543,264.245,458.375,298.077,424.543,331.909 "/>
</svg></div>';
            echo '<div class="swiper-button swiper-button-prev prev-' . $this->get_id() . '"><svg version="1.1" id="Livello_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
width="85.039px" height="85.039px" viewBox="378.426 255.12 85.039 85.039" enable-background="new 378.426 255.12 85.039 85.039"
xml:space="preserve">
<line fill="none" stroke="#C81517" stroke-width="1.3845" stroke-dasharray="0,0" stroke-miterlimit="10" x1="382.456" y1="298.077" x2="458.375" y2="298.077"/>
<polyline fill="none" stroke="#C81517" stroke-width="1.3845" stroke-dasharray="0,0" stroke-miterlimit="10" points="416.287,331.909,382.456,298.077,416.287,264.245 "/>
</svg></div>';
        }
        if (!empty($settings['usePagination']) && \count($acf_gallery) > 1) {
            // Add Pagination
            echo '<div class="swiper-container-horizontal"><div class="swiper-pagination pagination-' . $this->get_id() . '"></div></div>';
        }
        echo '  </div>';
        ?>
		</div>

		<?php 
    }
    /**
     * Get Attachment
     *
     * @param string|int $attachment_id
     * @return array<string,mixed>
     */
    protected function get_attachment($attachment_id)
    {
        $attachment_id = \intval($attachment_id);
        //phpstan
        $attachment = get_post($attachment_id);
        $img_src = wp_get_attachment_image_src($attachment_id, 'full');
        return ['id' => $attachment_id, 'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', \true), 'caption' => $attachment->post_excerpt, 'description' => $attachment->post_content, 'href' => get_permalink($attachment->ID), 'src' => $attachment->guid, 'title' => $attachment->post_title, 'url' => $img_src[0], 'width' => $img_src[1], 'height' => $img_src[2]];
    }
}
