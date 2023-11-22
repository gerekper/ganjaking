<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class SvgImageMask extends \DynamicContentForElementor\Widgets\WidgetPrototype
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
        $this->start_controls_section('section_imagemask', ['label' => __('Imagemask', 'dynamic-content-for-elementor')]);
        $this->add_control('mask_image', ['label' => __('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'dynamic' => ['active' => \true], 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), [
            'name' => 'image',
            // Actually its `image_size`
            'default' => 'thumbnail',
            'condition' => ['mask_image[id]!' => ''],
        ]);
        $this->add_control('mask_heading', ['label' => __('Mask', 'dynamic-content-for-elementor'), 'description' => __('Shape parameters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('mask_shape_type', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['image' => __('Image', 'dynamic-content-for-elementor'), 'ring' => __('Ring', 'dynamic-content-for-elementor'), 'triangle' => __('Triangle', 'dynamic-content-for-elementor'), 'circle' => __('Circle', 'dynamic-content-for-elementor'), 'hexagon' => __('Hexagon', 'dynamic-content-for-elementor'), 'pentagonal' => __('pentagon', 'dynamic-content-for-elementor'), 'rhombus' => __('Rhombus', 'dynamic-content-for-elementor'), 'star' => __('Star', 'dynamic-content-for-elementor'), 'heart' => __('Heart', 'dynamic-content-for-elementor'), 'custom_path' => __('Custom path', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'default' => 'ring', 'render_type' => 'template']);
        $this->add_control('options_heading', ['label' => __('Options', 'dynamic-content-for-elementor'), 'description' => __('Shape parameters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'condition' => ['mask_shape_type' => ['ring', 'custom_path']]]);
        $this->add_control('shape_numbers', ['label' => __('Custom Path (Numbers)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'default' => 'M568,568H0l172.5-284L0,0h568L395.5,287L568,568z', 'condition' => ['mask_shape_type' => 'custom_path']]);
        $this->add_control('radius_inner_circle', ['label' => __('Radius of the inner circle', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 25], 'range' => ['px' => ['min' => 1, 'max' => 50, 'step' => 1]], 'condition' => ['mask_shape_type' => 'ring']]);
        $this->add_control('image_masking', ['label' => __('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'dynamic' => ['active' => \true], 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()], 'condition' => ['mask_shape_type' => 'image']]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), [
            'name' => 'size_masking',
            // Actually its `image_size`
            'default' => 'thumbnail',
            'condition' => ['image_masking[id]!' => '', 'mask_shape_type' => 'image'],
        ]);
        $this->end_controls_section();
        $this->start_controls_section('section_viewbox', ['label' => __('Viewbox', 'dynamic-content-for-elementor')]);
        $this->add_control('viewbox_width', ['label' => __('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'label_block' => \false, 'default' => 600, 'min' => 100, 'max' => 2000, 'step' => 1]);
        $this->add_control('viewbox_height', ['label' => __('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'label_block' => \false, 'default' => 600, 'min' => 100, 'max' => 2000, 'step' => 1]);
        $this->add_responsive_control('image_max_width', ['label' => __('Max-Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', '%', 'vw'], 'range' => ['px' => ['min' => 0, 'max' => 1000], '%' => ['min' => 0, 'max' => 100], 'vw' => ['min' => 0, 'max' => 100]]]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Style', 'dynamic-content-for-elementor')]);
        $this->add_responsive_control('svg_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'prefix_class' => 'align-', 'default' => 'left', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => __('Source', 'dynamic-content-for-elementor')]);
        $this->add_control('mask_output', ['label' => __('Output', 'dynamic-content-for-elementor'), 'description' => __('Use masking only for application on other page elements. Activating this option the svg element will not be displayed.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->add_control('id_svg_class', ['label' => __('CSS Class', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '', 'condition' => ['mask_output' => 'yes']]);
        $this->add_control('mask_output_direct', ['label' => __('Directly to element', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->add_control('note_idclass', ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'raw' => __('Type the class of the element to trasform with the SVG distortion', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'separator' => 'after', 'condition' => ['mask_output' => 'yes']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $widgetId = $this->get_id();
        $viewBoxW = $settings['viewbox_width'];
        $viewBoxH = $settings['viewbox_height'];
        $id_svg_class = $settings['id_svg_class'];
        echo '<div class="dce_imagemask-wrapper">';
        ?>

		<svg id="dce-svg-<?php 
        echo $widgetId;
        ?>" class="dce-svg-imagemask" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 <?php 
        echo $viewBoxW;
        ?> <?php 
        echo $viewBoxH;
        ?>" preserveAspectRatio="xMidYMid slice">

			<defs>
				<mask id="image-mask-<?php 
        echo $widgetId;
        ?>"maskunits="userSpaceOnUse" maskcontentunits="userSpaceOnUse" preserveAspectRatio="xMidYMid slice">
					<?php 
        // https://codepen.io/tutsplus/pen/zREZGe
        if ($settings['mask_shape_type'] == 'image') {
            $image_masking_url = Group_Control_Image_Size::get_attachment_image_src($settings['image_masking']['id'], 'size_masking', $settings);
            ?>
						<image xlink:href="<?php 
            echo $image_masking_url;
            ?>"
						width="100%" height="100%"></image>
						<?php 
        } elseif ($settings['mask_shape_type'] == 'ring') {
            ?>
						<circle id="outer" cx="50%" cy="50%" r="50%" fill="white"/>
						<circle id="inner" cx="50%" cy="50%" r="<?php 
            echo $settings['radius_inner_circle']['size'];
            ?>%"/>
						<?php 
        } elseif ($settings['mask_shape_type'] == 'triangle') {
            ?>
						<path id="outer" d="M447.6,259.4L599,518.7H299.5H0l151.4-259.4L299.5,0L447.6,259.4z" fill="white"/>
						<?php 
        } elseif ($settings['mask_shape_type'] == 'circle') {
            ?>
						<circle id="outer" cx="50%" cy="50%" r="50%" fill="white"/>
						<?php 
        } elseif ($settings['mask_shape_type'] == 'hexagon') {
            ?>
						<polygon id="outer" points="446.3,0 148.8,0 0,257.7 148.8,515.3 446.3,515.3 595,257.7 " fill="white"/>
						<?php 
        } elseif ($settings['mask_shape_type'] == 'pentagonal') {
            ?>
						<polygon id="outer" x="0" y="0" points="298.6,0 0,217 114.1,568 483.2,568 597.2,217 " fill="white"/>
						<?php 
        } elseif ($settings['mask_shape_type'] == 'rhombus') {
            ?>
						<path id="outer" d="M586,293L293,586L147.4,438.6L0,293L293,0l147.1,148.9L586,293z" fill="white"/>
						<?php 
        } elseif ($settings['mask_shape_type'] == 'star') {
            ?>
						<path id="outer" d="M298,0l92.1,186.6L596,216.5L447,361.7l35.2,205.1L298,470l-184.2,96.8L149,361.7L0,216.5l205.9-29.9L298,0z" fill="white"/>
						<?php 
        } elseif ($settings['mask_shape_type'] == 'heart') {
            ?>
						<path id="outer" class="st0" d="M299,546.5L51.3,298.8c-68.4-68.4-68.4-179.2,0-247.5c68.4-68.4,179.2-68.4,247.5,0l0.2,0.2l0.2-0.2
		c68.4-68.4,179.2-68.4,247.5,0c68.4,68.4,68.4,179.2,0,247.5L299,546.5z" fill="white"/>
						<?php 
        } elseif ($settings['mask_shape_type'] == 'text') {
            ?>
						<text id="outer" fill="white"/>>Lorem ipsum</text>
						<?php 
        } elseif ($settings['mask_shape_type'] == 'custom_path') {
            ?>
						<path id="outer" d="<?php 
            echo $settings['shape_numbers'];
            ?>" fill="white"/>
						<?php 
        }
        ?>
				</mask>
			</defs>
			<?php 
        if (!$settings['mask_output']) {
            $image_url = Group_Control_Image_Size::get_attachment_image_src($settings['mask_image']['id'], 'image', $settings);
            ?>
				<image width="100%" height="100%" preserveAspectRatio="xMidYMid slice" xlink:href="<?php 
            echo $image_url;
            ?>" mask="url(#image-mask-<?php 
            echo $widgetId;
            ?>)"></image>
			<?php 
        }
        ?>
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
        if ($settings['mask_output'] && $settings['id_svg_class'] != '') {
            if (!$settings['mask_output_direct']) {
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
                ?> .elementor-icon,
				.<?php 
                echo $id_svg_class;
                ?> .elementor-button
				<?php 
            } else {
                echo '.' . $id_svg_class;
            }
            ?>
				{
					mask: url(#image-mask-<?php 
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
		var id_svg_class = settings.id_svg_class;

		var viewBoxW = settings.viewbox_width;
		var viewBoxH = settings.viewbox_height;
		var maxWidth = settings.image_max_width.size;
		var mask_output = settings.mask_output;
		var mask_output_direct = settings.mask_output_direct;

		var mask_shape_type = settings.mask_shape_type;
		var shape_numbers = settings.shape_numbers;

		var radius_inner_circle = settings.radius_inner_circle.size;

		var image = {
			id: settings.mask_image.id,
			url: settings.mask_image.url,
			size: settings.image_size,
			dimension: settings.image_custom_dimension,
			model: view.getEditModel()
		};
		var url_image = elementor.imagesManager.getImageUrl( image );

		var image_masking = {
			id: settings.image_masking.id,
			url: settings.image_masking.url,
			size: settings.size_masking_size,
			dimension: settings.size_masking_custom_dimension,
			model: view.getEditModel()
		};
		var url_image_masking = elementor.imagesManager.getImageUrl( image_masking );
		#>
		<div class="dce_imagemask-wrapper">
		<svg id="dce-svg-{{idWidget}}" class="dce-svg-imagemask" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 {{viewBoxW}} {{viewBoxH}}" preserveAspectRatio="xMidYMid slice">
			<defs>
				<mask id="image-mask-{{idWidget}}">
					<# if(mask_shape_type == 'image'){ #>
					<image xlink:href="{{url_image_masking}}" width="100%" height="100%"></image>
					<# } else if(mask_shape_type == 'ring'){ #>
						<circle id="outer" cx="50%" cy="50%" r="50%" fill="white"/>
						<circle id="inner" cx="50%" cy="50%" r="{{radius_inner_circle}}%"/>
					<# } else if(mask_shape_type == 'triangle'){ #>
						<path id="outer" d="M447.6,259.4L599,518.7H299.5H0l151.4-259.4L299.5,0L447.6,259.4z" fill="white"/>
					<# } else if(mask_shape_type == 'circle'){ #>
						<circle id="outer" cx="50%" cy="50%" r="50%" fill="white" fill="white"/>
					<# } else if(mask_shape_type == 'hexagon'){ #>
						<polygon id="outer" points="446.3,0 148.8,0 0,257.7 148.8,515.3 446.3,515.3 595,257.7 " fill="white"/>
					<# } else if(mask_shape_type == 'pentagonal'){ #>
						<polygon id="outer" x="0" y="0" points="298.6,0 0,217 114.1,568 483.2,568 597.2,217 " fill="white" fill="white"/>
					<# } else if(mask_shape_type == 'rhombus'){ #>
						<path id="outer" d="M586,293L293,586L147.4,438.6L0,293L293,0l147.1,148.9L586,293z" fill="white"/>
					<# } else if(mask_shape_type == 'star'){ #>
						<path id="outer" d="M298,0l92.1,186.6L596,216.5L447,361.7l35.2,205.1L298,470l-184.2,96.8L149,361.7L0,216.5l205.9-29.9L298,0z" fill="white"/>
					<# } else if(mask_shape_type == 'heart'){ #>
						<path id="outer" class="st0" d="M299,546.5L51.3,298.8c-68.4-68.4-68.4-179.2,0-247.5c68.4-68.4,179.2-68.4,247.5,0l0.2,0.2l0.2-0.2
		c68.4-68.4,179.2-68.4,247.5,0c68.4,68.4,68.4,179.2,0,247.5L299,546.5z" fill="white"/>
					<# } else if(mask_shape_type == 'text'){ #>
						<text id="outer" fill="white"/>>Lorem ipsum</text>
					<# } else if(mask_shape_type == 'custom_path'){ #>
						<path id="outer" d="{{shape_numbers}}" fill="white"/>
					<# } #>

				</mask>
			</defs>
			<# if( !mask_output ){ #>
			<image width="100%" height="100%" preserveAspectRatio="xMidYMid slice" xlink:href="{{url_image}}" mask="url(#image-mask-{{idWidget}})"></image>
			<# } #>
			<style>
			   	<# if( maxWidth && maxWidth > 0 ){ #>
				#dce-svg-{{idWidget}}{
					max-width: {{maxWidth}}px;
				}
				<# } #>
				<# if( mask_output && id_svg_class != '' ){
					if( mask_output_direct == '' ){
					#>
				.{{id_svg_class}} svg > image,
				.{{id_svg_class}} svg  path,
				.{{id_svg_class}} svg polyline,
				.{{id_svg_class}} img,
				.{{id_svg_class}} p,
				.{{id_svg_class}} .elementor-heading-title,
				.{{id_svg_class}} .elementor-icon,
				.{{id_svg_class}} .elementor-button
				<# }else{ #>
				.{{id_svg_class}}
				<# } #>
				{
					mask: url(#image-mask-{{idWidget}});

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
		<?php 
    }
}
