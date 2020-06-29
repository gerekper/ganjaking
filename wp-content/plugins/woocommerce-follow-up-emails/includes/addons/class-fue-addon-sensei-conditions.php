<?php

/**
 * Class FUE_Addon_Sensei_Conditions
 */
class FUE_Addon_Sensei_Conditions {

	private $fue_sensei;

	/**
	 * Class constructor
	 */
	public function __construct( $fue_sensei ) {
		$this->fue_sensei = $fue_sensei;

		add_action( 'fue_email_form_conditions_meta', array( $this, 'email_form_conditions'), 10, 2  );

		add_filter( 'fue_api_email_response', array( $this, 'normalize_conditions_output' ), 10, 2 );
		add_filter( 'fue_api_edit_email_data', array( $this, 'normalize_conditions_input' ) );
	}

	/**
	 * Inject additional form fields into the conditions section
	 *
	 * @param FUE_Email $email
	 * @param int $idx
	 */
	public function email_form_conditions( $email, $idx ) {
		$conditions = $email->conditions;
		include FUE_TEMPLATES_DIR .'/email-form/sensei/conditions.php';
	}

	/**
	 * Get Sensei-related conditions
	 * @return array
	 */
	public function get_conditions() {
		$conditions = array();

		$conditions['have_not_started_first_lesson'] = __('Have not started the first lesson', 'follow_up_emails');
		$conditions['have_not_completed_a_lesson'] = __('Have not completed a lesson', 'follow_up_emails');
		$conditions['have_not_completed_a_course'] = __('Have not completed a course', 'follow_up_emails');
		$conditions['have_not_taken_quiz'] = __('Have not yet taken a quiz', 'follow_up_emails');
		$conditions['have_failed_quiz'] = __('Have failed a quiz', 'follow_up_emails');
		$conditions['have_passed_quiz'] = __('Have passed a quiz', 'follow_up_emails');

		return apply_filters( 'fue_addon_sensei_conditions', $conditions );
	}

	/**
	 * Test if $item passes the requirements in $condition
	 * @param array $condition
	 * @param FUE_Sending_Queue_Item $item
	 * @return bool|WP_Error
	 */
	public function test_condition( $condition, $item ) {

		switch ( $condition['condition'] ) {

			case 'have_not_started_first_lesson':
				$result = $this->test_have_not_started_first_lesson_condition( $item, $condition );
				break;

			case 'have_not_completed_a_lesson':
				$result = $this->test_have_not_completed_a_lesson( $item, $condition );
				break;

			case 'have_not_completed_a_course':
				$result = $this->test_have_not_completed_a_course( $item, $condition );
				break;

			case 'have_not_taken_quiz':
				$result = $this->test_have_not_taken_quiz( $item, $condition );
				break;

			case 'have_failed_quiz':
				$result = $this->test_have_failed_quiz( $item, $condition );
				break;

			case 'have_passed_quiz':
				$result = $this->test_have_passed_quiz( $item, $condition );
				break;

			default:
				$result = new WP_Error( 'fue_email_conditions', sprintf( __('Unknown condition: %s', 'follow_up_emails'), $condition['condition'] ) );
				break;

		}

		return apply_filters( 'fue_addon_sensei_test_condition', $result, $item, $condition );

	}

	/**
	 * Test will pass if the customer have not started the first lesson in the given course
	 *
	 * @param FUE_Sending_Queue_Item $item
	 * @param array $condition
	 * @return bool|WP_Error
	 */
	public function test_have_not_started_first_lesson_condition( $item, $condition ) {
		global $woothemes_sensei;

		$passed = true;
		$email  = new FUE_Email( $item->email_id );

		if ( $email->type != 'sensei' || $item->user_id == 0 ) {
			return $passed;
		}

		$course_ids = !empty( $condition['courses'] ) ? array_filter( explode( ',', $condition['courses'] ) ) : array();

		if ( empty( $course_ids ) ) {
			$course_ids = $this->fue_sensei->get_user_course_ids( $item->user_id );
		}

		foreach ( $course_ids as $course_id ) {
			$course_lessons = $woothemes_sensei->course->course_lessons( $course_id );

			if ( $course_lessons && is_array( $course_lessons ) ) {
				$lesson = array_shift( $course_lessons );

				if ( WooThemes_Sensei_Utils::user_started_lesson( $lesson->ID, $item->user_id ) ) {
					return new WP_Error(
						'fue_email_conditions',
						sprintf( __('have_not_started_first_lesson condition failed for queue #%d (lesson: %d)', 'follow_up_emails'), $item->id, $lesson->ID )
					);
				}
			}
		}

		return $passed;
	}

