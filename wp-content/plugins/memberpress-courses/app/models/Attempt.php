<?php
namespace memberpress\courses\models;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses\lib as lib;

/**
 * @property int $id The attempt ID
 * @property int $quiz_id The quiz ID
 * @property int $user_id The user ID
 * @property int $points_awarded The total number of points awarded
 * @property int $points_possible The total number of points possible
 * @property int $score The score percentage
 * @property string $status The attempt status, 'draft' or 'complete'
 * @property string $started_at Datetime in MySQL format
 * @property string $finished_at Datetime in MySQL format
 */
class Attempt extends lib\BaseModel {
  public static $draft_str = 'draft';
  public static $complete_str = 'complete';

  public function __construct($obj = null) {
    $this->initialize(
      array(
        'id' => array('default' => 0, 'type' => 'integer'),
        'quiz_id' => array('default' => 0, 'type' => 'integer'),
        'user_id' => array('default' => 0, 'type' => 'integer'),
        'points_awarded' => array('default' => 0, 'type' => 'integer'),
        'points_possible' => array('default' => 0, 'type' => 'integer'),
        'score' => array('default' => 0, 'type' => 'integer'),
        'status' => array('default' => self::$draft_str, 'type' => 'string'),
        'started_at' => array('default' => null, 'type' => 'datetime'),
        'finished_at' => array('default' => null, 'type' => 'datetime'),
      ),
      $obj
    );
  }

  /**
   * Store this attempt
   *
   * @param bool $validate Whether to validate the model before storing
   * @return int|\WP_Error|false The attempt ID, a WP_Error if validation fails, or false on failure
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

    if(isset($this->id) && $this->id > 0) {
      $db->update_record($db->attempts, $this->id, $attrs);
    }
    else {
      $this->id = $db->create_record($db->attempts, $attrs, false);
    }

    return $this->id;
  }

  /**
   * Destroy this attempt
   *
   * @return int|false The number of affected rows or false if there was an error
   */
  public function destroy() {
    $db = lib\Db::fetch();
    $result = $db->delete_records($db->attempts, array('id' => $this->id));

    if($result) {
      // only delete answers if attempt deletion succeeded
      $db->delete_records($db->answers, array('attempt_id' => $this->id));

      // delete the UserProgress record too
      $user_progress = UserProgress::find_one_by_user_and_lesson($this->user_id, $this->quiz_id);

      if(!empty($user_progress) && !empty($user_progress->id)) {
        $user_progress->destroy();
      }
    }

    return $result;
  }

  /**
   * Get the quiz associated with this attempt
   *
   * @return Quiz|false
   */
  public function quiz() {
    return Quiz::find($this->quiz_id);
  }

  /**
   * Get the user associated with this attempt
   *
   * @return \WP_User|false
   */
  public function user() {
    return get_user_by('id', $this->user_id);
  }

  /**
   * Is this attempt complete?
   *
   * @return bool
   */
  public function is_complete() {
    return $this->status == self::$complete_str;
  }

  /**
   * Is this attempt a draft?
   *
   * @return bool
   */
  public function is_draft() {
    return $this->status == self::$draft_str;
  }

  /**
   * Get the score for this attempt formatted as "Score: #/# #%"
   *
   * @return string
   */
  public function get_score() {
    $score = sprintf(
      /* translators: %1$s: points awarded, %2$s: points possible, %3$s: score percent, %%: literal percent sign */
      __('Score: %1$s/%2$s (%3$s%%)', 'memberpress-courses'),
      $this->points_awarded,
      $this->points_possible,
      $this->score
    );

    return apply_filters('mpcs_attempt_score', $score, $this);
  }

  /**
   * Get the score for this attempt formatted as "Score: #%"
   *
   * @return string
   */
  public function get_score_percent() {
    $score = sprintf(
      /* translators: %s: score percent, %%: literal percent sign */
      __('Score: %s%%', 'memberpress-courses'),
      $this->score
    );

    return apply_filters('mpcs_attempt_score_percent', $score, $this);
  }

  /**
   * Validate the attempt object
   *
   * @return bool
   * @throws lib\ValidationException
   */
  public function validate() {
    $statuses = [self::$draft_str, self::$complete_str];

    lib\Validate::is_numeric($this->id, 0, null, __('Id', 'memberpress-courses'));
    lib\Validate::is_numeric($this->quiz_id, 0, null, __('Quiz Id', 'memberpress-courses'));
    lib\Validate::is_numeric($this->user_id, 0, null, __('User Id', 'memberpress-courses'));
    lib\Validate::is_numeric($this->points_awarded, 0, null, __('Points Awarded', 'memberpress-courses'));
    lib\Validate::is_numeric($this->points_possible, 0, null, __('Points Possible', 'memberpress-courses'));
    lib\Validate::is_numeric($this->score, 0, null, __('Score', 'memberpress-courses'));
    lib\Validate::is_in_array($this->status, $statuses, __('Status', 'memberpress-courses'));

    return true;
  }

  public static function list_table($order_by = '', $order = '', $paged = '', $search = '', $perpage = 10, $quiz_id = null) {
    global $wpdb;
    $db = lib\Db::fetch();

    $cols = [
      'id' => 'att.id',
      'quiz_id' => 'att.quiz_id',
      'user_id' => 'att.user_id',
      'user_login' => 'usr.user_login',
      'user_email' => 'usr.user_email',
      'first_name' => 'um_first_name.meta_value',
      'last_name' => 'um_last_name.meta_value',
      'name' => "CASE WHEN um_first_name.meta_value IS NULL OR TRIM(um_first_name.meta_value) = '' OR um_last_name.meta_value IS NULL OR TRIM(um_last_name.meta_value) = '' THEN usr.user_login ELSE CONCAT_WS(' ', um_first_name.meta_value, um_last_name.meta_value) END",
      'points_awarded' => 'att.points_awarded',
      'points_possible' => 'att.points_possible',
      'score' => 'att.score',
      'finished_at' => 'att.finished_at',
    ];

    $search_cols = [
      'um_first_name.meta_value',
      'um_last_name.meta_value',
      'usr.user_login',
      'user_email' => 'usr.user_email',
      'att.score',
    ];

    $from = "{$db->attempts} AS att";

    $joins = [
      "LEFT JOIN {$wpdb->users} AS usr ON att.user_id = usr.ID",
      "LEFT JOIN {$wpdb->usermeta} AS um_first_name ON um_first_name.user_id = usr.ID AND um_first_name.meta_key = 'first_name'",
      "LEFT JOIN {$wpdb->usermeta} AS um_last_name ON um_last_name.user_id = usr.ID AND um_last_name.meta_key = 'last_name'",
    ];

    $args = [$wpdb->prepare("att.status = %s", self::$complete_str)];

    if(is_numeric($quiz_id)) {
      $args[] = $wpdb->prepare("att.quiz_id = %d", (int) $quiz_id);
    }

    return lib\Db::list_table($cols, $from, $joins, $args, $order_by, $order, $paged, $search, $perpage, $search_cols);
  }
}
