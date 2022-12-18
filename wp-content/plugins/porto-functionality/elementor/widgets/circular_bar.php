<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Circular Bar Widget
 *
 * Porto Elementor widget to display a circular bar.
 *
 * @since 1.7.2
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Circular_Bar_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_circular_bar';
	}

	public function get_title() {
		return __( 'Porto Circular Bar', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'circular', 'bar', 'pie', 'chart' );
	}

	public function get_icon() {
		return 'eicon-counter-circle';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'easypiechart', 'porto-elementor-widgets-js' );
		} else {
			return array();
		}
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_circular_bar',
			array(
				'label' => __( 'Circular Bar', 'porto-functionality' ),
			)
		);

			$this->add_control(
				'view',
				array(
					'type'    => Controls_Manager::SELECT,
					'label'   => __( 'View Type', 'porto-functionality' ),
					'options' => array_combine( array_values( porto_vc_commons( 'circular_view_type' ) ), array_keys( porto_vc_commons( 'circular_view_type' ) ) ),
				)
			);

			$this->add_control(
				'icon_cl',
				array(
					'type'                   => Controls_Manager::ICONS,
					'label'                  => __( 'Select Icon', 'porto-functionality' ),
					'description'            => __( 'Please select the icon.', 'porto-functionality' ),
					'fa4compatibility'       => 'icon',
					'skin'                   => 'inline',
					'exclude_inline_options' => array( 'svg' ),
					'label_block'            => false,
					'default'                => array(
						'value'   => 'fas fa-star',
						'library' => 'fa-solid',
					),
					'condition'              => array(
						'view' => 'only-icon',
					),
				)
			);

			$this->add_control(
				'title',
				array(
					'label'       => __( 'Title', 'porto-functionality' ),
					'type'        => Controls_Manager::TEXT,
					'description' => __( 'Please input the title.', 'porto-functionality' ),
					'condition'   => array(
						'view!' => 'only-icon',
					),
				)
			);

			$this->add_control(
				'value',
				array(
					'label'     => __( 'Percent Value', 'porto-functionality' ),
					'type'      => Controls_Manager::NUMBER,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'label_value',
				array(
					'label'       => __( 'Label Value', 'porto-functionality' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => __( 'Enter label for circular bar.', 'porto-functionality' ),
					'condition'   => array(
						'view' => '',
					),
				)
			);

			$this->add_control(
				'units',
				array(
					'type'        => Controls_Manager::TEXT,
					'label'       => __( 'Units', 'porto-functionality' ),
					'description' => __( 'Enter measurement units (Example: %, px, points, etc).', 'porto-functionality' ),
					'condition'   => array(
						'view' => '',
					),
				)
			);

			$this->add_control(
				'view_size',
				array(
					'type'        => Controls_Manager::SELECT,
					'label'       => __( 'View Size', 'porto-functionality' ),
					'description' => __( 'Instead of this, you would better use options in style tab.', 'porto-functionality' ),
					'options'     => array_combine( array_values( porto_vc_commons( 'circular_view_size' ) ), array_keys( porto_vc_commons( 'circular_view_size' ) ) ),
					'condition'   => array(
						'view!' => 'only-icon',
					),
				)
			);

			$this->add_control(
				'linecap',
				array(
					'type'        => Controls_Manager::SELECT,
					'label'       => __( 'Line Cap', 'porto-functionality' ),
					'description' => __( 'Choose how the ending of the bar line looks like.', 'porto-functionality' ),
					'options'     => array(
						'round'  => __( 'Round', 'porto-functionality' ),
						'square' => __( 'Square', 'porto-functionality' ),
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_circular_bar_style',
			array(
				'label' => __( 'Style', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
			$this->add_control(
				'size',
				array(
					'type'    => Controls_Manager::NUMBER,
					'label'   => __( 'Bar Size', 'porto-functionality' ),
					'default' => 175,
					'min'     => 10,
					'max'     => 500,
				)
			);

			$this->add_control(
				'trackcolor',
				array(
					'type'        => Controls_Manager::COLOR,
					'label'       => __( 'Track Color', 'porto-functionality' ),
					'default'     => porto_is_dark_skin() ? '#2e353e' : '#eeeeee',
					'description' => __( 'Choose the color of the track. Please clear this if you want to use the default color.', 'porto-functionality' ),
				)
			);

			$this->add_control(
				'barcolor',
				array(
					'type'        => Controls_Manager::COLOR,
					'label'       => __( 'Bar color', 'porto-functionality' ),
					'description' => __( 'Select pie chart color. Please clear this if you want to use the default color.', 'porto-functionality' ),
				)
			);

			$this->add_control(
				'scalecolor',
				array(
					'type'        => Controls_Manager::COLOR,
					'label'       => __( 'Scale Color', 'porto-functionality' ),
					'description' => __( 'Choose the color of the scale. Please clear this if you want to hide the scale.', 'porto-functionality' ),
				)
			);

			$this->add_control(
				'speed',
				array(
					'type'    => Controls_Manager::NUMBER,
					'label'   => __( 'Animation Speed (ms)', 'porto-functionality' ),
					'default' => 2500,
				)
			);

			$this->add_control(
				'line',
				array(
					'type'    => Controls_Manager::NUMBER,
					'label'   => __( 'Line Width (px)', 'porto-functionality' ),
					'default' => 14,
					'min'     => 1,
					'max'     => 50,
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_circular_bar_title',
			array(
				'label'     => __( 'Title', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'view!' => 'only-icon',
				),
			)
		);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'title_typography',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Typography', 'porto-functionality' ),
					'selector' => '{{WRAPPER}} strong',
				)
			);
			$this->add_control(
				'title_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Color', 'porto-functionality' ),
					'selectors' => array(
						'{{WRAPPER}} strong' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'title_pos',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Top Position', 'porto-functionality' ),
					'size_units' => array(
						'%',
						'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} strong' => 'top: {{SIZE}}{{UNIT}};',
					),
				)
			);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_circular_bar_value',
			array(
				'label'     => __( 'Value', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'view' => '',
				),
			)
		);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'value_typography',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Typography', 'porto-functionality' ),
					'selector' => '{{WRAPPER}} label',
				)
			);
			$this->add_control(
				'value_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Color', 'porto-functionality' ),
					'selectors' => array(
						'{{WRAPPER}} label' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'value_pos',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Position', 'porto-functionality' ),
					'size_units' => array(
						'%',
						'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} label' => 'top: {{SIZE}}{{UNIT}};',
					),
				)
			);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_circular_bar_icon',
			array(
				'label'     => __( 'Icon', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'view' => 'only-icon',
				),
			)
		);
			$this->add_control(
				'icon_size',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Size', 'porto-functionality' ),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'{{WRAPPER}} i' => 'font-size: {{SIZE}}{{UNIT}};',
					),
				)
			);
			$this->add_control(
				'icon_color',
				array(
					'type'  => Controls_Manager::COLOR,
					'label' => __( 'Color', 'porto-functionality' ),
				)
			);
		$this->end_controls_section();
	}

	protected function render() {
		if ( ! defined( 'PORTO_DIR' ) ) {
			return;
		}
		$atts         = $this->get_settings_for_display();
		$atts['type'] = 'custom';
		if ( isset( $atts['icon_cl'] ) && isset( $atts['icon_cl']['value'] ) ) {
			$atts['icon'] = $atts['icon_cl']['value'];
		}
		include PORTO_DIR . '/vc_templates/vc_pie.php';
	}

	protected function content_template() {
		global $porto_settings;
		?>
		<#
			let options = {};
			options['trackColor']          = settings.trackcolor;
			options['barColor']            = settings.barcolor ? settings.barcolor : '<?php echo esc_html( $porto_settings['skin-color'] ); ?>';
			options['scaleColor']          = settings.scalecolor;
			options['lineCap']             = settings.linecap;
			options['lineWidth']           = settings.line;
			options['size']                = settings.size;
			options['animate']             = {};
			options['animate']['duration'] = settings.speed;
			options['labelValue']          = settings.label_value;

			view.addRenderAttribute( 'wrapper', 'class', 'circular-bar center' );
			if ( settings.view ) {
				view.addRenderAttribute( 'wrapper', 'class', settings.view );
			}
			if ( settings.view_size ) {
				view.addRenderAttribute( 'wrapper', 'class', 'circular-bar-' + settings.view_size );
			}

			view.addRenderAttribute( 'innerWrapper', 'data-percent', settings.value );
			view.addRenderAttribute( 'innerWrapper', 'data-plugin-options', JSON.stringify( options ) );
			view.addRenderAttribute( 'innerWrapper', 'style', 'height:' + Number( settings.size ) + 'px' );
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<div class="circular-bar-chart" {{{ view.getRenderAttributeString( 'innerWrapper' ) }}}>
			<#
				if ( 'only-icon' == settings.view && settings.icon_cl && settings.icon_cl.value ) {
					view.addRenderAttribute( 'icon', 'class', settings.icon_cl.value );
					if ( settings.icon_color ) {
						view.addRenderAttribute( 'icon', 'style', 'color:' + settings.icon_color );
					}
			#>
				<i {{{ view.getRenderAttributeString( 'icon' ) }}}></i>
			<# } else if ( 'single-line' == settings.view ) { #>
				<# if ( settings.title ) { #>
					<strong>{{{ settings.title }}}</strong>
				<# } #>
			<# } else { #>
				<# if ( settings.title ) { #>
					<strong>{{{ settings.title }}}</strong>
				<# } #>
				<label><span class="percent">0</span>{{{ settings.units }}}</label>
			<# } #>
			</div>
		</div>
		<?php
	}
}
