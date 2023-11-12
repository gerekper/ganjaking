<?php
class PAFE_Crossfade_Multiple_Background_Images extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-crossfade-multiple-background-images';
	}

	public function pafe_register_controls( $element, $section_id ) {

		$element->start_controls_section(
			'pafe_crossfade_multiple_background_images',
			[
				'label' => __( 'PAFE Crossfade Multiple Background Images', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			'pafe_crossfade_multiple_background_images_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'This feature only works on the frontend.', 'pafe' ),
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'pafe_crossfade_multiple_background_images_speed',
			[
				'label' => __( 'Speed (Milliseconds)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => '7000',
				'description' => __( '1000 ms = 1 second', 'pafe' ),
				'condition' => [
					'pafe_crossfade_multiple_background_images_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_crossfade_multiple_background_images_speed_fadeout',
			[
				'label' => __( 'Fade Out Speed (Milliseconds)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => '1500',
				'description' => __( '1000 ms = 1 second', 'pafe' ),
				'condition' => [
					'pafe_crossfade_multiple_background_images_enable' => 'yes',
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'pafe_crossfade_multiple_background_image', [
				'label' => __( 'Background Image', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
			]
		);

		$element->add_control(
			'pafe_crossfade_multiple_background_images_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
			)
		);

		$element->end_controls_section();

	}

	public function before_render_element($element) {
		$settings = $element->get_settings();
		if (!empty($settings['pafe_crossfade_multiple_background_images_speed']) && !empty($settings['pafe_crossfade_multiple_background_images_enable'])) {
			if ( array_key_exists( 'pafe_crossfade_multiple_background_images_list',$settings ) ) {
				$list = $settings['pafe_crossfade_multiple_background_images_list'];	
				if( !empty($list[0]['pafe_crossfade_multiple_background_image']['url']) ) {

					$images = '';
					foreach ($list as $item) {
						if(!empty($item['pafe_crossfade_multiple_background_image']['url'])) {
							$images .= $item['pafe_crossfade_multiple_background_image']['url'] . ',';
						}
					}

					$images = rtrim($images,",");

					$element->add_render_attribute( '_wrapper', [
						'data-pafe-crossfade-multiple-background-images' => $images,
						'data-pafe-crossfade-multiple-background-images-speed' => $settings['pafe_crossfade_multiple_background_images_speed'],
						'data-pafe-crossfade-multiple-background-images-speed-fadeout' => $settings['pafe_crossfade_multiple_background_images_speed_fadeout'],
					] );
				}
			}
		}
	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_background/after_section_end', [ $this, 'pafe_register_controls' ], 20, 2 );
		add_action( 'elementor/element/container/section_background_overlay/after_section_end', [ $this, 'pafe_register_controls' ], 20, 2 );
		add_action( 'elementor/element/column/section_style/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/section/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/container/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/column/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