	/**
	 * Test will pass if the customer have not completed a single lesson in the given course
	 *
	 * @param FUE_Sending_Queue_Item $item
	 * @param array $condition
	 * @return bool|WP_Error
	 */
	public function test_have_not_completed_a_lesson( $item, $condition ) {
		global $woothemes_sensei;

		$passed = true;
		$email  = new FUE_Email( $item->email_id );

		if ( $email->type != 'sensei' || $item->user_id == 0 ) {
			return $passed;
		}

		$course_ids = !empty( $condition['courses'] ) ? array_filter( explode( ',', $condition['courses'] ) ) : array();

		if ( empty( $course_ids ) ) {
			$course_ids = $this->fue_sensei->get_user_course_ids( $item->user_id );
		}

		foreach ( $course_ids as $course_id ) {
			$course_lessons = $woothemes_sensei->course->course_lessons( $course_id );

			if ( $course_lessons && is_array( $course_lessons ) ) {
				foreach ( $course_lessons as $lesson ) {
					if ( WooThemes_Sensei_Utils::user_completed_lesson( $lesson->ID, $item->user_id ) ) {
						return new WP_Error(
							'fue_email_conditions',
							sprintf( __('have_not_completed_a_lesson condition failed for queue #%d (lesson: %d)', 'follow_up_emails'), $item->id, $lesson->ID )
						);
					}
				}
			}
		}

		return $passed;
	}

	/**
	 * Passes if the customer haven't completed the given course
	 * or any course at all if no particular course is provided
	 *
	 * @param FUE_Sending_Queue_Item $item
	 * @param array $condition
	 * @return bool|WP_Error
	 */
	public function test_have_not_completed_a_course( $item, $condition ) {
		$passed = true;
		$email  = new FUE_Email( $item->email_id );

		if ( $email->type != 'sensei' || $item->user_id == 0 ) {
			return $passed;
		}

		$course_ids = !empty( $condition['courses'] ) ? array_filter( explode( ',', $condition['courses'] ) ) : array();

		if ( empty( $course_ids ) ) {
			$course_ids = $this->fue_sensei->get_user_course_ids( $item->user_id );
		}

		foreach ( $course_ids as $course_id ) {
			if ( WooThemes_Sensei_Utils::user_completed_course( $course_id, $item->user_id ) ) {
				return new WP_Error(
					'fue_email_conditions',
					sprintf( __('have_not_completed_a_course condition failed for queue #%d (course: %d)', 'follow_up_emails'), $item->id, $course_id )
				);
			}
		}

		return $passed;
	}

	/**
	 * Pass if the user have not taken any of the lesson quizzes specified
	 *
	 * @param FUE_Sending_Queue_Item $item
	 * @param array $condition
	 * @return bool|WP_Error
	 */
	public function test_have_not_taken_quiz( $item, $condition ) {
		$passed = true;
		$email  = new FUE_Email( $item->email_id );

		if ( $email->type != 'sensei' || $item->user_id == 0 ) {
			return $passed;
		}

		$lesson_ids = !empty( $condition['lessons'] ) ? array_filter( explode( ',', $condition['lessons'] ) ) : array();

		if ( empty( $lesson_ids ) ) {
			$lesson_ids = $this->fue_sensei->get_user_lesson_ids( $item->user_id );
		}

		foreach ( $lesson_ids as $lesson_id ) {
			$status = WooThemes_Sensei_Utils::sensei_user_quiz_status_message( $lesson_id, $item->user_id );

			if ( $status['status'] != 'not_started') {
				return new WP_Error(
					'fue_email_conditions',
					sprintf( __('have_not_taken_quiz condition failed for queue #%d (lesson: %d)', 'follow_up_emails'), $item->id, $lesson_id )
				);
			}
		}

		return $passed;
	}

