<?php
class PAFE_Display_Inline_Block extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-display-inline-block';
	}

	public function pafe_register_controls( $element, $section_id ) {

		$element_name = $element->get_name();

		if ($element_name != 'section' && $element_name != 'column') {
			$element->start_controls_section(
				'pafe_display_inline_block_section',
				[
					'label' => __( 'PAFE Display Inline Block', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
				]
			);

			$element->add_responsive_control(
				'pafe_display_inline_block_enable',
				[
					'label' => __( 'Enable Display Inline Block', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'label_block' => true,
					'options' => [
						'display: block;' => __( 'No', 'pafe' ),
						'display: inline-block; margin-bottom: 0; width: auto;' => __( 'Yes', 'pafe' ),
					],
					'desktop_default' => 'display: block;',
					'tablet_default' => 'display: block;',
					'mobile_default' => 'display: block;',
					'laptop_default' => 'display: block;',
					'devices' => [ 'desktop', 'tablet', 'mobile','laptop','mobile_extra','tablet_extra','widescreen'],
				]
			);

			$element->add_responsive_control(
				'pafe_display_inline_block_fix',
				[
					'label' => __( 'Fix for Elementor 2.5.x', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
					'condition' => [
						'pafe_display_inline_block_enable' => [
							'display: inline-block; margin-bottom: 0; width: auto;',
						]
					],
				]
			);

			$element->add_responsive_control(
				'pafe_display_inline_block_vertical_align',
				[
					'label' => __( 'Vertical Align', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'label_block' => true,
					'default' => 'initial',
					'options' => [
						'-webkit-baseline-middle' => __( '-webkit-baseline-middle', 'pafe' ),
						'baseline' => __( 'baseline', 'pafe' ),
						'bottom' => __( 'bottom', 'pafe' ),
						'inherit' => __( 'inherit', 'pafe' ),
						'initial' => __( 'initial', 'pafe' ),
						'middle' => __( 'middle', 'pafe' ),
						'sub' => __( 'sub', 'pafe' ),
						'super' => __( 'super', 'pafe' ),
						'text-bottom' => __( 'text-bottom', 'pafe' ),
						'text-top' => __( 'text-top', 'pafe' ),
						'top' => __( 'top', 'pafe' ),
						'unset' => __( 'unset', 'pafe' ),
					],
                    'devices' => [ 'desktop', 'tablet', 'mobile','laptop','mobile_extra','tablet_extra','widescreen'],
					'selectors' => [
						'{{WRAPPER}}' => 'vertical-align: {{pafe_display_inline_block_vertical_align}};',
					],
					'condition' => [
						'pafe_display_inline_block_enable' => [
							'display: inline-block; margin-bottom: 0; width: auto;',
						]
					],
				]
			);

			$element->end_controls_section();
		}

	}

	public function before_render_element($element) {
		$settings = $element->get_settings();

		if ( ( !empty($settings['pafe_display_inline_block_enable']) || !empty($settings['pafe_display_inline_block_enable_laptop']) || !empty($settings['pafe_display_inline_block_enable_widescreen']) ) && $settings['pafe_display_inline_block_enable'] == 'display: inline-block; margin-bottom: 0; width: auto;') {
            $element->add_render_attribute( '_wrapper', [
                'class' => 'pafe-display-inline-block-desktop',
            ] );
		}

        if ( !empty( $settings['pafe_display_inline_block_enable_tablet'] ) && $settings['pafe_display_inline_block_enable_tablet'] == 'display: inline-block; margin-bottom: 0; width: auto;' ) {
            $element->add_render_attribute( '_wrapper', [
                'class' => 'pafe-display-inline-block-tablet',
            ] );
		}

        if ( !empty( $settings['pafe_display_inline_block_enable_mobile'] ) && $settings['pafe_display_inline_block_enable_mobile'] == 'display: inline-block; margin-bottom: 0; width: auto;' ) {
            $element->add_render_attribute( '_wrapper', [
                'class' => 'pafe-display-inline-block-mobile',
            ] );
		}
	}
		
	protected function init_control() {
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
