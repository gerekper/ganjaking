<?php

/**
 * Class FUE_Addon_Sensei
 */
class FUE_Addon_Sensei {

	public $conditions;

	/**
	 * class constructor
	 */
	public function __construct() {
		$this->conditions = new FUE_Addon_Sensei_Conditions( $this );
		add_filter( 'fue_email_types', array($this, 'register_email_type') );

		// trigger fields
		add_filter( 'fue_email_form_trigger_fields', array($this, 'register_trigger_fields') );

		// trigger conditions
		add_filter( 'fue_trigger_conditions', array($this, 'register_conditions'), 10, 2 );
		add_filter( 'fue_queue_item_filter_conditions_before_sending', array($this, 'check_item_conditions'), 10, 2 );

		add_filter( 'fue_script_locale', array($this, 'inject_nonce_values') );

		add_action( 'fue_email_form_scripts', array($this, 'email_form_script') );
		add_action( 'fue_email_form_submit_script', array($this, 'email_form_validation_script') );

		// CRM hooks
		add_action( 'sensei_user_course_end', array( $this, 'send_crm_course_completed_email' ), 10, 2 );
		add_action( 'sensei_user_course_start', array( $this, 'schedule_crm_course_incomplete_email' ), 10, 3 );
		add_filter( 'fue_adhoc_email_message', array( $this, 'replace_crm_course_message' ), 10, 2 );

		// course
		add_action( 'sensei_user_course_start', array($this, 'course_sign_up'), 10, 2 );
		add_action( 'sensei_user_course_end', array($this, 'course_completed'), 10, 2 );

		// lesson
		add_action( 'sensei_user_lesson_start', array($this, 'lesson_start'), 10, 2 );
		add_action( 'sensei_user_lesson_end', array($this, 'lesson_end'), 10, 2 );

		// quiz score
		add_action( 'sensei_user_quiz_grade', array($this, 'quiz_grade'), 10, 4 );

		// specific answer action
		add_action( 'sensei_log_activity_after', array($this, 'check_for_answer'), 10, 2 );

		// new lesson published
		add_action( 'save_post', array($this, 'new_lesson_published'), 10, 2 );

		// email form variables
		add_action( 'fue_email_variables_list', array($this, 'email_variables_list') );

		// variable replacements
		add_action( 'fue_before_variable_replacements', array($this, 'register_variable_replacements'), 10, 4 );

		// Send Manual
		add_action( 'fue_manual_types', array($this, 'manual_types') );
		add_action( 'fue_manual_type_actions', array($this, 'manual_type_actions') );
		add_action( 'fue_manual_js', array($this, 'manual_js') );
		add_filter( 'fue_manual_email_recipients', array($this, 'get_manual_email_recipients'), 10, 2 );

		// integrations
		add_action( 'fue_settings_integration', array( $this, 'integrations' ) );
		add_action( 'fue_settings_saved', array( $this, 'addon_save_settings' ), 10, 1 );
	}

	/**
	 * Display a message on Sensei's frontend
	 *
	 * @param string $message
	 * @since 4.1
	 */
	public static function add_message( $message ) {
		global $woothemes_sensei;
		$woothemes_sensei->frontend->messages .= '<div class="woo-sc-box info">'. esc_html( $message ) .'</div>';
	}

	/**
	 * Checks if WooThemes Sensei is installed and activated
	 * @return bool True if Sensei is installed
	 */
	public static function is_installed() {
		return function_exists('Sensei');
	}

	/**
	 * Check if the Sensei-Certificates add-on is installed
	 * @return bool
	 */
	public static function is_certificates_installed() {
		return class_exists('WooThemes_Sensei_Certificates');
	}

	/**
	 * Register custom email type
	 *
	 * @param array $types
	 * @return array
	 */
	public function register_email_type( $types ) {
		$triggers = array(
			'specific_answer'       => __('after selecting a specific answer', 'follow_up_emails'),
			'course_signup'         => __('after signed up to a course', 'follow_up_emails'),
			'course_completed'      => __('after course is completed', 'follow_up_emails'),
			'lesson_start'          => __('after lesson is started', 'follow_up_emails'),
			'lesson_completed'      => __('after lesson is completed', 'follow_up_emails'),
			'quiz_completed'        => __('after completing a quiz', 'follow_up_emails'),
			'quiz_passed'           => __('after passing a quiz', 'follow_up_emails'),
			'quiz_failed'           => __('after failing a quiz', 'follow_up_emails'),
			'lesson_added'          => __('after lesson added', 'follow_up_emails')
		);

		$props = array(
			'label'                 => __('Sensei Emails', 'follow_up_emails'),
			'singular_label'        => __('Sensei Email', 'follow_up_emails'),
			'supports'              => array('conditions'),
			'triggers'              => $triggers,
			'durations'             => Follow_Up_Emails::$durations,
			'long_description'      => __('Create emails to send based upon the quiz/course/lesson/test status you define for Sensei.', 'follow_up_emails'),
			'short_description'     => __('Create emails to send based upon the quiz/course/lesson/test status you define for Sensei.', 'follow_up_emails')
		);
		$types[] = new FUE_Email_Type( 'sensei', $props );

		return $types;
	}

	/**
	 * Add course selector to the Trigger tab
	 *
	 * @param FUE_Email $email
	 */
	public function register_trigger_fields( $email ) {
		if ( $email->type == 'sensei' ) {
			// load the categories
			$course_id  = (isset($email->meta['sensei_course_id'])) ? $email->meta['sensei_course_id'] : '';
			$lesson_id  = (isset($email->meta['sensei_lesson_id'])) ? $email->meta['sensei_lesson_id'] : '';
			$quiz_id    = (isset($email->meta['sensei_quiz_id'])) ? $email->meta['sensei_quiz_id'] : '';
			$question_id= (isset($email->meta['sensei_question_id'])) ? $email->meta['sensei_question_id'] : '';
			$answer     = (isset($email->meta['sensei_answer'])) ? $email->meta['sensei_answer'] : '';

			include FUE_TEMPLATES_DIR .'/email-form/sensei/selectors.php';
		}
	}

