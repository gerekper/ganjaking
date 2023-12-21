<?php
/**
 * Nav Menu widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;


defined( 'ABSPATH' ) || die();

require HAPPY_ADDONS_DIR_PATH . 'extensions/walker-nav-menu.php';
use \Happy_Addons\Elementor\Extension\HANav_Menu_Walker;

class Navigation_Menu extends Base {

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return __( 'Nav Menu', 'happy-elementor-addons' );
	}

	public function get_custom_help_url() {
		return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/navigation-menu/';
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'hm hm-clip-board';
	}

	public function get_keywords() {
		return [ 'nav', 'menu', 'nav menu', 'navigation', 'Nav Menu', 'Navigation Menu' ];
	}

	/**
	 * Get a list of all Navigation Menu
	 *
	 * @return array
	 */
	public function get_menus() {
		$list  = [];
		$menus = wp_get_nav_menus();
		foreach ( $menus as $menu ) {
			$list[ $menu->slug ] = $menu->name;
		}

		return $list;
	}

	/**
	 * Register widget content controls
	 */
	protected function register_content_controls() {
		$this->ha_nav_menu_content_controls();
	}

	protected function ha_nav_menu_content_controls() {

		$this->start_controls_section(
			'_section_nav_menu_settings',
			[
				'label' => __( 'Nav Menu', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'nav_menu_list',
			[
				'label'   => __( 'Select menu', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_menus(),
			]
		);

		$this->add_control(
			'nav_menu_position',
			[
				'label'     => __( 'Alignment', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center'     => [
						'title' => __( 'Center', 'happy-elementor-addons' ),
						'icon'  => 'eicon-h-align-center',
					],
					'flex-end'   => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'toggle'    => true,
				'default'   => 'flex-end',
				'selectors' => [
					'{{WRAPPER}} .ha-navigation-menu-wrapper' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'_heading_nav_menu_responsive',
			[
				'label'     => __( 'Responsive', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'nav_menu_responsive_position',
			[
				'label'     => __( 'Alignment', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center'     => [
						'title' => __( 'Center', 'happy-elementor-addons' ),
						'icon'  => 'eicon-h-align-center',
					],
					'flex-end'   => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'toggle'    => true,
				'default'   => 'flex-end',
				'selectors' => [
					'{{WRAPPER}} .ha-navigation-burger-menu .ha-nav-humberger-wrapper' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hamburger_icon',
			[
				'label'                  => __( 'Menu Icon', 'happy-elementor-addons' ),
				'type'                   => Controls_Manager::ICONS,
				'label_block'            => false,
				'default'                => [
					'value'   => 'fas fa-bars',
					'library' => 'fa-solid',
				],
				'skin'                   => 'inline',
				'exclude_inline_options' => ['svg'],
			]
		);

		$this->add_control(
			'hamburger_close_icon',
			[
				'label'                  => __( 'Close Icon', 'happy-elementor-addons' ),
				'type'                   => Controls_Manager::ICONS,
				'label_block'            => false,
				'default'                => [
					'value'   => 'far fa-window-close',
					'library' => 'fa-solid',
				],
				'skin'                   => 'inline',
				'exclude_inline_options' => ['svg'],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget style controls
	 */
	protected function register_style_controls() {
		$this->__nav_menu_style_controls();
		$this->__nav_menu_dropdown_tyle_controls();
		$this->__nav_menu_responsive_tyle_controls();
	}

	protected function __nav_menu_style_controls() {

		$this->start_controls_section(
			'_section_nav_menu_style_control',
			[
				'label' => __( 'Main Menu', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ha_nav_menu_padding_x',
			[
				'label'      => __( 'Horizontal Padding', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 400,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-nav-menu .menu li.menu-item a' => 'padding-left: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ha-nav-menu .menu li.menu-item' => 'padding-right: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'ha_nav_menu_padding_y',
			[
				'label'      => __( 'Vertical Padding', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 400,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-nav-menu ul.menu li a' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
					// '{{WRAPPER}} .ha-navigation-menu-wrapper ul.menu > li.menu-item > a' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'ha_nav_menu_margin',
			[
				'label'      => __( 'Space Between', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 400,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-nav-menu .menu > li.menu-item:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ha-navigation-burger-menu ul.menu > li.menu-item' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'nav_menu_link_hover_effect',
			[
				'label'     => __( ' Link Hover Effect', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => [
					'none'      => __( 'None', 'happy-elementor-addons' ),
					'underline' => __( 'Underline', 'happy-elementor-addons' ),
					'overline'  => __( 'Overline', 'happy-elementor-addons' ),
				],
				'selectors' => [
					'{{WRAPPER}} .ha-navigation-menu-wrapper ul.menu li a:hover' => 'text-decoration: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'nav_menu_item_typography',
				'label'          => __( 'Typography', 'happy-elementor-addons' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'separator'      => 'before',
				'fields_options' => [
					'typography'  => [
						'default' => 'yes',
					],
					'font_family' => [
						'default' => 'Nunito',
					],
					'font_weight' => [
						'default' => 'bold',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '16',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .ha-navigation-menu-wrapper ul.menu li a, {{WRAPPER}} .ha-navigation-burger-menu ul.menu li a',
			]
		);

		$this->start_controls_tabs(
			'nav_menu_active_tabs'
		);

		$this->start_controls_tab(
			'nav_menu_normal_tab',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'nav_menu_item_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .ha-navigation-menu-wrapper ul.menu > li > a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-navigation-menu-wrapper ul.menu li .ha-submenu-indicator-wrap' => 'color: {{VALUE}}',
					// '{{WRAPPER}} .ha-navigation-burger-menu ul.menu li a' => 'color: {{VALUE}}',
					// '{{WRAPPER}} .ha-navigation-burger-menu ul.menu li .ha-submenu-indicator-wrap' => 'color: {{VALUE}}',
				],

			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'nav_menu_item_background',
				'label'    => __( 'Background', 'happy-elementor-addons' ),
				'types'    => ['classic', 'gradient'],
				'exclude'  => ['image'],
				'selector' => '{{WRAPPER}} .ha-navigation-menu-wrapper ul.menu > li, {{WRAPPER}} .ha-navigation-burger-menu ul.menu li',
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'nav_menu_hover_tab',
			[
				'label' => __( 'Hover', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'nav_menu_item_hover_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#E2498A',
				'selectors' => [
					'{{WRAPPER}} .ha-navigation-menu-wrapper ul.menu > li:hover > a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-navigation-menu-wrapper ul.menu > li:hover > .ha-submenu-indicator-wrap' => 'color: {{VALUE}}',
					// '{{WRAPPER}} .ha-navigation-burger-menu ul.menu li a:hover' => 'color: {{VALUE}}',
					// '{{WRAPPER}} .ha-navigation-burger-menu ul.menu li:hover .ha-submenu-indicator-wrap' => 'color: {{VALUE}}',
				],

			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'nav_menu_item_hover_background',
				'label'    => __( 'Background', 'happy-elementor-addons' ),
				'types'    => ['classic', 'gradient'],
				'exclude'  => ['image'],
				'selector' => '{{WRAPPER}} .ha-navigation-menu-wrapper ul.menu > li:hover, {{WRAPPER}} .ha-navigation-burger-menu ul.menu li:hover',
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'nav_menu_active_tab',
			[
				'label' => __( 'Active', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'nav_menu_item_active_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#cf005c',
				'selectors' => [
					'{{WRAPPER}} .ha-nav-menu ul.menu > li.active > a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-nav-menu ul.menu > li.active > .ha-submenu-indicator-wrap' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-nav-menu ul.menu > li.current-menu-ancestor > a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-nav-menu ul.menu > li.current-menu-ancestor > .ha-submenu-indicator-wrap' => 'color: {{VALUE}}',
				],

			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'nav_menu_item_active_background',
				'label'    => __( 'Background', 'happy-elementor-addons' ),
				'types'    => ['classic', 'gradient'],
				'exclude'  => ['image'],
				'selector' => '{{WRAPPER}} .ha-nav-menu ul.menu > li.active',
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	protected function __nav_menu_dropdown_tyle_controls() {

		$this->start_controls_section(
			'_nav_menu_dropdown_style_control',
			[
				'label' => __( 'Dropdown', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'_nav_menu_dropdown_wrap',
			[
				'label' => __( 'Wrapper', 'happy-elementor-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'nav_submenu_wrap_background',
				'label'    => __( 'Background', 'happy-elementor-addons' ),
				'types'    => ['classic', 'gradient'],
				'exclude'  => ['image'],
				'selector' => '{{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'nav_submenu_box_shadow',
				'label'    => __( 'Box Shadow', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'nav_submenu_border',
				'selector' => '{{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu',
			]
		);

		$this->add_responsive_control(
			'nav_submenu_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu' => 'border-radius: {{SIZE}}{{UNIT}};',
					// '{{WRAPPER}} .ha-navigation-menu-wrapper ul.menu li:first-child' => 'border-top-left-radius: {{SIZE}}{{UNIT}};border-top-right-radius: {{SIZE}}{{UNIT}};',
					// '{{WRAPPER}} .ha-navigation-menu-wrapper ul.menu li:last-child' => 'border-bottom-left-radius: {{SIZE}}{{UNIT}};border-bottom-right-radius: {{SIZE}}{{UNIT}};',
					// '{{WRAPPER}} .ha-navigation-menu-wrapper ul.menu li:last-child' => 'border-radius:  0 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}};',
					// '{{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu > li' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'nav_submenu_box_width',
			[
				'label'      => __( 'Box Width', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 220,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// $this->add_responsive_control(
		// 	'nav_submenu_padding',
		// 	[
		// 		'label'      => __( 'Padding', 'happy-elementor-addons' ),
		// 		'type'       => Controls_Manager::DIMENSIONS,
		// 		'size_units' => ['px', '%'],
		// 		'default'    => [
		// 			'top'    => 15,
		// 			'right'  => 15,
		// 			'bottom' => 15,
		// 			'left'   => 15,
		// 			'unit'   => 'px',
		// 		],
		// 		'selectors'  => [
		// 			'{{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// 		],
		// 	]
		// );

		$this->add_responsive_control(
			'nav_submenu_box_top_distance',
			[
				'label'      => __( 'Top Distance', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-navigation-menu-wrapper ul.menu > li.menu-item > ul.sub-menu' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		/* Items */
		$this->add_control(
			'_nav_menu_dropdown_item_heading',
			[
				'label' => __( 'Items', 'happy-elementor-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'nav_submenu_item_typography',
				'label'     => __( 'Typography', 'happy-elementor-addons' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu > li > a',
			]
		);

		$this->add_responsive_control(
			'nav_submenu_item_padding',
			[
				'label'      => __( 'Padding', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'default'    => [
					'top'    => 15,
					'right'  => '',
					'bottom' => 15,
					'left'   => '',
					'unit'   => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu > li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
			'nav_submenu_normal_tabs'
		);

		$this->start_controls_tab(
			'nav_submenu_normal_tab',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'nav_submenu_item_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu > li > a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu > li > .ha-submenu-indicator-wrap' => 'color: {{VALUE}}',
				],

			]
		);

		// $this->add_group_control(
		// 	Group_Control_Background::get_type(),
		// 	[
		// 		'name'     => 'nav_submenu_item_background',
		// 		'label'    => __( 'Background', 'happy-elementor-addons' ),
		// 		'types'    => ['classic', 'gradient'],
		// 		'exclude'  => ['image'],
		// 		'selector' => '{{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu > li',
		// 	]
		// );
		$this->end_controls_tab();

		$this->start_controls_tab(
			'nav_submenu_hover_tab',
			[
				'label' => __( 'Hover', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'nav_submenu_item_hover_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#E2498A',
				'selectors' => [
					'{{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu > li:hover > a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu > li:hover > .ha-submenu-indicator-wrap' => 'color: {{VALUE}}',
				],

			]
		);

		$this->add_group_control(
		    Group_Control_Background::get_type(),
		    [
		        'name' => 'nav_submenu_item_hover_background',
		        'label' => __('Background', 'happy-elementor-addons'),
		        'types' => ['classic', 'gradient'],
		        'exclude' => ['image'],
		        'selector' => '{{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu > li:hover',
		    ]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

        /*Divider*/
        $this->add_control(
			'_nav_menu_dropdown_divider_heading',
			[
				'label' => __( 'Divider', 'happy-elementor-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'nav_menu_dropdown_divider_border',
				'selector' => '{{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu > li:not(:last-child)',
			]
		);

		$this->end_controls_section();

	}

	protected function __nav_menu_responsive_tyle_controls() {

		$this->start_controls_section(
			'_nav_menu_responsive_style_control',
			[
				'label' => __( 'Responsive Navigation', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'nav_menu_res_item_hover_background',
				'label'    => __( 'Icon Background', 'happy-elementor-addons' ),
				'types'    => ['classic', 'gradient'],
				'exclude'  => ['image'],
				// 'selector' => '{{WRAPPER}} .ha-navigation-burger-menu .ha-menu-toggler, {{WRAPPER}} .ha-nav-humberger-wrapper',
				'selector' => '{{WRAPPER}} .ha-navigation-burger-menu .ha-menu-toggler',
			]
		);

		$this->add_control(
			'nav_res_menu_icon_size',
			[
				'label'      => __( 'Icon Size', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 150,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 22,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-navigation-burger-menu .ha-nav-humberger-wrapper .ha-menu-toggler' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'nav_res_menu_border_width',
			[
				'label'      => __( 'Border Width', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 10,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-navigation-burger-menu .ha-nav-humberger-wrapper .ha-menu-toggler' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'nav_res_menu_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-navigation-burger-menu .ha-nav-humberger-wrapper .ha-menu-toggler' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'nav_res_menu_border_padding',
			[
				'label'      => __( 'Padding', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'default'    => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
					'unit'   => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-navigation-burger-menu .ha-menu-toggler' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'nav_menu_res_icon_color',
			[
				'label'     => __( 'Icon Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7a7a7a',
				'selectors' => [
					'{{WRAPPER}} .ha-navigation-burger-menu .ha-nav-humberger-wrapper .ha-menu-toggler' => 'color: {{VALUE}}',
				],

			]
		);

		$this->add_control(
			'nav_menu_res_border_color',
			[
				'label'     => __( 'Border Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7a7a7a',
				'selectors' => [
					'{{WRAPPER}} .ha-navigation-burger-menu .ha-nav-humberger-wrapper .ha-menu-toggler' => 'border-color: {{VALUE}}',
				],

			]
		);

		$this->add_control(
			'nav_menu_res_seperator_color',
			[
				'label'     => __( 'Seperator Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#c4c4c4',
				'selectors' => [
					'{{WRAPPER}} .ha-navigation-burger-menu ul.menu li.menu-item:not(:last-child)' => 'border-bottom-color: {{VALUE}}',
				],

			]
		);

		$this->add_control(
			'_heading_nav_res_bg',
			[
				'label' => __( 'Menu Item', 'happy-elementor-addons' ),
				'type'  => Controls_Manager::HEADING,
				// 'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'nav_menu_res_item_background',
				'label'    => __( 'Background', 'happy-elementor-addons' ),
				'types'    => ['classic', 'gradient'],
				'exclude'  => ['image'],
				'selector' => '{{WRAPPER}} .ha-navigation-burger-menu ul.menu li.menu-item',
			]
		);

		$this->end_controls_section();

	}


	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( $this->get_menus() ) {
			$placeholder_msg = __( 'Please Select a Menu.', 'happy-elementor-addons' );
		} else {
			$placeholder_msg = __( 'You don\'t have any menu created. Please create a new one from Here', 'happy-elementor-addons' );
		}

		if ( ha_elementor()->editor->is_edit_mode() && $settings['nav_menu_list'] == '' ) { ?>

			<div class="ha-editor-placeholder">
				<h4 class="ha-editor-placeholder-title">
					<?php esc_html_e( 'Navigation Menu', 'happy-elementor-addons' ); ?>
				</h4>
				<div class="ha-editor-placeholder-content">
					<?php echo $placeholder_msg; ?>
				</div>
			</div>

		<?php }

		if ( $settings['nav_menu_list'] != '' && wp_get_nav_menu_items( $settings['nav_menu_list'] ) !== false && count( wp_get_nav_menu_items( $settings['nav_menu_list'] ) ) > 0 ) {
			echo '<nav class="ha-nav-menu ha-navigation-menu-wrapper">';
			$this->render_raw();
			echo '</nav>';
		}

	}

	protected function render_raw() {
		$settings = $this->get_settings_for_display();

		$walker = ( class_exists( '\Happy_Addons\Elementor\Extension\HANav_Menu_Walker' ) ? new \Happy_Addons\Elementor\Extension\HANav_Menu_Walker() : '' );
		ob_start(); ?>
				<div class="ha-nav-humberger-wrapper">
					<span class="ha-menu-open-icon ha-menu-toggler" data-humberger="open"><?php Icons_Manager::render_icon( $settings['hamburger_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
					<span class="ha-menu-close-icon ha-menu-toggler hide-icon" data-humberger="close"><?php Icons_Manager::render_icon( $settings['hamburger_close_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
				</div>
			<?php
			$icon_markup = ob_get_clean();

			$args = array(
				'items_wrap'    => $icon_markup . '<ul id="%1$s" class="%2$s">%3$s</ul>',
				'menu'          => $settings['nav_menu_list'],
				'fallback_cb'   => '\Happy_Addons\Elementor\Extension\HANav_Menu_Walker::fallback',
				'depth'         => 4,
				'link_before'   => '<span class="menu-item-title">',
				'link_after'    => '</span>',
				'walker'        => $walker,
				'sub_indicator' => '<span class="ha-navigation-submenu-indicator"></span>',
			);

			wp_nav_menu( $args );

	}

}
