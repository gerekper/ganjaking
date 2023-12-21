<?php
/**
 * remote carousel widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Group_Control_Background;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Utils;

defined( 'ABSPATH' ) || die();

class Remote_Carousel extends Base {

    /**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Remote Carousel', 'happy-addons-pro' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-remote_carousel';
	}

	public function get_keywords() {
		return [ 'remote-carousel', 'remote carosel', 'ha carousel', 'ha remote carousel', 'carousel', 'remote' ];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__remote_carousel_general_controls();
	}

	// define shipping bar content controls
	public function __remote_carousel_general_controls(){
		$this->start_controls_section(
			'_section_remote_carousel',
			[
				'label' => __( 'Remote Carousel', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ha_rcc_unique_id',
			[
				'label' => __( 'Unique ID', 'happy-addons-pro' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'Enter remote carousel unique id', 'happy-addons-pro' ),
                'description' => __('Input carousel ID that you want to remotely connect', 'happy-addons-pro')
			]
		);
		
		$this->add_control(
			'ha_rcc_next_btn_text',
			[
				'label' => __( 'Next Button Text', 'happy-addons-pro' ),
				'label_block' => false,
				'type' => Controls_Manager::TEXT,
				'default' => 'Next',
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Next', 'happy-addons-pro' ),
				'render_type' => 'template',
			]
		);
		
		$this->add_control(
			'ha_rcc_prev_btn_text',
			[
				'label' => __( 'Prev Button Text', 'happy-addons-pro' ),
				'label_block' => false,
				'type' => Controls_Manager::TEXT,
				'default' => 'Prev',
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Prev', 'happy-addons-pro' ),
				'render_type' => 'template',
			]
		);
		
		$this->add_responsive_control(
			'rcc_alignment',
			[
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'flex-start',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container' => 'display:flex;justify-content: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'rcc_next_btn_icon',
			[
				'label' => __( 'Next Button Icon', 'happy-addons-pro' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'exclude_inline_options' => ['svg','gif'],
				'frontend_available' => true,
				'default' => [
					'value' => 'fa fa-chevron-right',
					'library' => 'fa-solid',
				],
			]
		);	

		$this->add_responsive_control(
			'rcc_next_icon_spacing',
			[
				'label' => esc_html__( 'Next Icon Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1500,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next .ha-rc-next-btn-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'rcc_prev_btn_icon',
			[
				'label' => __( 'Prev Button Icon', 'happy-addons-pro' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'exclude_inline_options' => ['svg','gif'],
				'frontend_available' => true,
				'default' => [
					'value' => 'fa fa-chevron-left',
					'library' => 'fa-solid',
				],
			]
		);	

		$this->add_responsive_control(
			'rcc_prev_icon_spacing',
			[
				'label' => esc_html__( 'Prev Icon Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1500,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev .ha-rc-prev-btn-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'rcc_show_next_prev_thumbnail',
			[
				'label' => __( 'Show Next/Prev Thumbnail', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_block' => false,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);	

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__remote_carousel_style_controls();
	}

	//define rcc style controll
	public function __remote_carousel_style_controls() {
		$this->start_controls_section(
			'_section_rcc_style',
			[
				'label' => __( 'Button', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'rcc_btn_fixed_size',
			[
				'label' => __( 'Fixed Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$this->start_popover();
		$this->add_responsive_control(
			'ha_rcc_btn_height',
			[
				'label' => __( 'Height', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'rcc_btn_fixed_size' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'ha_rcc_btn_width',
			[
				'label' => __( 'Width', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'default' => [
					'unit' => 'px',
					'size' => ''
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'rcc_btn_fixed_size' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'ha_rcc_btn_align_x',
			[
				'type' => Controls_Manager::CHOOSE,
				'label' => __( 'Horizontal Align', 'happy-addons-pro' ),
				'default' => 'center',
				'toggle' => false,
				'options' => [
					'left' => [
						'title' =>  __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' =>  __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' =>  __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next' => '{{VALUE}}',
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'left' => '-webkit-box-pack: start; -ms-flex-pack: start; justify-content: flex-start;',
					'center' => '-webkit-box-pack: center; -ms-flex-pack: center; justify-content: center;',
					'right' => '-webkit-box-pack: end; -ms-flex-pack: end; justify-content: flex-end;',
				],
				'condition' => [
					'rcc_btn_fixed_size' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'ha_rcc_btn_align_y',
			[
				'type' => Controls_Manager::CHOOSE,
				'label' => __( 'Vertical Align', 'happy-addons-pro' ),
				'default' => 'center',
				'toggle' => false,
				'options' => [
					'top' => [
						'title' =>  __( 'Top', 'happy-addons-pro' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' =>  __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' =>  __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next' => '{{VALUE}}',
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'top' => '-webkit-box-align: start; -ms-flex-align: start; align-items: flex-start;',
					'center' => '-webkit-box-align: center; -ms-flex-align: center; align-items: center;',
					'bottom' => '-webkit-box-align: end; -ms-flex-align: end; align-items: flex-end;',
				],
				'condition' => [
					'rcc_btn_fixed_size' => 'yes',
				],
			]
		);
		$this->end_popover();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'rcc_font_style',
				'selector' => '{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next, {{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'rcc_btn_text_shadow',
				'selector' => '{{WRAPPER}} .ha-remote-carousel-container .ha-custom-nav-remote-carousel',
			]
		);
		
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'rcc_btn_box_shadow',
				'selector' => '{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next, {{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev',
			]
		);

		$this->add_control(
			'rcc_btn_border',
			[
				'label' => __( 'Border', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'   => __( 'None', 'happy-addons-pro' ),
					'solid'  => __( 'Solid', 'happy-addons-pro' ),
					'dotted' => __( 'Dotted', 'happy-addons-pro' ),
					'dashed' => __( 'Dashed', 'happy-addons-pro' ),
					'groove' => __( 'Groove', 'happy-addons-pro' ),
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next' => 'border-style: {{VALUE}};',
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'rcc_btn_border_width',
			[
				'label' => __( 'Border Width', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
					'em' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '0',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [ 'rcc_btn_border' => ['solid','dotted','dashed','groove'] ],
			]
		);

		$this->add_control(
			'rcc_btn_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev' => 'border-color: {{VALUE}};',
				],
				'condition' => [ 'rcc_btn_border' => ['solid','dotted','dashed','groove'] ],
			]
		);

		$this->add_responsive_control(
			'rcc_btn_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' =>[
					'top' 		=> 5,
					'right' 	=> 5,
					'bottom' 	=> 5,
					'left' 		=> 5,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rcc_btn_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '15',
					'right' => '20',
					'bottom' => '15',
					'left' => '20',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rcc_btn_gap',
			[
				'label' => esc_html__( 'Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1500,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		//start next button offset
		$this->add_control(
			'rcc_next_btn_offset',
			[
				'label' => __( 'Next Button Offset', 'happy-addons-pro' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'prefix_class' => 'ha-rcc-next-btn-offset-'
			]
		);

		$this->start_popover();
		$this->add_responsive_control(
			'ha_rcc_next_btn_align_x',
			[
				'label' => __( 'Horizontal Align', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => -2500,
						'max' => 2500,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next' => 'right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'rcc_next_btn_offset' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'ha_rcc_next_btn_align_y',
			[
				'label' => __( 'Vertical Align', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'default' => [
					'unit' => 'px',
					'size' => 20
				],
				'range' => [
					'px' => [
						'min' => -2500,
						'max' => 2500,
						'step' => 1
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'rcc_next_btn_offset' => 'yes',
				]
			]
		);
		$this->end_popover(); //end next button offset
		
		//start prev button offset
		$this->add_control(
			'rcc_prev_btn_offset',
			[
				'label' => __( 'Prev Button Offset', 'happy-addons-pro' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'prefix_class' => 'ha-rcc-prev-btn-offset-'
			]
		);

		$this->start_popover();
		$this->add_responsive_control(
			'ha_rcc_prev_btn_align_x',
			[
				'label' => __( 'Horizontal Align', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'default' => [
					'unit' => 'px',
					'size' => 20
				],
				'range' => [
					'px' => [
						'min' => -2500,
						'max' => 2500,
						'step' => 1
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'rcc_prev_btn_offset' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'ha_rcc_prev_btn_align_y',
			[
				'label' => __( 'Vertical Align', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'default' => [
					'unit' => 'px',
					'size' => 20
				],
				'range' => [
					'px' => [
						'min' => -2500,
						'max' => 2500,
						'step' => 1
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'rcc_prev_btn_offset' => 'yes',
				]
			]
		);
		$this->end_popover(); //end prev button offset
		
		//btn z-index
		$this->add_control(
			'rcc_btn_zindex',
			[
				'label' => __( 'Prev/Next Button Z-Index', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [],
				'default' => [
					'size' => 99
				],
				'range' => [
					'px' => [
						'min' => -10,
						'max' => 999,
						'step' => 1
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next' => 'z-index: {{SIZE}};',
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev' => 'z-index: {{SIZE}};',
				],
			]
		);

		//start overlay control
		$this->add_control(
			'rcc_btn_overlay',
			[
				'label' => __( 'Overlay', 'happy-addons-pro' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'prefix_class' => 'ha-rcc-btn-overlay-',
				'condition' => [ 'rcc_show_next_prev_thumbnail' => 'yes' ],
			]
		);

		$this->start_popover();
		$this->add_control(
			'rcc_btn_overlay_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#1d1c1c',
				'selectors' => [
					'{{WRAPPER}} .ha-rcc-btn-overlay' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ha_rcc_overlay_opacity',
			[
				'label' => __( 'Opacity', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '0.5',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-rcc-btn-overlay' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'ha_rcc_overlay_zindex',
			[
				'label' => __( 'Z-Index', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '0',
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 9999,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-rcc-btn-overlay' => 'z-index: {{SIZE}};',
				],
			]
		);
		$this->end_popover();

		$this->add_control(
			'rcc_btn_bg_size',
			[
				'label' => __( 'Background Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'cover',
				'options' => [
					'auto' => __( 'Auto', 'happy-addons-pro' ),
					'contain'  => __( 'Contain', 'happy-addons-pro' ),
					'cover' => __( 'Cover', 'happy-addons-pro' ),
					'inherit' => __( 'Inherit', 'happy-addons-pro' ),
					'initial' => __( 'Initial', 'happy-addons-pro' ),
					'unset' => __( 'Unset', 'happy-addons-pro' ),
				],
				'selectors' => [
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next' => 'background-size: {{VALUE}};',
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev' => 'background-size: {{VALUE}};',
				],
				'condition' => [ 'rcc_show_next_prev_thumbnail' => 'yes' ],
			]
		);

		// start btn normal/hover style
		$this->start_controls_tabs( 'rcc_color_style' );
		$this->start_controls_tab(
			'rcc_tab_color_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'rcc_text_color_normal',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ha_rcc_btn_background',
				'selector' => '{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next, {{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev',
				'type' => ['classic', 'gradient'],
				'exclude' => ['image'],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'rcc_tab_color_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'rcc_text_color_hover',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ha_rcc_btn_background_hover',
				'selector' => '{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next:hover, {{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev:hover',
				'type' => ['classic', 'gradient'],
				'exclude' => ['image'],
			]
		);

		$this->add_control(
			'rcc_btn_border_color_hover',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next:hover' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [ 'rcc_btn_border' => ['solid','dotted','dashed','groove'] ],
			]
		);
		
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'rcc_btn_box_shadow_hover',
				'selector' => '{{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-next:hover, {{WRAPPER}} .ha-remote-carousel-container .ha-remote-carousel-btn-prev:hover',
			]
		);

		$this->add_control(
			'rcc_btn_hover_animation',
			[
				'label' => esc_html__( 'Animation', 'happy-addons-pro' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();		

		$this->end_controls_section();
	}

	//load/render preview
	protected function render() { 

		$settings = $this->get_settings_for_display();
		extract( $settings ); 

		$elementClass = '';
		if ( $settings['rcc_btn_hover_animation'] ) {
			$elementClass .= ' elementor-animation-' . $settings['rcc_btn_hover_animation'];
		}

        $harcc_uid = !empty($ha_rcc_unique_id) ? 'harccuid_' . $ha_rcc_unique_id : '';
	?>

		<div id="ha_remote_carousel" class="ha-remote-carousel-container" data-ha_rc_id="<?php echo esc_attr($harcc_uid); ?>" data-show_thumbnail="<?php echo $rcc_show_next_prev_thumbnail;?>">
			<button type="button" class="<?php echo $elementClass; ?> ha-custom-nav-remote-carousel ha-remote-carousel-btn-prev" data-ha_rc_nav="ha_rc_prev_btn"> 
                <span class="ha-rc-prev-btn-icon">
					<?php Icons_Manager::render_icon( $settings['rcc_prev_btn_icon'], ['aria-hidden' => 'true'] ); ?>
			 	</span>     
                <?php 
					echo !empty($ha_rcc_prev_btn_text) ? '<span class="ha-rc-prev-btn-text"> '. $ha_rcc_prev_btn_text .' </span>' : '';	
				?>
				<span class="ha-rcc-btn-overlay"></span>
            </button>
			<button type="button" class="<?php echo $elementClass; ?> ha-custom-nav-remote-carousel ha-remote-carousel-btn-next" data-ha_rc_nav="ha_rc_next_btn">
				<?php 
					echo !empty($ha_rcc_next_btn_text) ? '<span class="ha-rc-next-btn-text"> '. $ha_rcc_next_btn_text .' </span>' : '';	
				?>
                <span class="ha-rc-next-btn-icon">
					<?php Icons_Manager::render_icon( $settings['rcc_next_btn_icon'], ['aria-hidden' => 'true'] ); ?>
				</span> 
				<span class="ha-rcc-btn-overlay"></span>
            </button>
		</div>

<?php 
	}

}