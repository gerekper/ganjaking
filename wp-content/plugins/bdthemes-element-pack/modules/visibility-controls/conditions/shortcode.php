<?php
	
	namespace ElementPack\Modules\VisibilityControls\Conditions;
	
	use ElementPack\Base\Condition;
	use Elementor\Controls_Manager;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	class Shortcode extends Condition {
		
		/**
		 * Get the name of condition
		 * @return string as per our condition control name
		 */
		public function get_name() {
			return 'shortcode';
		}
		
		/**
		 * Get the title of condition
		 * @return string as per condition control title
		 */
		public function get_title() {
			return esc_html__( 'Shortcode', 'bdthemes-element-pack' );
		}
		
		/**
		 * Get the group of condition
		 * @return string as per our condition control name
		 */
		public function get_group() {
			return 'misc';
		}
		
		/**
		 * Get the control value
		 * @return array as per condition control value
		 */
		public function get_control_value() {
			return [
				'label'       => __( 'Value', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
                'placeholder' => 'hello',               
				'description'     => __( 'Your shortcode return value. Example my_shortcode return value is: hello', 'bdthemes-element-pack' ),
			];
		}

		public function get_name_control() {
			return [
				'label'       => __( 'Type Your Shortcode Here', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
                'placeholder' => '[my_shortcode]',
			];
		}
		
		/**
		 * Check the condition
		 * @param string $relation Comparison operator for compare function
		 * @param mixed $val will check the control value as per condition needs
		 */
		public function check( $relation, $val, $custom_page_id = false, $extra = false ) {

			if( ! $extra ){
				return;
			}

            $extra = strval(do_shortcode(shortcode_unautop($extra)));

            $show = $extra === $val ? true : false;

            return $this->compare( $show, true, $relation );
        }
	}
