<?php
	
	namespace ElementPack\Modules\LogoGrid\Widgets;
	
	use ElementPack\Base\Module_Base;
	use Elementor\Group_Control_Css_Filter;
	use Elementor\Repeater;
	use Elementor\Controls_Manager;
	use Elementor\Group_Control_Box_Shadow;
	use Elementor\Group_Control_Image_Size;
	use Elementor\Group_Control_Typography;
	use Elementor\Group_Control_Background;
	use Elementor\Group_Control_Border;
	use Elementor\Utils;
	
	use ElementPack\Traits\Global_Mask_Controls;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	} // Exit if accessed directly
	
	class Logo_Grid extends Module_Base {
		
		use Global_Mask_Controls;
		
		public function get_name() {
			return 'bdt-logo-grid';
		}
		
		public function get_title() {
			return BDTEP . esc_html__( 'Logo Grid', 'bdthemes-element-pack' );
		}
		
		public function get_icon() {
			return 'bdt-wi-logo-grid';
		}
		
		public function get_categories() {
			return [ 'element-pack' ];
		}
		
		public function get_keywords() {
			return [ 'logo', 'grid', 'client', 'brand', 'showcase' ];
		}
		
		public function get_style_depends() {
			if ( $this->ep_is_edit_mode() ) {
				return [ 'ep-styles' ];
			} else {
				return [ 'ep-logo-grid', 'tippy' ];
			}
		}
		
		public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['popper', 'tippyjs', 'ep-scripts'];
        } else {
			return [ 'popper', 'tippyjs', 'ep-logo-grid' ];
        }
		}
		
		public function get_custom_help_url() {
			return 'https://youtu.be/Go1YE3O23J4';
		}
		
		protected function register_controls() {
			
			$this->start_controls_section(
				'ep_section_logo',
				[
					'label' => __( 'Logo Grid Items', 'bdthemes-element-pack' ),
					'tab'   => Controls_Manager::TAB_CONTENT,
				]
			);
			
			$repeater = new Repeater();
			
			$repeater->add_control(
				'image',
				[
					'label'   => __( 'Logo Image', 'bdthemes-element-pack' ),
					'type'    => Controls_Manager::MEDIA,
					'default' => [
						'url' => Utils::get_placeholder_image_src(),
					],
				]
			);
			
			$repeater->add_control(
				'link',
				[
					'label'         => __( 'Website Url', 'bdthemes-element-pack' ),
					'type'          => Controls_Manager::URL,
				]
			);
			
			$repeater->add_control(
				'name',
				[
					'label'   => __( 'Brand Name', 'bdthemes-element-pack' ),
					'type'    => Controls_Manager::TEXT,
					'default' => __( 'Brand Name', 'bdthemes-element-pack' ),
				]
			);
			
			$repeater->add_control(
				'description',
				[
					'label'   => __( 'Description', 'bdthemes-element-pack' ),
					'type'    => Controls_Manager::TEXTAREA,
					'default' => __( 'Brand Short Description Type Here.', 'bdthemes-element-pack' ),
				]
			);
			
			$repeater->add_control(
				'logo_tooltip',
				[
					'label'              => __( 'Tooltip', 'bdthemes-element-pack' ),
					'type'               => Controls_Manager::SWITCHER,
					'render_type'        => 'template',
					'frontend_available' => true,
				]
			);
			
			$repeater->add_control(
				'logo_tooltip_placement',
				[
					'label'     => esc_html__( 'Placement', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'top',
					'options'   => [
						'top-start'    => esc_html__( 'Top Left', 'bdthemes-element-pack' ),
						'top'          => esc_html__( 'Top', 'bdthemes-element-pack' ),
						'top-end'      => esc_html__( 'Top Right', 'bdthemes-element-pack' ),
						'bottom-start' => esc_html__( 'Bottom Left', 'bdthemes-element-pack' ),
						'bottom'       => esc_html__( 'Bottom', 'bdthemes-element-pack' ),
						'bottom-end'   => esc_html__( 'Bottom Right', 'bdthemes-element-pack' ),
						'left'         => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'right'        => esc_html__( 'Right', 'bdthemes-element-pack' ),
					],
					'condition' => [
						'logo_tooltip' => 'yes',
					],
				]
			);
			
			$this->add_control(
				'logo_list',
				[
					'show_label'  => false,
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'title_field' => '{{{ name }}}',
					'default'     => [
						[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
						[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
						[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
						[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
						[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
						[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
						[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
						[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
					]
				]
			);
			
			$this->end_controls_section();
			
			$this->start_controls_section(
				'ep_section_layout',
				[
					'label' => __( 'Additional Settings', 'bdthemes-element-pack' ),
					'tab'   => Controls_Manager::TAB_CONTENT,
				]
			);
			
			$this->add_control(
				'layout',
				[
					'label'          => __( 'Grid Layout', 'bdthemes-element-pack' ),
					'type'           => Controls_Manager::SELECT,
					'options'        => [
						'box'       => __( 'Box', 'bdthemes-element-pack' ),
						'border'    => __( 'Border', 'bdthemes-element-pack' ),
						'tictactoe' => __( 'Plus', 'bdthemes-element-pack' ),
					],
					'default'        => 'box',
					'prefix_class'   => 'bdt-logo-grid--',
					'style_transfer' => true,
					'render_type'    => 'template',
				]
			);
			
			$this->add_responsive_control(
				'columns',
				[
					'label'           => __( 'Columns', 'bdthemes-element-pack' ),
					'type'            => Controls_Manager::SELECT,
					'options'         => [
						1 => '1',
						2 => '2',
						3 => '3',
						4 => '4',
						5 => '5',
						6 => '6',
					],
					'desktop_default' => 4,
					'tablet_default'  => 2,
					'mobile_default'  => 2,
					'prefix_class'    => 'bdt-lg-col-%s',
					'style_transfer'  => true,
					'render_type'     => 'template',
					'selectors' => [
						'{{WRAPPER}} .bdt-logo-grid-wrapper' => 'grid-template-columns: repeat({{SIZE}}, 1fr); display: grid;',
					],
				]
			);

			$this->add_responsive_control(
				'column_gap',
				[
					'label'     => esc_html__( 'Grid Gap', 'bdthemes-element-pack' ) . BDTEP_NC,
					'type'      => Controls_Manager::SLIDER,
					'default'   => [
						'size' => 15,
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-logo-grid-wrapper' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'layout' => 'box'
					]
				]
			);
			
			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name'      => 'thumbnail',
					'default'   => 'large',
					'separator' => 'before',
					'exclude'   => [
						'custom'
					]
				]
			);
			
			$this->add_control(
				'image_mask_popover',
				[
					'label'        => esc_html__( 'Image Mask', 'bdthemes-element-pack' ) . BDTEP_NC,
					'type'         => Controls_Manager::POPOVER_TOGGLE,
					'render_type'  => 'template',
					'return_value' => 'yes',
				]
			);
			
			//Global Image Mask Controls
			$this->register_image_mask_controls();
			
			$this->add_responsive_control(
				'height',
				[
					'label'      => __( 'Item Height', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range'      => [
						'px' => [
							'max' => 500,
							'min' => 100,
						]
					],
					'selectors'  => [
						'{{WRAPPER}} .bdt-item' => 'height: {{SIZE}}{{UNIT}};'
					],
				]
			);
			
			$this->add_control(
				'grid_animation_type',
				[
					'label'     => esc_html__( 'Grid Entrance Animation', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '',
					'options'   => element_pack_transition_options(),
					'separator' => 'before',
				]
			);
			
			$this->add_control(
				'grid_anim_delay',
				[
					'label'      => esc_html__( 'Animation delay', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'ms', '' ],
					'range'      => [
						'ms' => [
							'min'  => 0,
							'max'  => 1000,
							'step' => 5,
						],
					],
					'default'    => [
						'unit' => 'ms',
						'size' => 300,
					],
					'condition'  => [
						'grid_animation_type!' => '',
					],
				]
			);
			
			$this->add_control(
				'logo_size_cover',
				[
					'label'   => esc_html__( 'Enable Logo Size Cover ', 'bdthemes-element-pack' ) . BDTEP_NC,
					'type'    => Controls_Manager::SWITCHER,
					'prefix_class' => 'ep-logo-grid-cover-',
					'separator' => 'before',
				]
			);
			
			$this->end_controls_section();
			
			$this->start_controls_section(
				'section_tooltip_settings',
				[
					'label' => __( 'Tooltip Settings', 'bdthemes-element-pack' ),
				]
			);
			
			$this->add_control(
				'logo_tooltip_animation',
				[
					'label'   => esc_html__( 'Animation', 'bdthemes-element-pack' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'shift-toward',
					'options' => [
						'shift-away'   => esc_html__( 'Shift-Away', 'bdthemes-element-pack' ),
						'shift-toward' => esc_html__( 'Shift-Toward', 'bdthemes-element-pack' ),
						'fade'         => esc_html__( 'Fade', 'bdthemes-element-pack' ),
						'scale'        => esc_html__( 'Scale', 'bdthemes-element-pack' ),
						'perspective'  => esc_html__( 'Perspective', 'bdthemes-element-pack' ),
					],
				]
			);
			
			$this->add_control(
				'logo_tooltip_x_offset',
				[
					'label'   => esc_html__( 'X Offset', 'bdthemes-element-pack' ),
					'type'    => Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
				]
			);
			
			$this->add_control(
				'logo_tooltip_y_offset',
				[
					'label'   => esc_html__( 'Y Offset', 'bdthemes-element-pack' ),
					'type'    => Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
				]
			);
			
			$this->add_control(
				'logo_tooltip_arrow',
				[
					'label' => esc_html__( 'Arrow', 'bdthemes-element-pack' ),
					'type'  => Controls_Manager::SWITCHER,
				]
			);
			
			$this->add_control(
				'logo_tooltip_trigger',
				[
					'label'       => __( 'Trigger on Click', 'bdthemes-element-pack' ),
					'description' => __( 'Don\'t set yes when you set lightbox image with marker.', 'bdthemes-element-pack' ),
					'type'        => Controls_Manager::SWITCHER,
				]
			);
			
			$this->end_controls_section();
			
			//Style
			$this->start_controls_section(
				'ep_section_style_grid',
				[
					'label' => __( 'Logo Grid', 'bdthemes-element-pack' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
			);
			
			$this->start_controls_tabs( 'ep_image_effects' );
			
			$this->start_controls_tab(
				'ep_tab_image_effects_normal',
				[
					'label' => __( 'Normal', 'bdthemes-element-pack' ),
				]
			);
			
			$this->add_control(
				'grid_bg_color',
				[
					'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-logo-grid-figure' => 'background-color: {{VALUE}};',
					],
				]
			);
			
			$this->add_control(
				'grid_border_type',
				[
					'label'     => __( 'Border Type', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => [
						'none'   => __( 'None', 'bdthemes-element-pack' ),
						'solid'  => __( 'Solid', 'bdthemes-element-pack' ),
						'double' => __( 'Double', 'bdthemes-element-pack' ),
						'dotted' => __( 'Dotted', 'bdthemes-element-pack' ),
						'dashed' => __( 'Dashed', 'bdthemes-element-pack' ),
						'groove' => __( 'Groove', 'bdthemes-element-pack' ),
					],
					'default'   => 'solid',
					'selectors' => [
						'{{WRAPPER}} .bdt-item' => 'border-style: {{VALUE}};',
					],
				]
			);
			
			$this->add_responsive_control(
				'grid_border_width',
				[
					'label'          => __( 'Border Width', 'bdthemes-element-pack' ),
					'type'           => Controls_Manager::SLIDER,
					'default'        => [
						'size' => 2,
					],
					'tablet_default' => [
						'size' => 2,
					],
					'mobile_default' => [
						'size' => 2,
					],
					'range'          => [
						'px' => [
							'min' => 0,
							'max' => 50,
						],
					],
					'selectors'      => [
						'{{WRAPPER}}' => '--ep-grid-border-width: {{SIZE}}{{UNIT}};',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border .bdt-item' => 'border-right-width: var(--ep-grid-border-width, 2px); border-bottom-width: var(--ep-grid-border-width, 2px);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border .bdt-item'  => 'border-right-width: var(--ep-grid-border-width, 2px); border-bottom-width: var(--ep-grid-border-width, 2px);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border .bdt-item'  => 'border-right-width: var(--ep-grid-border-width, 2px); border-bottom-width: var(--ep-grid-border-width, 2px);',
						
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-2 .bdt-item:nth-child(2n+1)' => 'border-left-width: var(--ep-grid-border-width, 2px);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-3 .bdt-item:nth-child(3n+1)' => 'border-left-width: var(--ep-grid-border-width, 2px);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-4 .bdt-item:nth-child(4n+1)' => 'border-left-width: var(--ep-grid-border-width, 2px);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-5 .bdt-item:nth-child(5n+1)' => 'border-left-width: var(--ep-grid-border-width, 2px);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-6 .bdt-item:nth-child(6n+1)' => 'border-left-width: var(--ep-grid-border-width, 2px);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-2 .bdt-item:nth-child(-n+2)' => 'border-top-width: var(--ep-grid-border-width, 2px);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-3 .bdt-item:nth-child(-n+3)' => 'border-top-width: var(--ep-grid-border-width, 2px);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-4 .bdt-item:nth-child(-n+4)' => 'border-top-width: var(--ep-grid-border-width, 2px);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-5 .bdt-item:nth-child(-n+5)' => 'border-top-width: var(--ep-grid-border-width, 2px);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-6 .bdt-item:nth-child(-n+6)' => 'border-top-width: var(--ep-grid-border-width, 2px);',
						
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet2 .bdt-item:nth-child(2n+1)' => 'border-left-width: var(--ep-grid-border-width, 2px);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet3 .bdt-item:nth-child(3n+1)' => 'border-left-width: var(--ep-grid-border-width, 2px);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet4 .bdt-item:nth-child(4n+1)' => 'border-left-width: var(--ep-grid-border-width, 2px);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet5 .bdt-item:nth-child(5n+1)' => 'border-left-width: var(--ep-grid-border-width, 2px);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet6 .bdt-item:nth-child(6n+1)' => 'border-left-width: var(--ep-grid-border-width, 2px);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet2 .bdt-item:nth-child(-n+2)' => 'border-top-width: var(--ep-grid-border-width, 2px);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet3 .bdt-item:nth-child(-n+3)' => 'border-top-width: var(--ep-grid-border-width, 2px);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet4 .bdt-item:nth-child(-n+4)' => 'border-top-width: var(--ep-grid-border-width, 2px);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet5 .bdt-item:nth-child(-n+5)' => 'border-top-width: var(--ep-grid-border-width, 2px);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet6 .bdt-item:nth-child(-n+6)' => 'border-top-width: var(--ep-grid-border-width, 2px);',
						
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile2 .bdt-item:nth-child(2n+1)' => 'border-left-width: var(--ep-grid-border-width, 2px);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile3 .bdt-item:nth-child(3n+1)' => 'border-left-width: var(--ep-grid-border-width, 2px);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile4 .bdt-item:nth-child(4n+1)' => 'border-left-width: var(--ep-grid-border-width, 2px);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile5 .bdt-item:nth-child(5n+1)' => 'border-left-width: var(--ep-grid-border-width, 2px);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile6 .bdt-item:nth-child(6n+1)' => 'border-left-width: var(--ep-grid-border-width, 2px);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile2 .bdt-item:nth-child(-n+2)' => 'border-top-width: var(--ep-grid-border-width, 2px);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile3 .bdt-item:nth-child(-n+3)' => 'border-top-width: var(--ep-grid-border-width, 2px);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile4 .bdt-item:nth-child(-n+4)' => 'border-top-width: var(--ep-grid-border-width, 2px);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile5 .bdt-item:nth-child(-n+5)' => 'border-top-width: var(--ep-grid-border-width, 2px);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile6 .bdt-item:nth-child(-n+6)' => 'border-top-width: var(--ep-grid-border-width, 2px);',
						
						'{{WRAPPER}}.bdt-logo-grid--tictactoe .bdt-item' => 'border-top-width: {{SIZE}}{{UNIT}}; border-right-width: {{SIZE}}{{UNIT}};',
						
						'{{WRAPPER}}.bdt-logo-grid--box .bdt-item' => 'border-width: {{SIZE}}{{UNIT}};',
					],
					'condition'      => [
						'grid_border_type!' => 'none',
					]
				]
			);
			
			$this->add_control(
				'grid_border_color',
				[
					'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-item' => 'border-color: {{VALUE}};',
					],
					'condition' => [
						'grid_border_type!' => 'none',
					]
				]
			);
			
			$this->add_responsive_control(
				'grid_border_radius',
				[
					'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}}' => '--ep-grid-border-radius-left: {{LEFT}}{{UNIT}}; --ep-grid-border-radius-right: {{RIGHT}}{{UNIT}};',
						'{{WRAPPER}}.bdt-logo-grid--border .bdt-logo-grid-wrapper, {{WRAPPER}}.bdt-logo-grid--box .bdt-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}}.bdt-logo-grid--border .bdt-item:first-child'                                            => 'border-top-left-radius: {{TOP}}{{UNIT}};',
						'{{WRAPPER}}.bdt-logo-grid--border .bdt-item:last-child'                                             => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}};',
						
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-2 .bdt-item:nth-child(2)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-2 .bdt-item:nth-last-child(2)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-3 .bdt-item:nth-child(3)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-3 .bdt-item:nth-last-child(3)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-4 .bdt-item:nth-child(4)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-4 .bdt-item:nth-last-child(4)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-5 .bdt-item:nth-child(5)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-5 .bdt-item:nth-last-child(5)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-6 .bdt-item:nth-child(6)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col-6 .bdt-item:nth-last-child(6)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet2 .bdt-item:nth-child(2)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet2 .bdt-item:nth-last-child(2)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet3 .bdt-item:nth-child(3)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet3 .bdt-item:nth-last-child(3)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet4 .bdt-item:nth-child(4)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet4 .bdt-item:nth-last-child(4)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet5 .bdt-item:nth-child(5)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet5 .bdt-item:nth-last-child(5)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet6 .bdt-item:nth-child(6)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--tablet6 .bdt-item:nth-last-child(6)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile2 .bdt-item:nth-child(2)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile2 .bdt-item:nth-last-child(2)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile3 .bdt-item:nth-child(3)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile3 .bdt-item:nth-last-child(3)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile4 .bdt-item:nth-child(4)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile4 .bdt-item:nth-last-child(4)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile5 .bdt-item:nth-child(5)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile5 .bdt-item:nth-last-child(5)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile6 .bdt-item:nth-child(6)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-lg-col--mobile6 .bdt-item:nth-last-child(6)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						
						// Tictactoe
						'{{WRAPPER}}.bdt-logo-grid--tictactoe .bdt-logo-grid-wrapper'                                                 => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}}.bdt-logo-grid--tictactoe .bdt-item:first-child'                                        => 'border-top-left-radius: {{TOP}}{{UNIT}};',
						'{{WRAPPER}}.bdt-logo-grid--tictactoe .bdt-item:last-child'                                         => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}};',
						
						'(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col-2 .bdt-item:nth-child(2)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col-2 .bdt-item:nth-last-child(2)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col-3 .bdt-item:nth-child(3)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col-3 .bdt-item:nth-last-child(3)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col-4 .bdt-item:nth-child(4)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col-4 .bdt-item:nth-last-child(4)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col-5 .bdt-item:nth-child(5)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col-5 .bdt-item:nth-last-child(5)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col-6 .bdt-item:nth-child(6)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col-6 .bdt-item:nth-last-child(6)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						
						'(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--tablet2 .bdt-item:nth-child(2)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--tablet2 .bdt-item:nth-last-child(2)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--tablet3 .bdt-item:nth-child(3)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--tablet3 .bdt-item:nth-last-child(3)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--tablet4 .bdt-item:nth-child(4)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--tablet4 .bdt-item:nth-last-child(4)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--tablet5 .bdt-item:nth-child(5)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--tablet5 .bdt-item:nth-last-child(5)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--tablet6 .bdt-item:nth-child(6)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--tablet6 .bdt-item:nth-last-child(6)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						
						'(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--mobile2 .bdt-item:nth-child(2)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--mobile2 .bdt-item:nth-last-child(2)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--mobile3 .bdt-item:nth-child(3)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--mobile3 .bdt-item:nth-last-child(3)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--mobile4 .bdt-item:nth-child(4)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--mobile4 .bdt-item:nth-last-child(4)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--mobile5 .bdt-item:nth-child(5)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--mobile5 .bdt-item:nth-last-child(5)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--mobile6 .bdt-item:nth-child(6)'      => 'border-top-right-radius: var(--ep-grid-border-radius-right, 0);',
						'(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-lg-col--mobile6 .bdt-item:nth-last-child(6)' => 'border-bottom-left-radius: var(--ep-grid-border-radius-left, 0);',
					],
				]
			);
			
			$this->add_responsive_control(
				'padding',
				[
					'label'      => __( 'Padding', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .bdt-logo-grid-figure' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'grid_box_shadow',
					'exclude'  => [
						'box_shadow_position',
					],
					'selector' => '{{WRAPPER}}.bdt-logo-grid--tictactoe .bdt-logo-grid-wrapper, {{WRAPPER}}.bdt-logo-grid--border .bdt-logo-grid-wrapper, {{WRAPPER}}.bdt-logo-grid--box .bdt-item'
				]
			);
			
			$this->add_control(
				'image_opacity',
				[
					'label'     => __( 'Opacity', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'max'  => 1,
							'min'  => 0.10,
							'step' => 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-logo-grid-figure img' => 'opacity: {{SIZE}};',
					],
				]
			);
			
			$this->add_group_control(
				Group_Control_Css_Filter::get_type(),
				[
					'name'     => 'image_css_filters',
					'selector' => '{{WRAPPER}} .bdt-logo-grid-figure img',
				]
			);

			$this->add_responsive_control(
				'image_size',
				[
					'label'      => __( 'Image Size', 'bdthemes-element-pack' ) . BDTEP_NC,
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range'      => [
						'px' => [
							'min' => 10,
							'max' => 500,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors'  => [
						'{{WRAPPER}} .bdt-logo-grid-img' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; object-fit: contain;'
					],
				]
			);
			
			$this->end_controls_tab();
			
			$this->start_controls_tab(
				'hover',
				[
					'label' => __( 'Hover', 'bdthemes-element-pack' ),
				]
			);
			
			$this->add_control(
				'grid_bg_hover_color',
				[
					'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-item:hover .bdt-logo-grid-figure' => 'background-color: {{VALUE}};',
					],
				]
			);
			
			$this->add_control(
				'grid_border_hover_color',
				[
					'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-item:hover' => 'border-color: {{VALUE}};',
					],
					'condition' => [
						'grid_border_type!' => 'none',
						'layout'            => 'box',
					]
				]
			);
			
			$this->add_control(
				'image_opacity_hover',
				[
					'label'     => __( 'Opacity', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'max'  => 1,
							'min'  => 0.10,
							'step' => 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-logo-grid-figure:hover img' => 'opacity: {{SIZE}};',
					],
				]
			);
			
			$this->add_group_control(
				Group_Control_Css_Filter::get_type(),
				[
					'name'     => 'image_css_filters_hover',
					'selector' => '{{WRAPPER}} .bdt-logo-grid-figure:hover img',
				]
			);
			
			$this->add_control(
				'image_bg_hover_transition',
				[
					'label'     => __( 'Transition Duration', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'max'  => 3,
							'step' => 0.1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-logo-grid-figure:hover img' => 'transition-duration: {{SIZE}}s;',
					],
				]
			);
			
			$this->add_control(
				'hover_animation',
				[
					'label' => __( 'Hover Animation', 'bdthemes-element-pack' ),
					'type'  => Controls_Manager::HOVER_ANIMATION,
				]
			);
			
			$this->end_controls_tab();
			
			$this->end_controls_tabs();
			
			$this->end_controls_section();
			
			$this->start_controls_section(
				'section_style_tooltip',
				[
					'label' => esc_html__( 'Tooltip', 'bdthemes-element-pack' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
			);
			
			$this->add_responsive_control(
				'logo_tooltip_width',
				[
					'label'       => esc_html__( 'Width', 'bdthemes-element-pack' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => [
						'px',
						'em',
					],
					'range'       => [
						'px' => [
							'min' => 50,
							'max' => 500,
						],
					],
					'selectors'   => [
						'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'max-width: calc({{SIZE}}{{UNIT}} - 10px) !important;',
					],
					'render_type' => 'template',
				]
			);
			
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'logo_tooltip_typography',
					'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
				]
			);
			
			$this->add_control(
				'logo_tooltip_title_color',
				[
					'label'     => esc_html__( 'Title Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .bdt-title' => 'color: {{VALUE}}',
					],
				]
			);
			
			$this->add_control(
				'logo_tooltip_color',
				[
					'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'color: {{VALUE}}',
					],
				]
			);
			
			$this->add_control(
				'logo_tooltip_text_align',
				[
					'label'     => esc_html__( 'Text Alignment', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::CHOOSE,
					'default'   => 'center',
					'options'   => [
						'left'   => [
							'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
							'icon'  => 'eicon-text-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
							'icon'  => 'eicon-text-align-center',
						],
						'right'  => [
							'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
							'icon'  => 'eicon-text-align-right',
						],
					],
					'selectors' => [
						'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'text-align: {{VALUE}};',
					],
				]
			);
			
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'logo_tooltip_background',
					'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"], .tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-backdrop',
				]
			);
			
			$this->add_control(
				'logo_tooltip_arrow_color',
				[
					'label'     => esc_html__( 'Arrow Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'color: {{VALUE}}',
					],
				]
			);
			
			$this->add_responsive_control(
				'logo_tooltip_padding',
				[
					'label'       => __( 'Padding', 'bdthemes-element-pack' ),
					'type'        => Controls_Manager::DIMENSIONS,
					'size_units'  => [ 'px', '%' ],
					'selectors'   => [
						'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'render_type' => 'template',
				]
			);
			
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'        => 'logo_tooltip_border',
					'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
					'placeholder' => '1px',
					'default'     => '1px',
					'selector'    => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
				]
			);
			
			$this->add_responsive_control(
				'logo_tooltip_border_radius',
				[
					'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'logo_tooltip_box_shadow',
					'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
				]
			);
			
			$this->end_controls_section();
			
		}
		
		protected function render() {
			$settings = $this->get_settings_for_display();
			
			$this->add_render_attribute( 'logo-grid', 'class', 'bdt-logo-grid-wrapper' );
			
			if ( $settings['grid_animation_type'] !== '' ) {
				$this->add_render_attribute( 'logo-grid', 'bdt-scrollspy', 'cls: bdt-animation-' . esc_attr( $settings['grid_animation_type'] ) . ';' );
				$this->add_render_attribute( 'logo-grid', 'bdt-scrollspy', 'delay: ' . esc_attr( $settings['grid_anim_delay']['size'] ) . ';' );
				$this->add_render_attribute( 'logo-grid', 'bdt-scrollspy', 'target: > .bdt-item' . ';' );
			}
			
			if ( empty( $settings['logo_list'] ) ) {
				return;
			}
			
			?>

        <div <?php $this->print_render_attribute_string( 'logo-grid' ); ?>>
			<?php
			foreach ( $settings['logo_list'] as $index => $item ) :
				$image = wp_get_attachment_image_url( $item['image']['id'], $settings['thumbnail_size'] );
				$repeater_key    = 'grid_item' . $index;
				$tag             = 'div ';
				$tooltip_content = '<span class="bdt-title">' . $item['name'] . '</span>' . $item['description'];
				$image_alt       = $item['name'] . ' : ' . $item['description'];
				$this->add_render_attribute( $repeater_key, 'class', 'bdt-item' );
				$this->add_render_attribute( $repeater_key, 'data-tippy-content', $tooltip_content, true );
				
				if ( $item['link']['url'] ) {
					$tag = 'a ';
					$this->add_render_attribute( $repeater_key, 'class', 'bdt-logo-grid-link' );
					$this->add_link_attributes( $repeater_key, $item['link'] );
				}
				
				if ( $item['name'] and $item['description'] and $item['logo_tooltip'] ) {
					// Tooltip settings
					$this->add_render_attribute( $repeater_key, 'class', 'bdt-tippy-tooltip' );
					$this->add_render_attribute( $repeater_key, 'data-tippy', '', true );
					
					if ( $item['logo_tooltip_placement'] ) {
						$this->add_render_attribute( $repeater_key, 'data-tippy-placement', $item['logo_tooltip_placement'], true );
					}
					
					if ( $settings['logo_tooltip_animation'] ) {
						$this->add_render_attribute( $repeater_key, 'data-tippy-animation', $settings['logo_tooltip_animation'], true );
					}
					
					if ( $settings['logo_tooltip_x_offset']['size'] or $settings['logo_tooltip_y_offset']['size'] ) {
						$this->add_render_attribute( $repeater_key, 'data-tippy-offset', '[' . $settings['logo_tooltip_x_offset']['size'] . ',' . $settings['logo_tooltip_y_offset']['size'] . ']', true );
					} 
					
					if ( 'yes' == $settings['logo_tooltip_arrow'] ) {
						$this->add_render_attribute( $repeater_key, 'data-tippy-arrow', 'true', true );
					} else {
						$this->add_render_attribute( $repeater_key, 'data-tippy-arrow', 'false', true );
					}
					
					if ( 'yes' == $settings['logo_tooltip_trigger'] ) {
						$this->add_render_attribute( $repeater_key, 'data-tippy-trigger', 'click', true );
					}
					
				}

				$image_mask = $settings['image_mask_popover'] == 'yes' ? ' bdt-image-mask' : '';
				$this->add_render_attribute('image-wrap', 'class', 'bdt-logo-grid-figure' . $image_mask);
				
				?>
                <<?php echo $tag . ' '; ?> <?php $this->print_render_attribute_string( $repeater_key ); ?>>
                <figure <?php echo $this->get_render_attribute_string('image-wrap'); ?>>
					<?php if ( $image ) :
						
						
						echo wp_get_attachment_image(
							$item['image']['id'],
							$settings['thumbnail_size'],
							false,
							[
								'class' => 'bdt-logo-grid-img elementor-animation-' . esc_attr( $settings['hover_animation'] ),
								'alt'   => esc_attr( $image_alt ),
							]
						);
					
					else :
						printf(
							'<img class="bdt-logo-grid-img elementor-animation-%s" src="%s" alt="%s">',
							esc_attr( $settings['hover_animation'] ),
							Utils::get_placeholder_image_src(),
							esc_attr( $image_alt )
						);
					endif; ?>

                </figure>
                </<?php echo $tag; ?>>
			<?php endforeach; ?>
            </div>
			
			<?php
		}
	}
