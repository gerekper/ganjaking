<?php
/**
 * Filter Preset widget for Elementor
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Elementor
 * @version 4.0.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! class_exists( 'YITH_WCAN_Elementor_Filters' ) ) {
	/**
	 * Filters Preset Elementor Widget
	 *
	 * @since 4.0.0
	 */
	class YITH_WCAN_Elementor_Filters extends Widget_Base {

		/**
		 * Get widget name.
		 *
		 * Retrieve YITH_WCAN_Elementor_Filters widget name.
		 *
		 * @return string Widget name.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_name() {
			return 'yith_wcan_filters';
		}

		/**
		 * Get widget title.
		 *
		 * Retrieve YITH_WCAN_Elementor_Filters widget title.
		 *
		 * @return string Widget title.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_title() {
			return _x( 'YITH AJAX Filters Preset', '[ADMIN] Name of the preset elementor widget', 'yith-woocommerce-ajax-navigation' );
		}

		/**
		 * Get widget icon.
		 *
		 * Retrieve YITH_WCAN_Elementor_Filters widget icon.
		 *
		 * @return string Widget icon.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_icon() {
			return 'eicon-toggle';
		}

		/**
		 * Get widget categories.
		 *
		 * Retrieve the list of categories the YITH_WCAN_Elementor_Filters widget belongs to.
		 *
		 * @return array Widget categories.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_categories() {
			return array( 'general', 'yith' );
		}


		/**
		 * Register YITH_WCAN_Elementor_Filters widget controls.
		 *
		 * Adds different input fields to allow the user to change and customize the widget settings.
		 *
		 * @since  1.0.0
		 * @access protected
		 */
		protected function register_controls() {
			$presets         = YITH_WCAN_Preset_Factory::list_presets();
			$presets_options = array_merge(
				array(
					'' => _x( 'Choose an option', '[ELEMENTOR] Default preset option', 'yith-woocommerce-ajax-navigation' ),
				),
				$presets
			);

			$this->start_controls_section(
				'fields_section',
				array(
					'label' => _x( 'Filters', '[ELEMENTOR] Section title', 'yith-woocommerce-ajax-navigation' ),
					'tab'   => Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'slug',
				array(
					'label'   => _x( 'Preset', '[ELEMENTOR] Control label', 'yith-woocommerce-ajax-navigation' ),
					'type'    => Controls_Manager::SELECT,
					'options' => $presets_options,
					'default' => '',
				)
			);

			$this->end_controls_section();
		}

		/**
		 * Render YITH_WCAN_Elementor_Filters widget output on the frontend.
		 *
		 * @since  1.0.0
		 * @access protected
		 */
		protected function render() {

			$attribute_string = '';
			$settings         = $this->get_settings_for_display();

			foreach ( $settings as $key => $value ) {
				if ( empty( $value ) || ! is_scalar( $value ) ) {
					continue;
				}
				$attribute_string .= " {$key}=\"{$value}\"";
			}

			echo do_shortcode( "[yith_wcan_filters {$attribute_string}]" );
		}

	}
}