	/**
	 * Register sensei-related conditions
	 *
	 * @param array $conditions
	 * @param FUE_Email $email
	 * @return array
	 */
	public function register_conditions( $conditions, $email ) {
		if ( $email->type == 'sensei' ) {
			$conditions = $conditions + $this->conditions->get_conditions();
		}

		return $conditions;
	}

	/**
	 * Run conditions on the queue $item and see if it passes
	 *
	 * @param bool|WP_Error $passed
	 * @param FUE_Sending_Queue_Item $item
	 * @return bool|WP_Error
	 */
	public function check_item_conditions( $passed, $item ) {

		if ( is_wp_error( $passed ) ) {
			return $passed;
		}

		$sensei_conditions  = $this->conditions->get_conditions();
		$email              = new FUE_Email( $item->email_id );
		$email_conditions   = !empty($email->conditions) ? $email->conditions : array();

		if ( !$email->supports('conditions') ) {
			return $passed;
		}

		foreach ( $email_conditions as $email_condition ) {

			if ( array_key_exists( $email_condition['condition'], $sensei_conditions ) ) {
				// this is a WC condition
				$passed = $this->conditions->test_condition( $email_condition, $item );

				if ( is_wp_error( $passed ) ) {
					// immediately return errors
					return $passed;
				}
			}

		}

		return $passed;
	}

	/**
	 * HTML for email form
	 *
	 * @param array $values
	 */
	public function email_form( $values ) {
		$course_id  = (isset($values['meta']['sensei_course_id'])) ? $values['meta']['sensei_course_id'] : '';
		$lesson_id  = (isset($values['meta']['sensei_lesson_id'])) ? $values['meta']['sensei_lesson_id'] : '';
		$quiz_id    = (isset($values['meta']['sensei_quiz_id'])) ? $values['meta']['sensei_quiz_id'] : '';
		$question_id= (isset($values['meta']['sensei_question_id'])) ? $values['meta']['sensei_question_id'] : '';
		$answer     = (isset($values['meta']['sensei_answer'])) ? $values['meta']['sensei_answer'] : '';

		include FUE_TEMPLATES_DIR .'/email-form/sensei/selectors.php';
	}

	/**
	 * Register sensei nonce for the email form
	 * @param array $locale
	 * @return array
	 */
	public function inject_nonce_values( $locale ) {
		$locale['sensei_search_courses']    = wp_create_nonce("search-courses");
		$locale['sensei_search_lessons']    = wp_create_nonce("search-lessons");
		$locale['sensei_search_quizzes']    = wp_create_nonce("search-quizzes");
		$locale['sensei_search_questions']  = wp_create_nonce("search-questions");

		return $locale;
	}

	/**
	 * JS for email form
	 */
	public function email_form_script() {
		wp_enqueue_script( 'fue-form-sensei', FUE_TEMPLATES_URL .'/js/email-form-sensei.js' );
	}

	/**
	 * JS for validating sensei emails
	 *
	 * @param array $values
	 */
	public function email_form_validation_script( $values ) {
		if ( $values['type'] != 'sensei' )
			return;
		?>
		if ( jQuery("#interval_type").val() == "specific_answer" && jQuery("select#question_id").val() == "" ) {
			jQuery("#question_id").parents(".field").addClass("fue-error");
			error = true;
		}

		<?php
	}

	/**
	 * Send a notification to the course creator that the course has been completed by all learners enrolled
	 * A minimum of 2 learners is required to fire the CRM email.
	 *
	 * @param int $user_id
	 * @param int $course_id
	 */
	public function send_crm_course_completed_email( $user_id, $course_id ) {
		if ( get_post_meta( $course_id, '_sensei_crm_course_completed_email_sent', true ) ) {
			return;
		}

		$learners   = $this->get_course_learners( $course_id );
		$incomplete = false;

		if ( count( $learners ) < 2 ) {
			return;
		}

		$learners_list = '<ul>';
		foreach ( $learners as $learner ) {
			$learners_list .= '<li>'. $learner['name'] .' &lt;'. $learner['email'] .'&gt;</li>';

			if ( $learner['status'] != 'complete' ) {
				$incomplete = true;
			}
		}
		$learners_list .= '</ul>';

		if ( $incomplete ) {
			return;
		}

		$course     = get_post( $course_id );
		$subject    = get_option( 'fue_sensei_course_completed_email_subject', '' );
		$body       = get_option( 'fue_sensei_course_completed_email_body', '' );

		if ( !$course || empty( $subject ) || empty( $body ) ) {
			return;
		}

		$variables = new FUE_Sending_Email_Variables();
		$replacements = array(
			'course'    => get_the_title( $course ),
			'learners'  => $learners_list
		);
		$variables->register( $replacements );

		$subject = $variables->apply_replacements( $subject );
		$body    = $variables->apply_replacements( $body );
		$creator = new WP_User( $course->post_author );

		$queue = new FUE_Sending_Queue_Item();
		$queue->email_trigger   = __('Sensei CRM: Course Completed', 'follow_up_emails');
		$queue->user_id         = $course->post_author;
		$queue->user_email      = $creator->user_email;
		$queue->is_sent         = 0;
		$queue->status          = 1;
		$queue->meta            = array(
			'subject'   => $subject,
			'message'   => $body
		);
		$queue->save();

		Follow_Up_Emails::instance()->mailer->send_adhoc_email( $queue );

		add_post_meta( $course_id, '_sensei_crm_course_completed_email_sent', true );

	}

