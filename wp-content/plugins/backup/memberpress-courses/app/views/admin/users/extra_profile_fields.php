<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<h3><?php _e('Course Information', 'memberpress-courses'); ?></h3>
<table class="form-table mpcs-course-information">
  <?php foreach($my_courses as $course): ?>
    <tr>
      <th><?php echo $course->post_title; ?></th>
      <td class="progress">
        <div class="course-progress">
          <div class="user-progress" data-value="<?php echo $course->user_progress($user->ID); ?>">
          </div>
        </div>
      </td>
      <td>
      <?php if($course->user_progress($user->ID) > 0){ ?>
        <a class="mpcs-reset-course-progress" data-value="<?php echo $course->ID; ?>" data-user="<?php echo $_GET['user_id']; ?>" data-nonce="<?php echo wp_create_nonce('reset_progress') ?>" href="#0"><?php _e('Reset Progress', 'memberpress-courses'); ?></a>
      <?php } ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<?php
