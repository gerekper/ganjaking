<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<?php
use memberpress\courses\models as models;
use memberpress\courses as base;
?>
<div id="bookmark" class="mpcs-section">
  <div class="mpcs-section-header">
    <?php require( base\VIEWS_PATH . '/courses/progress.php' ); ?>
    <?php require( base\VIEWS_PATH . '/courses/bookmark_link.php' ); ?>
  </div> <!-- mpcs-section-header -->
</div>