<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<?php
  use memberpress\courses\helpers as helpers,
      memberpress\courses\models as models;

  if(helpers\Lessons::has_next_lesson($current_lesson_index, $lesson_nav_ids)) {
    $next_page_url = helpers\Lessons::next_lesson_link($current_lesson_index, $lesson_nav_ids);
    $continue_button_text = __('Continue', 'memberpress-courses');
  }
  elseif(helpers\Lessons::has_next_section($current_section_index, $section_ids)) {
    $next_page_url = helpers\Lessons::next_section_link($current_section_index, $section_ids);
    $continue_button_text = __('Continue', 'memberpress-courses');
  }
  else {
    $next_page_url = get_permalink($current_course->ID);
    $continue_button_text = __('Course Overview', 'memberpress-courses');
  }
?>
<div id="mpcs-quiz-navigation">
  <?php if($attempt instanceof models\Attempt && $attempt->is_complete()) : ?>
    <button type="button" id="mpcs-quiz-continue-bottom" data-href="<?php echo esc_attr($next_page_url); ?>" class="<?php echo helpers\Options::val($options,'complete-link-css'); ?>">
      <?php echo esc_html($continue_button_text); ?>
    </button>
  <?php else : ?>
    <button type="button" id="mpcs-quiz-submit-bottom" data-next-page-url="<?php echo esc_attr($next_page_url); ?>" class="<?php echo helpers\Options::val($options,'complete-link-css'); ?>">
      <?php esc_html_e('Submit', 'memberpress-courses'); ?>
    </button>
  <?php endif; ?>
</div>
