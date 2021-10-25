<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Featrue_Box_Four_Widget extends Widget_Base {

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
		return 'appside-feature-four-box-widget';
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
		return esc_html__( 'Feature Box Four', 'aapside-master' );
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
					'theme'       => 'icon-bg-1',
					'title'       => esc_html__( 'Clean Design', 'aapside-master' ),
					'icon'        => 'flaticon-vector',
					'description' => esc_html__( 'Consectetur adipiscing elit, sed do eiusmod tempor  tempor incididunt', 'aapside-master' ),
				],
				[
					'theme'       => 'icon-bg-2',
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
						'icon-bg-1' => esc_html__( 'Gradient One' ),
						'icon-bg-2' => esc_html__( 'Gradient Two' ),
						'icon-bg-3' => esc_html__( 'Gradient Three' ),
						'icon-bg-4' => esc_html__( 'Gradient Four' ),
					),
					'default'     => 'icon-bg-1'
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
			'odd_feature_box_style_section',
			[
				'label' => esc_html__( 'Feature Box ODD Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control( 'odd_background_color', [
			'label'       => esc_html__( 'ODD Background Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change feature box ODD item background color', 'aapside-master' ),
			'default'     => '#e8f2f9',
			'selectors'   => [
				"{{WRAPPER}} .feature-list.style-03 .single-feature-list:nth-child(odd)" => "background-color: {{VALUE}}"
			]
		] );
		$this->add_control( 'odd_title_color', [
			'label'       => esc_html__( 'Title Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change feature box title color', 'aapside-master' ),
			'default'     => '#1c144e',
			'selectors'   => [
				"{{WRAPPER}} .single-feature-list:nth-child(odd) .content .title" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'odd_text_color', [
			'label'       => esc_html__( 'Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change feature box text color', 'aapside-master' ),
			'default'     => '#878a95',
			'selectors'   => [
				"{{WRAPPER}} .single-feature-list:nth-child(odd) .content p" => "color: {{VALUE}}"
			]
		] );
		$this->end_controls_section();
		$this->start_controls_section(
			'even_feature_box_style_section',
			[
				'label' => esc_html__( 'Feature Box EVEN Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control( 'even_background_color', [
			'label'       => esc_html__( 'EVEN Background Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change feature box EVEN item background color', 'aapside-master' ),
			'default'     => '#f0e4f7',
			'selectors'   => [
				"{{WRAPPER}} .feature-list.style-03 .single-feature-list:nth-child(even)" => "background-color: {{VALUE}}"
			]
		] );
		$this->add_control( 'even_title_color', [
			'label'       => esc_html__( 'Title Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change feature box title color', 'aapside-master' ),
			'default'     => '#1c144e',
			'selectors'   => [
				"{{WRAPPER}} .single-feature-list:nth-child(even) .content .title" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'even_text_color', [
			'label'       => esc_html__( 'Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change feature box text color', 'aapside-master' ),
			'default'     => '#878a95',
			'selectors'   => [
				"{{WRAPPER}} .single-feature-list:nth-child(even) .content p" => "color: {{VALUE}}"
			]
		] );
		$this->end_controls_section();
		$this->start_controls_section(
			'gd_one_feature_box_style_section',
			[
				'label' => esc_html__( 'Gradient One Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'gd_one_background',
			'selector'    => "{{WRAPPER}} .feature-list.style-03 .single-feature-list .icon.icon-bg-1:after",
			'description' => esc_html__( 'gradient one background', 'aapside-master' )
		] );
		$this->add_control( 'gd_01_icon_color', [
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'selectors' => [
				"{{WRAPPER}} .feature-list.style-03 .single-feature-list .icon.icon-bg-1" => "color: {{VALUE}}"
			]
		] );
		$this->end_controls_section();
		$this->start_controls_section(
			'gd_two_feature_box_style_section',
			[
				'label' => esc_html__( 'Gradient Two Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'gd_two_background',
			'selector'    => "{{WRAPPER}} .feature-list.style-03 .single-feature-list .icon.icon-bg-2:after",
			'description' => esc_html__( 'gradient two background', 'aapside-master' )
		] );
		$this->add_control( 'gd_02_icon_color', [
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'selectors' => [
				"{{WRAPPER}} .feature-list.style-03 .single-feature-list .icon.icon-bg-2" => "color: {{VALUE}}"
			]
		] );
		$this->end_controls_section();
		$this->start_controls_section(
			'gd_three_feature_box_style_section',
			[
				'label' => esc_html__( 'Gradient Three Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'gd_three_background',
			'selector'    => "{{WRAPPER}} .feature-list.style-03 .single-feature-list .icon.icon-bg-3:after",
			'description' => esc_html__( 'gradient three background', 'aapside-master' )
		] );
		$this->add_control( 'gd_03_icon_color', [
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'selectors' => [
				"{{WRAPPER}} .feature-list.style-03 .single-feature-list .icon.icon-bg-3" => "color: {{VALUE}}"
			]
		] );
		$this->end_controls_section();
		$this->start_controls_section(
			'gd_four_feature_box_style_section',
			[
				'label' => esc_html__( 'Gradient Four Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'gd_four_background',
			'selector'    => "{{WRAPPER}} .feature-list.style-03 .single-feature-list .icon.icon-bg-4:after",
			'description' => esc_html__( 'gradient four background', 'aapside-master' )
		] );
		$this->add_control( 'gd_04_icon_color', [
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'selectors' => [
				"{{WRAPPER}} .feature-list.style-03 .single-feature-list .icon.icon-bg-4" => "color: {{VALUE}}"
			]
		] );
		$this->end_controls_section();
		$this->start_controls_section(
			'hover_border_feature_box_style_section',
			[
				'label' => esc_html__( 'Hover Border Background', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'hover_border_background',
			'selector'    => "{{WRAPPER}} .feature-list.style-03 .single-feature-list:after",
			'description' => esc_html__( 'hover border background', 'aapside-master' )
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
			'name'  => 'title_typography',
			'label' => esc_html__( 'Title Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .single-feature-list .content .title"
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'  => 'description_typography',
			'label' => esc_html__( 'Description Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .single-feature-list .content p"
		] );
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
		$all_feature_list = $settings['features_items'];
		?>
        <ul class="feature-list style-03">
			<?php foreach ( $all_feature_list as $list ): ?>
                <li class="single-feature-list">
                    <div class="icon <?php echo esc_attr( $list['theme'] ); ?>">
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

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Featrue_Box_Four_Widget() );