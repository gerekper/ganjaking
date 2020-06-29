<?php
/*
Template Name: Frontend Dashboard custom template
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
    <html>
    <head>
        <meta charset="utf-8">
		<?php wp_head(); ?>
    </head>
<?php
/**
 * Load the skin header template
 */
do_action( 'yith_wcfm_load_skin1_header' );
//include_once( __DIR__ . '/header.php' );

// TO SHOW THE PAGE CONTENTS
while ( have_posts() ) : the_post(); ?> <!--Because the_content() works only inside a WP Loop -->
    <div id="yith_wcfm-main-content" class="<?php echo ''/*yith_wcfm-top-nav*/ ?>">
        <div class="yith_wcfm-container">
            <div class="yith_wcfm-main-content-wrap responsive-nav-closed">
				<?php the_content(); ?> <!-- Page Content -->
            </div>
        </div>
    </div><!-- .entry-content-page -->

	<?php
endwhile; //resetting the page loop
wp_reset_query(); //resetting the page query

/**
 * Load the skin footer template
 */
wp_footer();
do_action( 'yith_wcfm_load_skin1_footer' );
?>