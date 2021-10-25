<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Navbar_Style_One_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Elementor widget name.
	 *
	 * @return string Widget name.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'appside-navbar-style01-widget';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Elementor widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return esc_html__( 'Navbar Style: 01', 'aapside-master' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Elementor widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'eicon-nav-menu';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Elementor widget belongs to.
	 *
	 * @return array Widget categories.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_categories() {
		return [ 'appside_builder_widgets' ];
	}

	/**
	 * Register Elementor widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'settings_section',
			[
				'label' => esc_html__( 'General Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control( 'logo', [
			'type'        => Controls_Manager::MEDIA,
			'label'       => esc_html__( 'Logo', 'aapside-master' ),
			'description' => esc_html__( 'Upload logo for navbar', 'aapside-master' ),
			'default'     => [
				'src' => Utils::get_placeholder_image_src()
			]
		] );
		$this->add_control( 'menu', [
			'type'        => Controls_Manager::SELECT,
			'label'       => esc_html__( 'Menu', 'aapside-master' ),
			'options'     => appside_master()->get_nav_menu_list(),
			'description' => esc_html__( 'select menu for navbar', 'aapside-master' )
		] );
		$this->add_control( 'is_absolute', [
			'type'        => Controls_Manager::SWITCHER,
			'label'       => esc_html__( 'Absolute', 'aapside-master' ),
			'description' => esc_html__( 'make navbar absolute', 'aapside-master' )
		] );
		$this->add_control( 'button_status', [
			'type'        => Controls_Manager::SWITCHER,
			'label'       => esc_html__( 'Button', 'aapside-master' ),
			'description' => esc_html__( 'show/hide button', 'aapside-master' )
		] );
		$this->add_control( 'button_text', [
			'type'        => Controls_Manager::TEXT,
			'label'       => esc_html__( 'Button Text', 'aapside-master' ),
			'description' => esc_html__( 'set navbar button text', 'aapside-master' ),
			'default'     => esc_html__( 'Download', 'aapside-master' ),
            'condition' => ['button_status' => 'yes']
		] );
		$this->add_control( 'button_link', [
			'type'        => Controls_Manager::URL,
			'label'       => esc_html__( 'Button Link', 'aapside-master' ),
			'description' => esc_html__( 'set navbar button link', 'aapside-master' ),
			'default'     => [
				'url' => '#'
			],
			'condition' => ['button_status' => 'yes']
		] );
		$this->add_responsive_control(
			'align',
			[
				'label'     => esc_html__( 'Alignment', 'aapside-master' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'    => [
						'title' => esc_html__( 'Left', 'aapside-master' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__( 'Center', 'aapside-master' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => esc_html__( 'Right', 'aapside-master' ),
						'icon'  => 'eicon-text-align-right',
					]
				],
				'default'   => 'right',
				'selectors' => [
					'{{WRAPPER}} .navbar-elementor-style-one-wrapper .navbar-area .nav-container .navbar-collapse .navbar-nav' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'menu_styling_section',
			[
				'label' => esc_html__( 'Menu Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'menu_background_color',
			'label'       => esc_html__( 'Menu Background Color', 'aapside-master' ),
			'description' => esc_html__( 'change menu background color', 'aapside-master' ),
			"selector"    => "{{WRAPPER}} .navbar-elementor-style-one-wrapper .navbar-area.navbar-default"
		] );
		$this->add_responsive_control(
			'menu_padding',
			[
				'label' => esc_html__( 'Padding', 'aapside-master' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .navbar-elementor-style-one-wrapper .navbar-area.navbar-default' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'menu_items_gap',
			[
				'label' => esc_html__( 'Menu Item Gap', 'aapside-master' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .navbar-area .nav-container .navbar-collapse .navbar-nav li + li' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'button_left_margin_gap',
			[
				'label' => esc_html__( 'Button Left Margin', 'aapside-master' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .navbar-area .nav-container .nav-right-content ul' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control( 'menu_area_styling_divider', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_control( 'menu_color', [
			'label'       => esc_html__( 'Menu Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change menu color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .navbar-area .nav-container .navbar-collapse .navbar-nav li" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'menu_active_color', [
			'label'       => esc_html__( 'Menu Active Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change menu active color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .navbar-area .nav-container .navbar-collapse .navbar-nav li.current-menu-item" => "color: {{VALUE}}",
				"{{WRAPPER}} .navbar-area .nav-container .navbar-collapse .navbar-nav li:hover" => "color: {{VALUE}}",
				"{{WRAPPER}} .navbar-area .nav-container .navbar-collapse .navbar-nav li:hover > a" => "color: {{VALUE}}",
				"{{WRAPPER}} .navbar-area .nav-container .navbar-collapse .navbar-nav li.current-menu-item a" => "color: {{VALUE}}"
			]
		] );
		$this->end_controls_section();

		$this->start_controls_section(
			'menu_dropdown_styling_section',
			[
				'label' => esc_html__( 'Dropdown Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control( 'dropdown_color', [
			'label'       => esc_html__( 'Dropdown Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change dropdown color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .navbar-area .nav-container .navbar-collapse .navbar-nav li.menu-item-has-children .sub-menu li a" => "color: {{VALUE}}",
				"{{WRAPPER}} .navbar-area .nav-container .navbar-collapse .navbar-nav li.menu-item-has-children .sub-menu .menu-item-has-children:before" => "color: {{VALUE}}"
			]
		] );

		$this->add_control( 'dropdown_border_bottom_color', [
			'label'       => esc_html__( 'Dropdown Border Bottom Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change dropdown border bottom color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .navbar-area .nav-container .navbar-collapse .navbar-nav li.menu-item-has-children .sub-menu" => "border-bottom-color: {{VALUE}}"
			]
		] );
		$this->add_control( 'dropdown_item_border_bottom_color', [
			'label'       => esc_html__( 'Dropdown Item Border Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change dropdown item border color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .navbar-area .nav-container .navbar-collapse .navbar-nav li.menu-item-has-children .sub-menu li + li" => "border-top-color: {{VALUE}}"
			]
		] );
		$this->add_control( 'dropdown_hover_background_color', [
			'label'       => esc_html__( 'Dropdown Hover Background Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change dropdown hover background color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .navbar-area .nav-container .navbar-collapse .navbar-nav li.menu-item-has-children .sub-menu li a:hover" => "background-color: {{VALUE}}"
			]
		] );
		$this->add_control( 'dropdown_hover_color', [
			'label'       => esc_html__( 'Dropdown Hover Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change dropdown hover color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .navbar-area .nav-container .navbar-collapse .navbar-nav li.menu-item-has-children .sub-menu li a:hover" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( '_menu_typography_divider', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'menu_typography',
			'label'    => esc_html__( 'Menu Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .navbar-area .nav-container .navbar-collapse .navbar-nav li"
		] );
		$this->end_controls_section();

		$this->start_controls_section(
			'button_gd_two_section',
			[
				'label' => esc_html__( 'Button Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'button_one_padding',
			[
				'label' => esc_html__('Padding', 'aapside-master' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .navbar-area .nav-container .nav-right-content ul li .boxed-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'button_two_background' );

		$this->start_controls_tab( 'normal_two_style', [
			'label' => esc_html__( 'Normal', 'aapside-master' )
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'gd_two_background',
			'selector'    => "{{WRAPPER}} .navbar-area .nav-container .nav-right-content ul li .boxed-btn",
			'description' => esc_html__( 'button background', 'aapside-master' )
		] );
		$this->add_control( 'gd_two_text_color', [
			'label'       => esc_html__( 'Button Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button text color', 'aapside-master' ),
			'default'     => '#fff',
			'selectors'   => [
				"{{WRAPPER}} .navbar-area .nav-container .nav-right-content ul li .boxed-btn" => "color: {{VALUE}}"
			]
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'button_normal_border',
			'label'    => esc_html__( 'Border', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .navbar-area .nav-container .nav-right-content ul li .boxed-btn"
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover_two_style', [
			'label' => esc_html__( 'Hover', 'aapside-master' )
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'appside_button_hover_background',
			'selector'    => "{{WRAPPER}} .navbar-area .nav-container .nav-right-content ul li .boxed-btn:hover",
			'description' => esc_html__( 'button hover background', 'aapside-master' )
		] );
		$this->add_control( 'gd_two_hover_text_color', [
			'label'       => esc_html__( 'Button Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button text color', 'aapside-master' ),
			'default'     => '#333',
			'selectors'   => [
				"{{WRAPPER}} .navbar-area .nav-container .nav-right-content ul li .boxed-btn:hover" => "color: {{VALUE}}"
			]
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'button_hover_border',
			'label'    => esc_html__( 'Border', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .navbar-area .nav-container .nav-right-content ul li .boxed-btn:hover"
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->add_control( 'button_typography_divider', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'aapside-master' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .navbar-area .nav-container .nav-right-content ul li .boxed-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'button_typography',
			'label'    => esc_html__( 'Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .navbar-area .nav-container .nav-right-content ul li .boxed-btn"
		] );
		$this->end_controls_section();

		/* sticky menu styling */

		$this->start_controls_section(
			'nav_fixed_menu_dropdown_styling_section',
			[
				'label' => esc_html__( 'Sticky Nav Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'nav_fixed_menu_background_color',
			'label'       => esc_html__( 'Menu Background Color', 'aapside-master' ),
			'description' => esc_html__( 'change menu background color', 'aapside-master' ),
			"selector"    => "{{WRAPPER}} .navbar-elementor-style-one-wrapper .navbar-area.nav-fixed.navbar-default"
		] );
		$this->add_control( 'nav_fixed_menu_color', [
			'label'       => esc_html__( 'Menu Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change menu color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .navbar-area.nav-fixed .nav-container .navbar-collapse .navbar-nav li" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'nav_fixed_menu_active_color', [
			'label'       => esc_html__( 'Menu Active Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change menu active color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .navbar-area.nav-fixed .nav-container .navbar-collapse .navbar-nav li.current-menu-item" => "color: {{VALUE}}",
				"{{WRAPPER}} .navbar-area.nav-fixed .nav-container .navbar-collapse .navbar-nav li:hover" => "color: {{VALUE}}",
				"{{WRAPPER}} .navbar-area.nav-fixed .nav-container .navbar-collapse .navbar-nav li:hover > a" => "color: {{VALUE}}",
				"{{WRAPPER}} .navbar-area.nav-fixed .nav-container .navbar-collapse .navbar-nav li.current-menu-item a" => "color: {{VALUE}}"
			]
		] );
		$this->end_controls_section();

		$this->start_controls_section(
			'nav_fixed_button_styling_section',
			[
				'label' => esc_html__( 'Sticky Nav Button Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'nav_fixed_button_two_background' );

		$this->start_controls_tab( 'nav_fixed_normal_two_style', [
			'label' => esc_html__( 'Normal', 'aapside-master' )
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'nav_fixed_gd_two_background',
			'selector'    => "{{WRAPPER}} .navbar-area.nav-fixed .nav-container .nav-right-content ul li .boxed-btn",
			'description' => esc_html__( 'button background', 'aapside-master' )
		] );
		$this->add_control( 'nav_fixed_gd_two_text_color', [
			'label'       => esc_html__( 'Button Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button text color', 'aapside-master' ),
			'default'     => '#fff',
			'selectors'   => [
				"{{WRAPPER}} .navbar-area.nav-fixed .nav-container .nav-right-content ul li .boxed-btn" => "color: {{VALUE}}"
			]
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'nav_fixed_button_normal_border',
			'label'    => esc_html__( 'Border', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .navbar-area.nav-fixed .nav-container .nav-right-content ul li .boxed-btn"
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( 'nav_fixed_hover_two_style', [
			'label' => esc_html__( 'Hover', 'aapside-master' )
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'nav_fixed_appside_button_hover_background',
			'selector'    => "{{WRAPPER}} .navbar-area.nav-fixed .nav-container .nav-right-content ul li .boxed-btn:hover",
			'description' => esc_html__( 'button hover background', 'aapside-master' )
		] );
		$this->add_control( 'nav_fixed_gd_two_hover_text_color', [
			'label'       => esc_html__( 'Button Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button text color', 'aapside-master' ),
			'default'     => '#333',
			'selectors'   => [
				"{{WRAPPER}} .navbar-area.nav-fixed .nav-container .nav-right-content ul li .boxed-btn:hover" => "color: {{VALUE}}"
			]
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'nav_fixed_button_hover_border',
			'label'    => esc_html__( 'Border', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .navbar-area.nav-fixed .nav-container .nav-right-content ul li .boxed-btn:hover"
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Render Elementor widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings      = $this->get_settings_for_display();
		$site_logo_id  = $settings['logo']['id'];
		$site_logo_url = ! empty( $site_logo_id ) ? wp_get_attachment_image_src( $site_logo_id, 'full' )[0] : '';
		$site_logo_alt = ! empty( $site_logo_id ) ? get_post_meta( $site_logo_id, '_wp_attachment_image_alt', true ) : '';

		//button attribute
		$this->add_render_attribute( 'button_attr', 'class', 'boxed-btn' );
		if ( ! empty( $settings['button_link']['url'] ) ) {
			$this->add_link_attributes( 'button_attr', $settings['button_link'] );
		}

		//is_absolute
		$this->add_render_attribute( 'navbar_wrapper_class', 'class', 'navbar-area' );
		$this->add_render_attribute( 'navbar_wrapper_class', 'class', 'navbar' );
		$this->add_render_attribute( 'navbar_wrapper_class', 'class', 'navbar-expand-lg' );
		$this->add_render_attribute( 'navbar_wrapper_class', 'class', 'navbar-default' );
		if ( ! empty( $settings['is_absolute'] ) ) {
			$this->add_render_attribute( 'navbar_wrapper_class', 'class', 'navbar-absolute' );
		}
		?>
        <div class="navbar-elementor-style-one-wrapper">
            <nav <?php echo $this->get_render_attribute_string( 'navbar_wrapper_class' ) ?>>
                <div class="container nav-container">
                    <div class="responsive-mobile-menu">
                        <div class="logo-wrapper">
							<?php
							printf( '<a class="site-logo" href="%1$s"><img src="%2$s" alt="%3$s"/></a>', get_home_url(), $site_logo_url, $site_logo_alt );
							?>
                        </div>
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                                data-target="#appside_main_menu"
                                aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>
					<?php
					if ( ! empty( $settings['menu'] ) ) {
						$menu_args = [
							'container_class' => 'collapse navbar-collapse',
							'container_id'    => 'appside_main_menu',
							'menu_class'      => 'navbar-nav',
							'menu'            => $settings['menu']
						];
						if ( defined( 'APPSIDE_MASTER_SELF_PATH' ) ) {
							$menu_args['walker'] = new \Appside_Megamenu_Walker();
						}
						wp_nav_menu( $menu_args );
					}
					?>
					<?php if ( ! empty( $settings['button_status'] ) ): ?>
                        <div class="nav-right-content">
                            <ul>
                                <li class="button-wrapper">
                                    <a <?php echo $this->get_render_attribute_string( 'button_attr' ); ?>><?php echo esc_html( $settings['button_text'] ); ?></a>
                                </li>
                            </ul>
                        </div>
					<?php endif; ?>
                </div>
            </nav>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Navbar_Style_One_Widget() );