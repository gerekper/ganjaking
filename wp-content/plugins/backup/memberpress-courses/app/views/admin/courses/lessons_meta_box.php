<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<div class="wrap sort-scroll-container">
  <div id="builder-notice" class="builder-notice hidden">
    <p><?php _e('Warning: Your lessons and sections will only be saved after you publish or update the course.', 'memberpress-courses'); ?></p>
  </div>
  <div id="builder-tip" class="builder-notice tip hidden">
    <p><?php _e('Tip: To get started enter your section title then add or create new lessons. You can easily add sections using the "New Section" button.', 'memberpress-courses'); ?></p>
  </div>
  <input type="hidden" name="<?php echo memberpress\courses\models\Course::$nonce_str; ?>" value="<?php echo \wp_create_nonce(memberpress\courses\models\Course::$nonce_str . \wp_salt()); ?>" />
    <div id="course-builder">
      <?php memberpress\courses\helpers\Courses::course_lessons_list($course); ?>
    </div>
    <button id="add-new-section" class="button button-primary button-large" title="<?php _e('New Section', 'memberpress-courses'); ?>"><?php _e('New Section', 'memberpress-courses') ?></button>
  <div id="hidden-add-lesson" class="hidden">
    <?php memberpress\courses\helpers\Courses::add_lesson(); ?>
  </div>
  <div id="hidden-new-lesson" class="hidden">
    <?php memberpress\courses\helpers\Courses::new_lesson(); ?>
  </div>
  <div id="hidden-new-section" class="hidden">
    <?php memberpress\courses\helpers\Courses::new_section(); ?>
  </div>
</div>
