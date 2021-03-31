<h1><?php echo $course->post_title ?></h1>

<?php if( is_user_logged_in() ) : ?>

<div class="progress-bar">
  <div class="user-progress" data-value="<?php echo $course->user_progress($current_user->ID); ?>"></div>
</div>
<p class="progress-text">
  <span><?php echo $course->user_progress($current_user->ID) . '% ' ?></span>
  <?php esc_html_e('COMPLETE', 'memberpress-courses') ?>
</p>

<?php endif; ?>