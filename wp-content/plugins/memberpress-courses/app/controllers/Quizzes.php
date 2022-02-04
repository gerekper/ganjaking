<?php
namespace memberpress\courses\controllers;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses as base;
use memberpress\courses\helpers as helpers;
use memberpress\courses\lib as lib;
use memberpress\courses\models as models;

class Quizzes extends lib\BaseCtrl {
  public function load_hooks() {
    add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    add_filter('template_include', array($this, 'override_template'), 999999);
    add_filter('the_content', array($this, 'append_quiz_navigation'));
    add_action('wp_ajax_mpcs_submit_quiz', array($this, 'handle_submit_quiz'));
    add_action('wp_ajax_mpcs_auto_save_question', array($this, 'handle_auto_save_question'));
  }

  public function enqueue_scripts() {
    global $post;

    if($post instanceof \WP_Post && $post->post_type == models\Quiz::$cpt && is_single()) {
      $quiz = models\Quiz::find($post->ID);

      if($quiz instanceof models\Quiz) {
        wp_enqueue_style('mpcs-quiz', base\CSS_URL . '/quiz.css', array(), base\VERSION);
        wp_enqueue_script('jquery-scrollto', base\JS_URL . '/vendor/jquery.scrollTo.min.js', array('jquery'), base\VERSION);
        wp_enqueue_script('mpcs-quiz', base\JS_URL . '/quiz.js', array('jquery'), base\VERSION);

        $l10n = [
          'ajax_url' => admin_url('admin-ajax.php'),
          'submit_quiz_nonce' => wp_create_nonce('mpcs_submit_quiz'),
          'auto_save_question_nonce' => wp_create_nonce('mpcs_auto_save_question'),
          'post_id' => $quiz->ID,
          'error_submitting_quiz' => __('An error occurred submitting the quiz: %s', 'memberpress-courses'),
          'attempt_complete' => false,
          'auto_save_enabled' => apply_filters('mpcs_quiz_auto_save_enabled', true),
          'character_count' => __('%d characters', 'memberpress-courses'),
          'scroll_enabled' => apply_filters('mpcs_quiz_scroll_enabled', true),
          'scroll_speed' => apply_filters('mpcs_quiz_scroll_speed', 800),
          'scroll_offset' => apply_filters('mpcs_quiz_scroll_offset', -50),
          'progress_nonce' => wp_create_nonce('lesson_progress'),
        ];

        if(is_user_logged_in()) {
          $user_id = get_current_user_id();
          $is_quiz_available = $quiz->is_available($user_id);
          $has_completed_quiz = models\UserProgress::has_completed_lesson($user_id, $quiz->ID);

          if($is_quiz_available || $has_completed_quiz) {
            $attempt = models\Attempt::get_one(['quiz_id' => $quiz->ID, 'user_id' => get_current_user_id()]);

            if(!$attempt instanceof models\Attempt) {
              $attempt = new models\Attempt();
              $attempt->quiz_id = $quiz->ID;
              $attempt->user_id = get_current_user_id();
              $attempt->status = models\Attempt::$draft_str;
              $attempt->started_at = gmdate('Y-m-d H:i:s');
              $attempt->store();
            }

            $l10n = array_merge($l10n, [
              'attempt_id' => $attempt->id,
              'attempt_complete' => $attempt->is_complete(),
              'attempt_score' => apply_filters('mpcs_quiz_score_title', sprintf(
                '<h4 class="mpcs-quiz-score">%s</h4>',
                esc_html($attempt->get_score())
              ), $attempt),
            ]);
          }
        }

        wp_localize_script(
          'mpcs-quiz',
          'MpcsQuizL10n',
          ['l10n_print_after' => 'MpcsQuizL10n = ' . wp_json_encode($l10n)]
        );
      }
    }
  }

