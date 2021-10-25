<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Price_Plan_Two_Widget extends Widget_Base {

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
		return 'appside-price-plan-two-widget';
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
		return esc_html__( 'Price Plan: 02', 'aapside-master' );
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
		$this->add_control( 'title', [
			'label'       => esc_html__( 'Title', 'aapside-master' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'Primary Plan', 'aapside-master' ),
			'description' => esc_html__( 'enter title', 'aapside-master' )
		] );
		$this->add_control( 'featured', [
			'label'       => esc_html__( 'Featured', 'aapside-master' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => 'no',
			'description' => esc_html__( 'enable featured', 'aapside-master' )
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
			'default'     => esc_html__( '/Mo', 'aapside-master' ),
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

		$this->start_controls_section( 'styling_section', [
			'label' => esc_html__( 'Styling Settings', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		] );
		$this->add_control( 'title_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'     => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-02 .price-header .name" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'price_color', [
			'label'     => esc_html__( 'Price Color', 'aapside-master' ),
			'type'     => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-02 .price-header .price-wrap .price" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'month_color', [
			'label'     => esc_html__( 'Month Color', 'aapside-master' ),
			'type'     => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-02 .price-header .price-wrap .month" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'features_color', [
			'label'     => esc_html__( 'Features Color', 'aapside-master' ),
			'type'     => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-02 .price-body ul" => "color: {{VALUE}}"
			]
		] );
		$this->end_controls_section();

		/* button styling start */
		$this->start_controls_section( 'button_styling_section', [
			'label' => esc_html__( 'Button Styling', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		] );
		$this->start_controls_tabs(
			'button_style_tabs'
		);

		$this->start_controls_tab(
			'button_style_normal_tab',
			[
				'label' => __( 'Normal', 'aapside-master' ),
			]
		);
		$this->add_control( 'button_normal_color', [
			'label'     => esc_html__( 'Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-02 .price-footer .boxed-btn" => "color: {{VALUE}}"
			]
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'label'    => esc_html__( 'Button Background', 'aapside-master' ),
			'name'     => 'button_normal_bg_color',
			'selector' => "{{WRAPPER}} .single-price-plan-02 .price-footer .boxed-btn"
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'label'    => esc_html__( 'Border', 'aapside-master' ),
			'name'     => 'button_normal_border',
			'selector' => "{{WRAPPER}} .single-price-plan-02 .price-footer .boxed-btn"
		] );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_style_hover_tab',
			[
				'label' => __( 'Hover', 'aapside-master' ),
			]
		);
		$this->add_control( 'button_hover_color', [
			'label'     => esc_html__( 'Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-02 .price-footer .boxed-btn:hover" => "color: {{VALUE}}"
			]
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'label'    => esc_html__( 'Button Background', 'aapside-master' ),
			'name'     => 'button_hover_bg_color',
			'selector' => "{{WRAPPER}} .single-price-plan-02 .price-footer .boxed-btn:hover"
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'label'    => esc_html__( 'Border', 'aapside-master' ),
			'name'     => 'button_hover_border',
			'selector' => "{{WRAPPER}} .single-price-plan-02 .price-footer .boxed-btn:hover"
		] );

		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->add_control( 'button_divider', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_responsive_control( 'button_border_radius', [
			'label'     => esc_html__( 'Border Radius', 'aapside-master' ),
			'type'      => Controls_Manager::DIMENSIONS,
			'units'     => [ 'px', '%', 'em' ],
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-02 .price-footer .boxed-btn" => "border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};"
			]
		] );
		$this->end_controls_section();
		/* button styling end */

		/* border color start */
		$this->start_controls_section( 'hover_border_section', [
			'label' => esc_html__( 'Hover Border Style', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		] );

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'hover_background',
			'label'    => esc_html__( 'Active Border Color', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .single-price-plan-02:after"
		] );

		$this->end_controls_section();
		/* border color end */


		/* button styling start */
		$this->start_controls_section( 'typography_section', [
			'label' => esc_html__( 'Typography Settings', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'label'    => esc_html__( 'Title Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .single-price-plan-02 .price-header .name"
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'price_typography',
			'label'    => esc_html__( 'Price Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .single-price-plan-02 .price-header .price-wrap"
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'features_typography',
			'label'    => esc_html__( 'Features Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .single-price-plan-02 .price-body ul"
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'button_typography',
			'label'    => esc_html__( 'Button Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .single-price-plan-02 .price-footer .boxed-btn"
		] );
		$this->end_controls_section();
		/* button styling end */
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
		$featured          = $settings['featured'] == 'yes' ? 'featured' : '';
		?>
        <div class="single-price-plan-02 <?php echo esc_attr( $featured ); ?>">
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
                <a href="<?php echo esc_url( $settings['btn_link']['url'] ); ?>"
                   class="boxed-btn"><?php echo esc_html( $settings['btn_text'] ); ?></a>
            </div>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Price_Plan_Two_Widget() );