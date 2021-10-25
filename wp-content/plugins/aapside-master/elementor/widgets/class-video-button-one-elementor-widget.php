<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Video_Button_One_Widget extends Widget_Base {

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
		return 'appside-video-button-one-widget';
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
		return esc_html__( 'Video Button: 01', 'aapside-master' );
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
		return 'eicon-play';
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
			'btn_text',
			[
				'label'       => esc_html__( 'Button Text', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'enter button text.', 'aapside-master' ),
				'default'     => esc_html__( 'Watch Tutorial', 'aapside-master' )
			]
		);

		$this->add_control(
			'btn_link',
			[
				'label'       => esc_html__( 'Button Link', 'aapside-master' ),
				'type'        => Controls_Manager::URL,
				'default'     => array(
					'url' => '#'
				),
				'description' => esc_html__( 'enter button url.', 'aapside-master' ),
			]
		);

		if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
			$this->add_control(
				'icon',
				[
					'label'       => esc_html__( 'Icon', 'aapside-master' ),
					'type'        => Controls_Manager::ICONS,
					'description' => esc_html__( 'select Icon.', 'aapside-master' )
				]
			);
		} else {
			$this->add_control(
				'icon',
				[
					'label'       => esc_html__( 'Icon', 'aapside-master' ),
					'type'        => Controls_Manager::ICON,
					'description' => esc_html__( 'select Icon.', 'aapside-master' ),
					'default'     => 'flaticon-vector',
				]
			);
		}

		$this->add_control( 'video_url', [
			'label'       => esc_html__( 'Video URL', 'aapside-master' ),
			'type'        => Controls_Manager::URL,
			'description' => esc_html__( 'enter video url', 'aapside-master' )
		] );

		$this->end_controls_section();

		$this->start_controls_section(
			'button_styling_section',
			[
				'label' => esc_html__( 'Button Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control( 'button_normal_color', [
			'label'     => esc_html__( 'Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .video-btn-one .appside-video-btn" => "color:{{VALUE}}"
			]
		] );
		$this->add_control( 'button_hover_color', [
			'label'     => esc_html__( 'Hover Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .video-btn-one .appside-video-btn:hover" => "color:{{VALUE}}"
			]
		] );
		$this->end_controls_section();

		$this->start_controls_section(
			'button_icon_styling_section',
			[
				'label' => esc_html__( 'Icon Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'icon_right_gap',
			[
				'label' => esc_html__( 'Icon Right Gap', 'plugin-domain' ),
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
					'{{WRAPPER}} .video-btn-one .appside-video-btn' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'icon_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'aapside-master' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					"{{WRAPPER}} .video-btn-one .icon a" => "border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
				],
			]
		);
		$this->start_controls_tabs(
			'icon_style_tabs'
		);
		$this->start_controls_tab(
			'style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'aapside-master' ),
			]
		);

		$this->add_control('icon_color',[
			'label' => esc_html__('Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .video-btn-one .icon a" => "color: {{VALUE}}"
			]
		]);
		$this->add_group_control(Group_Control_Border::get_type(),[
			'label' => esc_html__('Border','aapside-master'),
			'name' => 'icon_border',
			"selector" => "{{WRAPPER}} .video-btn-one .icon a"
		]);
		$this->add_group_control(Group_Control_Background::get_type(),[
			'label' => esc_html__('Background','aapside-master'),
			'name' => 'icon_background',
			"selector" => "{{WRAPPER}} .video-btn-one .icon a"
		]);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'aapside-master' ),
			]
		);

		$this->add_control('icon_hover_color',[
			'label' => esc_html__('Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .video-btn-one .icon a:hover" => "color: {{VALUE}}"
			]
		]);
		$this->add_group_control(Group_Control_Border::get_type(),[
			'label' => esc_html__('Border','aapside-master'),
			'name' => 'icon_hover_border',
			"selector" => "{{WRAPPER}} .video-btn-one .icon a:hover"
		]);
		$this->add_group_control(Group_Control_Background::get_type(),[
			'label' => esc_html__('Background','aapside-master'),
			'name' => 'icon_hover_background',
			"selector" => "{{WRAPPER}} .video-btn-one .icon a:hover"
		]);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'typography_section',
			[
				'label' => esc_html__( 'Typography', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'button_typography',
			'label'    => esc_html__( 'Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .video-btn-wrapper .appside-video-btn"
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

		$this->add_render_attribute( 'button_attr', 'class', 'appside-video-btn' );
		if ( ! empty( $settings['btn_link']['url'] ) ) {
			$this->add_link_attributes( 'button_attr', $settings['btn_link'] );
		}
		?>
        <div class="video-btn-wrapper">
            <div class="video-btn-one">
                <div class="icon">
                    <a href="<?php echo esc_url( $settings['video_url']['url'] ); ?>" class="mfp-iframe">
						<?php
						if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
							! empty( $settings['icon']['value'] ) ? Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ) : '';
						} else {
							! empty( $settings['icon'] ) ? printf( '<i class="%1$s"></i>', esc_attr( $settings['icon'] ) ) : '';
						}
						?>
                    </a>
                </div>
                <a <?php echo $this->get_render_attribute_string( 'button_attr' ); ?>>
					<?php echo esc_html( $settings['btn_text'] ); ?>
                </a>
            </div>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Video_Button_One_Widget() );