<?php
/**
 * Reset button widget for Elementor
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

if ( ! class_exists( 'YITH_WCAN_Elementor_Reset_Button' ) ) {
	/**
	 * Reset Filters Button Elementor Widget
	 *
	 * @since 4.0.0
	 */
	class YITH_WCAN_Elementor_Reset_Button extends Widget_Base {

		/**
		 * Get widget name.
		 *
		 * Retrieve YITH_WCAN_Elementor_Reset_Button widget name.
		 *
		 * @return string Widget name.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_name() {
			return 'yith_wcan_reset_button';
		}

		/**
		 * Get widget title.
		 *
		 * Retrieve YITH_WCAN_Elementor_Reset_Button widget title.
		 *
		 * @return string Widget title.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_title() {
			return _x( 'YITH AJAX Filters Reset Button', '[ADMIN] Name of the preset elementor widget', 'yith-woocommerce-ajax-navigation' );
		}

		/**
		 * Get widget icon.
		 *
		 * Retrieve YITH_WCAN_Elementor_Reset_Button widget icon.
		 *
		 * @return string Widget icon.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_icon() {
			return 'eicon-button';
		}

		/**
		 * Get widget categories.
		 *
		 * Retrieve the list of categories the YITH_WCAN_Elementor_Reset_Button widget belongs to.
		 *
		 * @return array Widget categories.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_categories() {
			return array( 'general', 'yith' );
		}


		/**
		 * Register YITH_WCAN_Elementor_Reset_Button widget controls.
		 *
		 * Adds different input fields to allow the user to change and customize the widget settings.
		 *
		 * @since  1.0.0
		 * @access protected
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'fields_section',
				array(
					'label' => _x( 'General', '[ELEMENTOR] Section title', 'yith-woocommerce-ajax-navigation' ),
					'tab'   => Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'description',
				array(
					'type'       => Controls_Manager::RAW_HTML,
					'show_label' => false,
					'raw'        => '<p style="line-height: 1.2; margin: 15px 0;">' .
							_x( 'This widget will display "Filters reset" button, wherever it is placed', '[ELEMENTOR] Widget description', 'yith-woocommerce-ajax-navigation' ) .
							'</p>' .
							'<p style="line-height: 1.2; margin: 15px 0;">' .
							_x( 'It will allow users to reset the filer selection; it will only appear when there is an active filter.', '[ELEMENTOR] Widget description', 'yith-woocommerce-ajax-navigation' ) .
							'</p>' .
							'</p>' .
							'<small style="color: #cdcdcd;">' .
							_x( 'You can use this block to place "Reset filters" button inside your page, when "Reset button position" option won\'t work for your product\'s loop', '[ELEMENTOR] Widget description', 'yith-woocommerce-ajax-navigation' ) .
							'</small>',
				)
			);

			$this->end_controls_section();
		}

		/**
		 * Render YITH_WCAN_Elementor_Reset_Button widget output on the frontend.
		 *
		 * @since  1.0.0
		 * @access protected
		 */
		protected function render() {
			echo do_shortcode( '[yith_wcan_reset_button]' );
		}

		/**
		 * Render YITH_WCAN_Elementor_Reset_Button widget output on the Elementor editor.
		 *
		 * @since  1.0.0
		 * @access protected
		 * @deprecated Elementor 2.9.0
		 */
		protected function _content_template() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
			$this->content_template();
		}

		/**
		 * Render YITH_WCAN_Elementor_Reset_Button widget output on the Elementor editor.
		 *
		 * @since  1.0.0
		 * @access protected
		 */
		protected function content_template() {
			$frontend = new YITH_WCAN_Frontend();

			$_GET['min_price']     = 10;
			$_GET['max_price']     = 100;
			$_GET['onsale_filter'] = 1;

			$_REQUEST[ YITH_WCAN_Query()->get_query_param() ] = 1;

			$frontend->reset_button();
		}

	}
}
