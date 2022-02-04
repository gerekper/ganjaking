<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
  <h2>
    <?php
      echo esc_html(
        sprintf(
          /* translators: %s: the quiz name */
          __('%s Attempts', 'memberpress-courses'),
          $quiz->post_title
        )
      );
    ?>
  </h2>
  <form method="get">
    <input type="hidden" name="page" value="mpcs-quiz-attempts">
    <input type="hidden" name="id" value="<?php echo esc_attr($quiz->ID); ?>">
    <?php
      $table->search_box(esc_html__('Search Attempts', 'easy-affiliate', 'memberpress-courses'), 'mpcs-search-attempts');
      $table->display();
    ?>
  </form>
</div>
