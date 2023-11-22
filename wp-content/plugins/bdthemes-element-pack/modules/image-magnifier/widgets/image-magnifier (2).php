<?php
namespace ElementPack\Modules\ImageMagnifier\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;

use ElementPack\Modules\ImageMagnifier\Skins;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Image_Magnifier extends Module_Base {

	public function get_name() {
		return 'bdt-image-magnifier';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Image Magnifier', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-image-magnifier';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'image', 'magnifier', 'magnifying', 'zoom' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'imagezoom' ];
        }
    }

	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['imagezoom', 'imagesloaded', 'ep-scripts'];
        } else {
			return [ 'imagezoom', 'imagesloaded', 'ep-image-magnifier' ];
        }
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/GSy3pLihNPY';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
			]
		);

		$this->start_controls_tabs('tabs_image_choose');

		$this->start_controls_tab(
			'image_choose_thumb_image',
			[
				'label' => __('Thumb Image', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'image',
			[
				'label'   => esc_html__( 'Thumb Image', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [ 'active' => true ],
				'description' => esc_html__( 'If you want to load magnifying image as large so open right tab', 'bdthemes-element-pack' ),
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'image_choose_magnify_image',
			[
				'label' => __('Magnify Image', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'magnify_img',
			[
				'label'   => esc_html__( 'Magnify Image', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [ 'active' => true ],
			]
		);
		
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'type',
			[
				'label'   => esc_html__( 'Type', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inner',
				'options' => [
					'inner'    => esc_html__( 'Inner', 'bdthemes-element-pack' ),
					'standard' => esc_html__( 'Standard', 'bdthemes-element-pack' ),
					'follow'   => esc_html__( 'Follow', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->add_control(
			'smooth_move',
			[
				'label'   => esc_html__( 'Smooth Move', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'preload',
			[
				'label'   => esc_html__( 'Preload', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'zoom_ratio',
			[
				'label'       => esc_html__( 'Zoom Ratio', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::IMAGE_DIMENSIONS,
				'description' => 'Zoom ratio widht and height, such as 480:300',
				'condition'	  => [
					'type'		=> ['standard', 'follow']
				]
			]
		);

		$this->add_control(
			'horizontal_offset',
			[
				'label'   => esc_html__( 'Horizontal Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => '10',
				],
				'condition' => [
					'type' => 'standard',
				],
			]
		);

		$this->add_control(
			'vertical_offset',
			[
				'label'   => esc_html__( 'Vertical Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => '0',
				],
				'condition' => [
					'type' => 'standard',
				],
			]
		);

		$this->add_control(
			'position',
			[
				'label'   => esc_html__( 'Position', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'right' => esc_html__( 'Right', 'bdthemes-element-pack' ),
					'left'  => esc_html__( 'Left', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'type' => 'standard',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__( 'Image', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'image_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-image-magnifier' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .bdt-image-magnifier' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'image_border',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-image-magnifier',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-image-magnifier' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_control(
			'image_opacity',
			[
				'label'   => __( 'Opacity (%)', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-image-magnifier img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function render() {
		$settings      = $this->get_settings_for_display();
		$id            = 'bdt-image-magnifier-' . $this->get_id();
		$image_url     = wp_get_attachment_image_src( $settings['image']['id'], 'full' );
		$big_image_src = wp_get_attachment_image_src( $settings['magnify_img']['id'], 'full' );
		$big_image_src = ( $big_image_src ) ? : $image_url;


		$horizontal_offset = (isset($settings['horizontal_offset']['size']) ? $settings['horizontal_offset']['size'] : 0);
		$vertical_offset   = (isset($settings['vertical_offset']['size']) ? $settings['vertical_offset']['size'] : 0);
		
		$zoom_ratio_width  = ( isset($settings['zoom_ratio']['width']) ? $settings['zoom_ratio']['width'] : 480);
		$zoom_ratio_height = (isset($settings['zoom_ratio']['height']) ? $settings['zoom_ratio']['height'] : 300);

		$this->add_render_attribute(
			[
				'image-magnifier' => [
					'class' => [ 'bdt-image-magnifier-image' ],					
					'src'   => esc_attr($image_url[0]),
					'alt'   => '',
				]
			]
		);

		$this->add_render_attribute(
			[
				'image-magnifier-settings' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							"type"        => $settings["type"],
							"bigImageSrc" => esc_attr($big_image_src[0]),
							"smoothMove"  => $settings["smooth_move"] ? true : false,
							"preload"     => $settings["preload"] ? true : false,
							"position"    => $settings["position"],
							"zoomSize"    => [ (int) $zoom_ratio_width , (int) $zoom_ratio_height ],
							"offset"      => [ (int) $horizontal_offset , (int) $vertical_offset ],
				        ]))
					],
					'class' => [
						'bdt-image-magnifier',
						'bdt-position-relative',
					]
				]
			]
		);



		// type:The image zoom mode.(inner,standard,follow) Default:inner
		// bigImageSrc:If Call image zoom on the thumb image and want to zoom with large image set this option. Default:null
		// smoothMove:Is the zoomviewer's image move smooth. (true/false) Default:true
		// preload:Is ImageZoom preload the large image. Default:true
		// zoomSize:The ZoomView Size for standard mode and follow mode. Default:[100,100]
		// offset:Set the offset of the zoomviewer for standard mode. Default:[10,0]
		// position:Set left/right to show the zoomviewer. Default:right
		// alignTo:Set the id of the zoomviewer align to (Standard Mode). Default:null (alignTo the riginal image)
		// descriptionClass:The coustom description css class. Default:null
		// showDescription:Is zoomimage auto show the image description. Default:true
		// zoomViewerClass:The coustom class of the zoom viewer for follow mode and standard mode. Default:null
		// zoomHandlerClass:The coustom class of the zoom handler area for standard mode. Default:null       string
		// onShow:Event when zoom begin. Default:null
		// onHide:Event when zoom end. Default:null


		// if ($settings['position']) {
		// 	$this->add_render_attribute( 'image-magnifier-settings', 'position', $settings['position'] );
		// }

        if (isset($image_url[0])) {
        	?>
            <div <?php echo $this->get_render_attribute_string( 'image-magnifier-settings' ); ?>>
                <img <?php echo $this->get_render_attribute_string( 'image-magnifier' ); ?>>
            </div>
            <?php
        } else {
        	?>
        	<div class="bdt-alert-warning bdt-text-center">Opps!! You didn't choose any image for magnifying action</div>
        	<?php
        }
	}
}