	/**
	 * Schedule the sending of the email notification for learners who have not completed
	 * the course after a set amount of time
	 *
	 * @param int $user_id
	 * @param int $course_id
	 */
	public function schedule_crm_course_incomplete_email( $user_id, $course_id ) {
		$course     = get_post( $course_id );
		$subject    = get_option( 'fue_sensei_course_incomplete_email_subject', '' );
		$body       = get_option( 'fue_sensei_course_incomplete_email_body', '' );
		$data       = get_comments(array(
			'user_id'   => $user_id,
			'post_id'   => $course_id,
			'type'      => 'sensei_course_status',
			'status'    => 'in-progress'
		));

		if ( !$data || !$course || empty( $subject ) || empty( $body ) ) {
			return;
		}

		// schedule
		$status             = array_pop( $data );
		$duration_number    = get_option('fue_sensei_course_incomplete_email_deadline_number', '');
		$duration_period    = get_option('fue_sensei_course_incomplete_email_deadline_period', '');
		$start_date         = get_comment_meta( $status->comment_ID, 'start', true );

		if ( empty( $duration_number ) || empty( $duration_period ) ) {
			return;
		}

		$schedule = strtotime( sprintf( "%s +%d %s", $start_date, $duration_number, $duration_period ) );

		if ( $schedule === -1 ) {
			return;
		}

		$creator = new WP_User( $course->post_author );

		$queue = new FUE_Sending_Queue_Item();
		$queue->email_trigger   = __('Sensei CRM: Course Incomplete', 'follow_up_emails');
		$queue->user_id         = $course->post_author;
		$queue->user_email      = $creator->user_email;
		$queue->is_sent         = 0;
		$queue->send_on         = $schedule;
		$queue->status          = 1;
		$queue->meta            = array(
			'adhoc'     => true,
			'subject'   => $subject,
			'message'   => $body,
			'user_id'   => $user_id,
			'course_id' => $course_id,
			'comment_id'=> $status->comment_ID,
			'sensei_email' => 'course_incomplete'
		);
		$queue->save();

		Follow_Up_Emails::instance()->scheduler->schedule_email( $queue->id, $schedule );

	}

	/**
	 * Build the list of learners who have completed and those who are still taking up the course
	 *
	 * @param string $message
	 * @param FUE_Sending_Queue_Item $queue
	 * @return string
	 */
	public function replace_crm_course_message( $message, $queue ) {
		if ( !empty( $queue->meta['sensei_email'] ) && $queue->meta['sensei_email'] == 'course_incomplete' ) {
			$course     = get_post( $queue->meta['course_id'] );
			$user       = new WP_User( $queue->meta['user_id'] );
			$user_email = $user->user_email;
			$user_name  = $user->display_name;

			$data = get_comment( $queue->meta['comment_id'] );

			if ( empty( $data ) ) {
				return '';
			}

			$start  = get_comment_meta( $data->comment_ID, 'start', true );
			$start  = date( get_option('date_format') .' '. get_option('time_format'), strtotime( $start ) );

			if ( $data->comment_approved == 'complete' ) {
				return '';
			}

			if ( Follow_Up_Emails::instance()->is_woocommerce_installed() ) {
				$billing_email      = get_user_meta( $user->ID, 'billing_email', true );
				$billing_first_name = get_user_meta( $user->ID, 'billing_first_name', true );
				$billing_last_name  = get_user_meta( $user->ID, 'billing_last_name', true );

				if ( $billing_email ) {
					$user_email = $billing_email;
				}

				if ( $billing_first_name || $billing_last_name ) {
					$user_name = $billing_first_name .' '. $billing_last_name;
				}
			}

			$variables  = new FUE_Sending_Email_Variables();
			$replacements = array(
				'course'            => get_the_title( $course ),
				'learner_name'      => $user_name,
				'learner_email'     => $user_email,
				'start_date'        => $start
			);
			$variables->register( $replacements );

			$message = $variables->apply_replacements( $message );

		}

		return $message;
	}

	/**
	 * Get all learners in a particular course
	 *
	 * @param int $course_id
	 * @return array
	 */
	public function get_course_learners( $course_id ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		// get all learners
		$learners = array();
		$results  = $wpdb->get_results($wpdb->prepare(
			"SELECT comment_ID, user_id, comment_approved AS status
			FROM {$wpdb->comments}
			WHERE comment_type = 'sensei_course_status'
			AND comment_post_ID = %d",
			$course_id
		));

		$is_woocommerce = Follow_Up_Emails::instance()->is_woocommerce_installed();

		foreach ( $results as $row ) {
			$user = new WP_User( $row->user_id );
			$user_email = $user->user_email;
			$user_name  = $user->display_name;

			if ( $is_woocommerce ) {
				$billing_email      = get_user_meta( $user->ID, 'billing_email', true );
				$billing_first_name = get_user_meta( $user->ID, 'billing_first_name', true );
				$billing_last_name  = get_user_meta( $user->ID, 'billing_last_name', true );

				if ( $billing_email ) {
					$user_email = $billing_email;
				}

				if ( $billing_first_name || $billing_last_name ) {
					$user_name = $billing_first_name .' '. $billing_last_name;
				}
			}

			$learners[] = array(
				'id'        => $row->user_id,
				'status'    => $row->status,
				'email'     => $user_email,
				'name'      => $user_name
			);
		}

		return $learners;
	}

	/**
	 * Queue emails after a user signs up to a course
	 *
	 * @param int $user_id
	 * @param int $course_id
	 */
	public function course_sign_up( $user_id, $course_id ) {

		$emails = fue_get_emails( 'sensei', FUE_Email::STATUS_ACTIVE, array(
			'meta_query' => array(
				array(
					'key'   => '_interval_type',
					'value' => 'course_signup'
				)
			)
		) );

		foreach ( $emails as $email ) {
			$meta = maybe_unserialize( $email->meta );

			// look for a possible duplicate item in the queue
			$dupes = Follow_Up_Emails::instance()->scheduler->get_items(array(
				'email_id'  => $email->id,
				'is_sent'   => 0,
				'user_id'   => $user_id
			));

			if ( count( $dupes ) > 0 ) {

				foreach ( $dupes as $dupe_item ) {
					if ( !empty( $dupe_item->meta['course_id'] ) && $dupe_item->meta['course_id'] == $course_id ) {
						// there already is an unsent queue item for the exact same order
						continue 2;
					}
				}

			}

			if ( is_array( $meta ) && isset( $meta['sensei_course_id'] ) && $meta['sensei_course_id'] > 0 ) {
				// A specific course has been selected for this email.
				// Only queue if the course signed up for matches with the selected course
				if ( $course_id == $meta['sensei_course_id'] ) {
					$values = array(
						'user_id'   => $user_id,
						'meta'      => array('course_id' => $course_id)
					);

					FUE_Sending_Scheduler::queue_email( $values, $email );

				}

				continue;

			}

			$values = array(
				'user_id'   => $user_id,
				'meta'      => array('course_id' => $course_id)
			);

			FUE_Sending_Scheduler::queue_email( $values, $email );

		}
	}

