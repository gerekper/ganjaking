<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<div>
  <div id="file-downloads" class="mpdl-info-box">
    <div class="mpdl-info-box-header"><b><?php _e('# of Downloads', 'memberpress-downloads'); ?></b></div>
    <div id="download-count" class="mpdl-info-box-body"><?php echo $file->download_count; ?></div>
  </div>
</div>

