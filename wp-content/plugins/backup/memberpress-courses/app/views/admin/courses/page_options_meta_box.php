<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<div class="wrap">
  <input type="hidden" name="mpcs-course-nonce" value="<?php echo \wp_create_nonce('mpcs-course-nonce' . wp_salt()); ?>" />
  <label for="<?php echo memberpress\courses\models\Course::$page_template_str; ?>"><?php _e('Page Template', 'memberpress-courses'); ?></label>
  <select name="<?php echo memberpress\courses\models\Course::$page_template_str; ?>" id="<?php echo memberpress\courses\models\Course::$page_template_str;; ?>">
  <option value=""><?php _e('Default Template', 'memberpress-courses'); ?>&nbsp;</option>
  <?php foreach($templates as $template_name => $template_filename): ?>
      <option value="<?php echo $template_filename; ?>" <?php \selected($template_filename, $course->page_template); ?>><?php echo $template_name; ?>&nbsp;</option>
  <?php endforeach; ?>
  </select>
</div>
