<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Faqs Widget
 *
 * Porto Elementor widget to display faqs.
 *
 * @since 5.4.3
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Faqs_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_faqs';
	}

	public function get_title() {
		return __( 'Faqs', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'theme-elements' );
	}

	public function get_keywords() {
		return array( 'faqs', 'posts' );
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'porto-elementor-widgets-js' );
		} else {
			return array();
		}
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_faqs',
			array(
				'label' => __( 'Faqs Layout', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'type'  => Controls_Manager::TEXT,
				'label' => __( 'Title', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'cats',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Category IDs', 'porto-functionality' ),
				'description' => __( 'comma separated list of category ids', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'post_in',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'FAQ IDs', 'porto-functionality' ),
				'description' => __( 'comma separated list of faq ids', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'number',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'FAQs Count', 'porto-functionality' ),
				'min'     => 1,
				'max'     => 99,
				'default' => 8,
			)
		);

		$this->add_control(
			'view_more',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Show Archive Link', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'view_more_class',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Extra class name for Archive Link', 'porto-functionality' ),
				'condition' => array(
					'view_more' => 'yes',
				),
			)
		);

		$this->add_control(
			'filter',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Show Filter', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'pagination',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Show Pagination', 'porto-functionality' ),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		if ( $template = porto_shortcode_template( 'porto_faqs' ) ) {
			include $template;
		}
	}

	protected function content_template() {}
}
