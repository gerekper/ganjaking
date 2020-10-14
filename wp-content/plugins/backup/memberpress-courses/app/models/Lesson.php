<?php
namespace memberpress\courses\models;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses\lib as lib;

class Lesson extends lib\BaseCptModel {
  public static $cpt = 'mpcs-lesson';
  public static $nonce_str = 'mpcs-lesson-nonce';
  public static $permalink_slug = 'lessons';
  public static $section_id_str = '_mpcs_lesson_section_id';
  public static $lesson_order_str = '_mpcs_lesson_lesson_order';
  public $statuses;

  public function __construct($obj = null) {
    parent::__construct($obj);
    $this->load_cpt(
      $obj,
      self::$cpt,
      array(
        'status'        => array( 'default' => 'enabled', 'type' => 'string' ),
        'section_id'    => array( 'default' => 0,         'type' => 'integer' ),
        'lesson_order'  => array( 'default' => 0,         'type' => 'integer' )
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

  public function section() {
    if($this->section_id > 0)
      return new Section($this->section_id);
    else
      return false;
  }

  public function course() {
    if($section = $this->section()) {
      if($course = $section->course()) {
        return $course;
      }
    }
  else {
      return false;
    }
  }
  /**
  * Get ids of ordered surrounding pages
  * @return array[int] Array of lesson ids
  */
  public function nav_ids() {
    $query = new \WP_Query(
      array(
        'post_type'  => Lesson::$cpt,
        'meta_query' => array(
          array(
            'key'   => Lesson::$section_id_str,
            'value' => $this->section_id,
          ),
          array(
            'key'     => Lesson::$lesson_order_str,
            'value'   => array($this->lesson_order - 1, $this->lesson_order + 1),
            'type'    => 'numeric',
            'compare' => 'BETWEEN',
          )
        ),
        'meta_key'  => Lesson::$lesson_order_str,
        'orderby'  => 'meta_value_num',
        'order'     => 'ASC',
        'fields'    => 'ids',
      )
    );

    return $query->posts;
  }

  public function cloneit() {
    $lesson_dup = (array) $this->rec;
    $unset_keys = array('ID', 'post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt', 'guid');
    $lesson_dup = \array_diff_key($lesson_dup, \array_flip($unset_keys));
    $lesson_dup['post_title'] .= ' (' . __('Copy', 'memberpress-courses') . (string) time() . ')';

    return \wp_insert_post($lesson_dup);
  }

  public static function find_all_by_section($section_id) {
    global $wpdb;
    $query = $wpdb->prepare("
      SELECT ID FROM {$wpdb->posts} AS p
        JOIN {$wpdb->postmeta} AS pm
          ON p.ID = pm.post_id
         AND pm.meta_key = %s
         AND pm.meta_value = %s
        JOIN {$wpdb->postmeta} AS pm_order
          ON p.ID = pm_order.post_id
         AND pm_order.meta_key = %s
       WHERE p.post_type='mpcs-lesson' AND p.post_status='publish'
       ORDER BY pm_order.meta_value * 1
       ",
       Lesson::$section_id_str,
       $section_id,
       Lesson::$lesson_order_str
    );
    $lesson_ids = $wpdb->get_col($query);

    $lessons = array();
    foreach($lesson_ids as $lesson_id) {
      $lessons[] = new Lesson($lesson_id);
    }

    return $lessons;
  }


  public static function find_all($options = array()) {
    $lessons = Lesson::get_all_objects($options);

    if($lessons === false) {
      $lessons = array();
    }

    return $lessons;
  }

  public static function exists($lesson_id, $section_id) {
    $lesson = Lesson::get_one(array(
      'wheres' => array(
        'ID' => $lesson_id,
        'section_id' => $section_id,
      )
    ));

    return (isset($lesson) && $lesson instanceof Lesson) ? true : false;
  }

  public function add_to_section($section_id, $order) {
    $this->section_id = $section_id;
    $this->lesson_order = $order;
    $this->store_meta();
  }

  public function update_order($order) {
    $this->lesson_order = $order;
    $this->store_meta();
  }

  public function remove_from_section() {
    delete_post_meta($this->ID, Lesson::$section_id_str, $this->section_id);
    delete_post_meta($this->ID, Lesson::$lesson_order_str, $this->lesson_order);
  }
}
