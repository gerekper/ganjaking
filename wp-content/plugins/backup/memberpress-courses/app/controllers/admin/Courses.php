<?php
namespace memberpress\courses\controllers\admin;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses as base;
use memberpress\courses\lib as lib;
use memberpress\courses\models as models;
use memberpress\courses\helpers as helpers;

class Courses extends lib\BaseCptCtrl {
  public function load_hooks() {
    add_filter('manage_'.models\Course::$cpt.'_posts_columns', array($this, 'set_courses_columns'), 1);
    add_action('manage_'.models\Course::$cpt.'_posts_custom_column', array($this, 'courses_columns'), 10, 2 );
    add_filter( 'default_hidden_columns', array($this, 'hide_courses_columns'), 10, 2 );
    add_action( 'admin_footer-edit.php', array( $this, 'categories_tags_buttons' ) );
    add_action( 'admin_footer-edit-tags.php', array( $this, 'categories_tags_return_to_courses_button' ) );
    add_action('save_post', array($this, 'save_post_data'));
    add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets') );
    add_filter('user_contactmethods', array($this, 'user_social_links'));
    add_action('wp_ajax_mpcs_reset_course_progress', array($this, 'reset_course_progress'));
    add_filter('mepr-list-table-joins', array($this, 'members_table_joins'));
    $this->ctaxes = array();
  }

  public function categories_tags_return_to_courses_button() {

    if ( empty( $_GET['post_type'] ) || models\Course::$cpt !== $_GET['post_type'] ) {
      return;
    }

    if ( empty( $_GET['taxonomy'] ) || ! in_array( $_GET['taxonomy'], array( 'mpcs-course-tags', 'mpcs-course-categories' ) ) ) {
      return;
    }

    $new_links = sprintf( '<a href="%2$s" class="" style="display: block; margin-top: 10px; text-decoration: none;">%1$s</a>', esc_html__( '&larr; Back to Courses', 'memberpress-courses' ), add_query_arg( array(
      'post_type' => models\Course::$cpt
    ), admin_url( 'edit.php' ) ) );
    ?>
    <script>
      jQuery(document).ready(function($) {
        $('.wrap .wp-header-end').before("<?php echo addslashes( $new_links ); ?>");
      });
    </script>
    <?php
  }

  public function categories_tags_buttons() {
    if ( empty( $_GET['post_type'] ) || models\Course::$cpt !== $_GET['post_type'] ) {
      return;
    }
    $new_links = sprintf( '<a href="%2$s" class="page-title-action" style="margin-left: 0;">%1$s</a>', esc_html__( 'Categories', 'memberpress-courses' ), add_query_arg( array(
      'taxonomy' => 'mpcs-course-categories',
      'post_type' => models\Course::$cpt
    ), admin_url( 'edit-tags.php' ) ) );
    $new_links .= sprintf( '<a href="%2$s" class="page-title-action">%1$s</a>', esc_html__( 'Tags', 'memberpress-courses' ), add_query_arg( array(
      'taxonomy' => 'mpcs-course-tags',
      'post_type' => models\Course::$cpt
    ), admin_url( 'edit-tags.php' ) ) );
    ?>
    <script>
      jQuery(document).ready(function($) {
        $('.wrap .wp-header-end').before("<?php echo addslashes( $new_links ); ?>");
      });
    </script>
    <?php
  }


  /**
   * Add columns to Courses CPT
   *
   * @return void
   */
  public function set_courses_columns($default_cols){

    $columns = array();
    foreach($default_cols as $key=>$value) {
      if($key=='date') {  // when we find the date column
        $columns['mpcs-participants']     = __('Participants', 'memberpress-courses');
        $columns['mpcs-completed']        = __('Completed', 'memberpress-courses');
        $columns['mpcs-completion-rate']  = __('Completion Rate', 'memberpress-courses');
     }
     $columns[$key]=$value;
    }

    return $columns;
  }

  /**
   * Hide courses columns by default
   *
   * @param  mixed $hidden
   * @param  mixed $screen
   * @return void
   */
  public function hide_courses_columns($hidden, $screen){
    $hidden[] = 'mpcs-completed';
    $hidden[] = 'mpcs-completion-rate';
    return $hidden;
  }

