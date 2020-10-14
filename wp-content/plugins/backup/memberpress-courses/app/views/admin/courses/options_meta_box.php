<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<div class="wrap">
  <input type="hidden" name="mpcs-course-nonce" value="<?php echo \wp_create_nonce('mpcs-course-nonce' . wp_salt()); ?>" />
  <div class="grid">
    <div class="col-1-3 grid-pad">
      <div class="content">
        <label><?php _e('Course Visibility', 'memberpress-courses'); ?></label>
        <?php memberpress\courses\helpers\App::info_tooltip('course-status',
          __('Course Visibility', 'memberpress-courses'),
          __('The visibility of the course as displayed on the courses page. Hidden courses will not be displayed, but their content and lessons will remain available to enrolled users.', 'memberpress-courses'));
        ?>
      </div>
    </div>
    <?php foreach($course->statuses as $status): ?>
      <div class="col-1-3 grid-pad">
        <div class="content">
          <input type="radio" name="<?php echo memberpress\courses\models\Course::$page_status_str; ?>" id="<?php echo memberpress\courses\models\Course::$page_status_str . '-' . $status; ?>" value="<?php echo $status; ?>" <?php checked($status, $course->status); ?> />
          <label for="<?php echo memberpress\courses\models\Course::$page_status_str . '-' . $status; ?>"> <?php _e($status === 'enabled' ? 'Show' : 'Hide', 'memberpress-courses'); ?></label>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="grid">
    <div class="col-1-3 grid-pad">
      <div class="content">
        <label><?php _e('Course Order', 'memberpress-courses'); ?></label>
      </div>
    </div>
    <div class="col-2-3 grid-pad">
      <div class="content">
        <input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo $course->menu_order; ?>">
      </div>
    </div>
  </div>
</div>
