<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Section_Title_One_Widget extends Widget_Base {

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
		return 'appside-section-title-one-widget';
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
		return esc_html__( 'Section Title: 01', 'aapside-master' );
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
			'settings_section',
			[
				'label' => esc_html__( 'General Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'subtitle_status',
			[
				'label'       => esc_html__( 'Subtitle Show/Hide', 'aapside-master' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'show/hide subtitle', 'aapside-master' ),
			]
		);
		$this->add_control(
			'subtitle',
			[
				'label'       => esc_html__( 'Subtitle', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'enter  subtitle.', 'aapside-master' ),
				'default'     => esc_html__( 'Top Packages', 'aapside-master' ),
                'condition' => ['subtitle_status' => 'yes']
			]
		);
		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'aapside-master' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'enter title. use {b} border text {/b} for bordered text', 'aapside-master' ),
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label' => esc_html__( 'Alignment', 'aapside-master' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'aapside-master' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'aapside-master' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'aapside-master' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'toggle' => true,
                'selectors' => [
	                "{{WRAPPER}} .section-title-style-02" => "text-align: {{VALUE}}"
                ]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'styling_section',
			[
				'label' => esc_html__( 'Styling Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'subtitle_bottom_space',
			[
				'label' => __( 'Subtitle Bottom Space', 'aapside-master' ),
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
					'{{WRAPPER}} .section-title-style-02 .subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control( 'subtitle_color', [
			'label'     => esc_html__( 'Subtitle Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .section-title-style-02 .subtitle" => "color: {{VALUE}}"
			]
		] );

		$this->add_control( 'title_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .section-title-style-02 .title" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'title_border_color', [
			'label'     => esc_html__( 'Title Border Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .section-title-style-02 .title span:after" => "background-color: {{VALUE}}"
			]
		] );

		$this->end_controls_section();
		$this->start_controls_section(
			'styling_typogrpahy_section',
			[
				'label' => esc_html__( 'Typography Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'subtitle_typography',
			'label'    => esc_html__( 'Subtitle Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .section-title-style-02 .subtitle"
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'label'    => esc_html__( 'Title Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .section-title-style-02 .title"
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
		$settings = $this->get_settings_for_display();
		?>
            <div class="section-title-style-02">
                <?php
                    if (!empty($settings['subtitle_status'])){
                        printf('<span class="subtitle">%1$s</span>',esc_html($settings['subtitle']));
                    }
                ?>

                <h3 class="title">
                <?php
                    $title = $settings['title'];
                    $title = str_replace('{b}','<span>',$title);
                    $title = str_replace('{/b}','</span>',$title);
                    print $title;
                ?>
                </h3>
            </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Section_Title_One_Widget() );