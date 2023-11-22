<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class SvgDistortion extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-gsap-lib', 'dce-svgdistortion'];
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
        $this->start_controls_section('section_distortion', ['label' => __('Distortion', 'dynamic-content-for-elementor')]);
        $this->add_control('svg_trigger', ['label' => __('Trigger', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['static' => __('Static', 'dynamic-content-for-elementor'), 'animation' => __('Animation', 'dynamic-content-for-elementor'), 'rollover' => __('Rollover', 'dynamic-content-for-elementor'), 'scroll' => __('Scroll', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'default' => 'static', 'render_type' => 'template', 'prefix_class' => 'svg-trigger-']);
        $this->add_control('link_to', ['label' => __('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => __('None', 'dynamic-content-for-elementor'), 'home' => __('Home URL', 'dynamic-content-for-elementor'), 'custom' => __('Custom URL', 'dynamic-content-for-elementor')], 'condition' => ['svg_trigger' => 'rollover']]);
        $this->add_control('link', ['label' => __('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => __('https://your-link.com', 'dynamic-content-for-elementor'), 'dynamic' => ['active' => \true], 'condition' => ['link_to' => 'custom', 'svg_trigger' => 'rollover'], 'default' => ['url' => ''], 'show_label' => \false]);
        $this->add_control('playpause_control', ['label' => __('Animation Controls', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'default' => 'running', 'toggle' => \false, 'options' => ['running' => ['title' => __('Play', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-play'], 'paused' => ['title' => __('Pause', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-pause']], 'frontend_available' => \true, 'separator' => 'before', 'render_type' => 'ui', 'condition' => ['svg_trigger' => ['animation']]]);
        $this->add_control('animation_heading', ['label' => __('Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['svg_trigger' => ['animation', 'rollover', 'scroll']]]);
        $this->add_control('speed_animation', ['label' => __('Speed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'label_block' => \false, 'default' => ['size' => 3], 'range' => ['px' => ['min' => 0.1, 'max' => 10, 'step' => 0.1]], 'frontend_available' => \true, 'condition' => ['svg_trigger' => ['animation', 'rollover', 'scroll']]]);
        $this->add_control('delay_animation', ['label' => __('Delay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'label_block' => \false, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0, 'max' => 10, 'step' => 0.1]], 'frontend_available' => \true, 'condition' => ['svg_trigger' => ['animation', 'rollover', 'scroll']]]);
        $this->add_control('easing_animation', ['label' => __('Easing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor')] + Helper::get_gsap_ease(), 'default' => 'easeInOut', 'frontend_available' => \true, 'label_block' => \false, 'condition' => ['svg_trigger' => ['animation', 'rollover', 'scroll']]]);
        $this->add_control('easing_animation_ease', ['label' => __('Equation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor')] + Helper::get_gsap_timing_functions(), 'default' => 'Power3', 'frontend_available' => \true, 'label_block' => \false, 'condition' => ['svg_trigger' => ['animation', 'rollover', 'scroll']]]);
        // ----------------------------------------
        $this->add_control('base_image', ['label' => __('The base Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'separator' => 'before', 'dynamic' => ['active' => \true], 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]]);
        $this->add_control('displacement_image', ['label' => __('Displacement Map Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'dynamic' => ['active' => \true], 'default' => ['url' => ''], 'frontend_available' => \true]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), [
            'name' => 'image',
            // Actually its `image_size`
            'default' => 'thumbnail',
            'condition' => [
                //'displacement_image[id]!' => '',
                'base_image[id]!' => '',
            ],
        ]);
        $this->add_control('displacementmap_heading', ['label' => __('DisplacementMap', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('disp_factor', ['label' => __('Depth Factor', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '100', 'unit' => 'px'], 'size_units' => ['px'], 'frontend_available' => \true, 'range' => ['px' => ['min' => -300, 'max' => 300]]]);
        $this->add_control('disp_scale', ['label' => __('Scale (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'size_units' => ['px'], 'default' => ['size' => '100'], 'range' => ['px' => ['min' => 0, 'max' => 300]]]);
        $this->add_control('disp_factor_to', ['label' => __('Depth Factor: TO', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0', 'unit' => 'px'], 'frontend_available' => \true, 'condition' => ['svg_trigger' => ['rollover', 'scroll']], 'range' => ['px' => ['min' => 0, 'max' => 300]]]);
        $this->add_control('disp_scale_to', ['label' => __('Scale TO (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '100'], 'size_units' => ['px'], 'frontend_available' => \true, 'condition' => ['svg_trigger' => ['rollover', 'scroll']], 'range' => ['px' => ['min' => 0, 'max' => 300]]]);
        /*$this->add_control(
        			'displacementmap_transform_heading',
        			[
        				'label' => __( 'Transform DisplacementMap', 'dynamic-content-for-elementor' ),
        				'type' => Controls_Manager::HEADING,
        				'separator' => 'before',
        			]
        		);
        
        		$this->add_control(
        			'disp_x', [
        				'label' => __('X', 'dynamic-content-for-elementor'),
        				'type' => Controls_Manager::SLIDER,
        				'default' => [
        					'size' => '0',
        					'unit' => 'px',
        				],
        				'size_units' => ['px'],
        				'range' => [
        
        					'px' => [
        						'min' => -200,
        						'max' => 200,
        					]
        				],
        
        			]
        		);
        		$this->add_control(
        			'disp_y', [
        				'label' => __('Y', 'dynamic-content-for-elementor'),
        				'type' => Controls_Manager::SLIDER,
        				'default' => [
        					'size' => '0',
        					'unit' => 'px',
        				],
        				'size_units' => ['px'],
        				'range' => [
        
        					'px' => [
        						'min' => -200,
        						'max' => 200,
        					]
        				],
        
        			]
        		);*/
        $this->add_control('random_animation', ['label' => __('Random animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'frontend_available' => \true, 'condition' => ['svg_trigger' => 'animation']]);
        $this->add_control('random_animation_range', ['label' => __('Range of value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => '100', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 200]], 'condition' => ['svg_trigger' => 'animation', 'random_animation!' => '']]);
        $this->add_control('preserveAR', ['label' => __('Aspect ratio', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'separator' => 'before']);
        // no = none
        // mantieni = keep = meet
        // ritaglia = crop = slice
        $this->add_control('preserveMode', ['label' => __('Ratio mode ', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['slice' => __('Crop', 'dynamic-content-for-elementor'), 'meet' => __('Keep', 'dynamic-content-for-elementor')], 'default' => 'slice', 'condition' => ['preserveAR' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_viewbox', ['label' => __('Viewbox', 'dynamic-content-for-elementor')]);
        $this->add_control('viewbox_width', ['label' => __('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'label_block' => \false, 'default' => 600, 'min' => 100, 'max' => 2000, 'step' => 1]);
        $this->add_control('viewbox_height', ['label' => __('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'label_block' => \false, 'default' => 600, 'min' => 100, 'max' => 2000, 'step' => 1]);
        $this->add_responsive_control('image_max_width', ['label' => __('Max-Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', '%', 'vw'], 'range' => ['px' => ['min' => 0, 'max' => 1000], '%' => ['min' => 0, 'max' => 100], 'vw' => ['min' => 0, 'max' => 100]]]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Style', 'dynamic-content-for-elementor')]);
        $this->add_responsive_control('svg_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'prefix_class' => 'align-', 'default' => 'left', 'selectors' => ['{{WRAPPER}} .dce_distortion-wrapper' => 'text-align: {{VALUE}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'condition' => ['svg_trigger' => 'static']]);
        $this->add_control('distortion_output', ['label' => __('Output', 'dynamic-content-for-elementor'), 'description' => __('Use distortion only for appling to other page elements. With this Option activated, the svg element will not be displayed.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->add_control('id_svg_class', ['label' => __('CSS Class', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '', 'frontend_available' => \true, 'condition' => ['distortion_output' => 'yes']]);
        $this->add_control('note_idclass', ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'raw' => __('Here you can write the class of the element to trasform with the SVG distortion. Remember to write the class name on your element in advanced tab.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'separator' => 'after', 'condition' => ['distortion_output' => 'yes']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $widgetId = $this->get_id();
        $id_svg_class = $settings['id_svg_class'];
        $filterId = '';
        if ($settings['displacement_image']['url']) {
            $filterId = 'filter="url(#distortion-filter-' . $widgetId . ')"';
        }
        $image_size = $settings['image_size'];
        $image_url = Group_Control_Image_Size::get_attachment_image_src($settings['base_image']['id'], 'image', $settings);
        $dispimage_url = Group_Control_Image_Size::get_attachment_image_src($settings['displacement_image']['id'], 'image', $settings);
        $viewBoxW = $settings['viewbox_width'];
        $viewBoxH = $settings['viewbox_height'];
        $svg_trigger = $settings['svg_trigger'];
        $disp_factor = $settings['disp_factor']['size'];
        $scale_factor = $settings['disp_scale']['size'];
        $pos_factor = (100 - \intval($settings['disp_scale']['size'])) / 2;
        $data_run = 'paused';
        if ($svg_trigger == 'animation') {
            $data_run = $settings['playpause_control'];
        }
        $this->add_render_attribute('svgdistortion', ['class' => 'dce_distortion', 'data-coef' => 0.5, 'data-dispimage' => $dispimage_url]);
        $preserveAR = 'xMidYMid ' . $settings['preserveMode'];
        if (!$settings['preserveAR']) {
            $preserveAR = 'none';
        }
        ?>
		<div class="dce_distortion-wrapper">
		  <div <?php 
        echo $this->get_render_attribute_string('svgdistortion');
        ?>>
			<svg  id="dce-svg-<?php 
        echo $widgetId;
        ?>" data-run="<?php 
        echo $data_run;
        ?>" class="dce-svg-distortion" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 <?php 
        echo $viewBoxW;
        ?> <?php 
        echo $viewBoxH;
        ?>">
			  <defs>
				<filter id="distortion-filter-<?php 
        echo $widgetId;
        ?>" x="0" y="0" width="100%" height="100%" filterUnits="objectBoundingBox" primitiveUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
				  <feImage xlink:href="<?php 
        echo $dispimage_url;
        ?>" id="displacement-image" x="<?php 
        echo $pos_factor;
        ?>%" y="<?php 
        echo $pos_factor;
        ?>%" width="<?php 
        echo $scale_factor;
        ?>%" height="<?php 
        echo $scale_factor;
        ?>%" preserveAspectRatio="<?php 
        echo $preserveAR;
        ?>" result="distortionImage" />

				  <feDisplacementMap id="displacement-map"
							   xChannelSelector="R"
							   yChannelSelector="G"
							   in="SourceGraphic"
							   in2="distortionImage"
							   result="displacementMap"
							   color-interpolation-filters="sRGB"
							   scale="<?php 
        echo $disp_factor;
        ?>" />

				  <feComposite operator="in" in2="distortionImage"></feComposite>
				</filter>
			  </defs>

			<g id="item-distortion">
			  <image id="img-distorted"
				   preserveAspectRatio="<?php 
        echo $preserveAR;
        ?>"
				   width="100%"
				   height="100%"
				   xlink:href="<?php 
        echo $image_url;
        ?>" />

			  <image id="img-distorted"
				   preserveAspectRatio="<?php 
        echo $preserveAR;
        ?>"
				   width="100%"
				   height="100%"
				   xlink:href="<?php 
        echo $image_url;
        ?>"
				   <?php 
        echo $filterId;
        ?> />
			</g>

			<style>
			<?php 
        if ($settings['image_max_width']['size'] && $settings['image_max_width']['size'] > 0) {
            ?>
				#dce-svg-<?php 
            echo $widgetId;
            ?>{
					max-width: <?php 
            echo $settings['image_max_width']['size'];
            ?>px;
				}
			<?php 
        }
        ?>

			<?php 
        if ($settings['distortion_output'] && $settings['id_svg_class'] != '') {
            ?>
				.<?php 
            echo $id_svg_class;
            ?> svg > image,
				.<?php 
            echo $id_svg_class;
            ?> svg > path,
				.<?php 
            echo $id_svg_class;
            ?> svg > polyline,
				.<?php 
            echo $id_svg_class;
            ?> img,
				.<?php 
            echo $id_svg_class;
            ?> p,
				.<?php 
            echo $id_svg_class;
            ?> .elementor-heading-title,
				.<?php 
            echo $id_svg_class;
            ?> .elementor-icon i:before,
				.<?php 
            echo $id_svg_class;
            ?> .elementor-button{
					-webkit-filter: url(#distortion-filter-<?php 
            echo $widgetId;
            ?>);
					filter: url(#distortion-filter-<?php 
            echo $widgetId;
            ?>);

				}
				#dce-svg-<?php 
            echo $widgetId;
            ?>{
					position: absolute;
					width: 0;
					height: 0;
				}
			 <?php 
        }
        ?>
			</style>


		  </svg>
		</div>
	  </div>
		<?php 
    }
    protected function content_template()
    {
        ?>
	  <#
	  var idWidget = id;
	  var iFrameDOM = jQuery("iframe#elementor-preview-iframe").contents();
	  var scope = iFrameDOM.find('.elementor-element[data-id='+idWidget+']');
	  var id_svg_class = settings.id_svg_class;

	  var viewBoxW = settings.viewbox_width;
	  var viewBoxH = settings.viewbox_height;
	  var maxWidth = settings.image_max_width.size;
	  var dist_output = settings.distortion_output;

	  var dispFactor = settings.disp_factor.size;
	  var dispScale = settings.disp_scale.size;
	  var pos_factor = ((100-(Number(settings.disp_scale.size)))/2);



	  var baseImage = settings.base_image.url;


	  var image = {
		id: settings.base_image.id,
		url: settings.base_image.url,
		size: settings.image_size,
		dimension: settings.image_custom_dimension,
		model: view.getEditModel()
	  };
	  var dispimage = {
		id: settings.displacement_image.id,
		url: settings.displacement_image.url,
		size: settings.image_size,
		dimension: settings.image_custom_dimension,
		model: view.getEditModel()
	  };

	  var image_url = elementor.imagesManager.getImageUrl( image );
	  var dispimage_url = elementor.imagesManager.getImageUrl( dispimage );

	  if ( ! image_url ) {
		return;
	  }
	  var svg_trigger = settings.svg_trigger;

	  var data_run = 'paused';
	  if(svg_trigger == 'animation'){
		data_run = settings.playpause_control;
	  }

	  view.addRenderAttribute( {
		'svgdistortion' : {
		  'class' : [
			'dce_distortion',
		  ],
		  'data-coef' : [
			0.5,
		  ],
		  'data-dispimage' : [
			dispimage_url,
		  ],
		},
	  });

	  dce_getimageSizes(image_url, function (data) {
		// to do
	  });

	  var preserveAR = 'xMidYMid '+settings.preserveMode;
	  if(!settings.preserveAR) preserveAR = 'none';


	  #>
	  <div class="dce_distortion-wrapper">

		<div {{{ view.getRenderAttributeString( 'svgdistortion') }}}>

		  <svg id="dce-svg-{{idWidget}}" data-run="{{data_run}}" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 {{viewBoxW}} {{viewBoxH}}">
			  <defs>
				<filter id="distortion-filter-{{idWidget}}" x="0" y="0" width="100%" height="100%" filterUnits="objectBoundingBox" primitiveUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
				  <feImage xlink:href="{{dispimage_url}}" id="displacement-image" x="{{pos_factor}}%" y="{{pos_factor}}%" width="{{dispScale}}%" height="{{dispScale}}%" preserveAspectRatio="{{preserveAR}}" result="distortionImage" />
				  <feDisplacementMap id="displacement-map"
									 xChannelSelector="R"
									 yChannelSelector="G"
									 in="SourceGraphic"
									 in2="distortionImage"
									 result="displacementMap"
									 color-interpolation-filters="sRGB"
									 scale="{{dispFactor}}" />

				  <feComposite operator="in" in2="distortionImage"></feComposite>
				</filter>
			  </defs>
			  <g id="item-distortion">
				<image id="img-base"
					   preserveAspectRatio="{{preserveAR}}"
					   width="100%"
					   height="100%"
					   xlink:href="{{image_url}}" />
				<# if(settings.displacement_image.url){ #>
				<image id="img-distorted"
					   preserveAspectRatio="{{preserveAR}}"
					   width="100%"
					   height="100%"
					   xlink:href="{{image_url}}"
					   filter="url(#distortion-filter-{{idWidget}})" />
				<# } #>
			 </g>


			<style>
			<# if( maxWidth && maxWidth > 0 ){ #>
				#dce-svg-{{idWidget}}{
					max-width: {{maxWidth}}px;
				}
				<# } #>

			<# if( dist_output && id_svg_class != '' ){ #>
				.{{id_svg_class}} svg > image,
				.{{id_svg_class}} svg  path,
				.{{id_svg_class}} svg polyline,
				.{{id_svg_class}} img,
				.{{id_svg_class}} p,
				.{{id_svg_class}} .elementor-heading-title,
				.{{id_svg_class}} .elementor-icon i:before,
				.{{id_svg_class}} .elementor-button
				{
					-webkit-filter: url(#distortion-filter-{{idWidget}});
					filter: url(#distortion-filter-{{idWidget}});

				}
				#dce-svg-{{idWidget}}{
					position: absolute;
					width: 0;
					height: 0;
				}
			 <# } #>
			</style>


		  </svg>
		 </div>
	   </div>
		<?php 
    }
}
