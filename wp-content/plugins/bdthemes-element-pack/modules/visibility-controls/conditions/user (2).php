<?php
	
	namespace ElementPack\Modules\VisibilityControls\Conditions;
	
	use ElementPack\Base\Condition;
	use Elementor\Controls_Manager;
	use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	class User extends Condition {
		
		/**
		 * Get the name of condition
		 * @return string as per our condition control name
		 * @since  5.3.0
		 */
		public function get_name() {
			return 'user';
		}
		
		/**
		 * Get the title of condition
		 * @return string as per condition control title
		 * @since  5.3.0
		 */
		public function get_title() {
			return esc_html__( 'User', 'bdthemes-element-pack' );
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
			return [
				'label'       => esc_html__( 'Search & Select', 'bdthemes-element-pack' ),
				'type'        => Dynamic_Select::TYPE,
				'default'     => '',
				'placeholder' => esc_html__( 'Any', 'bdthemes-element-pack' ),
				'description' => esc_html__( 'Works only when visitor is a logged in user. Leave blank for all users.', 'bdthemes-element-pack' ),
				'label_block' => true,
				'multiple'    => true,
				//'options'     => element_pack_get_users(),
				'query_args'  => [
					'query' => 'authors',
				],
			];
		}
		
		/**
		 * Check the condition
		 *
		 * @param string $relation Comparison operator for compare function
		 * @param mixed $val will check the control value as per condition needs
		 *
		 * @return bool
		 * @since 5.3.0
		 */
		public function check( $relation, $val ) {
			$show = false;
			
			if ( is_array( $val ) && ! empty( $val ) ) {
				foreach ( $val as $_key => $_value ) {
					if ( $_value == get_current_user_id() ) {
						$show = true;
						break;
					}
				}
			} else {
				$show = $val == get_current_user_id();
			}
			
			return $this->compare( $show, true, $relation );
		}
	}
