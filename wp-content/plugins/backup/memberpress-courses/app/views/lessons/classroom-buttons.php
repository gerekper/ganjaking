<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<?php
  use memberpress\courses\helpers as helpers,
      memberpress\courses\models as models;
?>

<section class="navbar-section" id="mpcs-lesson-navigation">
  <?php if(helpers\Lessons::has_previous_lesson($current_lesson_index)): ?>
    <button id="previous_lesson_link" data-href="<?php echo helpers\Lessons::previous_lesson_link($current_lesson_index, $lesson_nav_ids); ?>" class="<?php echo helpers\Options::val($options,'previous-link-css'); ?>">
      <?php
        printf('<span class="hide-md"><i class="mpcs-left-big"></i> %s</span>', __('Previous Lesson', 'memberpress-courses') );
        printf('<span class="show-md"><i class="mpcs-left-big"></i> %s</span>', __('Previous', 'memberpress-courses') );
      ?>
    </button>
  <?php elseif(helpers\Lessons::has_previous_section($current_section_index)) : ?>
    <button id="previous_lesson_link" data-href="<?php echo helpers\Lessons::previous_section_link($current_section_index, $section_ids); ?>" data-value="<?php echo $post->ID ?>" data-section="<?php echo $current_section->id ?>" class="<?php echo helpers\Options::val($options,'complete-link-css'); ?>">
      <?php
        printf('<span class="show-md"><i class="mpcs-left-big"></i>%s</span>', __('Previous', 'memberpress-courses') );
        printf('<span class="hide-md"><i class="mpcs-left-big"></i>%s</span>', __('Previous Section', 'memberpress-courses') );
      ?>
    </button>
  <?php endif; ?>

  <?php if(helpers\Lessons::has_next_lesson($current_lesson_index, $lesson_nav_ids)): ?>
    <button id="next_lesson_link" data-href="<?php echo helpers\Lessons::next_lesson_link($current_lesson_index, $lesson_nav_ids); ?>" data-value="<?php echo $post->ID ?>" class="<?php echo helpers\Options::val($options,'complete-link-css'); ?>">
      <?php
        if( !is_user_logged_in() ||
            ( is_user_logged_in() &&
              models\UserProgress::has_completed_lesson($current_user->ID, $current_lesson->ID) ) ):
              printf('<span class="show-md">%s <i class="mpcs-right-big"></i></span>', __('Next', 'memberpress-courses') );
              printf('<span class="hide-md">%s <i class="mpcs-right-big"></i></span>', __('Next Lesson', 'memberpress-courses') );
        else:
          printf('<span class="hide-md">%s <i class="mpcs-right-big"></i></span>', __('Complete and Continue', 'memberpress-courses') );
          printf('<span class="show-md">%s <i class="mpcs-right-big"></i></span>', __('Complete', 'memberpress-courses') );
        endif;
      ?>
    </button>
  <?php elseif(helpers\Lessons::has_next_section($current_section_index, $section_ids)): ?>
    <button id="next_lesson_link" data-href="<?php echo helpers\Lessons::next_section_link($current_section_index, $section_ids); ?>" data-value="<?php echo $post->ID ?>" data-section="<?php echo $current_section->id ?>" class="<?php echo helpers\Options::val($options,'complete-link-css'); ?>">
      <?php
        if( !is_user_logged_in() ||
            ( is_user_logged_in() &&
              models\UserProgress::has_completed_lesson($current_user->ID, $current_lesson->ID) ) ):
              printf('<span class="show-md">%s <i class="mpcs-right-big"></i></span>', __('Next', 'memberpress-courses') );
              printf('<span class="hide-md">%s <i class="mpcs-right-big"></i></span>', __('Next Section', 'memberpress-courses') );
        else:
          printf('<span class="hide-md">%s <i class="mpcs-right-big"></i></span>', __('Complete and Continue', 'memberpress-courses') );
          printf('<span class="show-md">%s <i class="mpcs-right-big"></i></span>', __('Complete', 'memberpress-courses') );
        endif;
      ?>
    </button>
  <?php else: ?>
    <button id="next_lesson_link" data-href="<?php echo get_permalink($current_course->ID); ?>" data-value="<?php echo $post->ID ?>" data-section="<?php echo $current_section->id ?>" data-course="<?php echo $current_course->ID ?>" class="<?php echo helpers\Options::val($options,'complete-link-css'); ?>">
      <?php
        if( !is_user_logged_in() ||
            ( is_user_logged_in() &&
              models\UserProgress::has_completed_lesson($current_user->ID, $current_lesson->ID) ) ):
          _e('Course Overview', 'memberpress-courses');
        else:
          printf('<span>%s <i class="mpcs-right-big"></i></span>', __('Complete Lesson', 'memberpress-courses') );
        endif;
      ?>
    </button>
  <?php endif; ?>
  <a class="btn sidebar-open show-sm">
    <i class="mpcs-th-list"></i>
  </a>
  <!-- <div style="clear: both;"></div> -->
</section>
