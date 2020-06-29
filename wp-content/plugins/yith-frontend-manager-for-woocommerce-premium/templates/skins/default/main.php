<?php
/*
Template Name: Frontend Dashboard custom template
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_head();

$default_path = YITH_WCFM_TEMPLATE_PATH . 'skins/default/';
include_once( $default_path . 'header.php' );
?>
<body <?php body_class(); ?>>
<?php
// TO SHOW THE PAGE CONTENTS
while ( have_posts() ) : the_post(); ?> <!--Because the_content() works only inside a WP Loop -->
	<div id="yith_wcfm-main-content" class="<?php echo ''/*yith_wcfm-top-nav*/ ?>">
		<div class="yith_wcfm-container">
			<div class="yith_wcfm-main-content-wrap">
				<?php the_content(); ?> <!-- Page Content -->
			</div>
		</div>
	</div><!-- .entry-content-page -->

	<?php
endwhile; //resetting the page loop
wp_reset_query(); //resetting the page query

include_once( $default_path . 'footer.php' );

wp_footer();
?>
</body>
