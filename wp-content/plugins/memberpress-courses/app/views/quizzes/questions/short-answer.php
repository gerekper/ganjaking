<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<?php
  use memberpress\courses\models as models;

  $viewing_attempt = $attempt instanceof models\Attempt && $attempt->is_complete();
  $has_answer = $answer instanceof models\Answer;
  $has_correct_answer = $has_answer && $question->is_answer_correct($answer);
  $classes = ['mpcs-quiz-question', 'mpcs-quiz-question-short-answer'];

  if($viewing_attempt && $show_results) {
    $classes[] = $has_correct_answer ? 'mpcs-quiz-question-correct' : 'mpcs-quiz-question-incorrect';
  }
?>
<div id="mpcs-quiz-question-<?php echo esc_attr($question->id); ?>" class="<?php echo esc_attr(join(' ', $classes)); ?>">
  <div class="mpcs-quiz-question-label">
    <label for="mpcs-quiz-question-field-<?php echo esc_attr($question->id); ?>">
      <?php if($viewing_attempt && $show_results) : ?>
        <?php if($has_correct_answer) : ?>
          <span class="mpcs-quiz-correct-answer"><i class="mpcs-correct-answer"></i></span>
        <?php else : ?>
          <span class="mpcs-quiz-incorrect-answer"><i class="mpcs-incorrect-answer"></i></span>
        <?php endif; ?>
      <?php endif; ?>
      <?php echo nl2br(esc_html(apply_filters('mpcs_question_label', $question->text, $question))); ?>
      <?php if(apply_filters('mpcs_question_required_indicator', true, $question) && $question->required) : ?>
        <span class="mpcs-quiz-question-required">*</span>
      <?php endif; ?>
    </label>
  </div>
  <div class="mpcs-quiz-question-input">
    <input type="text" id="mpcs-quiz-question-field-<?php echo esc_attr($question->id); ?>" name="mpcs_quiz_question_<?php echo esc_attr($question->id); ?>" class="mpcs-quiz-question-field mpcs-quiz-question-field-short-answer" data-question-id="<?php echo esc_attr($question->id); ?>" value="<?php echo $has_answer ? esc_attr($answer->answer) : ''; ?>"<?php echo $viewing_attempt ? ' disabled' : ''; ?>>
  </div>
</div>
