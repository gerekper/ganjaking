<?php

namespace DynamicContentForElementor\Includes\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Skin_Gridtofullscreen3d extends \DynamicContentForElementor\Includes\Skins\Skin_Grid
{
    /**
     * Register Controls Actions
     *
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/dce-dynamicposts-v2/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/dce-dynamicposts-v2/section_dynamicposts/after_section_end', [$this, 'register_additional_gridtofullscreen3d_controls']);
        add_action('elementor/element/dce-dynamicposts-v2/section_dynamicposts/after_section_end', [$this, 'register_additional_grid_controls'], 20);
    }
    public $depended_scripts = ['dce-threejs-lib', 'dce-gsap-lib', 'dce-dynamicPosts-gridtofullscreen3d', 'dce-threejs-gridtofullscreeneffect', 'dce-ScrollToPlugin-lib'];
    public $depended_styles = ['dce-dynamicPosts-gridtofullscreen3d'];
    public function get_script_depends()
    {
        return \array_merge(['imagesloaded', 'dce-dynamicPosts-grid', 'jquery-masonry', 'dce-infinitescroll', 'isotope', 'dce-jquery-match-height'], $this->depended_scripts);
    }
    public function get_style_depends()
    {
        return \array_merge(['dce-dynamicPosts-grid'], $this->depended_styles);
    }
    public function get_id()
    {
        return 'gridtofullscreen3d';
    }
    public function get_title()
    {
        return __('Grid to Fullscreen 3D', 'dynamic-content-for-elementor');
    }
    public function register_additional_gridtofullscreen3d_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        $this->start_controls_section('section_gridtofullscreen3d', ['label' => __('Grid to Fullscreen 3D', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('gridtofullscreen3d_effects', ['label' => __('Effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'effect1', 'options' => ['effect1' => __('Effect 1', 'dynamic-content-for-elementor'), 'effect2' => __('Effect 2', 'dynamic-content-for-elementor'), 'effect3' => __('Effect 3', 'dynamic-content-for-elementor'), 'effect4' => __('Effect 4', 'dynamic-content-for-elementor'), 'effect5' => __('Effect 5', 'dynamic-content-for-elementor'), 'effect6' => __('Effect 6', 'dynamic-content-for-elementor'), 'custom_effect' => __('Custom effect', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'render_type' => 'template']);
        $this->add_responsive_control('gridtofullscreen3d_duration', ['label' => __('Duration (s)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1.8], 'range' => ['px' => ['max' => 5, 'min' => 0.3, 'step' => 0.1]], 'frontend_available' => \true]);
        $this->add_control('gridtofullscreen3d_activations', ['label' => __('Activations', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'corners', 'options' => ['corners' => __('Corners', 'dynamic-content-for-elementor'), 'topLeft' => __('Top Left', 'dynamic-content-for-elementor'), 'sides' => __('Sides', 'dynamic-content-for-elementor'), 'top' => __('Top', 'dynamic-content-for-elementor'), 'left' => __('Left', 'dynamic-content-for-elementor'), 'bottom' => __('Bottom', 'dynamic-content-for-elementor'), 'center' => __('Center', 'dynamic-content-for-elementor'), 'bottomStep' => __('Bottom Step', 'dynamic-content-for-elementor'), 'sinX' => __('Sine', 'dynamic-content-for-elementor'), 'mouse' => __('Mouse', 'dynamic-content-for-elementor'), 'closestCorner' => __('Closest Corner', 'dynamic-content-for-elementor'), 'closestSide' => __('Closest Side', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'render_type' => 'template', 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect']]);
        $this->add_control('gridtofullscreen3d_transformation', ['label' => __('Transformation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => __('None', 'dynamic-content-for-elementor'), 'flipX' => __('Flip', 'dynamic-content-for-elementor'), 'simplex' => 'Simplex', 'wavy' => 'Wavy', 'circle' => __('Circle', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'render_type' => 'template', 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect']]);
        $this->add_control('gridtofullscreen3d_easing_heading', ['label' => __('Timing equation Easing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect']]);
        $this->add_control('gridtofullscreen3d_easing_to_fullscreen_popover', ['label' => __('To Fullscreen', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::POPOVER_TOGGLE, 'label_off' => __('Default', 'dynamic-content-for-elementor'), 'label_on' => __('Custom', 'dynamic-content-for-elementor'), 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect']]);
        $this->get_parent()->start_popover();
        $this->add_control('gridtofullscreen3d_easing_morph_to_fullscreen', ['label' => __('To Fullscreen', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor')] + Helper::get_gsap_ease(), 'frontend_available' => \true, 'label_block' => \false, 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect', $this->get_control_id('gridtofullscreen3d_easing_to_fullscreen_popover') => 'yes']]);
        $this->add_control('gridtofullscreen3d_easing_morph_ease_to_fullscreen', ['label' => __('Equation to fullscreen', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor')] + Helper::get_gsap_timing_functions(), 'frontend_available' => \true, 'label_block' => \false, 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect', $this->get_control_id('gridtofullscreen3d_easing_to_fullscreen_popover') => 'yes']]);
        $this->get_parent()->end_popover();
        $this->add_control('gridtofullscreen3d_easing_to_grid_popover', ['label' => __('Timing function to Grid', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::POPOVER_TOGGLE, 'label_off' => __('Default', 'dynamic-content-for-elementor'), 'label_on' => __('Custom', 'dynamic-content-for-elementor'), 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect']]);
        $this->get_parent()->start_popover();
        $this->add_control('gridtofullscreen3d_easing_morph_to_grid', ['label' => __('Easing to fullscreen', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor')] + Helper::get_gsap_ease(), 'frontend_available' => \true, 'label_block' => \false, 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect', $this->get_control_id('gridtofullscreen3d_easing_to_grid_popover') => 'yes']]);
        $this->add_control('gridtofullscreen3d_easing_morph_ease_to_grid', ['label' => __('Equation to fullscreen', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor')] + Helper::get_gsap_timing_functions(), 'frontend_available' => \true, 'label_block' => \false, 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect', $this->get_control_id('gridtofullscreen3d_easing_to_grid_popover') => 'yes']]);
        $this->get_parent()->end_popover();
        $this->add_control('gridtofullscreen3d_panel_heading', ['label' => __('Panel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('gridtofullscreen3d_panel_position', ['label' => __('Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right'], 'top' => ['title' => __('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], 'bottom' => ['title' => __('Bottom', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom']], 'default' => is_rtl() ? 'left' : 'right', 'prefix_class' => 'dce-panel-position%s-', 'frontend_available' => \true, 'render_type' => 'template']);
        $this->add_responsive_control('gridtofullscreen3d_panel_width', ['label' => __('Width (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['%' => ['min' => 0, 'max' => 100]], 'devices' => Helper::get_active_devices_list(), 'desktop_default' => ['size' => 50, 'unit' => '%'], 'tablet_default' => ['size' => 50, 'unit' => '%'], 'mobile_default' => ['size' => 50, 'unit' => '%'], 'frontend_available' => \true, 'condition' => [$this->get_control_id('gridtofullscreen3d_panel_position') => ['left', 'right']]]);
        $this->add_responsive_control('gridtofullscreen3d_panel_height', ['label' => __('Height (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['%' => ['min' => 0, 'max' => 100]], 'devices' => Helper::get_active_devices_list(), 'desktop_default' => ['size' => 50, 'unit' => '%'], 'tablet_default' => ['size' => 50, 'unit' => '%'], 'mobile_default' => ['size' => 50, 'unit' => '%'], 'frontend_available' => \true, 'condition' => [$this->get_control_id('gridtofullscreen3d_panel_position') => ['top', 'bottom']]]);
        $this->add_control('gridtofullscreen3d_template', ['label' => __('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'frontend_available' => \true]);
        $this->add_control('gridtofullscreen3d_panel_background', ['label' => __('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-gridtofullscreen3d-container .fullview__item-box' => 'background-color: {{VALUE}};'], 'condition' => [$this->get_control_id('gridtofullscreen3d_template!') => '']]);
        $this->add_control('gridtofullscreen3d_panel_title_heading', ['label' => __('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('gridtofullscreen3d_panel_title_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-gridtofullscreen3d-container .fullview__item-title' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'gridtofullscreen3d_panel_title_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-gridtofullscreen3d-container .fullview__item-title']);
        $this->add_responsive_control('gridtofullscreen3d_panel_title_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-gridtofullscreen3d-container .fullview__item-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();
    }
    /**
     * Render Featured Image
     *
     * @param array<mixed> $settings
     * @return void
     */
    protected function render_featured_image(array $settings)
    {
        $use_bgimage = $settings['use_bgimage'];
        $use_overlay = $settings['use_overlay'];
        $use_overlay_hover = $this->get_parent()->get_settings('use_overlay_hover');
        $use_link = $settings['use_link'];
        $setting_key = $settings['thumbnail_size_size'];
        $image_attr = ['class' => $this->get_image_class()];
        $image_url = Group_Control_Image_Size::get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail_size', $settings);
        $thumbnail_html = wp_get_attachment_image(get_post_thumbnail_id(), $setting_key, \false, $image_attr);
        // Fallback
        if (empty($thumbnail_html) && !empty($settings['featured_image_fallback'])) {
            $thumbnail_html = wp_get_attachment_image($settings['featured_image_fallback']['id'], $setting_key, \false, $image_attr);
        }
        if (empty($thumbnail_html)) {
            return;
        }
        $bgimage = '';
        if ($use_bgimage) {
            $bgimage = ' dce-post-bgimage';
        }
        $overlayimage = '';
        if ($use_overlay) {
            $overlayimage = ' dce-post-overlayimage';
        }
        $overlayhover = '';
        if ($use_overlay_hover) {
            $overlayhover = ' dce-post-overlayhover';
        }
        $html_tag = 'div';
        $attribute_link = '';
        if ($use_link) {
            $html_tag = 'a';
            $attribute_link = ' href="' . $this->current_permalink . '"';
        }
        echo '<' . $html_tag . ' class="dce-post-image' . $bgimage . $overlayimage . $overlayhover . '"' . $attribute_link . '>';
        echo $thumbnail_html;
        $this->render_image_large($settings);
        echo '</' . $html_tag . '>';
    }
    protected function render_posts_before()
    {
        echo '<div id="app"></div>';
    }
    protected function render_posts_after()
    {
        $query = $this->get_parent()->get_query();
        if (!$query->found_posts) {
            return;
        }
        echo '<div class="fullview">';
        if ($query->in_the_loop) {
            $this->current_permalink = get_permalink();
            $this->current_id = get_the_ID();
            $this->render_fullview_item();
        } else {
            while ($query->have_posts()) {
                $query->the_post();
                $this->current_permalink = get_permalink();
                $this->current_id = get_the_ID();
                $this->render_fullview_item();
            }
        }
        wp_reset_postdata();
        echo '<button class="fullview__close" aria-label="' . __('Close preview', 'dynamic-content-for-elementor') . '"><svg aria-hidden="true" width="24" height="22px" viewBox="0 0 24 22"><path d="M11 9.586L20.192.393l1.415 1.415L12.414 11l9.193 9.192-1.415 1.415L11 12.414l-9.192 9.193-1.415-1.415L9.586 11 .393 1.808 1.808.393 11 9.586z" /></svg></button>';
        echo '</div>';
    }
    public function render_fullview_item()
    {
        $panel_template_id = $this->get_instance_value('gridtofullscreen3d_template');
        $title = get_the_title() ? wp_kses_post(get_the_title()) : the_ID();
        ?>
		<div class="fullview__item">
			<h2 class="fullview__item-title"><?php 
        echo $title;
        ?></h2>
			<?php 
        if ($panel_template_id) {
            ?>
				<div class="fullview__item-box">
					<?php 
            $this->render_template($panel_template_id);
            ?>
				</div>
			<?php 
        }
        ?>
		</div>
		<?php 
    }
    /**
     * Render Image Large
     *
     * @param array<mixed> $settings
     * @return void
     */
    public function render_image_large(array $settings)
    {
        $image_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
        // Fallback
        if (empty($image_url) && !empty($settings['featured_image_fallback'])) {
            $image_url = wp_get_attachment_image_src($settings['featured_image_fallback']['id'], 'full');
        }
        echo '<img class="grid__item-img grid__item-img--large" src="' . $image_url[0] . '" />';
    }
    public function get_container_class()
    {
        return 'dce-gridtofullscreen3d-container dce-skin-' . $this->get_id() . ' dce-skin-' . parent::get_id() . ' dce-skin-' . parent::get_id() . '-' . $this->get_instance_value('grid_type');
    }
    public function get_wrapper_class()
    {
        return 'dce-gridtofullscreen3d-wrapper dce-wrapper-' . $this->get_id() . ' dce-wrapper-' . parent::get_id();
    }
    public function get_item_class()
    {
        return 'dce-gridtofullscreen3d-item dce-item-' . $this->get_id() . ' dce-item-' . parent::get_id();
    }
    public function get_image_class()
    {
        return 'grid__item-img';
    }
}
