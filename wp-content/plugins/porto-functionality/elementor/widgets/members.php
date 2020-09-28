<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Members Widget
 *
 * Porto Elementor widget to display members.
 *
 * @since 5.4.1
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Members_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_members';
	}

	public function get_title() {
		return __( 'Members', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'theme-elements' );
	}

	public function get_keywords() {
		return array( 'member', 'person', 'author' );
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
			'section_members',
			array(
				'label' => __( 'Member Layout', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'porto-functionality' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Title', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'style',
			array(
				'label'   => __( 'Style', 'porto-functionality' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					''         => __( 'Baisc', 'porto-functionality' ),
					'advanced' => __( 'Advanced', 'porto-functionality' ),
				),
				'default' => '',
			)
		);

		$this->add_control(
			'columns',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Columns', 'porto-functionality' ),
				'condition' => array(
					'style' => array( '' ),
				),
				'default'   => '4',
				'options'   => porto_sh_commons( 'member_columns' ),
			)
		);

		$this->add_control(
			'view',
			array(
				'label'     => __( 'View Type', 'porto-functionality' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'classic',
				'options'   => array_combine( array_values( porto_sh_commons( 'member_view' ) ), array_keys( porto_sh_commons( 'member_view' ) ) ),
				'condition' => array(
					'style' => array( '' ),
				),
			)
		);

		$this->add_control(
			'hover_image_effect',
			array(
				'label'   => __( 'Hover Image Effect', 'porto-functionality' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array_combine( array_values( porto_sh_commons( 'custom_zoom' ) ), array_keys( porto_sh_commons( 'custom_zoom' ) ) ),
				'default' => 'zoom',
			)
		);

		$this->add_control(
			'overview',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Show Overview', 'porto-functionality' ),
				'default'   => 'yes',
				'condition' => array(
					'style' => array( '' ),
				),
			)
		);

		$this->add_control(
			'socials',
			array(
				'type'    => Controls_Manager::SWITCHER,
				'label'   => __( 'Show Social Links', 'porto-functionality' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'role',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Show Role', 'porto-functionality' ),
				'condition' => array(
					'view' => 'outimage_cat',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_members_selector',
			array(
				'label' => __( 'Members Selector', 'porto-functionality' ),
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
				'label'       => __( 'Member IDs', 'porto-functionality' ),
				'description' => __( 'comma separated list of member ids', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'number',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Members Count', 'porto-functionality' ),
				'default' => 8,
				'min'     => 1,
				'max'     => 100,
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
			'pagination',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Show Pagination', 'porto-functionality' ),
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

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		if ( $template = porto_shortcode_template( 'porto_members' ) ) {
			include $template;
		}
	}

	protected function content_template() {}
}
