<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Header Builder Search form widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_HB_Search_Form_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_hb_search_form';
	}

	public function get_title() {
		return __( 'Search Form', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-hb' );
	}

	public function get_keywords() {
		return array( 'search', 'form', 'query' );
	}

	public function get_icon() {
		return 'Simple-Line-Icons-magnifier';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_hb_search_form',
			array(
				'label' => __( 'Search Form Layout', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'placeholder_text',
			array(
				'type'  => Controls_Manager::TEXT,
				'label' => __( 'Placeholder Text', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'category_filter',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Show Category filter', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'category_filter_mobile',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Show Categories on Mobile', 'porto-functionality' ),
				'condition' => array(
					'category_filter' => 'yes',
				),
			)
		);

		$this->add_control(
			'popup_pos',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Popup Position', 'porto-functionality' ),
				'description' => __( 'This works for only "Popup 1" and "Popup 2" and "Form" search layout on mobile. You can change search layout using Porto -> Theme Options -> Header -> Search Form -> Search Layout.', 'porto-functionality' ),
				'options'     => array(
					''       => __( 'Default', 'porto-functionality' ),
					'left'   => __( 'Left', 'porto-functionality' ),
					'center' => __( 'Center', 'porto-functionality' ),
					'right'  => __( 'Right', 'porto-functionality' ),
				),
				'default'     => '',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_hb_search_form_style',
			array(
				'label' => __( 'Search Form Style', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'toggle_size',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Search Icon Size', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 40,
					),
					'em' => array(
						'step' => 0.1,
						'min'  => 0.1,
						'max'  => 4,
					),
				),
				'size_units' => array(
					'px',
					'em',
				),
				'selectors'  => array(
					'#header .searchform button, #header .searchform-popup .search-toggle' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'toggle_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Search Icon Color', 'porto-functionality' ),
				'default'   => '',
				'selectors' => array(
					'#header .searchform button, #header .searchform-popup .search-toggle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'input_size',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Input Box Width', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 800,
					),
					'em' => array(
						'step' => 0.1,
						'min'  => 0.1,
						'max'  => 80,
					),
					'%'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
				),
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors'  => array(
					'#header .searchform input, #header .searchform.searchform-cats input' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'height',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Height', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 80,
					),
					'em' => array(
						'step' => 0.1,
						'min'  => 0.1,
						'max'  => 8,
					),
				),
				'size_units' => array(
					'px',
					'em',
				),
				'selectors'  => array(
					'#header .searchform input, #header .searchform select, #header .searchform .selectric .label, #header .searchform button' => 'height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'border_width',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Border Width', 'porto-functionality' ),
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 10,
					),
				),
				'selectors' => array(
					'#header .searchform' => 'border-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'border_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Border Color', 'porto-functionality' ),
				'default'   => '',
				'selectors' => array(
					'#header .searchform' => 'border-color: {{VALUE}};',
					'#header .searchform-popup .search-toggle:after' => 'border-bottom-color: {{VALUE}};',
				),
			)
		);

		$border_radius_selectors = array(
			'#header .searchform'        => 'border-radius: {{SIZE}}{{UNIT}};',
			'#header .searchform input'  => 'border-radius: {{SIZE}}{{UNIT}} 0 0 {{SIZE}}{{UNIT}};',
			'#header .searchform button' => 'border-radius: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0;',
		);
		if ( is_rtl() ) {
			$border_radius_selectors = array(
				'#header .searchform'        => 'border-radius: {{SIZE}}{{UNIT}};',
				'#header .searchform input'  => 'border-radius: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0;',
				'#header .searchform button' => 'border-radius: {{SIZE}}{{UNIT}} 0 0 {{SIZE}}{{UNIT}};',
			);
		}
		$this->add_control(
			'border_radius',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Border Radius', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 40,
					),
					'em' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 4,
					),
				),
				'size_units' => array(
					'px',
					'em',
				),
				'selectors'  => $border_radius_selectors,
			)
		);

		$this->add_control(
			'divider_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Separator Color', 'porto-functionality' ),
				'default'   => '',
				'selectors' => array(
					'#header .searchform input, #header .searchform select, #header .searchform .selectric, #header .searchform .selectric-hover .selectric, #header .searchform .selectric-open .selectric, #header .searchform .autocomplete-suggestions, #header .searchform .selectric-items' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( function_exists( 'porto_header_elements' ) ) {
			global $porto_settings;
			$backup_cat_filter                    = $porto_settings['search-cats'];
			$backup_cat_filter_mobile             = $porto_settings['search-cats-mobile'];
			$porto_settings['search-cats']        = ! empty( $settings['category_filter'] ) ? true : false;
			$porto_settings['search-cats-mobile'] = ! empty( $settings['category_filter_mobile'] ) ? true : false;
			if ( ! empty( $settings['placeholder_text'] ) ) {
				$backup_placeholder                   = $porto_settings['search-placeholder'];
				$porto_settings['search-placeholder'] = $settings['placeholder_text'];
			}

			$el_cls = '';
			if ( ! empty( $settings['popup_pos'] ) ) {
				if ( 'simple' == $porto_settings['search-layout'] || 'large' == $porto_settings['search-layout'] || 'advanced' == $porto_settings['search-layout'] ) {
					$el_cls = 'search-popup-' . $settings['popup_pos'];
				}
			}

			porto_header_elements( array( (object) array( 'search-form' => '' ) ), $el_cls );
			$porto_settings['search-cats']        = $backup_cat_filter;
			$porto_settings['search-cats-mobile'] = $backup_cat_filter_mobile;
			if ( ! empty( $settings['placeholder_text'] ) ) {
				$porto_settings['search-placeholder'] = $backup_placeholder;
			}
		}
	}
}