  /**
   * Override default template with the quiz page template
   *
   * @param string $template current template
   * @return string modified template
   */
  public static function override_template($template) {
    global $post;

    if($post instanceof \WP_Post && $post->post_type == models\Quiz::$cpt && is_single()) {
      $quiz = new models\Quiz($post->ID);
      $course = $quiz->course();

      if($course instanceof models\Course) {
        $new_template = locate_template($course->page_template);
      }
      else {
        // If the course is not found, trigger a 404 error
        global $wp_query;
        $wp_query->set_404();

        status_header(404);
        nocache_headers();

        return get_404_template();
      }

      if(helpers\App::is_classroom()){
        $template = \MeprView::file('/classroom/courses_single_quiz');
      }
      elseif(isset($new_template) && !empty($new_template)) {
        return $new_template;
      }
      else {
        $template = locate_template(
          array(
            'single-mpcs-quiz.php',
            'single-mpcs-course.php',
            'page.php',
            'custom_template.php',
            'single.php',
            'index.php'
          )
        );
      }
    }

    return $template;
  }

  /**
   * Handle the quiz submission Ajax request
   */
  public function handle_submit_quiz() {
    if(!lib\Utils::is_post_request() || !isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
      wp_send_json_error(__('Bad request', 'memberpress-courses'));
    }

    if(!check_ajax_referer('mpcs_submit_quiz', false, false)) {
      wp_send_json_error(__('Security check failed', 'memberpress-courses'));
    }

    $values = wp_unslash($_POST);
    $quiz_id = isset($values['post_id']) && is_numeric($values['post_id']) ? (int) $values['post_id'] : 0;
    $quiz = new models\Quiz($quiz_id);
    $attempt_id = isset($values['attempt_id']) && is_numeric($values['attempt_id']) ? (int) $values['attempt_id'] : 0;

    if(empty($quiz->ID)) {
      wp_send_json_error(__('Quiz not found', 'memberpress-courses'));
    }

    $section = $quiz->section();

    if(!$section instanceof models\Section) {
      wp_send_json_error(__('Course section not found', 'memberpress-courses'));
    }

    $course = $section->course();

    if(!$course instanceof models\Course) {
      wp_send_json_error(__('Course not found', 'memberpress-courses'));
    }

    $user = lib\Utils::get_currentuserinfo();

    if(!$user instanceof \WP_User) {
      wp_send_json_error(__('You must be logged in to submit a quiz', 'memberpress-courses'));
    }

    $questions = $quiz->get_questions();
    $values = $this->sanitize_quiz_values($questions, $values, $quiz);
    $errors = $this->validate_quiz($questions, $values, $quiz);

    if(!empty($errors)) {
      wp_send_json_error(compact('errors'));
    }

    $attempt = models\Attempt::find($attempt_id);

    if(!$attempt instanceof models\Attempt) {
      wp_send_json_error(__('Attempt not found', 'memberpress-courses'));
    }

    if($attempt->user_id != $user->ID) {
      wp_send_json_error(__('This attempt is for a different user', 'memberpress-courses'));
    }

    if($attempt->quiz_id != $quiz->ID) {
      wp_send_json_error(__('This attempt is for a different quiz', 'memberpress-courses'));
    }

    $now = gmdate('Y-m-d H:i:s');
    $total_points_possible = 0;
    $total_points_awarded = 0;

    foreach($questions as $question) {
      $total_points_possible += (int) $question->points;
      $total_points_awarded += $question->get_score($values[$question->id]);
    }

    $attempt->status = models\Attempt::$complete_str;
    $attempt->points_awarded = $total_points_awarded;
    $attempt->points_possible = $total_points_possible;
    $attempt->score = $total_points_possible > 0 ? round(($total_points_awarded / $total_points_possible) * 100) : 0;

    if(empty($attempt->started_at) || $attempt->started_at == lib\Utils::db_lifetime()) {
      $attempt->started_at = $now;
    }

    $attempt->finished_at = $now;
    $result = $attempt->store();

    if($result instanceof \WP_Error) {
      wp_send_json_error($result->get_error_message());
    }

    if(empty($attempt->id)) {
      wp_send_json_error(__('The attempt could not be saved', 'memberpress-courses'));
    }

    foreach($questions as $question) {
      models\Answer::insert_or_replace_answer(
        $attempt->id,
        $question->id,
        $values[$question->id],
        $question->points,
        $question->get_score($values[$question->id]),
        $this->get_grader_user_id($quiz),
        $now,
        $now
      );
    }

    if(!models\UserProgress::has_completed_lesson($user->ID, $quiz->ID)) {
      $quiz->complete($user->ID);
    }

    wp_send_json_success(array(
      'show_results' => $course->show_results == 'enabled'
    ));
  }