	/**
	 * Queue emails after a course has been completed
	 *
	 * @param int $user_id
	 * @param int $course_id
	 */
	public function course_completed( $user_id, $course_id ) {

		$emails = fue_get_emails( 'sensei', FUE_Email::STATUS_ACTIVE, array(
			'meta_query' => array(
				array(
					'key'   => '_interval_type',
					'value' => 'course_completed'
				)
			)
		) );

		foreach ( $emails as $email ) {

			$meta = maybe_unserialize( $email->meta );

			// look for a possible duplicate item in the queue
			$dupes = Follow_Up_Emails::instance()->scheduler->get_items(array(
				'email_id'  => $email->id,
				'is_sent'   => 0,
				'user_id'   => $user_id
			));

			if ( count( $dupes ) > 0 ) {

				foreach ( $dupes as $dupe_item ) {
					if ( !empty( $dupe_item->meta['course_id'] ) && $dupe_item->meta['course_id'] == $course_id ) {
						// there already is an unsent queue item for the exact same order
						continue 2;
					}
				}

			}

			if ( is_array( $meta ) && isset( $meta['sensei_course_id'] ) && $meta['sensei_course_id'] > 0 ) {
				// A specific course has been selected for this email.
				// Only queue if the completed course matches with the selected course
				if ( $course_id == $meta['sensei_course_id'] ) {
					$values = array(
						'user_id'   => $user_id,
						'meta'      => array('course_id' => $course_id)
					);

					FUE_Sending_Scheduler::queue_email( $values, $email );

				}

				continue;

			}

			$values = array(
				'user_id'   => $user_id,
				'meta'      => array('course_id' => $course_id)
			);

			FUE_Sending_Scheduler::queue_email( $values, $email );
		}
	}

	/**
	 * Queue emails after a lesson starts
	 *
	 * @param int $user_id
	 * @param int $lesson_id
	 */
	public function lesson_start( $user_id, $lesson_id ) {

		$emails = fue_get_emails( 'sensei', FUE_Email::STATUS_ACTIVE, array(
			'meta_query' => array(
				array(
					'key'   => '_interval_type',
					'value' => 'lesson_start'
				)
			)
		) );

		foreach ( $emails as $email ) {

			$meta = maybe_unserialize( $email->meta );

			// look for a possible duplicate item in the queue
			$dupes = Follow_Up_Emails::instance()->scheduler->get_items(array(
				'email_id'  => $email->id,
				'is_sent'   => 0,
				'user_id'   => $user_id
			));

			if ( count( $dupes ) > 0 ) {

				foreach ( $dupes as $dupe_item ) {
					if ( !empty( $dupe_item->meta['lesson_id'] ) && $dupe_item->meta['lesson_id'] == $lesson_id ) {
						// there already is an unsent queue item for the exact same order
						continue 2;
					}
				}

			}

			if ( is_array( $meta ) && isset( $meta['sensei_lesson_id'] ) && $meta['sensei_lesson_id'] > 0 ) {
				// A specific lesson has been selected for this email.
				// Only queue if the lesson started matches the selected lesson
				if ( $lesson_id == $meta['sensei_lesson_id'] ) {
					$values = array(
						'user_id'   => $user_id,
						'meta'      => array('lesson_id' => $lesson_id)
					);

					FUE_Sending_Scheduler::queue_email( $values, $email );

				}

				continue;

			} else {
				$values = array(
					'user_id'   => $user_id,
					'meta'      => array('lesson_id' => $lesson_id)
				);

				FUE_Sending_Scheduler::queue_email( $values, $email );

			}

		}
	}

	/**
	 * Queue emails after a lesson ends
	 *
	 * @param int $user_id
	 * @param int $lesson_id
	 */
	public function lesson_end( $user_id, $lesson_id ) {

		$emails = fue_get_emails( 'sensei', FUE_Email::STATUS_ACTIVE, array(
			'meta_query' => array(
				array(
					'key'   => '_interval_type',
					'value' => 'lesson_completed'
				)
			)
		) );

		foreach ( $emails as $email ) {

			$meta = maybe_unserialize( $email->meta );

			// look for a possible duplicate item in the queue
			$dupes = Follow_Up_Emails::instance()->scheduler->get_items(array(
				'email_id'  => $email->id,
				'is_sent'   => 0,
				'user_id'   => $user_id
			));

			if ( count( $dupes ) > 0 ) {

				foreach ( $dupes as $dupe_item ) {
					if ( !empty( $dupe_item->meta['lesson_id'] ) && $dupe_item->meta['lesson_id'] == $lesson_id ) {
						// there already is an unsent queue item for the exact same order
						continue 2;
					}
				}

			}

			if ( is_array( $meta ) && isset( $meta['sensei_lesson_id'] ) && $meta['sensei_lesson_id'] > 0 ) {
				// Only queue if the selected lesson matches
				if ( $lesson_id == $meta['sensei_lesson_id'] ) {
					$values = array(
						'user_id'   => $user_id,
						'meta'      => array('lesson_id' => $lesson_id)
					);

					FUE_Sending_Scheduler::queue_email( $values, $email );

				}

				continue;

			}

			$values = array(
				'user_id'   => $user_id,
				'meta'      => array('lesson_id' => $lesson_id)
			);

			FUE_Sending_Scheduler::queue_email( $values, $email );
		}
	}

