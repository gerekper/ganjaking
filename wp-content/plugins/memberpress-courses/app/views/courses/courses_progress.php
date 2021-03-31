<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<div class="mpcs-progress-bar" id="progress">
    <span class="mpcs-progress-bar-title"><?php printf(__('Course Progress: %d%%', 'memberpress-courses'), $progress); ?></span>
    <div class="course-progress-bar-main">
      <span style="width:<?php echo $progress; ?>%"></span>
    </div>
</div>