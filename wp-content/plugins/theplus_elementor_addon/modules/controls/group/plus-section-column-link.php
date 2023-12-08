<?php
use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Theplus_Section_Column_Link extends Elementor\Widget_Base {

	public function __construct() {
		$theplus_options = get_option('theplus_options');
		$plus_extras = theplus_get_option('general', 'extras_elements');		
		
		if ( ( isset($plus_extras) && empty($plus_extras) && empty($theplus_options) ) || ( !empty($plus_extras) && in_array('plus_section_column_link', $plus_extras)) ){

			add_action( 'elementor/element/column/_section_responsive/after_section_end', [ $this, 'tp_section_column_link' ], 10, 2 );
			add_action( 'elementor/element/section/_section_responsive/after_section_end', [ $this, 'tp_section_column_link' ], 10, 2 );
			add_action( 'elementor/element/common/section_custom_css_pro/after_section_end', [ $this, 'tp_section_column_link' ], 10, 2 );

			$experiments_manager = Plugin::$instance->experiments;		
			if($experiments_manager->is_feature_active( 'container' )){		
				add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'tp_section_column_link' ], 10, 2  );
			}

			add_action( 'elementor/frontend/before_render', [ $this, 'plus_before_render'], 10, 1 );
			//add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'tp_enqueue_scripts' ], 10 );
		}		
	}
	
	public function get_name() {
		return 'plus-section-column-link';
	}
	
	public function tp_section_column_link($element) {		

		$element->start_controls_section(
			'plus_sc_link_section',
			[
				'label' => esc_html__( 'Plus Extras : Wrapper Link', 'theplus' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			'sc_link_switch',
			[
				'label' => esc_html__( 'Wrapper Link', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$element->add_control(
			'sc_link',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],				
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'condition' => [
					'sc_link_switch' => 'yes',
				],
			]
		);
		$element->end_controls_section();
	}

	public function tp_enqueue_scripts() {
		wp_enqueue_script( 'plus-section-column-link', THEPLUS_ASSETS_URL . 'js/main/section-column-link/plus-section-column-link.min.js', array( 'jquery' ), '', true );
	}

	public function plus_before_render($element) {
		$settings = $element->get_settings();

		$linkSwitch = !empty($settings['sc_link_switch'] && $settings['sc_link_switch'] == 'yes') ? $settings['sc_link_switch'] : '';
		$WrapperLink = !empty($settings['sc_link']) && !empty($settings['sc_link']['url']) ? $settings['sc_link']['url'] : '';
		$WrapperExternal = !empty($settings['sc_link']) && !empty($settings['sc_link']['is_external']) ? $settings['sc_link']['is_external'] : 'no';
		
		if( !empty($linkSwitch) && !empty($WrapperLink) ){
			$element->add_render_attribute( '_wrapper', array(
				'data-tp-sc-link' => $WrapperLink,
				'data-tp-sc-link-external' => $WrapperExternal,
				'style' => 'cursor: pointer'
			) );
		}
	
	}
}