	/**
	 * Queue emails after a quiz has been graded
	 *
	 * @param int $user_id
	 * @param int $quiz_id
	 * @param float $grade
	 * @param float $passmark
	 */
	public function quiz_grade( $user_id, $quiz_id, $grade, $passmark ) {
		global $wpdb;

		$triggers = array( 'quiz_completed', 'quiz_passed', 'quiz_failed' );
		$emails = fue_get_emails( 'sensei', FUE_Email::STATUS_ACTIVE, array(
			'meta_query' => array(
				array(
					'key'       => '_interval_type',
					'value'     => $triggers,
					'compare'   => 'IN'
				)
			)
		) );

		foreach ( $emails as $email ) {

			if ( $email->trigger == 'quiz_passed' && $grade < $passmark ) {
				// failed the quiz
				continue;
			}

			if ( $email->trigger == 'quiz_failed' && $grade >= $passmark ) {
				// passed the quiz
				continue;
			}

			// look for a possible duplicate item in the queue
			$dupes = Follow_Up_Emails::instance()->scheduler->get_items(array(
				'email_id'  => $email->id,
				'is_sent'   => 0,
				'user_id'   => $user_id
			));

			if ( count( $dupes ) > 0 ) {

				foreach ( $dupes as $dupe_item ) {
					if ( !empty( $dupe_item->meta['quiz_id'] ) && $dupe_item->meta['quiz_id'] == $quiz_id ) {
						// there already is an unsent queue item for the exact same order
						Follow_Up_Emails::instance()->scheduler->delete_item( $dupe_item->id );
					}
				}

			}

			$meta = maybe_unserialize( $email->meta );

			if ( is_array( $meta ) && isset( $meta['sensei_quiz_id'] ) && $meta['sensei_quiz_id'] > 0 ) {
				// Only queue if the selected lesson matches
				if ( $quiz_id == $meta['sensei_quiz_id'] ) {
					$values = array(
						'user_id'   => $user_id,
						'meta'      => array('quiz_id' => $quiz_id, 'grade' => $grade, 'passmark' => $passmark)
					);

					FUE_Sending_Scheduler::queue_email( $values, $email );

				}

				continue;

			}

			$values = array(
				'user_id'   => $user_id,
				'meta'      => array('quiz_id' => $quiz_id, 'grade' => $grade, 'passmark' => $passmark)
			);

			FUE_Sending_Scheduler::queue_email( $values, $email );
		}
	}

	/**
	 * Check if a specific answer has been submitted
	 *
	 * @param array $args
	 * @param array $data
	 */
	public function check_for_answer( $args, $data ) {
		global $wpdb;

		if ( $args['type'] != 'sensei_user_answer' )
			return;

		$question_id = $args['post_id'];

		$emails = fue_get_emails( 'sensei', FUE_Email::STATUS_ACTIVE, array(
			'meta_query' => array(
				array(
					'key'   => '_interval_type',
					'value' => 'specific_answer'
				)
			)
		) );

		foreach ( $emails as $email ) {

			$meta = maybe_unserialize( $email->meta );

			if ( is_array( $meta ) ) {

				$email_question_id  = (isset( $meta['sensei_question_id'] ) ) ? $meta['sensei_question_id'] : '';
				$email_answer       = (isset( $meta['sensei_answer']) ) ? $meta['sensei_answer'] : '';

				// The answer to check for is required
				if ( empty( $email_answer ) )
					continue;

				// Question IDs must match
				if ( $email_question_id != $question_id )
					continue;

				$posted_answer = maybe_unserialize( base64_decode($args['data']) );

				// answers do not match
				if ( is_array( $posted_answer ) ) {
					$posted_answer = current($posted_answer);
				}
				if ( $email_answer != $posted_answer )
					continue;

				$values = array(
					'user_id'   => $args['user_id'],
					'meta'      => array('question_id' => $question_id, 'answer' => $posted_answer)
				);

				FUE_Sending_Scheduler::queue_email( $values, $email );

			}

		}

	}

	/**
	 * Hooked into the 'publish_post' action, it checks and
	 * queues Sensei emails with the 'lesson added' trigger
	 *
	 * @param int $id
	 * @param WP_Post $post
	 */
	public function new_lesson_published( $id, $post ) {
		if ( get_post_type( $id ) != 'lesson' || $post->post_status != 'publish' ) {
			return;
		}

		$published_before = get_post_meta( $id, '_published_before', true );

		if ( $published_before ) {
			return;
		}

		$course_id = get_post_meta( $id, '_lesson_course', true );

		if ( !$course_id ) {
			return;
		}

		$users  = array();
		$emails = fue_get_emails( 'sensei', FUE_Email::STATUS_ACTIVE, array(
			'meta_query' => array(
				array(
					'key'   => '_interval_type',
					'value' => 'lesson_added'
				)
			)
		) );

		if ( !empty( $emails ) ) {
			$users = $this->get_course_learners( $course_id );
		}

		foreach ( $emails as $email ) {

			$meta = maybe_unserialize( $email->meta );

			if ( is_array( $meta ) && isset( $meta['sensei_course_id'] ) && $meta['sensei_course_id'] > 0 ) {
				// A specific course has been selected for this email.
				// Only queue if the completed course matches with the selected course
				if ( $course_id == $meta['sensei_course_id'] ) {

					foreach ( $users as $user ) {
						$values = array(
							'user_id'       => $user['id'],
							'user_email'    => $user['email'],
							'meta'          => array('course_id' => $course_id, 'lesson_id' => $id)
						);

						FUE_Sending_Scheduler::queue_email( $values, $email );
					}

				}

				continue;

			}

			foreach ( $users as $user ) {
				$values = array(
					'user_id'       => $user['id'],
					'user_email'    => $user['email'],
					'meta'          => array('course_id' => $course_id, 'lesson_id' => $id)
				);

				FUE_Sending_Scheduler::queue_email( $values, $email );
			}

		}

		update_post_meta( $id, '_published_before', true );
	}