  /**
   * Courses Columns
   *
   * @param  mixed $column
   * @param  mixed $post_id
   * @return void
   */
  public function courses_columns($column, $post_id){
    switch ( $column ) {
      case 'mpcs-participants' :
        $participants = (array) models\UserProgress::find_all_course_participants($post_id);
        $members_url = admin_url("admin.php?page=memberpress-members&course={$post_id}");
        if(count( $participants ) > 0){
          printf('<a href="%s">%d</a>', esc_url($members_url) , count( $participants ) );
        }else{
          echo count( $participants );
        }
        break;

      case 'mpcs-completed' :
        $completers = models\UserProgress::find_course_completers($post_id);
        $members_url = admin_url("admin.php?page=memberpress-members&course={$post_id}");

        echo count( $completers );
        break;

      case 'mpcs-completion-rate' :
        $members_url = admin_url("admin.php?page=memberpress-members&course={$post_id}");
        $completion_rate  = models\UserProgress::completion_rate($post_id);
        echo $completion_rate . '%';
        break;
    }
  }


  public static function save_post_data($post_id) {
    # Verify nonce
    if(!\wp_verify_nonce(isset($_POST[models\Course::$nonce_str]) ? $_POST[models\Course::$nonce_str] : '', models\Course::$nonce_str . \wp_salt())) {
      return $post_id;
    }

    # Skip ajax
    if(defined('DOING_AJAX') || defined('DOING_LESSON_SAVE')) {
      return;
    }

    $course = new models\Course($post_id);
    $course->page_template = isset($_POST[models\Course::$page_template_str]) ? sanitize_text_field($_POST[models\Course::$page_template_str]) : $course->attrs['page_template']['default'];
    $course->status = isset($_POST[models\Course::$page_status_str]) ? $_POST[models\Course::$page_status_str] : $course->attrs['status']['default'];
    $course->menu_order = (isset($_POST['menu_order']) && is_numeric($_POST['menu_order'])) ? $_POST['menu_order'] : $course->attrs['menu_order']['default'];
    $course->sales_url = (isset($_POST[models\Course::$sales_url_str])) ? esc_url($_POST[models\Course::$sales_url_str]) : $course->attrs[models\Course::$sales_url_str]['default'];

    $course->validate();
    $course->store_meta();
    $curriculum = json_decode(stripslashes($_POST['mpcs-curriculum']), TRUE);

    $course->remove_sections($curriculum['sections']);

    // $section_order = 0;
    # Create or update sections and lessons that were added or reordered in the UI
    foreach($curriculum['sections'] as $uuid => $section_data) {
      //Skip hidden section element
      if($uuid === '{uuid}') { continue; }

      $section = new models\Section($section_data['id']);
      $section->title         = sanitize_text_field(stripslashes($section_data['title']));
      $section->description   = '';
      // $section->description   = sanitize_text_field(stripslashes($section_data['description']));
      $section->course_id     = $course->ID;
      $section->section_order = array_search ($uuid, $curriculum['sectionOrder']);;
      $section->uuid          = $uuid;
      //FIXME: fix validation
      $section_id = $section->store();
      $section->remove_unassigned_lessons($section_data['lessonIds']);
      foreach($section_data['lessonIds'] as $index => $lessonId) {
        $lesson = $curriculum['lessons']['section'][$lessonId];
        $lesson_id = sanitize_text_field(stripslashes($lesson['id']));
        $lesson_title = sanitize_text_field(stripslashes($lesson['title']));
        $lesson = new models\Lesson(array(
          'ID'           => $lesson_id,
          'post_title'   => $lesson_title,
          'section_id'   => $section_id,
          'lesson_order' => $index,
        ));
        if (!defined('DOING_LESSON_SAVE')) define('DOING_LESSON_SAVE', true);
        $lesson->store();
      }

    }
  }


  /**
   * Modify Members Query Joins to filter members by courses
   *
   * @param  mixed $joins
   * @param  mixed $params
   * @return void
   */
  public function members_table_joins($joins){
    $params = $_GET;
    if(isset($params['page']) && 'memberpress-members' != $params['page']){
      return $joins;
    }

    if(isset($params['course']) && !empty($params['course']) && is_numeric($params['course'])) {
      global $wpdb;
      $db = lib\Db::fetch();

      $joins[] =  $wpdb->prepare("/* IMPORTANT */ INNER JOIN (
        SELECT user_id, course_id
        FROM   {$db->user_progress}
        GROUP  BY user_id, course_id
        ) AS user_progress ON user_progress.course_id=%d AND user_progress.user_id=m.user_id", $params['course']);
    }
    return $joins;
  }

