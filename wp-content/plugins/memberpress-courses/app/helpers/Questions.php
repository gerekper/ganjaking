<?php
namespace memberpress\courses\helpers;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses\lib\Utils;
use memberpress\courses\models as models;

class Questions {
  /**
   * Get questions with search metadata
   *
   * @param int $except_quiz_id The quiz ID to ignore from results
   * @param string $search The search term
   * @param int $page The page number
   * @return array
   */
  public static function questions_with_meta($except_quiz_id = 0, $search = '', $page = 1) {
    $limit = apply_filters('mpcs_admin_questions_per_page', 10);
    $count = models\Question::search_all_except($search, $except_quiz_id, 0, 1, true);
    $results = models\Question::search_all_except($search, $except_quiz_id, $limit, $page);
    $questions = [];

    foreach($results as $result) {
      $questions[] = [
        'id' => (int) $result->id,
        'text' => $result->text,
        'type' => $result->type,
        'quizId' => (int) $result->quiz_id,
        'quizTitle' => $result->quiz_title,
        'hasAnswers' => (bool) $result->has_answers,
      ];
    }

    return [
      'questions' => $questions,
      'searchMeta' => [
        'search' => $search,
        'page' => (int) $page,
        'pages' => ceil($count / $limit),
      ],
    ];
  }

  /**
   * Get an associative array of the question data from the given ID
   *
   * @param int $id The question ID
   * @return array
   */
  public static function get_question($id) {
    $model = new models\Question($id);
    return self::prepare_question($model);
  }

  /**
   * Converts a question model to an associative array
   *
   * @param models\Question $model The question model
   * @return array
   */
  public static function prepare_question($model) {
    $question = array(
      'questionId' => $model->id,
      'question' => $model->text,
      'number' => $model->number,
      'type' => $model->type,
      'required' => (bool) $model->required,
      'points' => (int) $model->points,
      'quizId' => (int) $model->quiz_id,
    );

    if($model->type == 'multiple-choice' || $model->type == 'multiple-answer') {
      $options = [];

      if($model->options && is_array($model->options)) {
        foreach($model->options as $index => $option) {
          if($model->type == 'multiple-answer') {
            $is_correct = is_array($model->answer) && in_array($index, $model->answer, true);
          }
          else {
            $is_correct = ((string) $index) === $model->answer;
          }

          $options[] = [
            'value' => $option,
            'isCorrect' => $is_correct
          ];
        }
      }

      $question['options'] = $options;
      $question['feedback'] = $model->feedback;
    }

    if($model->type == 'true-false') {
      $question['answer'] = (string) $model->answer;
      $question['feedback'] = $model->feedback;
    }

    if($model->type == 'essay') {
      if(is_array($model->settings)) {
        $question['min'] = (int) $model->settings['min'];
        $question['max'] = (int) $model->settings['max'];
      }
    }

    return $question;
  }

  /**
   * Takes and associative array and creates a question model, then stores it
   *
   * @param int $quiz_id The quiz ID
   * @param array $question The question data
   * @return int|false|\WP_Error The created question ID, false on failure, WP_Error on failed validation
   */
  public static function save_question($quiz_id, $question) {
    $id = isset($question['questionId']) ? absint($question['questionId']) : 0;

    if($id && $id > 0) {
      $model = new models\Question($id); // Existing question
    }
    else {
      $model = new models\Question(); // New question
    }

    $model->quiz_id = $quiz_id;
    $model->number = (int) $question['number'];
    $model->text = sanitize_textarea_field($question['question']);
    $model->type = sanitize_text_field($question['type']);
    $model->required = (bool) $question['required'];
    $model->points = (int) $question['points'];

    if($model->type == 'multiple-choice' || $model->type == 'multiple-answer') {
      $answer = $model->type == 'multiple-answer' ? [] : '';
      $options = [];

      if(is_array($question['options'])) {
        foreach($question['options'] as $index => $option) {
          $index = (int) $index;

          if(isset($option['value'])) {
            $options[$index] = sanitize_text_field($option['value']);
            $is_correct = isset($option['isCorrect']) && $option['isCorrect'];

            if($is_correct) {
              if($model->type == 'multiple-answer') {
                $answer[] = $index;
              }
              else {
                $answer = $index;
              }
            }
          }
        }
      }

      $model->options = $options;
      $model->answer = $answer;
      $model->feedback = isset($question['feedback']) ? wp_kses_post($question['feedback']) : '';
    }

    if($model->type == 'true-false') {
      $model->answer = (int) $question['answer'];
      $model->feedback = isset($question['feedback']) ? wp_kses_post($question['feedback']) : '';
    }

    if($model->type == 'essay') {
      $settings = [];

      $settings['min'] = (int) $question['min'];
      $settings['max'] = (int) $question['max'];

      $model->settings = $settings;
    }

    return $model->store();
  }

