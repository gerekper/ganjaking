<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Price_Plan_Area_One extends Widget_Base {

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
		return 'appside-price-plan-area-one-widget';
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
		return esc_html__( 'Price Plan Area', 'aapside-master' );
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
		return 'eicon-editor-list-ul';
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
		$this->add_control( 'subtitle', [
			'label'       => esc_html__( 'Subtitle', 'aapside-master' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'Pricing Plans', 'aapside-master' ),
			'description' => esc_html__( 'add section subtitle', 'aapside-master' )
		] );
		$this->add_control( 'title', [
			'label'       => esc_html__( 'Title', 'aapside-master' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'Afforadble Pricing', 'aapside-master' ),
			'description' => esc_html__( 'add section title', 'aapside-master' )
		] );
		$this->add_control( 'description', [
			'label'       => esc_html__( 'Description', 'aapside-master' ),
			'type'        => Controls_Manager::TEXTAREA,
			'default'     => esc_html__( 'Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor tempor incididunt ut labore dolore magna.', 'aapside-master' ),
			'description' => esc_html__( 'add section description', 'aapside-master' )
		] );
		$this->add_control( 'month_label', [
			'label'       => esc_html__( 'Month Label', 'aapside-master' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'Month', 'aapside-master' ),
			'description' => esc_html__( 'add month tab label', 'aapside-master' )
		] );
		$this->add_control( 'year_label', [
			'label'       => esc_html__( 'Year Label', 'aapside-master' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'Year', 'aapside-master' ),
			'description' => esc_html__( 'add year tab label', 'aapside-master' )
		] );
		$this->add_control( 'popular_label', [
			'label'       => esc_html__( 'Popular Label', 'aapside-master' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'Popular', 'aapside-master' ),
			'description' => esc_html__( 'add year tab label', 'aapside-master' )
		] );
		$this->end_controls_section();

		$this->start_controls_section(
			'price_plan_section',
			[
				'label' => esc_html__( 'Tab Content', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control( 'price_plan_items', [
			'label'       => esc_html__( 'Price Plan Item', 'aapside-master' ),
			'type'        => Controls_Manager::REPEATER,
			'default'     => [
				[
					'title'    => esc_html__( 'Basic', 'aapside-master' ),
					'features' => esc_html__( '5 Analyaer;3 Month support', 'aapside-master' ),
				]
			],
			'fields'      => [
				[
					'name'        => 'popular',
					'label'       => esc_html__( 'Popular', 'aapside-master' ),
					'type'        => Controls_Manager::SWITCHER,
					'default'     => 'no',
					'description' => esc_html__( 'set it as a popular item', 'aapside-master' )
				],
				[
					'name'        => 'type',
					'label'       => esc_html__( 'Plan Type', 'aapside-master' ),
					'type'        => Controls_Manager::SELECT,
					'options'     => array(
						'monthly' => esc_html__( 'Monthly', 'aapside-master' ),
						'yearly'  => esc_html__( 'Yearly', 'aapside-master' )
					),
					'default'     => 'monthly',
					'description' => esc_html__( 'selcet plan type', 'aapside-master' )
				],
				[
					'name'        => 'title',
					'label'       => esc_html__( 'Title', 'aapside-master' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Business', 'aapside-master' ),
					'description' => esc_html__( 'Enter title', 'aapside-master' )
				],
				[
					'name'        => 'price',
					'label'       => esc_html__( 'Price', 'aapside-master' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( '$10', 'aapside-master' ),
					'description' => esc_html__( 'Enter price', 'aapside-master' )
				],
				[
					'name'        => 'month',
					'label'       => esc_html__( 'Month', 'aapside-master' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( '/Mo', 'aapside-master' ),
					'description' => esc_html__( 'Enter month/year', 'aapside-master' )
				],
				[
					'name'        => 'features',
					'label'       => esc_html__( 'Features', 'aapside-master' ),
					'type'        => Controls_Manager::TEXTAREA,
					'defult'      => esc_html__( '5 Analyser ; 3 Month Support', 'aapside-master' ),
					'description' => esc_html__( 'Enter features separate by (;).', 'aapside-master' )
				],
				[
					'name'        => 'btn_text',
					'label'       => esc_html__( 'Button Text', 'aapside-master' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Get Started', 'aapside-master' ),
					'description' => esc_html__( 'Enter button text', 'aapside-master' )
				],
				[
					'name'        => 'btn_link',
					'label'       => esc_html__( 'Button Link', 'aapside-master' ),
					'type'        => Controls_Manager::URL,
					'default'     => [
						'url' => '#'
					],
					'description' => esc_html__( 'Enter button link', 'aapside-master' )
				],
			],
			'title_field' => "{{title}}"
		] );
		$this->end_controls_section();

		$this->start_controls_section( 'styling_settings', [
			'label' => esc_html__( 'Pricing Section Styling', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		] );
		$this->add_control( 'subtitle_color', [
			'label'     => esc_html__( 'Subtitle Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .price-plan-left-content .section-title .subtitle" => "color:{{VALUE}}"
			]
		] );
		$this->add_control( 'title_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .price-plan-left-content .section-title .title" => "color:{{VALUE}}"
			]
		] );
		$this->add_control( 'description_color', [
			'label'     => esc_html__( 'Description Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .price-plan-left-content .section-title p" => "color:{{VALUE}}"
			]
		] );
		$this->add_control( 'divider', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'subtitle_typography',
			'label'    => esc_html__( 'Subtitle Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .price-plan-left-content .section-title .subtitle"
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'label'    => esc_html__( 'Title Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .price-plan-left-content .section-title .title"
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'description_typography',
			'label'    => esc_html__( 'Description Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .price-plan-left-content .section-title p"
		] );

		$this->end_controls_section();

		/* tab button styling start */
		$this->start_controls_section( 'tab_active Button', [
			'label' => esc_html__( 'Tabs Button Styling', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		] );

		//start tab button tabs
		$this->start_controls_tabs(
			'tab_button_style_tabs'
		);

		$this->start_controls_tab(
			'tab_button_style_normal_tab',
			[
				'label' => __( 'Normal', 'aapside-master' ),
			]
		);

		$this->add_control( 'button_color', [
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Color', 'aapside-master' ),
			'selectors' => [
				"{{WRAPPER}} .price-plan-tab-nav .nav-tabs .nav-item" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'divider_01', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'button_background',
			'label'    => esc_html__( 'Background', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .price-plan-tab-nav .nav-tabs .nav-item"
		] );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_style_hover_tab',
			[
				'label' => __( 'Active', 'aapside-master' ),
			]
		);
		$this->add_control( 'button_hover_color', [
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Color', 'aapside-master' ),
			'selectors' => [
				"{{WRAPPER}} .price-plan-tab-nav .nav-tabs .nav-item.active" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'divider_02', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'button_hover_background',
			'label'    => esc_html__( 'Background', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .price-plan-tab-nav .nav-tabs .nav-item.active"
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();
		//end tab button tabs

		$this->add_control( 'tabs_button_divider', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'button_typography',
			'label'    => esc_html__( 'Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .price-plan-tab-nav .nav-tabs .nav-item"
		] );
		$this->end_controls_section();
		/* tab button styling end */

		/* price plan styling start */
		$this->start_controls_section( 'price_plan_styling_settings', [
			'label' => esc_html__( 'Price Plan Styling', 'aapside-master' ),
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

		$this->add_control('price_plan_title_color',[
			'label' => esc_html__('Title Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-03 .price-header .name" => "color: {{VALUE}}"
			]
		]);

		$this->add_control('price_color',[
			'label' => esc_html__('Price Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-03 .price-header .price-wrap .month" => "color:{{VALUE}}",
				"{{WRAPPER}} .single-price-plan-03 .price-header .price-wrap .price" => "color:{{VALUE}}",
			]
		]);
		$this->add_control('features_color',[
			'label' => esc_html__('Features Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-03 .price-body ul li" => "color: {{VALUE}}"
			]
		]);
		$this->add_control( 'tabs_button_divider_01', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_control('price_plan_button_color',[
			'label' => esc_html__('Button Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-03 .price-footer .boxed-btn" => "color: {{VALUE}}"
			]
		]);
		$this->add_group_control(Group_Control_Background::get_type(),[
			'name' => 'price_plan_button_background',
			'label' => esc_html__('Button Background','aapside-master'),
			'selector' => "{{WRAPPER}} .single-price-plan-03 .price-footer .boxed-btn"
		]);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'price_plan_style_hover_tab',
			[
				'label' => __( 'Hover', 'aapside-master' ),
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),[
			'name' => 'price_plan_border_color',
			'label' => esc_html__('Hover/Active Border','aapside-master'),
			'selector' => "{{WRAPPER}} .single-price-plan-03:after"
		]);
		$this->add_control( 'tabs_button_divider_02', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control(Group_Control_Background::get_type(),[
			'name' => 'price_plan_popular_background',
			'label' => esc_html__('Popular Background','aapside-master'),
			'selector' => "{{WRAPPER}} .single-price-plan-03 .popular"
		]);
		$this->add_control('popular_color',[
			'label' => esc_html__('Popular Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-03 .popular" => "color: {{VALUE}}"
			]
		]);

		$this->add_control( 'tabs_button_divider_03', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_control('price_plan_button_hover_color',[
			'label' => esc_html__('Button Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-price-plan-03 .price-footer .boxed-btn:hover" => "color: {{VALUE}}",
				"{{WRAPPER}} .single-price-plan-03.popular .price-footer .boxed-btn" => "color: {{VALUE}}",
				"{{WRAPPER}} .single-price-plan-03:hover .price-footer .boxed-btn" => "color: {{VALUE}}",
			]
		]);
		$this->add_group_control(Group_Control_Background::get_type(),[
			'name' => 'price_plan_button_hover_background_color',
			'label' => esc_html__('Button Background','aapside-master'),
			'selector' => "{{WRAPPER}} .single-price-plan-03 .price-footer .boxed-btn:hover, {{WRAPPER}} .single-price-plan-03.popular .price-footer .boxed-btn, {{WRAPPER}} .single-price-plan-03:hover .price-footer .boxed-btn"
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->add_control( 'tabs_button_divider_04', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'popular_typography',
			'label' => esc_html__('Popular Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .single-price-plan-03 .popular"
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'price_plan_title_typography',
			'label' => esc_html__('Title Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .single-price-plan-03 .price-header .name "
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'features_typography',
			'label' => esc_html__('Features Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .single-price-plan-03 .price-body ul"
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'price_plan_button_typography',
			'label' => esc_html__('Button Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .single-price-plan-03 .price-footer .boxed-btn"
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'price_typography',
			'label' => esc_html__('Price Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .single-price-plan-03 .price-header .price-wrap"
		]);
		$this->end_controls_section();
		/* price plan styling end */

		$this->start_controls_section(
			'price_plan_background_overlay',
			[
				'label' => __( 'Background Shape Styling', 'aapside-master' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),[
			'name' => 'background_shape_color',
			'label' => esc_html__('Background Shape Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selector' => "{{WRAPPER}} .price-plan-tab-content:after"
		]);
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
		$settings         = $this->get_settings_for_display();
		$price_plan_items = $settings['price_plan_items'];
		?>
        <section class="appside-price-plan-area">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-lg-5">
                        <div class="price-plan-left-content">
                            <div class="section-title">
                                <span class="subtitle"><?php echo esc_html( $settings['subtitle'] ); ?></span>
                                <h2 class="title"><?php echo esc_html( $settings['title'] ); ?></h2>
                                <p><?php echo esc_html( $settings['description'] ); ?></p>
                                <div class="price-plan-tab-nav">
                                    <div class="nav nav-tabs" role="tablist">
                                        <a class="nav-item nav-link active" id="nav-monthly-tab" data-toggle="tab"
                                           href="#nav-monthly"
                                           role="tab"><?php echo esc_html($settings['month_label']); ?></a>
                                        <a class="nav-item nav-link" id="nav-yearly-tab" data-toggle="tab"
                                           href="#nav-yearly"
                                           role="tab"><?php echo esc_html($settings['year_label']); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="nav-monthly" role="tabpanel"
                                 aria-labelledby="nav-monthly-tab">
                                <div class="price-plan-tab-content">
                                    <div class="row">
										<?php
										$a = 0;
										foreach ( $price_plan_items as $price_plan ):
											if ( 'yearly' == $price_plan['type'] ) {
												continue;
											}
											if ( $a == 0 ):
												$a ++;
												?>
                                                <div class="col-lg-6">
                                                    <div class="price-plan-single-item-area ">
                                                        <div class="single-price-plan-03 <?php echo 'yes' == $price_plan['popular'] ? 'popular' : '' ?>">
															<?php
															if ( 'yes' == $price_plan['popular'] ) {
																printf( '<span class="popular">%s</span>', esc_html($settings['popular_label']) );
															}
															?>

                                                            <div class="price-header">
                                                                <h4 class="name"><?php echo esc_html( $price_plan['title'] ) ?></h4>
                                                                <div class="price-wrap">
                                                                    <span class="price"><?php echo esc_html( $price_plan['price'] ) ?></span>
                                                                    <span class="month"><?php echo esc_html( $price_plan['month'] ) ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="price-body">
                                                                <ul>
																	<?php
																	$feature = explode( ';', $price_plan['features'] );
																	foreach ( $feature as $item ) {
																		printf( '<li>%1$s</li>', $item );
																	}

																	?>
                                                                </ul>
                                                            </div>
                                                            <div class="price-footer">
                                                                <a href="<?php echo esc_url( $price_plan['btn_link']['url'] ) ?>"
                                                                   class="boxed-btn"><?php echo esc_html( $price_plan['btn_text'] ) ?></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
											<?php endif;endforeach; ?>

                                        <div class="col-lg-6">
											<?php
											$i = 0;
											foreach ( $price_plan_items as $price_plan ):
												if ( 'yearly' == $price_plan['type'] ) {
													continue;
												}
												if ( $i == 0 ) {
													$i ++;
													continue;
												}

												$extra_class = 1 == $i ? 'margin-top-30' : '';
												?>
                                                <div class="single-price-plan-03 <?php echo esc_attr( $extra_class ) ?> <?php echo 'yes' == $price_plan['popular'] ? 'popular' : '' ?>">
													<?php
													if ( 'yes' == $price_plan['popular'] ) {
														printf( '<span class="popular">%s</span>', esc_html($settings['popular_label']) );
													}
													?>

                                                    <div class="price-header">
                                                        <h4 class="name"><?php echo esc_html( $price_plan['title'] ) ?></h4>
                                                        <div class="price-wrap">
                                                            <span class="price"><?php echo esc_html( $price_plan['price'] ) ?></span>
                                                            <span class="month"><?php echo esc_html( $price_plan['month'] ) ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="price-body">
                                                        <ul>
															<?php
															$feature = explode( ';', $price_plan['features'] );
															foreach ( $feature as $item ) {
																printf( '<li>%1$s</li>', $item );
															}

															?>
                                                        </ul>
                                                    </div>
                                                    <div class="price-footer">
                                                        <a href="<?php echo esc_url( $price_plan['btn_link']['url'] ) ?>"
                                                           class="boxed-btn"><?php echo esc_html( $price_plan['btn_text'] ) ?></a>
                                                    </div>
                                                </div>
											<?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-yearly" role="tabpanel" aria-labelledby="nav-yearly-tab">
                                <div class="price-plan-tab-content">
                                    <div class="row">
										<?php
										$b = 0;
										foreach ( $price_plan_items as $price_plan ):
											if ( 'monthly' == $price_plan['type'] ) {
												continue;
											}
											if ( $b == 0 ):
												$b ++;
												?>
                                                <div class="col-lg-6">
                                                    <div class="price-plan-single-item-area">
                                                        <div class="single-price-plan-03 <?php echo 'yes' == $price_plan['popular'] ? 'popular' : '' ?>">
															<?php
															if ( 'yes' == $price_plan['popular'] ) {
																printf( '<span class="popular">%s</span>', esc_html($settings['popular_label'])  );
															}
															?>

                                                            <div class="price-header">
                                                                <h4 class="name"><?php echo esc_html( $price_plan['title'] ) ?></h4>
                                                                <div class="price-wrap">
                                                                    <span class="price"><?php echo esc_html( $price_plan['price'] ) ?></span>
                                                                    <span class="month"><?php echo esc_html( $price_plan['month'] ) ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="price-body">
                                                                <ul>
																	<?php
																	$feature = explode( ';', $price_plan['features'] );
																	foreach ( $feature as $item ) {
																		printf( '<li>%1$s</li>', $item );
																	}
																	?>
                                                                </ul>
                                                            </div>
                                                            <div class="price-footer">
                                                                <a href="<?php echo esc_url( $price_plan['btn_link']['url'] ) ?>"
                                                                   class="boxed-btn"><?php echo esc_html( $price_plan['btn_text'] ) ?></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
											<?php endif;endforeach; ?>

                                        <div class="col-lg-6">
											<?php
											$j = 0;
											foreach ( $price_plan_items as $price_plan ):
												if ( 'monthly' == $price_plan['type'] ) {
													continue;
												}
												if ( $j == 0 ) {
													$j ++;
													continue;
												}

												$extra_class = 1 == $j ? 'margin-top-30' : '';
												?>
                                                <div class="single-price-plan-03 <?php echo esc_attr( $extra_class ) ?> <?php echo 'yes' == $price_plan['popular'] ? 'popular' : '' ?>">
													<?php
													if ( 'yes' == $price_plan['popular'] ) {
														printf( '<span class="popular">%s</span>', esc_html($settings['popular_label']) );
													}
													?>

                                                    <div class="price-header">
                                                        <h4 class="name"><?php echo esc_html( $price_plan['title'] ) ?></h4>
                                                        <div class="price-wrap">
                                                            <span class="price"><?php echo esc_html( $price_plan['price'] ) ?></span>
                                                            <span class="month"><?php echo esc_html( $price_plan['month'] ) ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="price-body">
                                                        <ul>
															<?php
															$feature = explode( ';', $price_plan['features'] );
															foreach ( $feature as $item ) {
																printf( '<li>%1$s</li>', $item );
															}

															?>
                                                        </ul>
                                                    </div>
                                                    <div class="price-footer">
                                                        <a href="<?php echo esc_url( $price_plan['btn_link']['url'] ) ?>"
                                                           class="boxed-btn"><?php echo esc_html( $price_plan['btn_text'] ) ?></a>
                                                    </div>
                                                </div>
											<?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Price_Plan_Area_One() );