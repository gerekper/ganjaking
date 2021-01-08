<?php
namespace memberpress\courses\controllers;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses as base;
use memberpress\courses\lib as lib;
use memberpress\courses\models as models;
use memberpress\courses\helpers as helpers;

class Lessons extends lib\BaseCtrl {
  public function load_hooks() {
    add_filter('the_content', array($this, 'prepend_breadcrumbs'));
    add_filter('the_content', array($this, 'append_lesson_navigation'));
    add_filter('template_include', array($this, 'override_template'));
    add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 99);
    add_action('wp_ajax_mpcs_record_lesson_progress', array($this, 'record_lesson_progress'));
    add_filter('embed_oembed_html', array($this, 'wrap_oembed_html'), 99, 2);
    add_action('save_post', array($this, 'delete_transients'), 10, 2 );
  }

  /**
  * Prepend the section breadcrumb navigation to the content
  * @see load_hooks(), add_filter('the_content')
  * @param string $content The post content
  * @return string $content The modified post content
  */
  public static function prepend_breadcrumbs($content) {
    global $post;

    if(is_single() && ! helpers\App::is_classroom()) {
      if(isset($post) && is_a($post, 'WP_Post') && $post->post_type === models\Lesson::$cpt) {
        $current_lesson = new models\Lesson($post->ID);
        $current_section = $current_lesson->section();
        if($current_section !== false) {
          $current_course = $current_section->course();
          $options = \get_option('mpcs-options');
          \ob_start();
            require(base\VIEWS_PATH . '/lessons/breadcrumbs.php');
          $breadcumbs = \ob_get_clean();

          $content = $breadcumbs . $content;
        }
      }
    }

    return $content;
  }

  /**
  * Append the course navigation to the content
  * @see load_hooks(), add_filter('the_content')
  * @param string $content The post content
  * @return string $content The modified post content
  */
  public static function append_lesson_navigation($content) {
    global $post;

    if(is_single() && ! helpers\App::is_classroom()) {
      if(isset($post) && is_a($post, 'WP_Post') && $post->post_type === models\Lesson::$cpt) {
        $current_user = lib\Utils::get_currentuserinfo();
        $current_lesson = new models\Lesson($post->ID);
        $lesson_nav_ids = $current_lesson->nav_ids();
        $current_lesson_index = \array_search($current_lesson->ID, $lesson_nav_ids);
        $current_section = $current_lesson->section();
        if($current_section !== false) {
          if(!helpers\Lessons::has_next_lesson($current_lesson_index, $lesson_nav_ids)) {
            $current_course = $current_section->course();
            $sections = $current_course->sections();
            $section_ids = \array_map(function($section) {
              return $section->id;
            }, $sections);
            $current_section_index = \array_search($current_section->id, $section_ids);
          }

          $options = \get_option('mpcs-options');

          \ob_start();
            require(base\VIEWS_PATH . '/lessons/navigation.php');
          $nav_links = \ob_get_clean();

          $content .= $nav_links;
        }
      }
    }

    return $content;
  }

  /**
  * Override default template with the courses page template
  * @param string $template current template
  * @return string $template modified template
  */
  public static function override_template($template) {
    global $post;

    if(is_single()) {
      if(isset($post) && is_a($post, 'WP_Post') && $post->post_type == models\Lesson::$cpt) {
        $lesson = new models\Lesson($post->ID);
        $section = $lesson->section();
        if($section !== false) {
          $course = $section->course();
          $new_template = locate_template($course->page_template);
        }
        if( helpers\App::is_classroom()){
          $template = base\VIEWS_PATH . '/classroom/single-lesson.php';
        }
        elseif(isset($new_template) && !empty($new_template)) {
          return $new_template;
        }
        else {
          $template = locate_template(
            array(
              'single-mpcs-lesson.php',
              'single-mpcs-course.php',
              'page.php',
              'custom_template.php',
              'single.php',
              'index.php'
            )
          );
        }
      }
    }

    return $template;
  }

  /**
  * Enqueue scripts for lessons controller
  * @see load_hooks(), add_action('wp_enqueue_scripts')
  */
  public static function enqueue_scripts() {
    global $post, $wp_styles;

    if( is_a($post, 'WP_Post') && is_single() && $post->post_type === models\Lesson::$cpt) {
      $locals = array(
        'ajaxurl' => \admin_url('admin-ajax.php'),
        'progress_nonce' => \wp_create_nonce('lesson_progress'),
      );


      if ( !helpers\App::is_classroom() ) {
        \wp_enqueue_style('mpcs-fontello-styles', base\FONTS_URL.'/fontello/css/mp-courses.css', array(), base\VERSION);
        \wp_enqueue_style('mpcs-lesson-css', base\CSS_URL . '/lesson.css', array(), base\VERSION);
        \wp_enqueue_script('mpcs-lesson', base\JS_URL . '/lesson.js', array('jquery'), base\VERSION);
        \wp_localize_script('mpcs-lesson', 'locals', $locals);
        return;
      }

      // Remove styles
      foreach( $wp_styles->queue as $style ) :
        $handle = $wp_styles->registered[$style]->handle;
        // Keep base block styles
        if ( 'wp-block-library' !== $style ) {
          \wp_deregister_style($handle);
          \wp_dequeue_style($handle);
        }
      endforeach;

      \wp_enqueue_style('mpcs-fontello-styles', base\FONTS_URL.'/fontello/css/mp-courses.css', array(), base\VERSION);
      \wp_enqueue_style('mpcs-lesson-css', base\CSS_URL . '/lesson.css', array(), base\VERSION);
      \wp_enqueue_script('mpcs-lesson', base\JS_URL . '/lesson.js', array('jquery'), base\VERSION);
      // Make ajaxurl available to JS
      \wp_localize_script('mpcs-lesson', 'locals', $locals);

      \wp_enqueue_style('mpcs-classroom', base\CSS_URL . '/classroom.css', array(), base\VERSION);
      \wp_enqueue_script('mpcs-classroom-js', base\JS_URL . '/classroom.js', array('jquery'), base\VERSION, true);
    }
  }

  /**
  * Record user_progress record for lesson
  * @see load_hooks(), add_action('wp_ajax_mpcs_record_lesson_progress')
  * @return void
  */
  public static function record_lesson_progress() {
    lib\Utils::check_ajax_referer('lesson_progress', 'progress_nonce');
    $current_user = lib\Utils::get_currentuserinfo();

    if(!is_user_logged_in()) {
      lib\Utils::exit_with_status(403, json_encode(array('error' => __('Forbidden', 'memberpress-courses'))));
    }

    try {
      lib\Validate::not_null($_POST['lesson_id'], 'lesson_id');
      lib\Validate::is_numeric($_POST['lesson_id'], 1, null, 'lesson_id');

      $lesson = new models\Lesson($_POST['lesson_id']);

      lib\Validate::is_numeric($lesson->ID, 1, null, 'lesson_id');
    }
    catch(lib\ValidationException $e) {
      lib\Utils::exit_with_status(403, json_encode(array('error' => $e->getErrorMessage())));
    }

    $course = $lesson->course();
    $section = $lesson->section();

    $user_progress = models\UserProgress::find_one_by_user_and_lesson($current_user->ID, $lesson->ID);

    // TODO: In the future we may want to update the percent of progress for
    // the record if it's found and not necessarily fail here
    if(!empty($user_progress) && !empty($user_progress->id)) {
      lib\Utils::exit_with_status(403, json_encode(array('error' => __('This lesson has already been completed', 'memberpress-courses'))));
    }

    $has_started_course = models\UserProgress::has_started_course($current_user->ID, $course->ID);
    $has_started_section = models\UserProgress::has_started_section($current_user->ID, $section->id);

    $user_progress = new models\UserProgress();
    $user_progress->lesson_id    = $lesson->ID;
    $user_progress->course_id    = $course->ID;
    $user_progress->user_id      = $current_user->ID;
    $user_progress->created_at   = lib\Utils::ts_to_mysql_date(time());
    $user_progress->completed_at = lib\Utils::ts_to_mysql_date(time());
    $user_progress->store();

    \do_action(base\SLUG_KEY . 'completed_lesson', $user_progress);

    if(false == $has_started_course){
      \do_action(base\SLUG_KEY . 'started_course', $user_progress);
    }

    if(false == $has_started_section){
      \do_action(base\SLUG_KEY . 'started_section', $user_progress, $section->id);
    }

    if(models\UserProgress::has_completed_course($current_user->ID, $course->ID)){
      \do_action(base\SLUG_KEY . 'completed_course', $user_progress);
    }

    if(models\UserProgress::has_completed_section($current_user->ID, $section->id)){
      \do_action(base\SLUG_KEY . 'completed_course', $user_progress);
    }

    lib\Utils::exit_with_status(200, json_encode(array('message' => __('Progress was recorded for this User and Lesson', 'memberpress-courses'))));
  }

  /**
   * Display classroom navigation
   *
   * @param  mixed $post
   * @return void
   */
  public static function display_classroom_navigation($post){
    $current_user = lib\Utils::get_currentuserinfo();
    $current_lesson = new models\Lesson($post->ID);
    $lesson_nav_ids = $current_lesson->nav_ids();
    $current_lesson_index = \array_search($current_lesson->ID, $lesson_nav_ids);
    $current_section = $current_lesson->section();

    if($current_section == false) {
      return;
    }

    if(!helpers\Lessons::has_next_lesson($current_lesson_index, $lesson_nav_ids)) {
      $current_course = $current_section->course();
      $sections = $current_course->sections();
      $section_ids = \array_map(function($section) {
        return $section->id;
      }, $sections);
      $current_section_index = \array_search($current_section->id, $section_ids);
    }

    $options = \get_option('mpcs-options');

    \ob_start();
      require(base\VIEWS_PATH . '/lessons/classroom/navigation.php');
    $nav_links = \ob_get_clean();

    return $nav_links;
  }

  /**
   * Add html wrapper to oembed_html
   *
   * @param  string $html
   * @param  string $url
   * @return string
   */
  public function wrap_oembed_html($html, $url) {

    if( !helpers\App::is_classroom() )
      return $html;

    $providers = array('vimeo.com', 'youtube.com', 'youtu.be', 'wistia.com', 'wistia.net');
    $found = array_filter($providers, function($provider) use ($url){
      return false !== strpos( $url, $provider);
    });

    if ( $found ) {
      $html = '<div class="responsive-video">' . $html . '</div>';
    }
    return $html;
  }

  /**
   * Delete Transients
   *
   * @param  mixed $new_status
   * @param  mixed $old_status
   * @param  mixed $post
   * @return void
   */
  function delete_transients( $post_id, $post ){
    if ( models\Lesson::$cpt !== $post->post_type )
      return; // restrict the filter to a specific post type

    // let's get and delete transients
    $transients = \get_option('mpcs-transients', array());
    foreach ($transients as $transient) {
      delete_transient( $transient );
    }
    \delete_option('mpcs-transients');
  }


}
