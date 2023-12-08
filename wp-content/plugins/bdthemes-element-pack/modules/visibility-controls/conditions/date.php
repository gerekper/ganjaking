<?php
	
	namespace ElementPack\Modules\VisibilityControls\Conditions;
	
	use DateTime;
	use ElementPack\Base\Condition;
	use Elementor\Controls_Manager;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	class Date extends Condition {
		
		/**
		 * Get the name of condition
		 * @return string as per our condition control name
		 * @since  5.3.0
		 */
		public function get_name() {
			return 'date';
		}
		
		/**
		 * Get the title of condition
		 * @return string as per condition control title
		 * @since  5.3.0
		 */
		public function get_title() {
			return esc_html__( 'Date Range', 'bdthemes-element-pack' );
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
			$default_date_start = date( 'Y-m-d', strtotime( '-3 day' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
			$default_date_end   = date( 'Y-m-d', strtotime( '+3 day' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
			$default_interval   = $default_date_start . ' to ' . $default_date_end;
			
			return [
				'label'          => esc_html__( 'In interval', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::DATE_TIME,
				'dynamic'     => ['active' => true],
				'picker_options' => [
					'enableTime' => false,
					'mode'       => 'range',
				],
				'label_block'    => true,
				'default'        => $default_interval,
			];
		}
		
		/**
		 * Check the condition
		 *
		 * @param string $relation Comparison operator for compare function
		 * @param mixed $val will check the control value as per condition needs
		 * @since 5.3.0
		 */
		public function check( $relation, $val ) {
			
			// Split control value into two dates
			$intervals = explode( 'to', preg_replace( '/\s+/', '', $val ) );
			
			// Make sure the explode return an array with exactly 2 indexes
			if ( ! is_array( $intervals ) || 2 !== count( $intervals ) ) {
				return false;
			}
			
			// Set start and end dates
			$start = strtotime( $intervals[0] );
			$end   = strtotime( $intervals[1] );
			
			// Check vars
			if ( ! $start || ! $end ) { // Make sure it's a date
				return false;
			}
			
			// get current time for test
			$today =  current_time( 'timestamp' );
			
			// Check that user date is between start & end
			$show = ( ( $today >= $start ) && ( $today <= $end ) );
			
			return $this->compare( $show, true, $relation );
		}
	}
