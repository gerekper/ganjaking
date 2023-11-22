<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Group_Control_Outline;
use DynamicContentForElementor\Controls\Group_Control_Filters_CSS;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class FeaturedImage extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_style_depends()
    {
        return ['dce-featuredImage'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $post_type_object = get_post_type_object(get_post_type());
        $this->start_controls_section('section_content', ['label' => __('Image settings', 'dynamic-content-for-elementor')]);
        $this->add_control('preview', ['type' => Controls_Manager::RAW_HTML, 'raw' => get_the_post_thumbnail(), 'separator' => 'none']);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'size', 'label' => __('Image Size', 'dynamic-content-for-elementor'), 'default' => 'large']);
        $this->add_responsive_control('align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};', '' => '']]);
        $this->add_control('link_to', ['label' => __('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => __('None', 'dynamic-content-for-elementor'), 'home' => __('Home URL', 'dynamic-content-for-elementor'), 'post' => 'Post URL', 'acf_url' => __('ACF URL', 'dynamic-content-for-elementor'), 'file' => __('Media File URL', 'dynamic-content-for-elementor'), 'custom' => __('Custom URL', 'dynamic-content-for-elementor')]]);
        $this->add_control('acf_field_url', ['label' => __('ACF Field Url', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => Helper::get_acf_field_urlfile(\true), 'default' => __('Select the field...', 'dynamic-content-for-elementor'), 'condition' => ['link_to' => 'acf_url']]);
        $this->add_control('acf_field_url_target', ['label' => __('Blank', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['link_to' => 'acf_url']]);
        $this->add_control('link', ['label' => __('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => __('https://your-link.com', 'dynamic-content-for-elementor'), 'condition' => ['link_to' => 'custom'], 'show_label' => \false]);
        $this->end_controls_section();
        /* -------------------- Background ------------------ */
        $post_type_object = get_post_type_object(get_post_type());
        $this->start_controls_section('section_backgroundimage', ['label' => __('Background', 'dynamic-content-for-elementor')]);
        $this->add_control('use_bg', ['label' => __('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['1' => ['title' => __('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => __('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '0']);
        $this->add_control('bg_position', ['label' => __('Background position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'top center', 'options' => ['' => __('Default', 'dynamic-content-for-elementor'), 'top left' => __('Top Left', 'dynamic-content-for-elementor'), 'top center' => __('Top Center', 'dynamic-content-for-elementor'), 'top right' => __('Top Right', 'dynamic-content-for-elementor'), 'center left' => __('Center Left', 'dynamic-content-for-elementor'), 'center center' => __('Center Center', 'dynamic-content-for-elementor'), 'center right' => __('Center Right', 'dynamic-content-for-elementor'), 'bottom left' => __('Bottom Left', 'dynamic-content-for-elementor'), 'bottom center' => __('Bottom Center', 'dynamic-content-for-elementor'), 'bottom right' => __('Bottom Right', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dynamic-content-featuredimage-bg' => 'background-position: {{VALUE}};'], 'condition' => ['use_bg' => '1']]);
        $this->add_control('bg_extend', ['label' => __('Extend Background', 'dynamic-content-for-elementor'), 'description' => __('Absolutely position the image by spreading it over the entire column. Warning: the height of the image depends on the elements contained in the column.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'condition' => ['use_bg' => '1'], 'prefix_class' => 'extendbg-']);
        $this->add_responsive_control('minimum_height', ['label' => __('Minimum Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'tablet_default' => ['size' => '', 'unit' => 'px'], 'mobile_default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', '%', 'vh'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vh' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dynamic-content-featuredimage-bg' => 'min-height: {{SIZE}}{{UNIT}};'], 'condition' => ['use_bg' => '1', 'bg_extend' => 'yes']]);
        $this->add_responsive_control('height', ['label' => __('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 200, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%', 'vh'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vh' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dynamic-content-featuredimage-bg' => 'height: {{SIZE}}{{UNIT}};'], 'condition' => ['use_bg' => '1', 'bg_extend' => '']]);
        $this->end_controls_section();
        // ------------------------------------------------------------- [ Overlay style ]
        $this->start_controls_section('section_overlay', ['label' => 'Overlay']);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'background_overlay', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-overlay']);
        $this->add_control('opacity_overlay', ['label' => __('Opacity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 1, 'min' => 0, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .dce-overlay' => 'opacity: {{SIZE}};'], 'condition' => ['background_overlay_background' => ['classic', 'gradient']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_hover_style', ['label' => 'Rollover', 'condition' => ['link_to!' => 'none']]);
        $this->add_control('bghover_heading', ['label' => __('Background color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'overlay_hover_color', 'label' => __('Background', 'dynamic-content-for-elementor'), 'description' => 'Background', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-overlay_hover', 'condition' => ['link_to!' => 'none']]);
        $this->add_control('bgoverlayhover_heading', ['label' => __('Change background color of overlay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['background_overlay_background' => ['classic', 'gradient']]]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'overlay_color_on_hover', 'label' => __('Background overlay', 'dynamic-content-for-elementor'), 'description' => 'Background color of overlay', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} a:hover .dce-overlay', 'condition' => ['background_overlay_background' => ['classic', 'gradient'], 'link_to!' => 'none']]);
        $this->add_control('opacity_overlay_on_hover', ['label' => __('Overlay Opacity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 1, 'min' => 0, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} a:hover .dce-overlay' => 'opacity: {{SIZE}};'], 'condition' => ['background_overlay_background' => ['classic', 'gradient'], 'link_to!' => 'none']]);
        $this->add_control('imageanimations_heading', ['label' => __('Rollover Animations', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('hover_animation', ['label' => __('Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION]);
        $this->add_control('imagefilters_heading', ['label' => __('Rollover Filters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Filters_CSS::get_type(), ['name' => 'filters_image_hover', 'label' => __('Filters', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} a:hover .wrap-filters']);
        $this->add_control('imageeffects_heading', ['label' => __('Rollover Effects', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('hover_effects', ['label' => __('Effects', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('None', 'dynamic-content-for-elementor'), 'zoom' => __('Zoom', 'dynamic-content-for-elementor')], 'default' => '', 'prefix_class' => 'hovereffect-', 'condition' => ['link_to!' => 'none']]);
        $this->end_controls_section();
        $this->start_controls_section('section_placeholder', ['label' => __('Placeholder', 'dynamic-content-for-elementor')]);
        $this->add_control('use_placeholter', ['label' => __('Use placeholder Image', 'dynamic-content-for-elementor'), 'description' => 'Use another image if the featured one does not exist.', 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['1' => ['title' => __('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => __('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '0']);
        $this->add_control('custom_placeholder_image', ['label' => __('Placeholder Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()], 'condition' => ['use_placeholter' => '1']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Image', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('space', ['label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => '%'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 500]], 'selectors' => ['{{WRAPPER}} .dce-featured-image' => 'width: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-featured-image.is-bg' => 'display: inline-block;'], 'condition' => ['bg_extend' => '']]);
        $this->add_responsive_control('maxwidth', ['label' => __('Max Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 500]], 'selectors' => ['{{WRAPPER}} .dce-featured-image' => 'max-width: {{SIZE}}{{UNIT}};'], 'condition' => ['use_bg' => '0', 'bg_extend' => '']]);
        $this->add_responsive_control('maxheight', ['label' => __('Max Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 500]], 'selectors' => ['{{WRAPPER}} .dce-featured-image' => 'max-height: {{SIZE}}{{UNIT}};'], 'condition' => ['use_bg' => '0', 'bg_extend' => '']]);
        $this->add_group_control(Group_Control_Filters_CSS::get_type(), ['name' => 'filters_image', 'label' => 'Filters image', 'selector' => '{{WRAPPER}} .wrap-filters']);
        $this->add_control('blend_mode', ['label' => __('Blend Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Normal', 'dynamic-content-for-elementor'), 'multiply' => __('Multiply', 'dynamic-content-for-elementor'), 'screen' => __('Screen', 'dynamic-content-for-elementor'), 'overlay' => __('Overlay', 'dynamic-content-for-elementor'), 'darken' => __('Darken', 'dynamic-content-for-elementor'), 'lighten' => __('Lighten', 'dynamic-content-for-elementor'), 'color-dodge' => __('Color Dodge', 'dynamic-content-for-elementor'), 'saturation' => __('Saturation', 'dynamic-content-for-elementor'), 'color' => __('Color', 'dynamic-content-for-elementor'), 'difference' => __('Difference', 'dynamic-content-for-elementor'), 'exclusion' => __('Exclusion', 'dynamic-content-for-elementor'), 'hue' => __('Hue', 'dynamic-content-for-elementor'), 'luminosity' => __('Luminosity', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dce-featured-image' => 'mix-blend-mode: {{VALUE}}'], 'separator' => 'none']);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'image_border', 'label' => __('Image Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-featured-image', 'condition' => ['use_bg' => '0', 'bg_extend' => '']]);
        $this->add_control('image_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-featured-image, {{WRAPPER}} .dce-featured-image .dce-overlay_hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['use_bg' => '0', 'bg_extend' => '']]);
        $this->add_control('image_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-featured-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['use_bg' => '0', 'bg_extend' => '']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'image_box_shadow', 'selector' => '{{WRAPPER}} .dce-featured-image', 'condition' => ['use_bg' => '0', 'bg_extend' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('data_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'description' => __('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => __('Same', 'dynamic-content-for-elementor'), 'label_off' => __('other', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_control('other_post_source', ['label' => __('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->add_control('other_post_parent', ['label' => __('From post parent', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id($settings['other_post_source'], $settings['other_post_parent']);
        $type_page = get_post_type($id_page);
        $overlay_hover_block = '';
        if ($settings['link_to'] != 'none') {
            $overlay_hover_block = '<div class="dce-overlay_hover"></div>';
        }
        $overlay_block = '<div class="dce-overlay"></div>';
        $wrap_effect_start = '<div class="mask"><div class="wrap-filters">';
        $wrap_effect_end = '</div></div>';
        $image_size = $settings['size_size'];
        $featuredImageID = get_post_thumbnail_id($id_page);
        // se il post parent non ha un'immagine, uso uso l'immagine dello stesso
        if (!$featuredImageID && $settings['other_post_parent'] == 'yes') {
            $parent_id = wp_get_post_parent_id($id_page);
            if ($parent_id) {
                $featuredImageID = get_post_thumbnail_id($parent_id);
            }
        }
        if ($type_page == 'attachment') {
            $featuredImageID = get_the_ID();
        }
        $featured_img_url = '';
        $image_alt = '';
        if ($featuredImageID) {
            $image_url = Group_Control_Image_Size::get_attachment_image_src($featuredImageID, 'size', $settings);
            $image_alt = get_post_meta($featuredImageID, '_wp_attachment_image_alt', \true);
            $featured_img_url = $image_url;
        }
        if (!$featuredImageID && $settings['other_post_parent'] != 'yes') {
            if ($settings['use_placeholter'] && $settings['custom_placeholder_image'] != '') {
                $featured_img_url = $settings['custom_placeholder_image']['url'];
            }
        }
        $get_featured_img = '';
        if ($featured_img_url != '') {
            if ($image_alt) {
                $image_alt = ' alt="' . $image_alt . '"';
            }
            $get_featured_img = '<img src="' . $featured_img_url . '"' . $image_alt . ' />';
        }
        $featured_image = '';
        if ($get_featured_img == '' && $settings['other_post_parent'] != 'yes') {
            $featured_image = $wrap_effect_start . '<img src="' . $featured_img_url . '" />' . $wrap_effect_end . $overlay_block . $overlay_hover_block;
        }
        if ($get_featured_img != '') {
            $featured_image = $wrap_effect_start . $get_featured_img . $wrap_effect_end . $overlay_block . $overlay_hover_block;
        }
        if (empty($featured_image)) {
            return;
        }
        $use_bg = $settings['use_bg'];
        $bg_class = '';
        if ($use_bg == '1') {
            $bg_class = 'is-bg ';
        }
        $target = !empty($settings['link']) && $settings['link']['is_external'] ? 'target="_blank"' : '';
        switch ($settings['link_to']) {
            case 'custom':
                if (!empty($settings['link']['url'])) {
                    $link = esc_url($settings['link']['url']);
                } else {
                    $link = \false;
                }
                break;
            case 'acf_url':
                if (!empty($settings['acf_field_url'])) {
                    $link = esc_url(\get_field($settings['acf_field_url'], $id_page));
                    $target = !empty($settings['acf_field_url_target']) ? 'target="_blank"' : '';
                } else {
                    $link = \false;
                }
                break;
            case 'file':
                $imageFull_url = wp_get_attachment_image_src($featuredImageID, 'full');
                $link = esc_url($imageFull_url[0]);
                break;
            case 'post':
                $link = esc_url(get_the_permalink($id_page));
                break;
            case 'home':
                $link = esc_url(get_home_url());
                break;
            case 'none':
            default:
                $link = \false;
                break;
        }
        if ($settings['hover_animation'] != '') {
            $animation_class = !empty($settings['hover_animation']) ? 'elementor-animation-' . $settings['hover_animation'] : '';
        } else {
            $animation_class = '';
        }
        $html = '<div class="dce-featured-image ' . $bg_class . $animation_class . '">';
        if ($use_bg == 0) {
            if ($link) {
                $html .= \sprintf('<a href="%1$s" %2$s>%3$s</a>', $link, $target, $featured_image);
            } else {
                $html .= $featured_image;
            }
        } else {
            $bg_featured_image = $wrap_effect_start . '<figure class="dynamic-content-featuredimage-bg ' . $animation_class . '" style="background-image: url(' . $featured_img_url . '); background-repeat: no-repeat; background-size: cover;">&nbsp;</figure>' . $wrap_effect_end . $overlay_block . $overlay_hover_block;
            if ($link) {
                $html .= \sprintf('<a href="%1$s" %2$s>%3$s</a>', $link, $target, $bg_featured_image);
            } else {
                $html .= $bg_featured_image;
            }
        }
        $html .= '</div>';
        echo $html;
    }
}
