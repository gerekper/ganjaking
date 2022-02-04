<?php
namespace memberpress\courses\controllers\admin;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses as base;
use memberpress\courses\lib as lib;
use memberpress\courses\models as models;
use memberpress\courses\helpers as helpers;
use memberpress\courses\controllers as controllers;

class Quizzes extends lib\BaseCptCtrl {
  public function load_hooks() {
    $this->ctaxes = ['course-tags', 'course-categories'];

    add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
    add_action('enqueue_block_editor_assets', [$this, 'enqueue_block_editor_assets']);
    add_action('wp_ajax_mpcs_delete_all_attempts', [$this, 'ajax_delete_all_attempts']);
    add_filter('manage_mpcs-quiz_posts_columns', [$this, 'add_course_column']);
    add_action('manage_mpcs-quiz_posts_custom_column', [$this, 'course_column_content'], 10, 2);
  }

  public function register_post_type() {
    $this->cpt = (object)array(
      'slug' => models\Quiz::$cpt,
      'config' => array(
        'labels' => array(
          'name' => __('Quizzes', 'memberpress-courses'),
          'singular_name' => __('Quiz', 'memberpress-courses'),
          'add_new_item' => __('Add New Quiz', 'memberpress-courses'),
          'edit_item' => __('Edit Quiz', 'memberpress-courses'),
          'new_item' => __('New Quiz', 'memberpress-courses'),
          'view_item' => __('View Quiz', 'memberpress-courses'),
          'search_items' => __('Search Quizzes', 'memberpress-courses'),
          'not_found' => __('No Quizzes found', 'memberpress-courses'),
          'not_found_in_trash' => __('No Quizzes found in Trash', 'memberpress-courses'),
          'parent_item_colon' => __('Parent Quiz:', 'memberpress-courses')
        ),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_in_menu' => false,
        'has_archive' => false,
        'capability_type' => 'page',
        'hierarchical' => false,
        'rewrite' => array('slug' => '/'.helpers\Courses::get_permalink_base().'/%course_slug%/' . models\Quiz::$permalink_slug, 'with_front' => false),
        'supports' => array('title','editor','thumbnail'),
        'taxonomies' => array(),
      )
    );

    if(!empty($this->ctaxes)) {
      $this->cpt->config['taxonomies'] = $this->ctaxes;
    }

    register_post_type( models\Quiz::$cpt, $this->cpt->config );
  }

  public function admin_enqueue_scripts() {
    global $current_screen, $post;

    if($post instanceof \WP_Post && $current_screen instanceof \WP_Screen && $current_screen->id == models\Quiz::$cpt) {
      $quiz = models\Quiz::find($post->ID);

      if($quiz instanceof models\Quiz) {
        $course = $quiz->course();

        if($course instanceof models\Course) {
          $data = helpers\Questions::questions_with_meta($quiz->ID);

          wp_enqueue_style('vex-css', base\CSS_URL . '/vendor/vex.css', [], base\VERSION);
          wp_enqueue_style('mpcs-quiz-editor', base\CSS_URL . '/admin_quiz_editor.css', [], base\VERSION);
          wp_enqueue_script('mpcs-course-editor-js', base\JS_URL . '/course-editor.js', ['jquery'], base\VERSION);
          wp_enqueue_script('vex-js', base\JS_URL . '/vendor/vex.combined.js', [], base\VERSION);
          wp_enqueue_script('mpcs-quiz-editor-js', base\JS_URL . '/quiz-editor.js', ['jquery', 'vex-js'], base\VERSION);

          wp_localize_script('mpcs-course-editor-js', 'MPCS_Course_Data', [
            'state' => [
              'questions' => (object) $this->get_questions($quiz),
              'sidebar' => [
                'questions' => $data['questions'],
                'searchMeta' => $data['searchMeta'],
              ],
            ],
            'imagesUrl' => base\IMAGES_URL,
            'coursesUrl' => get_edit_post_link($course->ID) . '#curriculum',
            'courseTitle' => $course->post_title,
            'api' => [
              'question' => controllers\CoursesApi::$namespace_str.'/'.controllers\CoursesApi::$resource_name_str.'/question/',
              'reserveId' => controllers\CoursesApi::$namespace_str.'/'.controllers\CoursesApi::$resource_name_str.'/reserveQuestionId/',
              'releaseQuestion' => controllers\CoursesApi::$namespace_str.'/'.controllers\CoursesApi::$resource_name_str.'/releaseQuestion/',
            ],
          ]);

          $quiz_editor_l10n = [
            'hasAttempts' => $quiz->has_attempts(),
            'quizLockedMessage' => $this->get_quiz_locked_dialog_html($quiz->ID),
            'confirmDeleteAllQuizAttempts' => __('Are you sure you want to delete all attempts for this quiz?', 'memberpress-courses'),
            'delete' => __('Delete', 'memberpress-courses'),
            'cancel' =>  __('Cancel', 'memberpress-courses'),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'quizId' => $post->ID,
            'deleteAllAttemptsNonce' => wp_create_nonce('mpcs_delete_all_attempts'),
            'courseUrl' => get_edit_post_link($course->ID, 'raw') . '#curriculum',
          ];

          wp_localize_script(
            'mpcs-quiz-editor-js',
            'MpcsQuizEditorL10n',
            ['l10n_print_after' => 'MpcsQuizEditorL10n = ' . wp_json_encode($quiz_editor_l10n)]
          );
        }
      }
    }
  }

