<?php

class WPML_GF_Quiz implements \IWPML_Frontend_Action, \IWPML_Backend_Action, \IWPML_DIC_Action {

	/**
	 * Quiz main messages
	 *
	 * @var array
	 */
	const QUIZ_MESSAGES = [
		'failConfirmationMessage'   => 'Fail Confirmation',
		'letterConfirmationMessage' => 'Letter Confirmation',
		'passConfirmationMessage'   => 'Pass Confirmation',
	];

	/**
	 * @var GFML_TM_API
	 */
	private $gfml_tm_api;

	public function __construct( GFML_TM_API $gfml_tm_api ) {
		$this->gfml_tm_api = $gfml_tm_api;
	}

	public function add_hooks() {
		add_action( 'wpml_gf_register_strings_field_quiz', [ $this, 'register_fields_strings' ], 10, 3 );
		add_action( 'gform_post_update_form_meta', [ $this, 'gform_post_update_form_meta' ] );

		if ( ! is_admin() ) {
			add_filter( 'gform_pre_render', [ $this, 'gform_pre_render' ], 10 );
			add_filter( 'gform_pre_submission_filter', [ $this, 'gform_pre_submission_filter' ], 10 );
			add_filter( 'gform_form_post_get_meta', [ $this, 'quiz_explanation_pre_render' ], 10 );
		}
	}

	/**
	 * Register un-handled quiz main strings
	 *
	 * @param string $form_meta Form.
	 */
	public function gform_post_update_form_meta( $form_meta ) {
		$form = json_decode( $form_meta, true );
		$this->register_main_strings( $form );
	}

	/**
	 * Register un-handled quiz main strings
	 *
	 * @param array $form Form.
	 */
	private function register_main_strings( $form ) {
		if ( $this->is_quiz( $form ) ) {
			$this->register_messages_strings( static::QUIZ_MESSAGES, $form );
			$this->register_grades_strings( $form );
		}
	}


	/**
	 * Register messages strings of the form
	 *
	 * @param array $quiz_messages Messages.
	 * @param array $form          Form.
	 */
	private function register_messages_strings( $quiz_messages, $form ) {
		if ( ! isset( $form['gravityformsquiz'] ) ) {
			return;
		}

		$form_package = $this->gfml_tm_api->get_form_package( $form );

		foreach ( $quiz_messages as $message => $title ) {
			$string_name = $this->get_quiz_message_string_name( $message );
			$this->gfml_tm_api->register_gf_string( $form['gravityformsquiz'][ $message ], $string_name, $form_package, $title, 'AREA' );
		}
	}

	/**
	 * Register grades strings of the form
	 *
	 * @param array $form Form.
	 */
	private function register_grades_strings( $form ) {
		if ( isset( $form['gravityformsquiz']['grades'] ) ) {
			$form_package = $this->gfml_tm_api->get_form_package( $form );

			foreach ( $form['gravityformsquiz']['grades'] as $grade ) {
				$string_name = $this->get_quiz_grade_string_name( $grade['value'] );
				$title       = 'Form Grade ' . $grade['value'];
				$this->gfml_tm_api->register_gf_string( $grade['text'], $string_name, $form_package, $title, 'LINE' );
			}
		}
	}

	/**
	 * Register un-handled quiz field strings
	 *
	 * @param array  $form         Form.
	 * @param array  $form_package String form package.
	 * @param object $form_field   Form field.
	 */
	public function register_fields_strings( $form, $form_package, $form_field ) {
		if ( $this->is_quiz( $form ) && in_array( $form_field->inputType, [ 'select', 'radio', 'checkbox' ], true ) ) {
			$this->gfml_tm_api->register_strings_field_option( $this->gfml_tm_api->get_form_package( $form ), $form_field );
			$this->register_quiz_explanation_string( $this->gfml_tm_api->get_form_package( $form ), $form_field );
		}
	}

	/**
	 * Register quiz explanation strings
	 *
	 * @param object $form_package Form Package.
	 * @param object $form_field   Form field.
	 */
	private function register_quiz_explanation_string( $form_package, $form_field ) {
		if ( ! empty( $form_field['gquizAnswerExplanation'] ) ) {
			$string_name  = $this->get_quiz_explanation_string_name( $form_field );
			$string_title = 'Quiz explanation';
			$this->gfml_tm_api->register_gf_string( $form_field['gquizAnswerExplanation'], $string_name, $form_package, $string_title, 'AREA' );
		}
	}


	/**
	 * Get quiz explanation string name
	 *
	 * @param object $form_field Form field.
	 *
	 * @return array Form
	 */
	private function get_quiz_explanation_string_name( $form_field ) {
		$snh = new GFML_String_Name_Helper();
		return $snh->sanitize_string( 'quiz-' . $form_field->id . '-explanation' );
	}


