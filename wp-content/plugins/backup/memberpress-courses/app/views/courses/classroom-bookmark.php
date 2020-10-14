<?php if ( ! defined( 'ABSPATH' ) ) {
  die( 'You are not allowed to call this page directly.' ); } ?>

<h2><?php esc_html_e("Course Curriculum", "memberpress-courses") ?></h2>

<div id="bookmark">
  <?php if(isset($bookmark_url)) : ?>
    <a href="<?php echo $bookmark_url; ?>" class="btn btn-green">
      <span><?php esc_html_e('Start Next Lesson', 'memberpress-courses') ?></span>
      <i class="mpcs-angle-right"></i>
    </a>
  <?php endif; ?>

  <span class="mpcs-bookmark-link-title hide-md"><?php echo $next_lesson_title; ?></span>
</div>