	/**
	 * List of available variables
	 * @param FUE_Email $email
	 */
	public function email_variables_list( $email ) {
		if ( $email->type != 'sensei' ) {
			return;
		}
		?>
		<li class="var hideable var_sensei var_sensei_course"><strong>{teacher_first_name}</strong> <img class="help_tip" title="<?php esc_attr_e('The Teacher\'s first name', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_sensei var_sensei_course"><strong>{teacher_last_name}</strong> <img class="help_tip" title="<?php esc_attr_e('The Teacher\'s last name', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_sensei var_sensei_course"><strong>{teacher_name}</strong> <img class="help_tip" title="<?php esc_attr_e('The Teacher\'s full name', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_sensei var_sensei_course"><strong>{course_name}</strong> <img class="help_tip" title="<?php esc_attr_e('The name of the course', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_sensei var_sensei_course"><strong>{course_url}</strong> <img class="help_tip" title="<?php esc_attr_e('The raw URL to the course', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_sensei var_sensei_course"><strong>{course_link}</strong> <img class="help_tip" title="<?php esc_attr_e('The link to the course with the course name as the display text', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_sensei var_sensei_course"><strong>{course_results_url}</strong> <img class="help_tip" title="<?php esc_attr_e('The URL to the course results page', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>

		<?php if ( self::is_certificates_installed() ): ?>
			<li class="var hideable var_sensei var_sensei_course"><strong>{certificate_url}</strong> <img class="help_tip" title="<?php esc_attr_e('The raw URL to the course certificate', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
			<li class="var hideable var_sensei var_sensei_course"><strong>{certificate_link}</strong> <img class="help_tip" title="<?php esc_attr_e('The link to the course certificate with the course name as the display text', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<?php endif; ?>

		<li class="var hideable var_sensei var_sensei_lesson"><strong>{lesson_name}</strong> <img class="help_tip" title="<?php esc_attr_e('The name of the lesson', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_sensei var_sensei_lesson"><strong>{lesson_url}</strong> <img class="help_tip" title="<?php esc_attr_e('The raw URL to the lesson', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_sensei var_sensei_lesson"><strong>{lesson_link}</strong> <img class="help_tip" title="<?php esc_attr_e('The link to the lesson with the lesson name as the display text', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>

		<li class="var hideable var_sensei var_sensei_quiz"><strong>{quiz_url}</strong> <img class="help_tip" title="<?php esc_attr_e('The raw URL to the quiz', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_sensei var_sensei_quiz"><strong>{quiz_link}</strong> <img class="help_tip" title="<?php esc_attr_e('The link to the quiz', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_sensei var_sensei_quiz var_sensei_grade"><strong>{quiz_grade}</strong> <img class="help_tip" title="<?php esc_attr_e('The score the user got on the quiz', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_sensei var_sensei_quiz var_sensei_passmark"><strong>{quiz_passmark}</strong> <img class="help_tip" title="<?php esc_attr_e('The passing mark on the quiz taken', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<?php
	}

	/**
	 * Register subscription variables to be replaced
	 *
	 * @param FUE_Sending_Email_Variables   $var
	 * @param array                 $email_data
	 * @param FUE_Email             $email
	 * @param object                $queue_item
	 */
	public function register_variable_replacements( $var, $email_data, $email, $queue_item ) {
		$variables = array(
			'teacher_first_name' => '',
			'teacher_last_name'  => '',
			'teacher_name'       => '',
			'course_name'        => '',
			'course_url'         => fue_replacement_url_var( '' ),
			'course_link'        => '',
			'course_results_url' => fue_replacement_url_var( '' ),
			'certificate_url'    => fue_replacement_url_var( '' ),
			'certificate_link'   => '',
			'lesson_name'        => '',
			'lesson_url'         => fue_replacement_url_var( '' ),
			'lesson_link'        => '',
			'quiz_url'           => fue_replacement_url_var( '' ),
			'quiz_link'          => '',
			'quiz_grade'         => '',
			'quiz_passmark'      => ''
		);

		// use test data if the test flag is set
		if ( isset( $email_data['test'] ) && $email_data['test'] ) {
			$variables = $this->add_test_variable_replacements( $variables, $email_data, $email );
		} else {
			$variables = $this->add_variable_replacements( $variables, $email_data, $queue_item, $email );
		}

		$var->register( $variables );
	}

	/**
	 * Additional recipient options for manual emails
	 */
	public function manual_types() {
		$options = array(
			'course' => __('Customers who have signed up for these courses', 'follow_up_emails')
		);

		include FUE_TEMPLATES_DIR .'/email-form/sensei/manual-email-types.php';

	}

	/**
	 * The actions for the additional manual email options
	 * @param FUE_Email $email
	 */
	public function manual_type_actions($email) {
		$courses = $this->get_courses();

		include FUE_TEMPLATES_DIR .'/email-form/sensei/manual-email-actions.php';
	}

	/**
	 * Inline JS for sending manual emails
	 */
	public function manual_js() {
		?>
		jQuery( '#send_type' ).on( 'change', function() {
			switch (jQuery(this).val()) {
				case "course":
					jQuery(".send-type-course").show();
					break;

			}
		} ).trigger( 'change' );

	<?php
	}

	/**
	 * Load all recipients matching the provided send type
	 *
	 * @param array $recipients
	 * @param array $post
	 *
	 * @return array
	 */
	public function get_manual_email_recipients( $recipients, $post ) {
		$send_type  = $post['send_type'];

		if ( $send_type == 'course' ) {
			// customers who bought products from the selected categories
			if ( is_array($post['course_ids']) ) {
				foreach ( $post['course_ids'] as $course_id ) {
					$users = $this->get_course_learners( $course_id );

					foreach ( $users as $user ) {
						$key    = $user['id'] .'|'. $user['email'] .'|'. $user['name'];
						$value  = array( $user['id'], $user['email'], $user['name'] );

						$recipients[ $key ] = $value;
					}

				}

			}
		}

		return $recipients;
	}

	/**
	 * Integrations tab
	 */
	public function integrations() {
		include FUE_TEMPLATES_DIR .'/settings/settings-sensei.php';
	}

