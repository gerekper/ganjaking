<?php
	
	namespace ElementPack\Modules\VisibilityControls\Conditions;
	
	use DateTime;
	use ElementPack\Base\Condition;
	use Elementor\Controls_Manager;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	class Orders_Placed extends Condition {
		
		/**
		 * Get the name of condition
		 * @return string as per our condition control name
		 * @since  6.7.1
		 */
		public function get_name() {
			return 'orders_placed';
		}
		
		/**
		 * Get the title of condition
		 * @return string as per condition control title
		 * @since  6.7.1
		 */
		public function get_title() {
			return esc_html__( 'Order(s) Placed', 'bdthemes-element-pack' );
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
				'type'           => Controls_Manager::NUMBER,
				'default'        => 1,
				'min'        => 0,
				'label_block'    => false,
				'description'    => __( 'Enter the number of orders placed by the user.', 'bdthemes-element-pack' )
			];
		}
		
		/**
		 * Check the condition
		 *
		 * @param string $relation Comparison operator for compare function
		 * @param mixed $val will check the control value as per condition needs
		 * @since 6.7.1
		 */
		public function check( $relation, $val, $custom_page_id=false, $extra=false, $addition_operator=false ) {

            if(!$val) return;

			$show = false;

            $args = array(
                'customer_id' => get_current_user_id(),
                'status'      => array( 'wc-completed' ),
            );
    
            $count = count( wc_get_orders( $args ) );
    
            if($addition_operator === 'equal' && (int) $count === $val){
                $show = true;
            }else if($addition_operator === 'greater_or_equal' && (int) $count >= $val){
                $show = true;
            }
			
			return $this->compare( $show, true, $relation );
		}
	}
