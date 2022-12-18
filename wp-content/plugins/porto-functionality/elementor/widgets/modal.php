<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Modal Widget
 *
 * Porto Elementor widget to display a modal dialog.
 *
 * @since 1.7.1
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Modal_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_modal';
	}

	public function get_title() {
		return __( 'Porto Modal Dialog Box', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'modal', 'dialog', 'popup box', 'overlay box' );
	}

	public function get_icon() {
		return 'eicon-lightbox-expand';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_modal_box',
			array(
				'label' => __( 'Porto Modal Box', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'modal_contain',
			array(
				'label'       => __( 'What\'s in Modal Popup?', 'porto-functionality' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'html'    => __( 'Miscellaneous Things', 'porto-functionality' ),
					'youtube' => __( 'Youtube Video', 'porto-functionality' ),
					'vimeo'   => __( 'Vimeo Video', 'porto-functionality' ),
				),
				'default'     => 'html',
				'description' => __( "Please put the embed code in the content for videos, eg: <a href='http://bsf.io/kuv3-' target='_blank'>http://bsf.io/kuv3-</a><br>For hosted video - Add any video with WordPress media uploader or with <a href='https://codex.wordpress.org/Video_Shortcode' target='_blank'>[video]</a> shortcode.", 'porto-functionality' ),
			)
		);

		$this->add_control(
			'youtube_url',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Youtube URL', 'porto-functionality' ),
				'condition' => array(
					'modal_contain' => 'youtube',
				),
			)
		);

		$this->add_control(
			'vimeo_url',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Vimeo URL', 'porto-functionality' ),
				'condition' => array(
					'modal_contain' => 'vimeo',
				),
			)
		);

		$this->add_control(
			'content',
			array(
				'type'        => Controls_Manager::WYSIWYG,
				'label'       => __( 'Modal Content', 'porto-functionality' ),
				'description' => __( 'Content that will be displayed in Modal Popup.', 'porto-functionality' ),
				'condition'   => array(
					'modal_contain' => 'html',
				),
			)
		);

		$this->add_control(
			'modal_on',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Display Modal On -', 'porto-functionality' ),
				'options'     => array(
					'onload'          => __( 'On Page Load', 'porto-functionality' ),
					'image'           => __( 'Image', 'porto-functionality' ),
					'custom-selector' => __( 'Selector', 'porto-functionality' ),
				),
				'default'     => 'onload',
				'description' => __( 'When should the popup be initiated?', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'modal_onload_timeout',
			array(
				'type'      => Controls_Manager::NUMBER,
				'label'     => __( 'Timeout in seconds', 'porto-functionality' ),
				'min'       => 0,
				'max'       => 100,
				'condition' => array(
					'modal_on' => 'onload',
				),
			)
		);

		$this->add_control(
			'modal_on_selector',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Class and/or ID', 'porto-functionality' ),
				'description' => __( 'Add .Class and/or #ID to open your modal. Multiple ID or Classes separated by comma', 'porto-functionality' ),
				'condition'   => array(
					'modal_on' => 'custom-selector',
				),
			)
		);

		$this->add_control(
			'btn_img',
			array(
				'type'        => Controls_Manager::MEDIA,
				'label'       => __( 'Upload Image', 'porto-functionality' ),
				'description' => __( 'Upload the custom image / image banner.', 'porto-functionality' ),
				'condition'   => array(
					'modal_on' => 'image',
				),
			)
		);

		$this->add_control(
			'modal_style',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Modal Box Style', 'porto-functionality' ),
				'options'   => array(
					'mfp-fade'       => __( 'Fade', 'porto-functionality' ),
					'my-mfp-zoom-in' => __( 'Zoom in', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'overlay_bg_color',
			array(
				'type'  => Controls_Manager::COLOR,
				'label' => __( 'Overlay Background Color', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'overlay_bg_opacity',
			array(
				'type'        => Controls_Manager::NUMBER,
				'label'       => __( 'Overlay Background Opacity (%)', 'porto-functionality' ),
				'default'     => 80,
				'min'         => 10,
				'max'         => 100,
				'description' => __( 'Select opacity of overlay background.', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'init_extra_class',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Extra Class (Button/Image)', 'porto-functionality' ),
				'description' => __( 'Provide ex class for this button/image.', 'porto-functionality' ),
				'condition'   => array(
					'modal_on' => 'image',
				),
			)
		);

		$this->add_control(
			'el_class',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Extra Class', 'porto-functionality' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		if ( $template = porto_shortcode_template( 'porto_modal' ) ) {
			$content = $atts['content'];
			include $template;
		}
	}
}