	/**
	 * Save addon settings
	 */
	public function addon_save_settings( $post ) {
		$post = array_map( 'sanitize_text_field', wp_unslash( $post ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Already handled before action.

		if ( $post['section'] == 'integration' ) {
			// disable email wrapping
			$completed                  = (isset($post['sensei_course_completed_email'])) ? (int)$post['sensei_course_completed_email'] : 0;
			$completed_email_subject    = '';
			$completed_email_body       = '';

			$incomplete                 = (isset($post['sensei_course_incomplete_email'])) ? (int)$post['sensei_course_incomplete_email'] : 0;
			$incomplete_email_subject   = '';
			$incomplete_email_body      = '';
			$deadline_number    = absint( $post['sensei_course_incomplete_email_deadline_number'] );
			$deadline_period    = sanitize_text_field( wp_unslash( $post['sensei_course_incomplete_email_deadline_period'] ) );

			if ( $completed ) {
				$completed_email_subject    = sanitize_text_field( wp_unslash( $post['sensei_course_completed_email_subject'] ) );
				$completed_email_body       = sanitize_text_field( wp_unslash( $post['sensei_course_completed_email_body'] ) );
			}

			if ( $incomplete ) {
				$incomplete_email_subject    = sanitize_text_field( wp_unslash( $post['sensei_course_incomplete_email_subject'] ) );
				$incomplete_email_body       = sanitize_text_field( wp_unslash( $post['sensei_course_incomplete_email_body'] ) );
			}

			update_option( 'fue_sensei_course_completed_email', $completed );
			update_option( 'fue_sensei_course_completed_email_subject', $completed_email_subject );
			update_option( 'fue_sensei_course_completed_email_body', $completed_email_body );

			update_option( 'fue_sensei_course_incomplete_email', $incomplete );
			update_option( 'fue_sensei_course_incomplete_email_subject', $incomplete_email_subject );
			update_option( 'fue_sensei_course_incomplete_email_body', $incomplete_email_body );
			update_option( 'fue_sensei_course_incomplete_email_deadline_number', $deadline_number );
			update_option( 'fue_sensei_course_incomplete_email_deadline_period', $deadline_period );
		}

	}

	/**
	 * Scan through the keys of $variables and apply the replacement if one is found
	 * @param array     $variables
	 * @param array     $email_data
	 * @param object    $queue_item
	 * @param FUE_Email $email
	 * @return array
	 */
	protected function add_variable_replacements( $variables, $email_data, $queue_item, $email ) {

		$meta = maybe_unserialize( $queue_item->meta );

		if ( !empty( $meta['course_id'] ) ) {
			$variables = array_merge( $variables, self::get_course_variables( $meta['course_id'] ) );

			if ( self::is_certificates_installed() ) {
				$variables['certificate_url']   = self::get_certificate_url( $meta['course_id'], $queue_item->user_id );
				$variables['certificate_link']  = '<a href="'. $variables['certificate_url'] .'">'. $variables['course_name'] .'</a>';
			}

			// See https://github.com/woocommerce/woocommerce-follow-up-emails/issues/357.
			$variables['course_results_url'] = fue_replacement_url_var( esc_url( Sensei()->course_results->get_permalink( $meta['course_id'] ) ) );
			$variables['quiz_passmark']      = Sensei_Utils::sensei_course_pass_grade( $meta['course_id'] );
			$variables['quiz_grade']         = Sensei_Utils::sensei_course_user_grade( $meta['course_id'], $queue_item->user_id );
		}

		if ( !empty( $meta['lesson_id'] ) ) {
			if ( empty( $variables['course_name'] ) ) {
				$course_id = get_post_meta( $meta['lesson_id'], '_lesson_course', true );
				$variables = array_merge( $variables, self::get_course_variables( $course_id ) );
			}

			$variables = array_merge( $variables, self::get_lesson_variables( $meta['lesson_id'] ) );
		}

		if ( !empty( $meta['quiz_id'] ) ) {
			if ( empty( $variables['course_name'] ) ) {
				$course_id  = get_post_meta( $meta['quiz_id'], '_lesson_course', true );
				$variables = array_merge( $variables, self::get_course_variables( $course_id ) );
			}

			if ( empty( $variables['lesson_name'] ) ) {
				$lesson_id  = get_post_meta( $meta['quiz_id'], '_quiz_lesson', true );
				$variables = array_merge( $variables, self::get_lesson_variables( $lesson_id ) );
			}

			$variables = array_merge( $variables, self::get_quiz_variables( $meta['quiz_id'] ) );

			$variables['quiz_grade']    = $meta['grade'];
			$variables['quiz_passmark'] = $meta['passmark'];
		}

		return $variables;
	}

	/**
	 * Add variable replacements for test emails
	 *
	 * @param array     $variables
	 * @param array     $email_data
	 * @param FUE_Email $email
	 *
	 * @return array
	 */
	protected function add_test_variable_replacements( $variables, $email_data, $email ) {
		$variables['teacher_first_name'] = 'Jane';
		$variables['teacher_last_name']  = 'Doe';
		$variables['teacher_name']       = 'Jane Doe';
		$variables['course_name']        = 'Test Course';
		$variables['course_url']         = fue_replacement_url_var( site_url() );
		$variables['course_link']        = '<a href="'. site_url() .'">Test Course</a>"';
		$variables['course_results_url'] = fue_replacement_url_var( site_url() );
		$variables['lesson_name']        = 'Test Lesson';
		$variables['lesson_url']         = fue_replacement_url_var( site_url() );
		$variables['lesson_link']        = '<a href="'. site_url() .'">Test Course</a>"';
		$variables['quiz_url']           = fue_replacement_url_var( site_url() );
		$variables['quiz_link']          = '<a href="'. site_url() .'">View the lesson quiz</a>"';
		$variables['quiz_grade']         = 87;
		$variables['quiz_passmark']      = 90;
		$variables['certificate_url']   = fue_replacement_url_var( site_url() );
		$variables['certificate_link']  = '<a href="'. site_url() .'">View Certificate</a>';

		return $variables;
	}

	/**
	 * Get sensei courses
	 * @param array $args
	 *
	 * @return array
	 */
	public static function get_courses( $args = array() ) {
		$default = array(
			'post_type'     => 'course',
			'posts_per_page'=> -1,
			'post_status'   => array('publish', 'private', 'draft'),
			'tax_query'			=> array(
				array(
					'taxonomy'	=> 'product_type',
					'field'		=> 'slug',
					'terms'		=> array( 'variable', 'grouped' ),
					'operator'	=> 'NOT IN'
				)
			)
		);
		$args = array_merge( $default, $args );

		return get_posts( $args );
	}

	/**
	 * Get sensei lessons under the give $course_id
	 *
	 * @param int $course_id
	 * @param array $args
	 *
	 * @return array
	 */
	public static function get_lessons( $course_id = 0, $args = array() ) {
		$default = array(
			'post_type'     => 'lesson',
			'posts_per_page'=> -1,
			'post_status'   => array('publish', 'private', 'draft')
		);

		if ( $course_id ) {
			$default['meta_query'][] = array(
				'meta_key'      => '_lesson_course',
				'meta_value'    => $course_id
			);
		}

		$args = array_merge( $default, $args );

		return get_posts( $args );
	}

	/**
	 * Get quizzes under the give $lesson_id
	 * @param int $lesson_id
	 * @param array $args
	 *
	 * @return array
	 */
	public static function get_quizzes( $lesson_id = 0, $args = array() ) {
		$default = array(
			'post_type'     => 'quiz',
			'posts_per_page'=> -1,
			'post_status'   => array('publish', 'private', 'draft')
		);

		if ( $lesson_id ) {
			$default['meta_query'][] = array(
				'meta_key'      => '_quiz_lesson',
				'meta_value'    => $lesson_id
			);
		}

		$args = array_merge( $default, $args );

		return get_posts( $args );
	}

	/**
	 * Get questions under the given $quiz_id
	 *
	 * @param int $quiz_id
	 * @param array $args
	 *
	 * @return array
	 */
	public static function get_questions( $quiz_id = 0, $args = array() ) {
		$default = array(
			'post_type'     => 'question',
			'posts_per_page'=> -1,
			'post_status'   => array('publish', 'private', 'draft')
		);

		if ( $quiz_id ) {
			$default['meta_query'][] = array(
				'meta_key'      => '_quiz_id',
				'meta_value'    => $quiz_id
			);
		}

		$args = array_merge( $default, $args );

		return get_posts( $args );
	}

	/**
	 * Get course IDs for the given user
	 * @param int $user_id
	 * @return array
	 */
	public static function get_user_course_ids( $user_id ) {
		$wpdb       = Follow_Up_Emails::instance()->wpdb;

		$courses = $wpdb->get_col($wpdb->prepare(
			"SELECT DISTINCT comment_post_ID
			FROM {$wpdb->comments}
			WHERE user_id = %d
			AND comment_type = 'sensei_course_status'",
			$user_id
		));

		if ( !$courses ) {
			$courses = array();
		}

		return array_map( 'absint', $courses );
	}

	/**
	 * Get course IDs for the given user
	 * @param int $user_id
	 * @param array $courses
	 * @return array
	 */
	public static function get_user_lesson_ids( $user_id, $courses = array() ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		if ( empty( $courses ) ) {
			$courses = self::get_user_course_ids( $user_id );
		}

		if ( empty( $courses ) ) {
			return array();
		}

		$course_ids = implode( ',', array_map( 'absint', $courses ) );

		$lessons = $wpdb->get_col(
			"SELECT DISTINCT post_id
			FROM {$wpdb->postmeta}
			WHERE meta_key = '_lesson_course'
			AND meta_value IN ($course_ids)"
		);

		if ( !$lessons ) {
			$lessons = array();
		}

		return array_map( 'absint', $lessons );
	}

	public static function get_course_variables( $course_id ) {
		$variables = array(
			'teacher_first_name'    => '',
			'teacher_last_name'     => '',
			'teacher_name'          => '',
			'course_name' => get_the_title( $course_id ),
			'course_url'  => get_permalink( $course_id )
		);

		$post = get_post( $course_id );
		$teacher = new WP_User( $post->post_author );

		$variables['teacher_first_name'] = $teacher->first_name;
		$variables['teacher_last_name'] = $teacher->last_name;
		$variables['teacher_name'] = $teacher->display_name;

		$variables['course_link'] = '<a href="'. $variables['course_url'] .'">'. $variables['course_name'] .'</a>';

		return $variables;
	}

	public static function get_lesson_variables( $lesson_id ) {
		$variables = array(
			'lesson_name' => get_the_title( $lesson_id ),
			'lesson_url'  => get_permalink( $lesson_id )
		);

		$variables['lesson_link'] = '<a href="'. $variables['lesson_url'] .'">'. $variables['lesson_name'] .'</a>';

		return $variables;
	}

	public static function get_quiz_variables( $quiz_id ) {
		$variables = array();
		$variables['quiz_url']      = get_permalink( $quiz_id );
		$variables['quiz_link']     = '<a href="'. $variables['quiz_url'] .'">'. __('View the lesson quiz', 'follow_up_emails') .'</a>';

		return $variables;
	}

	public static function get_certificate_url( $course_id, $user_id ) {

		$certificate_url = '';

		$args = array(
			'post_type'         => 'certificate',
			'author'            => $user_id,
			'meta_key'          => 'course_id',
			'meta_value'        => $course_id,
			'posts_per_page'    => 1,
			'fields'            => 'ids'
		);

		$posts = get_posts( $args );

		if ( count( $posts ) == 1 ) {
			$certificate_id = current( $posts );
			$certificate_url = get_permalink( $certificate_id );
		}

		return $certificate_url;

	}

}

$GLOBALS['fue_sensei'] = new FUE_Addon_Sensei();
