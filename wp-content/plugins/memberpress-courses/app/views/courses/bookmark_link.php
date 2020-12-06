<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php use memberpress\courses\models as models; ?>
<div class="mpcs-bookmark" id="bookmark_link">
  <a class="mpcs-bookmark-link mpcs-button is-rounded" href="<?php echo isset($bookmark_url) ? $bookmark_url : "#0" ; ?>" title="<?php echo isset($lesson->post_title) ? $lesson->post_title : __('Completed', 'memberpress-courses'); ?>">
    <?php if(isset($bookmark_url)): ?>
      <?php if(!models\UserProgress::has_started_course($current_user->ID, $course->ID)): ?>
        <span class="mpcs-bookmark-link-title mpcs-start-course is-green"><?php _e('Start', 'memberpress-courses'); ?></span>
      <?php else: ?>
        <span class="mpcs-bookmark-link-title mpcs-resume-course is-purple"><?php _e('In Progress', 'memberpress-courses'); ?></span>
      <?php endif; ?>
    <?php else: ?>
      <span class="mpcs-bookmark-link-title mpcs-resume-course is-gray"><?php _e('Completed', 'memberpress-courses'); ?></span>
    <?php endif; ?>
  </a>
</div>
