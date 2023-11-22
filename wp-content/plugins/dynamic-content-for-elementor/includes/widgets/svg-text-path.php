<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class SvgTextPath extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_style_depends()
    {
        return ['dce-svg'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_pathText', ['label' => __('pathText', 'dynamic-content-for-elementor')]);
        $this->add_control('svgpathtext_text', ['label' => __('Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Abcdefg', 'dynamic-content-for-elementor'), 'dynamic' => ['active' => \true]]);
        $this->add_control('svgpathtext_path', ['label' => __('Path', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => [
            'wave' => __('Wave', 'dynamic-content-for-elementor'),
            //'curve' => __('Curve', 'dynamic-content-for-elementor'),
            'circle' => __('Circle', 'dynamic-content-for-elementor'),
            'custom' => __('Custom', 'dynamic-content-for-elementor'),
        ], 'frontend_available' => \true, 'default' => 'wave']);
        $this->add_control('curveTension', ['label' => __('Curve Tension', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 2], 'label_block' => \false, 'range' => ['px' => ['min' => 0, 'max' => 10, 'step' => 0.01]], 'condition' => ['svgpathtext_path' => 'curve']]);
        $this->add_control('startOffset', ['label' => __('Start Offset', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0, 'unit' => '%'], 'size_units' => ['px', '%'], 'label_block' => \true, 'range' => ['px' => ['min' => -500, 'max' => 500, 'step' => 1], '%' => ['min' => -100, 'max' => 100, 'step' => 1]]]);
        $this->add_control('circleRadius', ['label' => __('Circle Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 200], 'label_block' => \true, 'range' => ['px' => ['min' => 0, 'max' => 600, 'step' => 1]], 'condition' => ['svgpathtext_path' => 'circle']]);
        $this->add_control('pathtext_path_custom', ['label' => __('Custom path (d)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'default' => 'M 10,90 Q 100,15 200,70 Q 340,140 400,30', 'condition' => ['svgpathtext_path' => 'custom']]);
        $this->add_control('show_path', ['label' => __('Show path', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->end_controls_section();
        $this->start_controls_section('section_viewbox', ['label' => __('Viewbox', 'dynamic-content-for-elementor')]);
        $this->add_control('viewbox_width', ['label' => __('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'label_block' => \false, 'default' => 600, 'min' => 100, 'max' => 2000, 'step' => 1]);
        $this->add_control('viewbox_height', ['label' => __('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'label_block' => \false, 'default' => 600, 'min' => 100, 'max' => 2000, 'step' => 1]);
        $this->add_responsive_control('image_max_width', ['label' => __('Max-Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', '%', 'vw'], 'range' => ['px' => ['min' => 0, 'max' => 1000], '%' => ['min' => 0, 'max' => 100], 'vw' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} svg' => 'max-width: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Style', 'dynamic-content-for-elementor')]);
        // Typography
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'text_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} text#pathtext-text', 'exclude' => ['line_height', 'letter-spacing']]);
        $this->add_control('length_adjust_spacing', ['label' => __('Length Adjust Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->add_control('textLength', ['label' => __('Text Length (only Firefox)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'label_block' => \true, 'range' => ['px' => ['min' => 0, 'max' => 1000, 'step' => 1]], 'condition' => ['length_adjust_spacing' => 'yes']]);
        $this->add_control('textSpacing', ['label' => __('Text Spacing (Other browser)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'label_block' => \true, 'range' => ['px' => ['min' => 0, 'max' => 500, 'step' => 1]], 'selectors' => ['{{WRAPPER}} text#pathtext-text' => 'letter-spacing: {{SIZE}}{{UNIT}};'], 'condition' => ['length_adjust_spacing' => 'yes']]);
        // Color
        $this->add_control('text_color', ['label' => __('Text color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#000000']);
        // CURVE Path
        $this->add_control('path_color', ['label' => __('Path color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#e20613', 'selectors' => ['{{WRAPPER}} path#pathtext-path, ' => 'stroke: {{VALUE}};'], 'condition' => ['show_path' => 'yes']]);
        $this->add_responsive_control('svg_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'prefix_class' => 'align-', 'default' => 'left', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        // ------------------------------------------
        $id_page = Helper::get_the_id();
        // ------------------------------------------
        $widgetId = $this->get_id();
        $maxWidth = $settings['image_max_width']['size'];
        $viewBoxW = $settings['viewbox_width'];
        $viewBoxH = $settings['viewbox_height'];
        $pathtext_text = $settings['svgpathtext_text'];
        $pathtext_path = $settings['svgpathtext_path'];
        $circleRadius = $settings['circleRadius']['size'];
        $show_path = $settings['show_path'];
        //if
        $path = '';
        //wave
        if ($pathtext_path == 'curve') {
            $path = '';
        } elseif ($pathtext_path == 'wave') {
            $path = 'M 10,90 Q 100,15 200,70 Q 340,140 400,30';
        } elseif ($pathtext_path == 'circle') {
            $path = 'M0, ' . $circleRadius . 'a' . $circleRadius . ', ' . $circleRadius . ' 0 1, 0 ' . $circleRadius * 2 . ', 0a' . $circleRadius . ', ' . $circleRadius . ' 0 1, 0 -' . $circleRadius * 2 . ', 0';
        } elseif ($pathtext_path == 'custom') {
            $path = $settings['pathtext_path_custom'];
        }
        $text_color = $settings['text_color'];
        $path_color = $settings['path_color'];
        $startOffset = $settings['startOffset']['size'] . $settings['startOffset']['unit'];
        $length_adjust_spacing = '';
        $textLength = $settings['textLength']['size'];
        if ($settings['length_adjust_spacing']) {
            $length_adjust_spacing = ' textLength="' . $textLength . '" lengthAdjust="spacing"';
        }
        echo '<div class="dce_svgpathtext-wrapper">';
        ?>

	   <svg id="dce-svg-<?php 
        echo $widgetId;
        ?>" class="dce-svg-pathtext" version="1.1" xmlns="http://www.w3.org/2000/svg"  width="100%" height="100%"  viewBox="0 0 <?php 
        echo $viewBoxW;
        ?> <?php 
        echo $viewBoxH;
        ?>" preserveAspectRatio="xMidYMid meet" xml:space="preserve">

			<?php 
        if (!$show_path) {
            ?>
				<defs>
			<?php 
        }
        ?>
			<path id="pathtext-path-<?php 
        echo $widgetId;
        ?>" d="<?php 
        echo $path;
        ?>" fill="none" stroke="<?php 
        echo $path_color;
        ?>" />
			<?php 
        if (!$show_path) {
            ?>
				</defs>
			<?php 
        }
        ?>
			<!--  transform="translate(100 50)" stroke-dasharray="100%" stroke-dashoffset="100%" text-anchor="middle" stroke="#88ce02" stroke-width="0.75" stroke-miterlimit="1" stroke-linejoin="miter" -->

			<text id="pathtext-text" fill="<?php 
        echo $text_color;
        ?>"<?php 
        echo $length_adjust_spacing;
        ?>><textPath xlink:href="#pathtext-path-<?php 
        echo $widgetId;
        ?>" startOffset="<?php 
        echo $startOffset;
        ?>" method="stretch" spacing="auto"><tspan><?php 
        echo $pathtext_text;
        ?></tspan></textPath></text>
		</svg>
		<?php 
        echo '</div>';
    }
    protected function content_template()
    {
        ?>
		<#
		var idWidget = id;
		var iFrameDOM = jQuery("iframe#elementor-preview-iframe").contents();
		var scope = iFrameDOM.find('.elementor-element[data-id='+idWidget+']');

		var maxWidth = settings.image_max_width.size;

		var viewBoxW = settings.viewbox_width;
		var viewBoxH = settings.viewbox_height;

		var pathtext_text = settings.svgpathtext_text;
		var pathtext_path = settings.svgpathtext_path;

		var text_color = settings.text_color;
		var path_color = settings.path_color;

		var pathtext_path_custom = settings.pathtext_path_custom;

		var show_path = settings.show_path;
		var circleRadius = settings.circleRadius.size;

		var path = '';
		if( pathtext_path == 'curve' ){
			path = '';
		}else if( pathtext_path == 'wave' ){
			path = 'M 10,90 Q 100,15 200,70 Q 340,140 400,30';
		}else if( pathtext_path == 'circle' ){
			path = 'M0, '+circleRadius+'a'+circleRadius+', '+circleRadius+' 0 1, 0 '+(circleRadius*2)+', 0a'+circleRadius+', '+circleRadius+' 0 1, 0 -'+(circleRadius*2)+', 0';
		}else if( pathtext_path == 'custom' ){
			path = pathtext_path_custom;
		}

		var startOffset = settings.startOffset.size+settings.startOffset.unit;

		var length_adjust_spacing = '';
		var textLength = settings.textLength.size;
		if( settings.length_adjust_spacing ) length_adjust_spacing = ' textLength='+textLength+' lengthAdjust=spacing';
		#>

		<div class="dce_svgpathtext-wrapper">
			 <svg id="dce-svg-{{idWidget}}" class="dce-svg-pathtext" version="1.1" xmlns="http://www.w3.org/2000/svg"  width="100%" height="100%" viewBox="0 0 {{viewBoxW}} {{viewBoxH}}" preserveAspectRatio="xMidYMid meet" xml:space="preserve">
				<# if(show_path == ''){ #>
					<defs>
				<# } #>
				<path id="pathtext-path-{{idWidget}}" d="{{path}}" fill="none" stroke="{{path_color}}" />
				<# if(show_path == ''){ #>
					</defs>
				<# } #>
				<!--  transform="translate(100 50)" stroke-dasharray="100%" stroke-dashoffset="100%" text-anchor="middle" stroke="#88ce02" stroke-width="0.75" stroke-miterlimit="1" stroke-linejoin="miter" -->
				<text id="pathtext-text" fill="{{text_color}}"{{length_adjust_spacing}}><textPath xlink:href="#pathtext-path-{{idWidget}}" startOffset="{{startOffset}}"><tspan>{{pathtext_text}}</tspan></textPath></text>
			 </svg>

		</div>
		<?php 
    }
}
