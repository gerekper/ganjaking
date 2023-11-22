<?php
	
	namespace ElementPack\Modules\VisibilityControls\Conditions;
	
	use ElementPack\Base\Condition;
	use Elementor\Controls_Manager;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	class Purchased_Item_Number extends Condition {
		
		/**
		 * Get the name of condition
		 * @return string as per our condition control name
		 * @since  6.7.1
		 */
		public function get_name() {
			return 'purchased_item_number';
		}
		
		/**
		 * Get the title of condition
		 * @return string as per condition control title
		 * @since  6.7.1
		 */
		public function get_title() {
			return esc_html__( 'Purchased Items Number', 'bdthemes-element-pack' );
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
				'label'			=> __( 'Equal or Heigher Than', 'bdthemes-element-pack' ),
				'type'			=> Controls_Manager::NUMBER,
				'min'			=> 0,
				'default'		=> 1,
				'description'	=> __( 'Set zero(0) to check empty purchased.', 'bdthemes-element-pack' ),
			];
		}
		
		/**
		 * Check the condition
		 * @param string $relation Comparison operator for compare function
		 * @param mixed $val will check the control value as per condition needs
		 * @since 6.7.1
		 */
		public function check( $relation, $val ) {
			
			if ( '' === $val ) {
				return false;
			}

			$args = array(
				'customer_id' => get_current_user_id(),
				'status'      => array( 'wc-completed' ),
			);

			$purchased_count = count( wc_get_orders( $args ) );

			if ( 0 === (int) $val ) {
				$show = (int) $val === $purchased_count ? true : false;
			} else {
				$show = (int) $val <= $purchased_count ? true : false;
			}

			return $this->compare( $show, true, $relation );
		}
	}
