<?php
namespace memberpress\courses\models;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses\lib as lib;
use memberpress\courses\models as models;

class Course extends lib\BaseCptModel {
  public static $cpt = 'mpcs-course';
  public static $nonce_str = 'mpcs-course-nonce';
  public static $page_template_str = 'mpcs-course-page-template';
  public static $page_status_str = 'mpcs-course-page-status';
  public static $sales_url_str = 'mpcs-sales-url';
  public static $permalink_slug = 'courses';
  public $statuses;

  public function __construct($obj = null) {
    parent::__construct($obj);
    $this->load_cpt(
      $obj,
      self::$cpt,
      array(
        'status'        => array('default' => 'enabled', 'type' => 'string'),
        'page_template' => array('default' => null, 'type' => 'string'),
        'menu_order'    => array('default' => 0, 'type' => 'int'),
        'sales_url'    => array('default' => "", 'type' => 'string'),
      )
    );

    $this->statuses = array(
      'enabled',
      'disabled'
    );
  }

  public function validate() {
    lib\Validate::is_in_array($this->status, $this->statuses, 'status');
  }

  public function sanitize() {
    // $this->first_name = sanitize_text_field($this->first_name);
  }

  /**
  * Find all sections for course
  * @return array[Section]
  */
  public function sections() {
    return models\Section::find_all_by_course($this->ID);
  }

  /**
  * Find all lessons for course ordered by section then by lesson
  * @return array[Lesson]
  */
  public function lessons($type='objects') {
    $lessons = array();
    $sections = models\Section::find_all_by_course($this->ID);

    foreach($sections as $section) {
      $lessons = array_merge(
        $lessons,
        models\Lesson::find_all_by_section($section->id)
      );
    }

    if($type=='ids') {
      return array_map( function($lesson) {
          return $lesson->ID;
        },
        $lessons
      );
    }
    else {
      return $lessons;
    }
  }

  /**
  * Find memberships containing course
  * @return array[MeprProduct]
  */
  public function memberships() {
    $memberships = array();
    $course_post = get_post($this->ID);
    $access_list = \MeprRule::get_access_list($course_post);

    foreach ($access_list['membership'] as $membership_id) {
      $memberships[] = new \MeprProduct($membership_id);
    }

    return $memberships;
  }

  /**
  * Get the number of lessons for this course
  * @return int Query count result
  */
  public function number_of_lessons() {
    global $wpdb;
    $db = new lib\Db;
    $section_id_str = models\Lesson::$section_id_str;

    $wheres = array('post_type' => models\Lesson::$cpt, 'post_status' => 'publish', "{$db->sections}.course_id" => $this->ID);
    $joins = array();
    $joins[] = "JOIN {$wpdb->postmeta} AS pm ON pm.post_id = {$wpdb->posts}.ID AND pm.meta_key = '{$section_id_str}'";
    $joins[] = "JOIN {$db->sections} ON {$db->sections}.id = pm.meta_value";

    $count =  $db->get_count($wpdb->posts, $wheres, $joins);

    return (int) $count;
  }

  /**
  * Return the user's progress for this course
  * @param int $user_id User to check progress against
  * @return int Percentage of completed / total lessons for this course
  */
  public function user_progress($user_id) {
    global $wpdb;

    if(UserProgress::has_completed_course($user_id, $this->ID)) {
      return 100.00;
    }

    $total_lessons = $this->number_of_lessons();

    // We don't need to go further
    if($total_lessons === 0) {
      return 0;
    }

    $db = new lib\Db;

    $lesson_ids = $this->lessons('ids');
    $lesson_ids_str = $db->prepare_array('%d', $lesson_ids);

    $total_progress = (float)($total_lessons * 100.00);

    $q = $wpdb->prepare("
        SELECT SUM(up.progress)
          FROM {$db->user_progress} AS up
         WHERE up.course_id = %d
           AND up.lesson_id IN (" . $lesson_ids_str . ")
           AND up.user_id = %d
      ",
      $this->ID,
      $user_id
    );

    $completed_progress = (float)$wpdb->get_var($q);

    $progress = (float)($completed_progress / $total_progress * 100.00);
    return number_format(min($progress,100));
  }

  /**
  * Remove an existing section not in $sections array
  * @param array $sections Sections from form
  */
  public function remove_sections($sections = array()) {
    $existing_sections = $this->sections();

    // Remove sections that were removed in the UI
    foreach ($existing_sections as $section) {
      if(!isset($sections[$section->uuid])) {
        $section->destroy();
      }
    }
  }

  /**
   * Get all authors of at least one course
   *
   * @return void
   */
  public static function post_authors(){
    global $wpdb;
    $db = new lib\Db;

    $q = $wpdb->prepare("
        SELECT usr.ID, usr.display_name, usr.user_login
          FROM {$wpdb->prefix}users AS usr
          INNER JOIN {$wpdb->prefix}posts AS pst
          ON usr.ID = pst.post_author
         WHERE pst.post_type = %s
         GROUP BY usr.ID
      ",
      models\Course::$cpt
    );
    $authors=$wpdb->get_results($q);
    return $authors;
  }

}
