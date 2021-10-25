<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Heading_One_Advance_Widget extends Widget_Base {

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
		return 'appside-advance-heading-one-widget';
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
		return esc_html__( 'Heading: Advance', 'aapside-master' );
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
		return 'eicon-t-letter';
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
			'heading_text',
			[
				'label'       => esc_html__( 'Text', 'aapside-master' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'enter text. you can use span tags in this heading', 'aapside-master' ),
			]
		);
		$this->add_responsive_control(
			'align',
			[
				'label'     => esc_html__( 'Alignment', 'aapside-master' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'aapside-master' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'aapside-master' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'aapside-master' ),
						'icon'  => 'eicon-text-align-right',
					]
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .appside-advance-heading' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'button_gd_two_section',
			[
				'label' => esc_html__( 'Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control( 'heading_color', [
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Heading Color', 'aapside-master' ),
			'selectors' => [
                   '{{WRAPPER}} .appside-heading' => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'heading_span_color', [
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Heading Span Color', 'aapside-master' ),
			'selectors' => [
				'{{WRAPPER}} .appside-heading span' => "color: {{VALUE}}"
			]
		] );

		$this->add_responsive_control( 'heading_span_display', [
			'type'      => Controls_Manager::SELECT,
			'label'     => esc_html__( 'Heading Span Display', 'aapside-master' ),
			'options' => [
			  'block' => esc_html__('Block','aapside-master'),
			  'inline-block' => esc_html__('Inline Block','aapside-master')    ,
			  'inherit' => esc_html__('Inherit','aapside-master')
            ],
			'selectors' => [
				'{{WRAPPER}} .appside-heading span' => "display: {{VALUE}}"
			]
		] );
		$this->end_controls_section();

		$this->start_controls_section(
			'typography_section',
			[
				'label' => esc_html__( 'Typography', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'heading_typography',
			'label'    => esc_html__( 'Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .appside-heading"
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'heading_span_typography',
			'label'    => esc_html__( 'Span Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .appside-heading span"
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
        <div class="appside-advance-heading">
            <h2 class="appside-heading">
				<?php echo wp_kses( $settings['heading_text'], appside_master()->kses_allowed_html( array('span') ) ); ?>
            </h2>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Heading_One_Advance_Widget() );