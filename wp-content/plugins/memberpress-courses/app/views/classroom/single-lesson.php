<?php
use memberpress\courses\models as models;
use memberpress\courses\helpers as helpers;
use memberpress\courses as base;

// Load header
echo helpers\Courses::get_classroom_header();
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
        <h1 class="entry-title"> <i class="mpcs-doc-text-inv"></i> <?php the_title() ?></h1>
        <?php the_content() ?>
      </div>
    </div>
  </div>


<?php
  echo helpers\Courses::get_classroom_footer();
?>