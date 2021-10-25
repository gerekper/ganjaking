<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Animation_Circle_One extends Widget_Base {

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
		return 'appside-animated-circle-one-widget';
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
		return esc_html__( 'Animated Circle', 'aapside-master' );
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
		return 'eicon-circle-o';
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
			'top_position',
			[
				'label' => __( 'Animation Start Top', 'aapside-master' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 120,
				],
				'selectors' => [
					'{{WRAPPER}} .animated-shape-circle-one' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'circle_one_color',
			[
				'label'       => esc_html__( 'Circle One Color', 'aapside-master' ),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__( 'change color.', 'aapside-master' ),
				'default'     => '#500ade'
			]
		);
		$this->add_control(
			'circle_one_top',
			[
				'label' => __( 'Circle One Top', 'aapside-master' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .animated-shape-circle-one .shape-1' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'circle_one_left',
			[
				'label' => __( 'Circle One Left', 'aapside-master' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .animated-shape-circle-one .shape-1' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'circle_two_color',
			[
				'label'       => esc_html__( 'Circle Two Color', 'aapside-master' ),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__( 'change color.', 'aapside-master' ),
				'default'     => '#500ade'
			]
		);
		$this->add_control(
			'circle_two_top',
			[
				'label' => __( 'Circle Two Top', 'aapside-master' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .animated-shape-circle-one .shape-2' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'circle_two_left',
			[
				'label' => __( 'Circle Two Left', 'aapside-master' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 6,
				],
				'selectors' => [
					'{{WRAPPER}} .animated-shape-circle-one .shape-2' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

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
        <div class="animated-shape-circle-one">
            <div class="shape-1">
                <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                     width="147px" height="147px" viewBox="0 0 147.000000 147.000000"
                     preserveAspectRatio="xMidYMid meet">
                    <g transform="translate(0.000000,147.000000) scale(0.100000,-0.100000)"
                       fill="<?php echo esc_attr($settings['circle_one_color'])?>" stroke="none">
                        <path d="M601 1459 c-313 -61 -547 -311 -591 -629 -13 -96 -7 -184 21 -289 52
                        -195 192 -367 369 -454 132 -65 200 -81 340 -81 136 1 209 18 325 74 148 73
                        256 182 330 335 211 434 -43 951 -513 1041 -81 15 -211 17 -281 3z m294 -95
                        c112 -27 202 -80 295 -174 134 -133 193 -274 193 -455 -1 -442 -448 -758 -860
                        -607 -272 100 -447 363 -430 647 10 165 71 299 193 420 162 163 383 224 609
                        169z"/>
                    </g>
                </svg>
            </div>
            <div class="shape-2">
                <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                     width="110px" height="110px" viewBox="0 0 110.000000 110.000000"
                     preserveAspectRatio="xMidYMid meet">
                    <g transform="translate(0.000000,110.000000) scale(0.100000,-0.100000)"
                       fill="<?php echo esc_attr($settings['circle_two_color'])?>" stroke="none">
                        <path d="M410 1085 c-96 -26 -181 -76 -250 -145 -242 -242 -205 -656 77 -843
                        229 -152 519 -122 707 73 277 287 169 756 -209 900 -78 30 -243 37 -325 15z
                        m210 -56 c182 -29 338 -164 391 -341 28 -90 23 -224 -10 -310 -46 -120 -159
                        -233 -279 -279 -78 -30 -225 -37 -303 -15 -230 67 -387 309 -349 538 39 228
                        232 399 470 417 8 0 44 -4 80 -10z"/>
                    </g>
                </svg>
            </div>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Animation_Circle_One() );