<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class SvgBlob extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-gsap-lib', 'dce-svgblob'];
    }
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
        $this->start_controls_section('section_blob', ['label' => __('Blob', 'dynamic-content-for-elementor')]);
        $this->add_control('tensionPoints', ['label' => __('Curve Tension', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 2], 'label_block' => \true, 'range' => ['px' => ['min' => 0, 'max' => 10, 'step' => 0.1]], 'frontend_available' => \true]);
        $this->add_control('numPoints', ['label' => __('Number of points', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 5], 'label_block' => \true, 'range' => ['px' => ['min' => 3, 'max' => 100, 'step' => 1]], 'frontend_available' => \true]);
        $this->add_control('minmaxRadius', ['label' => __('Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['sizes' => ['start' => 100.0, 'end' => 200.0], 'unit' => 'px'], 'range' => ['px' => ['min' => 10.0, 'max' => 600.0, 'step' => 1.0]], 'labels' => [__('Min', 'dynamic-content-for-elementor'), __('Max', 'dynamic-content-for-elementor')], 'handles' => 'range', 'frontend_available' => \true]);
        $this->add_control('minmaxDuration', ['label' => __('Duration', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['sizes' => ['start' => 1, 'end' => 2], 'unit' => 's'], 'range' => ['s' => ['min' => 0.1, 'max' => 5, 'step' => 0.1]], 'labels' => [__('Min', 'dynamic-content-for-elementor'), __('Max', 'dynamic-content-for-elementor')], 'scales' => 0, 'handles' => 'range', 'frontend_available' => \true]);
        $this->add_control('show_points', ['label' => __('Show Points', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'render_type' => 'template']);
        $this->end_controls_section();
        $this->start_controls_section('section_viewbox', ['label' => __('Viewbox', 'dynamic-content-for-elementor')]);
        $this->add_responsive_control('svg_max_width', ['label' => __('Max-Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', '%', 'vw'], 'range' => ['px' => ['min' => 0, 'max' => 1000], '%' => ['min' => 0, 'max' => 100], 'vw' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} svg' => 'max-width: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_dots', ['label' => __('Dots', 'dynamic-content-for-elementor'), 'condition' => ['show_points!' => '']]);
        $this->add_control('dot_r', ['label' => __('Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 5], 'label_block' => \true, 'range' => ['px' => ['min' => 1, 'max' => 100, 'step' => 1]], 'frontend_available' => \true]);
        $this->add_control('dot_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_pattern', ['label' => __('Pattern Image', 'dynamic-content-for-elementor')]);
        $this->add_control('svg_image', ['label' => __('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => ''], 'show_label' => \false, 'dynamic' => ['active' => \true]]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'image', 'default' => 'thumbnail', 'condition' => ['svg_image[id]!' => '']]);
        $this->add_responsive_control('svg_size', ['label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '100', 'unit' => '%'], 'size_units' => ['%', 'px'], 'range' => ['%' => ['min' => 1, 'max' => 200], 'px' => ['min' => 1, 'max' => 2000]], 'condition' => ['svg_image[id]!' => '']]);
        $this->add_control('svgimage_x', ['label' => __('X', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0'], 'size_units' => ['%', 'px'], 'range' => ['%' => ['min' => -100, 'max' => 100], 'px' => ['min' => -500, 'max' => 500, 'step' => 1]], 'label_block' => \false, 'condition' => ['svg_image[id]!' => '']]);
        $this->add_control('svgimage_y', ['label' => __('Y', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0'], 'size_units' => ['%', 'px'], 'range' => ['%' => ['min' => -100, 'max' => 100], 'px' => ['min' => -500, 'max' => 500, 'step' => 1]], 'label_block' => \false, 'condition' => ['svg_image[id]!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Style', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('fill_color1', ['label' => __('Fill Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#FF0000', 'alpha' => \false, 'condition' => ['svg_image[id]' => '']]);
        $this->add_control('stroke_color1', ['label' => __('Stroke Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#0000FF', 'alpha' => \false]);
        $this->add_control('stroke_width1', ['label' => __('Stroke Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0, 'max' => 60, 'step' => 1]]]);
        $this->add_responsive_control('svg_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'prefix_class' => 'align-', 'separator' => 'before', 'default' => 'left', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id();
        $widgetId = $this->get_id();
        $fill_color1 = $settings['fill_color1'];
        $stroke_color1 = $settings['stroke_color1'];
        $stroke_width1 = $settings['stroke_width1']['size'];
        // Pattern Image
        $image_url = Group_Control_Image_Size::get_attachment_image_src($settings['svg_image']['id'], 'image', $settings);
        if (!empty($settings['svg_image']['id'])) {
            $imageData = wp_get_attachment_image_src($settings['svg_image']['id'], $settings['image_size']);
            $h = $imageData[2];
            $w = $imageData[1];
            $imageProportion = $h / $w;
            $realHeight = $settings['svg_size']['size'] * $imageProportion;
            $this->add_render_attribute('_wrapper', 'data-coeff', $realHeight);
        }
        if (!isset($settings['svg_image']) || empty($settings['svgimage_x']['size'])) {
            $posX = 0;
        } else {
            $posX = $settings['svgimage_x']['size'];
        }
        if (!isset($settings['svg_image']) || empty($settings['svgimage_y']['size'])) {
            $posY = 0;
        } else {
            $posY = $settings['svgimage_y']['size'];
        }
        // https://codepen.io/osublake/pen/vdzjyg
        echo '<div class="dce_svgblob-wrapper">';
        ?>
		<svg id="dce-svg-<?php 
        echo $widgetId;
        ?>" class="dce-svg-blob" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" preserveAspectRatio="xMidYMid meet" xml:space="preserve">

			<?php 
        if ($settings['svg_image']['id'] != '') {
            ?>
			<defs>
				<pattern id="pattern-<?php 
            echo $widgetId;
            ?>" patternUnits="userSpaceOnUse" patternContentUnits="userSpaceOnUse" width="<?php 
            echo $settings['svg_size']['size'] . $settings['svg_size']['unit'];
            ?>" height="<?php 
            echo $realHeight . $settings['svg_size']['unit'];
            ?>" x="<?php 
            echo $posX . $settings['svgimage_x']['unit'];
            ?>" y="<?php 
            echo $posY . $settings['svgimage_y']['unit'];
            ?>">
					<image id="img-pattern" xlink:href="<?php 
            echo $image_url;
            ?>" width="<?php 
            echo $settings['svg_size']['size'] . $settings['svg_size']['unit'];
            ?>" height="<?php 
            echo $realHeight . $settings['svg_size']['unit'];
            ?>"> </image>
				</pattern>
			</defs>
			<?php 
        }
        ?>

			<path id="path1-<?php 
        echo $widgetId;
        ?>" fill="<?php 
        echo $fill_color1;
        ?>" stroke-width="<?php 
        echo $stroke_width1;
        ?>" stroke="<?php 
        echo $stroke_color1;
        ?>" stroke-miterlimit="10"></path>

			<g id="dot-container"></g>

			<?php 
        if ($settings['svg_image']['id'] != '') {
            ?>
			<style>
				#path1-<?php 
            echo $widgetId;
            ?>{
					fill: url(#pattern-<?php 
            echo $this->get_id();
            ?>) !important;
				}
			</style>
			<?php 
        }
        ?>
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

		var fill_color1 = settings.fill_color1;
		var stroke_color1 = settings.stroke_color1;
		var stroke_width1 = settings.stroke_width1.size;

		var image = {
			id: settings.svg_image.id,
			url: settings.svg_image.url,
			size: settings.image_size,
			dimension: settings.image_custom_dimension,
			model: view.getEditModel()
		};
		var bgImage = elementor.imagesManager.getImageUrl( image );

		var sizeImage = settings.svg_size.size;
		var sizeUnitImage = settings.svg_size.unit;
		var enable_image  = settings.enable_image;

		var image_x = settings.svgimage_x.size;
		var image_y = settings.svgimage_y.size;
		if(image_x == '') image_x = '0';
		if(image_y == '') image_y = '0';

		var sizeUnitXImage = settings.svgimage_x.unit;
		var sizeUnitYImage = settings.svgimage_y.unit;

		var iFrameDOM = jQuery("iframe#elementor-preview-iframe").contents();

		dce_getimageSizes(bgImage, function (data) {

			if (jQuery("iframe#elementor-preview-iframe").length) {
				var pattern = iFrameDOM.find('pattern#pattern-'+idWidget);
				var patternImage = iFrameDOM.find('pattern#pattern-'+idWidget+' image');

				if(patternImage.length){
					var realHeight = data.coef * settings.svg_size.size;
					pattern.attr('height',realHeight+settings.svg_size.unit);
					patternImage.attr('height',realHeight+settings.svg_size.unit);
				}
			}

		});

		#>
		<div class="dce_svgblob-wrapper">

			<svg id="dce-svg-{{idWidget}}" class="dce-svg-blob" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" preserveAspectRatio="xMidYMid meet" xml:space="preserve">

			<# if(bgImage){ #>
				<defs>
					<pattern id="pattern-{{idWidget}}" patternUnits="userSpaceOnUse" patternContentUnits="userSpaceOnUse" width="{{sizeImage}}{{sizeUnitImage}}" height="{{sizeImage}}{{sizeUnitImage}}" x="{{image_x}}{{sizeUnitXImage}}" y="{{image_y}}{{sizeUnitYImage}}">

						<image id="img-pattern" xlink:href="{{bgImage}}" width="{{sizeImage}}{{sizeUnitImage}}" height="{{sizeImage}}{{sizeUnitImage}}"> </image>

					</pattern>
				</defs>
				<# } #>

			<path id="path1-{{idWidget}}" fill="{{fill_color1}}" stroke-width="{{stroke_width1}}" stroke="{{stroke_color1}}" stroke-miterlimit="10"></path>

			<g id="dot-container"></g>

			<# if ( bgImage != '' ) { #>
				<style>
					#path1-{{idWidget}}{
						fill: url(#pattern-{{idWidget}}) !important;
					}
				</style>
			<# } #>
			</svg>

		</div>
		<?php 
    }
}
