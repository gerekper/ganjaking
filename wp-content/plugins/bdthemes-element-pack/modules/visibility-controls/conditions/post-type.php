<?php
	
	namespace ElementPack\Modules\VisibilityControls\Conditions;
	
	use ElementPack\Base\Condition;
	use Elementor\Controls_Manager;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	class Post_Type extends Condition {
		
		/**
		 * Get the name of condition
		 * @return string as per our condition control name
		 * @since  5.3.0
		 */
		public function get_name() {
			return 'post_type';
		}
		
		/**
		 * Get the title of condition
		 * @return string as per condition control title
		 * @since  5.3.0
		 */
		public function get_title() {
			return esc_html__( 'Post Type', 'bdthemes-element-pack' );
		}

		/**
		 * Get the group of condition
		 * @return string as per our condition control name
		 * @since  6.11.3
		 */
		public function get_group() {
			return 'post';
		}
		
		/**
		 * Get the control value
		 * @return array as per condition control value
		 * @since  5.3.0
		 */
		public function get_control_value() {
			return [
				'type'        => Controls_Manager::SELECT2,
				'default'     => '',
				'placeholder' => esc_html__( 'Any', 'bdthemes-element-pack' ),
				'description' => esc_html__( 'Leave blank or select all for any post type.', 'bdthemes-element-pack' ),
				'label_block' => true,
				'multiple'    => true,
				'options'     => element_pack_get_post_types(),
			];
		}
		
		/**
		 * Check the condition
		 * @param string $relation Comparison operator for compare function
		 * @param mixed $val will check the control value as per condition needs
		 * @since 5.3.0
		 */
		public function check( $relation, $val ) {
			$show = false;
			
			if ( is_array( $val ) && ! empty( $val ) ) {
				foreach ( $val as $_key => $_value ) {
					if ( is_singular( $_value ) ) {
						$show = true;
						break;
					}
				}
			} else {
				$show = is_singular( $val );
			}
			
			return $this->compare( $show, true, $relation );
		}
	}
