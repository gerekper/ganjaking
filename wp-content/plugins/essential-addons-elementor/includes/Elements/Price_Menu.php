<?php
namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use Elementor\Repeater;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Utils;
use \Elementor\Widget_Base;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Price_Menu extends Widget_Base {
    
    public function get_name() {
        return 'eael-price-menu';
    }

    public function get_title() {
        return __( 'Price Menu', 'essential-addons-elementor' );
    }

    public function get_categories() {
        return [ 'essential-addons-elementor' ];
    }

    public function get_keywords()
    {
        return [
            'ea price menu',
            'ea pricing menu',
            'ea restaurant menu',
            'restaurant menu',
            'cafe menu',
            'food menu',
            'pricing menu',
            'ea',
            'essential addons'
        ];
    }

    public function get_custom_help_url()
    {
		return 'https://essential-addons.com/elementor/docs/price-menu/';
	}

    public function get_icon() {
        return 'eaicon-price-menu';
    }

    protected function register_controls() {

        /*-----------------------------------------------------------------------------------*/
        /*	Content Tab
        /*-----------------------------------------------------------------------------------*/
        
        $this->start_controls_section(
            'section_price_menu',
            [
                'label'                 => __( 'Price Menu', 'essential-addons-elementor' ),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'menu_title',
            [
                'name'          => 'menu_title',
                'label'         => __( 'Title', 'essential-addons-elementor' ),
                'type'          => Controls_Manager::TEXT,
                'dynamic'       => [
                    'active'    => true,
                ],
                'label_block'   => true,
                'placeholder'   => __( 'Title', 'essential-addons-elementor' ),
                'default'       => __( 'Title', 'essential-addons-elementor' ),
                'ai' => [
					'active' => false,
				],
            ]
        );

        $repeater->add_control(
            'menu_description',
            [
                'name'          => 'menu_description',
                'label'         => __( 'Description', 'essential-addons-elementor' ),
                'type'          => Controls_Manager::TEXTAREA,
                'dynamic'       => [
                    'active'    => true,
                ],
                'label_block'   => true,
                'placeholder'   => __( 'Description', 'essential-addons-elementor' ),
                'default'       => __( 'Description', 'essential-addons-elementor' ),
            ]
        );

        $repeater->add_control(
            'menu_price',
            [
                'name'          => 'menu_price',
                'label'         => __( 'Price', 'essential-addons-elementor' ),
                'type'          => Controls_Manager::TEXT,
                'dynamic'       => [
                    'active'    => true,
                ],
                'default'       => '$49',
                'ai' => [
					'active' => false,
				],
            ]
        );

        $repeater->add_control(
            'discount',
            [
                'name'          => 'discount',
                'label'         => __( 'Discount', 'essential-addons-elementor' ),
                'type'          => Controls_Manager::SWITCHER,
                'default'       => 'no',
                'label_on'      => __( 'On', 'essential-addons-elementor' ),
                'label_off'     => __( 'Off', 'essential-addons-elementor' ),
                'return_value'  => 'yes',
            ]
        );

        $repeater->add_control(
            'original_price',
            [
                'name'          => 'original_price',
                'label'         => __( 'Original Price', 'essential-addons-elementor' ),
                'type'          => Controls_Manager::TEXT,
                'dynamic'       => [
                    'active'    => true,
                ],
                'default'       => '$69',
                'condition'     => [
                    'discount' => 'yes',
                ],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $repeater->add_control(
            'image_switch',
            [
                'name'          => 'image_switch',
                'label'         => __( 'Show Image', 'essential-addons-elementor' ),
                'type'          => Controls_Manager::SWITCHER,
                'default'       => '',
                'label_on'      => __( 'On', 'essential-addons-elementor' ),
                'label_off'     => __( 'Off', 'essential-addons-elementor' ),
                'return_value'  => 'yes',
            ]
        );

        $repeater->add_control(
            'image',
            [
                'name'          => 'image',
                'label'         => __( 'Image', 'essential-addons-elementor' ),
                'type'          => Controls_Manager::MEDIA,
                'dynamic'       => [
                    'active'    => true,
                ],
                'condition'     => [
                    'image_switch' => 'yes',
                ],
                'ai' => [
                    'active' => false,
                ],
            ]
        );

        $repeater->add_control(
            'link',
            [
                'name'          => 'link',
                'label'         => __( 'Link', 'essential-addons-elementor' ),
                'type'          => Controls_Manager::URL,
                'dynamic'       => [
                    'active'    => true,
                ],
                'placeholder'   => 'https://www.your-link.com',
            ]
        );



		$this->add_control(
			'menu_items',
			[
				'label'                 => '',
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					[
						'menu_title' => __( 'Menu Item #1', 'essential-addons-elementor' ),
						'menu_price' => '$49',
					],
					[
						'menu_title' => __( 'Menu Item #2', 'essential-addons-elementor' ),
						'menu_price' => '$49',
					],
					[
						'menu_title' => __( 'Menu Item #3', 'essential-addons-elementor' ),
						'menu_price' => '$49',
					],
				],
				'fields'                => $repeater->get_controls(),
				'title_field'       => '{{{ menu_title }}}',
			]
		);
        
        $this->add_control(
          'menu_style',
          [
             'label'                => __( 'Menu Style', 'essential-addons-elementor' ),
             'type'                 => Controls_Manager::SELECT,
             'default'              => 'style-1',
             'options'              => [
                'style-eael'   => __( 'EA Style', 'essential-addons-elementor' ),
                'style-1'           => __( 'Style 1', 'essential-addons-elementor' ),
                'style-2'           => __( 'Style 2', 'essential-addons-elementor' ),
                'style-3'           => __( 'Style 3', 'essential-addons-elementor' ),
                'style-4'           => __( 'Style 4', 'essential-addons-elementor' ),
             ],
          ]
        );
        
        $this->add_responsive_control(
			'menu_align',
			[
				'label'                 => __( 'Alignment', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'    => [
						'title' => __( 'Center', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'     => [
						'title' => __( 'Right', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify'   => [
						'title' => __( 'Justified', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .eael-restaurant-menu-style-4'   => 'text-align: {{VALUE}};',
				],
                'condition'             => [
                    'menu_style' => 'style-4',
                ],
			]
		);
        
        $this->add_control(
            'title_price_connector',
            [
                'label'                 => __( 'Title-Price Connector', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'no',
                'label_on'              => __( 'Yes', 'essential-addons-elementor' ),
                'label_off'             => __( 'No', 'essential-addons-elementor' ),
                'return_value'          => 'yes',
                'condition'             => [
                    'menu_style' => 'style-1',
                ],
            ]
        );
        
        $this->add_control(
            'title_separator',
            [
                'label'                 => __( 'Title Separator', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'no',
                'label_on'              => __( 'Yes', 'essential-addons-elementor' ),
                'label_off'             => __( 'No', 'essential-addons-elementor' ),
                'return_value'          => 'yes',
            ]
        );

        $this->end_controls_section();

        /*-----------------------------------------------------------------------------------*/
        /*	Style Tab
        /*-----------------------------------------------------------------------------------*/

        /**
         * Style Tab: Menu Items Section
         */
        $this->start_controls_section(
            'section_items_style',
            [
                'label'                 => __( 'Menu Items', 'essential-addons-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'items_bg_color',
            [
                'label'                 => __( 'Background Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu .eael-restaurant-menu-item' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'items_spacing',
            [
                'label'                 => __( 'Items Spacing', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    '%' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu-item-wrap' => 'margin-bottom: calc(({{SIZE}}{{UNIT}})/2); padding-bottom: calc(({{SIZE}}{{UNIT}})/2)',
                ],
            ]
        );

		$this->add_responsive_control(
			'items_padding',
			[
				'label'                 => __( 'Padding', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .eael-restaurant-menu .eael-restaurant-menu-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'items_border',
				'label'                 => __( 'Border', 'essential-addons-elementor' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .eael-restaurant-menu .eael-restaurant-menu-item',
			]
		);

		$this->add_control(
			'items_border_radius',
			[
				'label'                 => __( 'Border Radius', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .eael-restaurant-menu .eael-restaurant-menu-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'pricing_table_shadow',
				'selector'              => '{{WRAPPER}} .eael-restaurant-menu-item',
				'separator'             => 'before',
			]
		);
        
        $this->end_controls_section();
        
        /**
         * Style Tab: Menu Items Section
         */
        $this->start_controls_section(
            'section_content_style',
            [
                'label'                 => __( 'Content', 'essential-addons-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'menu_style' => 'style-eael',
                ],
            ]
        );

		$this->add_responsive_control(
			'content_padding',
			[
				'label'                 => __( 'Padding', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .eael-restaurant-menu-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'menu_style' => 'style-eael',
                ],
			]
		);
        
        $this->end_controls_section();

        /**
         * Style Tab: Title Section
         */
        $this->start_controls_section(
            'section_title_style',
            [
                'label'                 => __( 'Title', 'essential-addons-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'                 => __( 'Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu .eael-restaurant-menu-title' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'title_typography',
                'label'                 => __( 'Typography', 'essential-addons-elementor' ),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_ACCENT
                ],
                'selector'              => '{{WRAPPER}} .eael-restaurant-menu .eael-restaurant-menu-title, {{WRAPPER}} .eael-restaurant-menu .eael-restaurant-menu-title a',
            ]
        );
        
        $this->add_responsive_control(
            'title_margin',
            [
                'label'                 => __( 'Margin Bottom', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    '%' => [
                        'min'   => 0,
                        'max'   => 40,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu .eael-restaurant-menu-header' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        
        $this->end_controls_section();

        $this->start_controls_section(
            'section_title_separator_style',
            [
                'label'                 => __( 'Title Separator', 'essential-addons-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'title_separator' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'divider_title_border_type',
            [
                'label'                 => __( 'Border Type', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'dotted',
                'options'               => [
                    'none'      => __( 'None', 'essential-addons-elementor' ),
                    'solid'     => __( 'Solid', 'essential-addons-elementor' ),
                    'double'    => __( 'Double', 'essential-addons-elementor' ),
                    'dotted'    => __( 'Dotted', 'essential-addons-elementor' ),
                    'dashed'    => __( 'Dashed', 'essential-addons-elementor' ),
                ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu .eael-price-menu-divider' => 'border-bottom-style: {{VALUE}}',
                ],
                'condition'             => [
                    'title_separator' => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'divider_title_border_weight',
            [
                'label'                 => __( 'Border Height', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => 1,
                ],
                'range'                 => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 20,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu .eael-price-menu-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'title_separator' => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'divider_title_border_width',
            [
                'label'                 => __( 'Border Width', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => 100,
                    'unit'      => '%',
                ],
                'range'                 => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 20,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu .eael-price-menu-divider' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'title_separator' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'divider_title_border_color',
            [
                'label'                 => __( 'Border Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu .eael-price-menu-divider' => 'border-bottom-color: {{VALUE}}',
                ],
                'condition'             => [
                    'title_separator' => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'divider_title_spacing',
            [
                'label'                 => __( 'Margin Bottom', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    '%' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu .eael-price-menu-divider' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();

        $this->start_controls_section(
            'section_price_style',
            [
                'label'                 => __( 'Price', 'essential-addons-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'price_badge_heading',
            [
                'label'                 => __( 'Price Badge', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'menu_style' => 'style-eael',
                ],
            ]
        );

        $this->add_control(
            'badge_text_color',
            [
                'label'                 => __( 'Text Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu-style-eael .eael-restaurant-menu-price' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'menu_style' => 'style-eael',
                ],
            ]
        );

        $this->add_control(
            'badge_bg_color',
            [
                'label'                 => __( 'Background Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu-style-eael .eael-restaurant-menu-price:after' => 'border-right-color: {{VALUE}}',
                ],
                'condition'             => [
                    'menu_style' => 'style-eael',
                ],
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label'                 => __( 'Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu .eael-restaurant-menu-price-discount' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'menu_style!' => 'style-eael',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'price_typography',
                'label'                 => __( 'Typography', 'essential-addons-elementor' ),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_ACCENT
                ],
                'selector'              => '{{WRAPPER}} .eael-restaurant-menu .eael-restaurant-menu-price-discount',
            ]
        );
        
        $this->add_control(
            'original_price_heading',
            [
                'label'                 => __( 'Original Price', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
            ]
        );
        
        $this->add_control(
            'original_price_strike',
            [
                'label'                 => __( 'Strikethrough', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'On', 'essential-addons-elementor' ),
                'label_off'             => __( 'Off', 'essential-addons-elementor' ),
                'return_value'          => 'yes',
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu .eael-restaurant-menu-price-original' => 'text-decoration: line-through;',
                ],
            ]
        );

        $this->add_control(
            'original_price_color',
            [
                'label'                 => __( 'Original Price Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '#a3a3a3',
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu .eael-restaurant-menu-price-original' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'original_price_typography',
                'label'                 => __( 'Original Price Typography', 'essential-addons-elementor' ),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_ACCENT
                ],
                'selector'              => '{{WRAPPER}} .eael-restaurant-menu .eael-restaurant-menu-price-original',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_description_style',
            [
                'label'                 => __( 'Description', 'essential-addons-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label'                 => __( 'Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu-description' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'description_typography',
                'label'                 => __( 'Typography', 'essential-addons-elementor' ),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_ACCENT
                ],
                'selector'              => '{{WRAPPER}} .eael-restaurant-menu-description',
            ]
        );
        
        $this->add_responsive_control(
            'description_spacing',
            [
                'label'                 => __( 'Margin Bottom', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    '%' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();

        /**
         * Style Tab: Image Section
         */
        $this->start_controls_section(
            'section_image_style',
            [
                'label'                 => __( 'Image', 'essential-addons-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
		
        $this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'image_size',
				'label'                 => __( 'Image Size', 'essential-addons-elementor' ),
				'default'               => 'thumbnail',
			]
		);

        $this->add_control(
            'image_bg_color',
            [
                'label'                 => __( 'Background Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu-image img' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'image_width',
            [
                'label'                 => __( 'Width', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 20,
                        'max'   => 300,
                        'step'  => 1,
                    ],
                    '%' => [
                        'min'   => 5,
                        'max'   => 50,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu-image img' => 'width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

		$this->add_responsive_control(
			'image_margin',
			[
				'label'                 => __( 'Margin', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .eael-restaurant-menu-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_padding',
			[
				'label'                 => __( 'Padding', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .eael-restaurant-menu-image img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'image_border',
				'label'                 => __( 'Border', 'essential-addons-elementor' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .eael-restaurant-menu-image img',
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label'                 => __( 'Border Radius', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .eael-restaurant-menu-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_vertical_position',
			[
				'label'                 => __( 'Vertical Position', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'       => [
						'title' => __( 'Top', 'essential-addons-elementor' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle'    => [
						'title' => __( 'Middle', 'essential-addons-elementor' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom'    => [
						'title' => __( 'Bottom', 'essential-addons-elementor' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .eael-restaurant-menu .eael-restaurant-menu-image' => 'align-self: {{VALUE}}',
				],
				'selectors_dictionary'  => [
					'top'      => 'flex-start',
                    'middle'   => 'center',
					'bottom'   => 'flex-end',
				],
			]
		);

        $this->end_controls_section();

        /**
         * Style Tab: Items Divider Section
         */
        $this->start_controls_section(
            'section_table_title_connector_style',
            [
                'label'                 => __( 'Title-Price Connector', 'essential-addons-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'title_price_connector' => 'yes',
                    'menu_style' => 'style-1',
                ],
            ]
        );
        
        $this->add_control(
			'title_connector_vertical_align',
			[
				'label'                 => __( 'Vertical Alignment', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::CHOOSE,
				'default'               => 'middle',
				'options'               => [
					'top'          => [
						'title'    => __( 'Top', 'essential-addons-elementor' ),
						'icon'     => 'eicon-v-align-top',
					],
					'middle'       => [
						'title'    => __( 'Center', 'essential-addons-elementor' ),
						'icon'     => 'eicon-v-align-middle',
					],
					'bottom'       => [
						'title'    => __( 'Bottom', 'essential-addons-elementor' ),
						'icon'     => 'eicon-v-align-bottom',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .eael-restaurant-menu-style-1 .eael-price-title-connector'   => 'align-self: {{VALUE}};',
				],
				'selectors_dictionary'  => [
					'top'          => 'flex-start',
					'middle'       => 'center',
					'bottom'       => 'flex-end',
				],
                'condition'             => [
                    'title_price_connector' => 'yes',
                    'menu_style' => 'style-1',
                ],
			]
		);
        
        $this->add_control(
            'items_divider_style',
            [
                'label'                 => __( 'Style', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'dashed',
                'options'              => [
                    'solid'     => __( 'Solid', 'essential-addons-elementor' ),
                    'dashed'    => __( 'Dashed', 'essential-addons-elementor' ),
                    'dotted'    => __( 'Dotted', 'essential-addons-elementor' ),
                    'double'    => __( 'Double', 'essential-addons-elementor' ),
                ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu-style-1 .eael-price-title-connector' => 'border-bottom-style: {{VALUE}}',
                ],
                'condition'             => [
                    'title_price_connector' => 'yes',
                    'menu_style' => 'style-1',
                ],
            ]
        );

        $this->add_control(
            'items_divider_color',
            [
                'label'                 => __( 'Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '#000',
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu-style-1 .eael-price-title-connector' => 'border-bottom-color: {{VALUE}}',
                ],
                'condition'             => [
                    'title_price_connector' => 'yes',
                    'menu_style' => 'style-1',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'items_divider_weight',
            [
                'label'                 => __( 'Divider Weight', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [ 'size' => '1'],
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 30,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-restaurant-menu-style-1 .eael-price-title-connector' => 'border-bottom-width: {{SIZE}}{{UNIT}}; bottom: calc((-{{SIZE}}{{UNIT}})/2)',
                ],
                'condition'             => [
                    'title_price_connector' => 'yes',
                    'menu_style' => 'style-1',
                ],
            ]
        );
        
        $this->end_controls_section();

    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $i = 1;
        $this->add_render_attribute( 'price-menu', 'class', 'eael-restaurant-menu' );
        
		if ( $settings['menu_style'] ) {
			$this->add_render_attribute( 'price-menu', 'class', 'eael-restaurant-menu-' . $settings['menu_style'] );
		}
        ?>
        <div <?php echo $this->get_render_attribute_string( 'price-menu' ); ?>>
            <div class="eael-restaurant-menu-items">
                <?php foreach ( $settings['menu_items'] as $index => $item ) : ?>
                    <?php
                        $title_key = $this->get_repeater_setting_key( 'menu_title', 'menu_items', $index );
                        $this->add_render_attribute( $title_key, 'class', 'eael-restaurant-menu-title-text' );
                        $this->add_inline_editing_attributes( $title_key, 'none' );

                        $description_key = $this->get_repeater_setting_key( 'menu_description', 'menu_items', $index );
                        $this->add_render_attribute( $description_key, 'class', 'eael-restaurant-menu-description' );
                        $this->add_inline_editing_attributes( $description_key, 'basic' );

                        $discount_price_key = $this->get_repeater_setting_key( 'menu_price', 'menu_items', $index );
                        $this->add_render_attribute( $discount_price_key, 'class', 'eael-restaurant-menu-price-discount' );
                        $this->add_inline_editing_attributes( $discount_price_key, 'none' );

                        $original_price_key = $this->get_repeater_setting_key( 'original_price', 'menu_items', $index );
                        $this->add_render_attribute( $original_price_key, 'class', 'eael-restaurant-menu-price-original' );
                        $this->add_inline_editing_attributes( $original_price_key, 'none' );
                    ?>
                    <div class="eael-restaurant-menu-item-wrap">
                        <div class="eael-restaurant-menu-item">
                            <?php if ( $item['image_switch'] == 'yes' ) { ?>
                                <div class="eael-restaurant-menu-image">
                                    <?php
                                        if ( ! empty( $item['image']['url'] ) ) :
                                            $image = $item['image'];
                                            $image_url = Group_Control_Image_Size::get_attachment_image_src( $image['id'], 'image_size', $settings );
                                        ?>
                                        <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr(get_post_meta($image['id'], '_wp_attachment_image_alt', true)); ?>">	
                                     <?php endif; ?>
                                </div>
                            <?php } ?>

                            <div class="eael-restaurant-menu-content">
                                <div class="eael-restaurant-menu-header">
                                    <?php if ( ! empty( $item['menu_title'] ) ) { ?>
                                        <h4 class="eael-restaurant-menu-title">
                                            <?php
                                                if ( ! empty( $item['link']['url'] ) ) {
                                                    $this->add_render_attribute( 'price-menu-link' . $i, 'href', $item['link']['url'] );

                                                    if ( ! empty( $item['link']['is_external'] ) ) {
                                                        $this->add_render_attribute( 'price-menu-link' . $i, 'target', '_blank' );
                                                    }
                                                    ?>
                                                    <a <?php echo $this->get_render_attribute_string( 'price-menu-link' . $i ); ?>>
                                                        <span <?php echo $this->get_render_attribute_string( $title_key ); ?>>
                                                            <?php echo $item['menu_title']; ?>
                                                        </span>
                                                    </a>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <span <?php echo $this->get_render_attribute_string( $title_key ); ?>>
                                                        <?php echo $item['menu_title']; ?>
                                                    </span>
                                                    <?php
                                                }
                                            ?>
                                        </h4>
                                    <?php } ?>
                                    
                                    <?php if ( $settings['title_price_connector'] == 'yes' ) { ?>
                                        <span class="eael-price-title-connector"></span>
                                    <?php } ?>
                                    
                                    <?php if ( $settings['menu_style'] == 'style-1' ) { ?>
                                        <?php if ( ! empty( $item['menu_price'] ) ) { ?>
                                            <span class="eael-restaurant-menu-price">
                                                <?php if ( $item['discount'] == 'yes' ) { ?>
                                                    <span <?php echo $this->get_render_attribute_string( $original_price_key ); ?>>
                                                        <?php echo esc_attr( $item['original_price'] ); ?>
                                                    </span>
                                                <?php } ?>
                                                <span <?php echo $this->get_render_attribute_string( $discount_price_key ); ?>>
                                                    <?php echo esc_attr( $item['menu_price'] ); ?>
                                                </span>
                                            </span>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                
                                <?php if ( $settings['title_separator'] == 'yes' ) { ?>
                                    <div class="eael-price-menu-divider-wrap">
                                        <div class="eael-price-menu-divider"></div>
                                    </div>
                                <?php } ?>

                                <?php
                                    if ( ! empty( $item['menu_description'] ) ) {
                                        $description_html = sprintf( '<div %1$s>%2$s</div>', $this->get_render_attribute_string( $description_key ), $item['menu_description'] );
                                        
                                        echo $description_html;
                                    }
                                ?>

                                <?php if ( $settings['menu_style'] != 'style-1' ) { ?>
                                    <?php if ( ! empty( $item['menu_price'] ) ) { ?>
                                        <span class="eael-restaurant-menu-price">
                                            <?php if ( $item['discount'] == 'yes' ) { ?>
                                                <span <?php echo $this->get_render_attribute_string( $original_price_key ); ?>>
                                                    <?php echo esc_attr( $item['original_price'] ); ?>
                                                </span>
                                            <?php } ?>
                                            <span <?php echo $this->get_render_attribute_string( $discount_price_key ); ?>>
                                                <?php echo esc_attr( $item['menu_price'] ); ?>
                                            </span>
                                        </span>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php $i++; endforeach; ?>
            </div>
        </div>
        <?php
    }

    protected function content_template() { }
}
