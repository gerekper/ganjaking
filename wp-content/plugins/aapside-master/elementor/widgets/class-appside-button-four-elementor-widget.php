<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Boxed_Button_Four_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Elementor widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'appside-boxed-button-four-widget';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Elementor widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Button: 04', 'aapside-master' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Elementor widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-button';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Elementor widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
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
			'show_icon',
			[
				'label'       => esc_html__( 'Show Button Icon', 'aapside-master' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'show button icon.', 'aapside-master' ),
				'default'     => 'yes'
			]
		);
		$this->add_control(
			'icon',
			[
				'label'       => esc_html__( 'Button Icon', 'aapside-master' ),
				'type'        => Controls_Manager::ICON,
				'description' => esc_html__( 'enter button icon.', 'aapside-master' ),
				'default'     => 'flaticon-apple-1',
                'condition' => array(
                        'show_icon' => 'yes'
                )
			]
		);
		$this->add_control(
			'btn_text',
			[
				'label'       => esc_html__( 'Button Text', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'enter button text.', 'aapside-master' ),
				'default'     => esc_html__('Learn More','aapside-master')
			]
		);
		$this->add_control(
			'btn_link',
			[
				'label'       => esc_html__( 'Button Link', 'aapside-master' ),
				'type'        => Controls_Manager::URL,
				'default' => array(
					'url' => '#'
				),
				'description' => esc_html__( 'enter button url.', 'aapside-master' ),
			]
		);
	
		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'aapside-master' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'aapside-master' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'aapside-master' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'aapside-master' ),
						'icon' => 'eicon-text-align-right',
					]
				],
				'selectors' => [
					'{{WRAPPER}} .btn-wrapper' => 'text-align: {{VALUE}};',
				],
			]
		);


		$this->end_controls_section();
		$this->start_controls_section(
			'padding_section',
			[
				'label' => esc_html__( 'Button Padding', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control('padding',[
			'label' => esc_html__('Padding' ,'aapside-master'),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => ['px','em'],
			'selectors' => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn-04" => 'padding-top: {{TOP}}{{UNIT}};padding-left: {{LEFT}}{{UNIT}};padding-right: {{RIGHT}}{{UNIT}};padding-bottom: {{BOTTOM}}{{UNIT}};'
			],
			'description' => esc_html__('set padding for button','aapside-master')
		]);
		$this->end_controls_section();

		$this->start_controls_section(
			'typography_section',
			[
				'label' => esc_html__( 'Button Typography', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'button_typography',
			'label' => esc_html__('Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn-04"
		]);
		$this->end_controls_section();
		$this->start_controls_section(
			'styling_section',
			[
				'label' => esc_html__( 'Styling Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'aapside-master' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .btn-wrapper .boxed-btn-04' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'button_icon_gap',
			[
				'label' => esc_html__( 'Button Icon Gap', 'aapside-master' ),
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
					'{{WRAPPER}} .btn-wrapper .boxed-btn-04 i' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('button_two_background');

		$this->start_controls_tab('normal_two_style',[
			'label' => esc_html__('Normal','aapside-master')
		]);
		$this->add_control( 'gd_two_normal_icon_color', [
			'label'       => esc_html__( 'Button Icon Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button icon color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn-04 i" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'gd_two_text_color', [
			'label'       => esc_html__( 'Button Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button text color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn-04" => "color: {{VALUE}}"
			]
		] );
		$this->add_group_control(Group_Control_Background::get_type(),[
			'name' => 'gd_two_background',
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn-04",
			'description' => esc_html__('button background','aapside-master')
		]);

		$this->add_group_control(Group_Control_Border::get_type(),[
			'name' => 'button_normal_border',
			'label' => esc_html__('Border','aapside-master'),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn-04"
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('hover_two_style',[
			'label' => esc_html__('Hover','aapside-master')
		]);
		$this->add_control( 'gd_two_hover_text_color', [
			'label'       => esc_html__( 'Button Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button text color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn-04:hover" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'gd_two_hover_icon_color', [
			'label'       => esc_html__( 'Button Icon Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button icon color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn-04:hover i" => "color: {{VALUE}}"
			]
		] );

		$this->add_group_control(Group_Control_Background::get_type(),[
			'name' => 'appside_gd_two_hover_background',
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn-04:hover",
			'description' => esc_html__('button hover background','aapside-master')
		]);
		$this->add_group_control(Group_Control_Border::get_type(),[
			'name' => 'button_hover_border',
			'label' => esc_html__('Border','aapside-master'),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn-04:hover"
		]);
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
		$settings = $this->get_settings_for_display();

		//button attributes
        $this->add_render_attribute('button_attr','class','boxed-btn-04');

        if (!empty($settings['btn_link']['url'])){
            $this->add_link_attributes('button_attr',$settings['btn_link']);
        }

		$btn_icon = 'yes' == $settings['show_icon'] && $settings['icon'] ? '<i class="'.esc_attr($settings['icon']).'"></i>' : '';

		?>
		<div class="btn-wrapper">
			<a <?php echo $this->get_render_attribute_string('button_attr');?>>
                <?php echo wp_kses_post($btn_icon);?>
				<?php echo esc_html($settings['btn_text']);?>
			</a>
		</div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Boxed_Button_Four_Widget() );