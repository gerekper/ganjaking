<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div class="mp_wrapper">
  <div class="grid">
    <?php foreach($my_courses as $course): ?>
      <div class="col-1-2 grid-pad">
        <a href="<?php echo get_permalink($course->ID); ?>">
          <?php echo $course->post_title; ?>
        </a>
      </div>
      <div class="col-1-2 grid-pad">
        <?php if($show_bookmark === true): ?>
          <div class="course-progress">
            <div class="user-progress" data-value="<?php echo $course->user_progress($current_user->ID); ?>">
            </div>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>
