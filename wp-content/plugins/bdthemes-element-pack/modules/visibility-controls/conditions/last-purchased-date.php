<?php
	
	namespace ElementPack\Modules\VisibilityControls\Conditions;
	
	use DateTime;
	use ElementPack\Base\Condition;
	use Elementor\Controls_Manager;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	class Last_Purchased_Date extends Condition {
		
		/**
		 * Get the name of condition
		 * @return string as per our condition control name
		 * @since  6.7.1
		 */
		public function get_name() {
			return 'last_purchased_date';
		}
		
		/**
		 * Get the title of condition
		 * @return string as per condition control title
		 * @since  6.7.1
		 */
		public function get_title() {
			return esc_html__( 'Last Purchased Date', 'bdthemes-element-pack' );
		}

		/**
		 * Get the group of condition
		 * @return string as per our condition control name
		 * @since  6.11.3
		 */
		public function get_group() {
			return 'woocommerce';
		}
		
		/**
		 * Get the control value
		 * @return array as per condition control value
		 * @since  6.7.1
		 */
		public function get_control_value() {
			
			return [
				'label'          => __( 'On or Before', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::DATE_TIME,
				'default'        => gmdate( 'Y/m/d' ),
				'label_block'    => true,
				'picker_options' => array(
					'format'     => 'Y-m-d',
					'enableTime' => false,
				),
				'label_block'    => true,
			];
		}
		
		/**
		 * Check the condition
		 *
		 * @param string $relation Comparison operator for compare function
		 * @param mixed $val will check the control value as per condition needs
		 * @since 6.7.1
		 */
		public function check( $relation, $val ) {
			
			$args = array(
				'customer_id' => get_current_user_id(),
				'status'      => array( 'wc-completed' ),
				'order'       => 'DESC',
				'limit'       => 1,
				'orderby'     => 'date_completed',
			);
	
			$order_date = wc_get_orders( $args );
	
			$purchased_date = $order_date && $order_date[0] ? date( 'Y-m-d', strtotime( $order_date[0]->get_Date_completed() ) ) : false;
			
			// Check that purchase date is on or before
			$show = $val >= $purchased_date ? true : false;
			
			return $this->compare( $show, true, $relation );
		}
	}
