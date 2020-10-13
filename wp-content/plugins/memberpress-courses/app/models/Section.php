<?php
namespace memberpress\courses\models;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses\lib as lib;
use memberpress\courses\models as models;

class Section extends lib\BaseModel {
  public function __construct($obj = null) {
    $this->initialize(
      array(
        'id' =>            array('default' => 0,    'type' => 'integer'),
        'title' =>         array('default' => '',   'type' => 'string'),
        'description' =>   array('default' => '',   'type' => 'string'),
        'course_id' =>     array('default' => 0,    'type' => 'integer'),
        'section_order' => array('default' => 0,    'type' => 'integer'),
        'created_at' =>    array('default' => null, 'type' => 'datetime'),
        'uuid' =>          array('default' => null, 'type' => 'string')
      ),
      $obj
    );
  }

  /**
  * Used to validate the section object
  * @return true|null ValidationException raised on failure
  */
  public function validate() {
    lib\Validate::not_empty($this->title, 'title');
    lib\Validate::not_empty($this->uuid, 'uuid');

    return true;
  }

  /**
  * Used to create or update the section record
  * @param boolean $validate default true
  * @return integer id
  */
  public function store($validate = true) {
    if($validate) {
      try {
        $this->validate();
      }
      catch(lib\ValidationException $e) {
        return new \WP_Error(get_class($e), $e->getMessage());
      }
    }

    // Avoid duplicate sections in the database
    if( isset($this->uuid) && empty($this->id) ){
      $db = new lib\Db;
      $section = $db->get_one_record($db->sections, array('uuid' => $this->uuid));

      if(\is_object($section) && isset($section->id)){
        $this->id = $section->id;
      }
    }

    if(isset($this->id) && (int) $this->id > 0) {
      $this->update();
    }
    else {
      $this->id = self::create($this);
    }

    return $this->id;
  }

  /**
  * Destroy the section
  * @return integer|false Returns number of rows affected or false
  */
  public function destroy() {
    $db = new lib\Db;

    // First let's clean up the lessons
    $lessons = $this->lessons();
    foreach ($lessons as $lesson) {
      $lesson->remove_from_section();
    }

    return $db->delete_records($db->sections, array('id' => $this->id));
  }

  /**
  * Fetch lessons for section
  * @return array[Lesson] Returns an array of Lesson objects
  */
  public function lessons() {
    return models\Lesson::find_all_by_section($this->id);
  }

  /**
  * Used to create the section record
  * @param Section $section
  * @return integer id
  */
  public static function create($section) {
    $db = new lib\Db;
    $attrs = $section->get_values();

    return $db->create_record($db->sections, $attrs);
  }

  /**
  * Used to update the section record
  * @return integer id
  */
  private function update() {
    $db = new lib\Db;
    $attrs = $this->get_values();
    return $db->update_record($db->sections, $this->id, $attrs);
  }

  /**
  * Find all by course
  * @param integer $course_id
  * @return array[Section] Retuns array of Section objects ordered by section_order
  */
  public static function find_all_by_course($course_id) {
    $db = new lib\Db;

    $records = $db->get_records($db->sections, compact('course_id'), 'section_order');

    $sections = array();
    foreach($records as $rec) {
      $sections[] = new models\Section($rec->id);
    }

    return $sections;
  }

  /**
  * Course for Section
  * @return Course
  */
  public function course() {
    if($this->course_id > 0)
      return new Course($this->course_id);
    else
      return false;
  }

  /**
  * Remove lessons from section
  * @param array $section_lessons Lessons for section from the admin
  */
  public function remove_unassigned_lessons($section_lessons) {
    $existing_lessons = $this->lessons();

    foreach($existing_lessons as $lesson) {
      if(!\in_array($lesson->ID, $section_lessons)) {
        $lesson->remove_from_section();
      }
    }
  }
}
