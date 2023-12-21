<?php
namespace Happy_Addons_Pro\Extension\Features;

use Elementor\Controls_Manager;
use Happy_Addons_Pro\Extensions_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Section {

	public static function init() {
		// Activate sections for widgets
		add_action( 'elementor/element/common/_section_style/after_section_end', [ __CLASS__, 'add_controls_sections' ], 1, 2 );
		// Activate column for sections
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ __CLASS__, 'add_controls_sections' ], 1, 2 );
		// Activate sections for sections
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ __CLASS__, 'add_controls_sections' ], 1, 2 );

		add_action( 'elementor/element/container/section_layout/after_section_end', [ __CLASS__, 'add_controls_sections' ], 1, 2 );

		if ( hapro_is_display_condition_enabled() ) {
			Extensions_Manager::load_display_condition();
		}
	}

	public static function add_controls_sections( $element, $args ) {
		$element->start_controls_section(
			'_section_happy_pro_features',
			[
				'label' => __( 'Happy Features', 'happy-addons-pro' ) . ha_get_section_icon(),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		if ( hapro_is_display_condition_enabled() ) {
			\Happy_Addons_Pro\Extension\Conditions\Display_Conditions::instance()->add_controls( $element , $args );
		}

		$element->end_controls_section();
	}
}

Section::init();