	/**
	 * Get quiz message string name
	 *
	 * @param string $message message slug.
	 *
	 * @return array Form
	 */
	private function get_quiz_message_string_name( $message ) {
		$snh = new GFML_String_Name_Helper();
		return $snh->sanitize_string( 'quiz_' . $message );
	}


	/**
	 * Get quiz grade string name
	 *
	 * @param string $value grade value.
	 *
	 * @return array Form
	 */
	protected function get_quiz_grade_string_name( $value ) {
		$snh = new GFML_String_Name_Helper();
		return $snh->sanitize_string( 'quiz-grade-value_' . $value );
	}


	/**
	 * Replace strings with translations
	 *
	 * @param array $form Form.
	 *
	 * @return array Form
	 */
	public function gform_pre_render( $form ) {
		if ( $this->is_quiz( $form ) ) {
			$form_package = $this->gfml_tm_api->get_form_package( $form );

			$form = $this->translate_messages_strings( $form, $form_package, static::QUIZ_MESSAGES );
			$form = $this->translate_grades_strings( $form, $form_package );
			// Choices options are already translated in Gravity_Forms_Multilingual::get_form_strings().
		}
		return $form;
	}


	/**
	 * Replace strings with translations on form submission
	 *
	 * @param array $form Form.
	 *
	 * @return array $form Form
	 */
	public function gform_pre_submission_filter( $form ) {
		$form = $this->gform_pre_render( $form );
		return $form;
	}


	/**
	 * Replace quiz explanation strings with translations
	 * Note: Those strings are passed as JS vars in GFQuiz::enqueue_front_end_scripts()
	 * which is called earlier, so this is also hooked earlier.
	 *
	 * @param array $form Form.
	 *
	 * @return array Form
	 */
	public function quiz_explanation_pre_render( $form ) {
		if ( $this->is_quiz( $form ) ) {
			$form_package = $this->gfml_tm_api->get_form_package( $form );

			$form = $this->translate_quiz_explanation_strings( $form, $form_package );
		}
		return $form;
	}


	/**
	 * Translate main strings
	 *
	 * @param array  $form          Form.
	 * @param object $form_package  Form Package.
	 * @param array  $quiz_messages List of quiz messages.
	 *
	 * @return array Form
	 */
	private function translate_messages_strings( $form, $form_package, $quiz_messages ) {
		foreach ( $quiz_messages as $message => $title ) {
			if ( ! empty( $form['gravityformsquiz'][ $message ] ) ) {
				$string_value                         = $form['gravityformsquiz'][ $message ];
				$string_name                          = $this->get_quiz_message_string_name( $message );
				$form['gravityformsquiz'][ $message ] = apply_filters( 'wpml_translate_string', $string_value, $string_name, $form_package );
			}
		}
		return $form;
	}


	/**
	 * Translate grade strings
	 *
	 * @param array  $form         Form.
	 * @param object $form_package Form Package.
	 *
	 * @return array Form
	 */
	private function translate_grades_strings( $form, $form_package ) {
		if ( isset( $form['gravityformsquiz']['grades'] ) ) {
			foreach ( $form['gravityformsquiz']['grades'] as $key => $grade ) {
				if ( ! empty( $form['gravityformsquiz']['grades'][ $key ]['text'] ) ) {
					$string_value                                       = $form['gravityformsquiz']['grades'][ $key ]['text'];
					$string_name                                        = $this->get_quiz_grade_string_name( $grade['value'] );
					$form['gravityformsquiz']['grades'][ $key ]['text'] = apply_filters( 'wpml_translate_string', $string_value, $string_name, $form_package );
				}
			}
		}
		return $form;
	}


	/**
	 * Translate explanation strings
	 *
	 * @param array  $form         Form.
	 * @param object $form_package Form Package.
	 *
	 * @return array Form
	 */
	private function translate_quiz_explanation_strings( $form, $form_package ) {
		foreach ( $form['fields'] as $key => $form_field ) {
			if ( ! empty( $form_field['gquizAnswerExplanation'] ) ) {
				$string_value                                     = $form['fields'][ $key ]['gquizAnswerExplanation'];
				$string_name                                      = $this->get_quiz_explanation_string_name( $form_field );
				$form['fields'][ $key ]['gquizAnswerExplanation'] = apply_filters( 'wpml_translate_string', $string_value, $string_name, $form_package );
			}
		}
		return $form;
	}

	/**
	 * Check if this form has a quiz
	 *
	 * @param array $form Form.
	 *
	 * @return bool
	 */
	private function is_quiz( $form ) {
		$is_quiz = false;
		if ( isset( $form['gravityformsquiz'] ) ) {
			$is_quiz = true;
		} elseif ( isset( $form['fields'] ) ) {
			foreach ( $form['fields'] as $field ) {
				if ( isset( $field['gquizFieldType'] ) ) {
					$is_quiz = true;
				}
			}
		}
		return $is_quiz;
	}
}
