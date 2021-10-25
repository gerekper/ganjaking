<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Featrue_Box_Five_Widget extends Widget_Base {

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
		return 'appside-feature-five-box-widget';
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
		return esc_html__( 'Feature Box Five', 'aapside-master' );
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
		return 'eicon-icon-box';
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
		$this->add_control( 'features_items', [
			'label'       => esc_html__( 'Feature Item', 'aapside-master' ),
			'type'        => Controls_Manager::REPEATER,
			'default'     => [
				[
					'theme'       => 'theme-01',
					'title'       => esc_html__( 'Clean Design', 'aapside-master' ),
					'icon'        => 'flaticon-vector',
					'description' => esc_html__( 'Consectetur adipiscing elit, sed do eiusmod tempor  tempor incididunt', 'aapside-master' ),
				],
				[
					'theme'       => 'theme-02',
					'title'       => esc_html__( 'Fully Respnosive', 'aapside-master' ),
					'icon'        => 'flaticon-responsive',
					'description' => esc_html__( 'Consectetur adipiscing elit, sed do eiusmod tempor  tempor incididunt', 'aapside-master' ),
				]
			],
			'fields'      => [
				[
					'name'        => 'theme',
					'label'       => esc_html__( 'Theme', 'aapside-master' ),
					'type'        => Controls_Manager::SELECT,
					'description' => esc_html__( 'select theme.', 'aapside-master' ),
					'options'     => array(
						'theme-01' => esc_html__( 'Theme One' ),
						'theme-02' => esc_html__( 'Theme Two' ),
						'theme-03' => esc_html__( 'Theme Three' ),
					),
					'default'     => 'theme-01'
				],
				[
					'name'        => 'title',
					'label'       => esc_html__( 'Title', 'aapside-master' ),
					'type'        => Controls_Manager::TEXT,
					'description' => esc_html__( 'enter title.', 'aapside-master' ),
					'default'     => esc_html__( 'Clean Design', 'aapside-master' )
				],
				[
					'name'        => 'description',
					'label'       => esc_html__( 'Description', 'aapside-master' ),
					'type'        => Controls_Manager::TEXTAREA,
					'default'     => esc_html__( 'Consectetur adipiscing elit, sed do eiusmod tempor  tempor incididunt', 'aapside-master' ),
					'description' => esc_html__( 'enter description.', 'aapside-master' ),
				],
				[
					'name'        => 'icon',
					'label'       => esc_html__( 'Icon', 'aapside-master' ),
					'type'        => Controls_Manager::ICON,
					'description' => esc_html__( 'select icon.', 'aapside-master' ),
					'default'     => 'flaticon-vector'
				]
			],
			'title_field' => "{{title}}"
		] );


		$this->end_controls_section();

		$this->start_controls_section(
			'typography_section',
			[
				'label' => esc_html__( 'Typography Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'label'    => esc_html__( 'Title Typography' ),
			'name'     => 'title_typography',
			'selector' => "{{WRAPPER}} .feature-list-04 .single-feature-list-item-04 .content .title"
		] );
		$this->add_control( 'styling_divider', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'label'    => esc_html__( 'Description Typography' ),
			'name'     => 'description_typography',
			'selector' => "{{WRAPPER}} .feature-list-04 .single-feature-list-item-04 .content p"
		] );
		$this->end_controls_section();
		
		/* theme one icon color start */
		$this->start_controls_section(
			'theme_one_section',
			[
				'label' => __( 'Theme One Styling', 'aapside-master' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'theme_one_style_tabs'
		);

		$this->start_controls_tab(
			'theme_01_style_normal_tab',
			[
				'label' => __( 'Normal', 'aapside-master' ),
			]
		);
		$this->add_control( 'theme_01_icon_color', [
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-01.single-feature-list-item-04 .icon" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'theme_01_title_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-01.single-feature-list-item-04 .content .title" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'theme_01_description_color', [
			'label'     => esc_html__( 'Description Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-01.single-feature-list-item-04 .content p" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'styling_divider_01', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'  => 'feature_list_background',
			'title' => esc_html__( 'Background', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .feature-list-04 .theme-01.single-feature-list-item-04"
		] );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_hover_tab',
			[
				'label' => __( 'Hover', 'aapside-master' ),
			]
		);

		$this->add_control( 'theme_01_icon_hover_color', [
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-01.single-feature-list-item-04:hover .icon" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'theme_01_title_hover_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-01.single-feature-list-item-04:hover .content .title" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'theme_01_description_hover_color', [
			'label'     => esc_html__( 'Description Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-01.single-feature-list-item-04:hover .content p" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'styling_hover_divider', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'  => 'theme_01_feature_list_hover_background',
			'title' => esc_html__( 'Background', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .feature-list-04 .theme-01.single-feature-list-item-04:hover"
		] );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
		/* theme one icon color end */

		/* theme two icon color start */
		$this->start_controls_section(
			'theme_two_section',
			[
				'label' => __( 'Theme Two Styling', 'aapside-master' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'theme_two_style_tabs'
		);

		$this->start_controls_tab(
			'theme_02_style_normal_tab',
			[
				'label' => __( 'Normal', 'aapside-master' ),
			]
		);
		$this->add_control( 'theme_02_icon_color', [
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-02.single-feature-list-item-04 .icon" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'theme_02_title_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-02.single-feature-list-item-04 .content .title" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'theme_02_description_color', [
			'label'     => esc_html__( 'Description Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-02.single-feature-list-item-04 .content p" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'styling_divider_02', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'  => 'feature_theme_02_list_background',
			'title' => esc_html__( 'Background', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .feature-list-04 .theme-02.single-feature-list-item-04"
		] );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_theme_02_hover_tab',
			[
				'label' => __( 'Hover', 'aapside-master' ),
			]
		);

		$this->add_control( 'theme_02_icon_hover_color', [
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-02.single-feature-list-item-04:hover .icon" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'theme_02_title_hover_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-02.single-feature-list-item-04:hover .content .title" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'theme_02_description_hover_color', [
			'label'     => esc_html__( 'Description Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-02.single-feature-list-item-04:hover .content p" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'styling_hover_divider_02', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'  => 'theme_02_feature_list_hover_background',
			'title' => esc_html__( 'Background', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .feature-list-04 .theme-02.single-feature-list-item-04:hover"
		] );

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->end_controls_section();
		/* theme two icon end */

		/* theme two icon color start */
		$this->start_controls_section(
			'theme_three_section',
			[
				'label' => __( 'Theme Three Styling', 'aapside-master' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'theme_three_style_tabs'
		);

		$this->start_controls_tab(
			'theme_03_style_normal_tab',
			[
				'label' => __( 'Normal', 'aapside-master' ),
			]
		);
		$this->add_control( 'theme_03_icon_color', [
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-03.single-feature-list-item-04 .icon" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'theme_03_title_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-03.single-feature-list-item-04 .content .title" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'theme_03_description_color', [
			'label'     => esc_html__( 'Description Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-03.single-feature-list-item-04 .content p" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'styling_divider_03', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'  => 'feature_theme_03_list_background',
			'title' => esc_html__( 'Background', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .feature-list-04 .theme-03.single-feature-list-item-04"
		] );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'theme_03_style_hover_tab',
			[
				'label' => __( 'Hover', 'aapside-master' ),
			]
		);

		$this->add_control( 'theme_03_icon_hover_color', [
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-03.single-feature-list-item-04:hover .icon" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'theme_03_title_hover_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-03.single-feature-list-item-04:hover .content .title" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'theme_03_description_hover_color', [
			'label'     => esc_html__( 'Description Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .feature-list-04 .theme-03.single-feature-list-item-04:hover .content p" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'styling_hover_divider_03', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'  => 'theme_03_feature_list_hover_background',
			'title' => esc_html__( 'Background', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .feature-list-04 .theme-03.single-feature-list-item-04:hover"
		] );

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->end_controls_section();
		/* theme three icon color end */
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
		$all_feature_list = $settings['features_items'];
		?>
        <ul class="feature-list-04">
			<?php foreach ( $all_feature_list as $list ): ?>
                <li class="single-feature-list-item-04 <?php echo esc_attr( $list['theme'] ); ?>">
                    <div class="icon">
                        <i class="<?php echo esc_attr( $list['icon'] ); ?>"></i>
                    </div>
                    <div class="content">
                        <h4 class="title"><?php echo esc_attr( $list['title'] ); ?></h4>
                        <p><?php echo esc_attr( $list['description'] ); ?></p>
                    </div>
                </li>
			<?php endforeach; ?>
        </ul>

		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Featrue_Box_Five_Widget() );