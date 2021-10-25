<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Heading_Title_One_Widget extends Widget_Base {

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
		return 'appside-heading-title-one-widget';
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
		return esc_html__( 'Heading Title: 01', 'aapside-master' );
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
		return 'eicon-heading';
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
			'settings_heading',
			[
				'label' => esc_html__( 'General Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'line_status',
			[
				'label'       => esc_html__( 'Line Show/Hide', 'aapside-master' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'show/hide line', 'aapside-master' ),
			]
		);
		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'aapside-master' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'enter  title.', 'aapside-master' ),
				'default'     => esc_html__( 'Top Packages', 'aapside-master' )
			]
		);

		$this->add_control(
			'alignment',
			[
				'label'     => esc_html__( 'Alignment', 'aapside-master' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'aapside-master' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'aapside-master' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'aapside-master' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => 'center',
				'toggle'    => true,
				'selectors' => [
					"{{WRAPPER}} .heading-title-style-02" => "text-align: {{VALUE}}"
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'styling_heading',
			[
				'label' => esc_html__( 'Styling Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'line_bottom_space',
			[
				'label'      => esc_html__( 'Line Right Space', 'aapside-master' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .heading-title-style-02 .title span' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'line_width',
			[
				'label'      => esc_html__( 'Line width', 'aapside-master' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .heading-title-style-02 .title span' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'line_top_space',
			[
				'label'      => esc_html__( 'Line Top Space', 'aapside-master' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .heading-title-style-02 .title span' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(), [
			'label'    => esc_html__( 'Line Background Color', 'aapside-master' ),
			'name'     => 'line-background',
			'selector' => "{{WRAPPER}} .heading-title-style-02 .title span"
		] );
        $this->add_control('title_styling_divider',[
           'type' => Controls_Manager::DIVIDER
        ]);
		$this->add_control( 'subtitle_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .heading-title-style-02 .title" => "color: {{VALUE}}"
			]
		] );


		$this->end_controls_section();
		$this->start_controls_section(
			'styling_typogrpahy_heading',
			[
				'label' => esc_html__( 'Typography Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'subtitle_typography',
			'label'    => esc_html__( 'Subtitle Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .heading-title-style-02 .title"
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
		$settings    = $this->get_settings_for_display();
		$line_status = $settings['line_status'] == 'yes' ? '' : 'hide';
		?>
        <div class="heading-title-style-02">
			<?php
			printf( '<h4 class="title"><span class="%2$s"></span>%1$s</h4>', esc_html( $settings['title'] ), esc_attr( $line_status ) );
			?>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Heading_Title_One_Widget() );