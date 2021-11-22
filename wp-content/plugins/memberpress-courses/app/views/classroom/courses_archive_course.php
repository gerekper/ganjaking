<?php
use memberpress\courses as base;
use memberpress\courses\helpers as helpers;
use memberpress\courses\models as models;
// // use memberpress\courses\controllers as controllers;
// use memberpress\courses as base;
use memberpress\courses\lib as lib;

$search = isset($_GET['s']) ? esc_attr($_GET['s']) : '';
$category = isset($_GET['category']) ? esc_attr($_GET['category']) : '';
$author = isset($_GET['author']) ? esc_attr($_GET['author']) : '';
$options = \get_option('mpcs-options');
$progress_color = implode(', ', helpers\Options::get_rgb($options, 'progress-color') );
$filter_base_url = home_url( $wp->request );
$pos = strpos($filter_base_url , '/page');

if ($pos > 0) {
  $filter_base_url = substr($filter_base_url,0,$pos);
}
// Load header
echo helpers\Courses::get_classroom_header();
?>
<div class="entry entry-content" style="padding: 2em 0">
  <div class="container grid-xl">

    <div class="mpcs-course-filter columns">
      <div class="column col-sm-12">
        <div class="dropdown">
          <a href="#" class="btn btn-link dropdown-toggle" tabindex="0">
            <?php _e('Category', 'memberpress-courses') ?>: <span></span><i class="mpcs-down-dir"></i>
          </a>
          <ul class="menu">
            <?php
              $terms = get_terms('mpcs-course-categories'); // Get all terms of a taxonomy

              printf('<li><input type="text" class="form-input mpcs-dropdown-search" placeholder="%s" id="mpmcSearchCategory"></li>', esc_html__('Search', 'memberpress-courses') );

              printf('<li class="%s"><a href="%s">%s</a></li>', $category == '' ? 'active' : '', add_query_arg('category', '',  $filter_base_url), esc_html__('All', 'memberpress-courses') );
              foreach ($terms as $term) {
                printf('<li class="%s"><a href="%s">%s</a></li>', $category == $term->slug ? 'active' : '', add_query_arg('category', $term->slug,  $filter_base_url), $term->name );
              }
            ?>
          </ul>
        </div>

        <div class="dropdown">
          <a href="#0" class="btn btn-link dropdown-toggle" tabindex="0">
            <?php _e('Author', 'memberpress-courses') ?>: <span></span><i class="mpcs-down-dir"></i>
          </a>
          <!-- menu component -->
          <ul class="menu">
            <?php
              $post_authors = models\Course::post_authors();

              printf('<li><input type="text" class="form-input mpcs-dropdown-search" placeholder="%s" id="mpmcSearchCourses"></li>', esc_html__('Search', 'memberpress-courses') );

              printf('<li class="%s"><a href="%s">%s</a></li>', $author == '' ? 'active' : '', add_query_arg('author', '',  $filter_base_url), esc_html__('All', 'memberpress-courses') );

              foreach ($post_authors as $post_author) {
                printf('<li class="%s"><a href="%s">%s</a></li>', $author == $post_author->user_login ? 'active' : '', add_query_arg('author', $post_author->user_login,  $filter_base_url), lib\Utils::get_full_name( $post_author->ID ) );
              }
            ?>
          </ul>
        </div>

        <div class="archives-authors-section">
          <ul>

          </ul>
        </div>


      </div>

      <div class="column col-sm-12">
        <form method="GET" class="" action="">
          <div class="input-group">
            <input type="text" name="s" class="form-input" placeholder="<?php _e('Find a course', 'memberpress-courses') ?>" value="<?php echo $search ?>">
            <button class="btn input-group-btn"><i class="mpcs-search form-icon"></i></button>
          </div>
        </form>

      </div>
    </div>

    <div class="columns mpcs-cards">

      <?php
      if( have_posts() ) :
      while ( have_posts() ) : the_post(); // standard WordPress loop.
      $course = new models\Course($post->ID);
      $progress = $course->user_progress($current_user->ID);
      $course_is_locked = false;

      if(\MeprRule::is_locked($post) && helpers\Courses::is_course_archive()) {
        $course_is_locked = true;
      }
      ?>

      <div class="column col-4 col-sm-12">
        <div class="card s-rounded">
          <div class="card-image">
            <?php if ($course_is_locked) { ?>
              <div class="locked-course-overlay">
                <i class="mpcs-icon mpcs-lock"></i>
              </div>
            <?php } ?>
            <a href="<?php the_permalink(); ?>" alt="<?php the_title_attribute(); ?>">
              <?php if ( has_post_thumbnail()) :
                the_post_thumbnail('mpcs-course-thumbnail', ['class' => 'img-responsive']);
              else: ?>
                <img src="<?php echo base\IMAGES_URL . '/course-placeholder.png' ?>" class="img-responsive" alt="">
              <?php endif; ?>
            </a>
          </div>
          <div class="card-header">
            <div class="card-title">
              <h2 class="h5"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h2>
            </div>
          </div>
          <div class="card-body">
            <?php the_excerpt() ?>
          </div>
          <div class="card-footer">
            <span class="course-author">
              <?php
              $user_id = get_the_author_meta( 'ID' );
              $user_data = get_userdata( $user_id );
              $author_url = add_query_arg( 'author', $user_data->user_login,  $filter_base_url );
              // echo lib\Utils::get_avatar( $user_id, '30' );
              // echo lib\Utils::get_full_name( $user_id );
              ?>
              <a href="<?php echo esc_url_raw( $author_url ); ?>"><?php echo lib\Utils::get_avatar( $user_id, '30' ) . lib\Utils::get_full_name( $user_id ); ?></a>
            </span>

            <!-- <span class="price float-right">$15.00/m</span> -->
            <?php if(models\UserProgress::has_started_course(get_current_user_id(), $course->ID)): ?>
              <div class="mpcs-progress-ring" data-value="<?php echo $progress ?>" data-color="<?php echo $progress_color ?>">
                <div class="inner">
                  <div class="stat"><?php echo $progress ?></div>
                </div>
              </div>
            <?php endif; ?>

          </div>
        </div>
      </div>

      <?php endwhile; // end of the loop. ?>
      <?php else: ?>
        <p><?php esc_html_e('No Course found', 'memberpress-courses') ?></p>
      <?php endif; // the end of end. ?>
    </div>

    <div class="pagination">
      <?php echo helpers\Courses::archive_navigation(); ?>
    </div>


    <?php

?>


  </div>
</div>

<?php
echo helpers\Courses::get_classroom_footer();
?>
