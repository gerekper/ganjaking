<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Portfolios Slider Widget
 *
 * Porto Elementor widget to display portfolios slider.
 *
 * @since 5.4.1
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Recent_Portfolios_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_recent_portfolios';
	}

	public function get_title() {
		return __( 'Recent Portfolios', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'theme-elements' );
	}

	public function get_keywords() {
		return array( 'portfolio', 'article', 'slider', 'carousel' );
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'porto-elementor-widgets-js' );
		} else {
			return array();
		}
	}

	protected function _register_controls() {
		$slider_options = porto_vc_product_slider_fields();
		unset( $slider_options[8] );
		unset( $slider_options[9] );
		$slider_options = porto_update_vc_options_to_elementor( $slider_options );

		unset( $slider_options['dots_pos'], $slider_options['navigation']['default'] );

		$slider_options['nav_pos2']['condition']['navigation'] = 'yes';
		$slider_options['nav_type']['condition']['navigation'] = 'yes';

		$this->start_controls_section(
			'section_recent_portfolios',
			array(
				'label' => __( 'Recent Portfolios', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'porto-functionality' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Title', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'view',
			array(
				'label'   => __( 'View Type', 'porto-functionality' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array_combine( array_values( porto_sh_commons( 'portfolio_grid_view' ) ), array_keys( porto_sh_commons( 'portfolio_grid_view' ) ) ),
			)
		);

		$this->add_control(
			'info_view',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Info View Type', 'porto-functionality' ),
				'default' => '',
				'options' => array(
					''                 => __( 'Standard', 'porto-functionality' ),
					'left-info'        => __( 'Left Info', 'porto-functionality' ),
					'centered-info'    => __( 'Centered Info', 'porto-functionality' ),
					'bottom-info'      => __( 'Bottom Info', 'porto-functionality' ),
					'bottom-info-dark' => __( 'Bottom Info Dark', 'porto-functionality' ),
					'hide-info-hover'  => __( 'Hide Info Hover', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Image Size', 'porto-functionality' ),
				'options'   => array_combine( array_values( porto_sh_commons( 'image_sizes' ) ), array_keys( porto_sh_commons( 'image_sizes' ) ) ),
				'default'   => '',
				'condition' => array(
					'show_image' => 'yes',
				),
			)
		);

		$this->add_control(
			'thumb_bg',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Image Overlay Background', 'porto-functionality' ),
				'default' => '',
				'options' => array(
					''                => __( 'Standard', 'porto-functionality' ),
					'darken'          => __( 'Darken', 'porto-functionality' ),
					'lighten'         => __( 'Lighten', 'porto-functionality' ),
					'hide-wrapper-bg' => __( 'Transparent', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'thumb_image',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Hover Image Effect', 'porto-functionality' ),
				'default' => '',
				'options' => array(
					''        => __( 'Standard', 'porto-functionality' ),
					'zoom'    => __( 'Zoom', 'porto-functionality' ),
					'no-zoom' => __( 'No Zoom', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'ajax_load',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Enable Ajax Load', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'ajax_modal',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Ajax Load on Modal', 'porto-functionality' ),
				'condition' => array(
					'ajax_load' => 'yes',
				),
			)
		);

		$this->add_control(
			'number',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Portfolio Count', 'porto-functionality' ),
				'default' => 8,
			)
		);

		$this->add_control(
			'cats',
			array(
				'type'  => Controls_Manager::TEXT,
				'label' => __( 'Category IDs', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'post_in',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Portfolio IDs', 'porto-functionality' ),
				'description' => __( 'comma separated list of portfolio ids', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'items_desktop',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Items to show on Desktop', 'porto-functionality' ),
				'default' => 4,
			)
		);

		$this->add_control(
			'items_tablets',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Items to show on Tablets', 'porto-functionality' ),
				'default' => 3,
			)
		);

		$this->add_control(
			'items_mobile',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Items to show on Mobile', 'porto-functionality' ),
				'default' => 2,
			)
		);

		$this->add_control(
			'items_row',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Items Row', 'porto-functionality' ),
				'default' => 1,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_portfolios_slider_options',
			array(
				'label' => __( 'Slider Options', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'slider_config',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Change Slider Options', 'porto-functionality' ),
			)
		);

		foreach ( $slider_options as $key => $opt ) {
			unset( $opt['condition']['view'] );
			$opt['condition']['slider_config'] = 'yes';
			$this->add_control( $key, $opt );
		}

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( isset( $atts['navigation'] ) && 'yes' == $atts['navigation'] ) {
			$atts['show_nav'] = $atts['navigation'];
		} else {
			$atts['show_nav'] = false;
		}
		if ( isset( $atts['pagination'] ) && 'yes' == $atts['pagination'] ) {
			$atts['show_dots'] = $atts['pagination'];
		} else {
			$atts['show_dots'] = false;
		}

		if ( $template = porto_shortcode_template( 'porto_recent_portfolios' ) ) {
			include $template;
		}
	}
}
