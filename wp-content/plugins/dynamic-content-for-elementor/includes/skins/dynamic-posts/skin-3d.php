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
class Skin_3D extends \DynamicContentForElementor\Includes\Skins\Skin_Base
{
    /**
     * Register Controls Actions
     *
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/dce-dynamicposts-v2/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/dce-dynamicposts-v2/section_dynamicposts/after_section_end', [$this, 'register_additional_3d_controls']);
    }
    public $depended_scripts = ['dce-threejs-lib', 'dce-gsap-lib', 'dce-threejs-OrbitControls', 'dce-threejs-CSS3DRenderer', 'dce-dynamicPosts-3d', 'dce-ScrollToPlugin-lib'];
    public $depended_styles = ['dce-dynamicPosts-3d'];
    public function get_id()
    {
        return '3d';
    }
    public function get_title()
    {
        return __('3D', 'dynamic-content-for-elementor');
    }
    public function register_additional_3d_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        $this->start_controls_section('section_3d', ['label' => __('3D', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('type_3d', ['label' => __('3D Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'circle', 'options' => ['circle' => __('Circle', 'dynamic-content-for-elementor'), 'fila' => __('Row', 'dynamic-content-for-elementor')], 'frontend_available' => \true]);
        $this->add_control('size_plane_3d', ['label' => __('Size plane', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 1200, 'min' => 0, 'step' => 1]], 'render_type' => 'template', 'frontend_available' => \true, 'selectors' => ['{{WRAPPER}} .dce-posts-container.dce-skin-3d .dce-3d-element' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('blur_depth_3d', ['label' => __('Depth blur', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true, 'condition' => [$this->get_control_id('type_3d') => 'circle']]);
        $this->add_control('mousewheel_3d', ['label' => __('Mouse wheel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('mousewheel_3d_stop_at_end', ['label' => __('Free mouse wheel at the end', 'dynamic-content-for-elementor'), 'description' => __('Free mouse wheel after last element is reached', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => [$this->get_control_id('mousewheel_3d') => 'yes']]);
        $this->add_control('3d_center_at_start', ['label' => __('Center the first item at the start', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->end_controls_section();
    }
    protected function render_posts_before()
    {
        $_skin = $this->get_parent()->get_settings('_skin');
        ?>

		<div id="dce-scene-3d-container" class="dce-posts-wrapper"></div>
		<div class="dce-3d-navigation">
			<div class="dce-3d-prev dce-3d-arrow"><i class="fas fa-arrow-left"></i></div>
			<div class="dce-3d-next dce-3d-arrow"><i class="fas fa-arrow-right"></i></div>
		</div>
		<div class="dce-3d-quit"><i class="fas fa-times"></i></div>
		<?php 
    }
    public function get_container_class()
    {
        return 'dce-3d-container dce-skin-' . $this->get_id();
    }
    public function get_wrapper_class()
    {
        return 'dce-grid-3d dce-3d-wrapper dce-3d-wrapper-hidden dce-wrapper-' . $this->get_id();
    }
    public function get_item_class()
    {
        return 'dce-item-' . $this->get_id();
    }
    public function get_image_class()
    {
        return '';
    }
}
