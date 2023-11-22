<?php
	
	namespace ElementPack\Modules\VisibilityControls\Conditions;
	
	use ElementPack\Base\Condition;
	use Elementor\Controls_Manager;
	use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	class Categories_In_Cart extends Condition {
		
		/**
		 * Get the name of condition
		 * @return string as per our condition control name
		 * @since  6.6.0
		 */
		public function get_name() {
			return 'categories_in_cart';
		}
		
		/**
		 * Get the title of condition
		 * @return string as per condition control title
		 * @since  6.6.0
		 */
		public function get_title() {
			return esc_html__( 'Categories in Cart', 'bdthemes-element-pack' );
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
				'label'       => esc_html__( 'Select', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SELECT2,
				'default'     => '',
				'options' => element_pack_get_terms('product_cat'),
				'label_block' => true,
				'multiple'    => true,
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

			$product_cats = array();

			if ( $cart->is_empty() ) {
				return false;
			}

			foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {

				$product = $cart_item['data'];

				if ( $product->is_type( 'variation' ) ) {
					$product = wc_get_product( $product->get_parent_id() );
				}

				$product_cats = array_merge( $product_cats, $product->get_category_ids() );
			}

			$show = ! empty( array_intersect( (array) $val, $product_cats ) ) ? true : false;
			
			return $this->compare( $show, true, $relation );
		}
	}
