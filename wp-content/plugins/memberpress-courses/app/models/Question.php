<?php
namespace memberpress\courses\models;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses\lib as lib;

/**
 * @property int $id The question ID
 * @property int $quiz_id The quiz ID
 * @property int $number The question number
 * @property string $text The question label text
 * @property array $options The available choices
 * @property string|array $answer The correct answer, this is an array for multiple-answer type
 * @property string $type The question type
 * @property bool $required Whether this is a required question
 * @property int $points The number of points awarded for the correct answer
 * @property string $feedback The question feedback
 * @property array $settings The question settings
 */
class Question extends lib\BaseModel {
  public function __construct($obj = null) {
    $this->initialize(
      array(
        'id' =>       array('default' => 0,       'type' => 'integer'),
        'quiz_id' =>  array('default' => 0,       'type' => 'integer'),
        'number' =>   array('default' => 1,       'type' => 'integer'),
        'text' =>     array('default' => '',      'type' => 'string'),
        'options' =>  array('default' => array(), 'type' => 'array'),
        'answer' =>   array('default' => '',      'type' => 'string'),
        'type' =>     array('default' => '',      'type' => 'string'),
        'required' => array('default' => true,    'type' => 'bool'),
        'points' =>   array('default' => 1,       'type' => 'integer'),
        'feedback' => array('default' => '',      'type' => 'string'),
        'settings' => array('default' => array(), 'type' => 'array')
      ),
      $obj
    );
  }


  /**
   * Validate this question
   *
   * @return bool
   * @throws lib\ValidationException
   */
  public function validate() {
    lib\Validate::is_numeric($this->id, 0, null, __('Id', 'memberpress-courses'));
    lib\Validate::is_numeric($this->quiz_id, 0, null, __('Quiz Id', 'memberpress-courses'));
    lib\Validate::is_numeric($this->number, 1, null, __('Number', 'memberpress-courses'));
    lib\Validate::not_empty($this->text, __('Question Text', 'memberpress-courses'));
    lib\Validate::not_empty($this->type, __('Type', 'memberpress-courses'));

    if($this->type == 'multiple-answer') {
      lib\Validate::is_array($this->answer, __('Answer', 'memberpress-courses'));
    }
    elseif(in_array($this->type, array('multiple-choice', 'true-false'))) {
      lib\Validate::not_empty_string($this->answer, __('Answer', 'memberpress-courses'));
      lib\Validate::is_numeric($this->answer, 0, null, __('Answer', 'memberpress-courses'));
    }

    lib\Validate::is_bool($this->required, __('Required', 'memberpress-courses'));
    lib\Validate::is_numeric($this->points, 0, null, __('Points', 'memberpress-courses'));

    if(in_array($this->type, array('multiple-choice', 'multiple-answer'))) {
      lib\Validate::not_empty($this->options, __('Options', 'memberpress-courses'));
    }

    return true;
  }

  /* Returns true if this question is a placeholder, or not associated with a quiz or any responses/answers. */
  public function can_delete() {
    if(Answer::get_one(['question_id' => $this->id])) {
      return false;
    }

    return ($this->type == 'placeholder' || $this->quiz_id == 0);
  }

  /* Store the object in the database */
  public function store($validate=true) {
    $db = lib\Db::fetch();

    if($validate) {
      try {
        $this->validate();
      } catch(lib\ValidationException $e) {
        return new \WP_Error(get_class($e), $e->getMessage());
      }
    }

    $attrs = $this->get_values();

    //Serialize Array Values before passing to db
    if(is_array($attrs['answer'])) {
      $attrs['answer'] = serialize($attrs['answer']);
    }

    $attrs['options'] = $attrs['options'] ? serialize($attrs['options']) : '';
    $attrs['settings'] = $attrs['settings'] ? serialize($attrs['settings']) : '';

    if(isset($this->id) && (int) $this->id > 0) {
      $db->update_record($db->questions, $this->id, $attrs);
    }
    else {
      $this->id = $db->create_record($db->questions, $attrs, false);
    }

    return $this->id;
  }

