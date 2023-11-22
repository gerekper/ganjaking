<?php

namespace ElementPack\Modules\CustomCarousel\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Plugin;

use ElementPack\Utils;
use ElementPack\Traits\Global_Swiper_Controls;
use ElementPack\Traits\Global_Mask_Controls;

use ElementPack\Element_Pack_Loader;

use ElementPack\Modules\CustomCarousel\Skins;
use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class Custom_Carousel extends Module_Base {

	use Global_Swiper_Controls;
	use Global_Mask_Controls;

	private $slide_prints_count = 0;

	public function get_name() {
		return 'bdt-custom-carousel';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Custom Carousel', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-custom-carousel';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'custom', 'carousel', 'navigation' ];
	}

	public function get_style_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'ep-styles' ];
		} else {
			return [ 'ep-font', 'ep-custom-carousel' ];
		}
	}

	public function get_script_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'ep-scripts' ];
		} else {
			return [ 'ep-custom-carousel' ];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/RV0AHN6O0Fo';
	}

	public function register_skins() {
		$this->add_skin( new Skins\Skin_Custom_Content( $this ) );
	}

	protected function register_controls() {

		$slides_per_view = range( 1, 10 );
		$slides_per_view = array_combine( $slides_per_view, $slides_per_view );

		$this->start_controls_section(
			'section_slides',
			[ 
				'label' => esc_html__( 'Slides', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'type',
			[ 
				'type'        => Controls_Manager::CHOOSE,
				'label'       => esc_html__( 'Type', 'bdthemes-element-pack' ),
				'default'     => 'image',
				'options'     => [ 
					'image' => [ 
						'title' => esc_html__( 'Image', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-image',
					],
					'video' => [ 
						'title' => esc_html__( 'Video', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-video-camera',
					],
				],
				'label_block' => false,
				'toggle'      => false,
			]
		);

		$repeater->add_control(
			'image',
			[ 
				'label'   => esc_html__( 'Image', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'image_link_to_type',
			[ 
				'label'     => esc_html__( 'Link to', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [ 
					''       => esc_html__( 'None', 'bdthemes-element-pack' ),
					'file'   => esc_html__( 'Media File', 'bdthemes-element-pack' ),
					'custom' => esc_html__( 'Custom URL', 'bdthemes-element-pack' ),
				],
				'condition' => [ 
					'type' => 'image',
				],
			]
		);

		$repeater->add_control(
			'image_link_to',
			[ 
				'type'        => Controls_Manager::URL,
				'placeholder' => 'http://your-link.com',
				'dynamic'     => [ 'active' => true ],
				'condition'   => [ 
					'type'               => 'image',
					'image_link_to_type' => 'custom',
				],
				'separator'   => 'none',
				'show_label'  => false,
			]
		);

		$repeater->add_control(
			'video',
			[ 
				'label'         => esc_html__( 'Video Link', 'bdthemes-element-pack' ),
				'type'          => Controls_Manager::URL,
				'dynamic'       => [ 'active' => true ],
				'placeholder'   => esc_html__( 'Enter your video link', 'bdthemes-element-pack' ),
				'description'   => esc_html__( 'Insert YouTube or Vimeo link', 'bdthemes-element-pack' ),
				'show_external' => false,
				'condition'     => [ 
					'type' => 'video',
				],
			]
		);

		$this->add_control(
			'slides',
			[ 
				'label'     => esc_html__( 'Slides', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::REPEATER,
				'fields'    => $repeater->get_controls(),
				'default'   => $this->get_repeater_defaults(),
				'condition' => [ 
					'_skin' => ''
				],
			]
		);


		$repeater_2 = new Repeater();

		$repeater_2->add_control(
			'source',
			[ 
				'type'        => Controls_Manager::CHOOSE,
				'label'       => esc_html__( 'Source', 'bdthemes-element-pack' ),
				'default'     => 'editor',
				'options'     => [ 
					'editor'   => [ 
						'title' => esc_html__( 'Editor', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-edit',
					],
					'template' => [ 
						'title' => esc_html__( 'Template', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-section',
					],
				],
				'label_block' => false,
				'toggle'      => false,
			]
		);


		$repeater_2->add_control(
			'template_source',
			[ 
				'label'     => esc_html__( 'Select Source', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'elementor',
				'options'   => [ 
					"elementor" => esc_html__( 'Elementor Template', 'bdthemes-element-pack' ),
					'anywhere'  => esc_html__( 'Anywhere Template', 'bdthemes-element-pack' ),
				],
				'condition' => [ 
					'source' => 'template',
				],
			]
		);
		$repeater_2->add_control(
			'elementor_template',
			[ 
				'label'       => __( 'Select Template', 'bdthemes-element-pack' ),
				'type'        => Dynamic_Select::TYPE,
				'label_block' => true,
				'placeholder' => __( 'Type and select template', 'bdthemes-element-pack' ),
				'query_args'  => [ 
					'query' => 'elementor_template',
				],
				'condition'   => [ 
					'source'          => 'template',
					'template_source' => "elementor"
				],
			]
		);
		$repeater_2->add_control(
			'anywhere_template',
			[ 
				'label'       => __( 'Select Template', 'bdthemes-element-pack' ),
				'type'        => Dynamic_Select::TYPE,
				'label_block' => true,
				'placeholder' => __( 'Type and select template', 'bdthemes-element-pack' ),
				'query_args'  => [ 
					'query' => 'anywhere_template',
				],
				'condition'   => [ 
					'source'          => 'template',
					'template_source' => "anywhere"
				],
			]
		);

		$repeater_2->add_control(
			'editor_content',
			[ 
				'type'       => Controls_Manager::TEXTAREA,
				'dynamic'    => [ 'active' => true ],
				'default'    => __( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', 'bdthemes-element-pack' ),
				'show_label' => false,
				'condition'  => [ 
					'source' => 'editor',
				],
			]
		);

		$this->add_control(
			'skin_template_slides',
			[ 
				'label'     => esc_html__( 'Slides', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::REPEATER,
				'fields'    => $repeater_2->get_controls(),
				'default'   => $this->get_repeater_defaults(),
				'condition' => [ 
					'_skin' => 'bdt-custom-content'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[ 
				'name'      => 'image_size',
				'default'   => 'medium',
				'separator' => 'before',
				'condition' => [ 
					'_skin!' => 'bdt-custom-content',
				],
			]
		);

		$this->add_control(
			'image_fit',
			[ 
				'label'     => esc_html__( 'Image Fit', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [ 
					''        => esc_html__( 'Cover', 'bdthemes-element-pack' ),
					'contain' => esc_html__( 'Contain', 'bdthemes-element-pack' ),
					'auto'    => esc_html__( 'Auto', 'bdthemes-element-pack' ),
				],
				'selectors' => [ 
					'{{WRAPPER}} .swiper-carousel .bdt-ep-custom-carousel-thumbnail' => 'background-size: {{VALUE}}',
				],
				'condition' => [ 
					'_skin!' => 'bdt-custom-content',
				],
			]
		);

		$this->add_control(
			'image_mask_popover',
			[ 
				'label'        => esc_html__( 'Image Mask', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'render_type'  => 'template',
				'return_value' => 'yes',
				'condition'    => [ 
					'_skin!' => 'bdt-custom-content',
				],
			]
		);

		//Global Image Mask Controls
		$this->register_image_mask_controls();

		$this->add_responsive_control(
			'slides_per_view',
			[ 
				'label'          => esc_html__( 'Slides Per View', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => $slides_per_view,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
			]
		);

		$this->end_controls_section();

		//Navigation Controls
		$this->start_controls_section(
			'section_content_navigation',
			[ 
				'label' => __( 'Navigation', 'bdthemes-element-pack' ),
			]
		);

		//Global Navigation Controls
		$this->register_navigation_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional_options',
			[ 
				'label' => esc_html__( 'Carousel Settings', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'skin',
			[ 
				'label'        => esc_html__( 'Layout', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'carousel',
				'options'      => [ 
					'carousel'  => esc_html__( 'Carousel', 'bdthemes-element-pack' ),
					'coverflow' => esc_html__( 'Coverflow', 'bdthemes-element-pack' ),
				],
				'prefix_class' => 'bdt-ep-custom-carousel-style-',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'coverflow_toggle',
			[ 
				'label'        => __( 'Coverflow Effect', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition'    => [ 
					'skin' => 'coverflow'
				]
			]
		);

		$this->start_popover();

		$this->add_control(
			'coverflow_rotate',
			[ 
				'label'       => esc_html__( 'Rotate', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [ 
					'size' => 50,
				],
				'range'       => [ 
					'px' => [ 
						'min'  => -360,
						'max'  => 360,
						'step' => 5,
					],
				],
				'condition'   => [ 
					'coverflow_toggle' => 'yes'
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'coverflow_stretch',
			[ 
				'label'       => __( 'Stretch', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [ 
					'size' => 0,
				],
				'range'       => [ 
					'px' => [ 
						'min'  => 0,
						'step' => 10,
						'max'  => 100,
					],
				],
				'condition'   => [ 
					'coverflow_toggle' => 'yes'
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'coverflow_modifier',
			[ 
				'label'       => __( 'Modifier', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [ 
					'size' => 1,
				],
				'range'       => [ 
					'px' => [ 
						'min'  => 1,
						'step' => 1,
						'max'  => 10,
					],
				],
				'condition'   => [ 
					'coverflow_toggle' => 'yes'
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'coverflow_depth',
			[ 
				'label'       => __( 'Depth', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [ 
					'size' => 100,
				],
				'range'       => [ 
					'px' => [ 
						'min'  => 0,
						'step' => 10,
						'max'  => 1000,
					],
				],
				'condition'   => [ 
					'coverflow_toggle' => 'yes'
				],
				'render_type' => 'template',
			]
		);

		$this->end_popover();

		$this->add_control(
			'hr_655',
			[ 
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [ 
					'skin' => 'coverflow'
				]
			]
		);

		$this->add_control(
			'speed',
			[ 
				'label'   => esc_html__( 'Transition Duration', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 500,
			]
		);

		$this->add_control(
			'autoplay',
			[ 
				'label'   => esc_html__( 'Autoplay', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[ 
				'label'     => esc_html__( 'Autoplay Speed', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => [ 
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pauseonhover',
			[ 
				'label'     => esc_html__( 'Pause on Hover', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [ 
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'loop',
			[ 
				'label'   => esc_html__( 'Infinite Loop', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'centered_slides',
			[ 
				'label'       => esc_html__( 'Centered Slides', 'bdthemes-element-pack' ),
				'description' => esc_html__( 'Use even slides for get better look', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'grab_cursor',
			[ 
				'label' => __( 'Grab Cursor', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'free_mode',
			[ 
				'label' => __( 'Drag Free Mode', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'observer',
			[ 
				'label'       => __( 'Observer', 'bdthemes-element-pack' ),
				'description' => __( 'When you use carousel in any hidden place (in tabs, accordion etc) keep it yes.', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'overlay',
			[ 
				'label'     => esc_html__( 'Overlay', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [ 
					''     => esc_html__( 'None', 'bdthemes-element-pack' ),
					'text' => esc_html__( 'Text', 'bdthemes-element-pack' ),
					'icon' => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				],
				'separator' => 'before',
				'condition' => [ 
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'caption',
			[ 
				'label'     => esc_html__( 'Caption', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'title',
				'options'   => [ 
					'title'   => esc_html__( 'Title', 'bdthemes-element-pack' ),
					'caption' => esc_html__( 'Caption', 'bdthemes-element-pack' ),
				],
				'condition' => [ 
					'overlay' => 'text',
					'_skin'   => '',
				],
			]
		);

		$this->add_control(
			'icon',
			[ 
				'label'     => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'plus-circle',
				'options'   => [ 
					'search'      => [ 
						'icon' => 'eicon-search',
					],
					'plus-circle' => [ 
						'icon' => 'eicon-plus-circle-o',
					],
					'plus'        => [ 
						'icon' => 'eicon-plus',
					],
					'link'        => [ 
						'icon' => 'eicon-link',
					],
					'play-circle' => [ 
						'icon' => 'eicon-play',
					],
					'play'        => [ 
						'icon' => 'eicon-caret-right',
					],
				],
				'condition' => [ 
					'overlay' => 'icon',
					'_skin'   => '',
				],
			]
		);

		$this->add_control(
			'overlay_animation',
			[ 
				'label'     => esc_html__( 'Animation', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => element_pack_transition_options(),
				'condition' => [ 
					'overlay!' => '',
					'_skin'    => '',
				],
				'separator' => 'after',
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[ 
				'type'           => Controls_Manager::SELECT,
				'label'          => esc_html__( 'Slides to Scroll', 'bdthemes-element-pack' ),
				'default'        => 1,
				'tablet_default' => 1,
				'mobile_default' => 1,
				'options'        => [ 
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					5 => '5',
					6 => '6',
				],
				'condition'      => [ 
					'skin' => 'carousel',
				]
			]
		);

		$this->add_control(
			'slides_per_column',
			[ 
				'type'      => Controls_Manager::SELECT,
				'label'     => esc_html__( 'Slides Per Column', 'bdthemes-element-pack' ),
				'options'   => $slides_per_view,
				'default'   => '1',
				'condition' => [ 
					'skin' => 'carousel',
				]
			]
		);

		$this->add_responsive_control(
			'height',
			[ 
				'type'       => Controls_Manager::SLIDER,
				'label'      => esc_html__( 'Height', 'bdthemes-element-pack' ),
				'size_units' => [ 'px', 'vh' ],
				'range'      => [ 
					'px' => [ 
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [ 
						'min' => 20,
					],
				],
				'selectors'  => [ 
					'{{WRAPPER}} .swiper-carousel .swiper-slide' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [ 
					'_skin' => ''
				]
			]
		);

		$this->add_responsive_control(
			'width',
			[ 
				'type'       => Controls_Manager::SLIDER,
				'label'      => esc_html__( 'Width', 'bdthemes-element-pack' ),
				'range'      => [ 
					'px' => [ 
						'min' => 100,
						'max' => 1140,
					],
					'%'  => [ 
						'min' => 50,
					],
				],
				'size_units' => [ '%', 'px' ],
				'default'    => [ 
					'unit' => '%',
				],
				'selectors'  => [ 
					'{{WRAPPER}} .swiper-carousel' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slides_style',
			[ 
				'label' => esc_html__( 'Slides', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'space_between',
			[ 
				'label'   => esc_html__( 'Space Between', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [ 
					'px' => [ 
						'max' => 50,
					],
				],
				'default' => [ 
					'size' => 10,
				],
				// 'tablet_default' => [
				// 	'size' => 10,
				// ],
				// 'mobile_default' => [
				// 	'size' => 10,
				// ],
				// 'render_type' => 'none',
			]
		);

		$this->add_control(
			'slide_background_color',
			[ 
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .swiper-carousel .swiper-slide' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'slide_border_size',
			[ 
				'label'     => esc_html__( 'Border Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [ 
					'{{WRAPPER}} .swiper-carousel .swiper-slide' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'slide_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .swiper-carousel .swiper-slide' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'slide_padding',
			[ 
				'label'     => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [ 
					'{{WRAPPER}} .swiper-carousel .swiper-slide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'slide_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 
					'%' => [ 
						'max' => 50,
					],
				],
				'selectors'  => [ 
					'{{WRAPPER}} .swiper-carousel .swiper-slide, {{WRAPPER}} .bdt-ep-custom-carousel .swiper-carousel' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'shadow_mode',
			[ 
				'label'        => esc_html__( 'Shadow Mode', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-ep-shadow-mode-',
			]
		);

		$this->add_control(
			'shadow_color',
			[ 
				'label'     => esc_html__( 'Shadow Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'shadow_mode' => 'yes',
				],
				'selectors' => [ 
					'{{WRAPPER}} .elementor-widget-container:before' => is_rtl() ? 'background: linear-gradient(to left, {{VALUE}} 5%,rgba(255,255,255,0) 100%);' : 'background: linear-gradient(to right, {{VALUE}} 5%,rgba(255,255,255,0) 100%);',
					'{{WRAPPER}} .elementor-widget-container:after'  => is_rtl() ? 'background: linear-gradient(to left, rgba(255,255,255,0) 0%, {{VALUE}} 95%);' : 'background: linear-gradient(to right, rgba(255,255,255,0) 0%, {{VALUE}} 95%);',
				],
			]
		);

		$this->add_control(
			'active_item_mode',
			[ 
				'label'        => esc_html__( 'Active Item Style', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'         => Controls_Manager::SWITCHER,
				'separator'    => 'before',
				'prefix_class' => 'bdt-ep-active-item--',
				'render_type'  => 'template',
				'condition'    => [ 
					'centered_slides' => 'yes',
				],
			]
		);

		$this->add_control(
			'active_item_overlay_color',
			[ 
				'label'     => esc_html__( 'Overlay Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}}.bdt-ep-active-item--yes .swiper-slide.bdt-transition-toggle.swiper-slide-active>a>div:before, {{WRAPPER}}.bdt-ep-active-item--yes .swiper-slide.bdt-transition-toggle.swiper-slide-active>div:before' => 'background: {{VALUE}}',
				],
				'condition' => [ 
					'active_item_mode' => 'yes',
					'centered_slides'  => 'yes',
				],
			]
		);

		$this->add_control(
			'active_item_background_color',
			[ 
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}}.bdt-ep-active-item--yes .swiper-slide.bdt-transition-toggle.swiper-slide-active' => 'background: {{VALUE}}',
				],
				'condition' => [ 
					'active_item_mode' => 'yes',
					'centered_slides'  => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'active_item_padding',
			[ 
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [ 
					'{{WRAPPER}}.bdt-ep-active-item--yes .swiper-slide.bdt-ep-custom-carousel-item.bdt-transition-toggle.swiper-slide-active' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [ 
					'active_item_mode' => 'yes',
					'centered_slides'  => 'yes',
				],
			]
		);

		$this->add_control(
			'slide_opacity',
			[ 
				'label'     => esc_html__( 'Others Item Opacity', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min'  => 0,
						'step' => 0.1,
						'max'  => 1,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}}.bdt-ep-active-item--yes .swiper-slide.bdt-ep-custom-carousel-item' => 'opacity: {{SIZE}};',
				],
				'condition' => [ 
					'active_item_mode' => 'yes',
					'centered_slides'  => 'yes',
				],
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		//Navigation Style
		$this->start_controls_section(
			'section_style_navigation',
			[ 
				'label'      => __( 'Navigation', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [ 
					'relation' => 'or',
					'terms'    => [ 
						[ 
							'name'     => 'navigation',
							'operator' => '!=',
							'value'    => 'none',
						],
						[ 
							'name'  => 'show_scrollbar',
							'value' => 'yes',
						],
					],
				],
			]
		);

		//Global Navigation Style Controls
		$this->register_navigation_style_controls( 'swiper-carousel' );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_overlay',
			[ 
				'label'     => esc_html__( 'Overlay', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'overlay!' => '',
					'_skin'    => '',
				],
			]
		);

		$this->add_control(
			'overlay_background_color',
			[ 
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-ep-custom-carousel-item .bdt-overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'overlay_text_color',
			[ 
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-ep-custom-carousel-item .bdt-overlay' => 'color: {{VALUE}};',
				],
				'condition' => [ 
					'overlay' => 'text',
				],
			]
		);

		$this->add_control(
			'overlay_icon_color',
			[ 
				'label'     => esc_html__( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-ep-custom-carousel-item .bdt-overlay' => 'color: {{VALUE}};',
				],
				'condition' => [ 
					'overlay' => 'icon',
				],
			]
		);

		$this->add_control(
			'icon_size',
			[ 
				'label'     => esc_html__( 'Icon Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-ep-custom-carousel-item .bdt-overlay i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [ 
					'overlay' => 'icon',
				],
			]
		);

		$this->add_responsive_control(
			'ovarlay_spacing',
			[ 
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-ep-custom-carousel-item .bdt-overlay' => 'margin: {{SIZE}}px; max-width: calc(100% - ({{SIZE}}px * 2));',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'      => 'caption_typography',
				'label'     => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} .bdt-ep-custom-carousel-item .bdt-overlay',
				'condition' => [ 
					'overlay' => 'text',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_lightbox_style',
			[ 
				'label'     => esc_html__( 'Lightbox', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'lightbox_video_width',
			[ 
				'label'     => esc_html__( 'Video Width', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'units'     => [ '%' ],
				'default'   => [ 
					'unit' => '%',
				],
				'range'     => [ 
					'%' => [ 
						'min' => 50,
					],
				],
				'selectors' => [ 
					'.bdt-lightbox.bdt-open iframe' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function get_default_slides_count() {
		return 5;
	}

	protected function get_repeater_defaults() {
		$placeholder_image_src = Utils::get_placeholder_image_src();

		return array_fill( 0, $this->get_default_slides_count(), [ 
			'image' => [ 
				'url' => $placeholder_image_src,
			],
		] );
	}

	protected function get_image_caption( $slide ) {
		$caption_type = $this->get_settings_for_display( 'caption' );

		if ( empty( $caption_type ) ) {
			return '';
		}

		$attachment_post = get_post( $slide['image']['id'] );

		if ( 'caption' === $caption_type ) {
			return $attachment_post->post_excerpt;
		}

		if ( 'title' === $caption_type ) {
			return $attachment_post->post_title;
		}
	}

	protected function get_image_link_to( $slide ) {
		if ( isset( $slide['video']['url'] ) ) {
			return $slide['image']['url'];
		}

		if ( ! isset( $slide['image_link_to_type'] ) ) {
			return '';
		}
		if ( ! $slide['image_link_to_type'] ) {
			return '';
		}

		if ( 'custom' === $slide['image_link_to_type'] ) {
			return $slide['image_link_to']['url'];
		}

		return $slide['image']['url'];
	}

	protected function print_slide( array $slide, array $settings, $element_key ) {

		if ( 'bdt-custom-content' === $settings['_skin'] ) {
			$this->render_slide_template( $slide, $element_key, $settings );
		} else {
			if ( ! empty( $settings['thumbs_slider'] ) ) {

				$settings['video_play_icon'] = false;
				$this->add_render_attribute( $element_key . '-image', 'class', 'elementor-fit-aspect-ratio' );
			}

			$this->add_render_attribute( $element_key . '-image', [ 
				'class' => 'bdt-ep-custom-carousel-thumbnail',
				'style' => 'background-image: url(' . $this->get_slide_image_url( $slide, $settings ) . ')',
			] );

			$image_link_to = $this->get_image_link_to( $slide );



			if ( $image_link_to ) {

				if ( ( 'video' !== $slide['type'] ) && ( '' !== isset( $slide['video']['url'] ) ) ) {
					$this->add_render_attribute( $element_key . '_link', 'href', $image_link_to );
				}

				$image_mask = $settings['image_mask_popover'] == 'yes' ? ' bdt-image-mask' : '';

				if ( 'custom' === $slide['image_link_to_type'] ) {
					$this->add_render_attribute( $element_key . '_link', 'class', $image_mask );

					if ( $slide['image_link_to']['is_external'] ) {
						$this->add_render_attribute( $element_key . '_link', 'target', '_blank' );
					}

					if ( $slide['image_link_to']['nofollow'] ) {
						$this->add_render_attribute( $element_key . '_link', 'nofollow', '' );
					}
				} else {
					$this->add_render_attribute( $element_key . '_link', [ 
						'class'                        => 'bdt-ep-custom-carousel-lightbox-item' . $image_mask,
						'data-elementor-open-lightbox' => 'no',
						'data-caption'                 => $this->get_image_caption( $slide ),
					] );
				}

				if ( 'video' === $slide['type'] && $slide['video']['url'] ) {
					$this->add_render_attribute( $element_key . '_link', 'href', $slide['video']['url'] );
				}

				echo '<a ' . $this->get_render_attribute_string( $element_key . '_link' ) . '>';
			}

			$this->render_slide_image( $slide, $element_key, $settings );

			if ( $image_link_to ) {
				echo '</a>';
			}
		}
	}

	protected function render_slide_template( array $slide, $element_key, array $settings ) {

		?>
		<div <?php echo $this->get_render_attribute_string( $element_key . '-template' ); ?>>
			<?php

			if ( 'template' == $slide['source'] ) {
				if ( 'elementor' == $slide['template_source'] and ! empty( $slide['elementor_template'] ) ) {
					echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display( $slide['elementor_template'] );
					echo element_pack_template_edit_link( $slide['elementor_template'] );
				} elseif ( 'anywhere' == $slide['template_source'] and ! empty( $slide['anywhere_template'] ) ) {
					echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display( $slide['anywhere_template'] );
					echo element_pack_template_edit_link( $slide['anywhere_template'] );
				}
			} else {
				echo wp_kses_post( $slide['editor_content'] );
			}

			?>
		</div>
		<?php
	}

	protected function render_slide_image( array $slide, $element_key, array $settings ) {

		$this->add_render_attribute(
			[ 
				'overlay-settings' => [ 
					'class' => [ 
						'bdt-overlay',
						'bdt-overlay-default',
						'bdt-position-cover',
						'bdt-position-small',
						'bdt-flex',
						'bdt-flex-center',
						'bdt-flex-middle',
						$settings['overlay_animation'] ? 'bdt-transition-' . $settings['overlay_animation'] : ''
					],
				],
			],
			'',
			'',
			true
		);

		?>
		<div <?php echo $this->get_render_attribute_string( $element_key . '-image' ); ?>></div>

		<?php if ( $settings['overlay'] ) : ?>
			<div <?php echo $this->get_render_attribute_string( 'overlay-settings' ); ?>>
				<?php if ( 'text' === $settings['overlay'] ) : ?>
					<?php echo $this->get_image_caption( $slide ); ?>
				<?php else : ?>
					<i class="ep-icon-<?php echo esc_attr( $settings['icon'] ); ?>" aria-hidden="true"></i>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php
	}

	protected function render_header() {
		$id         = 'bdt-ep-custom-carousel-' . $this->get_id();
		$settings   = $this->get_settings_for_display();
		$skin_class = ( 'bdt-custom-content' == $settings['_skin'] ) ? 'custom-content' : 'default';

		$this->add_render_attribute( 'custom-carousel', 'id', $id );
		$this->add_render_attribute( 'custom-carousel', 'class', [ 'bdt-ep-custom-carousel', 'elementor-swiper', 'bdt-skin-' . $skin_class ] );
		$this->add_render_attribute( 'custom-carousel', 'data-bdt-lightbox', 'toggle: .bdt-ep-custom-carousel-lightbox-item; animation: slide;' );

		if ( 'arrows' == $settings['navigation'] ) {
			$this->add_render_attribute( 'custom-carousel', 'class', 'bdt-arrows-align-' . $settings['arrows_position'] );
		} elseif ( 'dots' == $settings['navigation'] ) {
			$this->add_render_attribute( 'custom-carousel', 'class', 'bdt-dots-align-' . $settings['dots_position'] );
		} elseif ( 'both' == $settings['navigation'] ) {
			$this->add_render_attribute( 'custom-carousel', 'class', 'bdt-arrows-dots-align-' . $settings['both_position'] );
		} elseif ( 'arrows-fraction' == $settings['navigation'] ) {
			$this->add_render_attribute( 'custom-carousel', 'class', 'bdt-arrows-dots-align-' . $settings['arrows_fraction_position'] );
		}

		$elementor_vp_lg = get_option( 'elementor_viewport_lg' );
		$elementor_vp_md = get_option( 'elementor_viewport_md' );
		$viewport_lg     = ! empty( $elementor_vp_lg ) ? $elementor_vp_lg - 1 : 1023;
		$viewport_md     = ! empty( $elementor_vp_md ) ? $elementor_vp_md - 1 : 767;

		if ( 'arrows-fraction' == $settings['navigation'] ) {
			$pagination_type = 'fraction';
		} elseif ( 'both' == $settings['navigation'] or 'dots' == $settings['navigation'] ) {
			$pagination_type = 'bullets';
		} elseif ( 'progressbar' == $settings['navigation'] ) {
			$pagination_type = 'progressbar';
		} else {
			$pagination_type = '';
		}

		$this->add_render_attribute(
			[ 
				'custom-carousel' => [ 
					'data-settings' => [ 
						wp_json_encode( array_filter( [ 
							"autoplay"        => ( "yes" == $settings["autoplay"] ) ? [ "delay" => $settings["autoplay_speed"] ] : false,
							"loop"            => ( $settings["loop"] == "yes" ) ? true : false,
							"speed"           => $settings["speed"],
							"pauseOnHover"    => ( "yes" == $settings["pauseonhover"] ) ? true : false,
							"slidesPerView"   => isset( $settings["slides_per_view_mobile"] ) ? (int) $settings["slides_per_view_mobile"] : 1,
							"spaceBetween"    => $settings["space_between"]["size"],
							"centeredSlides"  => ( $settings["centered_slides"] === "yes" ) ? true : false,
							"grabCursor"      => ( $settings["grab_cursor"] === "yes" ) ? true : false,
							"freeMode"        => ( $settings["free_mode"] === "yes" ) ? true : false,
							"effect"          => $settings["skin"],
							"slidesPerColumn" => ( $settings["slides_per_column"] > 1 ) ? $settings["slides_per_column"] : false,
							"slidesPerGroup"  => isset( $settings["slides_to_scroll_mobile"] ) ? (int) $settings["slides_to_scroll_mobile"] : 1,
							"observer"        => ( $settings["observer"] ) ? true : false,
							"observeParents"  => ( $settings["observer"] ) ? true : false,
							"breakpoints"     => [ 
								(int) $viewport_md => [ 
									"slidesPerView"  => isset( $settings["slides_per_view_tablet"] ) ? (int) $settings["slides_per_view_tablet"] : 2,
									"spaceBetween"   => $settings["space_between"]["size"],
									"slidesPerGroup" => isset( $settings["slides_to_scroll_tablet"] ) ? (int) $settings["slides_to_scroll_tablet"] : 1,
								],
								(int) $viewport_lg => [ 
									"slidesPerView"  => isset( $settings["slides_per_view"] ) ? (int) $settings["slides_per_view"] : 3,
									"spaceBetween"   => $settings["space_between"]["size"],
									"slidesPerGroup" => isset( $settings["slides_to_scroll"] ) ? (int) $settings["slides_to_scroll"] : 1,
								]
							],
							"navigation"      => [ 
								"nextEl" => "#" . $id . " .bdt-navigation-next",
								"prevEl" => "#" . $id . " .bdt-navigation-prev",
							],
							"pagination"      => [ 
								"el"             => "#" . $id . " .swiper-pagination",
								"type"           => $pagination_type,
								"clickable"      => "true",
								'dynamicBullets' => ( "yes" == $settings["dynamic_bullets"] ) ? true : false,

							],
							"scrollbar"       => [ 
								"el"   => "#" . $id . " .swiper-scrollbar",
								"hide" => "true",
							],
							'coverflowEffect' => [ 
								'rotate'       => ( "yes" == $settings["coverflow_toggle"] ) ? $settings["coverflow_rotate"]["size"] : 50,
								'stretch'      => ( "yes" == $settings["coverflow_toggle"] ) ? $settings["coverflow_stretch"]["size"] : 0,
								'depth'        => ( "yes" == $settings["coverflow_toggle"] ) ? $settings["coverflow_depth"]["size"] : 100,
								'modifier'     => ( "yes" == $settings["coverflow_toggle"] ) ? $settings["coverflow_modifier"]["size"] : 1,
								'slideShadows' => true,
							],
						] ) )
					]
				]
			]
		);

		$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute( 'swiper', 'class', 'swiper-carousel ' . $swiper_class );

		?>
		<div <?php echo $this->get_render_attribute_string( 'custom-carousel' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'swiper' ); ?>>
				<div class="swiper-wrapper">
					<?php
	}

	protected function render_footer() {
		$settings = $this->get_settings_for_display();

		if ( 'bdt-custom-content' == $settings['_skin'] ) {
			$slides_count = count( $settings['skin_template_slides'] );
		} else {
			$slides_count = count( $settings['slides'] );
		}

		?>
				</div>

				<?php if ( 'yes' === $settings['show_scrollbar'] ) : ?>
					<div class="swiper-scrollbar"></div>
				<?php endif; ?>

			</div>

			<?php if ( 1 < $slides_count ) : ?>

				<?php if ( 'both' == $settings['navigation'] ) : ?>
					<?php $this->render_both_navigation(); ?>
					<?php if ( 'center' === $settings['both_position'] ) : ?>
						<div class="bdt-position-z-index bdt-position-bottom">
							<div class="bdt-dots-container">
								<div class="swiper-pagination"></div>
							</div>
						</div>
					<?php endif; ?>
				<?php elseif ( 'arrows-fraction' == $settings['navigation'] ) : ?>
					<?php $this->render_arrows_fraction(); ?>
					<?php if ( 'center' === $settings['arrows_fraction_position'] ) : ?>
						<div class="bdt-dots-container">
							<div class="swiper-pagination"></div>
						</div>
					<?php endif; ?>
				<?php else : ?>
					<?php $this->render_pagination(); ?>
					<?php $this->render_navigation(); ?>
				<?php endif; ?>

			<?php endif; ?>

		</div>
		<?php
	}

	protected function render_loop_slides( array $settings = null ) {

		if ( null === $settings ) {
			$settings = $this->get_settings_for_display();
		}

		$default_settings = [ 'video_play_icon' => true ];
		$settings         = array_merge( $default_settings, $settings );

		$slides = array();

		if ( 'bdt-custom-content' == $settings['_skin'] ) {
			// $slides_count = count( $settings['skin_template_slides'] );
			$slides = $settings['skin_template_slides'];
		} else {
			// $slides_count = count( $settings['slides'] );
			$slides = $settings['slides'];
		}

		foreach ( $slides as $index => $slide ) :
			$this->slide_prints_count++;

			$image_mask = $settings['image_mask_popover'] == 'yes' ? ' bdt-image-mask' : '';

			$image_link_to = $this->get_image_link_to( $slide );

			if ( $image_link_to ) {
				$this->add_render_attribute( 'slide', 'class', 'swiper-slide bdt-ep-custom-carousel-item bdt-transition-toggle', true );
			} else {
				$this->add_render_attribute( 'slide', 'class', 'swiper-slide bdt-ep-custom-carousel-item bdt-transition-toggle' . $image_mask, true );
			}

			?>
			<div <?php echo $this->get_render_attribute_string( 'slide' ); ?>>
				<?php $this->print_slide( $slide, $settings, 'slide-' . $index . '-' . $this->slide_prints_count ); ?>
			</div>
			<?php
		endforeach;
	}

	protected function get_slide_image_url( $slide, array $settings ) {
		$image_url = Group_Control_Image_Size::get_attachment_image_src( $slide['image']['id'], 'image_size', $settings );

		if ( ! $image_url ) {
			$image_url = $slide['image']['url'];
		}

		return $image_url;
	}

	protected function render_slider( array $settings = null ) {
		$this->render_loop_slides( $settings );
	}

	public function render() {
		$this->render_header();
		$this->render_slider();
		$this->render_footer();
	}
}
