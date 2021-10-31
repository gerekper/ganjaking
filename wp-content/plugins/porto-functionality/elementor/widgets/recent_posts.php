<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Posts Slider Widget
 *
 * Porto Elementor widget to display posts slider.
 *
 * @since 5.1.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Recent_Posts_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_recent_posts';
	}

	public function get_title() {
		return __( 'Porto Recent Posts', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'blog', 'posts', 'article', 'slider', 'carousel' );
	}

	public function get_icon() {
		return 'eicon-posts-carousel';
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
		$slider_options['dots_pos']['options']['show-dots-title'] = __( 'Top beside title', 'porto-functionality' );

		unset( $slider_options['navigation']['default'] );

		$slider_options['nav_pos2']['condition']['navigation'] = 'yes';
		$slider_options['nav_type']['condition']['navigation'] = 'yes';

		$this->start_controls_section(
			'section_recent_posts',
			array(
				'label' => __( 'Recent Posts', 'porto-functionality' ),
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
				'options' => array(
					''        => __( 'Standard', 'porto-functionality' ),
					'style-1' => __( 'Read More Link', 'porto-functionality' ),
					'style-2' => __( 'Post Meta', 'porto-functionality' ),
					'style-3' => __( 'Read More Button', 'porto-functionality' ),
					'style-4' => __( 'Side Image', 'porto-functionality' ),
					'style-5' => __( 'Post Cats', 'porto-functionality' ),
					'style-7' => __( 'Post Author with photo', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'author',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Author Name', 'porto-functionality' ),
				'condition' => array(
					'view' => array( 'style-1', 'style-3' ),
				),
				'default'   => '',
				'options'   => array(
					''     => __( 'Standard', 'porto-functionality' ),
					'show' => __( 'Show', 'porto-functionality' ),
					'hide' => __( 'Hide', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'btn_style',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Button Style', 'porto-functionality' ),
				'condition' => array(
					'view' => array( 'style-3' ),
				),
				'default'   => '',
				'options'   => array(
					''            => __( 'Standard', 'porto-functionality' ),
					'btn-normal'  => __( 'Normal', 'porto-functionality' ),
					'btn-borders' => __( 'Borders', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'btn_size',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Button Size', 'porto-functionality' ),
				'condition' => array(
					'view' => array( 'style-3' ),
				),
				'default'   => '',
				'options'   => array(
					''           => __( 'Standard', 'porto-functionality' ),
					'btn-normal' => __( 'Normal', 'porto-functionality' ),
					'btn-sm'     => __( 'Small', 'porto-functionality' ),
					'btn-xs'     => __( 'Extra Small', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'btn_color',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Button Color', 'porto-functionality' ),
				'condition' => array(
					'view' => array( 'style-3' ),
				),
				'default'   => '',
				'options'   => array(
					''               => __( 'Standard', 'porto-functionality' ),
					'btn-default'    => __( 'Default', 'porto-functionality' ),
					'btn-primary'    => __( 'Primary', 'porto-functionality' ),
					'btn-secondary'  => __( 'Secondary', 'porto-functionality' ),
					'btn-tertiary'   => __( 'Tertiary', 'porto-functionality' ),
					'btn-quaternary' => __( 'Quaternary', 'porto-functionality' ),
					'btn-dark'       => __( 'Dark', 'porto-functionality' ),
					'btn-light'      => __( 'Light', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'number',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Posts Count', 'porto-functionality' ),
				'default' => 8,
			)
		);

		$this->add_control(
			'cats',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Category IDs', 'porto-functionality' ),
				'options'     => 'category',
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$this->add_control(
			'show_image',
			array(
				'type'    => Controls_Manager::SWITCHER,
				'label'   => __( 'Show Post Image', 'porto-functionality' ),
				'default' => 'yes',
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
			'show_metas',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Show Post Metas', 'porto-functionality' ),
				'default'   => 'yes',
				'condition' => array(
					'view' => array( '', 'style-1', 'style-2', 'style-3', 'style-4', 'style-5' ),
				),
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Excerpt Length', 'porto-functionality' ),
				'default' => 20,
			)
		);

		$this->add_control(
			'items',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Items to show on Large Desktop', 'porto-functionality' ),
				'default' => '',
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
			'section_posts_slider_options',
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

		if ( $template = porto_shortcode_template( 'porto_recent_posts' ) ) {
			if ( ! empty( $atts['cats'] ) && is_array( $atts['cats'] ) ) {
				$atts['cats'] = implode( ',', $atts['cats'] );
			}
			include $template;
		}
	}
}