  public function destroy() {
    $db = lib\Db::fetch();

    return $db->delete_records($db->questions, array('id' => $this->id));
  }

  /**
   * Get this quiz that this question belongs to
   *
   * @return Quiz|false
   */
  public function quiz() {
    $quiz = new Quiz($this->quiz_id);

    if($quiz->ID > 0) {
      return $quiz;
    }

    return false;
  }

  /**
   * Search for questions
   *
   * @param string $search The search term
   * @param int $except_quiz_id The quiz ID to ignore from results
   * @param int $limit Limit results to this number
   * @param int $page The page number
   * @param bool $count_only Return only the count
   * @return array|int
   */
  public static function search_all_except($search = '', $except_quiz_id = 0, $limit = 10, $page = 1, $count_only = false) {
    global $wpdb;
    $db = lib\Db::fetch();
    $page = $page > 0 ? $page : 1; // Make sure that the lowest page number is one, for offset calculation

    $query = $count_only ? "SELECT COUNT(*)" : "SELECT q.id, q.type, q.text, q.quiz_id, p.post_title AS quiz_title, !ISNULL(a.id) AS has_answers";
    $query .= " FROM {$db->questions} q";
    $query .= " LEFT JOIN {$wpdb->posts} p ON q.quiz_id = p.ID";
    $query .= " LEFT JOIN {$db->answers} a ON a.id = (SELECT id FROM {$db->answers} WHERE question_id = q.id ORDER BY id ASC LIMIT 1)";
    $query .= " WHERE q.type <> 'placeholder'";

    $offset = $limit * ($page - 1);
    $args = array();

    if($except_quiz_id > 0) {
      $query .= " AND q.quiz_id <> %d";
      $args[] = $except_quiz_id;
    }

    if($search) {
      $query .= " AND q.text LIKE %s";
      $args[] = '%' . $wpdb->esc_like($search) . '%';
    }

    // Display the newest questions first
    $query .= " ORDER BY q.id DESC";

    if(!$count_only) {
      $query .= " LIMIT %d OFFSET %d";
      $args[] = $limit;
      $args[] = $offset;
    }

    if(count($args) > 0 ) {
      $query = $wpdb->prepare($query, ...$args);
    }

    if($count_only) {
      return (int) $wpdb->get_var($query);
    }

    return $wpdb->get_results($query);
  }

  //This function just makes sure that the question in the quiz table and the quiz post match up.
  public static function sync_database($quiz_id, $question_ids) {
    if($quiz_id && $quiz_id > 0 && !empty($question_ids)) {
      global $wpdb;
      $db = lib\Db::fetch();

      $ids = implode(', ', $question_ids);

      // Delete and remaining placeholders, in case of changes that were not saved
      $query = 'DELETE FROM ' . $db->questions . ' WHERE quiz_id = %d AND type = %s AND id NOT IN (' . $ids . ')';
      $query = $wpdb->prepare($query, $quiz_id, 'placeholder');
      $wpdb->query($query);

      // Orphan all the questions that are no longer in the quiz, in case of changes that were not saved
      $query = 'UPDATE ' . $db->questions . ' SET quiz_id = 0 WHERE quiz_id = %d AND type <> %s AND id NOT IN (' . $ids . ')';
      $query = $wpdb->prepare($query, $quiz_id, 'placeholder');
      $wpdb->query($query);
    }
  }

