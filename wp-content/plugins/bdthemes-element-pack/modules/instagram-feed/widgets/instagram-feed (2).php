<?php
namespace ElementPack\Modules\InstagramFeed\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Instagram_Feed extends Module_Base {

	public function get_name() {
		return 'bdt-instagram-feed';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Instagram Feed', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-instagram-feed';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'instagram', 'feed', 'gallery', 'photos', 'images' ];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/Wf7naA7EL7s';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' )
			]
		);

		$this->add_control(
			'limit',
			[
				'label'   => esc_html__( 'Limit (Max 33)', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 12
			]
		);

		$this->add_control(
			'columns',
			[
				'label'   => esc_html__( 'Columns (Max 10)', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 8
			]
		);

		$this->add_control(
			'imageres',
			[
				'label'   => esc_html__( 'Image Size', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'full',
				'options' => [
					'auto'   => esc_html__( 'Auto', 'bdthemes-element-pack' ),
					'full'   => esc_html__( 'Full', 'bdthemes-element-pack' ),
					'medium' => esc_html__( 'Medium', 'bdthemes-element-pack' ),
					'thumb'  => esc_html__( 'Thumb', 'bdthemes-element-pack' )
				]
			]
		);

		$this->add_control(
			'showheader',
			[
				'label' => esc_html__( 'Show Header', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER
			]
		);

		$this->add_control(
			'showbutton',
			[
				'label' => esc_html__( 'Show Button', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER
			]
		);

		$this->add_control(
			'buttontext',
			[
				'label'       => esc_html__( 'Button Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Load More...', 'bdthemes-element-pack' ),
				'default'     => esc_html__( 'Load More...', 'bdthemes-element-pack' ),
				'label_block' => true,
				'condition'   => [
					'showbutton' => 'yes'
				]
			]
		);

		$this->add_control(
			'showfollow',
			[
				'label' => esc_html__( 'Show Follow', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER
			]
		);

		$this->add_control(
			'followtext',
			[
				'label'       => esc_html__( 'Follow Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Follow on Instagram', 'bdthemes-element-pack' ),
				'default'     => esc_html__( 'Follow on Instagram', 'bdthemes-element-pack' ),
				'label_block' => true,
				'condition'   => [
					'showfollow' => 'yes'
				]
			]
		);
	
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'imagepadding',
			[
				'label'   => esc_html__( 'Image Padding', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100
					]
				]
			]
		);

		$this->add_control(
			'headercolor',
			[
				'label'     => esc_html__( 'Header Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'showheader' => 'yes'
				]
			]
		);

		$this->add_control(
			'buttoncolor',
			[
				'label'     => esc_html__( 'Button Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'showbutton' => 'yes'
				]
			]
		);

		$this->add_control(
			'buttontextcolor',
			[
				'label'     => esc_html__( 'Button Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'showbutton' => 'yes'
				]
			]
		);

		$this->add_control(
			'followcolor',
			[
				'label'     => esc_html__( 'Follow Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'showfollow' => 'yes'
				]
			]
		);

		$this->add_control(
			'followtextcolor',
			[
				'label'     => esc_html__( 'Follow Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'showfollow' => 'yes'
				]
			]
		);

		$this->end_controls_section();
	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

		$attributes = [
			'num'              => $settings['limit'],
			'cols'             => $settings['columns'],
			'imageres'         => $settings['imageres'],
			'imagepadding'     => $settings['imagepadding']['size'],
			'imagepaddingunit' =>'px',
			'showheader'       => $settings['showheader'] ? 'true' : 'false',
			'showbutton'       => $settings['showbutton'] ? 'true' : 'false',
			'showfollow'       => $settings['showfollow'] ? 'true' : 'false',
			'headercolor'      => $settings['headercolor'],
			'buttoncolor'      => $settings['buttoncolor'],
			'buttontextcolor'  => $settings['buttontextcolor'],
			'buttontext'       => $settings['buttontext'],
			'followcolor'      => $settings['followcolor'],
			'followtextcolor'  => $settings['followtextcolor'],
			'followtext'       => $settings['followtext'],
		];

		$this->add_render_attribute( 'shortcode', $attributes );

		$shortcode   = [];
		$shortcode[] = sprintf( '[instagram-feed %s]', $this->get_render_attribute_string( 'shortcode' ) );

		return implode("", $shortcode);
	}

	protected function render() {
		echo do_shortcode( $this->get_shortcode() );
	}
}