	/**
	 * Test will pass if user have failed a quiz
	 * @param FUE_Sending_Queue_Item $item
	 * @param array $condition
	 *
	 * @return bool|WP_Error
	 */
	public function test_have_failed_quiz( $item, $condition ) {
		$passed = true;
		$email  = new FUE_Email( $item->email_id );

		if ( $email->type != 'sensei' || $item->user_id == 0 ) {
			return $passed;
		}

		$lesson_ids = !empty( $condition['lessons'] ) ? array_filter( explode( ',', $condition['lessons'] ) ) : array();

		if ( empty( $lesson_ids ) ) {
			$lesson_ids = $this->fue_sensei->get_user_lesson_ids( $item->user_id );
		}

		foreach ( $lesson_ids as $lesson_id ) {
			$status = WooThemes_Sensei_Utils::sensei_user_quiz_status_message( $lesson_id, $item->user_id );

			if ( $status['status'] != 'failed') {
				return new WP_Error(
					'fue_email_conditions',
					sprintf( __('have_failed_quiz condition failed for queue #%d (lesson: %d)', 'follow_up_emails'), $item->id, $lesson_id )
				);
			}
		}

		return $passed;
	}

	/**
	 * Test will pass if the user have passed a quiz
	 * @param FUE_Sending_Queue_Item $item
	 * @param array $condition
	 *
	 * @return bool|WP_Error
	 */
	public function test_have_passed_quiz( $item, $condition ) {
		$passed = true;
		$email  = new FUE_Email( $item->email_id );

		if ( $email->type != 'sensei' || $item->user_id == 0 ) {
			return $passed;
		}

		$lesson_ids = !empty( $condition['lessons'] ) ? array_filter( explode( ',', $condition['lessons'] ) ) : array();

		if ( empty( $lesson_ids ) ) {
			$lesson_ids = $this->fue_sensei->get_user_lesson_ids( $item->user_id );
		}

		foreach ( $lesson_ids as $lesson_id ) {
			$quiz_id = get_post_meta( $lesson_id, '_lesson_quiz', true );

			if ( !$quiz_id ) {
				continue;
			}

			if ( !WooThemes_Sensei_Utils::user_passed_quiz( $quiz_id, $item->user_id ) ) {
				return new WP_Error(
					'fue_email_conditions',
					sprintf( __('have_passed_quiz condition failed for queue #%d (lesson: %d)', 'follow_up_emails'), $item->id, $lesson_id )
				);
			}

		}

		return $passed;
	}

	/**
	 * Normalize the conditions array
	 *
	 * @param array $email_data
	 * @return array
	 */
	public function normalize_conditions_input( $email_data ) {
		if ( !isset( $email_data['requirements'] ) ) {
			return $email_data;
		}

		$normalized = array();

		foreach ( $email_data['requirements'] as $req ) {

			if ( in_array( $req['condition'], array( 'have_not_started_first_lesson', 'have_not_completed_a_lesson', 'have_not_completed_a_course') ) ) {
				$req['courses'] = '';
				if ( is_array( $req['value'] ) && !empty( $req['value'] ) ) {
					$req['courses'] = implode( ',', $req['value'] );
				}
			} elseif ( in_array( $req['condition'], array('have_not_taken_quiz', 'have_failed_quiz', 'have_passed_quiz') ) ) {
				$req['lessons'] = '';
				if ( is_array( $req['value'] ) && !empty( $req['value'] ) ) {
					$req['lessons'] = implode( ',', $req['value'] );
				}
			}

			$normalized[] = $req;
		}

		$email_data['conditions'] = $normalized;
		unset( $email_data['requirements'] );

		return $email_data;
	}

	/**
	 * Normalize the conditions array
	 *
	 * @param array $email_data
	 * @param FUE_Email $email
	 * @return array
	 */
	public function normalize_conditions_output( $email_data, $email ) {
		if ( empty( $email_data['requirements'] ) ) {
			return $email_data;
		}

		$normalized = array();

		foreach ( $email_data['requirements'] as $req ) {

			if ( in_array( $req['condition'], array( 'have_not_started_first_lesson', 'have_not_completed_a_lesson', 'have_not_completed_a_course') ) ) {
				$req['value'] = explode( ',', $req['courses'] );
				unset( $req['courses'] );
			} elseif ( in_array( $req['condition'], array('have_not_taken_quiz', 'have_failed_quiz', 'have_passed_quiz') ) ) {
				$req['value'] = $req['lessons'];
				unset( $req['lessons    '] );
			}

			$normalized[] = $req;

		}

		$email_data['requirements'] = $normalized;

		return $email_data;
	}

}