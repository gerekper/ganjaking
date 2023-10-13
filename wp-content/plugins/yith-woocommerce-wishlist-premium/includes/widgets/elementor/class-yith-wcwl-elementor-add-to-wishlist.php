<?php
/**
 * Add to Wishlist widget for Elementor
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes\Elementor
 * @version 3.0.7
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Elementor_Add_To_Wishlist' ) ) {
	/**
	 * Add to Wishlist Elementor block
	 *
	 * @since 3.0.0
	 */
	class YITH_WCWL_Elementor_Add_To_Wishlist extends \Elementor\Widget_Base {

		/**
		 * Get widget name.
		 *
		 * Retrieve YITH_WCWL_Elementor_Add_to_Wishlist widget name.
		 *
		 * @return string Widget name.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_name() {
			return 'yith_wcwl_add_to_wishlist';
		}

		/**
		 * Get widget title.
		 *
		 * Retrieve YITH_WCWL_Elementor_Add_to_Wishlist widget title.
		 *
		 * @return string Widget title.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_title() {
			return _x( 'YITH Wishlist Add button', 'Elementor widget name', 'yith-woocommerce-wishlist' );
		}

		/**
		 * Get widget icon.
		 *
		 * Retrieve YITH_WCWL_Elementor_Add_to_Wishlist widget icon.
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
		 * Retrieve the list of categories the YITH_WCWL_Elementor_Add_to_Wishlist widget belongs to.
		 *
		 * @return array Widget categories.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_categories() {
			return array( 'general', 'yith' );
		}

		/**
		 * Register YITH_WCWL_Elementor_Add_to_Wishlist widget controls.
		 *
		 * Adds different input fields to allow the user to change and customize the widget settings.
		 *
		 * @since  1.0.0
		 * @access protected
		 */
		protected function register_controls() {

			$this->start_controls_section(
				'product_section',
				array(
					'label' => _x( 'Product', 'Elementor section title', 'yith-woocommerce-wishlist' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'product_id',
				array(
					'label'       => _x( 'Product ID', 'Elementor control label', 'yith-woocommerce-wishlist' ),
					'type'        => \Elementor\Controls_Manager::NUMBER,
					'input_type'  => 'text',
					'placeholder' => '123',
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'labels_section',
				array(
					'label' => _x( 'Labels', 'Elementor section title', 'yith-woocommerce-wishlist' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'label',
				array(
					'label'       => _x( 'Button label', 'Elementor control label', 'yith-woocommerce-wishlist' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'input_type'  => 'text',
					'placeholder' => __( 'Add to wishlist', 'yith-woocommerce-wishlist' ),
				)
			);

			$this->add_control(
				'browse_wishlist_text',
				array(
					'label'       => _x( '"Browse wishlist" label', 'Elementor control label', 'yith-woocommerce-wishlist' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'input_type'  => 'text',
					'placeholder' => __( 'Browse wishlist', 'yith-woocommerce-wishlist' ),
				)
			);

			$this->add_control(
				'already_in_wishslist_text',
				array(
					'label'       => _x( '"Product already in wishlist" label', 'Elementor control label', 'yith-woocommerce-wishlist' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'input_type'  => 'text',
					'placeholder' => __( 'Product already in wishlist', 'yith-woocommerce-wishlist' ),
				)
			);

			$this->add_control(
				'product_added_text',
				array(
					'label'       => _x( '"Product added to wishlist" label', 'Elementor control label', 'yith-woocommerce-wishlist' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'input_type'  => 'text',
					'placeholder' => __( 'Product added to wishlist', 'yith-woocommerce-wishlist' ),
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'advanced_section',
				array(
					'label' => _x( 'Advanced', 'Elementor section title', 'yith-woocommerce-wishlist' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'wishlist_url',
				array(
					'label'       => _x( 'URL of the wishlist page', 'Elementor control label', 'yith-woocommerce-wishlist' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'input_type'  => 'url',
					'placeholder' => '',
				)
			);

			$this->add_control(
				'icon',
				array(
					'label'       => _x( 'Icon for the button', 'Elementor control label', 'yith-woocommerce-wishlist' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'input_type'  => 'text',
					'placeholder' => '',
				)
			);

			$this->add_control(
				'link_classes',
				array(
					'label'       => _x( 'Additional CSS classes for the button', 'Elementor control label', 'yith-woocommerce-wishlist' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'input_type'  => 'text',
					'placeholder' => '',
				)
			);

			$this->end_controls_section();
		}

		/**
		 * Render YITH_WCWL_Elementor_Add_to_Wishlist widget output on the frontend.
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

			echo do_shortcode( "[yith_wcwl_add_to_wishlist {$attribute_string}]" );
		}
	}
}
