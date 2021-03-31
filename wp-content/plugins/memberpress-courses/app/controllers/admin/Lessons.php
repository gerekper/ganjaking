<?php
namespace memberpress\courses\controllers\admin;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses as base;
use memberpress\courses\lib as lib;
use memberpress\courses\models as models;
use memberpress\courses\helpers as helpers;

class Lessons extends lib\BaseCptCtrl {
  public function load_hooks() {
    add_action('admin_action_duplicate_post', array($this, 'duplicate_post'));
    add_action('manage_mpcs-lesson_posts_custom_column', array($this, 'custom_column_content'), 10, 2);
    add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets') );
    add_filter('post_row_actions', array($this, 'duplicate_post_link'), 10, 2);
    add_filter('manage_mpcs-lesson_posts_columns', array($this, 'alter_columns'));
    add_filter('manage_edit-mpcs-lesson_sortable_columns', array($this, 'alter_sortable_columns'));
    $this->ctaxes = array('course-tags', 'course-categories');
  }

  public function register_post_type() {
    $this->cpt = (object)array(
      'slug' => models\Lesson::$cpt,
      'config' => array(
        'labels' => array(
          'name' => __('Lessons', 'memberpress-courses'),
          'singular_name' => __('Lesson', 'memberpress-courses'),
          'add_new_item' => __('Add New Lesson', 'memberpress-courses'),
          'edit_item' => __('Edit Lesson', 'memberpress-courses'),
          'new_item' => __('New Lesson', 'memberpress-courses'),
          'view_item' => __('View Lesson', 'memberpress-courses'),
          'search_items' => __('Search Lessons', 'memberpress-courses'),
          'not_found' => __('No Lessons found', 'memberpress-courses'),
          'not_found_in_trash' => __('No Lessons found in Trash', 'memberpress-courses'),
          'parent_item_colon' => __('Parent Lesson:', 'memberpress-courses')
        ),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_in_menu' => false,
        'has_archive' => false,
        'capability_type' => 'page',
        'hierarchical' => false,
        'register_meta_box_cb' => function () {
          $this->add_meta_boxes();
        },
        'rewrite' => array('slug' => '/'.helpers\Courses::get_permalink_base().'/%course_slug%/' . models\Lesson::$permalink_slug, 'with_front' => false),
        'supports' => array('title','editor','thumbnail'),
        'taxonomies' => array(),
      )
    );

    if(!empty($this->ctaxes)) {
      $this->cpt->config['taxonomies'] = $this->ctaxes;
    }

    register_post_type( models\Lesson::$cpt, $this->cpt->config );
  }

  /**
  * Save meta data on save_post
  * @see load_hooks(), add_action('save_post')
  * @param integer $post_id current post
  * @return mixed (id|false) postmeta id or false
  */
  public static function save_post_data($post_id) {
    # Verify nonce
    if(!\wp_verify_nonce(isset($_POST[models\Lesson::$nonce_str]) ? $_POST[models\Lesson::$nonce_str] : '', models\Lesson::$nonce_str . \wp_salt())) {
      return $post_id;
    }
    # Skip ajax
    if(defined('DOING_AJAX')) {
      return;
    }

    $lesson = new models\Lesson($post_id);

    $lesson->store_meta();
  }

  public static function duplicate_post_link($actions, $post) {
    global $current_screen;

    if(isset($current_screen->post_type) && $current_screen->post_type === models\Lesson::$cpt) {
      if(current_user_can('edit_posts')) {
        $actions['duplicate'] = '<a href="' . \wp_nonce_url('admin.php?action=duplicate_post&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce' ) . '">' . __('Duplicate', 'memberpress-courses') . '</a>';
      }
    }
    return $actions;
  }

  public static function duplicate_post() {
    if (!isset($_REQUEST['post'])  || (isset($_REQUEST['action']) && $_REQUEST['action'] !== 'duplicate_post')) {
      die();
    }
    if (!isset($_REQUEST['duplicate_nonce']) || !\wp_verify_nonce($_GET['duplicate_nonce'], basename( __FILE__ ))) {
      return;
    }

    $lesson = new models\Lesson($_REQUEST['post']);
    $cloned_post_id = $lesson->cloneit();

    \wp_redirect(admin_url('post.php?action=edit&post=' . $cloned_post_id));
  }

  public static function custom_column_content($column, $post_id) {
    if($column === 'course') {
      $lesson = new models\Lesson($post_id);
      $course = $lesson->course();
      echo empty($course) ? '' : $course->post_title;
    }
  }


  public function admin_enqueue_scripts() {
    global $current_screen;
    global $post;
    if($current_screen->post_type === models\Lesson::$cpt && isset($_GET['post'])) {
      $lesson = new models\Lesson($post->ID);
      $course = $lesson->course();

      \wp_enqueue_style('mpcs-lesson-admin', base\CSS_URL . '/lesson_admin.css', array(), base\VERSION);

      if(isset($course->ID) && absint($course->ID > 0)){
        \wp_enqueue_script('mpcs-course-editor-js', base\JS_URL . '/course-editor.js', array('jquery'), base\VERSION);
        \wp_localize_script('mpcs-course-editor-js', 'MPCS_Course_Data', array(
          'curriculum' => '',
          'imagesUrl' => base\IMAGES_URL,
          'coursesUrl' => get_edit_post_link($course->ID) . '#curriculum',
          'courseTitle' => $course->post_title
        ) );
      }

    }
  }


  /**
   * Enqueue block editor only JavaScript and CSS.
   */
  public function enqueue_block_editor_assets() {
    global $current_screen;

    if($current_screen->post_type === models\Lesson::$cpt) {
      // enqueue development or production React code
      if(file_exists(base\JS_PATH . "/builder.js")) {
        wp_enqueue_script( 'mpcs-builder', base\JS_URL . '/builder.js', ['wp-element'], '0.1', true );
      } else {
        wp_enqueue_script( 'mpcs-builder', 'http://localhost:3000/assets/main.js', ['wp-plugins', 'wp-element', 'wp-edit-post', 'wp-i18n', 'wp-api-request', 'wp-data', 'wp-hooks', 'wp-plugins', 'wp-components', 'wp-blocks', 'wp-editor', 'wp-compose'], '0.1', true );
      }
    }
  }


  public static function alter_columns($columns) {
    $columns['course'] = __('Course', 'memberpress-courses');

    return $columns;
  }

  public static function alter_sortable_columns($columns) {
    $columns['course'] = models\Course::$cpt;

    return $columns;
  }

  public function add_meta_boxes() {
    // add_meta_box(models\Lesson::$cpt . '-meta', __("Lesson Options", 'memberpress-courses'), array($this, 'lesson_meta_box'), models\Lesson::$cpt, "normal", "high");
  }

  public function lesson_meta_box() {
    require_once(base\VIEWS_PATH . '/admin/lessons/courses_meta_box.php');
  }
}
