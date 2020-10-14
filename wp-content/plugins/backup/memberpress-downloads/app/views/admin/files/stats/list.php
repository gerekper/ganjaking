<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }
?>
<div class="wrap">
  <h2><?php _e('File Stats', 'memberpress', 'memberpress-downloads'); ?></h2>
  <?php $list_table->display(); ?>
</div>
