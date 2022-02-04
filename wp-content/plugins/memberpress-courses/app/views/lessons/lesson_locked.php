<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<div class="mpcs-lesson-locked">
  <div class="mpcs-lesson-locked-header">
    <?php echo __('Lesson Unavailable' , 'memberpress-courses'); ?>
  </div>
  <div class="mpcs-lesson-locked-message">
    <?php echo __('You must complete all previous lessons and quizzes before you start this lesson.', 'memberpress-courses'); ?>
  </div>
  <div class="mpcs-lesson-locked-buttons">
    <a class="<?php echo $button_class; ?>" href="<?php echo get_permalink($lesson->course()->ID); ?>">
      <?php esc_html_e('Back to Course', 'memberpress-courses') ?>
    </a>
  </div>
</div>
