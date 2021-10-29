<?php  if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Example template for displaying 500 error pages (Server Error).
 */

// Change the page title for a 500 error
if ( function_exists( 'hmwp_ms_filter_wp_title' ) ) {
	add_filter( 'wp_title', 'hmwp_ms_filter_wp_title', 10, 3 );
}

// Add body classes so the twenty eleven theme renders the page properly
if ( function_exists( 'hmwp_ms_body_class' ) ) {
	add_filter( 'body_class', 'hmwp_ms_body_class' );
}

// Warning message
$hmwp_ms_error_title   = __( 'This is somewhat embarrassing, isn&rsquo;t it?', 'twentyeleven' );
$hmwp_ms_error_message = __( 'It seems there was some sort of problem. Perhaps searching, or one of the links below, can help.', 'twentyeleven' );

// Is this a ban request?
if ( function_exists( 'hmwp_ms_is_ban' ) AND hmwp_ms_is_ban() ) {
	// Ban message
	$hmwp_ms_error_title   = sprintf( __( '%s Unavailable', 'twentyeleven' ), get_bloginfo( 'name' ) );
	$hmwp_ms_error_message = __( 'There was a problem processing the request.', 'twentyeleven' );
}

get_header(); ?>

	<div id="primary">
		<div id="content" role="main">

			<article id="post-0" class="post error500 server-error">
				<header class="entry-header">
					<h1 class="entry-title"><?php echo $hmwp_ms_error_title; ?></h1>
				</header>

				<div class="entry-content">
					<p><?php echo $hmwp_ms_error_message; ?></p>

					<?php get_search_form(); ?>

					<?php the_widget( 'WP_Widget_Recent_Posts', array( 'number' => 10 ), array( 'widget_id' => '404' ) ); ?>

					<div class="widget">
						<h2 class="widgettitle"><?php _e( 'Most Used Categories', 'twentyeleven' ); ?></h2>
						<ul>
						<?php wp_list_categories( array( 'orderby' => 'count', 'order' => 'DESC', 'show_count' => 1, 'title_li' => '', 'number' => 10 ) ); ?>
						</ul>
					</div>

					<?php
					$archive_content = '<p>' . sprintf( __( 'Try looking in the monthly archives. %1$s', 'twentyeleven' ), convert_smilies( ':)' ) ) . '</p>';
					the_widget( 'WP_Widget_Archives', array( 'count' => 0, 'dropdown' => 1 ), "after_title=</h2>$archive_content" );
					?>

					<?php the_widget( 'WP_Widget_Tag_Cloud' ); ?>

				</div><!-- .entry-content -->
			</article><!-- #post-0 -->

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>