<?php
	
	namespace ElementPack\Modules\VisibilityControls\Conditions;
	
	use DateTime;
	use ElementPack\Base\Condition;
	use Elementor\Controls_Manager;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	class Time extends Condition {
		
		/**
		 * Get the name of condition
		 * @return string as per our condition control name
		 * @since  5.3.0
		 */
		public function get_name() {
			return 'time';
		}
		
		/**
		 * Get the title of condition
		 * @return string as per condition control title
		 * @since  5.3.0
		 */
		public function get_title() {
			return esc_html__( 'Till Time of Day (Server Time)', 'bdthemes-element-pack' );
		}

		/**
		 * Get the group of condition
		 * @return string as per our condition control name
		 * @since  6.11.3
		 */
		public function get_group() {
			return 'date_time';
		}
		
		/**
		 * Get the control value
		 * @return array as per condition control value
		 * @since  5.3.0
		 */
		public function get_control_value() {
			return [
				'label'          => esc_html__( 'Before', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'dateFormat' => 'H:i',
					'enableTime' => true,
					'noCalendar' => true,
				],
				'label_block'    => true,
				'default'        => '',
			];
		}
		
		/**
		 * Check the condition
		 *
		 * @param string $relation Comparison operator for compare function
		 * @param mixed $val will check the control value as per condition needs
		 *
		 * @return bool|void
		 * @since 5.3.0
		 */
		public function check( $relation, $val ) {
			// Split control valur into two dates
			$time = date( 'H:i', strtotime( preg_replace( '/\s+/', '', $val ) ) );
			$now  = date( 'H:i', strtotime( 'now' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
			
			// Check vars
			if ( DateTime::createFromFormat( 'H:i', $time ) === false ) {
				return;
			}
			
			// Convert time to timestamp
			$time_ts = strtotime( $time );
			$now_ts  = strtotime( $now );
			
			// is date between start & end ?
			$show = ( $now_ts <= $time_ts );
			
			return $this->compare( $show, true, $relation );
		}
	}
