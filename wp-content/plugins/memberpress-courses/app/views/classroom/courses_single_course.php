<?php
use memberpress\courses\helpers as helpers;
use memberpress\courses\models as models;
use memberpress\courses\controllers as controllers;
use memberpress\courses as base;

// Load header
echo helpers\Courses::get_classroom_header();
// Start the Loop.
while ( have_posts() ) :
  the_post();
  ?>
  <div class="entry entry-content">
    <div class="columns col-gapless">
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
        <?php the_content() ?>
      </div>
    </div>
  </div>

  <?php
endwhile; // End the loop.
echo helpers\Courses::get_classroom_footer(); ?>