<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<?php
use memberpress\courses\models as models;
use memberpress\courses as base;
?>
<div id="bookmark" class="mpcs-section">
  <div class="mpcs-section-header">
    <?php require( \MeprView::file('/courses/courses_progress' )); ?>
    <?php require( \MeprView::file('/courses/courses_bookmark_link' )); ?>
  </div> <!-- mpcs-section-header -->
</div>
