<?php
namespace memberpress\courses\models;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class Quiz extends Lesson {
  public static $cpt = 'mpcs-quiz';
  public static $permalink_slug = 'quizzes';
  public static $nonce_str = 'mpcs-quiz-nonce';

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
  }

  public function get_attr_key($attr) {
    return "_mpcs_lesson_" . $attr;
  }

  /**
   * Get the array of questions within the content of this quiz
   *
   * @param bool $ids_only Get only the question IDs rather than the question instances
   * @return Question[]|int[]
   */
  public function get_questions($ids_only = false) {
    if(!function_exists('parse_blocks')) {
      return array();
    }

    $questions = array();
    $blocks = parse_blocks($this->post_content);

    foreach($blocks as $block) {
      if(isset($block['blockName']) && strpos($block['blockName'], 'memberpress-courses') === 0) {
        $id = isset($block['attrs']['questionId']) ? (int) $block['attrs']['questionId'] : 0;

        if($id > 0) {
          if($ids_only) {
            $questions[] = $id;
          }
          else {
            $question = new Question($id);

            if($question->id > 0 && $question->type != 'placeholder') {
              $questions[] = $question;
            }
          }
        }
      }
    }

    return $questions;
  }

  /**
   * Does this quiz have any completed attempts?
   *
   * @return bool
   */
  public function has_attempts() {
    return Attempt::get_count(['quiz_id' => $this->ID, 'status' => 'complete']) > 0;
  }
}
