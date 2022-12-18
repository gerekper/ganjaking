<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Fancy Text Widget
 *
 * Porto Elementor widget to display fancy text which displays ratating words.
 *
 * @since 1.7.3
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Fancytext_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_fancytext';
	}

	public function get_title() {
		return __( 'Porto Fancy Text', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'fancy', 'text', 'words', 'rotator', 'rotate' );
	}

	public function get_icon() {
		return 'eicon-text-field';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'porto_word_rotator', 'porto-elementor-widgets-js' );
		} else {
			return array();
		}
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_fancytext',
			array(
				'label' => __( 'Fancy Text', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'fancytext_prefix',
			array(
				'type'  => Controls_Manager::TEXT,
				'label' => __( 'Prefix', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'fancytext_strings',
			array(
				'type'        => Controls_Manager::TEXTAREA,
				'label'       => __( 'Fancy Text', 'porto-functionality' ),
				'description' => __( 'Enter each string on a new line', 'porto-functionality' ),
				'default'     => __(
					'incredibly
especially',
					'porto-functionality'
				),
			)
		);

		$this->add_control(
			'fancytext_suffix',
			array(
				'type'  => Controls_Manager::TEXT,
				'label' => __( 'Suffix', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'fancytext_align',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Alignment', 'porto-functionality' ),
				'options' => array(
					'center' => __( 'Center', 'porto-functionality' ),
					'left'   => __( 'Left', 'porto-functionality' ),
					'right'  => __( 'Right', 'porto-functionality' ),
				),
				'default' => 'center',
			)
		);

		$this->add_control(
			'ticker_wait_time',
			array(
				'type'        => Controls_Manager::NUMBER,
				'label'       => __( 'Pause Time (ms)', 'porto-functionality' ),
				'min'         => 0,
				'default'     => 2500,
				'max'         => 9999,
				'description' => __( 'How long the string should stay visible?', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'animation_effect',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Animation Effect', 'porto-functionality' ),
				'options' => array(
					'slide'            => __( 'Slide', 'porto-functionality' ),
					'letters type'     => __( 'Letters Type', 'porto-functionality' ),
					'letters scale'    => __( 'Letters Scale', 'porto-functionality' ),
					'letters rotate-2' => __( 'Letters Rotate 1', 'porto-functionality' ),
					'letters rotate-3' => __( 'Letters Rotate 2', 'porto-functionality' ),
					'push'             => __( 'Push', 'porto-functionality' ),
					'clip'             => __( 'Clip', 'porto-functionality' ),
					'zoom'             => __( 'Zoom', 'porto-functionality' ),
				),
				'default' => 'slide',
			)
		);

		$this->add_control(
			'ticker_hover_pause',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Pause on Hover', 'porto-functionality' ),
				'options' => array(
					''     => 'No',
					'true' => 'Yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button_style_options',
			array(
				'label' => __( 'Style', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'fancy_heading',
			array(
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Fancy Text', 'porto-functionality' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'fancytext_tag',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Markup', 'porto-functionality' ),
				'options' => array(
					'div' => __( 'div', 'porto-functionality' ),
					'h1'  => __( 'H1', 'porto-functionality' ),
					'h2'  => __( 'H2', 'porto-functionality' ),
					'h3'  => __( 'H3', 'porto-functionality' ),
					'h4'  => __( 'H4', 'porto-functionality' ),
					'h5'  => __( 'H5', 'porto-functionality' ),
					'h6'  => __( 'H6', 'porto-functionality' ),
				),
				'default' => 'h2',
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'fancy_text_typography',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .word-rotator-items',
			)
		);

		$this->add_control(
			'fancytext_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .word-rotator-items' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ticker_background',
			array(
				'type'  => Controls_Manager::COLOR,
				'label' => __( 'Background', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'prefsuf_heading',
			array(
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Prefix and Suffix Text', 'porto-functionality' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'fancy_prefsuf_text_typography',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .word-rotate-prefix, {{WRAPPER}} .word-rotate-suffix',
			)
		);

		$this->add_control(
			'sufpref_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .word-rotate-prefix, {{WRAPPER}} .word-rotate-suffix' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sufpref_bg_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .word-rotate-prefix, {{WRAPPER}} .word-rotate-suffix' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( $template = porto_shortcode_template( 'porto_fancytext' ) ) {
			include $template;
		}
	}

	protected function content_template() {
		?>
		<#
			view.addRenderAttribute( 'wrapper', 'class', 'word-rotator ' + settings.animation_effect );
			view.addRenderAttribute( 'wrapper', 'data-plugin-options', "{'waittime': " + Number( settings.ticker_wait_time ) + ", 'pauseOnHover': " + ( settings.ticker_hover_pause ? settings.ticker_hover_pause : 'false' ) + '}' );
			view.addRenderAttribute( 'wrapper', 'style', 'text-align:' + settings.fancytext_align );

			view.addRenderAttribute( 'items', 'class', 'word-rotator-items' );
			if ( settings.ticker_background ) {
				view.addRenderAttribute( 'items', 'class', 'has-bg' );
			}
			if ( -1 !== settings.animation_effect.indexOf('type') ) {
				view.addRenderAttribute( 'items', 'class', 'waiting' );
			}
			if ( settings.ticker_background ) {
				view.addRenderAttribute( 'items', 'style', 'background:' + settings.ticker_background );
			}

			let lines = settings.fancytext_strings.split('\n');
		#>
		<{{{ settings.fancytext_tag }}} {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
		<# if ( settings.fancytext_prefix ) { #>
			<span class="word-rotate-prefix">{{{ settings.fancytext_prefix }}}</span>
		<# } #>
			<span {{{ view.getRenderAttributeString( 'items' ) }}}>
			<#
				let key = 0;
				lines.forEach(function(line) {
					if ( line ) {
						if ( ! key ) {
							print( '<b class="active">' + line + '</b>' );
						} else {
							print( '<b>' + line + '</b>' );
						}
						key++;
					}
				});
			#>
			</span>
		<# if ( settings.fancytext_suffix ) { #>
			<span class="word-rotate-suffix">{{{ settings.fancytext_suffix }}}</span>
		<# } #>
		</{{{ settings.fancytext_tag }}}>
		<?php
	}
}
