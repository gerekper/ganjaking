<?php
/**
 * Affiliate Dashboard widget for Elementor
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.7.0
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if( ! class_exists( 'YITH_WCAF_Elementor_Affiliate_Dashboard' ) ) {
	class YITH_WCAF_Elementor_Affiliate_Dashboard extends \Elementor\Widget_Base {

		/**
		 * Get widget name.
		 *
		 * Retrieve YITH_WCAF_Elementor_Affiliate_Dashboard widget name.
		 *
		 * @return string Widget name.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_name() {
			return 'yith_wcaf_affiliate_dashboard';
		}

		/**
		 * Get widget title.
		 *
		 * Retrieve YITH_WCAF_Elementor_Affiliate_Dashboard widget title.
		 *
		 * @return string Widget title.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_title() {
			return _x( 'YITH Affiliate Dashboard', 'Elementor widget name', 'yith-woocommerce-affiliates' );
		}

		/**
		 * Get widget icon.
		 *
		 * Retrieve YITH_WCAF_Elementor_Affiliate_Dashboard widget icon.
		 *
		 * @return string Widget icon.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_icon() {
			return 'eicon-gallery-justified';
		}

		/**
		 * Get widget categories.
		 *
		 * Retrieve the list of categories the YITH_WCAF_Elementor_Affiliate_Dashboard widget belongs to.
		 *
		 * @return array Widget categories.
		 * @since  1.0.0
		 * @access public
		 *
		 */
		public function get_categories() {
			return [ 'general', 'yith' ];
		}

		/**
		 * Register YITH_WCAF_Elementor_Affiliate_Dashboard widget controls.
		 *
		 * Adds different input fields to allow the user to change and customize the widget settings.
		 *
		 * @since  1.0.0
		 * @access protected
		 */
		protected function _register_controls() {

			$this->start_controls_section(
				'fields_section',
				[
					'label' => _x( 'Dashboard details', 'Elementor section title', 'yith-woocommerce-affiliates' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'show_dashboard_links',
				[
					'label'   => _x( 'Show navigation menu', 'Elementor control label', 'yith-woocommerce-affiliates' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'yes' => __( 'Show menu', 'yith-woocommerce-affiliates' ),
						'no'  => __( 'Hide menu', 'yith-woocommerce-affiliates' ),
					],
					'default' => 'no',
				]
			);

			$this->end_controls_section();
		}

		/**
		 * Render YITH_WCAF_Elementor_Affiliate_Dashboard widget output on the frontend.
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

			echo do_shortcode( "[yith_wcaf_affiliate_dashboard {$attribute_string}]" );
		}

	}
}