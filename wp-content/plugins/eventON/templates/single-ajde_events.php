<?php	
/*
 *	The template for displaying single event
 *
 *	Override this tempalte by coping it to ....yourtheme/eventon/single-ajde_events.php
 *	This template is built based on wordpress twentythirteen theme standards and may not fit your custom
 *	theme correctly, in which case you may have to add custom styles to fix style issues
 *
 *	@Author: AJDE
 *	@EventON
 *	@version: 2.8.6
 */

?>
	
<?php	

// you can also pass a lang value in below function to create fixed lang single event page.
// this value will be overriden by language corresponding events
do_action('eventon_before_main_content','');

?>	
<div id='main'>
	<div class='evo_page_body'>

		<?php do_action('eventon_single_content_wrapper');?>

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<div class="entry-content">

					<?php	
						do_action('eventon_single_content');
					?>		
					</div><!-- .entry-content -->

					<footer class="entry-meta">
						<?php edit_post_link( __( 'Edit', 'twentythirteen' ), '<span class="edit-link">', '</span>' ); ?>
					</footer><!-- .entry-meta -->
				</article><!-- #post -->
			<?php endwhile; ?>	

		<?php	do_action('eventon_single_sidebar');	?>

		<?php	do_action('eventon_single_after_loop');	?>

	</div><!-- #primary -->	

</div>	

<?php 	do_action('eventon_after_main_content'); ?>