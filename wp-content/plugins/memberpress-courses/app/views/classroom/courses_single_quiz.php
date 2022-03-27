<?php
use memberpress\courses\models as models;
use memberpress\courses\helpers as helpers;
use memberpress\courses as base;

// Load header
echo helpers\Courses::get_classroom_header();
// Start the Loop.
while ( have_posts() ) :
  the_post();

  $lesson = new models\Lesson($post->ID);
  $lesson_available = $lesson->is_available();
  ?>
    <div class="entry entry-content">
      <div class="columns col-gapless" style="flex-grow: 1;">
        <div id="mpcs-sidebar" class="column col-3 col-md-4 col-sm-12 hide-sm pl-0">
          <div id="mpcs-sidebar-navbar" class="show-sm">
            <a class="btn sidebar-close">
              <i class="mpcs-cancel"></i>
            </a>
          </div>

          <?php echo helpers\Courses::get_classroom_sidebar($post); ?>

        </div>
        <div id="mpcs-main" class="column col-9 col-md-8 col-sm-12" >
          <?php setup_postdata($post->ID) ?>
          <?php if ( is_active_sidebar( 'mpcs_classroom_lesson_header' ) ) : ?>
            <div id="primary-sidebar" class="primary-sidebar widget-area" role="complementary">
              <?php dynamic_sidebar( 'mpcs_classroom_lesson_header' ); ?>
            </div>
          <?php endif; ?>
          <h1 class="entry-title"> <i class="mpcs-quiz-icon"></i> <?php the_title() ?></h1>

          <?php
            if($lesson_available) {
              the_content();
            } else {
              $button_class = 'btn btn-green is-purple';
              require(\MeprView::file('/lessons/lesson_locked'));
            }
          ?>

          <?php if ( is_active_sidebar( 'mpcs_classroom_lesson_footer' ) ) : ?>
            <div id="primary-sidebar" class="primary-sidebar widget-area" role="complementary">
              <?php dynamic_sidebar( 'mpcs_classroom_lesson_footer' ); ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>


  <?php
endwhile; // End the loop.
echo helpers\Courses::get_classroom_footer();
