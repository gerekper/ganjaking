<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Price_Plan_One_Widget extends Widget_Base {

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
		return 'appside-price-plan-one-widget';
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
		return esc_html__( 'Price Plan: 01', 'aapside-master' );
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
		return 'eicon-price-table';
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
		return [ 'appside_widgets' ];
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
		$this->add_control( 'theme', [

			'label'       => esc_html__( 'Theme', 'aapside-master' ),
			'type'        => Controls_Manager::SELECT,
			'options'     => array(
				''      => esc_html__( 'Black', 'aapside-master' ),
				'white' => esc_html__( 'White', 'aapside-master' )
			),
			'default'     => '',
			'description' => esc_html__( 'set theme', 'aapside-master' )
		] );
		$this->add_control( 'title', [
			'label'       => esc_html__( 'Title', 'aapside-master' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'Primary Plan', 'aapside-master' ),
			'description' => esc_html__( 'enter title', 'aapside-master' )
		] );
		$this->add_control( 'price', [
			'label'       => esc_html__( 'Price', 'aapside-master' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( '250', 'aapside-master' ),
			'description' => esc_html__( 'enter price', 'aapside-master' )
		] );
		$this->add_control( 'sign', [
			'label'       => esc_html__( 'Sign', 'aapside-master' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( '$', 'aapside-master' ),
			'description' => esc_html__( 'enter sign', 'aapside-master' )
		] );
		$this->add_control( 'month', [
			'label'       => esc_html__( 'Month', 'aapside-master' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( '/mo', 'aapside-master' ),
			'description' => esc_html__( 'enter month', 'aapside-master' )
		] );
		$this->add_control( 'btn_text', [
			'label'       => esc_html__( 'Button Text', 'aapside-master' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'Get Started', 'aapside-master' ),
			'description' => esc_html__( 'enter button text', 'aapside-master' )
		] );
		$this->add_control( 'btn_link', [
			'label'       => esc_html__( 'Button Link', 'aapside-master' ),
			'type'        => Controls_Manager::URL,
			'default'     => array(
				'url' => '#'
			),
			'description' => esc_html__( 'enter button link', 'aapside-master' )
		] );

		$this->add_control( 'feature_items', [
			'label'   => esc_html__( 'Feature Item', 'aapside-master' ),
			'type'    => Controls_Manager::REPEATER,
			'default' => [
				[
					'feature' => esc_html__( '5 Analyzer', 'aapside-master' )
				],
			],
			'fields'  => [
				[
					'name'        => 'feature',
					'label'       => esc_html__( 'Feature', 'aapside-master' ),
					'type'        => Controls_Manager::TEXT,
					'description' => esc_html__( 'enter feature item.', 'aapside-master' ),
					'default'     => esc_html__( '5 Analyzer', 'aapside-master' )
				]
			],
		] );
		$this->end_controls_section();
		$this->start_controls_section( 'price_plan_styling_section', [
			'label' => esc_html__( 'Styling Settings', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		] );

		$this->start_controls_tabs(
			'price_plan_style_tabs'
		);

		$this->start_controls_tab(
			'price_plan_style_normal_tab',
			[
				'label' => __( 'Normal', 'aapside-master' ),
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'price_plan_background',
			'label'    => esc_html__( 'Price Plan Background ', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .single-price-plan-01"
		] );
		$this->add_control( 'title_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-01 .price-header .name" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'price_color', [
			'label'     => esc_html__( 'Price Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-01 .price-header .price-wrap .price" => "color: {{VALUE}}",
				"{{WRAPPER}} .single-price-plan-01 .price-header .price-wrap .month" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'features_color', [
			'label'     => esc_html__( 'Features Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-01 .price-body ul li" => "color: {{VALUE}}"
			]
		] );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'price_plan_style_hover_tab',
			[
				'label' => __( 'Hover', 'aapside-master' ),
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'price_plan_hover_background',
			'label'    => esc_html__( 'Price Plan Background ', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .single-price-plan-01:hover"
		] );
		$this->add_control( 'title_hover_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-01:hover .price-header .name" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'price_hover_color', [
			'label'     => esc_html__( 'Price Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-01:hover .price-header .price-wrap .price" => "color: {{VALUE}}",
				"{{WRAPPER}} .single-price-plan-01:hover .price-header .price-wrap .month" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'features_hover_color', [
			'label'     => esc_html__( 'Features Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-01:hover .price-body ul li" => "color: {{VALUE}}"
			]
		] );

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->end_controls_section();
		/* button styling */
		$this->start_controls_section( 'price_plan_button_section', [
			'label' => esc_html__( 'Button Settings', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		] );

		$this->start_controls_tabs( 'button_styling' );
		$this->start_controls_tab( 'normal_style', [
			'label' => esc_html__( 'Button Normal', "appside-master" )
		] );
		$this->add_control( 'button_normal_color', [
			'label'     => esc_html__( 'Button Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-2" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'divider_01', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'button_background',
			'label'    => esc_html__( 'Button Background ', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-2"
		] );
		$this->add_control( 'divider_02', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'price_plan_button_border',
			'label'    => esc_html__( 'Border', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-2"
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover_style', [
			'label' => esc_html__( 'Button Hover', "appside-master" )
		] );
		$this->add_control( 'button_hover_normal_color', [
			'label'     => esc_html__( 'Button Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-2:hover" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'divider_03', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'button_hover_background',
			'label'    => esc_html__( 'Button Background ', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-2:hover"
		] );
		$this->add_control( 'divider_04', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'price_plan_hover_button_border',
			'label'    => esc_html__( 'Border', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-2:hover"
		] );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control( 'divider_05', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_control(
			'button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'aapside-master' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-2' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		/* button styling end */

		/* typography settings start */
		$this->start_controls_section('typography_settings',[
			'label' => esc_html__('Typography Settings','aapside-master'),
			'tab' => Controls_Manager::TAB_STYLE
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'title_typography',
			'label' => esc_html__('Title Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .single-price-plan-01 .price-header .name"
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'price_typography',
			'label' => esc_html__('Price Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .single-price-plan-01 .price-header .price-wrap .price"
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'features_typography',
			'label' => esc_html__('Features Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .single-price-plan-01 .price-body ul"
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'button_typography',
			'label' => esc_html__('Button Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-2"
		]);
		$this->end_controls_section();
		/* typography settings end */
	}

	/**
	 * Render Elementor widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings          = $this->get_settings_for_display();
		$all_feature_items = $settings['feature_items'];
		?>
        <div class="single-price-plan-01 <?php echo esc_attr( $settings['theme'] ); ?>">
            <div class="price-header">
                <h4 class="name"><?php echo esc_html( $settings['title'] ); ?></h4>
                <div class="price-wrap">
                    <span class="price"><?php echo esc_html( $settings['sign'] ); ?><?php echo esc_html( $settings['price'] ); ?></span>
                    <span class="month"><?php echo esc_html( $settings['month'] ); ?></span>
                </div>
            </div>
            <div class="price-body">
                <ul>
					<?php foreach ( $all_feature_items as $item ): ?>
                        <li><?php echo esc_html( $item['feature'] ) ?></li>
					<?php endforeach; ?>
                </ul>
            </div>
            <div class="price-footer">
                <div class="btn-wrapper">
                    <a href="<?php echo esc_url( $settings['btn_link']['url'] ); ?>"
                       class="boxed-btn btn-rounded gd-bg-2"><?php echo esc_html( $settings['btn_text'] ); ?></a>
                </div>
            </div>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Price_Plan_One_Widget() );