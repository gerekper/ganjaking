<?php
	
	namespace ElementPack\Modules\VisibilityControls\Conditions;
	
	use ElementPack\Base\Condition;
	use Elementor\Controls_Manager;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	class Role extends Condition {
		
		/**
		 * Get the name of condition
		 * @return string as per our condition control name
		 * @since  5.3.0
		 */
		public function get_name() {
			return 'role';
		}
		
		/**
		 * Get the title of condition
		 * @return string as per condition control title
		 * @since  5.3.0
		 */
		public function get_title() {
			return esc_html__( 'User Role', 'bdthemes-element-pack' );
		}

		/**
		 * Get the group of condition
		 * @return string as per our condition control name
		 * @since  6.11.3
		 */
		public function get_group() {
			return 'user';
		}
		
		/**
		 * Get the control value
		 * @return array as per condition control value
		 * @since  5.3.0
		 */
		public function get_control_value() {
			global $wp_roles;
			
			return [
				'type'        => Controls_Manager::SELECT,
				'description' => esc_html__( 'Warning: This condition applies only to logged in visitors.', 'bdthemes-element-pack' ),
				'default'     => 'subscriber',
				'label_block' => true,
				'options'     => $wp_roles->get_names(),
			];
		}
		
		/**
		 * Check the condition
		 *
		 * @param string $relation Comparison operator for compare function
		 * @param mixed $val will check the control value as per condition needs
		 *
		 * @since 5.3.0
		 */
		public function check( $relation, $val ) {
			$user_role  = wp_get_current_user()->roles;
			return $this->compare( is_user_logged_in() && in_array( $val, $user_role ), true, $relation );
		}
	}