  /**
   * Validate the quiz submission
   *
   * @param  models\Question[] $questions The array of questions
   * @param  array             $values    The submitted values
   * @param  models\Quiz       $quiz      The quiz instance
   * @return array
   */
  protected function sanitize_quiz_values($questions, $values, $quiz) {
    $sanitized = array();

    foreach($questions as $question) {
      $key = "mpcs_quiz_question_{$question->id}";

      switch($question->type) {
        case 'multiple-choice':
        case 'true-false':
        case 'short-answer':
        default:
          $sanitized[$question->id] = isset($values[$key]) && is_string($values[$key]) ? sanitize_text_field($values[$key]) : '';
          break;
        case 'multiple-answer':
          $sanitized[$question->id] = isset($values[$key]) && is_array($values[$key]) ? array_map('sanitize_text_field', $values[$key]) : array();
          break;
        case 'essay';
          $sanitized[$question->id] = isset($values[$key]) && is_string($values[$key]) ? sanitize_textarea_field($values[$key]) : '';
          break;
      }
    }

    return apply_filters('mpcs_sanitize_quiz_values', $sanitized, $questions, $values, $quiz);
  }

  /**
   * Validate the quiz submission
   *
   * @param  models\Question[] $questions The array of questions
   * @param  array             $values    The submitted values
   * @param  models\Quiz       $quiz      The quiz instance
   * @return array
   */
  protected function validate_quiz($questions, $values, $quiz) {
    $errors = [];

    foreach($questions as $question) {
      if(!$question->required && $question->is_value_empty($values[$question->id])) {
        continue;
      }
      elseif($question->required && $question->is_value_empty($values[$question->id])) {
        $errors[] = [
          'id' => $question->id,
          'message' => __('This field is required', 'memberpress-courses')
        ];
      }
      elseif($question->type == 'essay') {
        $length = mb_strlen($values[$question->id]);
        $min_length = isset($question->settings['min']) && is_numeric($question->settings['min']) && $question->settings['min'] > 0 ? (int) $question->settings['min'] : 1;
        $max_length = isset($question->settings['max']) && is_numeric($question->settings['max']) && $question->settings['max'] >= 0 ? (int) $question->settings['max'] : 0;

        if($length < $min_length) {
          $errors[] = [
            'id' => $question->id,
            'message' => sprintf(
              /* translators: %s: the number of characters */
              _n(
                'Please enter at least %s character',
                'Please enter at least %s characters',
                $min_length,
                'memberpress-courses'
              ),
              number_format_i18n($min_length)
            )
          ];
        }
        elseif($max_length > 0 && $length > $max_length) {
          $errors[] = [
            'id' => $question->id,
            'message' => sprintf(
              /* translators: %s: the number of characters */
              _n(
                'Please enter no more than %s character',
                'Please enter no more than %s characters',
                $max_length,
                'memberpress-courses'
              ),
              number_format_i18n($max_length)
            )
          ];
        }
      }
    }

    return apply_filters('mpcs_validate_quiz', $errors, $questions, $values, $quiz);
  }

