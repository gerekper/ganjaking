<?php

namespace memberpress\courses\controllers\admin;

use memberpress\courses as base;
use memberpress\courses\lib as lib;
use memberpress\courses\models as models;

class Attempts extends lib\BaseCtrl {
  public function load_hooks() {
    add_action('admin_menu', [$this, 'add_submenu_page']);
    add_action('current_screen', [$this, 'add_attempts_screen_options']);
    add_filter('set-screen-option', [$this, 'set_attempts_screen_options'], 10, 3);
    add_action('admin_init', [$this, 'ensure_quiz_exists']);
    add_action('admin_init', [$this, 'process_bulk_actions']);
    add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
    add_action('wp_ajax_mpcs_quiz_attempt_delete', [$this, 'ajax_quiz_attempt_delete']);
    add_action('wp_ajax_mpcs_quiz_attempt_view', [$this, 'ajax_quiz_attempt_view']);
  }

  public function add_submenu_page() {
    add_submenu_page(
      base\PLUGIN_NAME,
      __('Quiz Attempts', 'memberpress-courses'),
      __('Quiz Attempts', 'memberpress-courses'),
      'manage_options',
      'mpcs-quiz-attempts',
      [$this, 'route']
    );
  }

  /**
   * Display an error page if the quiz was not found
   */
  public function ensure_quiz_exists() {
    if(empty($_GET['page']) || $_GET['page'] != 'mpcs-quiz-attempts') {
      return;
    }

    $quiz_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $quiz = new models\Quiz($quiz_id);

    if(empty($quiz->ID)) {
      wp_die(__( 'The quiz was not found. Perhaps it was deleted?', 'memberpress-courses'));
    }
  }

  public function process_bulk_actions() {
    if(empty($_GET['page']) || $_GET['page'] != 'mpcs-quiz-attempts') {
      return;
    }

    $action = isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] != '-1' ? $_GET['action'] : null;

    if(empty($action)) {
      $action = isset($_GET['action2']) && !empty($_GET['action2']) && $_GET['action2'] != '-1' ? $_GET['action2'] : null;
    }

