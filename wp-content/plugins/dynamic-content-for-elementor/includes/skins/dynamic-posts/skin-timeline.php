<?php

namespace DynamicContentForElementor\Includes\Skins;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Skin_Timeline extends \DynamicContentForElementor\Includes\Skins\Skin_Base
{
    /**
     * Register Controls Actions
     *
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/dce-dynamicposts-v2/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/dce-dynamicposts-v2/section_dynamicposts/after_section_end', [$this, 'register_additional_timeline_controls']);
    }
    public $depended_scripts = ['dce-dynamicPosts-timeline', 'dce-infinitescroll'];
    public $depended_styles = ['dce-dynamicPosts-timeline'];
    public function get_id()
    {
        return 'timeline';
    }
    public function get_title()
    {
        return __('Timeline', 'dynamic-content-for-elementor');
    }
    public function register_additional_timeline_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        $this->start_controls_section('section_timeline', ['label' => __('Timeline', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_responsive_control('timeline_imagesize', ['label' => __('Image Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'separator' => 'before', 'default' => ['size' => '64', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 400]], 'selectors' => ['{{WRAPPER}} .dce-timeline__img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('timeline_verticalposition', ['label' => __('Vertical Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', '%'], 'default' => ['size' => '32', 'unit' => 'px'], 'range' => ['px' => ['min' => 0, 'max' => 400], '%' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-timeline__block .dce-timeline__content::before, {{WRAPPER}} .dce-timeline__block .dce-timeline__img' => 'top: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('timeline_width', ['label' => __('Timeline Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'separator' => 'before', 'size_units' => ['px', '%', 'vw'], 'default' => ['size' => ''], 'range' => ['px' => ['max' => 1200, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-timeline-wrapper' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('timeline_space_content', ['label' => __('Content Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'separator' => 'before', 'size_units' => ['px', '%', 'vw'], 'default' => ['size' => ''], 'range' => ['px' => ['max' => 1200, 'min' => 0, 'step' => 1]], 'selectors' => ['body[data-elementor-device-mode=desktop] {{WRAPPER}} .dce-timeline__content' => 'width: calc((100% / 2) - ({{SIZE}}{{UNIT}} / 2))']]);
        $this->add_responsive_control('timeline_rowspace', ['label' => __('Row Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', 'em'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'render_type' => 'template', 'frontend_available' => \true, 'selectors' => ['{{WRAPPER}} .dce-timeline__block' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
    }
    protected function register_style_controls()
    {
        $this->start_controls_section('section_style_timeline', ['label' => __('Timeline', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        // ------------------- LINE - progress
        $this->add_control('timeline_heading_line', ['label' => __('Line', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->start_controls_tabs('timeline_styles');
        $this->start_controls_tab('timeline_style_normal', ['label' => __('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('timleline_line_color', ['label' => __('Line Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-timeline-wrapper::before' => 'background-color: {{VALUE}};', '{{WRAPPER}} .dce-timeline__block .dce-timeline__img' => 'border-color: {{VALUE}}']]);
        $this->add_control('timeline_line_size', ['label' => __('Line size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 30]], 'selectors' => ['{{WRAPPER}} .dce-timeline-wrapper::before' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('timeline_bg_color_content', ['label' => __('Panel Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-timeline__block .dce-timeline__content' => 'background-color: {{VALUE}};', '{{WRAPPER}} .dce-timeline__block:nth-child(odd) .dce-timeline__content::before' => 'border-left-color: {{VALUE}}; border-right-color: {{VALUE}};', '{{WRAPPER}} .dce-timeline__block:nth-child(even) .dce-timeline__content::before' => 'border-right-color: {{VALUE}};']]);
        $this->add_control('timeline_bg_color_image', ['label' => __('Image Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#fff', 'selectors' => ['{{WRAPPER}} .dce-timeline__block .dce-timeline__img' => 'background-color: {{VALUE}};']]);
        $this->add_responsive_control('timeline_borderimage_size', ['label' => __('Border image size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 30]], 'selectors' => ['{{WRAPPER}} .dce-timeline__block .dce-timeline__img' => 'border-width: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('timeline_style_active', ['label' => __('Active', 'dynamic-content-for-elementor')]);
        $this->add_control('timleline_activeline_color', ['label' => __('Active Line Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-timeline-wrapper::after' => 'background-color: {{VALUE}};', '{{WRAPPER}} .dce-timeline__block.dce-timeline__focus .dce-timeline__img' => 'border-color: {{VALUE}}']]);
        $this->add_control('timeline_activeline_size', ['label' => __('Active Line size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 30]], 'selectors' => ['{{WRAPPER}} .dce-timeline-wrapper::after' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('timeline_activebg_color_content', ['label' => __('Panel Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-timeline__block.dce-timeline__focus .dce-timeline__content' => 'background-color: {{VALUE}};', '{{WRAPPER}} .dce-timeline__block.dce-timeline__focus:nth-child(odd) .dce-timeline__content::before' => 'border-left-color: {{VALUE}}; border-right-color: {{VALUE}};', '{{WRAPPER}} .dce-timeline__block.dce-timeline__focus:nth-child(even) .dce-timeline__content::before' => 'border-right-color: {{VALUE}};']]);
        $this->add_control('timeline_activebg_color_image', ['label' => __('Image Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#fff', 'selectors' => ['{{WRAPPER}} .dce-timeline__block.dce-timeline__focus .dce-timeline__img' => 'background-color: {{VALUE}};']]);
        $this->add_control('timeline_activeborderimage_size', ['label' => __('Border image size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['min' => 0, 'max' => 30]], 'selectors' => ['{{WRAPPER}} .dce-timeline__block.dce-timeline__focus .dce-timeline__img' => 'border-width: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control('timeline_heading_panelcontent', ['label' => __('Panel Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('timeline_content_padding', ['label' => __('Content Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .dce-timeline__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('timeline_radius_content', ['label' => __('Content Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 0, 'max' => 50], 'px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-timeline__content' => 'border-radius: {{SIZE}}{{UNIT}};']]);
        $this->add_control('timeline_arrows_size', ['label' => __('Content arrows size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-timeline__content::before, {{WRAPPER}} .dce-timeline__content::before' => 'border-width: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'timeline_content_boxshadow', 'selector' => '{{WRAPPER}} .dce-post-item .dce-post-block']);
        $this->end_controls_section();
    }
    protected function render_post_start()
    {
        $thumbnail_id = get_post_thumbnail_id();
        // Featured image
        $featured_image = wp_get_attachment_image_src($thumbnail_id, 'thumbnail');
        if (\false === $thumbnail_id) {
            $featured_image_alt = '';
        } else {
            $featured_image_alt = Helper::get_attachment_alt($thumbnail_id);
        }
        ?>

		<div class="dce-timeline__block">
			<div class="dce-timeline__img dce-timeline__img--picture">
				<?php 
        if (!empty($featured_image[0])) {
            ?>
					<img src="<?php 
            echo $featured_image[0];
            ?>" alt="<?php 
            echo $featured_image_alt;
            ?>">
					<?php 
        }
        ?>
			</div>

			<div class="dce-timeline__content">
		<?php 
    }
    protected function render_post_end()
    {
        ?>
			</div>
		</div>
		<?php 
    }
    // Classes
    public function get_container_class()
    {
        return 'dce-timeline js-dce-timeline dce-timeline-container dce-skin-' . $this->get_id();
    }
    public function get_wrapper_class()
    {
        return 'dce-timeline-wrapper dce-wrapper-' . $this->get_id();
    }
    public function get_item_class()
    {
        return 'dce-timeline__block dce-timeline-item dce-item-' . $this->get_id();
    }
}