  /**
   * Get the questions for the quiz editor default state
   *
   * @param models\Quiz $quiz
   * @return array
   */
  protected function get_questions($quiz) {
    $questions = [];

    foreach($quiz->get_questions() as $question) {
      $questions[$question->id] = helpers\Questions::prepare_question($question);
    }

    return $questions;
  }

  /**
   * Enqueue block editor only JavaScript and CSS.
   */
  public function enqueue_block_editor_assets() {
    global $current_screen, $post;

    if($post instanceof \WP_Post && $current_screen instanceof \WP_Screen && $current_screen->id == models\Quiz::$cpt) {
      $quiz = models\Quiz::find($post->ID);

      if($quiz instanceof models\Quiz) {
        $course = $quiz->course();

        if($course instanceof models\Course) {
          // enqueue development or production React code
          if(file_exists(base\JS_PATH . '/quiz_builder.js')) {
            wp_enqueue_script('mpcs-quiz-builder', base\JS_URL . '/quiz_builder.js', ['wp-element'], '0.1', true);
          }
          else {
            wp_enqueue_script('mpcs-quiz-builder', 'http://localhost:3000/assets/quiz_builder.js', ['wp-plugins', 'wp-element', 'wp-edit-post', 'wp-i18n', 'wp-api-request', 'wp-data', 'wp-hooks', 'wp-plugins', 'wp-components', 'wp-blocks', 'wp-editor', 'wp-compose'], '0.1', true);
          }
        }
      }
    }
  }

  /**
   * Get the HTML for the popup message shown when a quiz can't be edited
   *
   * @param int $quiz_id
   * @return string
   */
  public function get_quiz_locked_dialog_html($quiz_id) {
    ob_start();
    ?>
    <div class="mpcs-quiz-locked-content">
      <div class="mpcs-quiz-locked-title"><?php esc_html_e("You Can't Edit this Quiz", 'memberpress-courses'); ?></div>
      <p>
        <?php
          printf(
            /* translators: %1$s: open link tag to view attempts, %2$s: close link tag to view attempts */
            esc_html__('This quiz %1$salready has attempts%2$s recorded for it. Do you want to delete all attempts?', 'memberpress-courses'),
            sprintf(
              '<a href="%s" target="_blank">',
              esc_url(add_query_arg(['id' => $quiz_id], admin_url('admin.php?page=mpcs-quiz-attempts')))
            ),
            '</a>'
          );
        ?>
      </p>
      <p><strong><?php esc_html_e('Warning: This cannot be undone and students will lose their scores.', 'memberpress-courses'); ?></strong></p>
    </div>
    <?php
    return ob_get_clean();
  }

  /**
   * Handle the Ajax request to delete all quiz attempts
   */
  public function ajax_delete_all_attempts() {
    lib\Utils::validate_admin_ajax_post_request('mpcs_delete_all_attempts');

    if(!isset($_POST['quiz_id']) || !is_numeric($_POST['quiz_id'])) {
      wp_send_json_error(__('Bad request', 'memberpress-courses'));
    }

    $quiz_id = (int) $_POST['quiz_id'];

    $attempts = models\Attempt::get_all('', '', ['quiz_id' => $quiz_id]);

    if(is_array($attempts)) {
      foreach($attempts as $attempt) {
        $attempt->destroy();
      }
    }

    wp_send_json_success();
  }

  public static function add_course_column($columns) {
    $columns['course'] = __('Course', 'memberpress-courses');

    return $columns;
  }

  public static function course_column_content($column, $post_id) {
    if($column === 'course') {
      $quiz = models\Quiz::find($post_id);

      if($quiz instanceof models\Quiz) {
        $course = $quiz->course();

        if($course instanceof models\Course) {
          echo esc_html($course->post_title);
        }
      }
    }
  }
}
