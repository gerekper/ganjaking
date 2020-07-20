<?php  if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Example template for displaying 500 error pages (Server Error).
 */

// Change the page title for a 500 error
if ( function_exists( 'hmwp_ms_filter_wp_title' ) ) {
	add_filter( 'wp_title', 'hmwp_ms_filter_wp_title', 10, 3 );
}

// Warning message
$hmwp_ms_error_title   = __( 'An Error Was Encountered', 'twentyten' );
$hmwp_ms_error_message = __( 'There was an error with the page you requested.', 'twentyten' );

// Is this a ban request?
if ( function_exists( 'hmwp_ms_is_ban' ) AND hmwp_ms_is_ban() ) {
	// Ban message
	$hmwp_ms_error_title   = sprintf( __( '%s Unavailable', 'twentyten' ), get_bloginfo( 'name' ) );
	$hmwp_ms_error_message = __( 'There was a problem processing your request.', 'twentyten' );
}

get_header(); ?>

	<div id="container">
		<div id="content" role="main">

			<div id="post-0" class="post error500 server-error">
				<h1 class="entry-title"><?php echo $hmwp_ms_error_title; ?></h1>
				<div class="entry-content">
					<p><?php echo $hmwp_ms_error_message; ?></p>
					<?php get_search_form(); ?>
				</div><!-- .entry-content -->
			</div><!-- #post-0 -->

		</div><!-- #content -->
	</div><!-- #container -->
	<script type="text/javascript">
		// focus on search field after it has loaded
		document.getElementById('s') && document.getElementById('s').focus();
	</script>

<?php get_footer(); ?>