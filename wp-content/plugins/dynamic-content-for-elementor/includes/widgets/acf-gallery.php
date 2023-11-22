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
use DynamicContentForElementor\Group_Control_Outline;
use DynamicContentForElementor\Controls\Group_Control_Filters_CSS;
use DynamicContentForElementor\Controls\Group_Control_Transform_Element;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class AcfGallery extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['imagesloaded', 'jquery-masonry', 'dce-wow', 'photoswipe', 'photoswipe-ui', 'dce-diamonds', 'dce-homeycombs', 'justifiedGallery-lib', 'dce-acfgallery'];
    }
    public function get_style_depends()
    {
        return ['animatecss', 'dce-photoSwipe_default', 'dce-photoSwipe_skin', 'dce-justifiedGallery', 'animatecss', 'dce-acfGallery'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => $this->get_title()]);
        $this->add_control('acf_field_list', ['label' => __('ACF Gallery Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'gallery']);
        $this->add_control('acf_gallery_from', ['label' => __('Retrieve the field from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'current_post', 'options' => ['current_post' => __('Current Post', 'dynamic-content-for-elementor'), 'current_user' => __('Current User', 'dynamic-content-for-elementor'), 'current_author' => __('Current Author', 'dynamic-content-for-elementor'), 'current_term' => __('Current Term', 'dynamic-content-for-elementor'), 'options_page' => __('Options Page', 'dynamic-content-for-elementor')]]);
        $this->add_control('enabled_wow', ['label' => __('WOW Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_settings_gallery', ['label' => __('Gallery', 'dynamic-content-for-elementor')]);
        $this->add_control('gallery_type', ['label' => __('Skin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['row' => __('Row', 'dynamic-content-for-elementor'), 'grid' => __('Grid', 'dynamic-content-for-elementor'), 'masonry' => __('Masonry', 'dynamic-content-for-elementor'), 'justified' => __('Justified', 'dynamic-content-for-elementor'), 'single_image' => __('Single Image', 'dynamic-content-for-elementor'), 'diamond' => __('Diamond', 'dynamic-content-for-elementor'), 'hexagon' => __('Hexagon', 'dynamic-content-for-elementor')], 'default' => 'masonry', 'frontend_available' => \true]);
        $this->add_control('limit', ['label' => __('Limit images to be shown', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'condition' => ['gallery_type!' => 'single_image']]);
        $this->add_control('limit_images', ['label' => __('Images to Show', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 100, 'step' => 1, 'default' => 10, 'condition' => ['gallery_type!' => 'single_image', 'limit!' => '']]);
        $this->add_control('single_image_type', ['label' => __('Show', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['first' => __('First', 'dynamic-content-for-elementor'), 'random' => __('Random', 'dynamic-content-for-elementor')], 'default' => 'first', 'condition' => ['gallery_type' => 'single_image']]);
        $this->add_responsive_control('columns_grid', ['label' => __('Columns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 2, 'tablet_default' => 3, 'mobile_default' => 1, 'min' => 1, 'max' => 24, 'selectors' => ['{{WRAPPER}} .acfgallery-item' => 'width: calc(100% / {{VALUE}}); flex: 0 1 calc( 100% / {{VALUE}} );'], 'condition' => ['gallery_type' => ['grid', 'masonry', 'justified']]]);
        $this->add_control('justified_rowHeight', ['label' => __('Row Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['min' => 10, 'max' => 800, 'step' => 1]], 'frontend_available' => \true, 'condition' => ['gallery_type' => 'justified']]);
        $this->add_control('justified_margin', ['label' => __('Space between images', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['min' => 0, 'max' => 100, 'step' => 1]], 'frontend_available' => \true, 'condition' => ['gallery_type' => 'justified']]);
        $this->add_control('justified_lastRow', ['label' => __('Last row', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'nojustify', 'options' => ['justify' => __('Justify', 'dynamic-content-for-elementor'), 'nojustify' => __('Left', 'dynamic-content-for-elementor'), 'center' => __('Center', 'dynamic-content-for-elementor'), 'right' => __('Right', 'dynamic-content-for-elementor'), 'hide' => __('Hide', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'condition' => ['gallery_type' => 'justified']]);
        $this->add_control('column_diamond', ['label' => __('Min diamonds per row', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '4', 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'], 'frontend_available' => \true, 'condition' => ['gallery_type' => 'diamond']]);
        $this->add_responsive_control('size_diamond', ['label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['min' => 20, 'max' => 800, 'step' => 1]], 'frontend_available' => \true, 'selectors' => ['{{WRAPPER}} .diamonds .diamond-box-wrap' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'], 'condition' => ['gallery_type' => 'diamond']]);
        $this->add_control('gap_diamond', ['label' => __('Gap between diamonds', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'min' => 0, 'max' => 100, 'step' => 1, 'frontend_available' => \true, 'condition' => ['gallery_type' => 'diamond']]);
        $this->add_control('hideIncompleteRow', ['label' => __('Hide Incomplete Row', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['gallery_type' => 'diamond']]);
        $this->add_responsive_control('size_honeycombs', ['label' => __('Hexagon Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 250, 'tablet_default' => 150, 'mobile_default' => 100, 'min' => 20, 'max' => 800, 'step' => 1, 'frontend_available' => \true, 'condition' => ['gallery_type' => 'hexagon']]);
        $this->add_control('gap_honeycombs', ['label' => __('Hexagon Gap', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 10, 'min' => 0, 'max' => 100, 'step' => 1, 'frontend_available' => \true, 'condition' => ['gallery_type' => 'hexagon']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_gallery', ['label' => __('Gallery', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'prefix_class' => 'align-', 'selectors' => ['{{WRAPPER}} .dce-acf-gallery' => 'text-align: {{VALUE}};']]);
        $this->add_responsive_control('v_align', ['label' => __('Vertical Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['top' => ['title' => __('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], 'middle' => ['title' => __('Middle', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'down' => ['title' => __('Down', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom']], 'default' => 'top', 'selectors' => ['{{WRAPPER}} .dce-acf-gallery  .acfgallery-item' => 'vertical-align: {{VALUE}};'], 'condition' => ['gallery_type' => ['grid']]]);
        $this->add_responsive_control('items_padding', ['label' => __('Paddings Items', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .acfgallery-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['gallery_type!' => ['hexagon']]]);
        $this->add_control('image_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .wrap-item-acfgallery' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['gallery_type!' => ['hexagon']]]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'image_border', 'label' => __('Image Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .wrap-item-acfgallery', 'condition' => ['gallery_type!' => ['diamond', 'hexagon']]]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'image_box_shadow', 'selector' => '{{WRAPPER}} .dce-acf-gallery-masonry .wrap-item-acfgallery, {{WRAPPER}} .dce-acf-gallery-diamond .diamond-box', 'condition' => ['gallery_type!' => ['hexagon']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_settings', ['label' => __('Images', 'dynamic-content-for-elementor')]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'size', 'label' => __('Image Size', 'dynamic-content-for-elementor'), 'default' => 'large']);
        $this->add_control('use_desc', ['label' => __('Description', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'multiple' => \true, 'options' => ['title' => __('Title', 'dynamic-content-for-elementor'), 'caption' => __('Caption', 'dynamic-content-for-elementor'), 'description' => __('Description', 'dynamic-content-for-elementor')], 'default' => '', 'condition' => ['gallery_type!' => ['diamond', 'hexagon']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_images', ['label' => __('Images', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('force_width', ['label' => __('Force Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'prefix_class' => 'forcewidth-']);
        $this->add_responsive_control('size_img', ['label' => __('Size (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 100, 'unit' => '%'], 'size_units' => ['%'], 'range' => ['%' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .wrap-item-acfgallery' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['force_width' => 'yes']]);
        $this->add_control('popover-toggle', ['label' => __('Transform', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE, 'return_value' => 'yes']);
        $this->start_popover();
        $this->add_group_control(Group_Control_Transform_Element::get_type(), ['name' => 'transform_image', 'label' => __('Transform', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-acf-gallery', 'separator' => 'before']);
        $this->end_popover();
        $this->add_group_control(Group_Control_Filters_CSS::get_type(), ['name' => 'filters_image', 'label' => __('Filters', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .acfgallery-item img']);
        $this->add_responsive_control('desc_margin', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} figcaption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['use_desc!' => '']]);
        $this->add_control('figure_title_heading', ['label' => __('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['use_desc' => 'title']]);
        $this->add_control('acf_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} figcaption .title' => 'margin-bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['use_desc' => 'title']]);
        $this->add_control('desc_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} figcaption .title' => 'color: {{VALUE}};'], 'condition' => ['use_desc' => 'title']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'desc_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} figcaption .title', 'condition' => ['use_desc' => 'title']]);
        $this->add_control('figure_caption_heading', ['label' => __('Caption', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['use_desc' => 'caption']]);
        $this->add_control('space_caption', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} figcaption .caption' => 'margin-bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['use_desc' => 'caption']]);
        $this->add_control('caption_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} figcaption .caption' => 'color: {{VALUE}};'], 'condition' => ['use_desc' => 'caption']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'caption_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} figcaption .caption', 'condition' => ['use_desc' => 'caption']]);
        $this->add_control('figure_description_heading', ['label' => __('Description', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['use_desc' => 'description']]);
        $this->add_control('space_description', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} figcaption .description' => 'margin-bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['use_desc' => 'description']]);
        $this->add_control('description_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} figcaption .description' => 'color: {{VALUE}};'], 'condition' => ['use_desc' => 'description']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'description_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} figcaption .description', 'condition' => ['use_desc' => 'description']]);
        $this->end_controls_section();
        $this->start_controls_section('section_wow', ['label' => __('WOW Animation', 'dynamic-content-for-elementor'), 'condition' => ['enabled_wow' => 'yes']]);
        $this->add_control('wow_coef', ['label' => __('Delay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'min' => 0.05, 'max' => 1, 'step' => 0.05, 'condition' => ['enabled_wow' => 'yes']]);
        $this->add_control('wow_animations', ['label' => __('Wow Animation Effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['fadeIn' => 'Fade In', 'fadeInDown' => 'Fade In Down', 'fadeInLeft' => 'Fade In Left', 'fadeInRight' => 'Fade In Right', 'fadeInUp' => 'Fade In Up', 'zoomIn' => 'Zoom In', 'zoomInDown' => 'Zoom In Down', 'zoomInLeft' => 'Zoom In Left', 'zoomInRight' => 'Zoom In Right', 'zoomInUp' => 'Zoom In Up', 'bounceIn' => 'Bounce In', 'bounceInDown' => 'Bounce In Down', 'bounceInLeft' => 'Bounce In Left', 'bounceInRight' => 'Bounce In Right', 'bounceInUp' => 'Bounce In Up', 'slideInDown' => 'Slide In Down', 'slideInLeft' => 'Slide In Left', 'slideInRight' => 'Slide In Right', 'slideInUp' => 'Slide In Up', 'rotateIn' => 'Rotate In', 'rotateInDownLeft' => 'Rotate In Down Left', 'rotateInDownRight' => 'Rotate In Down Right', 'rotateInUpLeft' => 'Rotate In Up Left', 'rotateInUpRight' => 'Rotate In Up Right', 'bounce' => 'Bounce', 'flash' => 'Flash', 'pulse' => 'Pulse', 'rubberBand' => 'Rubber Band', 'shake' => 'Shake', 'headShake' => 'Head Shake', 'swing' => 'Swing', 'tada' => 'Tada', 'wobble' => 'Wobble', 'jello' => 'Jello', 'lightSpeedIn' => 'Light Speed In', 'rollIn' => 'Roll In'], 'default' => 'fadeInUp', 'condition' => ['enabled_wow' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_lightbox_effects', ['label' => __('Lightbox', 'dynamic-content-for-elementor')]);
        $this->add_control('enable_lightbox', ['label' => __('Lightbox', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('enable_lightbox_link', ['label' => __('Image link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['enable_lightbox' => '']]);
        $this->add_control('lightbox_type', ['label' => __('Lightbox Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor'), 'photoswipe' => 'Photoswipe'], 'default' => '', 'condition' => ['enable_lightbox!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_image_link', ['label' => __('Rollover', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'background', 'types' => ['classic', 'gradient'], 'label' => __('Overlay', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .acfgallery-overlay_hover, {{WRAPPER}} .inner_span', 'popover' => \true]);
        $this->add_control('hover_effects', ['label' => __('Hover Effects', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('None', 'dynamic-content-for-elementor'), 'zoom' => __('Zoom', 'dynamic-content-for-elementor')], 'default' => '', 'separator' => 'before', 'prefix_class' => 'hovereffect-', 'condition' => ['enable_lightbox!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'condition' => ['acf_gallery_from' => 'current_post']]);
        $this->add_control('data_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'description' => __('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => __('Same', 'dynamic-content-for-elementor'), 'label_off' => __('Other', 'dynamic-content-for-elementor')]);
        $this->add_control('other_post_source', ['label' => __('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        if ('masonry' === $settings['gallery_type'] && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            Helper::notice('', __('Masonry is not displayed correctly in the Elementor editor due to technical limitations but works correctly in the frontend.', 'dynamic-content-for-elementor'));
        }
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
        $elementor_lightbox = '';
        $counter = 0;
        $title = '';
        $image_size = $settings['size_size'];
        $enable_lightbox = '';
        $lightbox_type = '';
        $overlay_hover_class = 'is-overlay';
        if ($settings['enable_lightbox']) {
            $enable_lightbox = 'is-lightbox';
        }
        if ($settings['lightbox_type'] == 'photoswipe') {
            $lightbox_type = ' ' . $settings['lightbox_type'];
        } else {
            $lightbox_type = 'dce-gallery';
            $data_elementor_slideshow = ' data-elementor-lightbox-slideshow="' . $this->get_id() . '"';
            $elementor_lightbox = 'gallery-lightbox';
        }
        $idFields = $settings['acf_field_list'];
        $acf_gallery = Helper::get_acf_field_value($idFields, $id_page);
        if (empty($acf_gallery)) {
            return;
        }
        $first_image = \reset($acf_gallery);
        if (!\is_array($first_image)) {
            if (\filter_var(\reset($acf_gallery), \FILTER_VALIDATE_URL)) {
                // URL format
                $tmp = array();
                foreach ($acf_gallery as $image_url) {
                    $tmp[] = Helper::get_image_id($image_url);
                }
                $acf_gallery = $tmp;
            }
            if (\is_numeric(\reset($acf_gallery))) {
                // ID format
                $args = array('post_type' => 'attachment', 'post__in' => $acf_gallery, 'post_status' => 'inherit', 'post_mime_type' => 'image', 'numberposts' => 0, 'posts_per_page' => -1);
                $acf_gallery = get_posts($args);
            }
            if (\is_object(\reset($acf_gallery))) {
                // URL format
                $tmp = array();
                foreach ($acf_gallery as $image) {
                    $tmp[] = $this->get_attachment($image->ID);
                }
                $acf_gallery = $tmp;
            }
        }
        // Single Image Skin
        if ('single_image' === $settings['gallery_type']) {
            if ('first' === $settings['single_image_type']) {
                $acf_gallery = \array_slice($acf_gallery, 0, 1);
            } else {
                $random_key = \array_rand($acf_gallery, 1);
                $acf_gallery = \array_slice($acf_gallery, $random_key, 1);
            }
        }
        // Limit images to show
        if (!empty($settings['limit']) && 'yes' === $settings['limit'] && !empty($settings['limit_images'])) {
            $acf_gallery = \array_slice($acf_gallery, 0, $settings['limit_images']);
        }
        $this->add_render_attribute('container', ['class' => ['dce-acf-gallery', 'dce-acf-gallery-' . $settings['gallery_type'], $enable_lightbox, $elementor_lightbox, $lightbox_type, $overlay_hover_class, !empty($settings['columns_grid']) ? 'column-' . $settings['columns_grid'] : '']]);
        $this->add_render_attribute('container', 'itemtype', '');
        $this->add_render_attribute('container', 'itemscope', 'http://schema.org/ImageGallery');
        ?>

		<div <?php 
        echo $this->get_render_attribute_string('container');
        ?>>
			<?php 
        foreach ($acf_gallery as $image) {
            $this->show_image($image, $settings, $counter, $enable_lightbox);
            $counter++;
        }
        ?>
		</div>
		<?php 
    }
    /**
     * Show a single image from ACF Gallery
     *
     * @param [type] $image
     * @param [type] $settings
     * @param [type] $counter
     * @param [type] $enable_lightbox
     * @return void
     */
    protected function show_image($image, $settings, $counter, $enable_lightbox)
    {
        if (!isset($image['id'])) {
            $img_id = $image;
            $image = $this->get_attachment($img_id);
        }
        $image_url = Group_Control_Image_Size::get_attachment_image_src($image['id'], 'size', $settings);
        $elementor_lightbox = '';
        $overlay_hover_class = 'is-overlay ';
        // Overlay Hover
        $overlay_hover_block = '';
        $overlay_hover_class = '';
        if ($settings['gallery_type'] == 'hexagon') {
            $overlay_hover_block = '<span><span>';
        } else {
            $overlay_hover_block = '<span class="acfgallery-overlay_hover"></span>';
        }
        if ($settings['enable_lightbox']) {
            $enable_lightbox = ' is-lightbox';
        }
        if ('photoswipe' !== $settings['lightbox_type']) {
            $elementor_lightbox = 'gallery-lightbox';
        }
        $type_gallery_item_a = '';
        if ($settings['gallery_type'] == 'hexagon' && $enable_lightbox != '') {
            $type_gallery_item_a = 'comb';
        }
        if ($settings['gallery_type'] != 'hexagon') {
            if (0 === $counter) {
                // Attributes for figure
                $this->add_render_attribute('figure', 'itemprop', 'associatedMedia');
                $this->add_render_attribute('figure', 'itemscope', '');
                $this->add_render_attribute('figure', 'itemtype', 'http://schema.org/ImageObject');
                $this->add_render_attribute('figure', ['class' => ['acfgallery-item', 'grid-item']]);
                if ($settings['enabled_wow']) {
                    $wow_coeff = $settings['wow_coef'] ? $settings['wow_coef'] : 0;
                    $this->add_render_attribute('figure', 'data-wow-delay', $counter * $wow_coeff . 's');
                    $this->add_render_attribute('figure', ['class' => ['wow', $settings['wow_animations']]]);
                }
                // Attributes for wrap item
                $this->add_render_attribute('wrap-item', ['class' => ['wrap-item-acfgallery']]);
            }
            ?>
			<figure <?php 
            echo $this->get_render_attribute_string('figure');
            ?>>
				<div <?php 
            echo $this->get_render_attribute_string('wrap-item');
            ?>>
			<?php 
        }
        if ($enable_lightbox) {
            $this->set_render_attribute('a', ['class' => [$type_gallery_item_a, $enable_lightbox, $elementor_lightbox]]);
            $this->set_render_attribute('a', 'href', $image['url']);
            if (isset($image['width']) && isset($image['height'])) {
                $this->set_render_attribute('a', 'data-size', $image['width'] . 'x' . $image['height']);
            }
            if ('photoswipe' === $settings['lightbox_type']) {
                $this->set_render_attribute('a', 'data-elementor-open-lightbox', 'no');
            } else {
                $this->set_render_attribute('a', 'data-elementor-open-lightbox', 'yes');
                $this->set_render_attribute('a', 'data-elementor-lightbox-slideshow', $this->get_id());
            }
            ?>
			<a <?php 
            echo $this->get_render_attribute_string('a');
            ?>>
			<?php 
        } elseif ($settings['enable_lightbox_link']) {
            $this->add_render_attribute('a', ['class' => [$type_gallery_item_a]]);
            $this->add_render_attribute('a', 'href', $image['url']);
            $this->add_render_attribute('a', 'itemprop', 'contentUrl');
            ?>
			<a <?php 
            echo $this->get_render_attribute_string('a');
            ?>>
			<?php 
        }
        echo '<img src="' . $image_url . '" itemprop="thumbnail" alt="' . $image['alt'] . '" />';
        echo $overlay_hover_block;
        if ($enable_lightbox || $settings['enable_lightbox_link']) {
            echo '</a>';
        }
        if ($settings['gallery_type'] != 'hexagon') {
            echo '</div>';
            if ($settings['use_desc'] != '' && ($settings['gallery_type'] != 'diamond' && $settings['gallery_type'] != 'hexagon')) {
                echo '<figcaption itemprop="description caption">';
                foreach ($settings['use_desc'] as $value) {
                    if ($value == 'caption') {
                        echo '          <p class="' . $value . '" >' . $image[$value] . '</p>';
                    } elseif ($value == 'description') {
                        echo '           <p class="' . $value . '">' . $image[$value] . '</p>';
                    } elseif ($value == 'title') {
                        echo '            <h3 class="' . $value . '">' . $image[$value] . '</h3>';
                    }
                }
                echo '</figcaption>';
            }
            echo '</figure>';
        }
    }
    protected function get_attachment($attachment_id)
    {
        $attachment = get_post($attachment_id);
        $img_src = wp_get_attachment_image_src($attachment_id, 'full');
        return array('ID' => $attachment_id, 'id' => $attachment_id, 'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', \true), 'caption' => $attachment->post_excerpt, 'description' => $attachment->post_content, 'href' => get_permalink($attachment->ID), 'src' => $attachment->guid, 'title' => $attachment->post_title, 'url' => $img_src[0], 'width' => $img_width = $img_src[1], 'height' => $img_height = $img_src[2]);
    }
}
