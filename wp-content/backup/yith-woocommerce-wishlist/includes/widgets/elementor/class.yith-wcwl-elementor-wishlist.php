<?php
/**
 * Wishlist widget for Elementor
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.7
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if( ! class_exists( 'YITH_WCWL_Elementor_Wishlist' ) ) {
	class YITH_WCWL_Elementor_Wishlist extends \Elementor\Widget_Base {

		/**
		 * Get widget name.
		 *
		 * Retrieve YITH_WCWL_Elementor_Wishlist widget name.
		 *
		 * @return string Widget name.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_name() {
			return 'yith_wcwl_wishlist';
		}

		/**
		 * Get widget title.
		 *
		 * Retrieve YITH_WCWL_Elementor_Wishlist widget title.
		 *
		 * @return string Widget title.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_title() {
			return _x( 'YITH Wishlist', 'Elementor widget name', 'yith-woocommerce-wishlist' );
		}

		/**
		 * Get widget icon.
		 *
		 * Retrieve YITH_WCWL_Elementor_Wishlist widget icon.
		 *
		 * @return string Widget icon.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_icon() {
			return 'eicon-table';
		}

		/**
		 * Get widget categories.
		 *
		 * Retrieve the list of categories the YITH_WCWL_Elementor_Wishlist widget belongs to.
		 *
		 * @return array Widget categories.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_categories() {
			return [ 'general', 'yith' ];
		}

		/**
		 * Register YITH_WCWL_Elementor_Wishlist widget controls.
		 *
		 * Adds different input fields to allow the user to change and customize the widget settings.
		 *
		 * @since  1.0.0
		 * @access protected
		 */
		protected function _register_controls() {

			$this->start_controls_section(
				'product_section',
				[
					'label' => _x( 'Wishlist', 'Elementor section title', 'yith-woocommerce-wishlist' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'wishlist_id',
				[
					'label'       => _x( 'Wishlist ID', 'Elementor control label', 'yith-woocommerce-wishlist' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'input_type'  => 'text',
					'placeholder' => 'K6EOWXB888ZD',
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'labels_section',
				[
					'label' => _x( 'Pagination', 'Elementor section title', 'yith-woocommerce-wishlist' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'pagination',
				[
					'label'   => _x( 'Paginate items', 'Elementor control label', 'yith-woocommerce-wishlist' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'yes' => __( 'Paginate', 'yith-woocommerce-wishlist' ),
						'no'  => __( 'Do not paginate', 'yith-woocommerce-wishlist' ),
					],
					'default' => 'no',
				]
			);

			$this->add_control(
				'per_page',
				[
					'label'       => _x( 'Items per page', 'yith-woocommerce-wishlist' ),
					'type'        => \Elementor\Controls_Manager::NUMBER,
					'input_type'  => 'number',
					'placeholder' => 5,
				]
			);

			$this->end_controls_section();

		}

		/**
		 * Render YITH_WCWL_Elementor_Wishlist widget output on the frontend.
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

			echo do_shortcode( "[yith_wcwl_wishlist {$attribute_string}]" );
		}

	}
}