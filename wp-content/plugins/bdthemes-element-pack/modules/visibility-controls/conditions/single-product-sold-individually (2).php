<?php
	
	namespace ElementPack\Modules\VisibilityControls\Conditions;
	
	use ElementPack\Base\Condition;
	use Elementor\Controls_Manager;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	class Single_Product_Sold_Individually extends Condition {
		
		/**
		 * Get the name of condition
		 * @return string as per our condition control name
		 */
		public function get_name() {
			return 'single_product_sold_individually';
		}
		
		/**
		 * Get the title of condition
		 * @return string as per condition control title
		 */
		public function get_title() {
			return esc_html__( 'Single Product Sold individually', 'bdthemes-element-pack' );
		}

		/**
		 * Get the group of condition
		 * @return string as per our condition control name
		 */
		public function get_group() {
			return 'woocommerce';
		}
		
		/**
		 * Get the control value
		 * @return array as per condition control value
		 */
		public function get_control_value() {
			return [
				'type'        => Controls_Manager::SELECT,
				'default'     => 'yes',
				'label_block' => true,
				'options'     => [						
					'yes' => esc_html__( 'Yes', 'bdthemes-element-pack' ),
				],
			];
		}
		
		/**
		 * Check the condition
		 * @param string $relation Comparison operator for compare function
		 * @param mixed $val will check the control value as per condition needs
		 */
		public function check( $relation, $val ) {
			$post_id	= get_queried_object_id();
			$post_type	= get_post_type();

			if (( '' === $val ) or ( 'product' !== $post_type ) or ( ! $post_id )) {
				return false;
			}

			$product = wc_get_product( $post_id );

			$individual = $product->is_sold_individually();
			
			return $this->compare( $individual, true, $relation );
		}
	}
