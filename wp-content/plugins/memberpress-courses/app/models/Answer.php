<?php
namespace memberpress\courses\models;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses\lib as lib;

/**
 * @property int $id The answer ID
 * @property int $attempt_id The attempt ID
 * @property int $question_id The question ID
 * @property string $answer The answer given
 * @property int $points_possible Highest possible points for this answer
 * @property int $points_awarded Points awarded for this answer
 * @property int $grader The user ID of the grader
 * @property string $answered_at Datetime in MySQL format
 * @property string $graded_at Datetime in MySQL format
 */
class Answer extends lib\BaseModel {
  public function __construct($obj = null) {
    $this->initialize(
      array(
        'id' => array('default' => 0,  'type' => 'integer'),
        'attempt_id' => array('default' => 0,  'type' => 'integer'),
        'question_id' => array('default' => 0,  'type' => 'integer'),
        'answer' => array('default' => '', 'type' => 'string'),
        'points_possible' => array('default' => 0,  'type' => 'integer'),
        'points_awarded' => array('default' => 0,  'type' => 'integer'),
        'grader' => array('default' => 0,  'type' => 'integer'),
        'answered_at' => array('default' => lib\Utils::ts_to_mysql_date(time()), 'type' => 'datetime'),
        'graded_at' => array('default' => lib\Utils::ts_to_mysql_date(time()), 'type' => 'datetime'), //TODO: When we implement manual grading, change this default to null
      ),
      $obj
    );
  }

  /**
   * Store this answer
   *
   * @param bool $validate Whether to validate the model before storing
   * @return int|\WP_Error|false The answer ID, a WP_Error if validation fails, or false on failure
   */
  public function store($validate = true) {
    if($validate) {
      try {
        $this->validate();
      } catch(lib\ValidationException $e) {
        return new \WP_Error(get_class($e), $e->getMessage());
      }
    }

    $db = lib\Db::fetch();
    $attrs = $this->get_values();

    $attrs['answer'] = is_array($attrs['answer']) ? serialize($attrs['answer']) : $attrs['answer'];

    if(isset($this->id) && $this->id > 0) {
      $db->update_record($db->answers, $this->id, $attrs);
    }
    else {
      $this->id = $db->create_record($db->answers, $attrs, false);
    }

    return $this->id;
  }

  /**
   * Destroy this answer
   *
   * @return int|false The number of affected rows or false if there was an error
   */
  public function destroy() {
    $db = lib\Db::fetch();

    return $db->delete_records($db->answers, array('id' => $this->id));
  }

  /**
   * Validate the answer object
   *
   * @return bool
   * @throws lib\ValidationException
   */
  public function validate() {
    lib\Validate::is_numeric($this->id, 0, null, __('Id', 'memberpress-courses'));
    lib\Validate::is_numeric($this->attempt_id, 0, null, __('Attempt Id', 'memberpress-courses'));
    lib\Validate::is_numeric($this->question_id, 0, null, __('Question Id', 'memberpress-courses'));
    lib\Validate::is_numeric($this->points_possible, 0, null, __('Points Possible', 'memberpress-courses'));
    lib\Validate::is_numeric($this->points_awarded, 0, null, __('Points Awarded', 'memberpress-courses'));
    lib\Validate::not_empty($this->answered_at, __('Created At', 'memberpress-courses'));

    return true;
  }

  /**
   * Insert an answer or update an existing answer
   *
   * @param  int          $attempt_id      The attempt ID
   * @param  int          $question_id     The question ID
   * @param  string|array $answer          The answer string, or array for multiple answer (pass it unserialized)
   * @param  int          $points_possible The points possible
   * @param  int          $points_awarded  The points awarded
   * @param  int          $grader          The grader user ID
   * @param  string       $answered_at     The MySQL datetime the answer was created at
   * @param  string       $graded_at       The MySQL datetime the answer was graded at
   * @return int|false                     Integer (0, 1 or 2) on success, false on failure
   */
  public static function insert_or_replace_answer(
    $attempt_id,
    $question_id,
    $answer,
    $points_possible,
    $points_awarded,
    $grader,
    $answered_at,
    $graded_at
  ) {
    global $wpdb;
    $db = lib\Db::fetch();

    $query = $wpdb->prepare(
      "INSERT INTO {$db->answers}
        (`attempt_id`, `question_id`, `answer`, `points_possible`, `points_awarded`, `grader`, `answered_at`, `graded_at`)
        VALUES
        (%d, %d, %s, %d, %d, %d, %s, %s)
        ON DUPLICATE KEY UPDATE
        `attempt_id` = VALUES(`attempt_id`),
        `question_id` = VALUES(`question_id`),
        `answer` = VALUES(`answer`),
        `points_possible` = VALUES(`points_possible`),
        `points_awarded` = VALUES(`points_awarded`),
        `grader` = VALUES(`grader`),
        `answered_at` = VALUES(`answered_at`),
        `graded_at` = VALUES(`graded_at`)",
      $attempt_id,
      $question_id,
      maybe_serialize($answer),
      $points_possible,
      $points_awarded,
      $grader,
      $answered_at,
      $graded_at
    );

    return $wpdb->query($query);
  }
}
