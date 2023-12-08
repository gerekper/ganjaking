<?php
	
	namespace ElementPack\Modules\VisibilityControls\Conditions;
	
	use ElementPack\Base\Condition;
	use Elementor\Controls_Manager;
	use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	class Single_Product_Category extends Condition {
		
		/**
		 * Get the name of condition
		 * @return string as per our condition control name
		 * @since  6.7.1
		 */
		public function get_name() {
			return 'single_product_category';
		}
		
		/**
		 * Get the title of condition
		 * @return string as per condition control title
		 * @since  6.7.1
		 */
		public function get_title() {
			return esc_html__( 'Single Product Category', 'bdthemes-element-pack' );
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
		 * @since  6.7.1
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
		 * @since 6.7.1
		 */
		public function check( $relation, $val ) {
			
			$post_id	= get_queried_object_id();
			$post_type	= get_post_type();

			if (( '' === $val ) or ( 'product' !== $post_type ) or ( ! $post_id )) {
				return false;
			}

			$product = wc_get_product( $post_id );

			$product_cats = $product->get_category_ids();

			$show = ! empty( array_intersect( (array) $val, $product_cats ) ) ? true : false;
			
			return $this->compare( $show, true, $relation );
		}
	}