  public function admin_enqueue_scripts() {
    global $current_screen;
    global $post;
    if($current_screen->post_type === models\Course::$cpt && isset($post->ID)) {
      \wp_enqueue_style('vex-css', base\CSS_URL . '/vendor/vex.css', array(), base\VERSION);
      \wp_dequeue_script('autosave'); //Disable auto-saving
      \wp_enqueue_script('vex-js', base\JS_URL . '/vendor/vex.combined.js', array(), base\VERSION);
      \wp_enqueue_script('mpcs-course-editor-js', base\JS_URL . '/course-editor.js', array('jquery'), base\VERSION);
      \wp_enqueue_script('mpcs-courses-js', base\JS_URL . '/admin-courses.js', array('mpcs-course-editor-js', 'vex-js'), base\VERSION);
      \wp_localize_script('mpcs-courses-js', 'MPCS_Course_Data', array(
        'curriculum' => helpers\Courses::course_curriculum($post->ID),
        'coursesUrl' => admin_url('edit.php?post_type='.models\Course::$cpt),
        'posts_url' => admin_url('post.php'),
        'settings' => helpers\Courses::course_settings($post->ID),
        'imagesUrl' => base\IMAGES_URL )
      );
    }
  }


  /**
   * Enqueue block editor only JavaScript and CSS.
   */
  public function enqueue_block_editor_assets() {
    global $current_screen;

    if($current_screen->post_type === models\Course::$cpt) {
      // enqueue development or production React code
      if(file_exists(base\JS_PATH . "/builder.js")) {
        wp_enqueue_script( 'mpcs-builder', base\JS_URL . '/builder.js', ['wp-element'], '0.1', true );
      } else {
        wp_enqueue_script( 'mpcs-builder', 'http://localhost:3000/assets/main.js', ['wp-plugins', 'wp-element', 'wp-edit-post', 'wp-i18n', 'wp-api-request', 'wp-data', 'wp-hooks', 'wp-plugins', 'wp-components', 'wp-blocks', 'wp-editor', 'wp-compose'], '0.1', true );
      }
    }
  }

  public function lesson_links() {
    $lessons = models\Lesson::find_all();
    $lesson_links = array();

    foreach($lessons as $lesson) {
      $lesson_links[$lesson->ID] = array(
        'view' => get_permalink($lesson->ID),
        'edit' => admin_url("post.php?post={$lesson->ID}&action=edit")
      );
    }

    return $lesson_links;
  }

  public function register_post_type() {
    $this->cpt = (object)array(
      'slug' => models\Course::$cpt,
      'config' => array(
        'labels' => array(
          'name' => __('Courses', 'memberpress-courses'),
          'singular_name' => __('Course', 'memberpress-courses'),
          'add_new_item' => __('Add New Course', 'memberpress-courses'),
          'edit_item' => __('Edit Course', 'memberpress-courses'),
          'new_item' => __('New Course', 'memberpress-courses'),
          'view_item' => __('View Course', 'memberpress-courses'),
          'search_items' => __('Search Courses', 'memberpress-courses'),
          'not_found' => __('No Courses found', 'memberpress-courses'),
          'not_found_in_trash' => __('No Courses found in Trash', 'memberpress-courses'),
          'parent_item_colon' => __('Parent Course:', 'memberpress-courses')
        ),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'query_var' => 'course',
        'show_in_menu' => base\PLUGIN_NAME,
        'has_archive' => true,
        'capability_type' => 'page',
        'hierarchical' => false,
        'register_meta_box_cb' => array( $this, 'add_meta_boxes' ),
        'rewrite' => array('slug' => helpers\Courses::get_permalink_base(), 'with_front' => false),
        'supports' => array('title', 'excerpt', 'editor', 'thumbnail', 'author'),
        'taxonomies' => array()
      )
    );

    if(!empty($this->ctaxes)) {
      $this->cpt->config['taxonomies'] = $this->ctaxes;
    }

    register_post_type( models\Course::$cpt, $this->cpt->config );
  }