    if(!empty($action)) {
      check_admin_referer('bulk-wp_list_mpcs_quiz_attempts');

      if(!lib\Utils::is_logged_in_and_an_admin()) {
        wp_die(__( 'Sorry, you are not allowed to do this.', 'easy-affiliate', 'memberpress-courses'), 403);
      }

      $sendback = remove_query_arg(['trashed', 'untrashed', 'deleted', 'locked', 'ids', '_wpnonce'], wp_get_referer());

      $attempts = isset($_GET['att']) ? array_map('intval', $_GET['att']) : [];

      if(empty($attempts)) {
        wp_redirect($sendback);
        exit;
      }

      switch($action) {
        case 'delete':
          $deleted = 0;

          foreach($attempts as $id) {
            $attempt = new models\Attempt($id);

            if($attempt->id > 0) {
              $attempt->destroy();
              $deleted++;
            }
          }

          $sendback = add_query_arg('deleted', $deleted, $sendback);
          break;
      }

      wp_redirect($sendback);
      exit;
    }
    elseif(!empty($_GET['_wp_http_referer'])) {
      wp_redirect(remove_query_arg(['_wp_http_referer', '_wpnonce'], wp_unslash($_SERVER['REQUEST_URI'])));
      exit;
    }
  }

  public function admin_enqueue_scripts($hook) {
    if(preg_match('/_page_mpcs-quiz-attempts$/', $hook)) {
      wp_enqueue_style('vex-css', base\CSS_URL . '/vendor/vex.css', [], base\VERSION);
      wp_enqueue_style('mpcs-quiz-attempts', base\CSS_URL . '/admin_quiz_attempts.css', [], base\VERSION);
      wp_enqueue_script('vex-js', base\JS_URL . '/vendor/vex.combined.js', [], base\VERSION);
      wp_enqueue_script('mpcs-quiz-attempts', base\JS_URL . '/admin-quiz-attempts.js', ['vex-js'], base\VERSION);

      wp_localize_script('mpcs-quiz-attempts', 'MpcsQuizAttemptsL10n', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'quiz_attempt_delete_nonce' => wp_create_nonce('mpcs_quiz_attempt_delete'),
        'quiz_attempt_delete_confirm' => __("Warning: Deleting this attempt will remove the student's answers and score. This cannot be undone. Are you sure you want to delete this attempt?", 'memberpress-courses'),
        'quiz_attempt_delete_bulk_confirm' => __("Warning: Deleting an attempt will remove the student's answers and score. This cannot be undone. Are you sure you want to delete the selected attempt(s)?", 'memberpress-courses'),
      ]);
    }
  }

  public function route() {
    $quiz_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $quiz = new models\Quiz($quiz_id);
    $table = new lib\AttemptsTable();
    $table->prepare_items();

    require_once base\VIEWS_PATH . '/admin/attempts/list.php';
  }

  /**
   * Add Screen Options to the Attempts list page
   *
   * @param \WP_Screen $screen
   */
  public function add_attempts_screen_options($screen) {
    if($screen instanceof \WP_Screen && preg_match('/_page_mpcs-quiz-attempts$/', $screen->id)) {
      add_screen_option('per_page', [
        'label' => esc_html__('Attempts per page', 'memberpress-courses'),
        'default' => 10,
        'option' => 'mpcs_attempts_per_page'
      ]);
    }
  }

  /**
   * Save the Screen Options on the Attempts list page
   *
   * @param  bool     $keep
   * @param  string   $option
   * @param  string   $value
   * @return int|bool
   */
  public function set_attempts_screen_options($keep, $option, $value) {
    if($option == 'mpcs_attempts_per_page' && is_numeric($value)) {
      return lib\Utils::clamp((int) $value, 1, 999);
    }

    return $keep;
  }

  /**
   * Handle the Ajax request to delete a quiz attempt
   */
  public function ajax_quiz_attempt_delete() {
    lib\Utils::validate_admin_ajax_post_request('mpcs_quiz_attempt_delete');

    if(!isset($_POST['id']) || !is_numeric($_POST['id'])) {
      wp_send_json_error(__('Bad request', 'memberpress-courses'));
    }

    $id = (int) $_POST['id'];
    $attempt = new models\Attempt($id);

    if($attempt->id > 0) {
      $attempt->destroy();

      wp_send_json_success();
    }

    wp_send_json_error(__('Attempt not found', 'memberpress-courses'));
  }

  /**
   * Handle the Ajax request to view a quiz attempt
   */
  public function ajax_quiz_attempt_view() {
    if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
      wp_send_json_error(__('Bad request', 'memberpress-courses'));
    }

    if(!lib\Utils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Insufficient permissions', 'memberpress-courses'));
    }

    $id = (int) $_GET['id'];
    $attempt = models\Attempt::get_one($id);

    if(!$attempt instanceof models\Attempt) {
      wp_send_json_error(__('Attempt not found', 'memberpress-courses'));
    }

    $quiz = $attempt->quiz();

    if(!$quiz instanceof models\Quiz) {
      wp_send_json_error(__('Quiz not found', 'memberpress-courses'));
    }

    $user = $attempt->user();

    if(!$user instanceof \WP_User) {
      wp_send_json_error(__('User not found', 'memberpress-courses'));
    }

    $show_results = apply_filters('mpcs_admin_attempt_show_results', true, $attempt, $quiz, $user);
    $show_answers = apply_filters('mpcs_admin_attempt_show_answers', true, $attempt, $quiz, $user);
    $questions = $quiz->get_questions();

    ob_start();

    require base\VIEWS_PATH . '/admin/attempts/view.php';

    wp_send_json_success(ob_get_clean());
  }
}
