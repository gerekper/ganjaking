<?php
require_once( __DIR__ . '/controls-manager.php' );

class PAFE_Section_Link extends \Elementor\Widget_Base {
	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-section-link';
	}

	public function pafe_register_controls( $element, $section_id ) {

		$element->start_controls_section(
			'pafe_section_link_section',
			[
				'label' => __( 'PAFE Section Link', 'pafe' ),
				'tab' => PAFE_Controls_Manager::TAB_PAFE,
			]
		);
		
		$element->add_control(
			'pafe_section_link',
			[
				'label' => __( 'Link', 'pafe' ),
				'type' => \Elementor\Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'description' => __( 'Note that it is not visible in edit/preview mode & can only be viewed on the frontend.', 'pafe' ),
				'label_block' => true,
			]
		);

		$element->end_controls_section();

	}

	public function before_render_section($element) {
		$settings = $element->get_settings_for_display();
		$link = $settings['pafe_section_link'];
		if( !empty($link['url']) ) { 
			$element->add_render_attribute( '_wrapper', [
				'data-pafe-section-link' => $link['url'],
				'data-pafe-section-link-external' => $link['is_external'],
			] );
		}
	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_layout/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/section_layout_container/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/section/before_render', [ $this, 'before_render_section'], 10, 1 );
		add_action( 'elementor/frontend/container/before_render', [ $this, 'before_render_section'], 10, 1 );
	}

}
