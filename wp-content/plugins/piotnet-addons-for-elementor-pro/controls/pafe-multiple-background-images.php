<?php
class PAFE_Multiple_Background_Images extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-multiple-background-images';
	}

	public function pafe_register_controls( $element, $section_id ) {

		$element_name = $element->get_name();
		if($element_name != 'section' && $element_name != 'column' && $element_name != 'container') {
			$element->start_controls_section(
				'pafe_multiple_background_images',
				[
					'label' => __( 'PAFE Multiple Background Images', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
				]
			);
		} else {
			$element->start_controls_section(
				'pafe_multiple_background_images',
				[
					'label' => __( 'PAFE Multiple Background Images', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
		}

		$element->add_control(
			'pafe_multiple_background_images_note',
			[
				'label' => '',
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'Note that currently effect are not visible in edit/preview mode & can only be viewed on the frontend.', 'pafe' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'pafe_multiple_background_image', [
				'label' => __( 'Background Image', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
			]
		);

		$repeater->add_control(
			'pafe_multiple_background_image_position', [
				'label' => _x( 'Position', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'top left',
				'options' => [
					'top left' => _x( 'Top Left', 'Background Control', 'elementor' ),
					'top center' => _x( 'Top Center', 'Background Control', 'elementor' ),
					'top right' => _x( 'Top Right', 'Background Control', 'elementor' ),
					'center left' => _x( 'Center Left', 'Background Control', 'elementor' ),
					'center center' => _x( 'Center Center', 'Background Control', 'elementor' ),
					'center right' => _x( 'Center Right', 'Background Control', 'elementor' ),
					'bottom left' => _x( 'Bottom Left', 'Background Control', 'elementor' ),
					'bottom center' => _x( 'Bottom Center', 'Background Control', 'elementor' ),
					'bottom right' => _x( 'Bottom Right', 'Background Control', 'elementor' ),
				],
				'condition' => [
					'pafe_multiple_background_image[url]!' => '',
				],
			]
		);


		$repeater->add_control(
			'pafe_multiple_background_image_attachment', [
				'label' => _x( 'Attachment', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'initial',
				'options' => [
					'initial' => _x( 'Initial', 'Background Control', 'elementor' ),
					'scroll' => _x( 'Scroll', 'Background Control', 'elementor' ),
					'fixed' => _x( 'Fixed', 'Background Control', 'elementor' ),
				],
				'condition' => [
					'pafe_multiple_background_image[url]!' => '',
				],
			]
		);

		$repeater->add_control(
			'pafe_multiple_background_image_repeat', [
				'label' => _x( 'Repeat', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'no-repeat',
				'options' => [
					'no-repeat' => _x( 'No-repeat', 'Background Control', 'elementor' ),
					'repeat' => _x( 'Repeat', 'Background Control', 'elementor' ),
					'repeat-x' => _x( 'Repeat-x', 'Background Control', 'elementor' ),
					'repeat-y' => _x( 'Repeat-y', 'Background Control', 'elementor' ),
				],
				'condition' => [
					'pafe_multiple_background_image[url]!' => '',
				],
			]
		);

		$repeater->add_control(
			'pafe_multiple_background_image_size', [
				'label' => _x( 'Size', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => [
					'auto' => _x( 'Auto', 'Background Control', 'elementor' ),
					'cover' => _x( 'Cover', 'Background Control', 'elementor' ),
					'contain' => _x( 'Contain', 'Background Control', 'elementor' ),
				],
				'condition' => [
					'pafe_multiple_background_image[url]!' => '',
				],
			]
		);


		$element->add_control(
			'pafe_multiple_background_images_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
			)
		);

		$element->end_controls_section();

	}

	public function before_render_element($element) {
		$settings = $element->get_settings();
		if ( array_key_exists( 'pafe_multiple_background_images_list',$settings ) ) {
			$list = $settings['pafe_multiple_background_images_list'];	
			if( !empty($list[0]['pafe_multiple_background_image']['url']) ) {

				$background_image = 'background-image:';
				$background_position = 'background-position:';
				$background_attachment = 'background-attachment:';
				$background_repeat = 'background-repeat:';
				$background_size = 'background-size:';

				$index = 0;
				foreach ($list as $item) {
					$index++;
					if(!empty($item['pafe_multiple_background_image']['url'])) {

						if($index == count($list)) {
							$sup = ';';
						} else {
							$sup = ',';
						}

						$background_image .= 'url(' . $item['pafe_multiple_background_image']['url'] . ')' . $sup;
						$background_position .= $item['pafe_multiple_background_image_position'] . $sup;
						$background_attachment .= $item['pafe_multiple_background_image_attachment'] . $sup;
						$background_repeat .= $item['pafe_multiple_background_image_repeat'] . $sup;
						$background_size .= $item['pafe_multiple_background_image_size'] . $sup;
					}
				}

				$style = $background_image . $background_position . $background_attachment . $background_repeat . $background_size;

				$element->add_render_attribute( '_wrapper', [
					'style' => $style,
				] );
			}
		}
	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_background/after_section_end', [ $this, 'pafe_register_controls' ], 20, 2 );
		add_action( 'elementor/element/container/section_background_overlay/after_section_end', [ $this, 'pafe_register_controls' ], 20, 2 );
		add_action( 'elementor/element/column/section_style/after_section_end', [ $this, 'pafe_register_controls' ], 20, 2 );
		add_action( 'elementor/frontend/section/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/container/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/column/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