  /**
   * Get the user ID of the grader
   *
   * Currently, it is set as the first admin user.
   *
   * @param models\Quiz $quiz
   * @return int
   */
  protected function get_grader_user_id($quiz) {
    $grader_user_id = 0;

    $admins = get_users(array(
      'role' => 'administrator',
      'number' => 1,
      'fields' => 'ID',
      'orderby' => 'ID',
      'order' => 'ASC',
    ));

    if(isset($admins[0])) {
      $grader_user_id = (int) $admins[0];
    }

    return (int) apply_filters('mpcs_get_grader_user_id', $grader_user_id, $quiz);
  }

  /**
   * Append the quiz navigation to the content
   *
   * @param string $content The post content
   * @return string The modified post content
   */
  public function append_quiz_navigation($content) {
    global $post;

    if($post instanceof \WP_Post && $post->post_type == models\Quiz::$cpt && is_single()) {
      $current_user = lib\Utils::get_currentuserinfo();
      $current_lesson = new models\Lesson($post->ID);
      $lesson_nav_ids = $current_lesson->nav_ids();
      $current_lesson_index = array_search($current_lesson->ID, $lesson_nav_ids);
      $current_section = $current_lesson->section();
      $lesson_available = is_user_logged_in() && $current_lesson->is_available($current_user->ID);

      if($current_section !== false && $lesson_available) {
        if(!helpers\Lessons::has_next_lesson($current_lesson_index, $lesson_nav_ids)) {
          $current_course = $current_section->course();
          $sections = $current_course->sections();
          $section_ids = array_map(function($section) {
            return $section->id;
          }, $sections);
          $current_section_index = array_search($current_section->id, $section_ids);
        }

        $options = get_option('mpcs-options');
        $attempt = is_user_logged_in() ? models\Attempt::get_one(['quiz_id' => $post->ID, 'user_id' => get_current_user_id()]) : null;

        ob_start();
        require(\MeprView::file('/quizzes/courses_navigation'));
        $nav_links = ob_get_clean();

        $content .= $nav_links;
      }
    }

    return $content;
  }

  /**
   * Handle the auto save question Ajax request
   */
  public function handle_auto_save_question() {
    if(
      !lib\Utils::is_post_request() ||
      !isset($_POST['attempt_id'], $_POST['question_id']) ||
      !is_numeric($_POST['attempt_id']) ||
      !is_numeric($_POST['question_id'])
    ) {
      wp_send_json_error(__('Bad request', 'memberpress-courses'));
    }

    $user = lib\Utils::get_currentuserinfo();

    if(!$user instanceof \WP_User) {
      wp_send_json_error(__('You must be logged in', 'memberpress-courses'));
    }

    if(!check_ajax_referer('mpcs_auto_save_question', false, false)) {
      wp_send_json_error(__('Security check failed', 'memberpress-courses'));
    }

    $values = wp_unslash($_POST);
    $attempt_id = (int) $values['attempt_id'];
    $question_id = (int) $values['question_id'];

    if($attempt_id <= 0 || $question_id <= 0) {
      wp_send_json_error(__('Bad request', 'memberpress-courses'));
    }

    $attempt = models\Attempt::find($attempt_id);

    if(!$attempt instanceof models\Attempt) {
      wp_send_json_error(__('Attempt not found', 'memberpress-courses'));
    }

    if($attempt->user_id != get_current_user_id()) {
      wp_send_json_error(__('Bad request', 'memberpress-courses'));
    }

    $quiz = $attempt->quiz();

    if(!$quiz instanceof models\Quiz) {
      wp_send_json_error(__('Quiz not found', 'memberpress-courses'));
    }

    $question = models\Question::find($question_id);

    if(!$question instanceof models\Question) {
      wp_send_json_error(__('Question not found', 'memberpress-courses'));
    }

    $now = gmdate('Y-m-d H:i:s');
    $values = $this->sanitize_quiz_values([$question], $values, $quiz);

    models\Answer::insert_or_replace_answer(
      $attempt->id,
      $question->id,
      $values[$question->id],
      0,
      0,
      0,
      $now,
      $now
    );

    wp_send_json_success();
  }
}
