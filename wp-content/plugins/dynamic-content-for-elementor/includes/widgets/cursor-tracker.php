<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Utils;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class CursorTracker extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-gsap-lib', 'dce-cursorTracker-js'];
    }
    public function get_style_depends()
    {
        return ['dce-cursorTracker'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_cursorTracker_settings', ['label' => __('Cursor', 'dynamic-content-for-elementor')]);
        $this->add_responsive_control('cursortracker_dimension', ['label' => __('Dimension', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'range' => ['px' => ['min' => 10, 'max' => 500, 'step' => 1]], 'selectors' => ['#cursors-{{ID}}' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};']]);
        $this->add_control('delay', ['label' => __('Delay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '1', 'unit' => 's'], 'range' => ['s' => ['min' => 0, 'max' => 3, 'step' => 0.1]], 'frontend_available' => 'true']);
        $this->start_controls_tabs('cursortracker_colors');
        $this->start_controls_tab('cursortracker_normal', ['label' => __('Normal', 'dynamic-content-for-elementor')]);
        $this->add_responsive_control('cursortracker_strokesize', ['label' => __('Stroke Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'range' => ['px' => ['min' => 1, 'max' => 40, 'step' => 1]], 'selectors' => ['#cursors-{{ID}} .progress-wrap svg.progress-circle path.dce-cursortrack-path1, #cursors-{{ID}} .progress-wrap svg.progress-circle path.dce-cursortrack-path2' => 'stroke-width: {{SIZE}};']]);
        $this->add_control('cursortracker_color_opacity', ['label' => __('Opacity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => '%'], 'range' => ['%' => ['min' => 0.1, 'max' => 1, 'step' => 0.1]], 'selectors' => ['#cursors-{{ID}} .progress-wrap svg.progress-circle path.dce-cursortrack-path2' => 'opacity: {{SIZE}};']]);
        $this->add_control('cursortracker_color_circle', ['label' => __('Stroke Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['#cursors-{{ID}} .progress-wrap svg.progress-circle path.dce-cursortrack-path2' => 'stroke: {{VALUE}};']]);
        $this->add_control('cursortracker_colorfill', ['label' => __('Fill Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['#cursors-{{ID}} .progress-wrap svg.progress-circle path.dce-cursortrack-path2' => 'fill: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('cursortracker_active', ['label' => __('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('cursortracker_note', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('To enable the mouse-over effect, assign the <strong>.cursor-target</strong> class to the desired element.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
        $this->add_responsive_control('cursortracker_strokesize_active', ['label' => __('Stroke Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'range' => ['px' => ['min' => 1, 'max' => 40, 'step' => 1]], 'selectors' => ['#cursors-{{ID}}.hover .progress-wrap svg.progress-circle path.dce-cursortrack-path1, #cursors-{{ID}}.hover .progress-wrap svg.progress-circle path.dce-cursortrack-path2' => 'stroke-width: {{SIZE}};']]);
        $this->add_control('cursortracker_color_opacity_active', ['label' => __('Opacity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => '%'], 'range' => ['%' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'selectors' => ['#cursors-{{ID}}.hover .cursor-wrap' => 'opacity: {{SIZE}};']]);
        $this->add_control('cursortracker_scale_active', ['label' => __('Scale', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => '%'], 'range' => ['%' => ['min' => 0.01, 'max' => 10, 'step' => 0.01]], 'selectors' => ['#cursors-{{ID}}.hover .cursor-wrap' => 'transform: scale({{SIZE}}) translate(-50%, -50%);']]);
        $this->add_control('cursortracker_colorstroke_active', ['label' => __('Stroke Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['#cursors-{{ID}}.hover .progress-wrap svg.progress-circle path.dce-cursortrack-path2' => 'stroke: {{VALUE}};']]);
        $this->add_control('cursortracker_colorfill_active', ['label' => __('Fill Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['#cursors-{{ID}}.hover .progress-wrap svg.progress-circle path.dce-cursortrack-path2' => 'fill: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        // Scroll Progress
        $this->add_control('title_scrollprogress', ['label' => __('Scroll Progress ', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('cursortracker_scroll', ['label' => __('Enable', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_responsive_control('cursortracker_strokesize_scrollprogress', ['label' => __('Stroke Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'range' => ['px' => ['min' => 1, 'max' => 40, 'step' => 1]], 'selectors' => ['#cursors-{{ID}} .progress-wrap svg.progress-circle path.dce-cursortrack-path1' => 'stroke-width: {{SIZE}};'], 'condition' => ['cursortracker_scroll!' => '']]);
        $this->add_control('cursortracker_color_scrollprogress', ['label' => __('Progress Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['#cursors-{{ID}} .progress-wrap svg.progress-circle path.dce-cursortrack-path1' => 'stroke: {{VALUE}};'], 'condition' => ['cursortracker_scroll!' => '']]);
        $this->add_control('responsive_cursorTracker', ['label' => __('Apply cursor on', 'dynamic-content-for-elementor'), 'description' => __('Responsive mode will take place on preview or live pages only, not while editing in Elementor.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'multiple' => \true, 'separator' => 'before', 'label_block' => \true, 'options' => \array_combine(Helper::get_active_devices_list(), Helper::get_active_devices_list()), 'default' => ['desktop', 'tablet', 'mobile'], 'frontend_available' => \true, 'render_type' => 'none']);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_widget = $this->get_id();
        $dimension_shape = $settings['cursortracker_dimension']['size'];
        ?>
		<div id="cursors-<?php 
        echo $id_widget;
        ?>" class="cursors" id="cursor">
			<div class="cursor-wrap">
				<div class="cursor1 cursor" id="cursor1"></div>
				<div class="cursor2 cursor" id="cursor2">
					<div class="progress-wrap">
						<svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-20 -20 140 140">
							<path class="dce-cursortrack-path2" d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/>
							<?php 
        if (!empty($settings['cursortracker_scroll'])) {
            ?><path class="dce-cursortrack-path1" d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/><?php 
        }
        ?>
						</svg>
					</div>
				</div>
				<div class="cursor3 cursor" id="cursor3"></div>
			</div>
		</div>
		<?php 
    }
}
