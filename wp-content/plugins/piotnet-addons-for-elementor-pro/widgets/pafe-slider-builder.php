<?php

class PAFE_Slider_Builder extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-slider-builder';
	}

	public function get_title() {
		return __( 'PAFE Slider Builder', 'pafe' );
	}

	public function get_icon() {
		return 'eicon-slideshow';
	}

	public function get_categories() {
		return [ 'pafe' ];
	}

	public function get_keywords() {
		return [ 'slides', 'carousel', 'image', 'title', 'slider' ];
	}

	public function get_script_depends() {
		return [ 
			'imagesloaded',
			'pafe-slick',
			'pafe-widget',
		];
	}

	public function get_style_depends() {
		return [ 
			'pafe-widget-style'
		];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'pafe_slider_builder_section',
			[
				'label' => __( 'Slides', 'elementor-pro' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'pafe_slider_builder_shortcode',
			[
				'label' => __( 'Shortcode', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'pafe_slider_builder_slides',
			[
				'label' => __( 'Slides', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'description' => __( "Please don't use Stretch Section for Section Slide Item", "pafe" ),
				'show_label' => true,
				'fields' => $repeater->get_controls(),
				'title_field' => __( 'Item', 'pafe' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_options',
			[
				'label' => __( 'Slider Options', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SECTION,
			]
		);

		$this->add_control(
			'responsive_items_desktop',
			[
				'label' => __( 'Desktop Items', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 1,
			]
		);

		$this->add_control(
			'responsive_items_tablet',
			[
				'label' => __( 'Tablet Items', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 1,
			]
		);

		$this->add_control(
			'responsive_items_mobile',
			[
				'label' => __( 'Mobile Items', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 1,
			]
		);

		$this->add_control(
			'navigation',
			[
				'label' => __( 'Navigation', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [
					'both' => __( 'Arrows and Dots', 'elementor-pro' ),
					'arrows' => __( 'Arrows', 'elementor-pro' ),
					'dots' => __( 'Dots', 'elementor-pro' ),
					'none' => __( 'None', 'elementor-pro' ),
				],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label' => __( 'Pause on Hover', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => __( 'Autoplay', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => __( 'Autoplay Speed', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .slick-slide-bg' => 'animation-duration: calc({{VALUE}}ms*1.2); transition-duration: calc({{VALUE}}ms)',
				],
			]
		);

		$this->add_control(
			'infinite',
			[
				'label' => __( 'Infinite Loop', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'transition',
			[
				'label' => __( 'Transition', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide' => __( 'Slide', 'elementor-pro' ),
					'fade' => __( 'Fade', 'elementor-pro' ),
				],
			]
		);

		$this->add_control(
			'transition_speed',
			[
				'label' => __( 'Transition Speed (ms)', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 500,
			]
		);

		$this->add_responsive_control(
			'spacing',
			[
				'label' => __( 'Spacing', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-slide' => 'padding: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-list' => 'margin: 0 -{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_navigation',
			[
				'label' => __( 'Navigation', 'elementor-pro' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'navigation' => [ 'arrows', 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'heading_style_arrows',
			[
				'label' => __( 'Arrows', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'arrows_size',
			[
				'label' => __( 'Arrows Size', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-slider .slick-prev:before, {{WRAPPER}} .slick-slider .slick-next:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label' => __( 'Arrows Color', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-slider .slick-prev:before, {{WRAPPER}} .slick-slider .slick-next:before' => 'color: {{VALUE}};',
				],
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'heading_style_dots',
			[
				'label' => __( 'Dots', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'dots_size',
			[
				'label' => __( 'Dots Size', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label' => __( 'Dots Color', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:before' => 'color: {{VALUE}};',
				],
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( $settings['pafe_slider_builder_slides'] ) {
			$is_rtl = is_rtl();
			$direction = $is_rtl ? 'rtl' : 'ltr';
			$show_dots = ( in_array( $settings['navigation'], [ 'dots', 'both' ] ) );
			$show_arrows = ( in_array( $settings['navigation'], [ 'arrows', 'both' ] ) );

			$default_breakpoints = Elementor\Core\Breakpoints\Manager::get_default_config();
			$md_breakpoint = get_option( 'elementor_viewport_md' );
			$lg_breakpoint = get_option( 'elementor_viewport_lg' );

			if(empty($md_breakpoint)) {
				$md_breakpoint = $default_breakpoints['mobile']['default_value'];
			}

			if(empty($lg_breakpoint)) {
				$lg_breakpoint = $default_breakpoints['tablet']['default_value'];
			}

			$slick_options = [
				'slidesToShow' => $settings['responsive_items_desktop'],
				'autoplaySpeed' => absint( $settings['autoplay_speed'] ),
				'autoplay' => ( 'yes' === $settings['autoplay'] ),
				'infinite' => ( 'yes' === $settings['infinite'] ),
				'pauseOnHover' => ( 'yes' === $settings['pause_on_hover'] ),
				'speed' => absint( $settings['transition_speed'] ),
				'arrows' => $show_arrows,
				'dots' => $show_dots,
				'rtl' => $is_rtl,
				'responsive'=> [
					[
						'breakpoint' => $lg_breakpoint,
						'settings' => [
							'slidesToShow' => $settings['responsive_items_tablet'],
						]
					],
					[
						'breakpoint' => $md_breakpoint,
						'settings' => [
							'slidesToShow' => $settings['responsive_items_mobile'],
						]
					],
				],
			];

			if ( 'fade' === $settings['transition'] ) {
				$slick_options['fade'] = true;
			}

		?>	
			<div class="elementor-slides-wrapper elementor-slick-slider">
	            <div class="pafe-slider-builder" data-pafe-slider-builder-slick data-slick='<?php echo json_encode( $slick_options ); ?>' style="visibility:hidden;">
	            	<?php
			            foreach (  $settings['pafe_slider_builder_slides'] as $item ) :
			        ?>
					<div class="pafe-slider-builder__item" data-pafe-slider-builder-item="1"><?php echo do_shortcode( $item['pafe_slider_builder_shortcode'] ); ?></div>
					<?php endforeach; ?>
	      		</div>
      		</div>
        <?php

		}

	}
}
