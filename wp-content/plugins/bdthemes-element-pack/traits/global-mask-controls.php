<?php
	
	namespace ElementPack\Traits;
	
	use Elementor\Controls_Manager;
	
	defined( 'ABSPATH' ) || die();
	
	trait Global_Mask_Controls {
		
		protected function register_image_mask_controls() {
		    $this->start_popover();

		    $this->add_control(
		        'image_mask_shape',
		        [
		            'label'     => esc_html__('Masking Shape', 'bdthemes-element-pack'),
		            'title'     => esc_html__('Masking Shape', 'bdthemes-element-pack'),
		            'type'      => Controls_Manager::CHOOSE,
		            'default'   => 'default',
		            'options'   => [
		                'default' => [
		                    'title' => esc_html__('Default Shapes', 'bdthemes-element-pack'),
		                    'icon'  => 'eicon-star',
		                ],
		                'custom'  => [
		                    'title' => esc_html__('Custom Shape', 'bdthemes-element-pack'),
		                    'icon'  => 'eicon-image-bold',
		                ],
		            ],
		            'toggle'    => false,
		            'condition' => [
		                'image_mask_popover' => 'yes',
		            ],
		        ]
		    );

		    $this->add_control(
		        'image_mask_shape_default',
		        [
		            'label'          => _x('Default', 'Mask Image', 'bdthemes-element-pack'),
		            'label_block'    => true,
		            'show_label'     => false,
		            'type'           => Controls_Manager::SELECT,
		            'default'        => 'shape-1',
		            'options'        => element_pack_mask_shapes(),
		            'selectors'      => [
		                '{{WRAPPER}} .bdt-image-mask>*' => '-webkit-mask-image: url('.BDTEP_ASSETS_URL . 'images/mask/'.'{{VALUE}}.svg); mask-image: url('.BDTEP_ASSETS_URL . 'images/mask/'.'{{VALUE}}.svg);',
		                '{{WRAPPER}} .bdt-image-mask:before' => 'background-image: url('.BDTEP_ASSETS_URL . 'images/mask/color-'.'{{VALUE}}.svg);',
		            ],
		            'condition'      => [
		                'image_mask_popover' => 'yes',
		                'image_mask_shape'   => 'default',
		            ],
		            'style_transfer' => true,
		        ]
		    );

		    $this->add_control(
		        'image_mask_shape_custom',
		        [
		            'label'      => _x('Custom Shape', 'Mask Image', 'bdthemes-element-pack'),
		            'type'       => Controls_Manager::MEDIA,
		            'show_label' => false,
		            'selectors'  => [
		                '{{WRAPPER}} .bdt-image-mask>*' => '-webkit-mask-image: url({{URL}}); mask-image: url({{URL}});',
		            ],
		            'condition'  => [
		                'image_mask_popover' => 'yes',
		                'image_mask_shape'   => 'custom',
		            ],
		        ]
		    );

		    $this->add_control(
		        'image_mask_shape_position',
		        [
		            'label'                => esc_html__('Position', 'bdthemes-element-pack'),
		            'type'                 => Controls_Manager::SELECT,
		            'default'              => 'center-center',
		            'options'              => [
		                'center-center' => esc_html__('Center Center', 'bdthemes-element-pack'),
		                'center-left'   => esc_html__('Center Left', 'bdthemes-element-pack'),
		                'center-right'  => esc_html__('Center Right', 'bdthemes-element-pack'),
		                'top-center'    => esc_html__('Top Center', 'bdthemes-element-pack'),
		                'top-left'      => esc_html__('Top Left', 'bdthemes-element-pack'),
		                'top-right'     => esc_html__('Top Right', 'bdthemes-element-pack'),
		                'bottom-center' => esc_html__('Bottom Center', 'bdthemes-element-pack'),
		                'bottom-left'   => esc_html__('Bottom Left', 'bdthemes-element-pack'),
		                'bottom-right'  => esc_html__('Bottom Right', 'bdthemes-element-pack'),
		            ],
		            'selectors_dictionary' => [
		                'center-center' => 'center center',
		                'center-left'   => 'center left',
		                'center-right'  => 'center right',
		                'top-center'    => 'top center',
		                'top-left'      => 'top left',
		                'top-right'     => 'top right',
		                'bottom-center' => 'bottom center',
		                'bottom-left'   => 'bottom left',
		                'bottom-right'  => 'bottom right',
		            ],
		            'selectors'            => [
		                '{{WRAPPER}} .bdt-image-mask>*' => '-webkit-mask-position: {{VALUE}}; mask-position: {{VALUE}};',
		            ],
		            'condition'            => [
		                'image_mask_popover' => 'yes',
		            ],
		        ]
		    );

		    $this->add_control(
		        'image_mask_shape_size',
		        [
		            'label'     => esc_html__('Size', 'bdthemes-element-pack'),
		            'type'      => Controls_Manager::SELECT,
		            'default'   => 'contain',
		            'options'   => [
		                'auto'    => esc_html__('Auto', 'bdthemes-element-pack'),
		                'cover'   => esc_html__('Cover', 'bdthemes-element-pack'),
		                'contain' => esc_html__('Contain', 'bdthemes-element-pack'),
		                'initial' => esc_html__('Custom', 'bdthemes-element-pack'),
		            ],
		            'selectors' => [
		                '{{WRAPPER}} .bdt-image-mask>*' => '-webkit-mask-size: {{VALUE}}; mask-size: {{VALUE}};',
		            ],
		            'condition' => [
		                'image_mask_popover' => 'yes',
		            ],
		        ]
		    );

		    $this->add_control(
		        'image_mask_shape_custom_size',
		        [
		            'label'      => _x('Custom Size', 'Mask Image', 'bdthemes-element-pack'),
		            'type'       => Controls_Manager::SLIDER,
		            'responsive' => true,
		            'size_units' => ['px', 'em', '%', 'vw'],
		            'range'      => [
		                'px' => [
		                    'min' => 0,
		                    'max' => 1000,
		                ],
		                'em' => [
		                    'min' => 0,
		                    'max' => 100,
		                ],
		                '%'  => [
		                    'min' => 0,
		                    'max' => 100,
		                ],
		                'vw' => [
		                    'min' => 0,
		                    'max' => 100,
		                ],
		            ],
		            'default'    => [
		                'size' => 100,
		                'unit' => '%',
		            ],
		            'required'   => true,
		            'selectors'  => [
		                '{{WRAPPER}} .bdt-image-mask>*' => '-webkit-mask-size: {{SIZE}}{{UNIT}}; mask-size: {{SIZE}}{{UNIT}};',
		            ],
		            'condition'  => [
		                'image_mask_popover'    => 'yes',
		                'image_mask_shape_size' => 'initial',
		            ],
		        ]
		    );

		    $this->add_control(
		        'image_mask_shape_repeat',
		        [
		            'label'                => esc_html__('Repeat', 'bdthemes-element-pack'),
		            'type'                 => Controls_Manager::SELECT,
		            'default'              => 'no-repeat',
		            'options'              => [
		                'repeat'          => esc_html__('Repeat', 'bdthemes-element-pack'),
		                'repeat-x'        => esc_html__('Repeat-x', 'bdthemes-element-pack'),
		                'repeat-y'        => esc_html__('Repeat-y', 'bdthemes-element-pack'),
		                'space'           => esc_html__('Space', 'bdthemes-element-pack'),
		                'round'           => esc_html__('Round', 'bdthemes-element-pack'),
		                'no-repeat'       => esc_html__('No-repeat', 'bdthemes-element-pack'),
		                'repeat-space'    => esc_html__('Repeat Space', 'bdthemes-element-pack'),
		                'round-space'     => esc_html__('Round Space', 'bdthemes-element-pack'),
		                'no-repeat-round' => esc_html__('No-repeat Round', 'bdthemes-element-pack'),
		            ],
		            'selectors_dictionary' => [
		                'repeat'          => 'repeat',
		                'repeat-x'        => 'repeat-x',
		                'repeat-y'        => 'repeat-y',
		                'space'           => 'space',
		                'round'           => 'round',
		                'no-repeat'       => 'no-repeat',
		                'repeat-space'    => 'repeat space',
		                'round-space'     => 'round space',
		                'no-repeat-round' => 'no-repeat round',
		            ],
		            'selectors'            => [
		                '{{WRAPPER}} .bdt-image-mask>*' => '-webkit-mask-repeat: {{VALUE}}; mask-repeat: {{VALUE}};',
		            ],
		            'condition'            => [
		                'image_mask_popover' => 'yes',
		            ],
		        ]
		    );

		    $this->end_popover();
		}
	}