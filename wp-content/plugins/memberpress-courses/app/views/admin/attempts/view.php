<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }
use memberpress\courses as base;
use memberpress\courses\helpers;
use memberpress\courses\lib;
use memberpress\courses\models;
?>
<div class="mpcs-admin-attempt">
  <h2>
    <?php
      echo esc_html(
        sprintf(
          /* translators: %1$s: the quiz title, %2$s: the user's full name */
          __('%1$s: %2$s', 'memberpress-courses'),
          $quiz->post_title,
          lib\Utils::name_or_username($user->first_name, $user->last_name, $user->user_login)
        )
      );
    ?>
  </h2>
  <h4 class="mpcs-admin-attempt-score">
    <?php echo esc_html($attempt->get_score()); ?>
  </h4>
  <div class="mpcs-admin-attempt-answers">
    <?php
      foreach($questions as $question) {
        $answer = models\Answer::get_one(['attempt_id' => $attempt->id, 'question_id' => $question->id]);

        require base\VIEWS_PATH . "/quizzes/questions/{$question->type}.php";
      }
    ?>
  </div>
</div>
