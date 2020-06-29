<?php

class WC_Conditional_Content_Rule_Schedule_Date extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'schedule_date' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
			'>=' => __( "starts", 'wc_conditional_content' ),
			'<=' => __( "ends", 'wc_conditional_content' )
		);

		return $operators;
	}

	public function get_condition_input_type() {
		return 'Date';
	}

	public function is_match( $rule_data, $arguments = null ) {
		$result  = false;
		if ( isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {

			$date = strtotime( $rule_data['condition'] );

			switch ( $rule_data['operator'] ) {
				case '>=' :
					$result = strtotime( date( 'Y-m-d' ) ) >= $date;
					break;
				case '<=' :
					$result = strtotime( date( 'Y-m-d' ) ) <= $date;
					break;
				default:
					$result = false;
					break;
			}
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}


class WC_Conditional_Content_Rule_Schedule_Day extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'schedule_day' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
			'==' => __( "is", 'wc_conditional_content' ),
			'!=' => __( "is not", 'wc_conditional_content' )
		);

		return $operators;
	}

	public function get_possibile_rule_values() {

		$options = array(
			'0' => __( 'Sunday', 'wc_conditional_content' ),
			'1' => __( 'Monday', 'wc_conditional_content' ),
			'2' => __( 'Tuesday', 'wc_conditional_content' ),
			'3' => __( 'Wednesday', 'wc_conditional_content' ),
			'4' => __( 'Thursday', 'wc_conditional_content' ),
			'5' => __( 'Friday', 'wc_conditional_content' ),
			'6' => __( 'Saturday', 'wc_conditional_content' )
		);

		return $options;
	}


	public function get_condition_input_type() {
		return 'Select';
	}

	public function is_match( $rule_data, $arguments = null ) {
		$result = false;
		if ( isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {

			$date = strtotime( $rule_data['condition'] );

			switch ( $rule_data['operator'] ) {
				case '==' :
					$result = date( 'j' ) == $date;
					break;
				case '!=' :
					$result = date( 'j' ) != $date;
					break;
				default:
					$result = false;
					break;
			}
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}


class WC_Conditional_Content_Rule_Schedule_Time extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'schedule_time' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
			'==' => __( "is equal to", 'wc_conditional_content' ),
			'!=' => __( "is not equal to", 'wc_conditional_content' ),
			'>'  => __( "is greater than", 'wc_conditional_content' ),
			'<'  => __( "is less than", 'wc_conditional_content' ),
			'>=' => __( "is greater or equal to", 'wc_conditional_content' ),
			'<=' => __( "is less or equal to", 'wc_conditional_content' )
		);

		return $operators;
	}

	public function get_condition_input_type() {
		return 'Text';
	}

	public function is_match( $rule_data, $arguments = null ) {
		$result = false;
		if ( isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
			$time = strtotime( $rule_data['condition'] );
			$now  = strtotime( date( "h:i:s" ) );
			switch ( $rule_data['operator'] ) {
				case '==' :
					$result = $time == $now;
					break;
				case '!=' :
					$result = $time != $now;
					break;
				case '>' :
					$result = $time > $now;
					break;
				case '<' :
					$result = $now < $time;
					break;
				case '>=' :
					$result = $time >= $now;
					break;
				case '<=' :
					$result = $now <= $time;
					break;
				default:
					$result = false;
					break;
			}
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}