  public static function save_question_placeholder($quiz_id) {
    $model = new models\Question();
    $model->type = 'placeholder';
    $model->quiz_id = $quiz_id;

    return $model->store(false);
  }

  public static function maybe_orphan_or_delete_question($id) {
    $model = new models\Question($id);

    //If the question is a placeholder, no data was ever saved to it, so just delete it
    //If it is an orphan (not part of a quiz and has no responses) then it can also be deleted
    if ($model->can_delete()) {
      $model->destroy();
    } else {
      //Question was removed from quiz, but still has responses associated with it,
      //and it could be added to a later quiz, so just orphan it.
      $model->quiz_id = 0;
      $model->store();
    }
  }

  public static function render_question($block_attributes) {
    $output = '';
    $question_id = isset($block_attributes['questionId']) ? $block_attributes['questionId'] : 0;

    if($question_id) {
      $question = new models\Question($question_id);
      list($attempt, $answer) = self::get_attempt_and_answer($question);
      list($show_results, $show_answers) = self::get_show_results_and_answers($question);

      $output = \MeprView::get_string('/quizzes/questions/' . $question->type, get_defined_vars());
    }

    return $output;
  }

  /**
   * Get the attempt and answer for the given question and current user
   *
   * @param models\Question $question
   * @return array
   */
  private static function get_attempt_and_answer($question) {
    $attempt = null;
    $answer = null;

    if(is_user_logged_in()) {
      $attempt = models\Attempt::get_one(['quiz_id' => $question->quiz_id, 'user_id' => get_current_user_id()]);

      if($attempt instanceof models\Attempt) {
        $answer = models\Answer::get_one(['attempt_id' => $attempt->id, 'question_id' => $question->id]);
      }
    }

    return [$attempt, $answer];
  }

  /**
   * Get the values of the settings "Show Results" and "Show Answers" from the course of the given question
   *
   * @param models\Question $question
   * @return array
   */
  private static function get_show_results_and_answers($question) {
    $show_results = false;
    $show_answers = false;

    $quiz = $question->quiz();

    if($quiz instanceof models\Quiz) {
      $course = $quiz->course();

      if($course instanceof models\Course) {
        $show_results = $course->show_results == 'enabled';
        $show_answers = $course->show_answers == 'enabled';
      }
    }

    return [$show_results, $show_answers];
  }

  /**
   * Duplicate the questions within the quiz content and update the block attributes with the new question IDs
   *
   * @param string $post_content The original post content
   * @param int $quiz_id The quiz ID to associate the new questions with
   * @return string The modified post content
   */
  public static function duplicate_quiz_questions($post_content, $quiz_id) {
    if(!function_exists('parse_blocks') || !function_exists('serialize_blocks')) {
      return $post_content;
    }

    $blocks = parse_blocks($post_content);

    if(empty($blocks)) {
      return $post_content;
    }

    foreach($blocks as $key => $block) {
      if(isset($block['blockName']) && strpos($block['blockName'], 'memberpress-courses') === 0) {
        $question_id = isset($block['attrs']['questionId']) ? $block['attrs']['questionId'] : 0;

        // If we can't find the original question, we'll set the questionId attribute to 0 so that a new
        // question placeholder is created when the editor loads.
        $new_question_id = 0;

        if($question_id > 0) {
          $original = models\Question::find($question_id);

          if($original instanceof models\Question) {
            $question = new models\Question();
            $question->load_from_array($original->get_values());
            $question->id = 0;
            $question->quiz_id = $quiz_id;
            $id = $question->store();

            if(!$id instanceof \WP_Error) {
              $new_question_id = $id;
            }
          }
        }

        $blocks[$key]['attrs']['questionId'] = $new_question_id;
      }
    }

    $new_post_content = serialize_blocks($blocks);

    if(empty($new_post_content)) {
      return $post_content;
    }

    return $new_post_content;
  }
}
