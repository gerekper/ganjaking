<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Image Comparison widget
 *
 * @since 6.2.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Image_Comparison_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_image_comparison';
	}

	public function get_title() {
		return __( 'Porto Image Comparison', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'compare', 'image', 'before', 'after' );
	}

	public function get_icon() {
		return 'eicon-image-before-after';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'porto-elementor-widgets-js', 'jquery-event-move', 'porto-image-comparison' );
		} else {
			return array();
		}
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_image_comparison',
			array(
				'label' => __( 'Image Comparison', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'before_img',
			array(
				'type'        => Controls_Manager::MEDIA,
				'label'       => __( 'Before Image', 'porto-functionality' ),
				'description' => __( 'Upload a before image to display.', 'porto-functionality' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'after_img',
			array(
				'type'        => Controls_Manager::MEDIA,
				'label'       => __( 'After Image', 'porto-functionality' ),
				'description' => __( 'Upload a after image to display.', 'porto-functionality' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'orientation',
			array(
				'label'              => __( 'Handle Orientation', 'porto-functionality' ),
				'type'               => Controls_Manager::CHOOSE,
				'options'            => array(
					'horizontal' => array(
						'title' => __( 'Horizontal', 'porto-functionality' ),
						'icon'  => 'eicon-navigation-horizontal',
					),
					'vertical'   => array(
						'title' => __( 'Vertical', 'porto-functionality' ),
						'icon'  => 'eicon-navigation-vertical',
					),
				),
				'default'            => 'horizontal',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'offset',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Handle Offset', 'porto-functionality' ),
				'description' => __( 'Controls the left or top position of the handle on page load.', 'porto-functionality' ),
				'range'       => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 50,
				),
				'size_units'  => array(
					'px',
				),
			)
		);

		$this->add_control(
			'movement',
			array(
				'label'              => __( 'Handle Movement Control', 'porto-functionality' ),
				'type'               => Controls_Manager::CHOOSE,
				'options'            => array(
					'click'       => array(
						'title' => __( 'Drag & Click', 'porto-functionality' ),
						'icon'  => 'eicon-click',
					),
					'handle_only' => array(
						'title' => __( 'Drag only', 'porto-functionality' ),
						'icon'  => 'eicon-drag-n-drop',
					),
					'hover'       => array(
						'title' => __( 'Hover', 'porto-functionality' ),
						'icon'  => 'eicon-cursor-move',
					),
				),
				'default'            => 'click',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'icon_cl',
			array(
				'label'       => __( 'Icon Class (ex: fas fa-pencil-alt)', 'porto-functionality' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => __( 'Inputs the css class of the icon which is located in handle.', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'handle_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Handle Color', 'porto-functionality' ),
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} .porto-image-comparison-handle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'handle_bg_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Handle Background Color', 'porto-functionality' ),
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} .porto-image-comparison-handle' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		if ( $template = porto_shortcode_template( 'porto_image_comparison' ) ) {
			if ( is_array( $atts['before_img'] ) && ! empty( $atts['before_img']['id'] ) ) {
				$atts['before_img']  = (int) $atts['before_img']['id'];
			}
			if ( is_array( $atts['after_img'] ) && ! empty( $atts['after_img']['id'] ) ) {
				$atts['after_img']  = (int) $atts['after_img']['id'];
			}

			if ( is_array( $atts['offset'] ) && isset( $atts['offset']['size'] ) ) {
				$atts['offset'] = $atts['offset']['size'];
			}

			include $template;
		}
	}

	protected function content_template() {
		?>
		<#
			view.addRenderAttribute( 'wrapper', 'class', 'porto-image-comparison' );
			if ( 'vertical' == settings.orientation ) {
				view.addRenderAttribute( 'wrapper', 'class', 'porto-image-comparison-vertical' );
			}
			view.addRenderAttribute( 'wrapper', 'data-orientation', settings.orientation );
			view.addRenderAttribute( 'wrapper', 'data-offset', settings.offset.size ? parseInt( settings.offset.size, 10 ) / 100 : 0.5 );
			view.addRenderAttribute( 'wrapper', 'data-handle-action', settings.movement ? settings.movement : 'click' );

			if ( settings.icon_cl ) {
				view.addRenderAttribute( 'handle', 'class', settings.icon_cl );
			} else {
				view.addRenderAttribute( 'handle', 'class', 'Simple-Line-Icons-cursor-move' );
			}

			var before_html = '', after_html = '';
			if ( settings.before_img.url ) {
				before_html = '<img src="' + settings.before_img.url + '" class="porto-image-comparison-before">';
			}
			if ( settings.after_img.url ) {
				after_html = '<img src="' + settings.after_img.url + '" class="' + 'porto-image-comparison-' + ( settings.before_img.url ? 'after' : 'before' ) + '">';
			}
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			{{{ before_html }}}
			{{{ after_html }}}
			<div class="porto-image-comparison-handle">
				<i {{{ view.getRenderAttributeString( 'handle' ) }}}></i>
			</div>
		</div>
		<?php
	}
}
