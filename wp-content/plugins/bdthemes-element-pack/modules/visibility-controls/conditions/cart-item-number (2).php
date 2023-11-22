<?php
	
	namespace ElementPack\Modules\VisibilityControls\Conditions;
	
	use ElementPack\Base\Condition;
	use Elementor\Controls_Manager;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	class Cart_Item_Number extends Condition {
		
		/**
		 * Get the name of condition
		 * @return string as per our condition control name
		 * @since  6.6.0
		 */
		public function get_name() {
			return 'cart_item_number';
		}
		
		/**
		 * Get the title of condition
		 * @return string as per condition control title
		 * @since  6.6.0
		 */
		public function get_title() {
			return esc_html__( 'Cart Items Number', 'bdthemes-element-pack' );
		}

		/**
		 * Get the group of condition
		 * @return string as per our condition control name
		 * @since  6.11.3
		 */
		public function get_group() {
			return 'woocommerce';
		}
		
		/* *
		 * Get the control value
		 * @return array as per condition control value
		 * @since  6.6.0
		 */
		public function get_control_value() {
			return [
				'label'			=> __( 'Equal or Heigher Than', 'bdthemes-element-pack' ),
				'type'			=> Controls_Manager::NUMBER,
				'min'			=> 0,
				'default'		=> 1,
				'description'	=> __( 'Set zero(0) to check empty order.', 'bdthemes-element-pack' ),
			];
		}

		
		/**
		 * Check the condition
		 * @param string $relation Comparison operator for compare function
		 * @param mixed $val will check the control value as per condition needs
		 * @since 6.6.0
		 */
		public function check( $relation, $val ) {
			
			$cart = WC()->cart;

			if ( '' === $val ) {
				return false;
			}

			$product_count = $cart->get_cart_contents_count();

			if ( 0 === (int) $val ) {
				$show = (int) $val === $product_count ? true : false;
			} else {
				$show = (int) $val <= $product_count ? true : false;
			}

			return $this->compare( $show, true, $relation );
		}
	}
