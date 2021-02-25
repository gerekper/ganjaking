<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses as base;
use memberpress\courses\helpers as helpers;
?>

<div class="mpcs-sidebar-wrapper">

  <?php do_action(base\SLUG_KEY . '_classroom_start_sidebar'); ?>

  <!-- Featured Image -->
  <figure class="figure">
  <?php if ( has_post_thumbnail()) : ?>
    <a href="<?php the_permalink(); ?>" alt="<?php the_title_attribute(); ?>">
      <?php the_post_thumbnail('mpcs-course-thumbnail', ['class' => 'img-responsive']); ?>
    </a>
  <?php endif; ?>
  </figure>

  <!-- Progress -->
  <div class="course-progress">
    <?php echo helpers\Courses::classroom_sidebar_progress($post); ?>
  </div>


  <!-- Menu -->
  <?php
    if(helpers\Lessons::is_a_lesson($post)){
      echo helpers\Courses::display_course_overview(false, true);
    }

    if(helpers\Courses::is_a_course($post)){ ?>
      <div class="section mpcs-sidebar-menu">
        <a class="tile <?php \MeprAccountHelper::active_nav('home', 'is-active') ?>" href="<?php echo get_permalink() ?>">
          <div class="tile-icon">
            <i class="mpcs-list-alt"></i>
          </div>
          <div class="tile-content">
            <p class="tile-title m-0"><?php esc_html_e('Course Overview', 'memberpress-courses') ?></p>
          </div>
        </a>
        <a class="tile <?php \MeprAccountHelper::active_nav('instructor', 'is-active') ?>" href="<?php echo get_permalink() . '?action=instructor' ?>">
          <div class="tile-icon">
            <i class="mpcs-user"></i>
          </div>
          <div class="tile-content">
            <p class="tile-title m-0"><?php esc_html_e('Your Instructor', 'memberpress-courses') ?></p>
          </div>
        </a>
      </div>
      <?php
    }
    ?>

  <?php do_action(base\SLUG_KEY . '_classroom_end_sidebar'); ?>
</div>