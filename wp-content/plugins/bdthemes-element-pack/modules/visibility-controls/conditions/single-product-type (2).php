<?php
	
	namespace ElementPack\Modules\VisibilityControls\Conditions;
	
	use ElementPack\Base\Condition;
	use Elementor\Controls_Manager;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	class Single_Product_Type extends Condition {
		
		/**
		 * Get the name of condition
		 * @return string as per our condition control name
		 */
		public function get_name() {
			return 'single_product_type';
		}
		
		/**
		 * Get the title of condition
		 * @return string as per condition control title
		 */
		public function get_title() {
			return esc_html__( 'Single Product Type', 'bdthemes-element-pack' );
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
				'default'     => 'simple',
				'label_block' => true,
				'options'     => [						
					'simple' => esc_html__( 'Simple', 'bdthemes-element-pack' ),
					'grouped' => esc_html__( 'Grouped', 'bdthemes-element-pack' ),
					'external' => esc_html__( 'External/Affiliate', 'bdthemes-element-pack' ),
					'variable' => esc_html__( 'Variable', 'bdthemes-element-pack' )
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

			$show = $val === $product->get_type() ? true : false;
			
			return $this->compare( $show, true, $relation );
		}
	}