  public function add_meta_boxes() {
    add_meta_box(models\Course::$cpt . '-builder', __("Curriculum Builder", 'memberpress-courses'), array($this, 'curriculum_meta_box'), models\Course::$cpt, "normal", "high");
    add_meta_box(models\Course::$cpt . '-settings', __("Course Setting", 'memberpress-courses'), array($this, 'course_settings_meta_box'), models\Course::$cpt, "normal", "high");
    // add_meta_box(models\Course::$cpt . '-meta', __("Course Options", 'memberpress-courses'), array($this, 'options_meta_box'), models\Course::$cpt, "normal", "high");
    // add_meta_box(models\Lesson::$cpt . '-meta', __("Course Builder", 'memberpress-courses'), array($this, 'lessons_meta_box'), models\Course::$cpt, "normal", "high");
    add_meta_box(models\Course::$cpt . "-custom-template", __('Page Options', 'memberpress-courses'), array($this, 'page_options_meta_box'), models\Course::$cpt, "side", "default");
  }

  public function curriculum_meta_box($post) {
    $course = new models\Course($post->ID);
    require_once(base\VIEWS_PATH . '/admin/courses/curriculum_meta_box.php');
  }

  public function course_settings_meta_box($post) {
    $course = new models\Course($post->ID);
    require_once(base\VIEWS_PATH . '/admin/courses/course_settings_meta_box.php');
  }


  public function options_meta_box($post) {
    $course = new models\Course($post->ID);
    require_once(base\VIEWS_PATH . '/admin/courses/options_meta_box.php');
  }

  public function lessons_meta_box($post) {
    $course = new models\Course($post->ID);
    require_once(base\VIEWS_PATH . '/admin/courses/lessons_meta_box.php');
  }

  public function page_options_meta_box($post) {
    $course = new models\Course($post->ID);
    $templates = get_page_templates();
  $course = new models\Course($post->ID);
    require_once(base\VIEWS_PATH . '/admin/courses/page_options_meta_box.php');
  }

  private static function add_or_reorder_lesson($section_id, $lesson_id, $index) {
    $section_lesson = models\Lesson::get_one(array(
      'wheres' => array(
        'ID' => $lesson_id,
        'section_id' => $section_id,
      )
    ));

    if($section_lesson !== false) {
      if($section_lesson->lesson_order != $index) {
        $section_lesson->update_order($index);
      }
    }
    else {
      $lesson = new models\Lesson($lesson_id);
      $lesson->add_to_section($section_id, $index);
    }
  }

  /**
   * Adds social links to user profile
   *
   * @param $user_contact
   * @return mixed
   */
  function user_social_links( $user_contact ) {

    /* Add user contact methods */
    $user_contact['facebook']   = __('Facebook URL', 'memberpress-courses');
    $user_contact['twitter']    = __('Twitter URL', 'memberpress-courses');
    $user_contact['Instagram']  = __('Instagram URL', 'memberpress-courses');
    $user_contact['youtube']    = __('Youtube URL', 'memberpress-courses');

    return $user_contact;
  }

  public function reset_course_progress(){
    lib\Utils::check_ajax_referer('reset_progress', 'nonce');

    try {
      lib\Validate::not_null($_POST['course_id'], 'Course ID');
      lib\Validate::is_numeric($_POST['user_id'], 1, null, 'User ID');

      $course = new models\Course($_POST['course_id']);
      $user_id = $_POST['user_id'];

      lib\Validate::is_numeric($course->ID, 1, null, 'Course ID');
    }
    catch(lib\ValidationException $e) {
      lib\Utils::exit_with_status(403, json_encode(array('error' => $e->getErrorMessage())));
    }

    // Only Admins can delete other user's progress
    if( $user_id != get_current_user_id() && false == lib\Utils::is_user_admin() ) {
      lib\Utils::exit_with_status(403, json_encode(array('error' => __('You are not allowed to delete user\'s progress', 'memberpress-courses'))));
    }

    $user_progresses = (array) models\UserProgress::find_all_by_user_and_course($user_id, $course->ID);

    foreach ($user_progresses as $user_progress) {
      $user_progress->destroy();
    }

    lib\Utils::exit_with_status(200, json_encode(array('message' => __('Progress was deleted for this User and Course', 'memberpress-courses'))));
  }

}
