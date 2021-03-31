<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<?php use memberpress\courses\helpers as helpers; ?>

<div class="mpcs-breadcrumbs">
  <!-- Current Course Link -->
  <span class="mpcs-breadcrumb">
    <a href="<?php echo helpers\Lessons::course_link($current_course->ID); ?>" class="<?php echo helpers\Options::val($options,'breadcrumb-link-css'); ?>"><?php echo $current_course->post_title; ?></a>
  </span>
  <!-- Current Section Link -->
  <span class="mpcs-breadcrumb">
    <a href="<?php echo helpers\Lessons::section_link($current_section->id); ?>" class="<?php echo helpers\Options::val($options,'breadcrumb-link-css'); ?>"><?php echo $current_section->title; ?></a>
  </span>
  <!-- Current Lesson -->
  <span class="mpcs-breadcrumb mpcs-active">
    <?php echo $current_lesson->post_title; ?>
  </span>
</div>