  /**
   * Get the score for this question based on the given answer
   *
   * @param  string|array $answer
   * @return int
   */
  public function get_score($answer) {
    $score = 0;

    switch($this->type) {
      case 'multiple-choice':
        if(is_string($this->answer) && $this->answer !== '' && isset($this->options[$this->answer]) && $answer === $this->options[$this->answer]) {
          $score = $this->points;
        }
        break;
      case 'multiple-answer':
        if(is_array($this->options) && is_array($this->answer) && is_array($answer) && count($answer)) {
          $has_incorrect_answer = false;

          foreach($this->options as $key => $option) {
            if(in_array($key, $this->answer, true) && !in_array($option, $answer, true)) {
              $has_incorrect_answer = true; // a correct answer was not provided
            }
            elseif(!in_array($key, $this->answer, true) && in_array($option, $answer, true)) {
              $has_incorrect_answer = true; // an incorrect answer was provided
            }
          }

          if(!$has_incorrect_answer) {
            $score = $this->points;
          }
        }
        break;
      case 'true-false':
        if(is_string($this->answer) && $this->answer !== '' && (($answer === 'False' && $this->answer === '0') || ($answer === 'True' && $this->answer === '1'))) {
          $score = $this->points;
        }
        break;
      case 'short-answer':
        if($answer !== '') {
          $score = $this->points;
        }
        break;
      case 'essay':
        if($answer !== '') {
          $length = mb_strlen($answer);
          $min_length = isset($this->settings['min']) && is_numeric($this->settings['min']) && $this->settings['min'] > 0 ? (int) $this->settings['min'] : 1;
          $max_length = isset($this->settings['max']) && is_numeric($this->settings['max']) && $this->settings['max'] >= 0 ? (int) $this->settings['max'] : 0;

          if($length >= $min_length && ($max_length == 0 || $length <= $max_length)) {
            $score = $this->points;
          }
        }
        break;
    }

    return (int) apply_filters('mpcs_get_score_for_answer', $score, $answer, $this);
  }

  /**
   * Is the given answer correct?
   *
   * @param Answer $answer
   */
  public function is_answer_correct($answer) {
    $is_answer_correct = $this->get_score($answer->answer) == $this->points;

    return apply_filters('mpcs_is_answer_correct', $is_answer_correct, $this, $answer);
  }

  /**
   * Is the given value one of the correct answers?
   *
   * @param string $value
   * @return false
   */
  public function is_option_correct($value) {
    $correct = false;

    switch($this->type) {
      case 'multiple-answer':
        if(is_array($this->options) && is_array($this->answer)) {
          foreach($this->options as $key => $option) {
            if($option == $value && in_array($key, $this->answer, true)) {
              $correct = true;
              break;
            }
          }
        }
        break;
      case 'multiple-choice':
        if(is_string($this->answer) && $this->answer !== '' && isset($this->options[$this->answer]) && $this->options[$this->answer] == $value) {
          $correct = true;
        }
        break;
      case 'true-false':
        if(is_string($this->answer) && $this->answer !== '' && (($value === 'False' && $this->answer === '0') || ($value === 'True' && $this->answer === '1'))) {
          $correct = true;
        }
        break;
    }

    return $correct;
  }

  /**
   * Get the question feedback HTML
   *
   * @return string
   */
  public function get_feedback_html() {
    if(empty($this->feedback)) {
      return '';
    }

    $feedback = $this->feedback;

    if(apply_filters('mpcs_display_feedback_incorrect_prefix', true, $this)) {
      $feedback = sprintf(
        '<strong>%1$s</strong> %2$s',
        esc_html__('Incorrect:', 'memberpress-courses'),
        $feedback
      );
    }

    $feedback = do_shortcode(shortcode_unautop(wpautop($feedback)));

    return apply_filters('mpcs_question_feedback_html', $feedback, $this);
  }

  /**
   * Determine if the given value is considered empty
   *
   * Using empty() does not allow '0' as an answer.
   *
   * @param string|array $value Array for multiple answer, string otherwise.
   * @return bool
   */
  public function is_value_empty($value) {
    if($this->type == 'multiple-answer') {
      return !is_array($value) || count($value) == 0;
    }

    return !is_string($value) || $value === '';
  }
}
